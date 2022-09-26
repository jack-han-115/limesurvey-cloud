<?php

namespace LimeSurveyProfessional\email;

use LimeSurvey\PluginManager\PluginEvent;

class LinkFilter extends EmailFilter
{
    /**
     * Constructor for LinkFilter
     *
     * Most of the code is taken from Tokens.php which should be removed eventually
     *
     *
     * @param PluginEvent $event
     */
    public function __construct(PluginEvent $event)
    {
        parent::__construct($event);
    }

    /**
     * Checks for a given mail if it has spam links.
     * If so, true will be returned
     * @return bool
     *
     */
    public function lookForSpamLinks()
    {
        $detected = false;
        $mailer = $this->event->get('mailer', new \LimeMailer());
        $isHtml = $mailer->getIsHtml();
        $body = $this->event->get('body', '');
        $surveyId = (int)$this->event->get('survey', '');

        $links = ($isHtml) ? $this->getLinksForHtml($body) : $this->getLinks($body);

        // Check if the link has the wanted infos
        foreach ($links as $link) {
            if (
                strpos($link, 'token') === false || strpos($link, (string)$surveyId) === false || strpos(
                    $link,
                    $_SERVER['HTTP_HOST']
                ) === false
            ) {
                $detected = true;
                break;
            }
        }
        return $detected;
    }

    /**
     * In HTML mode, the message of the mail must be filterer.
     * We only want the body content: headers or css can have legitimate external links
     * We also want to exclude pictures source
     *
     * @param $body string the content of the mail
     * @return array an array containing the links found inside that mail
     */
    private function getLinksForHtml(string $body)
    {
        $links = array();
        $doc = new \DOMDocument();
        @$doc->loadHTML($body);

        // This will exclude pictures but include links
        $body = $doc->getElementsByTagName('body');
        foreach ($body as $p) {
            $links = array_merge($links, $this->getLinks($p->nodeValue));
        }

        // A link tag (<a href="">) can contain a link without http or https
        // So we just add them to the array of links to check
        $linkTags = $doc->getElementsByTagName('a');
        foreach ($linkTags as $link) {
            $links[] = $link->getAttribute('href');
        }
        return $links;
    }

    /**
     * Look for any links inside a chunk of text (any string starting with http or https)
     *
     * @param $chunk string the content of the mail
     * @return array an array containing the links found inside that mail
     */
    private function getLinks($chunk)
    {
        $urlPattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        $links = array();

        preg_match_all($urlPattern, $chunk, $matches);

        // The pattern catch too many things so this will clean the results
        foreach ($matches[0] as $match) {
            if (substr($match, 0, 4) == 'http' || substr($match, 0, 3) == 'www') {
                $links[] = $match;
            }
        }
        return $links;
    }
}
