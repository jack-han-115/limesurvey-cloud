<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class LimitReminderNotification
{
    /** @var boolean */
    private $hasResponseNotification;

    /** @var boolean */
    private $hasStorageNotification;

    /** @var int */
    private $reminderLimitStorage;

    /** @var int */
    private $reminderLimitResponses;

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

        $this->reminderLimitStorage = $this->plugin->limeserviceSystem->getReminderLimitStorage();
        $this->reminderLimitResponses = $this->plugin->limeserviceSystem->getReminderLimitResponses();

        $this->hasResponseNotification = $this->plugin->limeserviceSystem->showResponseNotificationForUser();
        // If no storage is left, this notification will not be shown!
        $this->hasStorageNotification = $this->plugin->limeserviceSystem->calcRestStoragePercent() > 0
            && $this->plugin->limeserviceSystem->calcRestStoragePercent() < $this->reminderLimitStorage;
    }

    /**
     * creates the limit notification for storage and responses, if required
     *
     * @return void
     */
    public function createNotification()
    {
        if ($this->hasResponseNotification || $this->hasStorageNotification) {
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->getTitle(),
                'message' => $this->getMessage()
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
        $title = '';
        if ($this->hasResponseNotification && $this->hasStorageNotification) {
            $title = $this->plugin->gT('You are almost out of storage & responses');
        } elseif ($this->hasResponseNotification) {
            $title = $this->plugin->gT('You are almost out of responses');
        } elseif ($this->hasStorageNotification) {
            $title = $this->plugin->gT('You are almost out of storage');
        }

        return $title;
    }

    /**
     * Returns the html formatted message of the notification
     * @return string
     */
    private function getMessage()
    {
        $links = new LinksAndContactHmtlHelper();

        return $this->getMainString() . $this->getContactString() . $this->getButton($links);
    }

    /**
     * Generates and returns the main message string
     *
     * @return string
     */
    public function getMainString()
    {
        $mainString = '';
        if ($this->hasResponseNotification && $this->hasStorageNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The responses on your survey site are below the configured responses reminder limit of %s.'
                ),
                $this->reminderLimitResponses
            );
            $mainString .= '<br>' . sprintf(
                    $this->plugin->gT(
                        'Also, the storage on your survey site is below the configured storage reminder limit of %s.'
                    ),
                    $this->reminderLimitStorage . '%'
                );
        } elseif ($this->hasResponseNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The responses on your survey site are below the configured responses reminder limit of %s.'
                ),
                $this->reminderLimitResponses
            );
        } elseif ($this->hasStorageNotification) {
            $mainString = sprintf(
                $this->plugin->gT(
                    'The storage on your survey site is below the configured storage reminder limit of %s.'
                ),
                $this->reminderLimitStorage . '%'
            );
        }

        return '<p>' . $mainString . '</p>';
    }

    /**
     * Generates and returns the contact part of the message
     *
     * @return string
     */
    public function getContactString()
    {
        $contactString = '';
        if ($this->hasResponseNotification && $this->hasStorageNotification) {
            if ($this->plugin->isSiteAdminUser) {
                $contactString = $this->plugin->gT(
                    'Please upgrade or renew your plan to increase your storage & responses.'
                );
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your storage & responses.'
                );
            }
        } elseif ($this->hasResponseNotification) {
            if ($this->plugin->isSiteAdminUser) {
                $contactString = $this->plugin->gT(
                    'Please upgrade or renew your plan to increase your responses.'
                );
            } else {//all other users
                $contactString = $this->plugin->gT(
                    'Please contact your Survey Site Administrator to upgrade or renew your plan to increase your responses.'
                );
            }
        } elseif ($this->hasStorageNotification) {
            if ($this->plugin->isSiteAdminUser) {
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
     * @return string
     */
    private function getButton(LinksAndContactHmtlHelper $links)
    {
        if ($this->plugin->isSiteAdminUser) {
            $button = $links->toHtmlLinkButton(
                \Yii::app()->getConfig(
                    "linkToPricingPage"
                ),
                $this->plugin->gT('Upgrade or renew plan')
            );
        } else {
            $button = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Admin')
            );
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }


}
