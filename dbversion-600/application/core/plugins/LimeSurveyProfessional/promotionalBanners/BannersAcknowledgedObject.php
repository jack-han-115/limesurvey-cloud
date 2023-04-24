<?php

namespace LimeSurveyProfessional\promotionalBanners;

class BannersAcknowledgedObject
{
    /** @var string */
    public $subscriptionCreated;

    /** @var string */
    public $plan;

    /** @var AcknowledgedBanner[]  banners which were acknowledged by the user previously each containing keys: id, shows and ack */
    public $banners;

    /**
     * Constructor of class BannersAcknowledgedObject which represents the json object which is stored in settings_user.stg_value
     * of record with stg_name "promotionalBanners"
     *
     * @param string $subscriptionCreated
     * @param string $plan
     */
    public function __construct(string $subscriptionCreated, string $plan)
    {
        $this->subscriptionCreated = $subscriptionCreated;
        $this->plan = $plan;
        $this->banners = [];
    }

    /**
     * if there is a record in table settings_user for this user and the subscription didn't change, it will be loaded into this class
     */
    public function load()
    {
        $settingsUser = \SettingsUser::getUserSetting('promotionalBanners');
        if ($settingsUser) {
            $decodedStgValue = $this->decodeSetting($settingsUser->stg_value);
            if (!$this->subscriptionChanged($decodedStgValue['plan'], $decodedStgValue['subscriptionCreated'])) {
                $this->setBanners($decodedStgValue['banners']);
            }
        }
    }

    /**
     * Updates $this->banners considering the banner of $id was acknowledged by the user
     * and saves $this->banners as json object into settings_user table
     *
     * @param int $id
     */
    public function update(int $id)
    {
        $settingsUser = \SettingsUser::getUserSetting('promotionalBanners');
        if ($settingsUser) {
            $decodedStgValue = $this->decodeSetting($settingsUser->stg_value);
            // 1. If plan or subscription_created has changed everything in $this->banners needs to be reset
            if ($this->subscriptionChanged($decodedStgValue['plan'], $decodedStgValue['subscriptionCreated'])) {
                $this->resetBanners($id);
            } else {
                $this->setBanners($decodedStgValue['banners']);
                // 2. check if current banner id is in banners array
                // 2a: if so - update shows and ack
                $bannerWasAckBefore = $this->acknowledgeExistingBanner($id);

                if (!$bannerWasAckBefore) {
                    //2b: if not - add this banner to array $this->banners
                    $this->acknowledgeNewBanner($id);
                }
            }
        } else {
            //  first save of this setting because no banner was ever acknowledged
            $this->resetBanners($id);
        }
        $stgValue = $this->encodeSetting();
        \SettingsUser::setUserSetting('promotionalBanners', $stgValue);
    }

    /**
     * $this->banners will be emptied and only one banner of the given $id will be added.
     * this can happen when the subscription changed, or when an acknowledged banner is the first to be saved ever
     * @param int $id
     */
    private function resetBanners(int $id)
    {
        $this->banners = [];
        $this->acknowledgeNewBanner($id);
    }

    /**
     * A banner of given $id is added to $this->banners
     * As it was acknowledged for the first time, shows will be set to 1
     * and ack to today
     * @param int $id
     */
    private function acknowledgeNewBanner(int $id)
    {
        $today = new \DateTime();
        $this->banners[] = new AcknowledgedBanner(
            [
                'id' => $id,
                'shows' => 1,
                'ack' => $today->format('Y-m-d')
            ]
        );
    }

    /**
     * Goes through all banners acknowledged by this user, if the banner with this $id was acknowledged before
     * the shows value will be increased by 1 and ack will be set to today
     * @param int $id
     * @return bool
     */
    private function acknowledgeExistingBanner(int $id)
    {
        $bannerWasAckBefore = false;
        foreach ($this->banners as $index => $savedBanner) {
            /** @var AcknowledgedBanner $savedBanner */
            if ($savedBanner->id === $id) {
                $today = new \DateTime();
                $this->banners[$index]->ack = $today->format('Y-m-d');
                $this->banners[$index]->shows++;
                $bannerWasAckBefore = true;
            }
        }
        return $bannerWasAckBefore;
    }

    /**
     * Decodes json string to associative array
     * @param $json
     * @return mixed
     */
    private function decodeSetting($json)
    {
        return json_decode_ls($json);
    }

    /**
     * Encodes this class to json object
     * @return array
     */
    private function encodeSetting()
    {
        return ls_json_encode($this);
    }

    /**
     * compares given $plan and $subscriptionCreated to
     * class properties of same name which represent the current state of users subscription.
     * If there is a change in one of those it returns true
     *
     * @param $plan
     * @param $subscriptionCreated
     * @return bool
     */
    private function subscriptionChanged($plan, $subscriptionCreated)
    {
        return $this->plan != $plan || $this->subscriptionCreated != $subscriptionCreated;
    }

    /**
     * Takes an associative array of $banners and sets them into $this->banners as AcknowledgedBanners
     * @param array $banners
     */
    private function setBanners(array $banners)
    {
        foreach ($banners as $banner) {
            $this->banners[] = new AcknowledgedBanner(
                ['id' => $banner['id'], 'shows' => $banner['shows'], 'ack' => $banner['ack']]
            );
        }
    }
}
