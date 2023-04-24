<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;
use LimeSurveyProfessional\InstallationData;

class EmailFilter
{
    /** @var PluginEvent */
    public $event;

    /** @var String type of email (remind, invite, register, confirm) */
    public $emailType;

    /** @var int contains the number of violations for this installation */
    public $counter;

    /** @var bool if email is of type remind */
    public $isRemind;

    /** @var bool if email is of type invite */
    public $isInvite;

    /** @var bool if email is of type register */
    public $isRegister;

    /** @var bool if email is of type confirm */
    public $isConfirm;

    /** @var array the loaded config from blacklistConfig.php */
    public $blacklistConfig;

    /**
     * Constructor for EmailFilter
     *
     * This is for filtering emails of type invite, remind, confirm and register.
     * Filters:
     * A) Strings which are stored in a blacklist. (file blackListConfig)
     * B) Links in those mails which are not related to token-links. (only if installations have advertising > 0).
     *
     * If filter A hits, a violation counter will be raised by one.
     * For invite and reminder emails, this counter is stored in the static $violationCount as there are bulk sends possible.
     *
     * For confirm and register emails, we use a new setting in table settings_global, where the counter
     * will only be raised if there are multiple attempts within the configured timeLimit in file blackListConfig.
     *
     * If the counter hits the random threshold (currently between 3 and 5), this email will not be sent and
     * field email_lock of table installations will be set to 2, so all future emails will fail silently.
     * In parallel a mail with additional info will be sent to alerts@limesurvey.org.
     *
     * This whole filtering can be deactivated for installations if the setting "disableEmailSpamChecking"
     * in config-defaults.php will be set to true.
     *
     * @param PluginEvent $event
     */
    public function __construct(PluginEvent $event)
    {
        $this->event = $event;
        $this->emailType = $this->event->get('type', '');
        $this->isRemind = $this->emailType == 'remind';
        $this->isInvite = $this->emailType == 'invite';
        $this->isRegister = $this->emailType == 'register';
        $this->isConfirm = $this->emailType == 'confirm';

        include(\Yii::getPathOfAlias('LimeSurveyProfessional') . '/email/blacklistConfig.php');
        /** @var  array $blacklistConfig */
        $this->blacklistConfig = $blacklistConfig;
        $this->counter = $this->getRecentCounter();
    }

    /**
     * Blacklist and SpamLink filters will be initialized here
     * @param InstallationData $installationData
     */
    public function filter(InstallationData $installationData)
    {
        // If installation is already blocked (emailLock == 2) for email, we don't need to filter
        if ($installationData->emailLock != 2) {
            $blacklistFilter = new BlacklistFilter($this->event);
            $blacklistFilter->filterBlacklist();

            $linkFilter = new LinkFilter($this->event);
            if ($installationData->hasAdvertising) {
                $hasExternalLink = $linkFilter->lookForSpamLinks();
                if ($hasExternalLink) {
                    $this->pretendEmailSent(true);
                }
            }
        }

        if ($installationData->emailLock == 2 || $this->violationCounterThresholdIsReached()) {
            $this->handleViolationCase($installationData->emailLock);
        }
    }

    /**
     * Calls functions to handle an email send attempt after too many violations
     * Spam alert email only goes out, if the installation was not on emailLock = 2 before.
     * @param int $emailLock email_lock value from table installations
     */
    private function handleViolationCase(int $emailLock)
    {
        $this->pretendEmailSent();

        if ($emailLock != 2) {
            $limeserviceSystem = new \LimeSurvey\Models\Services\LimeserviceSystem(
                \Yii::app()->dbstats,
                (int)getInstallationID()
            );
            $this->sendSpamAlertEmail();
            $limeserviceSystem->setEmailLock(2);
        }
    }

    /**
     * lets LimeSurvey assume that the email was sent in this plugin
     * but doesn't send the email.
     *
     * If externalLink is true, and email is of type register, an error message will be set additionally
     * @param bool $externalLink
     */
    private function pretendEmailSent(bool $externalLink = false)
    {
        $this->event->set('send', false);
        if ($this->isRegister) {
            if ($externalLink) {
                $this->event->set('error', gT("Invalid external links in email"));
            } else {
                $this->event->set('error', gT("Invalid Content."));
            }
        }

        if ($this->isInvite || $this->isRemind) {
            usleep(6000);
        }
    }

    /**
     * notifies LS that this Filter was activated
     */
    private function sendSpamAlertEmail()
    {
        $mailer = \mailHelper::getMailer();
        $installationId = getInstallationID();
        $loggedInUser = \Yii::app()->session['user'] ? \Yii::app()->session['user'] : 'no user logged in';

        $violationBody = $this->event->get('body', '');
        $violationSubject = $this->event->get('subject', '');
        $violationTo = $this->event->get('to', '');
        $violationType = $this->event->get('type', '');
        $violationSurvey = $this->event->get('survey', '');
        $violationFrom = $this->event->get('from', '');

        $message = '<p>Email blacklist filter was activated! ' .
            'Field email_lock is now set to 2. Future emails of this installation ID ' . $installationId . ' will not be sent, silently</p>' .
            '<p><b>Filtered email:</b></p>' .
            '<b>Matching blacklist-keyword:</b> ' . \LimeSurveyProfessional::$violationText . '<br>' .
            '<b>Sent by user:</b> ' . $loggedInUser . '<br>' .
            '<b>Type:</b> ' . $violationType . '<br>' .
            '<b>From:</b> ' . $violationFrom . '<br>' .
            '<b>To:</b> ' . print_r($violationTo, true) . '<br>' .
            '<b>Survey:</b> ' . $violationSurvey . '<br>' .
            '<b>Subject:</b> ' . $violationSubject . '<br>' .
            '<b>Body:</b> ' . '<p><pre>' . $violationBody . '</pre></p>' .
            '<p>-----------------------------------------------------</p>' .
            '<b>$_SERVER:</b> ' . '<br><pre>' . print_r($_SERVER, true) . '</pre>';

        $mailer->Subject = 'Possible email policy violation (Installation: ' . $installationId . ' User: ' . $loggedInUser . ')';
        $mailer->addAddress('alerts@limesurvey.org');
        $mailer->Body = $message;
        $mailer->FromName = $_SERVER['SERVER_NAME'];
        $mailer->Sender = $mailer->From = 'noreply@limesurvey.org';
        $mailer->isHTML(true);

        $mailer->send();
    }

    /**
     * Returns recent violation counter for this installation
     * @return int
     */
    private function getRecentCounter()
    {
        $counter = 0;
        if ($this->isRemind || $this->isInvite) {
            $counter = \LimeSurveyProfessional::$violationCount;
        } elseif ($this->isConfirm || $this->isRegister) {
            $counter = $this->getGlobalSettingsCounter();
        }

        return $counter;
    }

    /**
     * Raises the violation counter. Invite and reminder mails use a static variable,
     * for register and confirm a setting in db table settings_global is used.
     *
     * $violationText will be stored in the static for future use in the alert email.
     *
     * @param string $violationText will be stored in the static for future use in the alert email
     */
    public function raiseViolationCount(string $violationText)
    {
        $this->counter++;
        \LimeSurveyProfessional::$violationText = $violationText;
        if ($this->isRemind || $this->isInvite) {
            \LimeSurveyProfessional::$violationCount = $this->counter;
        } elseif ($this->isConfirm || $this->isRegister) {
            $settingsValue = [
                'violationCount' => $this->counter,
                'time' => time()
            ];

            \SettingGlobal::setSetting('emailViolationCounter', ls_json_encode($settingsValue));
        }
    }

    /**
     * * Gets the violationCounter setting from settings_global table,
     * and checks if the counter value is from within the last x minutes.
     *
     * If true the stored value will be returned, else 0.
     * @return int
     */
    private function getGlobalSettingsCounter()
    {
        $violationCounter = 0;
        $setting = App()->getConfig('emailViolationCounter');

        if ($setting) {
            $decodedSetting = json_decode_ls($setting);
            $storedTime = $decodedSetting['time'];
            $subtractMinutesString = '-' . $this->blacklistConfig['timeLimit'] . ' minutes';
            //check if stored time is within minute timeframe stored in blacklistConfig
            if ($storedTime > strtotime($subtractMinutesString)) {
                $violationCounter = (int)$decodedSetting['violationCount'];
            }
        }

        return $violationCounter;
    }

    /**
     * Checks if the randomized violation threshold from the config file is reached
     * @return bool true when violationThreshold is reached
     */
    public function violationCounterThresholdIsReached()
    {
        return $this->counter >= $this->blacklistConfig['violationThreshold'];
    }
}
