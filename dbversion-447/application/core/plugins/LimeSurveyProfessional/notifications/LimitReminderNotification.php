<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\InstallationData;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class LimitReminderNotification
{
    /** @var \LimeSurveyProfessional */
    private $plugin;

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
     * creates the limit notification for storage and responses, if required
     * @param InstallationData $installationData
     *
     * @return void
     */
    public function createNotification(InstallationData $installationData)
    {
        if ($installationData->hasResponseNotification || $installationData->hasStorageNotification) {
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->getTitle($installationData),
                'message' => $this->getMessage($installationData)
            ));
            $not->save();
        }
    }

    /**
     * Generates and returns the title of the notification
     * @param InstallationData $installationData
     *
     * @return string
     */
    private function getTitle(InstallationData $installationData)
    {
        $title = '';
        if ($installationData->hasResponseNotification && $installationData->hasStorageNotification) {
            $title = $this->plugin->gT('You are almost out of storage & responses');
        } elseif ($installationData->hasResponseNotification) {
            $title = $this->plugin->gT('You are almost out of responses');
        } elseif ($installationData->hasStorageNotification) {
            $title = $this->plugin->gT('You are almost out of storage');
        }

        return $title;
    }

    /**
     * Returns the html formatted message of the notification
     * @param InstallationData $installationData
     *
     * @return string
     */
    private function getMessage(InstallationData $installationData)
    {
        $links = new LinksAndContactHmtlHelper();

        return $this->getMainString($installationData) . $this->getContactString($installationData) . $this->getButton($links, $installationData->isSiteAdminUser);
    }

    /**
     * Generates and returns the main message string
     * @param InstallationData $installationData
     *
     * @return string
     */
    public function getMainString(InstallationData $installationData)
    {
        $mainString = '';
        if ($installationData->hasResponseNotification && $installationData->hasStorageNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The responses on your survey site are below the configured responses reminder limit of %s.'
                ),
                $installationData->reminderLimitResponses
            );
            $mainString .= '<br>' . sprintf(
                    $this->plugin->gT(
                        'Also, the storage on your survey site is below the configured storage reminder limit of %s.'
                    ),
                    $installationData->reminderLimitStorage . '%'
                );
        } elseif ($installationData->hasResponseNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The responses on your survey site are below the configured responses reminder limit of %s.'
                ),
                $installationData->reminderLimitResponses
            );
        } elseif ($installationData->hasStorageNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The storage on your survey site is below the configured storage reminder limit of %s.'
                ),
                $installationData->reminderLimitStorage . '%'
            );
        }

        return '<p>' . $mainString . '</p>';
    }

    /**
     * Generates and returns the contact part of the message
     * @param InstallationData $installationData
     *
     * @return string
     */
    public function getContactString(InstallationData $installationData)
    {
        $contactString = '';
        if ($installationData->hasResponseNotification && $installationData->hasStorageNotification) {
            if ($installationData->isSiteAdminUser) {
                $contactString = $this->plugin->gT(
                    'Please upgrade or renew your plan to increase your storage & responses.'
                );
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your storage & responses.'
                );
            }
        } elseif ($installationData->hasResponseNotification) {
            if ($installationData->isSiteAdminUser) {
                $contactString = $this->plugin->gT(
                    'Please upgrade or renew your plan to increase your responses.'
                );
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your responses.'
                );
            }
        } elseif ($installationData->hasStorageNotification) {
            if ($installationData->isSiteAdminUser) {
                $contactString = $this->plugin->gT('Please upgrade or renew your plan to increase your storage.');
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your storage.'
                );
            }
        }
        return '<p>' . $contactString . '</p>';
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
                $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                $this->plugin->gT('Upgrade or renew plan')
            );
        } else {
            $button = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Adminstrator')
            );
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }


}
