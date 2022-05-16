<?php

namespace LimeSurveyProfessional\upgradeButton;

use LimeSurveyProfessional\InstallationData;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class UpgradeButton
{
    /**
     * Main function to display the upgrade button for free users
     * Loads necessary js and css and adds a menu item to the admin menu
     * @param \LimeSurveyProfessional $plugin
     * @param InstallationData $installationData
     * @return bool
     * @throws \CException
     */
    public function displayUpgradeButton(\LimeSurveyProfessional $plugin, InstallationData $installationData)
    {
        $display = false;
        if (!$installationData->isPayingUser) {
            // add upgradeButton js
            $assetsUrl = \Yii::app()->assetManager->publish(dirname(__FILE__) . '/../js/upgradeButton');
            App()->clientScript->registerScriptFile($assetsUrl . '/upgradeButton.js');
            // add upgradeButton css
            $cssUrl = \Yii::app()->assetManager->publish(dirname(__FILE__) . '/../css/upgradeButton');
            \Yii::app()->clientScript->registerCssFile($cssUrl . '/upgradeButton.css');

            $links = new \LimeSurveyProfessional\LinksAndContactHmtlHelper();
            $iconClass = '';
            if (!$installationData->isSiteAdminUser) {
                $iconClass = ' no-siteadmin';
                $this->prepareModal($plugin, $links);
            }

            $aMenuItemAdminOptions = [
                'isDivider' => false,
                'isSmallText' => false,
                'label' => $plugin->gT("Upgrade plan"),
                'href' => $links->getPricingPageLink(\Yii::app()->session['adminlang']),
                'iconClass' => 'fa fa-arrow-circle-up upgrade-icon' . $iconClass,
            ];

            $menuItem = new UpgradeButtonMenuClass($aMenuItemAdminOptions);

            $event = $plugin->getEvent();
            $event->append('extraMenus', [$menuItem]);
            $display = true;
        }

        return $display;
    }

    /**
     * renders the modal for non siteadmins, and makes js able to show the modal
     * @param \LimeSurveyProfessional $plugin
     * @param LinksAndContactHmtlHelper $links
     * @throws \CException
     */
    public function prepareModal(\LimeSurveyProfessional $plugin, LinksAndContactHmtlHelper $links)
    {
        $data = [
            'plugin' => $plugin,
            'title' => $plugin->gT('Upgrade notification'),
            'message' => sprintf(
                $plugin->gT(
                    'You do not have any permission to upgrade to a higher plan, please contact your site admin %s'
                ),
                $links->toHtmlMailLink($links->getSiteAdminEmail())
            ),
            'buttons' => [
                $links->toHtmlMailLinkButton(
                    $links->getSiteAdminEmail(),
                    $plugin->gT('Contact Survey Site Adminstrator')
                )
            ],
            'modalId' => 'upgrade-notification',
            'unclosable' => false
        ];

        // Generate modal html
        $modal = json_encode(
            \Yii::app()->controller->renderPartial(
                'LimeSurveyProfessional.views.notifications.modal',
                $data,
                true
            ),
            JSON_HEX_APOS
        );

        // add notifications js
        $assetsUrl = \Yii::app()->assetManager->publish(dirname(__FILE__) . '/../js/upgradeButton');

        // Make modal html accessible for js
        App()->clientScript->registerScript(
            'modalJsHtml',
            <<<EOT
                function getModalHtml() {
                    
                    return $modal;
                }
EOT
            ,
            \CClientScript::POS_BEGIN
        );
    }
}
