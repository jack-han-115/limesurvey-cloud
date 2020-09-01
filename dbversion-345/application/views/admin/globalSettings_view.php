<?php
/**
 * @var $tgis AdminController
 */

// DO NOT REMOVE This is for automated testing to validate we see that page
echo viewHelper::getViewTestTag('globalsettings');

App()->getClientScript()->registerPackage('jquery-selectboxes');
App()->getClientScript()->registerScript('GlobalSettingsBSSwitcher', "
LS.renderBootstrapSwitch();
", LSYii_ClientScript::POS_POSTSCRIPT);

// LimeService Mod start
if (isset($scripts)) {
    echo $scripts;
}
// LimeService Mod end
?>
<script type="text/javascript">
    var msgCantRemoveDefaultLanguage = '<?php eT("You can't remove the default language.",'js'); ?>';
</script>

<div class="container-fluid welcome full-page-wrapper">
<div class="pagetitle h3"><?php eT("Global settings"); ?></div>

<ul class="nav nav-tabs" id="settingTabs">
        <li role="presentation" class="active"><a role="tab" data-toggle="tab" href='#overview'><?php eT("Overview"); ?></a></li>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#general'><?php eT("General"); ?></a></li>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#email'><?php eT("Email settings"); ?></a></li>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#bounce'><?php eT("Bounce settings"); ?></a></li>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#security'><?php eT("Security"); ?></a></li>
        <?php // LimeService Mod start ?>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#useradmin'><?php eT("User administration"); ?></a></li>
        <?php // LimeService Mod end ?>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#presentation'><?php eT("Presentation"); ?></a></li>
        <?php // LimeService Mod start ?>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#datapolicy_legalnotice'><?php eT("Data policy"); ?></a></li>
        <?php // LimeService Mod end ?>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#language'><?php eT("Language"); ?></a></li>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#interfaces'><?php eT("Interfaces"); ?></a></li>
        <li role="presentation" ><a role="tab" data-toggle="tab" href='#storage'><?php eT("Storage"); ?></a></li>
</ul>

<?php echo CHtml::form(array("admin/globalsettings"), 'post', array('class'=>'','id'=>'frmglobalsettings','name'=>'frmglobalsettings'));?>
<div class="tab-content">
    <div id="overview" class="tab-pane  in active col-md-6 col-md-offset-1">
            <?php $this->renderPartial("./global_settings/_overview", array(
                'usercount'=>$usercount,
                'surveycount'=>$surveycount,
                'activesurveycount'=>$activesurveycount,
                'deactivatedsurveys'=>$deactivatedsurveys,
                'activetokens'=>$activetokens,
                'deactivatedtokens'=>$deactivatedtokens)
            ); ?>
    </div>

    <div id="general" class="tab-pane col-md-10 col-md-offset-1">
            <?php $this->renderPartial("./global_settings/_general", array(
                'aListOfThemeObjects' => $aListOfThemeObjects,
                'aEncodings' => $aEncodings,
                'thischaracterset' => $thischaracterset,
                'sideMenuBehaviour' => $sideMenuBehaviour)
            ); ?>
    </div>

    <div id="email" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_email"); ?>
    </div>

    <div id="bounce" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_bounce"); ?>
    </div>

    <div id="security" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_security"); ?>
    </div>

    <?php // LimeService Mod start ?>
    <div id="useradmin" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_useradministration", [
                'sSendAdminCreationEmail'       => $sGlobalSendAdminCreationEmail,
                'sAdminCreationEmailSubject'    => $sGlobalAdminCreationEmailSubject,   
                'sAdminCreationEmailTemplate'   => $sGlobalAdminCreationEmailTemplate,     
            ]);
        ?>
    </div>
    <?php // LimeService Mod end ?>

    <div id="presentation" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_presentation"); ?>
    </div>

    <?php // LimeService Mod start ?>
    <div id="datapolicy_legalnotice" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_datapolicy", [
            'sLanguage' => $sCurrentDefaultLanguage,
            'sLegalNotice' => $sGlobalLegalNotice,
            'sDataPolicy' => $sGlobalDataPolicy,
            'sShowGlobalLegalNoticeButton' => $sShowGlobalLegalNoticeButton,
            'sShowGlobalDataPolicyButton' => $sShowGlobalDataPolicyButton
        ]);
        ?>
    </div>
    <?php // LimeService Mod end ?>

    <div id="language" class="tab-pane col-md-10 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_language", array(
            'restrictToLanguages'=>$restrictToLanguages,
            'allLanguages'=>$allLanguages,
            'excludedLanguages'=>$excludedLanguages));
        ?>
    </div>

    <div id="interfaces" class="tab-pane col-md-6 col-md-offset-1">
        <?php $this->renderPartial("./global_settings/_interfaces"); ?>
    </div>

    <div id="storage" class="tab-pane col-md-6 col-md-offset-1">
        <?php
            $this->renderPartial("./global_settings/_storage");
        ?>
    </div>
</div>
    <input type='hidden' name='restrictToLanguages' id='restrictToLanguages' value='<?php implode(' ',$restrictToLanguages); ?>'/>
    <input type='hidden' name='action' value='globalsettingssave'/>
    <input type='submit' class="hidden"/>
</form>
</div>
