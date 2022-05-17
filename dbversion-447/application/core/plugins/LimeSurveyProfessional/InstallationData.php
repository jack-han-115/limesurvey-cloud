<?php

namespace LimeSurveyProfessional;

use LimeSurvey\Models\Services\LimeserviceSystem;

class InstallationData
{
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

    /** @var string */
    public $accessToken;

    /** @var string */
    public $apiId;

    /** @var string */
    public $apiSecret;

    /**
     * Populates this data object with relevant data of the installation
     * @param LimeserviceSystem $limeserviceSystem
     * @param int $userId
     * @throws \CException
     */
    public function create(LimeserviceSystem $limeserviceSystem, int $userId)
    {
        $this->isHardLocked = $limeserviceSystem->getHardLock() === 1;
        $this->plan = $limeserviceSystem->getUsersPlan();
        $this->isSiteAdminUser = $userId;
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

        $accessTokenSetting = \SettingGlobal::model()->findByAttributes(['stg_name' => 'AccessToken']);
        $this->accessToken = $accessTokenSetting ? $accessTokenSetting->stg_value : '';
        $apiIdSecretResult = $limeserviceSystem->getApiIdAndSecret();
        $this->apiId = $apiIdSecretResult['installation_api_id'];
        $this->apiSecret = $apiIdSecretResult['installation_api_secret'];
    }
}
