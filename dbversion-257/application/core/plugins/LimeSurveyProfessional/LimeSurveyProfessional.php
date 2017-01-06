<?php

/**
 * The LimeSurveyProfessional plugin for "free" LimeService systems
 * Source for the cookie consent popup: https://cookieconsent.insites.com/documentation/javascript-api/
 * Requires Bootstrap for modal popup.
 */
class LimeSurveyProfessional extends \ls\pluginmanager\PluginBase
{
    protected $storage = 'DbStorage';
    static protected $description = 'LimeSurvey Professional extras';
    static protected $name = 'LimeSurveyProfessional';

    protected $settings = array();

    /**
     * @return void
     */
    public function init()
    {   
        $this->subscribe('beforeSurveyPage');
        $this->subscribe('beforeDeactivate');

        /* Settings not available in LimeService version
        $this->settings = array(
            'protectionPolicyOption' => array(
                'type'    => 'select',
                'label'   => gT('Protection policy option'),
                'options' => array(
                    'default' => gT('Default information'),
                    'text'    => gT('Text box'),
                    'link'    => gT('Link')
                ),
                'help'    => 'All options will show information in a modal popup.',
                'default' => 'opt1',
            ),
            'protectionPolicyText' => array(
                'type'    => 'text',
                'label'   => gT('Protection policy text'),
                'default' => '',
                'help'    => gT('Paste your protection policy here.')
            ),
            'protectionPolicyLink' => array(
                'type'    => 'string',
                'label'   => gT('Protection policy link'),
                'default' => '',
                'help'    => 'Link to your protection policy information.'
            ),
        );
        */
    }

    /**
     * If this is a LimeService installation with free subscription, don't allow to disable it
     * @return void
     */
    public function beforeDeactivate()
    {   
        // Check if this is a LimeService installation
        $isLimeServiceInstallation = function_exists('getInstallationID') && isset(Yii::app()->dbstats);
        if ($isLimeServiceInstallation) {
            // Get subsription plan
            $result = Yii::app()->dbstats
                ->createCommand(
                    'SELECT subscription_alias FROM limeservice_system.installations WHERE user_id = ' . getInstallationID())
                    ->queryRow();
            // If "free", it should not be possible to deactivate
            if ($result['subscription_alias'] == 'free') {
                $event = $this->getEvent();
                $event->set('success', false);
            }
        }
    }

    /**
     * @todo Use $this->gT() instead of gT() (LS 3.0.0)
     * @todo Download cookieconsent?
     * @return void
     */
    public function beforeSurveyPage()
    {   
        $settings = $this->getPluginSettings(true);

        // Get survey language
        $event = $this->getEvent();
        $surveyId = $event->get('surveyId');
        $lang = $_SESSION['survey_' . $surveyId]['s_lang'];
        if (empty($lang)) {
            $lang = 'en';  // Default to English
        }

        $message = gT('This website uses cookies. By continuing this survey you approve the data protection policy of the service provider.');
        $gotit   = gT('OK');
        $moreinfo = gT('View policy');

        Yii::app()->clientScript->registerScript('cint-common-js', <<<EOT
            window.addEventListener("load", function() {
            window.cookieconsent.initialise({
                "content": {
                    "message": "$message",
                    "dismiss": "$gotit",
                    "link":    "$moreinfo",
                    "href":    ""
                },
                "layout": "my-layout",
                "layouts": {
                    "my-layout": "{{messageandlink}}{{dismiss}}"
                },
                "elements": {
                    "messageandlink": "<span id='cookieconsent:desc' class='cc-message'>{{message}}&nbsp;<a aria-label='learn more about cookies' tabindex='0' class='cc-link' href='{{href}}' onclick='$(\"#plugin-adsense-modal\").modal(); return false;'>{{link}}</a></span>",
                    "dismiss": "<a aria-label='dismiss cookie message' tabindex='0' class='cc-btn cc-dismiss'>{{dismiss}}</a>"
                },
                "palette": {
                    "popup": {
                        "background": "#000"
                    },
                    "button": {
                        "background": "#f1d600"
                    }
                },
                "theme": "classic",
                "position": "top",
                onPopupOpen: function() {
                }
            })
          });
EOT
        , CClientScript::POS_END);

        $data = array(
            'settings' => $settings,
            'lang'     => $lang
        );

        Yii::setPathOfAlias('lspro', dirname(__FILE__));
        Yii::app()->controller->renderPartial('lspro.views.modal', $data);

    }

}

