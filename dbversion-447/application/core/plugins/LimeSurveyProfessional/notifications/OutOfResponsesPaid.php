<?php

namespace LimeSurveyProfessional\notifications;

class OutOfResponsesPaid
{
    /** @var \LimeSurveyProfessional */
    private $plugin;

    /**
     * Constructor for OutOfResponsesPaid
     *
     * $plugin needs to be available here for plugin translation function
     *
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(\LimeSurveyProfessional $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * creates the notification when paid installation is out of responses
     */
    public function createNotification()
    {
        if ($this->plugin->isPayingUser && $this->plugin->outOfResponses) {
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->plugin->gT('Maximum Number of Responses Reached'),
                'message' => $this->getMessage()
            ));
            $not->save();
        }
    }

    /**
     * Returns whole message for the notification
     * @return string
     */
    private function getMessage()
    {
        return $this->getMainString() . $this->getButton();
    }

    /**
     * Returns the main message html for the notification
     * @return string
     */
    private function getMainString()
    {
        $message = $this->plugin->gT(
                'You have reached the maximum number of responses allowed for your chosen plan.'
            ) . ' ';

        if ($this->plugin->isSuperAdminReadUser) {
            $message .= $this->plugin->gT(
                'Please renew plan or purchase more responses.'
            );
        } else {
            $message .= $this->plugin->gT(
                'Please contact your Survey Site Administrator to renew plan or purchase more responses.'
            );
        }

        return '<p>' . $message . '</p>';
    }

    /**
     * Returns the button html for the notification
     * @return string
     */
    private function getButton()
    {
        if ($this->plugin->isSuperAdminReadUser) {
            $buttonText = $this->plugin->gT('Renew plan / Purchase responses');
            $button = '<a class="btn btn-primary" href="https://account.limesurvey.org/" target="_blank">' .
                '<span class="fa fa-external-link"></span>&nbsp;' . $buttonText . '</a>';
        } else {
            $button = '<a class="btn btn-primary" href="mailto:' . $this->plugin->getSiteAdminEmail() . '">' .
                '<span class="fa fa-envelope"></span>&nbsp;' . $this->plugin->gT('Contact Survey Site Admin') . '</a>';
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }

}
