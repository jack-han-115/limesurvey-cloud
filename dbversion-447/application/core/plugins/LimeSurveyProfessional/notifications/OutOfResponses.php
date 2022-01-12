<?php

namespace LimeSurveyProfessional\notifications;

class OutOfResponses extends UnclosableModal
{


    /**
     * Constructor for OutOfResponses
     *
     * $plugin needs to be available here for plugin translation function
     *
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(\LimeSurveyProfessional $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * creates the unpaid invoice notification, if required
     * Returns true when notification was created
     *
     * @return boolean
     */
    public function createNotification()
    {
        $notificationCreated = false;
        $locked = $this->plugin->limeserviceSystem->getLocked() == 1;
        if ($this->plugin->outOfResponses || $locked) {
            $notificationCreated = true;
            $this->title = $this->plugin->gT('Maximum Number of Responses Reached');
            $this->getMessage();
            $this->getButton();
            $this->showModal();
        }

        return $notificationCreated;
    }

    /**
     * Generates and sets the html formatted message of the notification
     */
    private function getMessage()
    {
        $this->message = $this->plugin->gT(
                'You have reached the maximum number of responses allowed for your chosen plan.'
            ) . ' ';

        if ($this->plugin->isSuperAdminReadUser) {
            if ($this->plugin->isPayingUser) {
                $this->message .= $this->plugin->gT(
                    'Please renew plan or purchase more responses.'
                );
            } else {
                $this->message .= $this->plugin->gT(
                    'Please upgrade your plan or purchase more responses.'
                );
            }
        } else {
            if ($this->plugin->isPayingUser) {
                $this->message .= $this->plugin->gT(
                    'Please contact your Survey Site Administrator to renew plan or purchase more responses.'
                );
            } else {
                $this->message .= $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade plan or purchase more responses.'
                );
            }
        }
    }

    /**
     * Generates and sets the button html
     *
     */
    private function getButton()
    {
        if ($this->plugin->isSuperAdminReadUser) {
            if ($this->plugin->isPayingUser) {
                $buttonText = $this->plugin->gT('Renew plan / Purchase responses');
            } else {
                $buttonText = $this->plugin->gT('Upgrade / Purchase responses');
            }
            $this->buttons[] = '<a class="btn btn-primary" href="https://account.limesurvey.org/" target="_blank">' .
                $buttonText . '</a>';
        } else {
            $this->buttons[] = '<a class="btn btn-primary" href="mailto:' . $this->plugin->getSiteAdminEmail() . '">' .
                $this->plugin->gT('Contact Survey Site Admin') . '</a>';
        }
    }
}
