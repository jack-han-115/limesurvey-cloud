<?php

use LimeSurveyProfessional\notifications\LimitReminderNotification;

/**
 * The LimeSurveyProfessional plugin for "free" LimeService systems
 * Source for the cookie consent popup: https://cookieconsent.insites.com/documentation/javascript-api/
 * Requires Bootstrap for modal popup.
 *
 * This plugin should at some point include all necessary different implementation for cloud edition.
 *
 *
 */
class LimeSurveyProfessional extends \LimeSurvey\PluginManager\PluginBase
{
    protected $storage = 'DbStorage';
    static protected $description = 'LimeSurvey Cloud extras';
    static protected $name = 'LimeSurveyProfessional';
    static $violationCount = 0;

    protected $settings = array();

    /** @var \LimeSurvey\Models\Services\LimeserviceSystem */
    public $limeserviceSystem;

    /** @var boolean */
    public $isSuperAdminReadUser;

    /** @var boolean */
    public $isPayingUser;

    /** @var bool */
    public $outOfResponses;


    /**
     * @return void
     */
    public function init()
    {
        \Yii::setPathOfAlias(get_class($this), dirname(__FILE__));
        $this->subscribe('beforeDeactivate'); // user should not be able to deactivate this one ...
        $this->subscribe('beforeControllerAction');
        $this->subscribe('beforeTokenEmail');
    }

    /**
     * If this is a LimeService installation with free subscription, don't allow to disable it
     *
     * @return void
     */
    public function beforeDeactivate()
    {
        $this->getEvent()->set('success', false);

        // Optionally set a custom error message.
        $this->getEvent()->set('message', gT('Core plugin can not be disabled.'));
        /*
        $isLimeServiceInstallation = function_exists('getInstallationID') && isset(Yii::app()->dbstats);
        if ($isLimeServiceInstallation) {
            // Get subsription plan
            $result = Yii::app()->dbstats
                ->createCommand(
                    'SELECT advertising FROM limeservice_system.installations WHERE user_id = ' . getInstallationID()
                )
                ->queryRow();
            // If "free", it should not be possible to deactivate
            if ($result['advertising'] == '1') {
                $event = $this->getEvent();
                $event->set('success', false);
            }
        }*/
    }

    /**
     * Only one modal will be displayed, so the createNotification calls need to be sorted ascending by importance
     * 1. if admin welcome-page is called, the limitReminderNotification functionality will be called
     * 2. blocking notifications (unclosable modal)
     *
     */
    public function beforeControllerAction()
    {
        $controller = $this->getEvent()->get('controller');
        $action = $this->getEvent()->get('action');
        if ($this->isBackendAccess()) {
            $this->initPluginData();

            if (!$this->createBlockingNotifications()) {
                if ($controller === 'admin' && $action === 'index') {
                    $limitReminderNotification = new LimitReminderNotification($this);
                    $limitReminderNotification->createNotification();
                }
            }
        }
    }

    /**
     * Returns the email address of the site admin either as plain address or as simple html mailto link
     * - is needed by several subclasses
     * @param bool $asHtmlLink
     * @return string
     */
    public function getSiteAdminEmail(bool $asHtmlLink = false)
    {
        $email = getGlobalSetting('siteadminemail');
        if ($asHtmlLink) {
            $email = '<a href="mailto:' . $email . '">' . $email . '</a>';
        }

        return $email;
    }

    /**
     * If user is a logged-in user we can assume, that backend is accessed right now.
     * @return bool
     */
    public function isBackendAccess()
    {
        return !Yii::app()->user->isGuest;
    }

    /**
     * Redirects to welcome-page if any other url is opened except welcome-page and logout
     */
    public function forceRedirectToWelcomePage()
    {
        $controller = $this->getEvent()->get('controller');
        $action = $this->getEvent()->get('action');
        $subAction = $this->getEvent()->get('subaction');
        if (!($controller == 'admin' && $action == 'index') && $subAction != 'logout') {
            \Yii::app()->controller->redirect(App()->getController()->createUrl('admin/index'));
        }
    }

    /**
     * Sets class variables for subscription, installation and admin related data which is used in several subclasses
     * @throws CException
     */
    private function initPluginData()
    {
        $this->limeserviceSystem = new \LimeSurvey\Models\Services\LimeserviceSystem(
            \Yii::app()->dbstats,
            (int)getInstallationID()
        );
        $plan = $this->limeserviceSystem->getUsersPlan();
        $this->isSuperAdminReadUser = \Permission::model()->hasGlobalPermission('superadmin', 'read');
        $this->isPayingUser = $plan !== 'free' && $plan != '';
        $this->outOfResponses = $this->limeserviceSystem->getResponsesAvailable() <= 0;
    }

    /**
     * All notification types which use unclosable modal sorted by importance
     * (most important first)
     * When a blocking modal is successfully created, it will be shown and the leftover classes won't be processe
     *
     * Attention: Every class inside array $blockingNotifications needs to have the function createNotification()
     * createNotification() needs to return a boolean!
     *
     * @return boolean
     */
    private function createBlockingNotifications()
    {
        $blockingNotification = false;
        $blockingNotifications = [
            ['class' => 'LimeSurveyProfessional\notifications\OutOfResponses'],
        ];

        foreach ($blockingNotifications as $notification) {
            $className = $notification['class'];
            $blockingNotificationClass = new $className($this);
            $blockingNotification = $blockingNotificationClass->createNotification();
            if ($blockingNotification) {
                break;
            }
        }

        if ($blockingNotification) {
            $this->forceRedirectToWelcomePage();
        }

        return $blockingNotification;
    }

    /**
     *  Before a tokenEmail of types remind or invite are sent, it will run through a blacklist filter
     */
    public function beforeTokenEmail()
    {
        $type = $this->getEvent()->get('type', '');
        if ($type == 'invite' || $type == 'remind') {
            $this->initPluginData();
            $blacklistFilter = new \LimeSurveyProfessional\email\EmailFilter($this->getEvent(), $this);
            $blacklistFilter->filter();
        }
    }
}
