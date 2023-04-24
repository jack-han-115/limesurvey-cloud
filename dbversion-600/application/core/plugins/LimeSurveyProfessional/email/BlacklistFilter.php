<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;

class BlacklistFilter extends EmailFilter
{

    /**
     * Constructor for BlacklistFilter
     *
     * @param PluginEvent $event
     */
    public function __construct(PluginEvent $event)
    {
        parent::__construct($event);
    }

    /**
     * Just calls the detectSpam function.
     * This function exists, so we can unit-test the detectSpam function.
     */
    public function filterBlacklist()
    {
        $emailMethod = \Yii::app()->getConfig('emailmethod');
        $this->detectSpam($emailMethod);
    }

    /**
     * Checks if email body, subject or replyto contain blacklisted words/sentences.
     * If so, violationCounter will be raised, and search will be ended.
     *
     * @param string $emailMethod emailMethod value from config (mail / smtp)
     */
    public function detectSpam(string $emailMethod)
    {
        if ($emailMethod != 'smtp') {
            $emailBody = $this->event->get('body', '');
            $emailSubject = $this->event->get('subject', '');
            $emailReplyTo = $this->implodeMultiDimArray($this->event->get('replyto', []));

            foreach ($this->blacklistConfig['blacklistEntries'] as $entry) {
                if (
                    stripos($emailBody, $entry) !== false || stripos($emailSubject, $entry) !== false || stripos(
                        $emailReplyTo,
                        $entry
                    ) !== false
                ) {
                    $this->raiseViolationCount($entry);
                    break;
                }
            }
        }
    }

    /**
     * Implodes a multidimensional array
     *
     * @param array $array a multidimensional array
     * @return string converted array to a string
     */
    private function implodeMultiDimArray(array $array)
    {
        return implode(
            " / ",
            array_map(function ($a) {
                return implode(" ", $a);
            }, $array)
        );
    }
}
