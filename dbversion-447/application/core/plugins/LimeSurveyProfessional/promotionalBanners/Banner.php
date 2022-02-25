<?php

namespace LimeSurveyProfessional\promotionalBanners;

use LimeSurveyProfessional\InstallationData;

class Banner extends AcknowledgedBanner
{
    /** @var boolean */
    public $ackForToday = false;

    /** @var int */
    public $cycleStart;

    /** @var int */
    public $maxShow;

    /** @var string */
    public $mainMessage;

    /** @var string */
    public $messageSiteAdmin;

    /** @var string */
    public $messageAdmin;

    /** @var boolean */
    public $isSiteAdmin;

    /**
     * Constructor for class Banner.
     * Banner properties consist of banner data from the bannerConfig of class PromotionalBanners
     * and will be enhanced with data from the db if this banner was acknowledged before.
     *
     * @param InstallationData $installationData
     * @param array $bannerData
     */
    public function __construct(InstallationData $installationData, array $bannerData)
    {
        parent::__construct($bannerData, $installationData->dateSubscriptionCreated, $installationData->plan);
        $this->cycleStart = $bannerData['cycleStart'];
        $this->maxShow = $bannerData['maxShow'];
        $this->mainMessage = $bannerData['mainMessage'];
        $this->messageSiteAdmin = $bannerData['messageSiteAdmin'];
        $this->messageAdmin = $bannerData['messageAdmin'];
        $this->isSiteAdmin = $installationData->isSiteAdminUser;
        $this->setAckForToday();
    }

    /**
     * if ack is set to a date and it is today,
     * setAckForToday will be set to true.
     * It means that this banner was already acknowledged today by the user.
     */
    private function setAckForToday()
    {
        if ($this->ack != '') {
            $acknowledgeDate = new \DateTime($this->ack);
            $acknowledgeDate->setTime(0, 0, 0);
            $today = new \DateTime('midnight');
            if ($today == $acknowledgeDate) {
                $this->ackForToday = true;
            }
        }
    }

    /**
     * Loads custom css, renders the view and makes it available for custom js.
     *
     * @throws \CException
     */
    public function show()
    {
        $cssUrl = \Yii::app()->assetManager->publish(dirname(__FILE__) . '/../css/promotionalBanners');
        \Yii::app()->clientScript->registerCssFile($cssUrl . '/promotionalBanners.css');

        $data = [
            'bannerId' => $this->id,
            'message' => $this->getMessageForBanner()
        ];

        // Generate banner html
        $banner = json_encode(
            \Yii::app()->controller->renderPartial(
                'LimeSurveyProfessional.views.promotionalBanners.promotionalBanner',
                $data,
                true
            ),
            JSON_HEX_APOS
        );

        // add banner js
        $assetsUrl = \Yii::app()->assetManager->publish(dirname(__FILE__) . '/../js/promotionalBanners');

        // Make banner html accessible for js
        App()->clientScript->registerScript(
            'promotionalBannerHtml',
            <<<EOT
                function getBannerHtml() {
                    
                    return $banner;
                }
EOT
            ,
            \CClientScript::POS_BEGIN
        );
        App()->clientScript->registerScriptFile(
            $assetsUrl . '/promotionalBanner.js',
            \CClientScript::POS_END
        );
    }

    /**
     * Returns the full message considering the role of current user.
     * @return string
     */
    private function getMessageForBanner()
    {
        $message = $this->mainMessage;
        $linkMessage = $this->isSiteAdmin ? $this->messageSiteAdmin : $this->messageAdmin;
        return $message . ' ' . $linkMessage;
    }

}

