<?php

use LimeSurveyProfessional\notifications\LimitReminderNotification;
use LimeSurveyProfessional\notifications\OutOfResponsesPaid;
use LimeSurveyProfessional\promotionalBanners\PromotionalBanners;
use LimeSurveyProfessional\notifications\GracePeriodNotification;
use LimeSurveyProfessional\ParticipantRegisterCta\ParticipantRegisterCta;
use LimeSurvey\Libraries\FormExtension\Inputs\ButtonSwitchInput;
use LimeSurvey\Libraries\FormExtension\SaveFailedException;

require_once(__DIR__ . '/vendor/autoload.php');

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
    protected static $description = 'LimeSurvey Cloud extras';
    protected static $name = 'LimeSurveyProfessional';
    protected $complete = false;
    /** @var int violationCounter for email blacklist filter */
    public static $violationCount = 0;
    /** @var string can contain info regarding the email blacklist filter */
    public static $violationText = '';

    protected $settings = array();

    /**
     * @return void
     */
    public function init($loadConfig = true)
    {
        \Yii::setPathOfAlias(get_class($this), dirname(__FILE__));
        \Yii::import('application.helpers.common_helper', true);
        if ($loadConfig) {
            $this->readConfigFile();
        }
        $this->subscribe('beforeDeactivate'); // user should not be able to deactivate this one ...
        $this->subscribe('beforeControllerAction');
        $this->subscribe('beforeTokenEmail');
        $this->subscribe('beforeAdminMenuRender');
        $this->subscribe('newDirectRequest');
        $this->subscribe('beforeCloseHtml');
        $this->subscribe('afterSurveyComplete');
        $this->subscribe('beforeSurveyPage');

        // @todo This needs to be properly db versioned
        if (!function_exists('db_upgrade_all')) {
            Yii::app()->loadHelper('update/updatedb');
        }
        $Column = App()->db->getSchema()->getTable('{{surveys_languagesettings}}')->getColumn('surveyls_legal_notice');
        if ($Column === null) {
            addColumn('{{surveys_languagesettings}}', 'surveyls_legal_notice', "text");
        }
        $Column = App()->db->getSchema()->getTable('{{surveys}}')->getColumn('showdatapolicybutton');
        if ($Column === null) {
            addColumn('{{surveys}}', 'showdatapolicybutton', 'integer DEFAULT 0');
        }
        $Column = App()->db->getSchema()->getTable('{{surveys}}')->getColumn('showlegalnoticebutton');
        if ($Column === null) {
            addColumn('{{surveys}}', 'showlegalnoticebutton', 'integer DEFAULT 0');
        }

        $installationData = $this->getInstallationData();
        $this->addAdvertisementGlobalSettings($installationData);
    }

    protected function addAdvertisementGlobalSettings($installationData)
    {
        $that = $this;
        if (
            $installationData->plan === 'free' ||
            $installationData->plan === 'basic'
        ) {
            $helpLinkText = $this->gT('Upgrade now');
            $helpLink = sprintf(
                ' <a href="%s" target="_blank">' . $helpLinkText . '</a>.',
                'https://www.limesurvey.org/pricing'
            );
            $help = $installationData->isSiteAdminUser ?
                $this->gT(
                    'This option can only be disabled by customers using our Expert or Enterprise package.'
                ) . $helpLink
            : $this->gT('Contact your site admin to upgrade');

            Yii::app()->formExtensionService->add(
                'globalsettings.general',
                new ButtonSwitchInput(
                    [
                        'name' => 'limesurvey_professional_advertisement',
                        'label' => $this->gT('Show LimeSurvey branding in survey'),
                        'disabled' => true,
                        'help' => $help,
                        'save' => function ($request, $connection) {
                            // Empty function, free and basic installation cannot change this value
                            return true;
                        },
                        'load' => function () {
                            return 'Y';
                        }
                    ]
                )
            );
        } else {
            Yii::app()->formExtensionService->add(
                'globalsettings.general',
                new ButtonSwitchInput(
                    [
                        'name' => 'limesurvey_professional_advertisement',
                        'label' => $this->gT('Show LimeSurvey branding in survey'),
                        'help' => $this->gT('Turn on or off LimeSurvey branding in survey footer and end of survey'),
                        'save' => function ($request, $connection) use ($that) {
                            $value = $request->getPost('limesurvey_professional_advertisement') === '1' ? 'Y' : 'N';
                            return $that->set('limesurvey_professional_advertisement', $value);
                        },
                        'load' => function () use ($that) {
                            // Default to 'Y'
                            $val = $that->get('limesurvey_professional_advertisement') ?? 'Y';
                            return $val === 'Y' ? '1' : '0';
                        }
                    ]
                )
            );
        }
    }

    /**
     * This plugin can never be disabled.
     * @return void
     */
    public function beforeDeactivate()
    {
        $this->getEvent()->set('success', false);

        // Optionally set a custom error message.
        $this->getEvent()->set('message', gT('Core plugin can not be disabled.'));
    }

    /**
     * If user is on backend-level
     * 1. blocking notifications (unclosable modal) may be created
     * if not:
     * 2. if admin welcome-page is called, the
     *    limitReminderNotification and outOfresponsesPaid functionalities will be called
     * 3. promotionalBanner showing will be checked on every action
     */
    public function beforeControllerAction()
    {
        $controller = $this->getEvent()->get('controller');
        $action = $this->getEvent()->get('action');
        if ($this->isBackendAccess()) {
            $installationData = $this->getInstallationData();

            if (!$this->createBlockingNotifications($this->getEvent(), $installationData)) {
                if ($controller === 'admin' && $action === 'index') {
                    $limitReminderNotification = new LimitReminderNotification($this);
                    $limitReminderNotification->createNotification($installationData);

                    $outOfResponsesPaid = new OutOfResponsesPaid($this);
                    $outOfResponsesPaid->createNotification($installationData);

                    // Deactivated because of insufficient data on cloud side
                    //only usefull for paying users
                    //                    if ($installationData->isPayingUser) {
                    //                        $gracePeriodNotification = new GracePeriodNotification($this);
                    //                        $gracePeriodNotification->createNotification($installationData);
                    //                    }
                }
            }
            $today = new \DateTime('midnight');
            $promotionalBanner = new PromotionalBanners($this);
            $promotionalBanner->showPromotionalBanner($today, $installationData);
        }
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
     *
     * @param \LimeSurvey\PluginManager\PluginEvent $event
     * @return boolean
     * @throws Exception if there's no event
     */
    public function forceRedirectToWelcomePage($event = null)
    {
        if (is_null($event)) {
            return false;
        }
        $controller = $event->get('controller');
        $action = $event->get('action');
        $subAction = $event->get('subaction');
        return !($controller == 'admin' && $action == 'index') && $subAction != 'logout';
    }

    /**
     *  returns populated InstallationData
     *
     * @return \LimeSurveyProfessional\InstallationData
     */
    private function getInstallationData()
    {
        $installationData = new \LimeSurveyProfessional\InstallationData();
        $installationData->create(
            new \LimeSurvey\Models\Services\LimeserviceSystem(
                \Yii::app()->dbstats,
                (int)getInstallationID()
            ),
            App()->user->id == 1
        );

        return $installationData;
    }

    /**
     * All notification types which use unclosable modal sorted by importance
     * (most important first)
     * When a blocking modal is successfully created, it will be shown and the leftover classes won't be processe
     *
     * Attention: Every class inside array $blockingNotifications needs to have the function createNotification()
     * createNotification() needs to return a boolean!
     * @param \LimeSurvey\PluginManager\PluginEvent $event
     * @param \LimeSurveyProfessional\InstallationData $installationData
     * @return boolean
     */
    public function createBlockingNotifications($event, $installationData)
    {
        $blockingNotification = false;
        $blockingNotifications = [
            ['class' => 'LimeSurveyProfessional\notifications\HardLockModal'],
            ['class' => 'LimeSurveyProfessional\notifications\OutOfResponsesFree'],
        ];

        foreach ($blockingNotifications as $notification) {
            $className = $notification['class'];
            $blockingNotificationClass = new $className($this);
            $blockingNotification = $blockingNotificationClass->createNotification($installationData);
            if ($blockingNotification) {
                break;
            }
        }

        if ($blockingNotification && $this->forceRedirectToWelcomePage($event)) {
            $controller = Yii::app()->getController();
            $controller->redirect($controller->createUrl('admin/index'));
        }

        return $blockingNotification;
    }

    /**
     *  Before a tokenEmail is sent, it will run through a blacklist filter, except this installation is whitelisted
     *  via 'disableEmailSpamChecking' config param
     */
    public function beforeTokenEmail()
    {
        if (!Yii::app()->getConfig("disableEmailSpamChecking")) {
            $installationData = $this->getInstallationData();
            $blacklistFilter = new \LimeSurveyProfessional\email\EmailFilter($this->getEvent());
            $blacklistFilter->filter($installationData);
        }
    }

    /**
     * Append new menu item to the admin topbar
     *
     * @return void
     */
    public function beforeAdminMenuRender()
    {
        $installationData = $this->getInstallationData();
        $upgradeButton = new \LimeSurveyProfessional\upgradeButton\UpgradeButton();
        $upgradeButton->displayUpgradeButton($this, $installationData);
    }

    /**
     * @return void
     */
    public function newDirectRequest()
    {
        $request = $this->api->getRequest();
        $event = $this->getEvent();
        if ($event->get('target') != 'LimeSurveyProfessional') {
            return;
        }

        $action = $event->get('function');
        if ($action == 'updateBannersAcknowledgedObject') {
            $installationData = $this->getInstallationData();
            $promotionalBanner = new PromotionalBanners($this);
            $promotionalBanner->updateBannersAcknowledgedObject($request, $installationData);
        }
    }

    /**
     * Initialise ParticipantRegisterCta before rendering
     *
     * We need to do this because we can not render partials within beforeCloseHtml or afterSurveyComplete.
     * Instead we render in advance.
     *
     * @return void
     */
    public function beforeSurveyPage()
    {
        // Use getInstance() to initialise the ParticipantRegisterCta instance
        // - so that it can be re-used in other call backs.
        // Missing variable assignment is intentional.
        ParticipantRegisterCta::getInstance(
            $this,
            $this->getInstallationData()
        );
    }

    /**
     * Is Survey Complete
     *
     * @return void
     */
    public function isComplete()
    {
        $isSurveyController = Yii::app()->controller->getId() == 'survey';
        $iSurveyID = Yii::app()->request->getQuery('sid');
        $session = isset(Yii::app()->session['survey_' . $iSurveyID])
            ? Yii::app()->session['survey_' . $iSurveyID]
            : [];
        return $isSurveyController && $iSurveyID && (
            $this->complete || (
                !empty($session['finished'])
            )
        );
    }

    /**
     * Is Survey in Progress
     *
     * @return void
     */
    public function isViewingSurvey()
    {
        $session = Yii::app()->session;
        $controller = Yii::app()->controller;
        $action = $controller ? $controller->getAction() : null;
        $isSurveyController = $controller ? $controller->getId() == 'survey' : false;
        $isSurveyAction = $action? $action->getId() == 'index' : false;
        $sid = Yii::app()->request->getQuery('sid');
        return $isSurveyController
            && $isSurveyAction
            && isset($session['survey_' . $sid]);
    }

    /**
     * Append new content to the HTML body
     *
     * @return void
     */
    public function beforeCloseHtml()
    {
        if ($this->isViewingSurvey() && !$this->isComplete()) {
            $participantRegisterCta =
                ParticipantRegisterCta::getInstance(
                    $this,
                    $this->getInstallationData()
                );
            $participantRegisterCta->display();
        }
    }


    /**
     * After survey is completed
     *
     * @return void
     */
    public function afterSurveyComplete()
    {
        $this->complete = true;
        $participantRegisterCta =
            ParticipantRegisterCta::getInstance(
                $this,
                $this->getInstallationData()
            );
        $participantRegisterCta->display(true);
    }

    /**
     * Get config to allow plugin config to be read from plugin "sub-modules".
     *
     * This function retrieves plugin data. Do not cache this data; the plugin storage
     * engine will handling caching. After the first call to this function, subsequent
     * calls will only consist of a few function calls and array lookups.
     *
     * @param string $key
     * @param string $model
     * @param int $id
     * @param mixed $default The default value to use when not was set
     * @return boolean
     */
    public function getConfig($key = null, $model = null, $id = null, $default = null)
    {
        return $this->get($key, $model, $id, $default);
    }
}
