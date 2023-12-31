<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
    <head>
        <title><?php printf(gT('Editing %s'), $sFieldText); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex, nofollow" />
        <?php
            App()->getClientScript()->registerPackage('jqueryui');
            App()->getClientScript()->registerPackage('jquery-superfish');
            App()->getClientScript()->registerPackage('ckeditor');
            App()->getClientScript()->registerPackage('ckeditoradditions');
            App()->getClientScript()->registerCssFile(Yii::app()->getConfig('publicstyleurl') . 'jquery-ui.css');
        ?>
        <!--<script type="text/javascript" src="<?php echo Yii::app()->getConfig('sCKEditorURL') . '/ckeditor.js'; ?>"></script>-->
    </head>

    <body>
        <?php echo CHtml::form('', 'post', array('onsubmit'=>'saveChanges=true;'));?>

            <script type='text/javascript'>
                <!--
                function closeme()
                {
                    self.close();
                }

                window.onbeforeunload= function (evt) {
                    close_editor();
                    closeme();
                }


                var saveChanges = false;
                var sReplacementFieldTitle = '<?php eT('Placeholder fields','js');?>';
                var sReplacementFieldButton = '<?php eT('Insert/edit placeholder field','js');?>';
                jQuery(document).on('ready pjax:scriptcomplete', function(){
                    //console.log('iGroupId: '+iGroupId);
            // Better use try/catch to not crash JS completely
            /*
                try{ console.log('iGroupId: '+iGroupId); } catch (e){ console.log(e); }
                */
                if($('textarea').length > 0){
                    CKEDITOR.on('instanceReady',CKeditor_OnComplete);
                    var oCKeditor = CKEDITOR.replace( 'MyTextarea' ,  { height	: '350',
                        width	: '98%',
                        toolbarStartupExpanded : true,
                        ToolbarCanCollapse : false,
                        toolbar : '<?php echo $toolbarname; ?>',
                        LimeReplacementFieldsSID : "<?php echo intval($iSurveyId); ?>",
                        LimeReplacementFieldsGID : "<?php echo intval($iGroupId); ?>",
                        LimeReplacementFieldsQID : "<?php echo intval($iQuestionId); ?>",
                        LimeReplacementFieldsType: "<?php echo $sFieldType; ?>",
                        LimeReplacementFieldsAction: "<?php echo $sAction; ?>",
                        LimeReplacementFieldsPath : "<?php echo $this->createUrl("/admin/limereplacementfields/sa/index"); ?>",
                        language : "<?php echo $ckLanguage ?>"
                        <?php echo $htmlformatoption; ?> });
                }
                });

                function CKeditor_OnComplete( evt )
                {
                    var editor = evt.editor;
                    editor.setData(window.opener.document.getElementsByName("<?php echo $sFieldName; ?>")[0].value);
                    editor.execCommand('maximize');
                    window.status='LimeSurvey <?php eT('Editing', 'js') . ' ' . 'javascriptEscape(' . $sFieldText . ', true)'; ?>';
                }

                function html_transfert()
                {
                    var oEditor = CKEDITOR.instances['MyTextarea'];

                    <?php
                    if (in_array($sFieldType, array('editanswer', 'addanswer', 'editlabel', 'addlabel')))
                    {
                    ?>

                    var editedtext = oEditor.getData().replace(new RegExp( "\n", "g" ),'');
                    var editedtext = oEditor.getData().replace(new RegExp( "\r", "g" ),'');

                    <?php
                    }
                    else
                    {
                    ?>

                    var editedtext = oEditor.getData('no strip new line'); // adding a parameter avoids stripping \n

                        <?php
                    }
                    ?>

                    window.opener.document.getElementsByName('<?php echo $sFieldName; ?>')[0].value = editedtext;
                }


                function close_editor()
                {
                    html_transfert();

                    window.opener.document.getElementsByName('<?php echo $sFieldName; ?>')[0].readOnly= false;
                    window.opener.document.getElementById('<?php echo $sControlIdEna; ?>').style.display='';
                    window.opener.document.getElementById('<?php echo $sControlIdDis; ?>').style.display='none';
                    window.opener.focus();
                    return true;
                }

                //-->
                </script>

                <textarea id='MyTextarea' name='MyTextarea'></textarea>
        </form>
    </body>
</html>
