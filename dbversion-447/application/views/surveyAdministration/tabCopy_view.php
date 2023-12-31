<?php
/**
 * Copy survey
 */
?>
<div class="ls-flex-row">
<div class="grow-10 ls-space padding left-10 right-10">
    <div class="row">

        <!-- copy survey form -->
        <?php echo CHtml::form(array('surveyAdministration/copy'), 'post', array('id'=>'copysurveyform', 'name'=>'copysurveyform', 'class'=>'form30 ')); ?>
            <div class="ls-flex-column col-md-4">
                    <!-- Select survey -->
                    <div class="form-group">
                        <label for='copysurveylist' class=" control-label"><?php  eT("Select survey to copy:"); ?> </label>
                        <div class="">
                            <select id='copysurveylist' name='copysurveylist' required="required" class="form-control activate-search">
                                <?php echo getSurveyList(false); ?>
                            </select>
                        </div>
                        <p class="form-control-static">
                            <span class='annotation text-warning'><?php echo  gT("Required"); ?> </span>
                        </p>
                    </div>

                    <!-- New survey title -->
                    <div class="form-group">
                        <label for='copysurveyname' class=" control-label"><?php echo  eT("New survey title:"); ?> </label>
                        <div class="">
                            <input type='text' id='copysurveyname' size='82' maxlength='200' name='copysurveyname' placeholder="<?php eT('Copy original survey title');?>" value='' class="form-control" />
                        </div>
                        <div class="">
                          <p class="form-control-static">
                            <span class='annotation text-warning'><?php echo  gT("Optional"); ?>  </span>
                          </p>
                        </div>
                    </div>

                    <!-- New survey id -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveyid'><?php echo  eT("New survey id:"); ?> </label>
                        <div class="">
                            <input type='number' step="1" min="1" max="999999" id='copysurveyid' size='82' name='copysurveyid' value='' class="form-control" />
                        </div>
                        <div class="help-block">
                            <span class='annotation text-info'><?php echo  gT("Optional"); ?> </span>
                            -
                            <?= gT("If the new survey ID is already used, a random one will be assigned."); ?> </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <!-- Submit -->
                        <div class="text-center">
                            <input type='submit' class='btn btn-primary col-4' value='<?php  eT("Copy survey"); ?>' />
                            <?php if (isset($surveyid)) echo '<input type="hidden" name="sid" value="' . $surveyid . '" />'; ?>
                            <input type='hidden' name='action' value='copysurvey' />
                        </div>
                    </div>
                </div>

                <div class="ls-flex-column col-md-4">
                    <!-- Convert resource links -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveytranslinksfields'><?php echo  eT("Copy survey resource files and adapt links"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveytranslinksfields',
                                'value'=> "1",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

                    <!-- Exclude quotas -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveyexcludequotas'><?php echo  eT("Exclude quotas"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveyexcludequotas',
                                'value'=> "0",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

                    <!-- Exclude survey permissions -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveyexcludepermissions'><?php echo  eT("Exclude survey permissions"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveyexcludepermissions',
                                'value'=> "0",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

                    <!-- Exclude answers -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveyexcludeanswers'><?php echo  eT("Exclude answers"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveyexcludeanswers',
                                'value'=> "0",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

                    <!-- Reset conditions/relevance -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveyresetconditions'><?php echo  eT("Reset conditions"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveyresetconditions',
                                'value'=> "0",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

                    <!-- Reset start/end date/time -->
                    <div class="form-group">
                        <label class=" control-label" for='copysurveyresetstartenddate'><?php echo  eT("Reset start/end date/time"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveyresetstartenddate',
                                'value'=> "0",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class=" control-label" for='copysurveyresetresponsestartid'><?php echo  eT("Reset response start ID"); ?> </label>
                        <div class="">
                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                'name' => 'copysurveyresetresponsestartid',
                                'value'=> "0",
                                'onLabel'=>gT('On'),
                                'offLabel'=>gT('Off')
                                ));
                            ?>
                        </div>
                    </div>

            </div>
        </form>
    </div>
</div>
</div>

<script>
    $(document).on('ready pjax:scriptcomplete', function(){
        $('#copysurveyform').on('submit',  function(event){
            // Disable both buttons. Normally there's no need to re-enable them. The 'save-form-button' may already be disabled by it's onclick event.
            $('#copysurveyform').find('input[type="submit"]').prop('disabled', true);
            $('#save-form-button').addClass('disabled').attr('onclick', 'return false;');
        });
    });

</script>