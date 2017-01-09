<?php
/**
 * This view generate the 'general' tab inside global settings.
 * 
 */
?>

<ul>
    <li><label for='sitename'><?php eT("Site name:"); echo ((Yii::app()->getConfig("demoMode")==true)?'*':''); ?></label>
        <input type='text' size='50' id='sitename' name='sitename' value="<?php echo htmlspecialchars(getGlobalSetting('sitename')); ?>" /></li>
    <?php

        $thisdefaulttemplate=getGlobalSetting('defaulttemplate');
        $templatenames=array_keys(getTemplateList());

    ?>

    <li><label for="defaulttemplate"><?php eT("Default template:"); echo ((Yii::app()->getConfig("demoMode")==true)?'*':''); 
    
    ?></label>
        <select name="defaulttemplate" id="defaulttemplate">
            <?php
                foreach ($templatenames as $templatename)
                {
                    echo "<option value='$templatename'";
                    if ($thisdefaulttemplate==$templatename) { echo " selected='selected' ";}
                    echo ">$templatename</option>";
                }
            ?>
        </select>
    </li>
    <?php

        $thisadmintheme=getGlobalSetting('admintheme');
        $adminthemes=array_keys(getAdminThemeList());

    ?>
    <li><label for="admintheme"><?php eT("Administration template:"); ?></label>
        <select name="admintheme" id="admintheme">
            <?php
                foreach ($adminthemes as $templatename)
                {
                    echo "<option value='{$templatename}'";
                    if ($thisadmintheme==$templatename) { echo " selected='selected' ";}
                    echo ">{$templatename}</option>";
                }
            ?>
        </select>
    </li>


    <?php $thisdefaulthtmleditormode=getGlobalSetting('defaulthtmleditormode'); ?>
    <li><label for='defaulthtmleditormode'><?php eT("Default HTML editor mode:"); echo ((Yii::app()->getConfig("demoMode")==true)?'*':''); ?></label>
        <select name='defaulthtmleditormode' id='defaulthtmleditormode'>
            <option value='none'
                <?php if ($thisdefaulthtmleditormode=='none') { echo "selected='selected'";} ?>
                ><?php eT("No HTML editor"); ?></option>
            <option value='inline'
                <?php if ($thisdefaulthtmleditormode=='inline') { echo "selected='selected'";} ?>
                ><?php eT("Inline HTML editor (default)"); ?></option>
            <option value='popup'
                <?php if ($thisdefaulthtmleditormode=='popup') { echo "selected='selected'";} ?>
                ><?php eT("Popup HTML editor"); ?></option>
        </select></li>
    <?php $thisdefaultquestionselectormode=getGlobalSetting('defaultquestionselectormode'); ?>
    <li><label for='defaultquestionselectormode'><?php eT("Question type selector:"); echo((Yii::app()->getConfig("demoMode")==true)?'*':''); ?></label>
        <select name='defaultquestionselectormode' id='defaultquestionselectormode'>
            <option value='default'
                <?php if ($thisdefaultquestionselectormode=='default') { echo "selected='selected'";} ?>
                ><?php eT("Full selector (default)"); ?></option>
            <option value='none'
                <?php if ($thisdefaultquestionselectormode=='none') { echo "selected='selected'";} ?>
                ><?php eT("Simple selector"); ?></option>
        </select></li>
    <?php $thisdefaulttemplateeditormode=getGlobalSetting('defaulttemplateeditormode'); ?>
    <li><label for='defaulttemplateeditormode'><?php eT("Template editor:"); echo ((Yii::app()->getConfig("demoMode")==true)?'*':''); ?></label>
        <select name='defaulttemplateeditormode' id='defaulttemplateeditormode'>
            <option value='default'
                <?php if ($thisdefaulttemplateeditormode=='default') { echo "selected='selected'";} ?>
                ><?php eT("Full template editor (default)"); ?></option>
            <option value='none'
                <?php if ($thisdefaulttemplateeditormode=='none') { echo "selected='selected'";} ?>
                ><?php eT("Simple template editor"); ?></option>
        </select></li>
    <?php $dateformatdata=getDateFormatData(Yii::app()->session['dateformat']); ?>
    <li><label for='timeadjust'><?php eT("Time difference (in hours):"); ?></label>
        <span><input type='text' size='10' id='timeadjust' name='timeadjust' value="<?php echo htmlspecialchars(str_replace(array('+',' hours',' minutes'),array('','',''),getGlobalSetting('timeadjust'))/60); ?>" />
            <?php echo gT("Server time:").' '.convertDateTimeFormat(date('Y-m-d H:i:s'),'Y-m-d H:i:s',$dateformatdata['phpdate'].' H:i')." - ". gT("Corrected time:").' '.convertDateTimeFormat(dateShift(date("Y-m-d H:i:s"), 'Y-m-d H:i:s', getGlobalSetting('timeadjust')),'Y-m-d H:i:s',$dateformatdata['phpdate'].' H:i'); ?>
        </span></li>

    <li <?php if( ! isset(Yii::app()->session->connectionID)) echo 'style="display: none"';?>><label for='iSessionExpirationTime'><?php eT("Session lifetime for surveys (seconds):"); ?></label>
        <input type='text' size='10' id='iSessionExpirationTime' name='iSessionExpirationTime' value="<?php echo htmlspecialchars(getGlobalSetting('iSessionExpirationTime')); ?>" /></li>
    <li><label for='ipInfoDbAPIKey'><?php eT("IP Info DB API Key:"); ?></label>
        <input type='text' size='35' id='ipInfoDbAPIKey' name='ipInfoDbAPIKey' value="<?php echo htmlspecialchars(getGlobalSetting('ipInfoDbAPIKey')); ?>" /></li>
    <li><label for='googleMapsAPIKey'><?php eT("Google Maps API key:"); ?></label>
        <input type='text' size='35' id='googleMapsAPIKey' name='googleMapsAPIKey' value="<?php echo htmlspecialchars(getGlobalSetting('googleMapsAPIKey')); ?>" /></li>
    <li><label for='googleanalyticsapikey'><?php eT("Google Analytics API key:"); ?></label>
        <input type='text' size='35' id='googleanalyticsapikey' name='googleanalyticsapikey' value="<?php echo htmlspecialchars(getGlobalSetting('googleanalyticsapikey')); ?>" /></li>
    <li><label for='googletranslateapikey'><?php eT("Google Translate API key:"); ?></label>
        <input type='text' size='35' id='googletranslateapikey' name='googletranslateapikey' value="<?php echo htmlspecialchars(getGlobalSetting('googletranslateapikey')); ?>" /></li>
</ul>

<p><br/><input type='button' onclick='$("#frmglobalsettings").submit();' class='standardbtn' value='<?php eT("Save settings"); ?>' /><br /></p>
<?php if (Yii::app()->getConfig("demoMode")==true):?>
    <p><?php eT("Note: Demo mode is activated. Marked (*) settings can't be changed."); ?></p>
<?php endif;
        
        
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
        