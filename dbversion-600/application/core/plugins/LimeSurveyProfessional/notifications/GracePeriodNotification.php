<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\apiClient\ApiClient;
use LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl;
use LimeSurveyProfessional\InstallationData;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class GracePeriodNotification
{
    /** @var \LimeSurveyProfessional */
    public $plugin;

    /**
     * Constructor for LimitReminderNotification
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
     * creates notification for "in grace period", if required
     * @param InstallationData $installationData
     *
     * @return void
     * @throws \CException
     */
    public function createNotification(InstallationData $installationData)
    {
        if ($this->hasUnpaidInvoicesDuringGracePeriod($installationData)) {
            $links = new LinksAndContactHmtlHelper();
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->getTitle(),
                'message' => $this->getMessage($links, $installationData->isSiteAdminUser) . $this->getButton(
                        $links,
                        $installationData->isSiteAdminUser
                    )
            ));
            $not->save();
        }
    }

    /**
     * Generates and returns the title of the notification
     * @return string
     */
    private function getTitle()
    {
        return $this->plugin->gT('Unpaid invoice');
    }

    /**
     * Generates and returns the html formatted message of the notification
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getMessage(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $message = sprintf(
                $this->plugin->gT(
                    'You have an unpaid invoice. To avoid being locked out of your survey site, please view and pay the invoice from the %s tab of your LimeSurvey account homepage'
                ),
                $links->toHtmlLink(
                    $links->getTransactionHistoryLink(\Yii::app()->session['adminlang']),
                    $this->plugin->gT('transaction history')
                )
            );
        } else {
            $message = sprintf(
                $this->plugin->gT(
                    'There is an unpaid invoice for this account. Please contact your survey site admin, %s, to have the invoice paid to avoid being locked out of your survey site.'
                ),
                $links->toHtmlMailLink($links->getSiteAdminEmail())
            );
        }
        return '<p>' . $message . '</p>';
    }

    /**
     * Generates and returns the button html
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getButton(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $button = $links->toHtmlLinkButton(
                $links->getTransactionHistoryLink(\Yii::app()->session['adminlang']),
                $this->plugin->gT('Pay invoice')
            );
        } else {
            $button = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Adminstrator')
            );
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }

    /**
     * @param InstallationData $installationData
     * @return bool
     */
    private function hasUnpaidInvoicesDuringGracePeriod(InstallationData $installationData)
    {
        $client = new ApiClient($installationData, new LimeSurveyProfessionalCurl(), $this->plugin->config);
        $data = $client->getHasUnpaidInvoicesDuringGracePeriod();

        return is_object($data) && property_exists($data, 'result') ? $data->result : false;
    }
}
