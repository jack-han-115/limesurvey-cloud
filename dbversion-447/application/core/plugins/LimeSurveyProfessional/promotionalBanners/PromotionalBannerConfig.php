<?php

namespace LimeSurveyProfessional\promotionalBanners;

use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class PromotionalBannerConfig
{
    /**
     * Returns array
     * grouped by subscription alias / plan
     * 'id': every banner needs a unique id,
     * 'cycleStart': number of days after subscription was created when it is supposed to be shown the first time,
     *               this should differ within banners of one plan (only one banner a day)
     * 'maxShow': number of times it will be shown overall per user (when user clicks away)
     * 'messageSiteAdmin': text which is displayed for site admins,
     * 'messageAdmin': text which is displayed for other admins
     * @param \LimeSurveyProfessional $plugin
     * @return array[][]
     */
    public function getBannerConfig(\LimeSurveyProfessional $plugin)
    {
        $links = new LinksAndContactHmtlHelper();
        $contactSiteAdminLink = sprintf(
            $plugin->gT('Contact your site admin %s to upgrade.'),
            $links->toHtmlMailLink($links->getSiteAdminEmail())
        );
        return [
            'free' => [
                [
                    'id' => 1,
                    'cycleStart' => 5,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Unlock more powerful features.'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to our paid basic plan.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
                [
                    'id' => 2,
                    'cycleStart' => 6,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Need email support?'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to email with our experts.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
            ],
            'basic' => [
                [
                    'id' => 3,
                    'cycleStart' => 5,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Need faster email support?'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to get faster support.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
                [
                    'id' => 4,
                    'cycleStart' => 6,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT(
                        'Change your survey site url to surveyname.survey-research.net.'
                    ),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to our Expert plan.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
                [
                    'id' => 5,
                    'cycleStart' => 7,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Change your survey site url to your own domain.'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to our Enterprise plan.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
            ],
            'expert' => [
                [
                    'id' => 6,
                    'cycleStart' => 5,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Need more then 1 alias domain?'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to our Enterprise plan.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
                [
                    'id' => 7,
                    'cycleStart' => 6,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Need priority email support?'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Upgrade to get priority support.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
            ],
            'enterprise' => [
                [
                    'id' => 8,
                    'cycleStart' => 5,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Need a more tailored surveys solution?'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getContactCorporateLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Please contact our sales team for a tailored solution.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
                [
                    'id' => 9,
                    'cycleStart' => 6,
                    'maxShow' => 3,
                    'mainMessage' => $plugin->gT('Need custom tailored email support?'),
                    'messageSiteAdmin' => $links->toHtmlLink(
                        $links->getContactCorporateLink(\Yii::app()->session['adminlang']),
                        $plugin->gT('Please contact our sales team for a tailored solution.')
                    ),
                    'messageAdmin' => $contactSiteAdminLink
                ],
            ]
        ];
    }
}
