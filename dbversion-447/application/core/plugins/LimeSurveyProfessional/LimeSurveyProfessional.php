<?php

use LimeSurveyProfessional\InstallationData;
use LimeSurveyProfessional\notifications\LimitReminderNotification;
use LimeSurveyProfessional\notifications\OutOfResponsesPaid;
use LimeSurveyProfessional\promotionalBanners\PromotionalBanners;
use LimeSurveyProfessional\ParticipantRegisterCta\ParticipantRegisterCta;
use LimeSurvey\Libraries\FormExtension\Inputs\ButtonSwitchInput;
use LimeSurvey\PluginManager\PluginBase;
use LimeSurvey\PluginManager\PluginEvent;

require_once(__DIR__ . '/vendor/autoload.php');
/**
 * The LimeSurveyProfessional plugin for "free" LimeService systems
 * Source for the cookie consent popup: https://cookieconsent.insites.com/documentation/javascript-api/
 * Requires Bootstrap for modal popup.
 *
 * This plugin should at some point include all necessary different implementation for cloud edition.
 *
 *
 */
class LimeSurveyProfessional extends PluginBase
{
    protected $storage = 'DbStorage';
    protected static $description = 'LimeSurvey Cloud extras';
    protected static $name = 'LimeSurveyProfessional';
    protected $complete = false;
    /** @var int violationCounter for email blacklist filter */
    public static $violationCount = 0;
    /** @var string can contain info regarding the email blacklist filter */
    public static $violationText = '';

    protected $settings = [
        'allowedServersForAnalytics' => [
            'limesurvey-1.limesurvey.org',
        ]
    ];

    public function init($loadConfig = true): void
    {
        \Yii::setPathOfAlias(get_class($this), dirname(__FILE__));
        \Yii::import('application.helpers.common_helper', true);
        if ($loadConfig) {
            $this->readConfigFile();
        }
        $this->subscribe('beforeDeactivate'); // user should not be able to deactivate this one ...
        $this->subscribe('beforeControllerAction');
        $this->subscribe('beforeTokenEmail');
        $this->subscribe('beforeAdminMenuRender');
        $this->subscribe('newDirectRequest');
        $this->subscribe('beforeCloseHtml');
        $this->subscribe('afterSurveyComplete');
        $this->subscribe('beforeSurveyPage');
        if (in_array(gethostname(), $this->settings['allowedServersForAnalytics'], true)) {
            $this->subscribe('renderHead');
        }

        // @todo This needs to be properly db versioned
        if (!function_exists('db_upgrade_all')) {
            Yii::app()->loadHelper('update/updatedb');
        }
        $Column = App()->db->getSchema()->getTable('{{surveys_languagesettings}}')->getColumn('surveyls_legal_notice');
        if ($Column === null) {
            addColumn('{{surveys_languagesettings}}', 'surveyls_legal_notice', "text");
        }
        $Column = App()->db->getSchema()->getTable('{{surveys}}')->getColumn('showdatapolicybutton');
        if ($Column === null) {
            addColumn('{{surveys}}', 'showdatapolicybutton', 'integer DEFAULT 0');
        }
        $Column = App()->db->getSchema()->getTable('{{surveys}}')->getColumn('showlegalnoticebutton');
        if ($Column === null) {
            addColumn('{{surveys}}', 'showlegalnoticebutton', 'integer DEFAULT 0');
        }

        $installationData = $this->getInstallationData();
        $this->addAdvertisementGlobalSettings($installationData);
    }

    protected function addAdvertisementGlobalSettings(InstallationData $installationData): void
    {
        $that = $this;
        if (
            $installationData->plan === 'free' ||
            $installationData->plan === 'basic'
        ) {
            $helpLinkText = $this->gT('Upgrade now');
            $helpLink = sprintf(
                ' <a href="%s" target="_blank">' . $helpLinkText . '</a>.',
                'https://www.limesurvey.org/pricing'
            );
            $help = $installationData->isSiteAdminUser ?
                $this->gT(
                    'This option can only be disabled by customers using our Expert or Enterprise package.'
                ) . $helpLink
            : $this->gT('Contact your site admin to upgrade');

            Yii::app()->formExtensionService->add(
                'globalsettings.general',
                new ButtonSwitchInput(
                    [
                        'name' => 'limesurvey_professional_advertisement',
                        'label' => $this->gT('Show LimeSurvey branding in survey'),
                        'disabled' => true,
                        'help' => $help,
                        'save' => function ($request, $connection) {
                            // Empty function, free and basic installation cannot change this value
                            return true;
                        },
                        'load' => function () {
                            return 'Y';
                        }
                    ]
                )
            );
        } else {
            Yii::app()->formExtensionService->add(
                'globalsettings.general',
                new ButtonSwitchInput(
                    [
                        'name' => 'limesurvey_professional_advertisement',
                        'label' => $this->gT('Show LimeSurvey branding in survey'),
                        'help' => $this->gT('Turn on or off LimeSurvey branding in survey footer and end of survey'),
                        'save' => function ($request, $connection) use ($that) {
                            $value = $request->getPost('limesurvey_professional_advertisement') === '1' ? 'Y' : 'N';
                            return $that->set('limesurvey_professional_advertisement', $value);
                        },
                        'load' => function () use ($that) {
                            // Default to 'Y'
                            $val = $that->get('limesurvey_professional_advertisement') ?? 'Y';
                            return $val === 'Y' ? '1' : '0';
                        }
                    ]
                )
            );
        }
    }

    /**
     * This plugin can never be disabled.
     */
    public function beforeDeactivate(): void
    {
        $this->getEvent()->set('success', false);

        // Optionally set a custom error message.
        $this->getEvent()->set('message', gT('Core plugin can not be disabled.'));
    }

    /**
     * If user is on backend-level
     * 1. blocking notifications (unclosable modal) may be created
     * if not:
     * 2. if admin welcome-page is called, the
     *    limitReminderNotification and outOfresponsesPaid functionalities will be called
     * 3. promotionalBanner showing will be checked on every action
     */
    public function beforeControllerAction(): void
    {
        $controller = $this->getEvent()->get('controller');
        $action = $this->getEvent()->get('action');
        if ($this->isBackendAccess()) {
            $installationData = $this->getInstallationData();

            if (!$this->createBlockingNotifications($this->getEvent(), $installationData)) {
                if ($controller === 'admin' && $action === 'index') {
                    $limitReminderNotification = new LimitReminderNotification($this);
                    $limitReminderNotification->createNotification($installationData);

                    $outOfResponsesPaid = new OutOfResponsesPaid($this);
                    $outOfResponsesPaid->createNotification($installationData);

                    // Deactivated because of insufficient data on cloud side
                    //only usefull for paying users
                    //                    if ($installationData->isPayingUser) {
                    //                        $gracePeriodNotification = new GracePeriodNotification($this);
                    //                        $gracePeriodNotification->createNotification($installationData);
                    //                    }
                }
            }
            $today = new \DateTime('midnight');
            $promotionalBanner = new PromotionalBanners($this);
            $promotionalBanner->showPromotionalBanner($today, $installationData);
        }
    }

    /**
     * If user is a logged-in user we can assume, that backend is accessed right now.
     */
    public function isBackendAccess(): bool
    {
        return !Yii::app()->user->isGuest;
    }

    /**
     * Redirects to welcome-page if any other url is opened except welcome-page and logout
     *
     * @throws Exception if there's no event
     */
    public function forceRedirectToWelcomePage(PluginEvent $event = null): bool
    {
        if (is_null($event)) {
            return false;
        }
        $controller = $event->get('controller');
        $action = $event->get('action');
        $subAction = $event->get('subaction');
        return !($controller == 'admin' && $action == 'index') && $subAction != 'logout';
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
    }

    /**
     * All notification types which use unclosable modal sorted by importance
     * (most important first)
     * When a blocking modal is successfully created, it will be shown and the leftover classes won't be processe
     *
     * Attention: Every class inside array $blockingNotifications needs to have the function createNotification()
     * createNotification() needs to return a boolean!
     */
    public function createBlockingNotifications(PluginEvent $event, InstallationData $installationData): bool
    {
        $blockingNotification = false;
        $blockingNotifications = [
            ['class' => 'LimeSurveyProfessional\notifications\HardLockModal'],
            ['class' => 'LimeSurveyProfessional\notifications\OutOfResponsesFree'],
        ];

        foreach ($blockingNotifications as $notification) {
            $className = $notification['class'];
            $blockingNotificationClass = new $className($this);
            $blockingNotification = $blockingNotificationClass->createNotification($installationData);
            if ($blockingNotification) {
                break;
            }
        }

        if ($blockingNotification && $this->forceRedirectToWelcomePage($event)) {
            $controller = Yii::app()->getController();
            $controller->redirect($controller->createUrl('admin/index'));
        }

        return $blockingNotification;
    }

    /**
     *  Before a tokenEmail is sent, it will run through a blacklist filter, except this installation is whitelisted
     *  via 'disableEmailSpamChecking' config param
     */
    public function beforeTokenEmail(): void
    {
        if (!Yii::app()->getConfig("disableEmailSpamChecking")) {
            $installationData = $this->getInstallationData();
            $blacklistFilter = new \LimeSurveyProfessional\email\EmailFilter($this->getEvent());
            $blacklistFilter->filter($installationData);
        }
    }

    /**
     * Append new menu item to the admin topbar
     */
    public function beforeAdminMenuRender(): void
    {
        $installationData = $this->getInstallationData();
        $upgradeButton = new \LimeSurveyProfessional\upgradeButton\UpgradeButton();
        $upgradeButton->displayUpgradeButton($this, $installationData);
    }

    public function newDirectRequest(): void
    {
        $request = $this->api->getRequest();
        $event = $this->getEvent();
        if ($event->get('target') != 'LimeSurveyProfessional') {
            return;
        }

        $action = $event->get('function');
        if ($action == 'updateBannersAcknowledgedObject') {
            $installationData = $this->getInstallationData();
            $promotionalBanner = new PromotionalBanners($this);
            $promotionalBanner->updateBannersAcknowledgedObject($request, $installationData);
        }
    }

    /**
     * Initialise ParticipantRegisterCta before rendering
     *
     * We need to do this because we can not render partials within beforeCloseHtml or afterSurveyComplete.
     * Instead, we render in advance.
     */
    public function beforeSurveyPage(): void
    {
        // Use getInstance() to initialise the ParticipantRegisterCta instance
        // - so that it can be re-used in other call backs.
        // Missing variable assignment is intentional.
        ParticipantRegisterCta::getInstance(
            $this,
            $this->getInstallationData()
        );
    }

    /**
     * Is Survey Complete
     */
    public function isComplete(): bool
    {
        $isSurveyController = Yii::app()->controller->getId() == 'survey';
        $iSurveyID = Yii::app()->request->getQuery('sid');
        return $isSurveyController && (
            $this->complete || isset(Yii::app()->session['survey_' . $iSurveyID]['srid'])
        );
    }

    /**
     * Is Survey in Progress
     */
    public function isViewingSurvey(): bool
    {
        $session = Yii::app()->session;
        $controller = Yii::app()->controller;
        $action = $controller ? $controller->getAction() : null;
        $isSurveyController = $controller ? $controller->getId() == 'survey' : false;
        $isSurveyAction = $action? $action->getId() == 'index' : false;
        $sid = Yii::app()->request->getQuery('sid');
        return $isSurveyController
            && $isSurveyAction
            && isset($session['survey_' . $sid]);
    }

    /**
     * Append new content to the HTML body
     */
    public function beforeCloseHtml(): void
    {
        if ($this->isViewingSurvey() && !$this->isComplete()) {
            $participantRegisterCta =
                ParticipantRegisterCta::getInstance(
                    $this,
                    $this->getInstallationData()
                );
            $participantRegisterCta->display();
        }
    }


    /**
     * After survey is completed
     */
    public function afterSurveyComplete(): void
    {
        $this->complete = true;
        $participantRegisterCta =
            ParticipantRegisterCta::getInstance(
                $this,
                $this->getInstallationData()
            );
        $participantRegisterCta->display(true);
    }

    /**
     * Add analytics script of PostHog
     */
    public function renderHead(): void
    {
        if (!in_array(gethostname(), $this->settings['allowedServersForAnalytics'], true)) {
            return;
        }

        $html = $this->getEvent()->get('html') ?? '';

        $versionConfig = require(__DIR__ . '/../config/version.php');

        /** If we are in the admin part of LimeSurvey */
        if ($this->isBackendAccess() && !$this->isViewingSurvey()) {
            $html .= '<script>
                !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.async=!0,p.src=s.api_host+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
                posthog.init(
                    \'phc_zgWEIuSlDVtXXISxJce6HvJC7mYI0UvuDlD8QfI3s8L\',
                    {
                        api_host:\'https://analytics.limesurvey.org\',
                        save_referrer: false,
                        ip: false,
                        property_blacklist: ["$current_url", "$host", "$referrer", "$referring_domain"],
                        disable_session_recording: true,
                    }
                );
                posthog.register(
                    {"limeSurveyVersion": "' . $versionConfig['versionnumber'] . '"},
                    {"tarifPlan": "' . $this->getInstallationData()->plan . '"}
                    {"pathWithGetParams": window.location.pathname+window.location.search}
                );
            </script>';

            $this->getEvent()->set('html', $html);
        }
    }

    /**
     * Get config to allow plugin config to be read from plugin "sub-modules".
     *
     * This function retrieves plugin data. Do not cache this data; the plugin storage
     * engine will handle caching. After the first call to this function, subsequent
     * calls will only consist of a few function calls and array lookups.
     *
     * @param mixed $default The default value to use when not was set
     */
    public function getConfig(?string $key = null, ?string $model = null, ?int $id = null, $default = null): bool
    {
        return $this->get($key, $model, $id, $default);
    }
}
