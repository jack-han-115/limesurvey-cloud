<?php
/** @var int $bannerId */

/** @var String $message */

$saveUrl = \Yii::app()->createUrl(
    "plugins/direct/plugin/LimeSurveyProfessional/function/updateBannersAcknowledgedObject"
);
?>

<div id="promotional-banner" class="alert alert-info alert-dismissible" role="alert"
     data-href="<?= $saveUrl ?>"
     data-bid="<?= $bannerId ?>">

    <strong><?= $message ?></strong>
    <button type="button" id="promotional-close" class="close" data-dismiss="alert"
            aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
