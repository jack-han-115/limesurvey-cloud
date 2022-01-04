<?php
/**
 * Modal which can not be closed.
 *
 * needs the following parameters to work:
 * @var LimeSurveyProfessional $plugin this class is needed for translation of logout button
 * @var String $title the title for the modal header
 * @var String $message the message to be shown in the modal body
 * @var array $buttons array of button-html strings
 */

// Logout Button will always be added
$buttons[] = '<a class="btn btn-default" href="' . $this->createUrl("admin/authentication/sa/logout") . '">' .
    $plugin->gT("Logout") .
    '</a>';
?>

<!-- Modal for block -->
<div id="unclosable-notification-modal" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header panel-heading">
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
                <p class="text-center">
                    <?php foreach ($buttons as $button): ?>
                        <?= $button ?>
                    <?php endforeach; ?>
                </p>
            </div>
        </div>
    </div>
</div>
