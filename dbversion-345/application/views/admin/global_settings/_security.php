<?php

/**
* This view generate the 'security' tab inside global settings.
*
*/

?>
<div class="form-group">

    <label class=" control-label"  for='surveyPreview_require_Auth'><?php eT("Survey preview only for administration users:"); ?></label>
    <div class="">
        <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
            'name' => 'surveyPreview_require_Auth',
            'id' => 'surveyPreview_require_Auth',
            'value' => getGlobalSetting('surveyPreview_require_Auth'),
            'onLabel' => gT('On'),
            'offLabel' => gT('Off')));
                                                                    ?>
    </div>
</div>

<div class="form-group">
    <label class=" control-label"  for='filterxsshtml'><?php eT("Filter HTML for XSS:");
    echo ((Yii::app()->getConfig("demoMode") == true) ? '*' : ''); ?></label>
    <div class="">
        <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
            'name' => 'filterxsshtml',
            'id' => 'filterxsshtml',
            'value' => getGlobalSetting('filterxsshtml'),
            'onLabel' => gT('On'),
            'offLabel' => gT('Off')
            ));
    ?>
    </div>
    <div class="">
        <span class='hint'><?php eT("Note: XSS filtering is always disabled for the superadministrator."); ?></span>
    </div>
</div>

<div class="form-group">
    <label class=" control-label"  for='usercontrolSameGroupPolicy'><?php eT("Group member can only see own group:"); ?></label>
    <div class="">
        <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
            'name' => 'usercontrolSameGroupPolicy',
            'id' => 'usercontrolSameGroupPolicy',
            'value' => getGlobalSetting('usercontrolSameGroupPolicy'),
            'onLabel' => gT('On'),
            'offLabel' => gT('Off')));
                                                                    ?>
    </div>
</div>

<div class="form-group">
    <label class=" control-label"  for="x_frame_options">
    <?php if (Yii::app()->getConfig("demoMode") == true) { ?>
    <span class="text-danger asterisk"></span>
    <?php }; ?>
     <?php eT('IFrame embedding allowed:');
        echo ((Yii::app()->getConfig("demoMode") == true) ? '*' : '');?></label>
    <div class="">
        <?php $this->widget('yiiwheels.widgets.buttongroup.WhButtonGroup', array(
            'name' => 'x_frame_options',
            'value' => getGlobalSetting('x_frame_options'),
            'selectOptions' => array(
                "allow" => gT("Allow", 'unescaped'),
                "sameorigin" => gT("Same origin", 'unescaped')
            )
        ));?>
    </div>
</div>

<?php // LimeService Mod Remove SSL setting ?>

<div class="form-group">
    <label class="control-label" for='loginIpWhitelist'>
        <?php eT("IP whitelist for login:"); ?>
    </label>
    <textarea class="form-control" id='loginIpWhitelist' name='loginIpWhitelist'><?php echo htmlspecialchars(Yii::app()->getConfig('loginIpWhitelist')); ?></textarea>
    <span class='hint'><?php eT("List of IP addresses to exclude from the maximum login attempts check. Separate each IP address with a comma or a new line."); ?></span>
</div>

<div class="form-group">
    <label class="control-label" for='tokenIpWhitelist'>
        <?php eT("IP whitelist for token access:"); ?>
    </label>
    <textarea class="form-control" id='tokenIpWhitelist' name='tokenIpWhitelist'><?php echo htmlspecialchars(Yii::app()->getConfig('tokenIpWhitelist')); ?></textarea>
    <span class='hint'><?php eT("List of IP addresses to exclude from the maximum token validation attempts check. Separate each IP address with a comma or a new line."); ?></span>
</div>

<?php if (Yii::app()->getConfig("demoMode") == true) :?>
    <p><?php eT("Note: Demo mode is activated. Marked (*) settings can't be changed."); ?></p>
<?php endif; ?>
