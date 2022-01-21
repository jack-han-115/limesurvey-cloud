<?php

namespace ls\tests;

use LimeSurveyProfessional;
use Yii;
use LimeSurvey\PluginManager\PluginManager;
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
        $this->expectException(Exception::class);
        $lsp->forceRedirectToWelcomePage();
    }
}
