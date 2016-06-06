<?php
    /*
    * LimeSurvey (tm)
    * Copyright (C) 2011 The LimeSurvey Project Team / Carsten Schmitz
    * All rights reserved.
    * License: GNU/GPL License v2 or later, see LICENSE.php
    * LimeSurvey is free software. This version may have been modified pursuant
    * to the GNU General Public License, and as distributed it includes or
    * is derivative of works licensed under the GNU General Public License or
    * other free or open source software licenses.
    * See COPYRIGHT.php for copyright notices and details.
    *
    */
    class InstallCommand extends CConsoleCommand
    {
        public $connection;

        public function run($sArgument)
        {
            if (!isset($sArgument[0])) die("You have to set admin/password/full name and email address on the command line like this: php console.php install <my.domain.com>\n");
            Yii::import('application.helpers.common_helper', true);
            $aConfig=Yii::app()->getComponents(false);
            $aLocalConfig=require(str_replace('instances','installations',dirname(dirname(dirname(dirname(__FILE__))))).'/'.$sArgument[0].'/userdata/config.php');
            $aLocalConfig=$aLocalConfig['config'];
            $bDatabaseExists=true;
            try
            {
                $this->connection=new CDbConnection($aConfig['db']['connectionString'], $aConfig['db']['username'], $aConfig['db']['password']);
                $this->connection->active=true;
            }
            catch(Exception $e){
                $bDatabaseExists=false;
                $sConnectionString=preg_replace('/dbname=([^;]*)/', '', $aConfig['db']['connectionString']);
                try
                {
                    $this->connection=new CDbConnection($sConnectionString, $aConfig['db']['username'], $aConfig['db']['password']);
                    $this->connection->active=true;
                }
                catch(Exception $e){
                    echo "Invalid access data. Check your config.php db access data"; die();
                }

            };

            $sDatabaseType = substr($this->connection->connectionString,0,strpos($this->connection->connectionString,':'));
            $sDatabaseName= $this->getDBConnectionStringProperty('dbname');

            if (!$bDatabaseExists)
            {

                $createDb = true; // We are thinking positive
                switch ($sDatabaseType)
                {
                    case 'mysqli':
                    case 'mysql':
                    try
                    {
                        $this->connection->createCommand("CREATE DATABASE `$sDatabaseName` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
                    }
                    catch(Exception $e)
                    {
                        $createDb=false;
                    }
                    break;

                    case 'dblib':
                    case 'mssql':
                    case 'odbc':
                    try
                    {
                        $this->connection->createCommand("CREATE DATABASE [$sDatabaseName];")->execute();
                    }
                    catch(Exception $e)
                    {
                        $createDb=false;
                    }
                    break;
                    case 'postgres':
                    try
                    {
                        $this->connection->createCommand("CREATE DATABASE \"$sDatabaseName\" ENCODING 'UTF8'")->execute();
                    }
                    catch (Exception $e)
                    {
                        $createdb = false;
                    }
                    break;
                    default:
                    try
                    {
                        $this->connection->createCommand("CREATE DATABASE $sDatabaseName")->execute();
                    }
                    catch(Exception $e)
                    {
                        $createDb=false;
                    }
                    break;
                }
                if (!$createDb)
                {
                    echo 'Database could not be created because it either existed or you have no permissions'; die();
                }
                else
                {
                    $this->connection=new CDbConnection($aConfig['db']['connectionString'],$aConfig['db']['username'],$aConfig['db']['password']);
                    $this->connection->active=true;

                }
            }

            $this->connection->charset = 'utf8';
            switch ($sDatabaseType) {
                case 'mysql':
                case 'mysqli':
                    $this->connection->createCommand("ALTER DATABASE ". $this->connection->quoteTableName($sDatabaseName) ." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;")->execute();
                    $sql_file = 'mysql';
                    break;
                case 'pgsql':
                    if (version_compare($this->connection->getServerVersion(),'9','>=')) {
                        $this->connection->createCommand("ALTER DATABASE ". $this->connection->quoteTableName($sDatabaseName) ." SET bytea_output='escape';")->execute();
                    }
                    $sql_file = 'pgsql';
                    break;
                case 'dblib':
                case 'mssql':
                    $sql_file = 'mssql';
                    break;
                default:
                    throw new Exception(sprintf('Unknown database type "%s".', $sDatabaseType));
            }
            $this->_executeSQLFile(dirname(Yii::app()->basePath).'/installer/sql/create-'.$sql_file.'.sql', $this->connection->tablePrefix);
            $this->connection->createCommand()->insert($this->connection->tablePrefix.'users', array(
            'users_name'=>$aLocalConfig['defaultuser'],
            'password'=>hash('sha256',$aLocalConfig['defaultpass']),
            'full_name'=>'',
            'parent_id'=>0,
            'lang'=>'auto',
            'email'=>$aLocalConfig['siteadminemail'],
            ));
            $this->connection->createCommand()->insert($this->connection->tablePrefix.'permissions', array(
            'entity'=>'global',
            'entity_id'=>0,
            'uid'=>1,
            'permission'=>'superadmin',
            'create_p'=>0,
            'read_p'=>1,
            'update_p'=>0,
            'delete_p'=>0,
            'import_p'=>0,
            'export_p'=>0
            ));
        }

        function _executeSQLFile($sFileName, $sDatabasePrefix)
        {
            $aMessages = array();
            $sCommand = '';

            if (!is_readable($sFileName)) {
                return false;
            } else {
                $aLines = file($sFileName);
            }
            foreach ($aLines as $sLine) {
                $sLine = rtrim($sLine);
                $iLineLength = strlen($sLine);

                if ($iLineLength && $sLine[0] != '#' && substr($sLine,0,2) != '--') {
                    if (substr($sLine, $iLineLength-1, 1) == ';') {
                        $sCommand .= $sLine;
                        $sCommand = str_replace('prefix_', $sDatabasePrefix, $sCommand); // Table prefixes

                        try {
                            $this->connection->createCommand($sCommand)->execute();
                        } catch(Exception $e) {
                            $aMessages[] = "Executing: ".$sCommand." failed! Reason: ".$e;
                            var_dump($e); die();
                        }

                        $sCommand = '';
                    } else {
                        $sCommand .= $sLine;
                    }
                }
            }
            return $aMessages;


        }

        function getDBConnectionStringProperty($sProperty)
        {
            $aConfig=Yii::app()->getComponents(false);
            // Yii doesn't give us a good way to get the database name
            preg_match('/'.$sProperty.'=([^;]*)/',$this->connection->connectionString, $aMatches);
            if ( count($aMatches) === 0 ) {
                return null;
            }
            return $aMatches[1];
        }

    }
?>