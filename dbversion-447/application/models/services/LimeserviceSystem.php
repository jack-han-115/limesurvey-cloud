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

    const PLAN_FREE = 'free';

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
     * @throws \CException
     */
    public function getReminderLimitResponses()
    {
        $reminderLimitResponses = $this->dbConnection->createCommand()
            ->select('reminderlimitresponses')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();

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
     * @throws \CException
     */
    public function getResponsesAvailable()
    {
        return (int)$this->dbConnection->createCommand()
            ->select('responses_avail')
            ->from('limeservice_system.balances')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Returns the value for 'hard_lock' from table installations
     *
     * @return int
     * @throws \CException
     */
    public function getHardLock()
    {
        return (int)$this->dbConnection->createCommand()
            ->select('hard_lock')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Returns the value for 'locked' from table installations
     *
     * @return int
     * @throws \CException
     */
    public function getLocked()
    {
        return (int)$this->dbConnection->createCommand()
            ->select('locked')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Calculate remaining storage in percent
     *
     * @return float
     * @throws \CException
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
     * @throws \CException
     */
    public function getUploadStorageSize()
    {
        return (int)$this->dbConnection->createCommand()
            ->select('upload_storage_size')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Gets the used storage from database.
     *
     * @return float
     * @throws \CException
     */
    public function getStorageUsed()
    {
        return $this->dbConnection->createCommand()
            ->select('storage_used')
            ->from('limeservice_system.balances')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Gets the user plan from table installations. This value is a string e.g. 'free'.
     *
     * @return mixed
     * @throws \CException
     */
    public function getUsersPlan()
    {
        return $this->dbConnection->createCommand()
            ->select('subscription_alias')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Gets reminderlimitstorage value from table installations.
     *
     * @return int
     * @throws \CException
     */
    public function getReminderLimitStorage()
    {
        return (int)$this->dbConnection->createCommand()
            ->select('reminderlimitstorage')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * @return int
     * @throws \CException
     */
    public function getEmailLock()
    {
        return (int)$this->dbConnection->createCommand()
            ->select('email_lock')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * @param int $value
     * @return int
     * @throws \CDbException
     */
    public function setEmailLock(int $value = 1)
    {
        return $this->dbConnection->createCommand()
            ->update(
                'limeservice_system.installations',
                ['email_lock' => $value],
                "user_id=:user_id",
                [':user_id' => $this->userInstallationId]
            );
    }

    /**
     * Update "sent" counter in table mail_ratings
     *
     * todo: not used at the moment (will be used in future for blacklisting emails?)
     *
     * @param int $value
     * @return int
     * @throws \CDbException
     */
    public function increaseSentCount(int $value = 1)
    {
      /*  $sql = "UPDATE limeservice_system.mail_ratings
                SET sent = sent + {$value}
                WHERE installation_id=" . getInstallationID();

        return $this->dbConnection->createCommand($sql)->execute(); */

        return $this->dbConnection->createCommand()
            ->update(
                'limeservice_system.mail_ratings',
                ['sent' => new \CDbExpression("sent + $value")],
                "installation_id=:installation_id",
                [':installation_id' => $this->userInstallationId]
            );
    }

    /**
     * Returns subscription_created value from table installations
     *
     * @return \CDbDataReader|false|mixed|string
     * @throws \CException
     */
    public function getSubscriptionCreated()
    {
        return $this->dbConnection->createCommand()
            ->select('subscription_created')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Returns subscription_paid value from table installations
     *
     * @return \CDbDataReader|false|mixed|string
     * @throws \CException
     */
    public function getSubscriptionPaid()
    {
        return $this->dbConnection->createCommand()
            ->select('subscription_paid')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Returns subscription_period value from table installations
     *
     * @return \CDbDataReader|false|mixed|string
     * @throws \CException
     */
    public function getSubscriptionPeriod()
    {
        return $this->dbConnection->createCommand()
            ->select('subscription_period')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryScalar();
    }

    /**
     * Returns client_id value from table installations
     *
     * @return \CDbDataReader|mixed
     * @throws \CException
     */
    public function getApiIdAndSecret()
    {
        return $this->dbConnection->createCommand()
            ->select('installation_api_id, installation_api_secret')
            ->from('limeservice_system.installations')
            ->where('user_id=:user_id', [':user_id' => $this->userInstallationId])
            ->queryRow();
    }
}
