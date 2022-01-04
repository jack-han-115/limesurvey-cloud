<?php

namespace LimeSurveyProfessional\notifications;

class OutOfResponsesPaid extends UnclosableModal
{


    /**
     * Constructor for InvoiceAfterGracePeriodNotification
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
        if ($this->plugin->outOfResponses) {
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
        if ($this->plugin->isSuperAdminReadUser) {
            $this->message = $this->plugin->gT(
                'You have reached the maximum number of responses allowed for your chosen plan.  Please renew plan or purchase more responses.'
            );
        } else {
            $this->message = $this->plugin->gT(
                'You have reached the maximum number of responses allowed for your chosen plan.  Please contact your Survey Site Administrator to renew plan or purchase more responses.'
            );
        }
    }

    /**
     * Generates and sets the button html
     *
     */
    private function getButton()
    {
        if ($this->plugin->isSuperAdminReadUser) {
            $this->buttons[] = '<a class="btn btn-primary" href="https://account.limesurvey.org/" target="_blank">' .
                $this->plugin->gT('Renew plan') . '</a>';
        } else {
            $this->buttons[] = '<a class="btn btn-primary" href="mailto:' . $this->plugin->getSiteAdminEmail() . '">' .
                $this->plugin->gT('Contact Survey Site Admin') . '</a>';
        }
    }
}
