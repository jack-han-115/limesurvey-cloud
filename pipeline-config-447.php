<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	'connectionString' Hostname, database, port and database type for 
|	 the connection. Driver example: mysql. Currently supported:
|				 mysql, pgsql, mssql, sqlite, oci
|	'username' The username used to connect to the database
|	'password' The password used to connect to the database
|	'tablePrefix' You can add an optional prefix, which will be added
|				 to the table name when using the Active Record class
|
*/
return array(
    // TODO: Do we need these?
    //'basePath' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'limesurvey'.DIRECTORY_SEPARATOR.'application',
    //'runtimePath' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'runtime',
	'name' => 'LimeSurvey',
	'defaultController' => 'survey',
	
	'import' => array(
		'application.core.*',
		'application.models.*',
		'application.controllers.*',
		'application.modules.*',
	),
	
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=lsusr_12345;',
			'emulatePrepare' => true,
			'username' => 'lsusr_12345',
			'password' => 'password',
			'charset' => 'utf8',
			'tablePrefix' => '',
		),
        'dbstats' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=limeservice_statistics',
            'username' => 'statistics',
            'password' => 'password',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'class' => 'CDbConnection'
        ),
        
		 'session' => array (
          'sessionName' => 'LS-SESSION-123'
        // Uncomment the following line if you need table-based sessions
			// 'class' => 'system.web.CDbHttpSession',
			// 'connectionID' => 'db',
			// 'sessionTableName' => '{{sessions}}',
		),
		
		'urlManager' => array(
			'urlFormat' => 'path',
            // We don't need rules to run Psalm on code-base
            //'rules' => require(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'limesurvey'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'routes.php'),
			'showScriptName' => true,
		),
	
	),
	// Use the following config variable to set modified optional settings copied from config-defaults.php
	'config'=>array(
	// debug: Set this to 1 if you are looking for errors. If you still get no errors after enabling this
	// then please check your error-logs - either in your hosting provider admin panel or in some /logs directory
	// on your webspace.
	// LimeSurvey developers: Set this to 2 to additionally display STRICT PHP error messages and get full access to standard templates
		'debug'=>2,
        'debugsql'=>0, // Set this to 1 to enanble sql logging, only active when debug = 2
        'defaultuser'=>html_entity_decode('admin',ENT_QUOTES),
        'defaultpass'=>html_entity_decode('password',ENT_QUOTES),
        'siteadminemail'=>'admin@admin.admin'
	)
);
/* End of file config.php */
/* Location: ./application/config/config.php */
