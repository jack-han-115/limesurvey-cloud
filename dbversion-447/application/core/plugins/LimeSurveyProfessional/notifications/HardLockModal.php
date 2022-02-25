<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\InstallationData;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class HardLockModal extends UnclosableModal
{
    /**
     * Constructor for HardLock
     *
     * $plugin needs to be available here for plugin translation function
     *
     * @param \LimeSurveyProfessional $plugin
     * @throws \CException
     */
    public function __construct(\LimeSurveyProfessional $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * creates the unpaid invoice or normal hard_lock notification, if required
     * @param InstallationData $installationData
     *
     * @return boolean
     */
    public function createNotification(InstallationData $installationData)
    {
        $notificationCreated = false;
        if ($installationData->isHardLocked) {
            $links = new LinksAndContactHmtlHelper();
            $notificationCreated = true;
            $this->title = $this->plugin->gT('Account locked');
            $this->initMessage($links, $installationData->isSiteAdminUser);
            $this->initButtons($links, $installationData->isSiteAdminUser);

            $this->showModal();
        }

        return $notificationCreated;
    }


    /**
     * Generates and sets the default html formatted message of the notification for installation hardlock
     *
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     */
    private function initMessage(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $this->message = sprintf(
                $this->plugin->gT(
                    'Your survey site has been locked due to an unpaid invoice or suspected abuse. To unlock your account, please view and pay the invoice from the %s tab of your LimeSurvey account homepage. If you do not have an unpaid invoice, please contact our customer support.'
                ),
                $links->toHtmlLink(
                    $links->getTransactionHistoryLink(\Yii::app()->session['adminlang']),
                    $this->plugin->gT('transaction history')
                )
            );
        } else {
            $this->message = sprintf(
                $this->plugin->gT(
                    'Your survey site has been locked due to an unpaid invoice or suspected abuse. Please contact your survey site admin, %s, to unlock the survey site.'
                ),
                $links->toHtmlMailLink($links->getSiteAdminEmail())
            );
        }
    }

    /**
     * Generates and sets the default button html for installation hardlock
     *
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     *
     */
    private function initButtons(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $this->buttons[] = $links->toHtmlLinkButton(
                $links->getTransactionHistoryLink(\Yii::app()->session['adminlang']),
                $this->plugin->gT('Pay invoice')
            );
            $this->buttons[] = $links->toHtmlMailLinkButton(
                'support@limesurvey.org',
                $this->plugin->gT('Contact LimeSurvey support')
            );
        } else {
            $this->buttons[] = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Adminstrator')
            );
        }
    }


}
