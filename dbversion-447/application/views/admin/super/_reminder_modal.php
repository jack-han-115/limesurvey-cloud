<?php
/**
 * @var string $titel
 * @var string $message
 */

?>

<script type="text/javascript">
    $(window).load(function () {
        $('#reminderModal').modal('show');
    });
</script>

<div class="modal fade" id="reminderModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            Yii::app()->getController()->renderPartial(
                '/layouts/partial_modals/modal_header',
                ['modalTitle' => $titel]
            );
            ?>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <?php echo '<p>' . $message . '</p>'; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php
                    eT('Close'); ?></button>
                <?php
                if (Permission::model()->hasGlobalPermission('superadmin', 'read')) { ?>
                    <a class="btn btn-primary" href="<?php
                    echo Yii::app()->getConfig("linkToPricingPage") ?>" target="_blank">
                        <?php
                        et('Upgrade/Renew plan') ?>
                    </a>
                <?php
                }else{ ?>
                    <a class="btn btn-primary" href="mailto:<?php echo getGlobalSetting('siteadminemail')?>" >
                        <?php
                        et('Contact Survey Site Admin') ?>
                </a>
               <?php } ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
