<?php

namespace LimeSurveyProfessional;

class LinksAndContactHmtlHelper
{
    /**
     * Returns plain link of Transaction history site
     *
     * @param String $adminLang
     * @return string
     */
    public function getTransactionHistoryLink(string $adminLang)
    {
        // @TODO this array will need to be in a separate config file at some point
        $languages = array('de' => 'de', 'es' => 'es', 'fr' => 'fr', 'pt' => 'pt', 'pt-BR' => 'pt');

        $lang = array_key_exists($adminLang, $languages) ? $languages[$adminLang] . '/' : '';

        return sprintf('https://account.limesurvey.org/%sbilling?action=invoices&task=view', $lang);
    }

    /**
     * Returns the email address of the site admin
     *
     * @return string
     */
    public function getSiteAdminEmail()
    {
        return getGlobalSetting('siteadminemail');
    }

    /**
     * Returns the $link as html formatted link
     * @param string $link
     * @param string $title
     *
     * @return string
     */
    public function toHtmlLink(string $link, string $title)
    {
        return '<a href="' . $link . '" target="_blank">' . $title . '</a>';
    }

    /**
     * Returns the $link as html formatted link-button
     * @param string $link
     * @param string $title
     *
     * @return string
     */
    public function toHtmlLinkButton(string $link, string $title)
    {
        return '<a class="btn btn-primary" href="' . $link . '" target="_blank"><span class="fa fa-external-link"></span>&nbsp;' . $title . '</a>';
    }

    /**
     * Returns the email address as html mailto-link
     * @param string $email
     * @return string
     */
    public function toHtmlMailLink(string $email)
    {
        return '<a href="mailto:' . $email . '">' . $email . '</a>';
    }

    /**
     * Returns the email address as html mailto-link-button
     * @param string $email
     * @param string $title
     * @return string
     */
    public function toHtmlMailLinkButton(string $email, string $title)
    {
        return '<a class="btn btn-primary" href="mailto:' . $email . '"><span class="fa fa-envelope"></span>&nbsp;' . $title . '</a>';
    }


}
