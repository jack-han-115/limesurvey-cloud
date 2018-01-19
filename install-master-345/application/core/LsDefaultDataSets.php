<?php
/**
 * A collection of default data sets, like surveymenus, surveymenuentries, and tutorials
 */
class LsDefaultDataSets {

    public static function getSurveyMenuEntryData(){
        $headerArray = ['menu_id','user_id','ordering','name','title','menu_title','menu_description','menu_icon','menu_icon_type','menu_class','menu_link','action','template','partial','classes','permission','permission_grade','data','getdatamethod','language','active','changed_at','changed_by','created_at','created_by'];
        $basicMenues = [
            [1,NULL,1,'overview','Survey overview','Overview','Open general survey overview and quick action','list','fontawesome','','admin/survey/sa/view','','','','','','','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,2,'generalsettings','General survey settings','General settings','Open general survey settings','gears','fontawesome','','','updatesurveylocalesettings_generalsettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_generaloptions_panel','','surveysettings','read',NULL,'_generalTabEditSurvey','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,3,'surveytexts','Survey text elements','Text elements','Survey text elements','file-text-o','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/tab_edit_view','','surveylocale','read',NULL,'_getTextEditData','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,4,'theme_options','Theme options','Theme options','Edit theme options for this survey','paint-brush','fontawesome','','admin/themeoptions/sa/updatesurvey','','','','','themes','read','{"render": {"link": { "pjaxed": true, "data": {"surveyid": ["survey","sid"], "gsid":["survey","gsid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,5,'participants','Survey participants','Survey participants','Go to survey participant and token settings','user','fontawesome','','admin/tokens/sa/index/','','','','','surveysettings','update','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,6,'presentation','Presentation &amp; navigation settings','Presentation','Edit presentation and navigation settings','eye-slash','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_presentation_panel','','surveylocale','read',NULL,'_tabPresentationNavigation','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,7,'publication','Publication and access control settings','Publication &amp; access','Edit settings for publicationa and access control','key','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_publication_panel','','surveylocale','read',NULL,'_tabPublicationAccess','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,8,'surveypermissions','Edit surveypermissions','Survey permissions','Edit permissions for this survey','lock','fontawesome','','admin/surveypermission/sa/view/','','','','','surveysecurity','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,9,'tokens','Survey participant settings','Participant settings','Set additional options for survey participants','users','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_tokens_panel','','surveylocale','read',NULL,'_tabTokens','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,10,'quotas','Edit quotas','Quotas','Edit quotas for this survey.','tasks','fontawesome','','admin/quotas/sa/index/','','','','','quotas','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,11,'assessments','Edit assessments','Assessments','Edit and look at the assessements for this survey.','comment-o','fontawesome','','admin/assessments/sa/index/','','','','','assessments','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,12,'notification','Notification and data management settings','Data management','Edit settings for notification and data management','feed','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_notification_panel','','surveylocale','read',NULL,'_tabNotificationDataManagement','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,13,'emailtemplates','Email templates','Email templates','Edit the templates for invitation, reminder and registration emails','envelope-square','fontawesome','','admin/emailtemplates/sa/index/','','','','','assessments','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,14,'panelintegration','Edit survey panel integration','Panel integration','Define panel integrations for your survey','link','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_integration_panel','','surveylocale','read','{"render": {"link": { "pjaxed": false}}}','_tabPanelIntegration','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [1,NULL,15,'resources','Add/Edit resources to the survey','Resources','Add/Edit resources to the survey','file','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_resources_panel','','surveylocale','read',NULL,'_tabResourceManagement','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,1,'activateSurvey','Activate survey','Activate survey','Activate survey','play','fontawesome','','admin/survey/sa/activate','','','','','surveyactivation','update','{"render": {"isActive": false, "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,2,'deactivateSurvey','Stop this survey','Stop this survey','Stop this survey','stop','fontawesome','','admin/survey/sa/deactivate','','','','','surveyactivation','update','{"render": {"isActive": true, "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,3,'testSurvey','Go to survey','Go to survey','Go to survey','cog','fontawesome','','survey/index/','','','','','','','{"render": {"link": {"external": true, "data": {"sid": ["survey","sid"], "newtest": "Y", "lang": ["survey","language"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,4,'listQuestions','List questions','List questions','List questions','list','fontawesome','','admin/survey/sa/listquestions','','','','','surveycontent','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,5,'listQuestionGroups','List question groups','List question groups','List question groups','th-list','fontawesome','','admin/survey/sa/listquestiongroups','','','','','surveycontent','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,6,'generalsettings_collapsed','General survey settings','General settings','Open general survey settings','gears','fontawesome','','','updatesurveylocalesettings_generalsettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_generaloptions_panel','','surveysettings','read',NULL,'_generalTabEditSurvey','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,7,'surveypermissions_collapsed','Edit surveypermissions','Survey permissions','Edit permissions for this survey','lock','fontawesome','','admin/surveypermission/sa/view/','','','','','surveysecurity','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,8,'quotas_collapsed','Edit quotas','Survey quotas','Edit quotas for this survey.','tasks','fontawesome','','admin/quotas/sa/index/','','','','','quotas','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,9,'assessments_collapsed','Edit assessments','Assessments','Edit and look at the assessements for this survey.','comment-o','fontawesome','','admin/assessments/sa/index/','','','','','assessments','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,10,'emailtemplates_collapsed','Email templates','Email templates','Edit the templates for invitation, reminder and registration emails','envelope-square','fontawesome','','admin/emailtemplates/sa/index/','','','','','surveylocale','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,11,'surveyLogicFile','Survey logic file','Survey logic file','Survey logic file','sitemap','fontawesome','','admin/expressions/sa/survey_logic_file/','','','','','surveycontent','read','{"render": { "link": {"data": {"sid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,12,'tokens_collapsed','Survey participant settings','Participant settings','Set additional options for survey participants','user','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_tokens_panel','','surveylocale','read','{"render": { "link": {"data": {"surveyid": ["survey","sid"]}}}}','_tabTokens','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,13,'cpdb','Central participant database','Central participant database','Central participant database','users','fontawesome','','admin/participants/sa/displayParticipants','','','','','tokens','read','{"render": {"link": {}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,14,'responses','Responses','Responses','Responses','icon-browse','iconclass','','admin/responses/sa/browse/','','','','','responses','read','{"render": {"isActive": true, "link": {"data": {"surveyid": ["survey", "sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,15,'statistics','Statistics','Statistics','Statistics','bar-chart','fontawesome','','admin/statistics/sa/index/','','','','','statistics','read','{"render": {"isActive": true, "link": {"data": {"surveyid": ["survey", "sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [2,NULL,16,'reorder','Reorder questions/question groups','Reorder questions/question groups','Reorder questions/question groups','icon-organize','iconclass','','admin/survey/sa/organize/','','','','','surveycontent','update','{"render": {"isActive": false, "link": {"data": {"surveyid": ["survey","sid"]}}}}','','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
            [3,NULL,16,'plugins','Simple plugin settings', 'Simple plugins', 'Edit simple plugin settings','plug','fontawesome','','','updatesurveylocalesettings','editLocalSettings_main_view','/admin/survey/subview/accordion/_plugins_panel','','surveysettings','read','{"render": {"link": {"data": {"surveyid": ["survey","sid"]}}}}','_pluginTabSurvey','en-GB',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0],
        ];
        $returnArray = [];
        
        foreach($basicMenues as $basicMenu){
            $returnArray[] = array_combine($headerArray, $basicMenu);
        }

        return $returnArray;
    }

    public static function getSurveyMenuData(){
        $headerArray = [
            'parent_id',
            'survey_id',
            'user_id',
            'ordering',
            'level',
            'name',
            'title',
            'position',
            'description',
            'active',
            'changed_at',
            'changed_by',
            'created_at',
            'created_by'
        ];
        $returnArray = [];
        $returnArray[] = array_combine($headerArray, [NULL,NULL,NULL,0,0,'mainmenu','Survey menu','side','Main survey menu',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0]);
        $returnArray[] = array_combine($headerArray, [NULL,NULL,NULL,0,0,'quickmenu','Quick menu','collapsed','Quick menu',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0]);
        $returnArray[] = array_combine($headerArray, [1,NULL,NULL,0,1,'pluginmenu','Plugin menu','side','Plugin menu',1, date('Y-m-d H:i:s'),0,date('Y-m-d H:i:s'),0]);
        
        return $returnArray;
    }

    public static function getBoxesData(){
        
        $returnArray = [];
        $returnArray[] = ['position' => 1, 'url' => 'admin/survey/sa/newsurvey', 'title' => 'Create survey', 'ico' => 'add', 'desc' => 'Create a new survey', 'page' => 'welcome', 'usergroup' => '-2'];
        $returnArray[] = ['position' => 2, 'url' => 'admin/survey/sa/listsurveys', 'title' => 'List surveys', 'ico' => 'list', 'desc' => 'List available surveys', 'page' => 'welcome', 'usergroup' => '-1'];
        $returnArray[] = ['position' => 3, 'url' => 'admin/globalsettings', 'title' => 'Global settings', 'ico' => 'settings', 'desc' => 'Edit global settings', 'page' => 'welcome', 'usergroup' => '-2'];
        $returnArray[] = ['position' => 4, 'url' => 'admin/update', 'title' => 'ComfortUpdate', 'ico' => 'shield', 'desc' => 'Stay safe and up to date', 'page' => 'welcome', 'usergroup' => '-2'];
        $returnArray[] = ['position' => 5, 'url' => 'admin/labels/sa/view', 'title' => 'Label sets', 'ico' => 'label', 'desc' => 'Edit label sets', 'page' => 'welcome', 'usergroup' => '-2'];
        $returnArray[] = ['position' => 6, 'url' => 'admin/themeoptions', 'title' => 'Themes', 'ico' => 'templates', 'desc' => 'Themes', 'page' => 'welcome', 'usergroup' => '-2'];
        
        return $returnArray;
        
    }

    public static function getTemplateConfigurationData(){
        $returnArray = [];
        
        $returnArray[] = [
            'template_name'     =>  'vanilla',
            'sid'               =>  NULL,
            'gsid'              =>  NULL,
            'uid'               =>  NULL,
            'files_css'         => '{"add":["css/ajaxify.css","css/theme.css","css/custom.css"]}',
            'files_js'          =>  '{"add":["scripts/theme.js","scripts/ajaxify.js","scripts/custom.js"]}',
            'files_print_css'   => '{"add":["css/print_theme.css"]}',
            'options'           => '{"ajaxmode":"on","brandlogo":"on","container":"on","brandlogofile":"./files/logo.png","font":"noto"}',
            'cssframework_name' => 'bootstrap',
            'cssframework_css'  => '{}',
            'cssframework_js'   => '',
            'packages_to_load'  => '{"add":["pjax","font-noto"]}',
            'packages_ltr'      => NULL,
            'packages_rtl'      => NULL
        ];
        $returnArray[] = [
            'template_name'     =>  'fruity',
            'sid'               =>  NULL,
            'gsid'              =>  NULL,
            'uid'               =>  NULL,
            'files_css'         => '{"add":["css/ajaxify.css","css/animate.css","css/variations/sea_green.css","css/theme.css","css/custom.css"]}',
            'files_js'          => '{"add":["scripts/theme.js","scripts/ajaxify.js","scripts/custom.js"]}',
            'files_print_css'   => '{"add":["css/print_theme.css"]}',
            'options'           => '{"ajaxmode":"off","brandlogo":"on","brandlogofile":"./files/logo.png","container":"on","backgroundimage":"off","backgroundimagefile":"./files/pattern.png","animatebody":"off","bodyanimation":"fadeInRight","bodyanimationduration":"1.0","animatequestion":"off","questionanimation":"flipInX","questionanimationduration":"1.0","animatealert":"off","alertanimation":"shake","alertanimationduration":"1.0","font":"noto","bodybackgroundcolor":"#ffffff","fontcolor":"#444444","questionbackgroundcolor":"#ffffff","questionborder":"on","questioncontainershadow":"on","checkicon":"f00c","animatecheckbox":"on","checkboxanimation":"rubberBand","checkboxanimationduration":"0.5","animateradio":"on","radioanimation":"zoomIn","radioanimationduration":"0.3"}',
            'cssframework_name' => 'bootstrap',
            'cssframework_css'  => '{}',
            'cssframework_js'   => '',
            'packages_to_load'  => '{"add":["pjax","font-noto","moment"]}',
            'packages_ltr'      => NULL,
            'packages_rtl'      => NULL
        ];
        $returnArray[] = [
            'template_name'     =>  'bootswatch',
            'sid'               =>  NULL,
            'gsid'              =>  NULL,
            'uid'               =>  NULL,
            'files_css'         => '{"add":["css/ajaxify.css","css/theme.css","css/custom.css"]}',
            'files_js'          =>  '{"add":["scripts/theme.js","scripts/ajaxify.js","scripts/custom.js"]}',
            'files_print_css'   => '{"add":["css/print_theme.css"]}',
            'options'           => '{"ajaxmode":"on","brandlogo":"on","container":"on","brandlogofile":"./files/logo.png"}',
            'cssframework_name' => 'bootstrap',
            'cssframework_css'  => '{"replace":[["css/bootstrap.css","css/variations/flatly.min.css"]]}',
            'cssframework_js'   => '',
            'packages_to_load'  => '{"add":["pjax","font-noto"]}',
            'packages_ltr'      => NULL,
            'packages_rtl'      => NULL
        ];

        return $returnArray;
    }
    
    
    public static function getSurveygroupData(){

        $returnArray = [
            [
            'name' => 'default',
            'title' => 'Default Survey Group',
            'template' =>  NULL,
            'description' => 'LimeSurvey core default survey group',
            'sortorder' => 0,
            'owner_uid' => 1,
            'parent_id' => NULL,
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'created_by' => 1
            ]
        ];

        return $returnArray;
    }


    public static function getTemplatesData(){
        $returnArray = [];

        $returnArray[] = [
            'name'          => 'vanilla',
            'folder'        => 'vanilla',
            'title'         => 'Vanilla Theme',
            'creation_date' => date('Y-m-d H:i:s'),
            'author'        =>'Louis Gac',
            'author_email'  => 'louis.gac@limesurvey.org',
            'author_url'    => 'https://www.limesurvey.org/',
            'copyright'     => 'Copyright (C) 2007-2017 The LimeSurvey Project Team\\r\\nAll rights reserved.',
            'license'       => 'License: GNU/GPL License v2 or later, see LICENSE.php\\r\\n\\r\\nLimeSurvey is free software. This version may have been modified pursuant to the GNU General Public License, and as distributed it includes or is derivative of works licensed under the GNU General Public License or other free or open source software licenses. See COPYRIGHT.php for copyright notices and details.',
            'version'       => '3.0',
            'api_version'   => '3.0',
            'view_folder'   => 'views',
            'files_folder'  => 'files',
            'description'   => '<strong>LimeSurvey Bootstrap Vanilla Survey Theme</strong><br>A clean and simple base that can be used by developers to create their own Bootstrap based theme.',
            'last_update'   => NULL,
            'owner_id'      => 1,
            'extends'       => '',
        ];
        $returnArray[] = [
            'name'          => 'fruity',
            'folder'        => 'fruity',
            'title'         => 'Fruity Theme',
            'creation_date' => date('Y-m-d H:i:s'),
            'author'        =>'Louis Gac',
            'author_email'  => 'louis.gac@limesurvey.org',
            'author_url'    => 'https://www.limesurvey.org/',
            'copyright'     => 'Copyright (C) 2007-2017 The LimeSurvey Project Team\\r\\nAll rights reserved.',
            'license'       => 'License: GNU/GPL License v2 or later, see LICENSE.php\\r\\n\\r\\nLimeSurvey is free software. This version may have been modified pursuant to the GNU General Public License, and as distributed it includes or is derivative of works licensed under the GNU General Public License or other free or open source software licenses. See COPYRIGHT.php for copyright notices and details.',
            'version'       => '3.0',
            'api_version'   => '3.0',
            'view_folder'   => 'views',
            'files_folder'  => 'files',
            'description'   => '<strong>LimeSurvey Fruity Theme</strong><br>A fruity theme for a flexible use. This theme offers monochromes variations and many options for easy customizations.',
            'last_update'   => NULL,
            'owner_id'      => 1,
            'extends'       => 'vanilla',
        ];
        $returnArray[] = [
            'name'          => 'bootswatch',
            'folder'        => 'bootswatch',
            'title'         => 'Bootswatch Theme',
            'creation_date' => date('Y-m-d H:i:s'),
            'author'        =>'Louis Gac',
            'author_email'  => 'louis.gac@limesurvey.org',
            'author_url'    => 'https://www.limesurvey.org/',
            'copyright'     => 'Copyright (C) 2007-2017 The LimeSurvey Project Team\\r\\nAll rights reserved.',
            'license'       => 'License: GNU/GPL License v2 or later, see LICENSE.php\\r\\n\\r\\nLimeSurvey is free software. This version may have been modified pursuant to the GNU General Public License, and as distributed it includes or is derivative of works licensed under the GNU General Public License or other free or open source software licenses. See COPYRIGHT.php for copyright notices and details.',
            'version'       => '3.0',
            'api_version'   => '3.0',
            'view_folder'   => 'views',
            'files_folder'  => 'files',
            'description'   => '<strong>LimeSurvey Bootwatch Theme</strong><br>Based on BootsWatch Themes: <a href="https://bootswatch.com/3/"">Visit BootsWatch page</a> ',
            'last_update'   => NULL,
            'owner_id'      => 1,
            'extends'       => 'vanilla',
        ];

        return $returnArray;
    }

    public static function getTutorialData() {
        $returnArray = [];
        
        $returnArray[] = [
            'name' => 'firstStartTour',
            'title' => 'Beginner tour',
            'icon' => 'fa-rocket',
            'description' => 'The first start tour to get your first feeling into LimeSurvey',
            'active' => 1,
            'settings' => json_encode(array(
                'keyboard' => false,
                'orphan' => true,
                'template' => ""
                ."<div class='popover tour lstutorial__template--mainContainer'>" 
                ."<div class='arrow'></div>"
                ."<button class='pull-right ls-space margin top-5 right-5 btn btn-warning btn-sm' data-role='end' data-toggle='tooltip' title='".gT('End tour')."'><i class='fa fa-close'></i></button>"
                ."<h3 class='popover-title lstutorial__template--title'></h3>"
                    ."<div class='popover-content lstutorial__template--content'></div>"
                    ."<div class='popover-navigation lstutorial__template--navigation'>"
                        ."<div class='row'>"
                            ."<div class='btn-group col-xs-12' role='group' aria-label='...'>"
                                ."<button class='btn btn-default col-md-6' data-role='prev'>".gT('Previous')."</button>"
                                ."<button class='btn btn-primary col-md-6' data-role='next'>".gT('Next')."</button>"
                            ."</div>"
                        ."</div>"
                    ."</div>"
                ."</div>",
                'onShown' => "(function(tour){ console.ls.log($('#notif-container').children()); $('#notif-container').children().remove(); })",
                'onEnd' => "(function(tour){window.location.reload();})",
                'endOnOrphan' => true,
            )),
            'permission' => 'survey',
            'permission_grade' => 'create'
        ];
        
        return $returnArray;
    }

    public static function getTutorialEntryData() {
        $returnArray = array(
            array(
                'teid' => 1,
                'ordering' => 1,
                'title' => gT('Welcome to LimeSurvey!'),
                'content' => gT("This tour will help you to easily get a basic understanding of LimeSurvey.")."<br/>"
                    .gT("We would like to help you with a quick tour of the most essential functions and features."),
                'settings' => json_encode(array (
                    'element' => '#lime-logo',
                    'path' => ['/admin/index'],
                    'placement' => 'bottom',
                    'redirect' => true,
                    'onShow' => "(function(tour){ $('#welcomeModal').modal('hide'); })"
                    ))
                ),
            array(
                'teid' => 2,
                'ordering' => 2,
                'title' => gT('The basic functions'),
                'content' => gT("The three top boxes are the most basic functions of LimeSurvey.")."<br/>"
                    .gT("From left to right it should be 'Create survey', 'List surveys' and 'Global settings'. Best we start by creating a survey.")
                    .'<p class="alert bg-warning">'.gT("Click on the 'Create survey' box - or 'Next' in this tutorial").'</p>',
                'settings' => json_encode(array(
                    'element' => '.selector__create_survey',
                    'path' => ['/admin/index'],
                    'reflex' => true,
                    'onShow' => "(function(tour){ $('#welcomeModal').modal('hide'); })",
                    'onNext' => "(function(tour){
                    })",
                ))
            ),
            array(
                'teid' => 3,
                'ordering' => 3,
                'title' => gT('The survey title'),
                'content' => gT("This is the title of your survey.")."<br/>"
                .gT("Your participants will see this title in the browser's title bar and on the welcome screen.")
                ."<p class='bg-warning alert'>".gT("You have to put in at least a title for the survey to be saved.").'</p>',
                'settings' => json_encode(array(
                    'path' => ['/admin/survey/sa/newsurvey'],
                    'element' => '#surveyls_title',
                    'redirect' => true,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 4,
                'ordering' => 4,
                'title' => gT('The survey description'),
                'content' => gT("In this field you may type a short description of your survey.")."<br/>"
                .gT("The text inserted here will be displayed on the welcome screen, which is the first thing that your respondents will see when they access your survey..").' '
                .gT("Describe your survey, but do not ask any question yet."),
                'settings' => json_encode(array(
                    'element' => '#cke_description',
                    'path' => ['/admin/survey/sa/newsurvey'],
                    'placement' => 'top',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 5,
                'ordering' => 5,
                'title' => gT('Create a sample question and question group'),
                'content' => gT("We will be creating a question group and a question in this tutorial. There is need to automatically create it."),
                'settings' => json_encode(array(
                    'element' => '.bootstrap-switch-id-createsample',
                    'path' => ['/admin/survey/sa/newsurvey'],
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 6,
                'ordering' => 6,
                'title' => gT('The welcome message'),
                'content' => gT("This message is shown directly below the survey description on the welcome page. You may leave this blank for now but it is a good way to introduce your participants to the survey."),
                'settings' => json_encode(array(
                    'element' => '#cke_welcome',
                    'placement' => 'top',
                    'path' => ['/admin/survey/sa/newsurvey'],
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 7,
                'ordering' => 7,
                'title' => gT('The end message'),
                'content' => gT("This message is shown at the end of your survey to every participant. It's a great way to say thank you or give some links or hints where to go next."),
                'settings' => json_encode(array(
                    'element' => '#cke_endtext',
                    'path' => ['/admin/survey/sa/newsurvey'],
                    'placement' => 'top',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 8,
                'ordering' => 8,
                'title' => gT('Now save your survey'),
                'content' => gT("You may play around with more settings, but let's save and start adding questions to your survey now. Just click on 'Save'."),
                'settings' => json_encode(array(
                    'element' => '#save-form-button',
                    'path' => ['/admin/survey/sa/newsurvey'],
                    'placement' => 'bottom',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    tour.setCurrentStep(8);
                                    $('#save-form-button').trigger('click');
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 9,
                'ordering' => 9,
                'title' => gT('The sidebar'),
                'content' => gT('This is the sidebar.').'<br/>'
                .gT('All important settings can be reached in this sidebar.').'<br/>'
                .gT('The most important settings of your survey can be reached from this sidebar: the survey settings menu and the survey structure menu.'
                .' You may resize it to fit your screen to easily navigate through the available options.'
                .' If the size of the sidebar is too small, the options get collapsed and the quick-menu is displayed.'
                .' If you wish to work from the quick-menu, either click on the arrow button or drag it to the left.'),
                'settings' => json_encode(array(
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}']],
                    'element' => '#sidebar',
                    'placement' => 'top',
                    'redirect' => false,
                    'prev' => '-1',
                    'onShow' => "(function(tour){
                        $('#adminpanel__sidebar--selectorSettingsButton').trigger('click');
                    })",
                ))
            ),
            array(
                'teid' => 10,
                'ordering' => 10,
                'title' => gT('The settings tab with the survey menu'),
                'content' => gT('If you click on this tab, the survey settings menu will be displayed.'
                .' The most important settings of your survey are accessible from this menu.'). '<br/>'
                .gT('If you want to know more about them, check our manual.'),
                'settings' => json_encode(array(
                    'element' => '#adminpanel__sidebar--selectorSettingsButton',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 11,
                'ordering' => 11,
                'title' => gT('The top bar'),
                'content' => gT('This is the top bar.').'<br/>'
                .gT('This bar will change as you move through the functionalities.'
                .'The current bar corresponds to the "overview" tab. It contains the most important LimeSurvey functionalities such as preview and activate survey.'),
                'settings' => json_encode(array(
                    'element' => '#surveybarid',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 12,
                'ordering' => 12,
                'title' => gT('The survey structure'),
                'content' => gT('This is the structure view of your survey. Here you can see all your question groups and questions.'),
                'settings' => json_encode(array(
                    'element' => '#adminpanel__sidebar--selectorStructureButton',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'redirect' => false,
                    'onShow' => "(function(tour){
                                    $('#adminpanel__sidebar--selectorStructureButton').trigger('click');
                                })",
                ))
            ),
            array(
                'teid' => 13,
                'ordering' => 13,
                'title' => gT("Let's add a question group"),
                'content' => gT("What good would your survey be without questions?").'<br/>'
                .gT('In LimeSurvey a survey is organized in question groups and questions. To begin creating questions, we first need a question group.')
                .'<p class="alert bg-warning">'.gT("Click on the 'Add question group' button").'</p>',
                'settings' => json_encode(array(
                    'element' => '#adminpanel__sidebar--selectorCreateQuestionGroup',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'right',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    document.location.href = $('#adminpanel__sidebar--selectorCreateQuestionGroup').attr('href');
                                    tour.setCurrentStep(13);
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 14,
                'ordering' => 14,
                'title' => gT('Enter a title for your first question group'),
                'content' => gT('The title of the question group is visible to your survey participants (this setting can be changed later and it cannot be empty). '
                .'Question groups are important because they allow the survey administrators to logically group the questions. '
                .'By default, each question group (including its questions) is shown on its own page (this setting can be changed later).'),
                'settings' => json_encode(array(
                    'element' => '#group_name_en',
                    'path' => ['/admin/questiongroups/sa/add', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'redirect' => false,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 15,
                'ordering' => 15,
                'title' => gT('A description for your question group'),
                'content' => gT('This description is also visible to your participants.').'<br/>'
                .gT('You do not need to add a description to your question group, but sometimes it makes sense to add a little extra information for your participants.'),
                'settings' => json_encode(array(
                    'element' => 'label[for=description_en]',
                    'path' => ['/admin/questiongroups/sa/add', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'top',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 16,
                'ordering' => 16,
                'title' => gT('Advanced settings'),
                'content' => gT("For now it's best to leave these additional settings as they are. If you want to know more about randomization and relevance settings, have a look at our manual."),
                'settings' => json_encode(array(
                    'element' => '#randomization_group',
                    'path' => ['/admin/questiongroups/sa/add', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'left',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 17,
                'ordering' => 17,
                'title' => gT('Save and add a new question'),
                'content' => gT("Now when you are finished click on 'Save and add question'.").'<br/>'
                .gT('This will directly add a question to the current question group.')
                .'<p class="alert bg-warning">'.gT("Now click on 'Save and add question'.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#save-and-new-question-button',
                    'path' => ['/admin/questiongroups/sa/add', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    $('#save-and-new-question-button').trigger('click');
                                    tour.setCurrentStep(17);
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 18,
                'ordering' => 18,
                'title' => gT('The title of your question'),
                'content' =>
                gT("This code is normally not shown to your participants, still it is necessary and has to be unique for the survey.").'<br>'
                .gT("This code is also the name of the variable that will be exported to SPSS or Excel.")
                .'<p class="alert bg-warning">'.gT("Please type in a code that consists only of letters and numbers, and doesn't start with a number.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#title',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'top',
                    'redirect' => false,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 19,
                'ordering' => 19,
                'title' => gT('The actual question text'),
                'content' => gT('The content of this box is the actual question text shown to your participants.'
                .' It may be empty, but that is not recommended. You may use all the power of our WYSIWYG editor to make your question shine.'),
                'settings' => json_encode(array(
                    'element' => '#cke_question_en',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'top',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 20,
                'ordering' => 20,
                'title' => gT('An additional help text for your question'),
                'content' => gT('You can add some additional help text to your question. '
                .'If you decide not to offer any additional question hints, then no help text will be displayed to your respondents.'),
                'settings' => json_encode(array(
                    'element' => '#cke_help_en',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'top',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 21,
                'ordering' => 21,
                'title' => gT('Set your question type.'),
                'content' => gT("LimeSurvey offers you a lot of different question types.").'<br/>'
                .gT("As you can see, the preselected question type is the 'Long free text' one. We will use in this example the 'Array' question type.").'<br/>'
                .gT("This type of question allows you to add multiple subquestions and a set of answers.")
                .'<p class="alert bg-warning">'.gT("Please select the 'Array'-type.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#question_type_button',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'left',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 22,
                'ordering' => 22,
                'title' => gT('Now save the created question'),
                'content' => gT('Next, we will create subquestions and answer options.').'<br/>'
                    .gT('Please remember that in order to have a valid code, it must contain only letters and numbers, '
                    .'also please check that it starts with a letter.'),
                'settings' => json_encode(array(
                    'element' => '#save-button',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'left',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    $('#question_type').val('F');
                                    $('#save-button').trigger('click');
                                    tour.setCurrentStep(22);
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 23,
                'ordering' => 23,
                'title' => gT('The question bar'),
                'content' => gT('This is the question bar.').'<br/>'
                    .gT('The most important question-related options are displayed here.').'<br/>'
                    .gT('The availability of options is related to the type of question you previously chose.'),
                'settings' => json_encode(array(
                    'element' => '#questionbarid',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'backdrop' => false,
                    'redirect' => false,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 24,
                'ordering' => 24,
                'title' => gT('Add some subquestions to your question'),
                'content' => gT("The array question is a type that creates a matrix for the participant.").'<br/>'
                    .gT("To fully use it, you have to add subquestions as well as answer options.").'<br/>'
                    .gT("Let's start with subquestions.")
                    .'<p class="alert bg-warning">'.gT("Click on the 'Edit subquestions' button.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#adminpanel__topbar--selectorAddSubquestions',
                    'placement' => 'bottom',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    document.location.href = $('#adminpanel__topbar--selectorAddSubquestions').attr('href');
                                    tour.setCurrentStep(24);
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 25,
                'ordering' => 25,
                'title' => gT('Edit subquestions'),
                'content' => gT("You should add some subquestions for your question here.").'<br/>'
                .gT("Every row is one subquestion. We recommend the usage of logical or numerical codes for subquestions."
                ." Your participants cannot see the subquestion code, only the subquestion text itself.")
                ."<p class='bg-info alert'>".gT("Pro tip: The subquestion may even contain HTML code.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#rowcontainer',
                    'path' => ['admin/questions/sa/subquestions/surveyid/[0-9]{4,25}/gid/[0-9]{1,25}/qid/[0-9]{4,25}'],
                    'placement' => 'bottom',
                    'redirect' => false,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 26,
                'ordering' => 26,
                'title' => gT('Add subquestion row'),
                'content' => sprintf(gT('Click on the plus sign %s to add another subquestion to your question.'), '<i class="icon-add text-success"></i>')
                ."<p class='bg-warning alert'>".gT('Please add at least two subquestions')."</p>",
                'settings' => json_encode(array(
                    'element' => '#rowcontainer>tr:first-of-type .btnaddanswer',
                    'path' => ['admin/questions/sa/subquestions/surveyid/[0-9]{4,25}/gid/[0-9]{1,25}/qid/[0-9]{4,25}'],
                    'placement' => 'left',
                    'redirect' => false,
                ))
            ),
            array(
                'teid' => 27,
                'ordering' => 27,
                'title' => gT('Now save the subquestions'),
                'content' => gT("You may save empty subquestions, but that would be pointless.")
                ."<p class='bg-warning alert'>".gT("Save and close now and let's edit the answer options.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#save-and-close-button',
                    'path' => ['admin/questions/sa/subquestions/surveyid/[0-9]{4,25}/gid/[0-9]{1,25}/qid/[0-9]{4,25}'],
                    'placement' => 'left',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    $('#save-and-close-button').trigger('click');
                                    tour.setCurrentStep(27);
                                    return new Promise(function(res,rej){});
                                })"
                ))
            ),
            array(
                'teid' => 28,
                'ordering' => 28,
                'title' => gT('Add some answer options to your question'),
                'content' => gT("Now that we've got some subquestions, we have to add answer options as well.").'<br/>'
                .gT("The answer options will be shown for each subquestion.")
                .'<p class="alert bg-warning">'.gT("Click on the 'Edit answer options' button.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#adminpanel__topbar--selectorAddAnswerOptions',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'reflex' => true,
                    'redirect' => false,
                    'prev' => '-1',
                    'onNext' => "(function(tour){
                                    document.location.href = $('#adminpanel__topbar--selectorAddAnswerOptions').attr('href');
                                    tour.setCurrentStep(28);
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 29,
                'ordering' => 29,
                'title' => gT('Edit answer options'),
                'content' => gT("As you can see, editing answer options is quite similar to editing subquestions.").'<br/>'
                .gT('Remember the plus button').'<i class="icon-add text-success"></i>?'.'<br/>'
                .'<p class="alert bg-warning">'."Please add at least two answer options to proceed.".'</p>',
                'settings' => json_encode(array(
                    'element' => '#rowcontainer',
                    'path' => ['admin/questions/sa/answeroptions/surveyid/[0-9]{4,25}/gid/[0-9]{1,25}/qid/[0-9]{4,25}'],
                    'placement' => 'bottom',
                    'redirect' => false,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 30,
                'ordering' => 30,
                'title' => gT('Now save the answer options'),
                'content' => gT("Click on 'Save and close' or 'Next' to proceed."),
                'settings' => json_encode(array(
                    'element' => '#save-and-close-button',
                    'path' => ['admin/questions/sa/answeroptions/surveyid/[0-9]{4,25}/gid/[0-9]{1,25}/qid/[0-9]{4,25}'],
                    'placement' => 'left',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    $('#save-and-close-button').trigger('click');
                                    tour.setCurrentStep(30);
                                    return new Promise(function(res,rej){});
                                })"
                ))
            ),
            array(
                'teid' => 31,
                'ordering' => 31,
                'title' => gT('Preview survey'),
                'content' => gT("Let's have a look at your first survey.").'<br/>'
                .gT("Just click on this button and a new window will open, where you can test run your survey.").'<br/>'
                .gT("Please be aware that your answers will not be saved, because the survey isn't active yet.")
                .'<p class="alert bg-warning">'.gT("Click on 'Preview survey' and return to this window when you are done testing.").'</p>',
                'settings' => json_encode(array(
                    'element' => '.selector__topbar--previewSurvey',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'redirect' => false,
                    'prev' => '-1',
                ))
            ),
            array(
                'teid' => 32,
                'ordering' => 32,
                'title' => gT('Easy navigation with the "breadcrumbs"'),
                'content' => gT('You can see the "breadcrumbs" In the top bar of the admin interface.').'<br/>'
                .gT("They represent an easy way to get back to any previous setting, and provide a general overview of where you are.")
                .'<p class="alert bg-warning">'.gT("Click on the name of your survey to get back to the survey settings overview.").'</p>',
                'settings' => json_encode(array(
                    'element' => '#breadcrumb__survey--overview',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}', 'gid' => '[0-9]{1,25}', 'qid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'reflex' => true,
                    'redirect' => false,
                    'onNext' => "(function(tour){
                                    tour.setCurrentStep(32);
                                    document.location.href = $('#breadcrumb__survey--overview').attr('href');
                                    return new Promise(function(res,rej){});
                                })",
                ))
            ),
            array(
                'teid' => 33,
                'ordering' => 33,
                'title' => gT('Finally, activate your survey'),
                'content' => gT("Now, activate your survey.").'<br/>'
                .gT("You can create as many surveys as you like.")
                .'<p class="alert bg-warning">'.gT("Click on 'Activate this survey'").'</p>',
                'settings' => json_encode(array(
                    'element' => '#ls-activate-survey',
                    'path' => ['/admin/survey/sa/view', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'reflex' => true,
                    'redirect' => false,
                    'prev' => '-1',
                    'onNext' => "(function(tour){
                            document.location.href = $('#ls-activate-survey').attr('href');
                            tour.setCurrentStep(33);
                            return new Promise(function(res,rej){});
                        })",
                ))
            ),
            array(
                'teid' => 34,
                'ordering' => 34,
                'title' => gT('Activation settings'),
                'content' => gT('These settings cannot be changed once the survey is online.').'<br/>'
                .gT("For this simple survey the default settings are ok, but read the disclaimer carefully when you activate your own surveys.").'<br/>'
                .gT("For more information consult our manual, or our forums.")
                .'<p class="alert bg-warning">'.gT('Now click on "Save & activate survey"').'</p>',
                'settings' => json_encode(array(
                    'element' => '#activateSurvey__basicSettings--proceed',
                    'path' => ['/admin/survey/sa/activate', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => '',
                    'reflex' => true,
                    'redirect' => false,
                    'prev' => '-1',
                    'onNext' => "(function(tour){
                            $('#activateSurvey__basicSettings--proceed').trigger('click');
                            tour.setCurrentStep(34);
                            return new Promise(function(res,rej){});
                        })",
                ))
            ),
            array(
                'teid' => 35,
                'ordering' => 35,
                'title' => gT('Activate survey participants table'),
                'content' => gT("Here you can select to start your survey in closed access mode.")."<br/>"
                .gT("For our simple survey it is better to start in open access mode.")."<br/>"
                .gT("The closed access mode needs a participant list, which you may create by clicking on the menu entry 'Participants'.")."<br/>"
                .gT("For more information please consult our manual or our forum.")
                .'<p class="alert bg-warning">'.gT("Click on 'No, thanks'").'</p>',
                'settings' => json_encode(array(
                    'element' => '#activateTokenTable__selector--no',
                    'path' => ['/admin/survey/sa/activate', ['surveyid' => '[0-9]{4,25}']],
                    'placement' => 'bottom',
                    'reflex' => true,
                    'redirect' => false,
                    'prev' => '-1',
                    'onNext' => "(function(tour){
                            $('#activateTokenTable__selector--no').trigger('click');
                            tour.setCurrentStep(35);
                            return new Promise(function(res,rej){});
                        })",
                ))
            ),
            array(
                'teid' => 36,
                'ordering' => 36,
                'title' => gT('Share this link'),
                'content' => gT("Just share this link with some of your friends and of course, test it yourself.")
                .'<p class="alert bg-success lstutorial__typography--white">'.gT("Thank you for taking the tour!").'</p>',
                'settings' => json_encode(array(
                    'element' => '#adminpanel__surveysummary--mainLanguageLink',
                    'path' => ['/'.'(index.php)?'],
                    'placement' => 'top',
                    'redirect' => false,
                    'prev' => '-1',
                    'onHide' => '(function(){window.location.reload()})'
                ))
            ),
        );
        return $returnArray;
    }
}
