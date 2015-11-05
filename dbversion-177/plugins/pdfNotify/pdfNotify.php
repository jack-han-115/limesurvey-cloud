<?php

/**
 * PDF Notification Copyright by Kai Ravesloot
 * User: kairavesloot
 * Date: 18.03.14
 * Time: 10:14
 */
class pdfNotify extends PluginBase
{
    protected $storage = 'DbStorage';
    static protected $description = 'After a respondent finished a survey, a PDF is send to a certain email with his answers. Register &  Readme on Lime-Support.com';
    static protected $name = 'PDFNotify';

    public $adminenmail;

    // insert admin from

    protected $settings = array(
        'url' => array( // should be email??
            'type' => 'string',
            'label' => 'Your registration url'
        ),
        'key' => array(
            'type' => 'string',
            'label' => 'Your personal key'
        ),
    );


    public function __construct(PluginManager $manager, $id)
    {
        parent::__construct($manager, $id);

        include_once(dirname(__FILE__) . '/assets/pdfNotify_license.php'); // ************* LimeService *************
        include_once(dirname(__FILE__) . '/assets/pdfNotify_helper.php');
        include_once(Yii::getPathOfAlias('application') . '/third_party/tcpdf/tcpdf.php');
        include_once(dirname(__FILE__) . '/assets/pdfNotify_createPDF.php');

        $this->pluginID = $id;
        $this->subscribe('beforeSurveySettings');
        $this->subscribe('newSurveySettings');
        $this->subscribe('afterSurveyComplete', 'pdfNotify_start');
        $this->subscribe('afterPluginLoad', 'licenseValidation'); // ************* LimeService *************
    }

    /**
     *  ************* LimeService *************
     *
     * This Function validates the license key and set TRUE or False to Database.
     * The the boolean is used for a smart "Demo" validation in class createPDF
     *
     * todo insert key 'license_last_check' as date for monthly sql validation
     */

    public function licenseValidation()
    {
        $event = $this->getEvent();
        $key = $this->get('key');
        $validation = new PDFNotify_license($key); // $this->get('key')

        // check if the validation already fired
        // check if validation return FALSE or nothing
        if (!$validation->check_key() && !empty($key)) {
            $this->set('license_validation', FALSE, NULL, NULL);
        } else {
            if ($validation->check_key()) {
                $this->set('license_validation', TRUE, NULL, NULL);
            }
        }
    }

    /*
     * Below are the actual methods that handle events
     */
    public function pdfNotify_start()
    {
        // get survey data
        $event = $this->getEvent();
        $surveyId = $event->get('surveyId');
        $responseId = $event->get('responseId');
        $survey_response = $this->pluginManager->getAPI()
            ->getResponse($surveyId, $responseId);
        // get plugin-settings
        $plugin_settings = pdfNotify_helper::getPluginSettings($surveyId, $this->pluginID);


        // fire plugin function only if plugin is active for this survey
        if ($plugin_settings['pdfnotify_active']['current'] == 1) {
            $SurveyInfo = getSurveyInfo($surveyId);
            $sender['mail'] = $SurveyInfo['adminemail'];
            $sender['name'] = $SurveyInfo['admin'];
            // lang setting for pdf
            if ($plugin_settings['pdfnotify_lang']['current'] == 0) {
                $startlanguage = $SurveyInfo['language']; // todo what languge should be sent?
            } else {
                $startlanguage = $survey_response['startlanguage'];
            }
            $baseLang = $SurveyInfo['language'];
            $licenseValid = 'false'; // // ************* LimeService ************* only for LimeService set to true for open product
            $licenseValid = $this->get('license_validation'); //// ************* LimeService ************* LimeService: only for LimeService set to true for open product

            $helper = new pdfNotify_helper($surveyId,$this->pluginID);
            $FullResponseTable = $helper->getFullResponseTable($surveyId, $responseId, $startlanguage);

            if ($SurveyInfo['datestamp'] != "Y") {
                unset ($FullResponseTable['submitdate']);
            } else {
                unset ($FullResponseTable['id']);
            } // do we need this??
            unset ($FullResponseTable['token']);
            unset ($FullResponseTable['lastpage']);
            unset ($FullResponseTable['startlanguage']);
            unset ($FullResponseTable['datestamp']);
            unset ($FullResponseTable['startdate']);

            $sSiteName = Yii::app()->getConfig('sitename');
            // get recipients ONLY from base lang
            $recipients = $helper->EmailAddressProcess($plugin_settings['pdfnotify_recipient']['current'], $survey_response, $baseLang, $surveyId);

            // create email subject
            if(empty($plugin_settings['pdfnotify_subject']['current'])){
                $sSubject = 'PDF Notify';
            }else{
                $sSubject = pdfNotify_helper::tokenReplacement($surveyId, $survey_response, $plugin_settings['pdfnotify_subject']['current']); //ok
            }
            // create body
            if(empty($plugin_settings['pdfnotify_body']['current'])){
                $sBody = 'PDF Notify';
            }else{
                $sBody = $plugin_settings['pdfnotify_body']['current'];
            }
            // create pdf
            $filename = $responseId . '-survey_' . $surveyId . '.pdf';
            $responsePDF = Yii::app()
                    ->getConfig('tempdir') . DIRECTORY_SEPARATOR . $filename;
            $notifyPdf = new pdfNotify_createPDF($surveyId, $FullResponseTable, $filename, $licenseValid); // ($orientation, $unit, $format, true, 'UTF-8', false);
            $notifyPdf->Output($responsePDF, 'F'); // e oder f  this is working with saving!!

            // create attachment
            $attachment = array();
            $attachment[] = $responsePDF ;
            // use LimeSurvey "SendEmailMessage" Function
            $sent =  SendEmailMessage($sBody, $sSubject, $recipients, $sender['mail'], $sSiteName, $ishtml=false, $bouncemail=null, $attachment, $customheaders="");
            // delete file
            unset($notifyPdf);
            unlink($responsePDF);
        }
    }

    /**
     * This event is fired by the administration panel to gather extra settings
     * available for a survey.
     * The plugin should return setting meta data.
     */
    public function beforeSurveySettings()
    {
        $event = $this->getEvent();

        $SurveyInfo = getSurveyInfo($event->get('survey')); // do we need this? other way to get the adminemail?
        $sender['mail'] = $SurveyInfo['adminemail'];

        $subject = '[surveyId] LimeSurvey PDF notification'; // default value from UI!!

        $event->set("surveysettings.{$this->id}", array(
            'name' => get_class($this),
            'settings' => array(
                'pdfnotify_active' => array(
                    'type' => 'select',
                    'options' => array(
                        0 => 'No',
                        1 => 'Yes'
                    ),
                    'default' => 0,
                    'tab' => 'notification', // @todo: Setting no used yet
                    'category' => 'pdfnotify', // @todo: Setting no used yet
                    'label' => 'Activate PDF-Notify',
                    'current' => $this->get('pdfnotify_active', 'Survey', $event->get('survey'))
                ),
                'pdfnotify_lang' => array(
                    'type' => 'select',
                    'options' => array(
                        0 => 'base language',
                        1 => 'start language'
                    ),
                    'default' => 0,
                    'tab' => 'notification', // @todo: Setting no used yet
                    'category' => 'pdfnotify', // @todo: Setting no used yet
                    'label' => 'What language should the PDF have?',
                    'current' => $this->get('pdfnotify_lang', 'Survey', $event->get('survey'))
                ),
                'pdfnotify_recipient' => array(
                    'type' => 'string',
                    'default' => $sender['mail'],
                    'class' => 'myclass',
                    'tab' => 'notification',
                    // @todo: Setting no used yet
                    'category' => 'pdfnotify',
                    // @todo: Setting no used yet
                    'label' => 'Recipient',
                    'current' => $this->get('pdfnotify_recipient', 'Survey', $event->get('survey'))
                ),
                'pdfnotify_subject' => array(
                    'type' => 'string',
                    'default' => $subject,
                    // <p class="help-block">Example block-level help text here.</p>
                    'class' => 'myclass',
                    'tab' => 'notification',
                    // @todo: Setting no used yet
                    'category' => 'pdfnotify',
                    // @todo: Setting no used yet
                    'label' => 'Subject for notification email',
                    'current' => $this->get('pdfnotify_subject', 'Survey', $event->get('survey'))
                ),
                'pdfnotify_body' => array(
                    'type' => 'text',
                    'default' => 'PDF Notify',
                    'class' => 'myclass',
                    'tab' => 'notification',
                    // @todo: Setting no used yet
                    'category' => 'pdfnotify',
                    // @todo: Setting no used yet
                    'label' => 'Email body for notification email',
                    'current' => $this->get('pdfnotify_body', 'Survey', $event->get('survey'))
                ),
            )
        ));
    }

    public function newSurveySettings()
    {
        $event = $this->getEvent();
        foreach ($event->get('settings') as $name => $value) {
            $this->set($name, $value, 'Survey', $event->get('survey'));
        }
    }



}
