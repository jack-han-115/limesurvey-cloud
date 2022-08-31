<?php

namespace LimeSurveyProfessional\ParticipantRegisterCta;

use DateTime;
use LimeSurveyProfessional;
use LimeSurveyProfessional\InstallationData;

class ParticipantRegisterCta
{
    private const INTRO_DATE = '2022-08-30';
    private const PLAN_BASIC = 'basic';
    private const PLAN_FREE = 'free';

    /**
     * @var ParticipantRegisterCta
     */
    private static $instance = null;

    private $plugin = null;
    private $installationData = null;
    private $viewContentFooter = '';
    private $viewContentCompleted = '';

    public function __construct(LimeSurveyProfessional $plugin, InstallationData $installationData)
    {
        $this->plugin = $plugin;
        $this->installationData = $installationData;
        $this->prepareViewContent();
    }

    protected function prepareViewContent()
    {
        $event = $this->plugin->getEvent();
        $surveyId = $event->get('surveyId');

        if (!empty($surveyId)) {
            $this->viewContentFooter =
                $this->plugin->renderPartial(
                    'participantRegisterCta.footer',
                    array(),
                    true,
                    true
                );
            $this->viewContentCompleted =
                $this->plugin->renderPartial(
                    'participantRegisterCta.complete',
                    array(),
                    true,
                    true
                );
        }
    }

    /**
     * Get a single instance to be shared between plugin events
     * 
     * This plugin needs to handle plugin events beforeCloseHtml and afterSurveyComplete.
     * The beforeCloseHtml should be triggered only if afterSurveyComplete as not triggered.
     * So we need to share state between plugin events. This method gives you back the 
     * same instance regardless of what event handler you are in.
     *
     * @return ParticipantRegisterCta
     */
    public static function getInstance(LimeSurveyProfessional $plugin, InstallationData $installationData)
    {
        if (static::$instance === null) {
            static::$instance = new static($plugin, $installationData);
        }
        return static::$instance;
    }

    /**
     * Main function to display the registration call to action
     * 
     * @param bool $isComplete
     * @return bool
     * @throws \CException
     */
    public function display($isComplete = false)
    {
        $shouldDisplay = $this->shouldDisplay();
        if ($shouldDisplay) {
            $event = $this->plugin->getEvent();
            if ($isComplete) {
                $event->setContent($this->plugin, $this->viewContentCompleted);
            } else {
                $event->set('html', $this->viewContentFooter);
            }
        }
        return $shouldDisplay;
    }

    /**
     * Determine if the branding should be displayed
     *
     * @return bool
     * @throws \CException
     */
    protected function shouldDisplay()
    {
        $event = $this->plugin->getEvent();
        $surveyId = $event->get('surveyId');

        $disable =
            empty($surveyId)
            || $this->isEnablePermitted() === false
            // Todo: Add ability to enable or disable branding for isDisablePermitted() installations
            // - then compare here to decide if the branding should be disabled or not
            || $this->isDisablePermitted();

        return $disable === false;
    }

    /**
     * Determine branding is permitted to be enabled
     * 
     * We don't display branding for installations who created before we implemented
     * the branding feature.
     *
     * @return bool
     * @throws \CException
     */
    protected function isEnablePermitted()
    {
        $brandingFeatureIntroduced = new DateTime(self::INTRO_DATE);
        return $this->installationData->dateCreated < $brandingFeatureIntroduced;
    }

    /**
     * Determine branding is permitted to be disabled
     * 
     * Only paying customers on plan 'expert' or 'enterprise' can disable branding.
     *
     * @return bool
     * @throws \CException
     */
    protected function isDisablePermitted()
    {
        $plansNotPermitted = [
            self::PLAN_BASIC,
            self::PLAN_FREE
        ];
        return ($this->installationData->isPayingUser === true
            && false == in_array($this->installationData->plan, $plansNotPermitted)
        );
    }
}
