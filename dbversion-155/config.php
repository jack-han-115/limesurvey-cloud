<?php
/*
* LimeSurvey
* Copyright (C) 2007 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or is 
* derivative of works licensed under the GNU General Public License or other 
* free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
 * $Id: config.php 9651 2010-12-16 14:25:20Z c_schmitz $
*/

/* IMPORTANT NOTICE
*  With LimeSurvey v1.70+ the configuration of LimeSurvey was simplified,
*  Now config.php only contains the basic required settings.
*  Some optional settings are also set by default in config-defaults.php.
*  If you want to change an optional parameter, DON'T change values in config-defaults.php!!!
*  Just copy the parameter into your config.php-file and adjust the value!
*  All settings in config.php overwrite the default values from config-defaults.php
*/


// Basic Setup

  $databasetype       =   'mysqli';       // ADOdb database driver - valid values are mysql, odbc_mssql or postgres
  $databasetabletype  =   "myISAM";
  $databaselocation   =   'localhost';   // Network location of your Database - for odbc_mssql use the mssql servername, not localhost or IP
  $databasename       =   '#dbname';  // The name of the database that we will create
  $databaseuser       =   '#dbuser';        // The name of a user with rights to create db (or if db already exists, then rights within that db)
  $databasepass       =   '#dbpass';            // Password of db user
  $databaseport       =   'default';     
  $databasepersistent = false;    
  $dbprefix           =   '';       // A global prefix that can be added to all LimeSurvey tables. Use this if you are sharing
// a database with other applications. Suggested prefix is 'lime_'
  
  // File Locations
  $rooturl            =   "http://{$_SERVER['HTTP_HOST']}"; //The root web url for your limesurvey installation (without a trailing slash).
  
  // Site Setup
  $sitename           =   'LimeService - Your online survey service';     // The official name of the site (appears in the Window title)
  
  $defaultuser        =   '#aduser';          // This is the default username when LimeSurvey is installed
  $defaultpass        =   '#adpass';       // This is the default password for the default user when LimeSurvey is installed
  
// Debug Settings
$debug              =   0;                  // Set this to 1 if you are looking for errors. If you still get no errors after enabling this
                                            // then please check your error-logs - either in your hosting provider admin panel or in some /logs dir
                                            // on your webspace.
                                           // LimeSurvey developers: Set this to 3 to circumvent the restriction to remove the installation directory and full access to standard templates
                                            // or to change the password. If you set it to 3 then PHP STRICT warnings will be shown additionally.

  $siteadminemail     =   '#adminemail'; // The default email address of the site administrator
  $siteadminbounce    =   'your@email.org'; // The default email address used for error notification of sent messages for the site administrator (Return-Path)
  $siteadminname      =   'Administrator';      // The name of the site administrator
  $memorylimit        =   '32M';    // This sets how much memory LimeSurvey can access. 16M is the minimum (M=mb) recommended.
  $lock=false;
