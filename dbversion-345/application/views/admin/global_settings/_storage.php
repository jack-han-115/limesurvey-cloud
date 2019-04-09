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
        <!-- LimeService Mod Start -->
        <br/>
        <?php eT('Update the storage usage of your installation. Use this to unlock surveys after you\'ve deleted files, in case you\'ve got locked due to too high storage usage.'); ?>
        <!-- LimeService Mod End -->
    </span>
</div>
