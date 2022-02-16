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
    }

    public function testForceRedirectToWelcomePageWithNoEvent()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
        $this->assertFalse($lsp->forceRedirectToWelcomePage(null));
    }

    public function testForceRedirectToWelcomePageEmptyEvent()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
        $event = new PluginEvent('eventname');
        $this->assertTrue($lsp->forceRedirectToWelcomePage($event));
    }

    public function testForceRedirectToWelcomePageFalseEvent()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
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

    public function testEmailButtonLink()
    {
        $linksClass = new LimeSurveyProfessional\LinksAndContactHmtlHelper();
        $emailButton = $linksClass->toHtmlLinkButton('test@limesurvey.org', 'Test-Title');
        $this->assertTrue(
            strpos($emailButton, 'test@limesurvey.org') !== false
            && strpos($emailButton, 'btn-') !== false
            && strpos($emailButton, 'Test-Title') !== false
        );
    }

    /**
     * @return LimeSurveyProfessional\DataTransferObject
     */
    private function getBasicDto()
    {
        $dto = new LimeSurveyProfessional\DataTransferObject();
        $dto->isHardLocked = false;
        $dto->plan = 'free';
        $dto->isSiteAdminUser = true;
        $dto->isPayingUser = false;
        $dto->outOfResponses = false;
        $dto->locked = false;
        $dto->emailLock = 0;
        $dto->dateSubscriptionCreated = '2020-10-29 00:00:00';
        $dto->dateSubscriptionPaid = '2021-12-31 00:00:00';
        $dto->paymentPeriod = 'M';
        $dto->reminderLimitStorage = 10;
        $dto->reminderLimitResponses = 10;
        $dto->hasResponseNotification = false;
        $dto->hasStorageNotification = false;

        return $dto;
    }

    public function testHasNoBlockingNotification()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
        $dto = $this->getBasicDto();
        $event = new PluginEvent('eventname');
        $event->set('subaction', 'logout');

        $this->assertFalse($lsp->createBlockingNotifications($event, $dto));
    }

    public function testBlacklistFilterNoSpam()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
        $dto = $this->getBasicDto();
        $event = new PluginEvent('eventname');
        $event->set('body', '');
        $event->set('subject', '');
        $event->set('replyto', ['test@limesurvey.org']);

        $blacklist = new LimeSurveyProfessional\email\BlacklistFilter($event);
        $emailMethod = 'mail';
        $folder = getcwd() . '/application/core/plugins/LimeSurveyProfessional';

        $this->assertFalse($blacklist->detectSpam($emailMethod, $folder, $dto->emailLock));
    }

    public function testBlacklistFilterSpam()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
        $dto = $this->getBasicDto();
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
                $locked = $blacklist->detectSpam($emailMethod, $folder, $dto->emailLock);
            }
        }

        $this->assertTrue($locked);
    }

    public function testIsInGracePeriod()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $lsp->init();
        $dto = $this->getBasicDto();
        $fakeTodayDate = new \DateTime('2022-01-28 00:00:00');
        $gracePeriodClass = new LimeSurveyProfessional\notifications\GracePeriodNotification($lsp);

        $this->assertTrue($gracePeriodClass->isInGracePeriod($fakeTodayDate, $dto));
    }

    public function testNoUpgradeButtonForPaidUsers()
    {
        $pm = new PluginManager();
        $id = 'dummyid';
        $lsp = new LimeSurveyProfessional($pm, $id);
        $dto = $this->getBasicDto();
        $dto->isPayingUser = true;
        $upgradeButtonClass = new LimeSurveyProfessional\upgradeButton\UpgradeButton();

        $this->assertFalse($upgradeButtonClass->displayUpgradeButton($lsp, $dto));
    }
}
