<?php

/**
 * Universal modal for LimeSurveyProfessional Plugin
 *
 * needs the following parameters to work:
 * @var LimeSurveyProfessional $plugin this class is needed for translation of logout button
 * @var String $modalId the id for the modal
 * @var String $title the title for the modal header
 * @var String $message the message to be shown in the modal body
 * @var array $buttons array of button-html strings
 * @var boolean $unclosable if the modal should be unclosable
 */
$modalAttr = '';
$closeButton = '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
// Logout Button will always be added when unclosable Modal
if ($unclosable) {
    $buttons[] = '<a class="btn btn-default" href="' . $this->createUrl("admin/authentication/sa/logout") . '">' .
        '<span class="fa fa-sign-out"></span>&nbsp;' . $plugin->gT("Logout") .
        '</a>';
    $modalAttr = 'data-keyboard="false" data-backdrop="static"';
    $closeButton = '';
}

?>

<!-- Modal for block -->
<div id="<?= $modalId ?>" class="modal fade" role="dialog" <?= $modalAttr ?>>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <?= $closeButton ?>
                <div class="h3 modal-title">
                    <?= $title ?>
                </div>
            </div>
            <div class="modal-body">
                <p class='modal-body-text'>
                    <?= $message ?>
                </p>
            </div>
            <div class="modal-footer">
                <p class="text-right">
                    <?php foreach ($buttons as $button) : ?>
                        <?= $button ?>
                    <?php endforeach; ?>
                </p>
            </div>
        </div>
    </div>
</div>
