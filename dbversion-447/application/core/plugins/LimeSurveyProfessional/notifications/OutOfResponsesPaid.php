<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\DataTransferObject;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

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
     * @param DataTransferObject $dto
     */
    public function createNotification(DataTransferObject $dto)
    {
        if ($dto->isPayingUser && $dto->outOfResponses) {
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->plugin->gT('Maximum Number of Responses Reached'),
                'message' => $this->getMessage($dto->isSiteAdminUser)
            ));
            $not->save();
        }
    }

    /**
     * Returns whole message for the notification
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getMessage(bool $isSiteAdminUser)
    {
        $links = new LinksAndContactHmtlHelper();
        return $this->getMainString($isSiteAdminUser) . $this->getButton($links, $isSiteAdminUser);
    }

    /**
     * Returns the main message html for the notification
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getMainString(bool $isSiteAdminUser)
    {
        $message = $this->plugin->gT(
                'You have reached the maximum number of responses allowed for your chosen plan.'
            ) . ' ';

        if ($isSiteAdminUser) {
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
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getButton(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $buttonText = $this->plugin->gT('Renew plan / Purchase responses');
            $button = $links->toHtmlLinkButton('https://account.limesurvey.org/', $buttonText);
        } else {
            $button = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Admin')
            );
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }

}
