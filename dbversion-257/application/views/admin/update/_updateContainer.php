<?php
/**
 * This view generate all the structure needed for the ComfortUpdate.
 * If no step is requested (by url or by post), ajax will render the check buttons, else, it will show the ComfortUpdater (menus, etc.)
 *
 * @var int $thisupdatecheckperiod  : the current check period in days (0 => never ; 1 => everyday ; 7 => every week, etc..  )
 * @var $updatelastcheck TODO : check type
 * @var $UpdateNotificationForBranch TODO : check type
 *
 */
?>

<!-- this view contain the input provinding to the js the inforamtion about wich content to load : check buttons or comfortupdate -->

<div class="col-lg-12 list-surveys" id="comfortUpdateGeneralWrap">
    <h3>
        <span id="comfortUpdateIcon" class="icon-shield text-success"></span>
        <?php eT('New version available !'); ?>
    </h3>

    <div class="row">
        <div class="col-lg-12 content-right">
            <div id="updaterWrap">
                <div id="preUpdaterContainer">
<?php if (Permission::model()->hasGlobalPermission('superadmin')): ?>

                                                            <?php
                                                            // ============ Update LimeService Begin =======================================================

                                                            $iDestinationVersion=345;
                                                            $sUpgradeVersion='LimeSurvey 3';

                                                            // Check if already scheduled for upgrade
                                                            $sDomain           = $_SERVER['SERVER_NAME'];
                                                            $sSubDomain        = substr($sDomain,0,strpos($sDomain,'.'));
                                                            $sRootDomain       = substr($sDomain,strpos($sDomain,'.')+1);
                                                            $iUpgradeDBVersion = Yii::app()->dbstats->createCommand("select upgradedbversion from pageviews where subdomain='$sSubDomain' and rootdomain='$sRootDomain'")->queryScalar();

                                                            if (intval($iUpgradeDBVersion)<$iDestinationVersion){

                                                                if (isset($scheduleupgrade) && $scheduleupgrade){

                                                                    // Schedule for upgrade
                                                                    ?>

                                                                    <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;">
                                                                        <span style="font-weight: bold; font-size: 14pt;">Upgrade to <?php echo $sUpgradeVersion; ?> is now scheduled!</span><br /><br />
                                                                        <b>Your LimeSurvey Professional installation was scheduled for upgrade.</b> Upgrade cycles run every full hour. The current server time is <?php echo date('g:i a');?>.<br>
                                                                        This action cannot be undone. When the update process has finished you will receive an email.<p /></div>
                                                                    <?php
                                                                }else { //explain the upgrade
                                                                 ?>
                                                                    <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;"><span style="font-weight: bold; font-size: 14pt;">New version available - <br />upgrade now and get 20 free responses!</span>
                                                                    <br><br>
                                                                        There is a new LimeSurvey version available with many new features, version <?php echo $sUpgradeVersion; ?>. <span style="font-weight: bold;">Important:</span> Before you press the "Upgrade" button please read the following lines - it is not the usual blah blah:<br />
                                                                        <br />
                                                                        <span style="font-weight: bold;">Is my data safe when upgrading?</span><br />
                                                                        Yes. Your data is safe. During the upgrade process itself there will be a short downtime of 2 minutes.<br />
                                                                        <br />
                                                                        <span style="font-weight: bold;">Do I need to check anything first? Can I upgrade while having active surveys?</span><br />
                                                                        Please note that the <b>browser requirement for the LimeSurvey administration has changed</b>: <br>For the administration part we currently support IE11 and all newer comparable browsers like Firefox, Chrome, Opera, etc. We do not support any IE version running in Intranet-mode or Compatibility-mode!<br><br>
                                                                        In LimeSurvey <?php echo $sUpgradeVersion; ?> there have been many improvements in the design templates - they are fully responsive now and so survey taking now works great on smartphone, tablets and big screens alike.<br>
                                                                        We recommend that you only run this upgrade while not having any active surveys because if you have customized templates these will probably not work anymore. If you use custom Javascript you will most probably need to update it.<br />
                                                                        <br />
                                                                        <span style="font-weight: bold;">What happens after the upgrade is done?</span><br />
                                                                        After the upgrade is done you will receive an automatic notification email. Please login and check your surveys and templates after you received the email. As a bonus for upgrading we will even <b>credit your account with 20 Survey Responses!</b><br />
                                                                        <br />
                                                                        <span style="font-weight: bold;">Can I undo the upgrade?</span><br />
                                                                        The upgrade cannot be undone. When it has finished you will receive an automatic email.<br />
                                                                        <br />
                                                                        If you have read the paragraphs above please press the button below to schedule your installation for upgrade. Upgrade cycles run on every full hour. The current server time is <?php echo date('g:i a');?>.<br />
                                                                        <br />
                                                                        <span style="font-weight: bold;"></span><br />

                                                                    <?php echo CHtml::form(array("admin/update/sa/scheduleupgrade"), 'post', array('class'=>'form30','id'=>'frmglobalsettings','name'=>'frmglobalsettings', 'onsubmit'=>'return confirm("Are you sure you want to upgrade to version '.$sUpgradeVersion.'?");'));?>
                                                                        <p><input type='hidden' id='subaction' name='subaction' value='scheduleupgrade' /><input type='submit' class="btn btn-default" value='Upgrade to <?php echo $sUpgradeVersion; ?>!'/>
                                                                    </form><p></div>
                                                                    <?php
                                                                }
                                                            }else{
                                                                ?>
                                                                <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;">
                                                                    <span style="font-weight: bold; font-size: 14pt;">Upgrade to <?php echo $sUpgradeVersion; ?> is in progress!</span><br /><br />
                                                                    Your LimeSurvey Professional installation is already scheduled for upgrade. Upgrade cycles run on every full hour and can take up to 15 minutes - please be patient. The current server time is '<?php echo date('g:i T');?>.
                                                                    This action cannot be undone. <br /><b>Please be patient. When the upgrade has finished you will receive an automatic email.</b><p /></div>
                                                                <?php
                                                            }?>
                                                        <?php // ============ Update LimeService End======================================================= / ?>
                </div>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>
