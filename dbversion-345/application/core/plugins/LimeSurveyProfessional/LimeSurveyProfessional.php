<?php

use LimeSurveyProfessional\InstallationData;

/**
 * The LimeSurveyProfessional plugin for "free" LimeService systems
 * Source for the cookie consent popup: https://cookieconsent.insites.com/documentation/javascript-api/
 * Requires Bootstrap for modal popup.
 */
class LimeSurveyProfessional extends \LimeSurvey\PluginManager\PluginBase
{
    protected $storage = 'DbStorage';
    static protected $description = 'LimeSurvey Cloud extras';
    static protected $name = 'LimeSurveyProfessional';

    protected $settings = [
        'analytics' => [
            'apiHost' => 'https://analytics.limesurvey.org',
            'postHogToken' => 'phc_zgWEIuSlDVtXXISxJce6HvJC7mYI0UvuDlD8QfI3s8L',
            'allowedServersForAnalytics' => [
                's1.limesurvey.host',
                's2.limesurvey.host'
            ],
        ]
    ];

    /**
     * @return void
     */
    public function init()
    {
        $this->subscribe('beforeCloseHtml');
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
                    'SELECT advertising FROM limeservice_system.installations WHERE user_id = ' . getInstallationID()
                )
                ->queryRow();
            // If "free", it should not be possible to deactivate
            if ($result['advertising'] == '1') {
                $event = $this->getEvent();
                $event->set('success', false);
            }
        }
    }

    /**
     * @todo Use $this->gT() instead of gT() (LS 3.0.0)
     * @todo Download cookieconsent?
     * @todo Remove plugin.
     * @return void
     */
    public function beforeCloseHtml()
    {
        // OBS OBS OBS: Disabled.
        return;

        $settings = $this->getPluginSettings(true);

        // Get survey language
        $event = $this->getEvent();
        $surveyId = $event->get('surveyId');
        if ($surveyId && isset($_SESSION['survey_' . $surveyId])) {
            $lang = $_SESSION['survey_' . $surveyId]['s_lang'];
        } else {
            $lang = App()->language;
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

        $assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__));
        App()->clientScript->registerCssFile($assetsUrl . '/css/cookieconsent.min.css');
        App()->clientScript->registerScriptFile($assetsUrl . '/js/cookieconsent.min.js', CClientScript::POS_END);

        Yii::setPathOfAlias('lspro', dirname(__FILE__));
        $event->set('html', Yii::app()->controller->renderPartial('lspro.views.modal', $data, true));
    }

    /**
     * If user is a logged-in user we can assume, that backend is accessed right now.
     */
    public function isBackendAccess(): bool
    {
        return !Yii::app()->user->isGuest;
    }

    /**
     * Is Survey in Progress
     */
    public function isViewingSurvey(bool $ignoreAction = false): bool
    {
        $session = Yii::app()->session;
        $controller = Yii::app()->controller;

        $isSurveyController = $controller ? $controller->getId() == 'survey' : false;
        $sid = Yii::app()->request->getQuery('sid');

        if (!$ignoreAction) {
            $action = $controller ? $controller->getAction() : null;
            $isSurveyAction = $action ? $action->getId() == 'index' : false;

            return $isSurveyController
                && $isSurveyAction
                && isset($session['survey_' . $sid]);
        } else {
            return $isSurveyController
                && isset($session['survey_' . $sid]);
        }
    }

    /**
     *  returns populated InstallationData
     */
    private function getInstallationData(): InstallationData
    {
        $installationData = new InstallationData();
        $installationData->create(
            new \LimeSurvey\Models\Services\LimeserviceSystem(
                \Yii::app()->dbstats,
                (int)getInstallationID()
            ),
            App()->user->id == 1
        );

        return $installationData;
    }K
}
