<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\DataTransferObject;
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
     * @param DataTransferObject $dto
     *
     * @return void
     */
    public function createNotification(DataTransferObject $dto)
    {
        if ($dto->hasResponseNotification || $dto->hasStorageNotification) {
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->getTitle($dto),
                'message' => $this->getMessage($dto)
            ));
            $not->save();
        }
    }

    /**
     * Generates and returns the title of the notification
     * @param DataTransferObject $dto
     *
     * @return string
     */
    private function getTitle(DataTransferObject $dto)
    {
        $title = '';
        if ($dto->hasResponseNotification && $dto->hasStorageNotification) {
            $title = $this->plugin->gT('You are almost out of storage & responses');
        } elseif ($dto->hasResponseNotification) {
            $title = $this->plugin->gT('You are almost out of responses');
        } elseif ($dto->hasStorageNotification) {
            $title = $this->plugin->gT('You are almost out of storage');
        }

        return $title;
    }

    /**
     * Returns the html formatted message of the notification
     * @param DataTransferObject $dto
     *
     * @return string
     */
    private function getMessage(DataTransferObject $dto)
    {
        $links = new LinksAndContactHmtlHelper();

        return $this->getMainString($dto) . $this->getContactString($dto) . $this->getButton($links, $dto->isSiteAdminUser);
    }

    /**
     * Generates and returns the main message string
     * @param DataTransferObject $dto
     *
     * @return string
     */
    public function getMainString(DataTransferObject $dto)
    {
        $mainString = '';
        if ($dto->hasResponseNotification && $dto->hasStorageNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The responses on your survey site are below the configured responses reminder limit of %s.'
                ),
                $dto->reminderLimitResponses
            );
            $mainString .= '<br>' . sprintf(
                    $this->plugin->gT(
                        'Also, the storage on your survey site is below the configured storage reminder limit of %s.'
                    ),
                    $dto->reminderLimitStorage . '%'
                );
        } elseif ($dto->hasResponseNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The responses on your survey site are below the configured responses reminder limit of %s.'
                ),
                $dto->reminderLimitResponses
            );
        } elseif ($dto->hasStorageNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The storage on your survey site is below the configured storage reminder limit of %s.'
                ),
                $dto->reminderLimitStorage . '%'
            );
        }

        return '<p>' . $mainString . '</p>';
    }

    /**
     * Generates and returns the contact part of the message
     * @param DataTransferObject $dto
     *
     * @return string
     */
    public function getContactString(DataTransferObject $dto)
    {
        $contactString = '';
        if ($dto->hasResponseNotification && $dto->hasStorageNotification) {
            if ($dto->isSiteAdminUser) {
                $contactString = $this->plugin->gT(
                    'Please upgrade or renew your plan to increase your storage & responses.'
                );
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your storage & responses.'
                );
            }
        } elseif ($dto->hasResponseNotification) {
            if ($dto->isSiteAdminUser) {
                $contactString = $this->plugin->gT(
                    'Please upgrade or renew your plan to increase your responses.'
                );
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your responses.'
                );
            }
        } elseif ($dto->hasStorageNotification) {
            if ($dto->isSiteAdminUser) {
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
