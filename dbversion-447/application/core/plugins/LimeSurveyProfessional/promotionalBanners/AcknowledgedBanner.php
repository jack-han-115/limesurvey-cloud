<?php

namespace LimeSurveyProfessional\promotionalBanners;

class AcknowledgedBanner
{
    /** @var int */
    public $id;

    /** @var int */
    public $shows = 0;

    /** @var string */
    public $ack;

    /**
     * Constructor for class AcknowledgedBanner.
     * This class represents the way a banner is saved in table settings_user when acknowledged
     *
     * @param array $bannerData
     * @param string $subscriptionCreated
     * @param string $plan
     */
    public function __construct(array $bannerData = [], string $subscriptionCreated = '', string $plan = '')
    {
        $this->id = array_key_exists('id', $bannerData) ? $bannerData['id'] : 0;
        $this->shows = array_key_exists('shows', $bannerData) ? $bannerData['shows'] : 0;
        $this->ack = array_key_exists('ack', $bannerData) ? $bannerData['ack'] : '';
        $testingMode = array_key_exists('test', $bannerData);
        if ($this->shows == 0 && !$testingMode) {
            $this->enhanceDataFromDb($subscriptionCreated, $plan);
        }
    }

    /**
     * completes the Banner data if there is already data in the db
     *
     * @param string $subscriptionCreated
     * @param string $plan
     */
    private function enhanceDataFromDb(string $subscriptionCreated, string $plan)
    {
        $bannerAcknowledgedObj = new BannersAcknowledgedObject($subscriptionCreated, $plan);
        $bannerAcknowledgedObj->load();
        foreach ($bannerAcknowledgedObj->banners as $ackBanner) {
            /** @var AcknowledgedBanner $ackBanner */
            // if this banner was acknowledged before, set its ack and shows values
            if ($ackBanner->id == $this->id) {
                $this->ack = $ackBanner->ack;
                $this->shows = $ackBanner->shows;
            }
        }
    }
}
