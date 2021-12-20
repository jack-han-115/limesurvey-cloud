<?php

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

    protected $settings = array();

    /** @var \LimeSurvey\Models\Services\LimeserviceSystem */
    public $limeserviceSystem;

    /** @var boolean */
    public $isSuperAdminReadUser;

    /**
     * @return void
     */
    public function init()
    {
        \Yii::setPathOfAlias(get_class($this), dirname(__FILE__));
        $this->subscribe('beforeDeactivate'); // user should not be able to deactivate this one ...
        $this->subscribe('beforeControllerAction');
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
     * 1. if admin welcome-page is called, the limitReminderNotification function will be called
     * @TODO other subtasks of this epic can be handled here also
     * @TODO eg. the blocked modal for unpaid invoices can be shown and user has no chance to manually redirect anywhere
     */
    public function beforeControllerAction()
    {
        $this->limeserviceSystem = new \LimeSurvey\Models\Services\LimeserviceSystem(
            \Yii::app()->dbstats,
            (int)getInstallationID()
        );
        $this->isSuperAdminReadUser = \Permission::model()->hasGlobalPermission('superadmin', 'read');

        $controller = $this->getEvent()->get('controller');
        $action = $this->getEvent()->get('action');

        if ($controller === 'admin' && $action === 'index') {
            $limitReminderNotification = new \LimeSurveyProfessional\notifications\LimitReminderNotification($this);
            $limitReminderNotification->createNotification();
        }
    }


}
