<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;

class EmailFilter
{
    /** @var PluginEvent */
    public $event;

    /** @var \LimeSurvey\PluginManager\PluginBase */
    public $plugin;

    /** @var int */
    public $emailLock;

    /**
     * Constructor for BlacklistFilter
     *
     *
     * @param PluginEvent $event
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(PluginEvent $event, \LimeSurveyProfessional $plugin)
    {
        $this->event = $event;
        $this->plugin = $plugin;
        $this->emailLock = $this->plugin->emailLock;
    }

    /**
     * @TODO for other email checkups before sending
     * Blacklist filter will be initialized here
     */
    public function filter()
    {
        $blacklistFilter = new BlacklistFilter($this->event, $this->plugin);
        $blacklistFilter->filterBlacklist();
    }
}
