<?php
    /** This is the survey view, when the survey is hardlocked, and a participant tries to access it. */
?>
 <html>
    <head>
        <title>LimeSurvey | Hardlock Participants</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <body>
    <style>
        body .top-container {
            margin-top: 70px;
        }

        body {
            padding-bottom: 10px;
            padding-top: 60px;/* now is redefine in JS to fit any title length */
            background-color:#ffffff ;
            color: #444444;
        }

        .navbar-default .navbar-nav > li > a:hover {
            color: #444444;
        }

        .question-container {
            background-color: #ffffff;
            border:  1px solid #e6e6e6;
            box-shadow: 0 1px 2px rgba(0,0,0,.2);
        }

        .checkbox-item input[type="checkbox"]:checked + label::after, .checkbox-item input[type="radio"]:checked + label::after {
            content: "\f00c";
        }
            
        .checkbox-item input[type="checkbox"]:checked + label::after{
            animation-name: rubberBand;
            animation-duration: 500ms;
            animation-fill-mode: both;
            animation-iteration-count: 1;
            display: inline-block;
            -webkit-transform: none;
            -ms-transform: none;
            -o-transform: none;
            transform: none;
        }

        .checkbox-item input[type="checkbox"] + label::after{
            display: none;
            -webkit-transform: none;
            -ms-transform: none;
            -o-transform: none;
            transform: none;
        }
            
        .radio-item input[type="radio"]:checked + label::after{
            animation-name: zoomIn;
            animation-duration: 500ms;
            animation-fill-mode: both;
            animation-iteration-count: 1;
            display: inline-block;
            -webkit-transform: none;
            -ms-transform: none;
            -o-transform: none;
            transform: none;
        }

        .radio-item input[type="radio"] + label::after{
            display:none;
            -webkit-transform: none;
            -ms-transform: none;
            -o-transform: none;
            transform: none;
        }
</style>
    <!-- Bootstrap Navigation Bar -->
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed"  data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"   >
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand logo-container hidden-xs">
                <img class="logo img-responsive" src="/tmp/assets/3be5aa7a/logo.png" alt="Dummy Survey 01" />
            </div>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-action-link navbar-right">
                <!-- Load unfinished survey button -->
                <li class=" ls-no-js-hidden " >
                    <a href="#" data-limesurvey-submit='{ "loadall":"loadall" }' class=' ls-link-action ls-link-loadall  animate' >
                        Load unfinished survey
                    </a>
                </li>
            </ul>
        </div>
    </div>
        <div class="container">
            <div class="jumbotron center-block">
                <div class="container">
                    <h1>Attention!</h1>
                    <p>We are sorry but this survey is currently not available - please come back later.</p>
                </div>
            </div>
        </div>
    </body>
    div id="bottomScripts" class="script-container">
                <script type="text/javascript" src="/assets/packages/expressionscript/expression.js"></script>
<script type="text/javascript" src="/assets/packages/embeddables/build/embeddables.min.js"></script>
<script type="text/javascript">
/*<![CDATA[*/

            try{ 
                triggerEmClassChange(); 
            } catch(e) {
                console.ls.warn('triggerEmClassChange could not be run. Is survey.js correctly loaded?');
            }


                if(window.basicThemeScripts === undefined){ 
                    window.basicThemeScripts = new ThemeScripts(); 
                } 
                basicThemeScripts.initGlobal(); 
                
triggerEmRelevance();
jQuery(document).off('pjax:scriptcomplete.mainBottom').on('ready pjax:scriptcomplete.mainBottom', function() {
activateActionLink();
activateConfirmButton();
basicThemeScripts.initTopMenuLanguageChanger('.ls-language-link ', 'form#limesurvey'); 

    $('#limesurvey').append('<input type="hidden" name="ajax" value="off" id="ajax" />');
    

    if(window.basicThemeScripts === undefined){ 
        window.basicThemeScripts = new ThemeScripts(); 
    } 

updateMandatoryErrorClass();
});
jQuery(document).off('pjax:scriptsuccess.debugger').on('pjax:scriptsuccess.debugger',function(e) { console.ls.log('PJAX scriptsuccess', e); });
jQuery(document).off('pjax:scripterror.debugger').on('pjax:scripterror.debugger',function(e) { console.ls.log('PJAX scripterror', e); });
jQuery(document).off('pjax:scripttimeout.debugger').on('pjax:scripttimeout.debugger',function(e) { console.ls.log('PJAX scripttimeout', e); });
jQuery(document).off('pjax:success.debugger').on('pjax:success.debugger',function(e) { console.ls.log('PJAX success', e);});
jQuery(document).off('pjax:error.debugger').on('pjax:error.debugger',function(e) { console.ls.log('PJAX error', e);});
/*]]>*/
</script>

            </div>
           

                    <script>
                window.basicThemeScripts.init();
            </script>
</html>