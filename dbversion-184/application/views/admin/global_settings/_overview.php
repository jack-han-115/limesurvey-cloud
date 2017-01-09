<?php
/**
 * This view generate the 'overview' tab inside global settings.
 *
 * @var int $usercount
 * @var int $surveycount
 * @var int $activesurveycount
 * @var int $deactivatedsurveys
 * @var int $activetokens
 * @var int $deactivatedtokens
 *
 */
?>

<div class='header ui-widget-header'><?php eT("System overview"); ?></div>
<br /><table class='statisticssummary'>
    <tr>
        <th ><?php eT("Users"); ?>:</th><td><?php echo $usercount; ?></td>
    </tr>
    <tr>
        <th ><?php eT("Surveys"); ?>:</th><td><?php echo $surveycount; ?></td>
    </tr>
    <tr>
        <th ><?php eT("Active surveys"); ?>:</th><td><?php echo $activesurveycount; ?></td>
    </tr>
    <tr>
        <th ><?php eT("Deactivated result tables"); ?>:</th><td><?php echo $deactivatedsurveys; ?></td>
    </tr>
    <tr>
        <th ><?php eT("Active token tables"); ?>:</th><td><?php echo $activetokens; ?></td>
    </tr>
    <tr>
        <th ><?php eT("Deactivated token tables"); ?>:</th><td><?php echo $deactivatedtokens; ?></td>
    </tr>
    <?php
        if (Yii::app()->getConfig('iFileUploadTotalSpaceMB')>0)
        {
            $fUsed=calculateTotalFileUploadUsage();
        ?>
        <tr>
            <th ><?php eT("Used/free space for file uploads"); ?>:</th><td><?php echo sprintf('%01.2F',$fUsed); ?> MB / <?php echo sprintf('%01.2F',Yii::app()->getConfig('iFileUploadTotalSpaceMB')-$fUsed); ?> MB</td>
        </tr>
        <?php
        }
    ?>
</table>
<?php
    // ============ Update LimeService Begin =======================================================
    $iDestinationVersion=257;
    $sUpgradeVersion='2.50+';
    // Check if already scheduled for upgrade
    $sDomain=$_SERVER['SERVER_NAME'];
    $sSubDomain=substr($sDomain,0,strpos($sDomain,'.'));
    $sRootDomain=substr($sDomain,strpos($sDomain,'.')+1);
    $iUpgradeDBVersion = Yii::app()->dbstats->createCommand("select upgradedbversion from pageviews where subdomain='$sSubDomain' and rootdomain='$sRootDomain'")->queryScalar();
    if (intval($iUpgradeDBVersion)<$iDestinationVersion)
    {
        if (Yii::app()->request->getPost('subaction')=='scheduleupgrade')
        {
            // Schedule for upgrade
            Yii::app()->dbstats->createCommand("Update pageviews set upgradedbversion=$iDestinationVersion where subdomain='$sSubDomain' and rootdomain='$sRootDomain'")->execute();
            ?>
            <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;">
                <span style="font-weight: bold; font-size: 14pt;">Upgrade to <?php echo $sUpgradeVersion; ?> is now scheduled!</span><br /><br />
                <b>Your LimeService installation was scheduled for upgrade.</b> Upgrade cycles run every full hour. The current server time is <?php echo date('g:i a');?>.<br> 
                This action cannot be undone. When the update process has finished you will receive an email.<p /></div>
            <?php 
        }
        else //explain the upgrade
        { ?>
            <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;"><span style="font-weight: bold; font-size: 14pt;">New version available - <br />upgrade now and get 20 free responses!</span>
            <br><br>
                There is a new LimeSurvey version available with many new features, version <?php echo $sUpgradeVersion; ?>. <span style="font-weight: bold;">Important:</span> Before you press the "Upgrade" button please read the following lines - it is not the usual blah blah:<br />
                <br />
                <span style="font-weight: bold;">Is my data safe when upgrading?</span><br />
                Yes. Your data is safe. During the upgrade process itself there will be a short downtime of 2 minutes.<br />
                <br />
                <span style="font-weight: bold;">Do I need to check anything first? Can I upgrade while having active surveys?</span><br />
                Please note that the <b>browser requirement for the LimeSurvey administration has changed</b>: <br>For the administration part we currently support IE9 and all newer comparable browsers like FF, Chrome, Opera, etc. We do not support any IE version running in Intranet-mode or Compatibility-mode!<br>
                In LimeSurvey <?php echo $sUpgradeVersion; ?> there have been many improvements. It is in general possible to upgrade while having running surveys but existing links to your surveys will be changed by the upgrade
                and so we recommend that you only run this upgrade while not having any active surveys. If you have customized templates they will not properly work anymore. If you have customized Javascript it most probably will need to be adjusted.<br />  
                <br />
                <span style="font-weight: bold;">What happens after the upgrade is done?</span><br />
                After the upgrade is done you will receive an automatic notification email. Please login and check your surveys and templates after you received the email. As a bonus for upgrading we will even <b>credit your account with 20 Survey Responses!</b><br />
                <br />
                <span style="font-weight: bold;">Can I undo the upgrade?</span><br />
                The upgrade cannot be undone. When it has finished you will receive an automatic email.<br />
                <br />
                If you have read the paragraphs above please press the button below to schedule your installation for upgrade. Upgrade cycles run on every full hour. The current server time is <?php echo date('g:i a');?>.<br />
                <br />
                <span style="font-weight: bold;"></span><br /></div>

            <?php echo CHtml::form(array("admin/globalsettings"), 'post', array('class'=>'form30','id'=>'frmglobalsettings','name'=>'frmglobalsettings', 'onsubmit'=>'return confirm("Are you sure you want to upgrade to version '.$sUpgradeVersion.'?");'));?>
                <p><input type='hidden' id='subaction' name='subaction' value='scheduleupgrade' /><input type='submit' value='Upgrade to <?php echo $sUpgradeVersion; ?>!'/>
            </form><p>
            <?php 
        }
    }
    else
    { ?>
        <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;">
            <span style="font-weight: bold; font-size: 14pt;">Upgrade to <?php echo $sUpgradeVersion; ?> is in progress!</span><br /><br />
            Your LimeService installation is already scheduled for upgrade. Upgrade cycles run on every full hour and can take up to 15 minutes - please be patient. The current server time is '<?php echo date('g:i T');?>.
            This action cannot be undone. <br /><b>Please be patient. When the upgrade has finished you will receive an automatic email.</b><p /></div>
        <?php
    } 

    // ============ Update LimeService End=======================================================
    ?>

