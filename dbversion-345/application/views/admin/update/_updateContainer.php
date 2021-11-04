<?php
/**
 * This view generate all the structure needed for the ComfortUpdate.
 * If no step is requested (by url or by post), ajax will render the check buttons, else, it will show the ComfortUpdater (menus, etc.)
 *
 * @var $this AdminController
 * @var int $thisupdatecheckperiod  : the current check period in days (0 => never ; 1 => everyday ; 7 => every week, etc..  )
 * @var $updatelastcheck TODO : check type
 * @var $UpdateNotificationForBranch TODO : check type
 *
 */

// DO NOT REMOVE This is for automated testing to validate we see that page
echo viewHelper::getViewTestTag('update');
?>

<!-- this view contain the input provinding to the js the inforamtion about wich content to load : check buttons or comfortupdate -->
<?php
    $this->renderPartial("./update/_ajaxVariables");
?>

<div class="col-lg-12 list-surveys" id="comfortUpdateGeneralWrap">
    <h3>
        <span id="comfortUpdateIcon" class="icon-shield text-success"></span>
        <?php eT('ComfortUpdate'); ?>
        <?php if(YII_DEBUG):?>
            <small>Server:<em class="text-warning"> <?php echo Yii::app()->getConfig("comfort_update_server_url");?></em></small>
        <?php endif;?>
    </h3>

    <div class="row">
        <div class="col-lg-12 content-right">
            <div id="updaterWrap">
                <div id="preUpdaterContainer">

                <?php
                    // ============ Update LimeService Begin =======================================================

                    $iDestinationVersion=447;
                    $sUpgradeVersion='5.x';

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
                                <b>Your LimeSurvey Cloud installation was scheduled for upgrade.</b> Upgrade cycles run every full hour. The current server time is <?php echo date('g:i a');?>.<br>
                                This action cannot be undone. When the update process has finished you will receive an email.<p /></div>
                            <?php
                        }else { //explain the upgrade
                            ?>
                            <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;">
                            <br><br>
                            <h4><strong>New version available!<br>Upgrade now to version 5.x and get 25 free responses!</strong></h4>
                            <p style='text-align:left'><a target='_blank' href='https://www.limesurvey.org/blog/763-what-s-new-in-limesurvey'>Read our article about the changes in this release</a> to find out what's new....</p>
                            <p style='text-align:left'>There is a new LimeSurvey version (<?php echo $sUpgradeVersion; ?>) available with many new features! Check out our <a href="https://www.limesurvey.org/blog">latest </a><a href="https://www.limesurvey.org/blog">blog</a> articles for more info. Before you press the button "Upgrade" please read the following text.</p>
                            <p style='text-align:left'><strong>Is my data safe when upgrading?</strong></p>
                            <p style='text-align:left'>Yes, your data is safe. During the upgrade process, there will be a short downtime of two minutes.</p>
                            <p style='text-align:left'><strong>Is there anything that I need to know before upgrading?</strong></p>
                            <p style='text-align:left'>Please note that the browser requirement for LimeSurvey administration has changed. For survey participants we currently support IE11&nbsp;and all newer comparable browsers like Firefox, Chrome, Opera, etc.. For the new administration interface you will need to use a recent version of any popular browser, like Firefox, Chrome, Opera, Edge, etc.</p>
                            <p style='text-align:left'><strong>Can I upgrade while having active surveys?</strong></p>
                            <p style='text-align:left'>While it is possible, we do not recommend upgrading while running active surveys. Upgrading while running active surveys may cause issues with customized templates.</p>
                            <p style='text-align:left'><strong>What happens after the upgrade is done?</strong></p>
                            <p style='text-align:left'>After the upgrade is complete, you will receive a notification email. Upon receipt of the email, please log in and check your surveys and templates.</p>
                            <p style='text-align:left'>As a bonus for upgrading, <strong>we will credit your account with 25 survey responses</strong>!</p>
                            <p style='text-align:left'><strong>Can I undo the upgrade?</strong></p>
                            <p style='text-align:left'>No, the upgrade cannot be undone.</p>
                            <p style='text-align:left'>Please click the button below to schedule your upgrade. Upgrade cycles run every full hour.</p>
                            <br />

                            <?php echo CHtml::form(array("admin/update/sa/scheduleupgrade"), 'post', array('class'=>'form30','id'=>'frmglobalsettings','name'=>'frmglobalsettings', 'onsubmit'=>'return confirm("Are you sure you want to upgrade to version '.$sUpgradeVersion.'?");'));?>
                                <p><input type='hidden' id='subaction' name='subaction' value='scheduleupgrade' /><input type='submit' class="btn btn-default" value='Upgrade to <?php echo $sUpgradeVersion; ?>!'/>
                            </form><p></div>
                            <?php
                        }
                    }else{
                        ?>
                        <div style="width:600px;text-align: left;padding-top:50px; margin:0 auto;">
                            <span style="font-weight: bold; font-size: 14pt;">Upgrade to <?php echo $sUpgradeVersion; ?> is in progress!</span><br /><br />
                            Your LimeSurvey Cloud installation is already scheduled for upgrade. Upgrade cycles run on every full hour and can take up to 15 minutes - please be patient. The current server time is <?php echo date('g:i T');?>.
                            This action cannot be undone. <br /><b>Please be patient. When the upgrade has finished you will receive an automatic email.</b><p /></div>
                        <?php
                    }?>

                
                
                <!-- The check buttons : render by ajax only if no step is required by url or post -->
                <?php // $this->renderPartial("./update/check_updates/_checkButtons", array( "thisupdatecheckperiod"=>$thisupdatecheckperiod, "updatelastcheck"=>$updatelastcheck,"UpdateNotificationForBranch"=>$UpdateNotificationForBranch )); ?>
                <?php /*
                    if( $serverAnswer->result )
                    {
                        unset($serverAnswer->result);
                        $this->renderPartial('./update/check_updates/update_buttons/_updatesavailable', array('updateInfos' => $serverAnswer));
                    }
                    else
                    {
                        // Error : we build the error title and messages
                        $this->renderPartial('./update/check_updates/update_buttons/_updatesavailable_error', array('serverAnswer' => $serverAnswer));
                    }
                ?>
                </div>

                <!-- The updater  -->
                <?php $this->renderPartial("./update/updater/_updater"); */?>
                <?php // ============ Update LimeService End======================================================= / ?>
            </div>
        </div>
    </div>
</div>
