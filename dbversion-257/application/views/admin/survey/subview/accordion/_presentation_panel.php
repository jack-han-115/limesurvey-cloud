<?php
/**
 * Presentation panel
 */
?>

<!-- Presentation panel -->
<div id='presentation' class="tab-pane fade in">

    <!-- Format -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='format'><?php  eT("Format:"); ?></label>
        <div class="col-sm-7">
            <select id='format' name='format' class="form-control" >
                <option value='S'
                    <?php if ($esrow['format'] == "S" || !$esrow['format']) { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Question by Question"); ?>
                </option>
                <option value='G'
                    <?php if ($esrow['format'] == "G") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Group by Group"); ?>
                </option>
                <option value='A'
                    <?php if ($esrow['format'] == "A") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("All in one"); ?>
                </option>
            </select>
        </div>
    </div>

    <!-- Template -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='template'><?php  eT("Template:"); ?></label>
        <div class="col-sm-4">
            <select id='template' class="form-control"  name='template'>
                <?php foreach (array_keys(getTemplateList()) as $tname) {

                        if (Permission::model()->hasGlobalPermission('superadmin','read') || Permission::model()->hasGlobalPermission('templates','read') || hasTemplateManageRights(Yii::app()->session["loginID"], $tname) == 1 || $esrow['template']==htmlspecialchars($tname) ) { ?>
                        <option value='<?php echo $tname; ?>'
                            <?php if ($esrow['template'] && htmlspecialchars($tname) == $esrow['template']) { ?>
                                selected='selected'
                                <?php   } elseif (!$esrow['template'] && $tname == Yii::app()->getConfig('defaulttemplate')) { ?>
                                selected='selected'
                                <?php } ?>
                            ><?php echo $tname; ?></option>
                        <?php }
                } ?>
            </select>
        </div>
        <div class="col-sm-3 template-img">
            <img class="img-responsive" alt='<?php  eT("Template preview image"); ?>' id='preview' src='<?php echo getTemplateURL($esrow['template']); ?>/preview.png' />
        </div>
    </div>

    <!-- welcome screen -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='showwelcome'><?php  eT("Show welcome screen:") ; ?></label>
        <div class="col-sm-7">
            <select id='showwelcome' name='showwelcome' class="form-control">
                <option value='Y'
                    <?php if (!$esrow['showwelcome'] || $esrow['showwelcome'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes") ; ?>
                </option>
                <option value='N'
                    <?php if ($esrow['showwelcome'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No") ; ?>
                </option>
            </select>

        </div>
    </div>

    <!-- Navigation delay -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='navigationdelay'><?php  eT("Navigation delay (seconds):"); ?></label>
        <div class="col-sm-7">
            <input type='text' class="form-control" value="<?php echo $esrow['navigationdelay']; ?>" name='navigationdelay' id='navigationdelay' size='12' maxlength='2' onkeypress="return goodchars(event,'0123456789')" />
        </div>
    </div>

    <!-- Show [<< Prev] button -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='allowprev'><?php  eT("Show [<< Previous] button:"); ?></label>
        <div class="col-sm-7">
            <select id='allowprev' class="form-control" name='allowprev'>
                <option value='Y'
                    <?php if (!isset($esrow['allowprev']) || !$esrow['allowprev'] || $esrow['allowprev'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (isset($esrow['allowprev']) && $esrow['allowprev'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?>
                </option>
            </select>

        </div>
    </div>

    <!-- Show question index -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='questionindex'><?php  eT("Show question index / allow jumping:"); ?></label>
        <div class="col-sm-7">

            <?php
                $data = array(
                    0 => gT('Disabled','unescaped'),
                    1 => gT('Incremental','unescaped'),
                    2 => gT('Full','unescaped')
                );
                echo CHtml::dropDownList('questionindex', $esrow['questionindex'], $data, array(
                    'id' => 'questionindex',
                    'class' => 'form-control'
                ));
            ?>

        </div>
    </div>

    <!-- Keyboard-less operation -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='nokeyboard'><?php  eT("Keyboard-less operation:"); ?></label>
        <div class="col-sm-7">
            <select class="form-control"  id='nokeyboard' name='nokeyboard'>
                <option value='Y'
                    <?php if (!isset($esrow['nokeyboard']) || !$esrow['nokeyboard'] || $esrow['nokeyboard'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (isset($esrow['nokeyboard']) && $esrow['nokeyboard'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?>
                </option>
            </select>

        </div>
    </div>

    <!-- Show progress bar -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='showprogress'><?php  eT("Show progress bar:"); ?></label>
        <div class="col-sm-7">
            <select class="form-control" id='showprogress' name='showprogress'>
                <option value='Y'
                    <?php if (!isset($esrow['showprogress']) || !$esrow['showprogress'] || $esrow['showprogress'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (isset($esrow['showprogress']) && $esrow['showprogress'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?></option>
            </select>
        </div>
    </div>

    <!-- Participants may print answers -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='printanswers'><?php  eT("Participants may print answers:"); ?></label>
        <div class="col-sm-7">
            <select id='printanswers' name='printanswers' class="form-control" >
                <option value='Y'
                    <?php if (!isset($esrow['printanswers']) || !$esrow['printanswers'] || $esrow['printanswers'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (isset($esrow['printanswers']) && $esrow['printanswers'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?>
                </option>
            </select>

        </div>
    </div>

    <!-- Public statistics -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='publicstatistics'><?php  eT("Public statistics:"); ?></label>
        <div class="col-sm-7">
            <select  class="form-control"  id='publicstatistics' name='publicstatistics'>
                <option value='Y'
                    <?php if (!isset($esrow['publicstatistics']) || !$esrow['publicstatistics'] || $esrow['publicstatistics'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (isset($esrow['publicstatistics']) && $esrow['publicstatistics'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?>
                </option>
            </select>
        </div>
    </div>

    <!-- Show graphs in public statistics -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='publicgraphs'><?php  eT("Show graphs in public statistics:"); ?></label>
        <div class="col-sm-7">
            <select id='publicgraphs' name='publicgraphs' class="form-control" >
                <option value='Y'
                    <?php if (!isset($esrow['publicgraphs']) || !$esrow['publicgraphs'] || $esrow['publicgraphs'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (isset($esrow['publicgraphs']) && $esrow['publicgraphs'] == "N") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?></option>
            </select>
        </div>
    </div>

    <!-- Automatically load URL -->
    <div class="form-group">
        <label class="col-sm-5 control-label" for='autoredirect'><?php  eT("Automatically load URL when survey complete:"); ?></label>
        <div class="col-sm-7">
            <select id='autoredirect' name='autoredirect'  class="form-control" >
                <option value='Y'
                    <?php if (isset($esrow['autoredirect']) && $esrow['autoredirect'] == "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("Yes"); ?>
                </option>
                <option value='N'
                    <?php if (!isset($esrow['autoredirect']) || $esrow['autoredirect'] != "Y") { ?>
                        selected='selected'
                        <?php } ?>
                    ><?php  eT("No"); ?>
                </option>
            </select>
        </div>
    </div>

    <!-- showxquestions -->
    <?php switch($showxquestions):
            case 'show':  ?>

                <!-- Show "There are X questions in this survey -->
                <div class="form-group">
                    <label class="col-sm-5 control-label" for="dis_showxquestions"><?php  eT('Show "There are X questions in this survey":'); ?></label>
                    <div class="col-sm-7">
                        <input type="hidden" class="form-control"  name="showxquestions" id="" value="Y" /> <input type="text" name="dis_showxquestions" id="dis_showxquestions" disabled="disabled" value="<?php  eT('Yes (Forced by the system administrator)'); ?>" />
                    </div>
                </div>
        <?php break;?>

        <?php case 'hide': ?>

            <!-- Show "There are X questions in this survey -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showxquestions"><?php  eT('Show "There are X questions in this survey":'); ?></label> <input type="hidden" name="showxquestions" id="" value="N" />
                <div class="col-sm-7">
                     <input type="text" name="dis_showxquestions" id="dis_showxquestions" disabled="disabled" value="<?php  eT('No (Forced by the system administrator)'); ?>" />
                </div>
            </div>
        <?php break;?>

        <?php default: ?>

            <!-- Show "There are X questions in this survey" -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="showxquestions"><?php  eT('Show "There are X questions in this survey":'); ?></label>
                <div class="col-sm-7">

                    <?php $sel_showxq = array( 'Y' => '' , 'N' => '' );
                    if (isset($esrow['showxquestions'])) {
                        $set_showxq = $esrow['showxquestions'];
                        $sel_showxq[$set_showxq] = ' selected="selected"';
                    }
                    if (empty($sel_showxq['Y']) && empty($sel_showxq['N'])) {
                        $sel_showxq['Y'] = ' selected="selected"';
                    }; ?>

                    <select id="showxquestions" name="showxquestions" class="form-control">
                        <option value="Y" <?php echo $sel_showxq['Y']; ?>><?php  eT('Yes'); ?></option>
                        <option value="N" <?php echo $sel_showxq['N']; ?>><?php  eT('No'); ?></option>
                    </select>

                    <?php unset($sel_showxq,$set_showxq); ?>
                </div>
            </div>
        <?php break;?>
    <?php endswitch ?>

    <?php switch($showgroupinfo):
            case 'both': ?>

                <!-- Show group name and/or group description -->
                <div class="form-group">
                    <label class="col-sm-5 control-label" for="dis_showgroupinfo"><?php  eT('Show group name and/or group description:'); ?></label>
                    <div class="col-sm-7">
                        <input type="hidden" name="showgroupinfo" id="showgroupinfo" value="B" />
                        <input class="form-control"  type="text" name="dis_showgroupinfo" id="dis_showgroupinfo" disabled="disabled" value="<?php  eT('Show both (Forced by the system administrator)'); ?>" />
                    </div>
                </div>
        <?php break;?>
        <?php case 'name': ?>

            <!-- Show group name and/or group description -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showgroupinfo"><?php  eT('Show group name and/or group description:'); ?></label>
                <div class="col-sm-7">
                    <input type="hidden" name="showgroupinfo" id="showgroupinfo" value="N" />
                    <input type="text" class="form-control" name="dis_showgroupinfo" id="dis_showgroupinfo" disabled="disabled" value="<?php  eT('Show group name only (Forced by the system administrator)'); ?>" />
                </div>
            </div>
        <?php break;?>

        <?php case 'description': ?>

            <!-- Show group name and/or group description -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showgroupinfo"><?php  eT('Show group name and/or group description:'); ?></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control"  name="dis_showgroupinfo" id="dis_showgroupinfo" disabled="disabled" value="<?php  eT('Show group description only (Forced by the system administrator)'); ?>" />
                    <input type="hidden" name="showgroupinfo" id="showgroupinfo" value="D" />
                </div>
            </div>
        <?php break;?>

        <?php case 'none': ?>

            <!-- Show group name and/or group description -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showgroupinfo"><?php  eT('Show group name and/or group description:'); ?></label>
                <div class="col-sm-7">
                    <input type="hidden" name="showgroupinfo" id="showgroupinfo" value="X" />
                    <input type="text"  class="form-control" name="dis_showgroupinfo" id="dis_showgroupinfo" disabled="disabled" value="<?php  eT('Hide both (Forced by the system administrator)'); ?>" />

                </div>
            </div>
        <?php break;?>

        <?php default: ?>
            <?php
                $sel_showgri = array( 'B' => '' , 'D' => '' , 'N' => '' , 'X' => '' );
                if (isset($esrow['showgroupinfo']))
                {
                    $set_showgri = $esrow['showgroupinfo'];
                    $sel_showgri[$set_showgri] = ' selected="selected"';
                }
                if (empty($sel_showgri['B']) && empty($sel_showgri['D']) && empty($sel_showgri['N']) && empty($sel_showgri['X']))
                    $sel_showgri['C'] = ' selected="selected"';
            ?>

            <!-- Show group name and/or group description -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="showgroupinfo"><?php  eT('Show group name and/or group description:'); ?></label>
                <div class="col-sm-7">
                    <select id="showgroupinfo" name="showgroupinfo"  class="form-control" >
                        <option value="B"<?php echo $sel_showgri['B']; ?>><?php  eT('Show both'); ?></option>
                        <option value="N"<?php echo $sel_showgri['N']; ?>><?php  eT('Show group name only'); ?></option>
                        <option value="D"<?php echo $sel_showgri['D']; ?>><?php  eT('Show group description only'); ?></option>
                        <option value="X"<?php echo $sel_showgri['X']; ?>><?php  eT('Hide both'); ?></option>
                    </select>
                    <?php unset($sel_showgri,$set_showgri); ?>
                </div>
            </div>
        <?php break;?>

    <?php endswitch ?>

    <?php switch($showqnumcode):
            case 'none':  ?>

                <!-- Show question number and/or code -->
                <div class="form-group">
                    <label class="col-sm-5 control-label" for="dis_showqnumcode"><?php  eT('Show question number and/or code:'); ?></label>
                    <div class="col-sm-7">
                        <input type="hidden" name="showqnumcode" id="showqnumcode" value="X" />
                        <input type="text" class="form-control" name="dis_showqnumcode" id="dis_showqnumcode" disabled="disabled" value="<?php  eT('Hide both (Forced by the system administrator)'); ?>" />
                    </div>
                </div>
        <?php break;?>

        <?php case 'number': ?>

            <!-- Show question number and/or code -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showqnumcode"><?php  eT('Show question number and/or code:'); ?></label>
                <div class="col-sm-7">
                    <input type="hidden" name="showqnumcode" id="showqnumcode" value="N" />
                    <input class="form-control" type="text" name="dis_showqnumcode" id="dis_showqnumcode" disabled="disabled" value="<?php  eT('Show question number only (Forced by the system administrator)') ; ?>" />

                </div>
            </div>
        <?php break;?>

        <?php case 'code': ?>

            <!-- Show question number and/or code -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showqnumcode"><?php  eT('Show question number and/or code:'); ?></label>
                <div class="col-sm-7">
                    <input type="hidden" name="showqnumcode" id="showqnumcode" value="C" />
                    <input class="form-control" type="text" name="dis_showqnumcode" id="dis_showqnumcode" disabled="disabled" value="<?php  eT('Show question code only (Forced by the system administrator)'); ?>" />
                </div>
            </div>
        <?php break;?>

        <?php case 'both': ?>

            <!-- Show question number and/or code -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_showqnumcode"><?php  eT('Show question number and/or code:'); ?></label>
                <div class="col-sm-7">
                    <input type="hidden" name="showqnumcode" id="showqnumcode" value="B" />
                    <input class="form-control" type="text" name="dis_showqnumcode" id="dis_showqnumcode" disabled="disabled" value="<?php  eT('Show both (Forced by the system administrator)'); ?>"/>
                </div>
            </div>
        <?php break;?>

        <?php default: ?>
            <?php
                $sel_showqnc = array( 'B' => '' , 'C' => '' , 'N' => '' , 'X' => '' );
                if (isset($esrow['showqnumcode'])) {
                    $set_showqnc = $esrow['showqnumcode'];
                    $sel_showqnc[$set_showqnc] = ' selected="selected"';
                }
                if (empty($sel_showqnc['B']) && empty($sel_showqnc['C']) && empty($sel_showqnc['N']) && empty($sel_showqnc['X'])) {
                    $sel_showqnc['X'] = ' selected="selected"';
                };
            ?>

            <!-- Show question number and/or code -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="showqnumcode"><?php  eT('Show question number and/or code:'); ?></label>
                <div class="col-sm-7">
                    <select class="form-control" id="showqnumcode" name="showqnumcode">
                        <option value="B"<?php echo $sel_showqnc['B']; ?>><?php  eT('Show both'); ?></option>
                        <option value="N"<?php echo $sel_showqnc['N']; ?>><?php  eT('Show question number only'); ?></option>
                        <option value="C"<?php echo $sel_showqnc['C']; ?>><?php  eT('Show question code only'); ?></option>
                        <option value="X"<?php echo $sel_showqnc['X']; ?>><?php  eT('Hide both'); ?></option>
                    </select>
                    <?php unset($sel_showqnc,$set_showqnc);?>
                </div>
            </div>
        <?php break;?>
    <?php endswitch; ?>

    <?php switch($shownoanswer):
            case 0:  ?>

                <!-- Show "No answer" -->
                <div class="form-group">
                    <label class="col-sm-5 control-label" for="dis_shownoanswer"><?php  eT('Show "No answer":'); ?></label> <input type="hidden" name="shownoanswer" id="shownoanswer" value="N" />
                    <div class="col-sm-7">
                        <input class="form-control" type="text" name="dis_shownoanswer" id="dis_shownoanswer" disabled="disabled" value="<?php  eT('Off (Forced by the system administrator)'); ?>" />
                    </div>
                </div>
        <?php break;?>

        <?php case 2: ?>
            <?php
                $sel_showno = array( 'Y' => '' , 'N' => '' );
                if (isset($esrow['shownoanswer'])) {
                    $set_showno = $esrow['shownoanswer'];
                    $sel_showno[$set_showno] = ' selected="selected"';
                };
                if (empty($sel_showno)) {
                    $sel_showno['Y'] = ' selected="selected"';
                };
            ?>

            <!-- Show "No answer" -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="shownoanswer"><?php  eT('Show "No answer":'); ?></label>
                <div class="col-sm-7">
                    <select class="form-control" id="shownoanswer" name="shownoanswer">
                        <option value="Y"<?php echo $sel_showno['Y']; ?>><?php  eT('Yes'); ?></option>
                        <option value="N"<?php echo $sel_showno['N']; ?>><?php  eT('No'); ?></option>
                    </select>

                </div>
            </div>
        <?php break;?>

        <?php default: ?>

            <!-- Show "No answer" -->
            <div class="form-group">
                <label class="col-sm-5 control-label" for="dis_shownoanswer"><?php  eT('Show "No answer":'); ?></label>
                <div class="col-sm-7">
                      <input type="hidden" name="shownoanswer" id="shownoanswer" value="Y" />
                       <input class="form-control" type="text" name="dis_shownoanswer" id="dis_shownoanswer" disabled="disabled" value="<?php  eT('On (Forced by the system administrator)'); ?>" />
                </div>
            </div>
        <?php break;?>

    <?php endswitch ?>
    </div>
