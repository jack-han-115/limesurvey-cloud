<?php
/*
* LimeSurvey
* Copyright (C) 2007-2016 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
*/

/**
 * This widget display the list of surveys.
 */
class ListSurveysWidget extends CWidget
{
    public $model;                                                              // Survey model
    public $bRenderFooter    = true;                                            // Should the footer be rendered?
    public $bRenderSearchBox = true;                                            // Should the search box be rendered?
    public $formUrl          = 'admin/survey/sa/listsurveys/';

    public $massiveAction;                                                      // Used to render massive action in GridViews footer
    public $pageSize;                                                           // Default page size (should be set to Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']))
    public $template;

    public function run()
    {

        // Search
        if (isset($_GET['Survey']['searched_value'])){
            $this->model->searched_value = $_GET['Survey']['searched_value'];
        }

        $this->model->active = null;

        // Filter state
        if (isset($_GET['active']) && !empty($_GET['active'])){
            $this->model->active = $_GET['active'];
        }

        // Filter survey group
        if (isset($_GET['gsid']) &&  !empty($_GET['gsid'])){
            $this->model->gsid = $_GET['gsid'];
        }


        // Set number of page
        if (isset($_GET['pageSize'])){
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
        }

        $this->pageSize = Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']);

        Yii::app()->getClientScript()->registerScriptFile(App()->getAssetManager()->publish(dirname(__FILE__) . '/assets/reload.js'));

        $this->massiveAction = $this->render('massive_actions/_selector', array(), true, false);

        if ($this->bRenderFooter){
            $this->template = "{items}\n<div class=\"row-fluid\"><div class=\"col-sm-4\" id=\"massive-action-container\">$this->massiveAction</div><div class=\"col-sm-4 pager-container ls-ba \">{pager}</div><div class=\"col-sm-4 summary-container\">{summary}</div></div>";
        }else{
            $this->template = "{items}";
        }

        if ($this->bRenderSearchBox){
            $this->render('searchBox');
        }

        $this->render('listSurveys');

    }
}
