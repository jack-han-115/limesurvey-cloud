<?php
/** @var int $bannerId */

/** @var String $message */

$saveUrl = \Yii::app()->createUrl(
    "plugins/direct/plugin/LimeSurveyProfessional/function/updateBannersAcknowledgedObject"
);
?>
<?php
$this->widget('ext.AlertWidget.AlertWidget', [
        'text'            => $message,
        'type'            => 'info',
        'showIcon'        => false,
        'showCloseButton' => true,
        'isFilled'        => false,
        'htmlOptions'     => [
            'id'        => 'promotional-banner',
            'class'     => 'text-center fw-bold m-2',
            'data-href' => $saveUrl,
            'data-bid'  => $bannerId
        ],
    ]
);
?>
