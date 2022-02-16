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

    public function build()
    {
        $limeserviceSystem = new \LimeSurvey\Models\Services\LimeserviceSystem(
            \Yii::app()->dbstats,
            (int)getInstallationID()
        );
        $this->isHardLocked = $limeserviceSystem->getHardLock() === 1;
        $this->plan = $limeserviceSystem->getUsersPlan();
        $this->isSiteAdminUser = App()->user->id == 1;
        $this->isPayingUser = $this->plan !== 'free' && $this->plan != '';
        $this->outOfResponses = $limeserviceSystem->getResponsesAvailable() < 0;
        $this->locked = $limeserviceSystem->getLocked() == 1;
        $this->emailLock = $limeserviceSystem->getEmailLock();
        $this->dateSubscriptionPaid = $limeserviceSystem->getSubscriptionPaid();
        $this->dateSubscriptionCreated = $limeserviceSystem->getSubscriptionCreated();
        $this->paymentPeriod = $limeserviceSystem->getSubscriptionPeriod();
        $this->reminderLimitStorage = $limeserviceSystem->getReminderLimitStorage();
        $this->reminderLimitResponses = $limeserviceSystem->getReminderLimitResponses();
        $this->hasResponseNotification = $limeserviceSystem->showResponseNotificationForUser();
        $calcRestStoragePercent = $limeserviceSystem->calcRestStoragePercent();
        $this->hasStorageNotification = $calcRestStoragePercent > 0
            && $calcRestStoragePercent < $this->reminderLimitStorage;
    }
}
