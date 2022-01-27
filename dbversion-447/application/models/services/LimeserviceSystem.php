<?php

namespace LimeSurvey\Models\Services;

/**
 * This class should have all sql-statements for getting data from the
 * database limeservice_systems. Some functions in this class calculate percentages from data
 * values (e.g. "You have x% reponses left")
 *
 */
class LimeserviceSystem
{
    /**
     * This is the default value for reminderlimitresponses
     */
    const DEFAULT_REMINDER_LIMIT_RESPONSES = 10;

    /**
     * The connection to the database.
     *
     * @var \CDbConnection
     */
    private $dbConnection;

    /**
     * The installation id for this user.
     *
     * @var int
     */
    private $userInstallationId;

    /**
     * Initializes class params
     *
     * @param \CDbConnection $dbConnection
     * @param int $userInstallationId
     */
    public function __construct($dbConnection, $userInstallationId)
    {
        $this->dbConnection = $dbConnection;
        $this->userInstallationId = $userInstallationId;
    }


    /**
     * This function compares
     *    "reminderlimitresponses" (this is a value the user can set(on account.limesurvey.org)
     *                           when he would like to be reminded)
     *    with
     *    "responses_avail" (responses still available for him)
     *    he has.
     *    (If responses_available is 0 the notification will not be shown)
     *
     * @return bool  true, if user should be informed, and false otherwise.
     */
    public function showResponseNotificationForUser()
    {
        $reminderLimitResponses = $this->getReminderLimitResponses();
        $responsesAvailable = $this->getResponsesAvailable();

        return $responsesAvailable >= 0 && ($responsesAvailable < $reminderLimitResponses);
    }

    /**
     * Returns the reminderlimitresponse a user has set in
     * account.limesurvey.org (the default value, if not set by user is 10)
     *
     * @return null|int|mixed
     */
    public function getReminderLimitResponses()
    {
        $sql = 'select reminderlimitresponses 
                from limeservice_system.installations 
                where user_id=' . $this->userInstallationId;

        $reminderLimitResponses = \Yii::app()->dbstats->createCommand($sql)->queryScalar();

        if ($reminderLimitResponses === null || $reminderLimitResponses === 0) {
            $reminderLimitResponses = self::DEFAULT_REMINDER_LIMIT_RESPONSES;
        }
        return $reminderLimitResponses;
    }


    /**
     * Returns the available responses from table balances.
     * If the value in database is null, then 0 is returned (fallback scenario)
     *
     * @return int
     */
    public function getResponsesAvailable()
    {
        $sql = "select responses_avail from limeservice_system.balances where user_id=" . $this->userInstallationId;

        $responses = (int)\Yii::app()->dbstats->createCommand($sql)->queryScalar();

        return $responses;
    }

    /**
     * Returns the value for 'hard_lock' from table installations
     *
     * @return int
     */
    public function getHardLock()
    {
        $sql = "select hard_lock from limeservice_system.installations where user_id=" . $this->userInstallationId;

        return (int)\Yii::app()->dbstats->createCommand($sql)->queryScalar();
    }

    /**
     * Returns the value for 'locked' from table installations
     *
     * @return int
     */
    public function getLocked()
    {
        $sql = "select locked from limeservice_system.installations where user_id=" . $this->userInstallationId;

        return (int)\Yii::app()->dbstats->createCommand($sql)->queryScalar();
    }

    /**
     * Calculate remaining storage in percent
     *
     * @return float
     */
    public function calcRestStoragePercent()
    {
        $uploadStorageSize = $this->getUploadStorageSize();
        $usedStorage = $this->getStorageUsed();

        $remainingUploadStorage = $uploadStorageSize - $usedStorage;

        //calc in percent
        return ($remainingUploadStorage / $uploadStorageSize) * 100;
    }


    /**
     * Gets the upload_storage_size from table installations.
     *
     * @return int
     */
    public function getUploadStorageSize()
    {
        $sql = 'select upload_storage_size 
                from limeservice_system.installations 
                where user_id=' . $this->userInstallationId;

        $uploadStorage = (int)\Yii::app()->dbstats->createCommand($sql)->queryScalar();

        return $uploadStorage;
    }

    /**
     * Gets the used storage from database.
     *
     * @return float
     */
    public function getStorageUsed()
    {
        $sql = "select storage_used from limeservice_system.balances where user_id=" . $this->userInstallationId;

        $usedStorage = \Yii::app()->dbstats->createCommand($sql)->queryScalar();

        return $usedStorage;
    }

    /**
     * Gets the user plan from table installations. This value is a string e.g. 'free'.
     *
     * @return mixed
     */
    public function getUsersPlan()
    {
        $sql = 'select subscription_alias 
                from limeservice_system.installations 
                where user_id=' . $this->userInstallationId;

        /* @todo check if there is more then one installation for a user in the table (in that case queryScalar is wrong) */
        $userPlan = $this->dbConnection->createCommand($sql)->queryScalar();

        return $userPlan;
    }

    /**
     * Gets reminderlimitstorage value from table installations.
     *
     * @return int
     */
    public function getReminderLimitStorage()
    {
        $sql = 'select reminderlimitstorage 
                from limeservice_system.installations 
                where user_id=' . $this->userInstallationId;

        return (int)$this->dbConnection->createCommand($sql)->queryScalar();
    }

    /**
     * @return int
     * @throws \CException
     */
    public function getEmailLock()
    {
        $sql = 'select email_lock 
                from limeservice_system.installations 
                where user_id=' . getInstallationID();

        return (int)$this->dbConnection->createCommand($sql)->queryScalar();
    }

    /**
     * @param int $value
     * @return int
     * @throws \CDbException
     */
    public function setEmailLock(int $value = 1)
    {
        $sql = "UPDATE limeservice_system.installations
                SET email_lock = $value  
                WHERE user_id=" . getInstallationID();

        return $this->dbConnection->createCommand($sql)->execute();
    }

    /**
     * @param int $value
     * @return int
     * @throws \CDbException
     */
    public function increaseSentCount(int $value = 1)
    {
        $sql = "UPDATE limeservice_system.mail_ratings
                SET sent = sent + {$value}
                WHERE installation_id=" . getInstallationID();

        return $this->dbConnection->createCommand($sql)->execute();
    }
}
