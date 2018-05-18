<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
/*
* LimeSurvey
* Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
*
*/

/**
*
* @package       LimeSurvey
* @subpackage    Backend
*/

/**
* !!! LIMESERVIVE VERSION !!!!
* to merge with master, just replace the content of the file by this one.
*/
class update extends Survey_Common_Action
{

    // ============ Update LimeService Begin =======================================================
    public function scheduleupgrade()
    {
        $iDestinationVersion=345;
        $sUpgradeVersion='LimeSurvey 3';

        // Check if already scheduled for upgrade
        $sDomain           = $_SERVER['SERVER_NAME'];
        $sSubDomain        = substr($sDomain,0,strpos($sDomain,'.'));
        $sRootDomain       = substr($sDomain,strpos($sDomain,'.')+1);
        $iUpgradeDBVersion = Yii::app()->dbstats->createCommand("select upgradedbversion from pageviews where subdomain='$sSubDomain' and rootdomain='$sRootDomain'")->queryScalar();

        Yii::app()->dbstats->createCommand("Update pageviews set upgradedbversion=$iDestinationVersion where subdomain='$sSubDomain' and rootdomain='$sRootDomain'")->execute();

        $aData['scheduleupgrade']       = true;

        $this->_renderWrappedTemplate('update', '_updateContainer', $aData);
    }


    /**
     * First function to be called, when comming to admin/update
     *
     */
    public function index()
    {
        $aData['fullpagebar']['update'] = false;
        $this->_renderWrappedTemplate('update', '_updateContainer', $aData);
    }
    // ============ Update LimeService End======================================================= /

}
