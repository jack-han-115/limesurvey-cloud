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
*
* This controller performs updates, it is highly ajax oriented
* Methods are only called from JavaScript controller (wich is called from the global_setting view). comfortupdate.js is the first registered script.
*
*
*
* Public methods are written in a chronological way:
*   - First, when the user click on the 'check for updates' button, the plugin buildComfortButtons.js call for getstablebutton() or getbothbuttons() method and inject the HTML inside the li#udapteButtonsContainer in the _checkButtons view
*   - Then, when the user click on one of those buttons, the comfortUpdateNextStep.js plugin will call for the getWelcome() method and inject the HTML inside div#updaterContainer in the _right_container view (all steps will be then injected here)
*   - Then, when the user click on the continue button, the comfortUpdateNextStep.js plugin will call for the step1() method and inject the  the HTML inside div#updaterContainer in the _right_container view
*   - etc. etc.
*
*
*
*  Some steps must be shown out of the chronological process: getNewKey and submitKey. They are at the end of the controller's interface.
*  Some steps must be 'checked again' after the user fixed some errors (such as file permissions).
*  Those steps are/can be diplayed by the plugin displayComfortStep.js. They are called from buttons like :
*
*  <a class='button' href='<?php Yii::app()->createUrl('admin/globalsettings', array('update'=>'methodToCall', 'neededVariable'=>$value));?>'>
*    <span class='ui-button-text'>button text</span>
*  </a>
*
* so they will call an url such as : globalsettings?update=methodToCall&neededVariable=value.
* So the globalsetting controller will render the view as usual, but : the _ajaxVariables view will parse those url datas to some hidden field.
* The comfortupdate.js check the value of the hidden field update, and if the update's one contain a step, it call displayComfortStep.js wich will display the right step instead of the 'check update' buttons.
*
* Most steps are retrieving datas from the comfort update server thanks to the model UpdateForm's methods.
* The server return an answer object, with a property 'result' to tell if the process was succesfull or if it failed. This object contains in general all the necessary datas for the views.
*
*
* Handling errors :
* They are different types of possible errors :
* - Warning message (like : modified files, etc.) : they don't stop the process, they are parsed to the step view, and the view manage how to display them. They can be generated from the ComfortUpdate server ($answer_from_server->result == TRUE ; and something like $answer_from_server->error == message or anything else that the step view manage ), or in the LimeSurvey update controller/model
* - Error while processing a request on the server part : should never happen, but if something goes wrong in the server side (like generating an object from model), the server returns an error object ($answer_from_server->result == FALSE ; $answer_from_server->error == message )
*   Those errors stop the process, and are display in _error view. Very usefull to debug. They are parsed directly to $this->_renderError
* - Error while checking needed datas in the LimeSurvey update controller : the controller always check if it has the needed datas (such as destintion_build, or zip_file), or the state of the key (outdated, etc). For the code to be dryer, the method parse an error string to $this->_renderErrorString($error), wich generate the error object, and then render the error view
*
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

        $aData['fullpagebar']['update'] = true;
        $aData['scheduleupgrade']       = true;

        $this->_renderWrappedTemplate('update', '_updateContainer', $aData);
    }

    // ============ Update LimeService End======================================================= /



}
