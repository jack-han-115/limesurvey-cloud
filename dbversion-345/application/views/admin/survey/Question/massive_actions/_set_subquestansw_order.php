<?php
/**
 * Set subquestion/answer order
 */
$surveyid = (int) App()->request->getParam('surveyid', 0);
?>
<form class="custom-modal-datas form-horizontal" data-trigger-validation="true">
    <div  class="form-group" id="CssClass">
        <label class="col-sm-4 control-label"><?php eT("Random order:"); ?></label>
        <div class="col-sm-8">
            <select class="form-control custom-data attributes-to-update" id="random_order" name="random_order" required>
                <option value="" selected="selected"><?php eT('Please select an option');?></option>
                <option value="0"><?php eT('Off');?></option>
                <option value="1"><?php eT('Randomize on each page load');?></option>
            </select>
        </div>
        <input type="hidden" name="sid" value="<?php echo $surveyid; ?>" class="custom-data"/>
        <input type="hidden" name="aValidQuestionTypes" value="!ABCEFHKLMOPQRWZ1:;" class="custom-data"/>
    </div>
</form>
