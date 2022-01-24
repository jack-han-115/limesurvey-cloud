<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;
use PHPMailer\PHPMailer\PHPMailer;

class BlacklistFilter extends EmailFilter
{

    /**
     * Constructor for BlacklistFilter
     *
     *
     * @param PluginEvent $event
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(PluginEvent $event, \LimeSurveyProfessional $plugin)
    {
        parent::__construct($event, $plugin);
    }

    /**
     * Checks if email body contains blacklisted words/sentences.
     * If so, static $violationCount will be raised.
     * If $violationCount reaches set $violationThreshold of blacklistConfig
     * function handleViolationCase() will be called.
     */
    public function detectSpam()
    {
        if (\Yii::app()->getConfig('emailmethod') != 'smtp') {
            $folder = \Yii::getPathOfAlias('LimeSurveyProfessional');
            include($folder . '/email/blacklistConfig.php');

            $emailBody = $this->event->get('body', '');
            $emailSubject = $this->event->get('subject', '');
            $emailReplyTo = implode(' ', $this->event->get('replyto', ''));
            /** @var int $violationThreshold */
            /** @var array $blacklistEntries */
            foreach ($blacklistEntries as $entry) {
                if ($this->emailLock == 2 || \LimeSurveyProfessional::$violationCount >= $violationThreshold) {
                    $this->handleViolationCase();
                    break;
                }
                if (stripos($emailBody, $entry) !== false || stripos($emailSubject, $entry) !== false || stripos($emailReplyTo, $entry) !== false) {
                    \LimeSurveyProfessional::$violationCount++;
                    break;
                }
            }
        }
    }

    /**
     * Calls functions to handle an email send attempt after too many violations
     */
    private function handleViolationCase()
    {
        $this->pretendEmailSent();

        if ($this->emailLock != 2) {
            $this->sendSpamAlertEmail();
            $this->plugin->limeserviceSystem->setEmailLock(2);
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
            'Field email_lock is now set to 2. Future emails of this installationId ' . $installationId .
            ' will not be sent, silently</p>' .
            '<p><b>Filtered Mail:</b></p>' .
            '<b>sent by user:</b> ' . $loggedInUser . '<br>' .
            '<b>type:</b> ' . $violationType . '<br>' .
            '<b>from:</b> ' . $violationFrom . '<br>' .
            '<b>to:</b> ' . print_r($violationTo, true) . '<br>' .
            '<b>survey:</b> ' . $violationSurvey . '<br>' .
            '<b>subject:</b> ' . $violationSubject . '<br>' .
            '<b>body:</b> ' . '<p><pre>' . $violationBody . '</pre></p>' .
            '<p>-----------------------------------------------------</p>' .
            '<b>$_SERVER:</b> ' . '<br><pre>' . print_r($_SERVER, true) . '</pre>';

        $mailer->Subject = 'Possible email violation attempt (Installation: ' . $installationId . ' User: ' . $loggedInUser . ')';
        $mailer->addAddress('alerts@limesurvey.org');
        $mailer->Body = $message;
        $mailer->FromName = $_SERVER['SERVER_NAME'];
        $mailer->Sender = $mailer->From = 'noreply@limesurvey.org';
        $mailer->isHTML(true);

        $mailer->send();
    }
}
