<?php

namespace LimeSurveyProfessional\ParticipantRegisterCta;

use LimeSurveyProfessional;
use LimeSurveyProfessional\InstallationData;

class ParticipantRegisterCta
{
    /**
     * @var ParticipantRegisterCta
     */
    private static $instance = null;

    private $plugin = null;
    private $installationData = null;
    private $viewContentFooter = 'nope';
    private $viewContentCompleted = 'nope nope';

    public function __construct(LimeSurveyProfessional $plugin, InstallationData $installationData)
    {
        $this->plugin = $plugin;
        $this->installationData = $installationData;

        $this->prepareViewContent();
    }

    protected function prepareViewContent()
    {
        $this->viewContentFooter =
            $this->plugin->renderPartial(
                'participantRegisterCta.footer',
                array(),
                true,
                false
            );
        $this->viewContentCompleted =
            $this->plugin->renderPartial(
                'participantRegisterCta.complete',
                array(),
                true,
                false
            );
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
     * @param \LimeSurveyProfessional $plugin
     * @param InstallationData $installationData
     * @return bool
     * @throws \CException
     */
    public function display($isComplete = false)
    {
        $event = $this->plugin->getEvent();
        $surveyId = $event->get('surveyId');
        $display = $surveyId && !$this->installationData->isPayingUser;
        $plan = $this->installationData->plan;

        if ($display) {
            if ($isComplete) {
                $event->setContent($this->plugin, $this->viewContentCompleted);
            } else {
                $event->set('html', $this->viewContentFooter);
            }
        }
        return $display;
    }
}
