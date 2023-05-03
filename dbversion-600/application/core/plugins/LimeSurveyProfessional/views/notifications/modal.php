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
$closeButton = '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
// Logout Button will always be added when unclosable Modal
if ($unclosable) {
    $buttons[] = '<a class="btn btn-outline-secondary" href="' . $this->createUrl("admin/authentication/sa/logout") . '">' .
        '<span class="ri-logout-box-r-line"></span>&nbsp;' . $plugin->gT("Logout") .
        '</a>';
    $modalAttr = 'data-bs-keyboard="false" data-bs-backdrop="static"';
    $closeButton = '';
}

?>

<!-- Modal for block -->
<div id="<?= $modalId ?>" class="modal fade" role="dialog" <?= $modalAttr ?>>
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $title ?>
                </h5>
                <?= $closeButton ?>
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
