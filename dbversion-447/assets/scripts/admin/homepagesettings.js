/**
 * JavaScript functions for HomePage Settings
 */

// Namespace
var LS = LS || {  onDocumentReady: {} };

$(document).on('ready  pjax:scriptcomplete', function(){

    /**
     * Toggle show logo value
     */
    $('#show_logo').on('switchChange.bootstrapSwitch', function(event, state) {
        $url = $('#show_logo-url').attr('data-url');
        $.ajax({
            url : $url,
            type : 'GET',
            dataType : 'html',

            // html contains the buttons
            success : function(html, statut){
            },
            error :  function(html, statut){
                alert('error');
            }
        });
    });

    /**
     * Toggle show last_survey_and_question value
     */
    $('#show_last_survey_and_question').on('switchChange.bootstrapSwitch', function(event, state) {
        $url = $('#show_last_survey_and_question-url').attr('data-url');
        $.ajax({
            url : $url,
            type : 'GET',
            dataType : 'html',

            // html contains the buttons
            success : function(html, statut){
            },
            error :  function(html, statut){
                alert('error');
            }
        });
    });

    /**
     * Toggle show survey list value
     */
    $('#show_survey_list').on('switchChange.bootstrapSwitch', function(event, state) {
        $url = $('#show_survey_list-url').attr('data-url');
        console.ls.log($url);
        $.ajax({
            url : $url,
            type : 'GET',
            dataType : 'html',

            // html contains the buttons
            success : function(html, statut){
            },
            error :  function(html, statut){
                alert('error');
            }
        });
    });

    /**
     * Toggle show survey list search value
     */
    $('#show_survey_list_search').on('switchChange.bootstrapSwitch', function(event, state) {
        $url = $('#show_survey_list_search-url').attr('data-url');
        console.ls.log($url);
        $.ajax({
            url : $url,
            type : 'GET',
            dataType : 'html',

            // html contains the buttons
            success : function(html, statut){
            },
            error :  function(html, statut){
                alert('error');
            }
        });
    });

    /**
     * Toggle wrap boxes in container value
     */
    $('#boxes_in_container').on('switchChange.bootstrapSwitch', function(event, state) {
        $url = $('#boxes_in_container-url').attr('data-url');
        console.ls.log($url);
        $.ajax({
            url : $url,
            type : 'GET',
            dataType : 'html',

            // html contains the buttons
            success : function(html, statut){
            },
            error :  function(html, statut){
                alert('error');
            }
        });
    });

    /**
     * Save box settings
     */
    $('#save_boxes_setting').on('click', function(){
        $url = $(this).attr('data-url');
        $iBoxesByRow = $('#iBoxesByRow').val();
        $iBoxesOffset = $('#iBoxesOffset').val();
        $successMessage = $('#boxesupdatemessage').data('ajaxsuccessmessage');
        $.ajax({
            url : $url+'/boxesbyrow/'+$iBoxesByRow+'/boxesoffset/'+$iBoxesOffset,
            type : 'GET',
            dataType : 'html',
            // html contains the buttons
            success : function(html, statut){
                $('#notif-container').append('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close limebutton" data-dismiss="alert" aria-label="Close"><span>×</span></button>'+$successMessage+'</div>');
            },
            error :  function(html, statut){
                alert('error');
            }
        });
    });

    // Create Update : icons
    if($('.option-icon').length>1){
        $('.option-icon').on('click', function (ev, that) {
            ev.preventDefault()
            var fullIconName = $(ev.currentTarget).attr('data-icon');

            // Set icon preview and hidden input
            $('input[name="Box[ico]"]').val(fullIconName);
            $('#chosen-icon').attr('class', fullIconName + ' text-success');
        });

        // Show current icon
        var currentIcon = $('input[name="Box[ico]"]').val();
        if (currentIcon !== '')
        {
            $('#chosen-icon').attr('class', currentIcon + ' text-success');
        }
    }
});
