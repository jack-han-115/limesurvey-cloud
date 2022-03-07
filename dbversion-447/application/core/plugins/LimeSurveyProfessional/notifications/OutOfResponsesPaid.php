<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\InstallationData;
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
     * @param InstallationData $installationData
     */
    public function createNotification(InstallationData $installationData)
    {
        if ($installationData->isPayingUser && $installationData->outOfResponses) {
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->plugin->gT('Maximum number of responses reached'),
                'message' => $this->getMessage($installationData->isSiteAdminUser)
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
                'Please renew your current plan or upgrade to a higher plan.'
            );
        } else {
            $message .= $this->plugin->gT(
                'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your responses.'
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
                $this->plugin->gT('Contact Survey Site Adminstrator')
            );
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }
}
