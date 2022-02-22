<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\DataTransferObject;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class OutOfResponsesFree extends UnclosableModal
{
    /**
     * Constructor for OutOfResponsesFree
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
     * @param DataTransferObject $dto
     *
     * @return boolean
     */
    public function createNotification(DataTransferObject $dto)
    {
        $notificationCreated = false;

        if (!$dto->isPayingUser && ($dto->outOfResponses || $dto->locked)) {
            $notificationCreated = true;
            $this->title = $this->plugin->gT('Maximum number of responses reached');
            $this->initMessage($dto->isSiteAdminUser);
            $this->initButton(new LinksAndContactHmtlHelper(), $dto->isSiteAdminUser);
            $this->showModal();
        }

        return $notificationCreated;
    }

    /**
     * Generates and sets the html formatted message of the notification
     * @param bool $isSiteAdminUser
     */
    private function initMessage(bool $isSiteAdminUser)
    {
        $this->message = $this->plugin->gT(
                'You have reached the maximum number of responses allowed for your chosen plan.'
            ) . ' ';

        if ($isSiteAdminUser) {
            $this->message .= $this->plugin->gT(
                'Please upgrade your plan to increase your responses.'
            );
        } else {
            $this->message .= $this->plugin->gT(
                'Please contact your Survey Site Administrator to upgrade the plan to increase your responses.'
            );
        }
    }

    /**
     * Generates and sets the button html
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     */
    private function initButton(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $buttonText = $this->plugin->gT('Upgrade / Purchase responses');
            $this->buttons[] = $links->toHtmlLinkButton('https://account.limesurvey.org/', $buttonText);
        } else {
            $this->buttons[] = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Adminstrator')
            );
        }
    }
}
