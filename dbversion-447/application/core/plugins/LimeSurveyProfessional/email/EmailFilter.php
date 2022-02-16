<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;
use LimeSurveyProfessional\DataTransferObject;

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
     * @param DataTransferObject $dto
     */
    public function filter(DataTransferObject $dto)
    {
        $blacklistFilter = new BlacklistFilter($this->event);
        $blacklistFilter->filterBlacklist($dto->emailLock);
    }
}
