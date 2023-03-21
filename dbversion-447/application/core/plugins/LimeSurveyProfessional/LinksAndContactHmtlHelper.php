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
     * Returns plain link of contact corporate site
     *
     * @param String $adminLang
     * @return string
     */
    public function getContactCorporateLink(string $adminLang)
    {
        $enLink = 'https://www.limesurvey.org/support/contact-corporate';
        // @TODO this array will need to be in a separate config file at some point
        $languages = [
            'de' => 'https://www.limesurvey.org/de/hilfe/kontakt-corporate',
            'es' => 'https://www.limesurvey.org/es/ayuda/contact-corporate-es',
            'fr' => 'https://www.limesurvey.org/fr/aide/contact-corporate-fr',
            'pt' => 'https://www.limesurvey.org/pt/ajuda/contact-corporate-pt',
            'pt-BR' => 'https://www.limesurvey.org/pt/ajuda/contact-corporate-pt'
        ];

        return array_key_exists($adminLang, $languages) ? $languages[$adminLang] : $enLink;
    }

    /**
     * Returns plain link of pricing site
     *
     * @param String $adminLang
     * @return string
     */
    public function getPricingPageLink(string $adminLang)
    {
        $enPricingSubDir = 'pricing';
        // @TODO this array will need to be in a separate config file at some point
        $languages = [
            'cs' => 'cs/cenik',
            'de' => 'de/preise',
            'es' => 'es/precios',
            'es-MX' => 'es-mx/precios',
            'fi' => 'fi/hinnat',
            'fr' => 'fr/prix',
            'hr' => 'hr/cijene',
            'id' => 'id/harga',
            'it' => 'it/prezzi',
            'hu' => 'hu/arkepzes',
            'ms' => 'ms/harga',
            'nb' => 'nb/priser',
            'nl' => 'nl/prijzen',
            'pl' => 'pl/wycena',
            'pt' => 'pt/precos',
            'pt-BR' => 'pt/precos',
            'ro' => 'ro/tarife',
            'sk ' => 'sk/stanovenie-cien',
            'tr' => 'tr/fiyatlandirma',
            'vi' => 'vi/bieu-gia',
        ];
        $pricingSubDir = array_key_exists($adminLang, $languages) ? $languages[$adminLang] . '/' : $enPricingSubDir;

        return 'https://www.limesurvey.org/' . $pricingSubDir;
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
