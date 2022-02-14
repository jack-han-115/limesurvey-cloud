<?php

namespace LimeSurveyProfessional;

use LimeSurvey\Models\Services\LimeserviceSystem;

class DataTransferObject
{
    /** @var \LimeSurvey\Models\Services\LimeserviceSystem */
    public $limeserviceSystem;

    /** @var boolean */
    public $isSiteAdminUser;

    /** @var boolean */
    public $isPayingUser;

    /** @var bool */
    public $outOfResponses;

    /** @var boolean */
    public $isHardLocked;

    /** @var boolean */
    public $locked;

    /** @var int */
    public $emailLock;

    /** @var string|null */
    public $dateSubscriptionPaid;

    /** @var string|null */
    public $dateSubscriptionCreated;

    /** @var string|null */
    public $paymentPeriod;

    /** @var string */
    public $plan;

    /** @var int */
    public $reminderLimitStorage;

    /** @var int */
    public $reminderLimitResponses;

    /** @var boolean */
    public $hasResponseNotification;

    /** @var boolean */
    public $hasStorageNotification;


    public function __construct()
    {
        $this->limeserviceSystem = new \LimeSurvey\Models\Services\LimeserviceSystem(
            \Yii::app()->dbstats,
            (int)getInstallationID()
        );
        $this->build();
    }

    public function build()
    {
        $this->isHardLocked = $this->limeserviceSystem->getHardLock() === 1;
        $this->plan = $this->limeserviceSystem->getUsersPlan();
        $this->isSiteAdminUser = App()->user->id == 1;
        $this->isPayingUser = $this->plan !== 'free' && $this->plan != '';
        $this->outOfResponses = $this->limeserviceSystem->getResponsesAvailable() < 0;
        $this->locked = $this->limeserviceSystem->getLocked() == 1;
        $this->emailLock = $this->limeserviceSystem->getEmailLock();
        $this->dateSubscriptionPaid = $this->limeserviceSystem->getSubscriptionPaid();
        $this->dateSubscriptionCreated = $this->limeserviceSystem->getSubscriptionCreated();
        $this->paymentPeriod = $this->limeserviceSystem->getSubscriptionPeriod();
        $this->reminderLimitStorage = $this->limeserviceSystem->getReminderLimitStorage();
        $this->reminderLimitResponses = $this->limeserviceSystem->getReminderLimitResponses();
        $this->hasResponseNotification = $this->limeserviceSystem->showResponseNotificationForUser();
        $calcRestStoragePercent = $this->limeserviceSystem->calcRestStoragePercent();
        $this->hasStorageNotification = $calcRestStoragePercent > 0
            && $calcRestStoragePercent < $this->reminderLimitStorage;
    }


}
