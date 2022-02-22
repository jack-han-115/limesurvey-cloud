<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;

class BlacklistFilter extends EmailFilter
{

    /**
     * Constructor for BlacklistFilter
     *
     *
     * @param PluginEvent $event
     */
    public function __construct(PluginEvent $event)
    {
        parent::__construct($event);
    }

    /**
     * Calls the detectSpam function and takes further action if that returns true.
     * This function exists, so we can test the detectSpam function.
     * @param int $emailLock
     */
    public function filterBlacklist(int $emailLock)
    {
        $emailMethod = \Yii::app()->getConfig('emailmethod');
        $folder = \Yii::getPathOfAlias('LimeSurveyProfessional');
        if ($this->detectSpam($emailMethod, $folder, $emailLock)) {
            $this->handleViolationCase($emailLock);
        }
    }

    /**
     * Checks if email body, subject or replyto contain blacklisted words/sentences.
     * If so, static $violationCount will be raised, and search will be ended.
     * If $violationCount reaches set $violationThreshold of blacklistConfig
     * return will be "true".
     * @param string $emailMethod
     * @param string $folder
     * @param int $emailLock
     * @return bool
     */
    public function detectSpam(string $emailMethod, string $folder, int $emailLock)
    {
        $spamDetected = false;
        if ($emailMethod != 'smtp') {
            include($folder . '/email/blacklistConfig.php');

            $emailBody = $this->event->get('body', '');
            $emailSubject = $this->event->get('subject', '');
            $emailReplyTo = implode(' ', $this->event->get('replyto', ''));
            /** @var int $violationThreshold */
            /** @var array $blacklistEntries */
            foreach ($blacklistEntries as $entry) {
                if ($emailLock == 2 || \LimeSurveyProfessional::$violationCount >= $violationThreshold) {
                    $spamDetected = true;
                    break;
                }
                if (stripos($emailBody, $entry) !== false || stripos($emailSubject, $entry) !== false || stripos(
                        $emailReplyTo,
                        $entry
                    ) !== false) {
                    \LimeSurveyProfessional::$violationCount++;
                    \LimeSurveyProfessional::$violationText = $entry;
                    break;
                }
            }
        }

        return $spamDetected;
    }

    /**
     * Calls functions to handle an email send attempt after too many violations
     * @param int $emailLock
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
     * but doesn't send the email
     */
    private function pretendEmailSent()
    {
        $this->event->set('send', false);
        usleep(6000);
    }

    /**
     * notifies LS that this Filter was activated
     */
    private function sendSpamAlertEmail()
    {
        $mailer = \mailHelper::getMailer();
        $installationId = getInstallationID();
        $loggedInUser = \Yii::app()->session['user'];

        $violationBody = $this->event->get('body', '');
        $violationSubject = $this->event->get('subject', '');
        $violationTo = $this->event->get('to', '');
        $violationType = $this->event->get('type', '');
        $violationSurvey = $this->event->get('survey', '');
        $violationFrom = $this->event->get('from', '');

        $message = '<p>Email blacklist filter was activated! ' .
            'Field email_lock is now set to 2. Future emails of this installation ID ' . $installationId .' will not be sent, silently</p>' .
            '<p><b>Filtered email:</b></p>' .
            '<b>Matching keyword:</b> ' . \LimeSurveyProfessional::$violationText . '<br>' .
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
}
