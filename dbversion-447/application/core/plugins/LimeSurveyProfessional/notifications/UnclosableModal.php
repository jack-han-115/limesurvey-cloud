<?php

namespace LimeSurveyProfessional\notifications;

class UnclosableModal
{
    /** @var \LimeSurveyProfessional */
    public $plugin;

    /** @var string the title for the modal header */
    public $title;

    /** @var string the message to be shown in the modal body */
    public $message;

    /** @var array of button-html strings */
    public $buttons;

    /**
     * Constructor for UnclosableModal a parent class for all classes who show a unclosable modal
     *
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(\LimeSurveyProfessional $plugin)
    {
        $this->plugin = $plugin;
        $this->title = '';
        $this->message = '';
        $this->buttons = [];
    }

    /**
     * takes the pre-set data renders the modal, and lets js show the modal
     */
    public function showModal()
    {
        $data = [
            'plugin' => $this->plugin,
            'title' => $this->title,
            'message' => $this->message,
            'buttons' => $this->buttons
        ];

        // Generate modal html
        $modal = json_encode(
            \Yii::app()->controller->renderPartial(
                'LimeSurveyProfessional.views.notifications.unclosableModal',
                $data,
                true
            ),
            JSON_HEX_APOS
        );

        // add notifications js
        $assetsUrl = \Yii::app()->assetManager->publish(dirname(__FILE__) . '/../js/notifications');

        // Make modal html accessible for js
        App()->clientScript->registerScript(
            'unclosableModalJsHtml',
            <<<EOT
                function getModalHtml() {
                    
                    return $modal;
                }
EOT
            ,
            \CClientScript::POS_BEGIN
        );
        App()->clientScript->registerScriptFile(
            $assetsUrl . '/unclosableModal.js',
            \CClientScript::POS_END
        );
    }
}
