<?php

/**
 * @var $aTabTitles
 * @var $aTabContents
 * @var $has_permissions
 * @var $surveyid
 * @var $surveyls_language
 */
if (isset($data)) {
    extract($data);
}
$count = 0;
if (isset($scripts)) {
    echo $scripts;
}


$iSurveyID                                = Yii::app()->request->getParam('surveyid');
Yii::app()->session['FileManagerContext'] = "edit:survey:{$iSurveyID}";
initKcfinder();

PrepareEditorScript(false, $this);

$optionsOnOff = array(
    1 => gT('On','unescaped'),
    0 => gT('Off','unescaped'),
);
?>
<!-- security notice -->
<div class="mb-3">
            <label class="form-label" for='showsurveypolicynotice'><?php  eT("Show privacy policy text with mandatory checkbox:") ; ?></label>
    <div class="">
                <div class="btn-group" data-bs-toggle="buttons">
                    <input class="btn-check" type="radio" id="showsurveypolicynotice_0" name="showsurveypolicynotice" value="0" <?=$oSurvey->showsurveypolicynotice==0 ? 'checked' : ''?> autocomplete="off">
                    <label for="showsurveypolicynotice_0" class="btn btn-outline-secondary">
                        <?=gT("Don't show");?>
            </label>
                    <input class="btn-check" type="radio" id="showsurveypolicynotice_1" name="showsurveypolicynotice" value="1" <?=$oSurvey->showsurveypolicynotice==1 ? 'checked' : ''?> autocomplete="off">
                    <label for="showsurveypolicynotice_1" class="btn btn-outline-secondary">
                        <?=gT("Inline text");?>
            </label>
                    <input class="btn-check" type="radio" id="showsurveypolicynotice_2" name="showsurveypolicynotice" value="2" <?=$oSurvey->showsurveypolicynotice==2 ? 'checked' : ''?> autocomplete="off">
                    <label for="showsurveypolicynotice_2" class="btn btn-outline-secondary">
                        <?=gT("Collapsible text");?>
            </label>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6">
        <label class="control-label" for='showdatapolicybutton'><?php eT("Show data policy in survey:"); ?></label>
        <div>
            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
                'name'          => 'showdatapolicybutton',
                'checkedOption' => isset($oSurvey->showdatapolicybutton) ? $oSurvey->showdatapolicybutton : 0,
                'selectOptions' => $optionsOnOff,
                'htmlOptions'   => [
                    'class'        => 'custom-data bootstrap-switch-boolean',
                    'uncheckValue' => false,
                ]
            ]); ?>
        </div>
    </div>
    <div class="col-sm-6">
        <label class="control-label" for='showlegalnoticebutton'><?php eT("Show legal notice in survey:"); ?></label>
        <div>
            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
                'name'          => 'showlegalnoticebutton',
                'checkedOption' => isset($oSurvey->showlegalnoticebutton) ? $oSurvey->showlegalnoticebutton : 0,
                'selectOptions' => $optionsOnOff,
                'htmlOptions'   => [
                    'class'        => 'custom-data bootstrap-switch-boolean',
                    'uncheckValue' => false,
                ]
            ]); ?>
        </div>
    </div>
</div>

<nav>
    <div class="nav nav-tabs" id="edit-survey-datasecurity-element-language-selection">
        <?php foreach ($aTabTitles as $i => $eachtitle): ?>
            <button class="nav-link <?php if ($count == 0) {
                echo "active";
            } ?>" data-bs-toggle="tab" data-bs-target="#editdatasecele-<?php echo $count;
            $count++; ?>" type="button">
                <?php echo $eachtitle; ?>
            </button>
        <?php endforeach; ?>
    </div>
    <div class="tab-content">
        <?php foreach ($aTabContents as $i => $sTabContent): ?>
            <?php
            echo $sTabContent;
            ?>
        <?php endforeach; ?>
    </div>
</nav>

<?php App()->getClientScript()->registerScript("EditSurveyDataSecurityTabs",
    "
$('#edit-survey-text-element-language-selection').find('a').on('shown.bs.tab', function(e){
    try{ $(e.relatedTarget).find('textarea').ckeditor(); } catch(e){ }
})",
    LSYii_ClientScript::POS_POSTSCRIPT
); ?>
