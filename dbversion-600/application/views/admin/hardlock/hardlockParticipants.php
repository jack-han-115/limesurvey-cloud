<?php
    /** This is the survey view, when the survey is hardlocked, and a participant tries to access it. */
?>
 <html>
    <head>
        <title>LimeSurvey | Hardlock Participants</title>
        <!-- Hardlock Participants Styles -->
        <link rel="stylesheet" type="text/css" href="./application/views/admin/hardlock/hardlockParticipantsStyles.css">
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
        </style>
        <!-- Navigation Bar -->
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed"  data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="navbar-brand logo-container hidden-xs">
                    <img class="logo img-responsive align-image-for-navigation" src="./assets/images/Logo_LimeSurvey.png" alt="LimeSurvey Logo" />
                </div>
            </div>
        </div>
        <div class="container">
            <div class="panel panel-default align-panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Attention!</h3>
                </div>
                <div class="panel-body">
                    <p>We are sorry but this survey is currently not available - please come back later.</p>
                </div>
            </div>
        </div>
        <!-- Footer Bar -->
        <div class="fixed-hardlock-footer">
            <div class="hidden-xs">
                <img src="./assets/images/__Limesurvey_logo.png" class="align-image-for-footer" alt="LimeSurvey logo with font" />
            </div>
        </div>
    </body>
</html>
