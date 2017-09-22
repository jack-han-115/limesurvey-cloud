<?php

/**
 * @since 2017-09-20
 * @author Olle Härstedt
 */

?>

<div id='global-settings-storage'>
    <input type='hidden' name='global-settings-storage-url' value='<?php echo Yii::app()->createUrl('admin/globalsettings', array('sa' => 'getStorageData')); ?>' />
    <button id='global-settings-calculate-storage' class='btn btn-default '>
        <span class='fa fa-cogs'></span>&nbsp;
        <?php eT('Calculate storage'); ?>
    </button>
    <br/>
    <span class='text-muted'>
        <?php eT('Depending on the number of uploaded files, this might take some time.'); ?>
    </span>
</div>

<!-- Start LimeService Mod -->
<div id='global-settings-limeservice'>
    <br/>
    <button
        id='global-settings-calculate-storage'
        class='btn btn-primary '
        onclick='window.location.href = "<?php echo Yii::app()->createUrl('admin/globalsettings/', array('sa' => 'refreshStorageUsage')); ?>"; return false;'
        >
        <span class='fa fa-refresh'></span>&nbsp;
        <?php eT('Refresh LimeSurvey Pro storage usage'); ?>
    </button>
    <br/>
    <span class='text-muted'>
        <?php eT('Update the storage usage of your installation. Use this to unlock surveys after you\'ve deleted files.'); ?>
    </span>
</div>
<!-- End LimeService Mod -->
