<?php

namespace ls\tests;

use LimeSurveyProfessional;
use LSYii_Application;
use LSYii_Controller;
use Yii;
use LimeSurvey\PluginManager\PluginManager;
use LimeSurvey\PluginManager\PluginEvent;
use Exception;

class LimeSurveyProfessionalTest extends TestBaseClass
{
    public static function setupBeforeClass(): void
    {
        $pluginDir = 'application.core.plugins.LimeSurveyProfessional.*';
        Yii::import($pluginDir);
        Yii::import('application.helpers.common_helper', true);
    }

    public function testForceRedirectToWelcomePageWithNoEvent()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $this->assertFalse($lsp->forceRedirectToWelcomePage(null));
    }

    public function testForceRedirectToWelcomePageEmptyEvent()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $event = new PluginEvent('eventname');
        $this->assertTrue($lsp->forceRedirectToWelcomePage($event));
    }

    public function testForceRedirectToWelcomePageFalseEvent()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $event = new PluginEvent('eventname');
        $event->set('subaction', 'logout');
        $this->assertFalse($lsp->forceRedirectToWelcomePage($event));
    }

    public function testTransactionHistoryLinkDe()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $historyLinkDe = $linksClass->getTransactionHistoryLink('de');
        $this->assertTrue(strpos($historyLinkDe, 'de/') !== false);
    }

    public function testTransactionHistoryLinkBr()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $historyLinkBr = $linksClass->getTransactionHistoryLink('pt-BR');
        $this->assertTrue(strpos($historyLinkBr, 'pt/') !== false);
    }

    public function testContactCorporateLink()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $corporateLink = $linksClass->getContactCorporateLink('de');
        $this->assertTrue(strpos($corporateLink, 'de/hilfe') !== false);
    }

    public function testPricingPageLink()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $pricingLink = $linksClass->getPricingPageLink('fr');
        $this->assertTrue(strpos($pricingLink, 'fr/') !== false);
    }

    public function testButtonLink()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $emailButton = $linksClass->toHtmlLinkButton('test@limesurvey.org', 'Test-Title');
        $this->assertTrue(
            strpos($emailButton, 'test@limesurvey.org') !== false
            && strpos($emailButton, 'btn-') !== false
            && strpos($emailButton, 'Test-Title') !== false
        );
    }

    public function testEmailButtonLink()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $emailButton = $linksClass->toHtmlMailLinkButton('test@limesurvey.org', 'Test-Title');
        $this->assertTrue(
            strpos($emailButton, 'test@limesurvey.org') !== false
            && strpos($emailButton, 'fa-envelope') !== false
            && strpos($emailButton, 'Test-Title') !== false
        );
    }

    /**
     * @return LimeSurveyProfessional\InstallationData
     */
    private function getInstallationData()
    {
        $installationData = new LimeSurveyProfessional\InstallationData();
        $installationData->isHardLocked = false;
        $installationData->plan = 'free';
        $installationData->isSiteAdminUser = true;
        $installationData->isPayingUser = false;
        $installationData->outOfResponses = false;
        $installationData->locked = false;
        $installationData->emailLock = 0;
        $installationData->dateSubscriptionCreated = '2020-10-29 00:00:00';
        $installationData->dateSubscriptionPaid = '2021-12-31 00:00:00';
        $installationData->paymentPeriod = 'M';
        $installationData->reminderLimitStorage = 10;
        $installationData->reminderLimitResponses = 10;
        $installationData->hasResponseNotification = false;
        $installationData->hasStorageNotification = false;
        $installationData->accessToken = 12345;
        $installationData->apiId = '';
        $installationData->apiSecret = '';

        return $installationData;
    }

    public function testHasNoBlockingNotification()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $installationData = $this->getInstallationData();
        $event = new PluginEvent('eventname');
        $event->set('subaction', 'logout');

        $this->assertFalse($lsp->createBlockingNotifications($event, $installationData));
    }

    public function testBlacklistFilterNoSpam()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $installationData = $this->getInstallationData();
        $event = new PluginEvent('eventname');
        $event->set('body', '');
        $event->set('subject', '');
        $event->set('replyto', ['test@limesurvey.org']);

        $blacklist = new LimeSurveyProfessional\email\BlacklistFilter($event);
        $emailMethod = 'mail';
        $folder = getcwd() . '/application/core/plugins/LimeSurveyProfessional';

        $this->assertFalse($blacklist->detectSpam($emailMethod, $folder, $installationData->emailLock));
    }

    public function testBlacklistFilterSpam()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $installationData = $this->getInstallationData();
        $event = new PluginEvent('eventname');
        $event->set('body', 'Tax return');
        $event->set('subject', '');
        $event->set('replyto', ['test@limesurvey.org']);

        // as of now a random number of 3 to 5 emails containing blacklisted words
        // need to be sent before filter hits. so we try it 6 times
        $numberOfEmails = 6;
        $locked = false;

        for ($i = 0; $i < $numberOfEmails; $i++) {
            if (!$locked) {
                $blacklist = new LimeSurveyProfessional\email\BlacklistFilter($event);
                $emailMethod = 'mail';
                $folder = getcwd() . '/application/core/plugins/LimeSurveyProfessional';
                $locked = $blacklist->detectSpam($emailMethod, $folder, $installationData->emailLock);
            }
        }

        $this->assertTrue($locked);
    }

    public function testNoUpgradeButtonForPaidUsers()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $installationData = $this->getInstallationData();
        $installationData->isPayingUser = true;
        $upgradeButtonClass = new LimeSurveyProfessional\upgradeButton\UpgradeButton();

        $this->assertFalse($upgradeButtonClass->displayUpgradeButton($lsp, $installationData));
    }

    public function testPromotionalBanner()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init(false);
        $installationData = $this->getInstallationData();
        $installationData->plan = 'free';
        $installationData->dateSubscriptionCreated = '2022-01-23 12:11:04';
        $fakeTodayDateDay5 = new \DateTime('2022-01-27 00:00:00'); //1st show day of banner 1
        $fakeTodayDateDay6 = new \DateTime('2022-03-04 00:00:00'); // 35 days after 1st show day of banner 2
        $testConfig = [
            'free' => [
                [
                    'id' => 1,
                    'cycleStart' => 5,
                    'maxShow' => 3,
                    'mainMessage' => 'TestMessage 1',
                    'messageSiteAdmin' => 'for site admin',
                    'messageAdmin' => 'for normal admin',
                    'test' => true
                ],
                [
                    'id' => 2,
                    'cycleStart' => 6,
                    'maxShow' => 3,
                    'mainMessage' => 'TestMessage 2',
                    'messageSiteAdmin' => 'for site admin',
                    'messageAdmin' => 'for normal admin',
                    'test' => true
                ],
            ]
        ];

        $promotionalBannersClass = new LimeSurveyProfessional\promotionalBanners\PromotionalBanners($lsp);
        $bannerDay5 = $promotionalBannersClass->getBannerFromConfig($fakeTodayDateDay5, $testConfig, $installationData);
        $this->assertTrue($bannerDay5->id == 1 && $bannerDay5->shows == 0);

        $bannerDay6 = $promotionalBannersClass->getBannerFromConfig($fakeTodayDateDay6, $testConfig, $installationData);
        $this->assertTrue($bannerDay6->id == 2 && $bannerDay6->shows == 0);
    }
}
