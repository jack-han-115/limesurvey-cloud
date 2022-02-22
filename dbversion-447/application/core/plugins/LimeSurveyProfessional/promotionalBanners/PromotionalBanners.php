<?php

namespace LimeSurveyProfessional\promotionalBanners;

use LimeSurveyProfessional\DataTransferObject;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class PromotionalBanners
{
    /**
     * Number of days when cycle is repeated
     * Per plan there is a maximum of 5 different banners possible with a cycle of 5 days,
     * because there is only one banner allowed to be shown a day
     */
    const CYCLE_DAYS = 5;

    /** @var \LimeSurveyProfessional */
    public $plugin;

    /**
     * Constructor for class PromotionalBanners.
     * Only one promotionalBanner can be shown a day. If it is clicked away by the user it will be not shown again in that day.
     * Each banner has its own value of how often it will be shown before it will never be shown again.
     *
     * See getBannerConfig() to configure banners and learn more about the individual settings
     *
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(\LimeSurveyProfessional $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * If there is a banner to be shown today, it will be shown
     * @param \DateTime $today For testing purposes this is a parameter
     * @param DataTransferObject $dto
     *
     * @throws \CException
     */
    public function showPromotionalBanner(\DateTime $today, DataTransferObject $dto)
    {
        $bannerConfig = new PromotionalBannerConfig();
        $banner = $this->getBannerFromConfig($today, $bannerConfig->getBannerConfig($this->plugin), $dto);
        if ($banner) {
            $banner->show();
        }
    }

    /**
     * Returns a Banner if there is one to be shown today
     * Conditions for it to be shown are:
     * - a banner for the plan of user exists in config
     * - today is the day of the cycle on which the banner is supposed to be shown
     * - the maximium shows of the banner according to the config was not reached yet
     * - it was not acknowledged today already
     *
     * @param \DateTime $today For testing purposes this is a parameter
     * @param array $allBannersConfig For testing purposes this is a parameter
     * @param DataTransferObject $dto
     *
     * @return Banner|null
     */
    public function getBannerFromConfig(\DateTime $today, array $allBannersConfig, DataTransferObject $dto)
    {
        $banner = null;
//        1. check if there's banners for users plan
        if (array_key_exists($dto->plan, $allBannersConfig)) {
            foreach ($allBannersConfig[$dto->plan] as $bannerConfig) {
                $subscriptionCreated = new \DateTime($dto->dateSubscriptionCreated);
                $subscriptionCreated->setTime(0, 0, 0);
                $firstShowDay = clone $subscriptionCreated;
                $firstShowDay = $firstShowDay->add(new \DateInterval('P' . ($bannerConfig['cycleStart'] - 1) . 'D'));
                $interval = $firstShowDay->diff($today);
                $daysSinceFirstShow = (int)$interval->format('%r%a');
//              2. check if today would be the day to show one particular banner
                if ($daysSinceFirstShow == 0 || ($daysSinceFirstShow > 0 && $daysSinceFirstShow % self::CYCLE_DAYS === 0)) {
                    $banner = new Banner($dto, $bannerConfig);
//                   3. this banner could be shown today - so check if it was already acknowledged today or has its maximum shows reached
                    if ($banner->shows >= $banner->maxShow || $banner->ackForToday) {
                        $banner = null;
                    }
                }
            }
        }

        return $banner;
    }

    /**
     * Function that is called when a promotional banner is acknowledged (clicked away) by the user.
     * It triggers a update of the promotionalBanners setting in the table settings_user
     * @param \LSHttpRequest $request
     * @param DataTransferObject $dto
     */
    public function updateBannersAcknowledgedObject(\LSHttpRequest $request, DataTransferObject $dto)
    {
        if ($request->getIsPostRequest()) {
            $id = (int)$request->getPost('bid', 0);
            if ($id > 0) {
                $bannersAckObj = new BannersAcknowledgedObject(
                    $dto->dateSubscriptionCreated,
                    $dto->plan
                );
                $bannersAckObj->update($id);
            }
        }
    }
}
