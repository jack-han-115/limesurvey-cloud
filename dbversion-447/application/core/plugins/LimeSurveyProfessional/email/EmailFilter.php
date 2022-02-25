<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;
use LimeSurveyProfessional\InstallationData;

class EmailFilter
{
    /** @var PluginEvent */
    public $event;

    /**
     * Constructor for BlacklistFilter
     *
     *
     * @param PluginEvent $event
     */
    public function __construct(PluginEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @TODO for other email checkups before sending
     * Blacklist filter will be initialized here
     * @param InstallationData $installationData
     */
    public function filter(InstallationData $installationData)
    {
        $blacklistFilter = new BlacklistFilter($this->event);
        $blacklistFilter->filterBlacklist($installationData->emailLock);
    }
}
