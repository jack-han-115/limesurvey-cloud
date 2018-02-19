<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * LimeSurvey
 * Copyright (C) 2013 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
  * 	Files Purpose: lots of common functions
 */
class SurveyDynamic extends LSActiveRecord
{
    /** @var string $completed_filter */
    public  $completed_filter;
    /** @var string $firstname_filter */
    public $firstname_filter;
    /** @var string $lastname_filter */
    public $lastname_filter;
    /** @var string $email_filter */
    public $email_filter;
    /** @var integer $lastpage */
    public $lastpage;

    /** @var int $sid */
    protected static $sid = 0;

    /** @var Survey $survey */
    protected static $survey;

    /** @var  boolean $bHaveToken */
    protected $bHaveToken;

    /**
     * @inheritdoc
     * @return SurveyDynamic
     */
    public static function model($sid = null)
    {
        $refresh = false;
        $survey = Survey::model()->findByPk($sid);
        if ($survey) {
            self::sid($survey->sid);
            self::$survey = $survey;
            $refresh = true;
        }

        /** @var self $model */
        $model = parent::model(__CLASS__);

        //We need to refresh if we changed sid
        if ($refresh === true) {
            $model->refreshMetaData();
        }

        return $model;
    }

    /**
     * Sets the survey ID for the next model
     *
     * @static
     * @access public
     * @param int $sid
     * @return void
     */
    public static function sid($sid)
    {
        self::$sid = (int) $sid;
    }

    /** @inheritdoc */
    public function tableName()
    {
        return '{{survey_'.self::$sid.'}}';
    }

    /** @inheritdoc */
    public function relations()
    {
        if ($this->getbHaveToken()) {
            TokenDynamic::sid(self::$sid);
            return array(
                'survey'   => array(self::HAS_ONE, 'Survey', array(), 'condition'=>('sid = '.self::$sid)),
                'tokens'   => array(self::HAS_ONE, 'TokenDynamic', array('token' => 'token'))
            );
        } else {
            return array();
        }
    }

    /** @inheritdoc */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * Insert records from $data array
     *
     * @access public
     * @param array $data
     * @return boolean
     */
    public function insertRecords($data)
    {
        $record = new self;
        foreach ($data as $k => $v) {
            $search = array('`', "'");
            $k = str_replace($search, '', $k);
            $v = str_replace($search, '', $v);
            $record->$k = $v;
        }

        try {
            $record->save();
            return $record->id;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * Deletes some records from survey's table
     * according to specific condition
     *
     * @static
     * @access public
     * @param array|bool $condition
     * @return int|CDbCriteria
     */
    public static function deleteSomeRecords($condition = false)
    {
        $survey = new SurveyDynamic;
        $criteria = new CDbCriteria;

        if ($condition) {
            foreach ($condition as $column => $value) {
                return $criteria->addCondition($column."=`".$value."`");
            }
        }

        return $survey->deleteAll($criteria);
    }

    /**
     * Return criteria updated with the ones needed for including results from the timings table
     *
     * @param array $condition
     * @return CDbCriteria
     */
    public function addTimingCriteria($condition)
    {
        $newCriteria = new CDbCriteria();
        $criteria = $this->getCommandBuilder()->createCriteria($condition);

        if ($criteria->select == '*') {
            $criteria->select = 't.*';
        }
        $alias = $this->getTableAlias();

        $newCriteria->join = "LEFT JOIN ".self::$survey->tokensTableName." survey_timings ON $alias.id = survey_timings.id";
        $newCriteria->select = 'survey_timings.*'; // Otherwise we don't get records from the survey participants table
        $newCriteria->mergeWith($criteria);

        return $newCriteria;
    }

    /**
     * Return criteria updated with the ones needed for including results from the survey participants table
     *
     * @param string $condition
     * @return CDbCriteria
     */
    public function addTokenCriteria($condition)
    {
        $newCriteria = new CDbCriteria();
        $criteria = $this->getCommandBuilder()->createCriteria($condition);
        $aSelectFields = Yii::app()->db->schema->getTable(self::$survey->responsesTableName)->getColumnNames();
        $aSelectFields = array_diff($aSelectFields, array('token'));
        $aSelect = array();
        $alias = $this->getTableAlias();
        foreach ($aSelectFields as $sField) {
            $aSelect[] = "$alias.".Yii::app()->db->schema->quoteColumnName($sField);
        }
        $aSelectFields = $aSelect;
        $aSelectFields[] = "$alias.token";

        if ($criteria->select == '*') {
            $criteria->select = $aSelectFields;
        }

        $newCriteria->join = "LEFT JOIN {{tokens_".self::$sid."}} tokens ON $alias.token = tokens.token";

        $aTokenFields = Yii::app()->db->schema->getTable(self::$survey->tokensTableName)->getColumnNames();
        $aTokenFields = array_diff($aTokenFields, array('token'));

        $newCriteria->select = $aTokenFields; // Otherwise we don't get records from the survey participants table
        $newCriteria->mergeWith($criteria);

        return $newCriteria;
    }

    /**
     * @param integer $sid
     * @return array
     */
    public static function countAllAndPartial($sid)
    {
        $select = array(
            'count(*) AS cntall',
            'sum(CASE
                 WHEN '. Yii::app()->db->quoteColumnName('submitdate').' IS NULL THEN 1
                          ELSE 0
                 END) AS cntpartial',
            );
        $result = Yii::app()->db->createCommand()->select($select)->from('{{survey_'.$sid.'}}')->queryRow();
        return $result;
    }

    /**
     * Return true if actual survey is completed
     *
     * @param integer $srid : actual save survey id
     *
     * @return boolean
     */
    public function isCompleted($srid)
    {
        static $resultCache = array();

        $sid = self::$sid;
        if (array_key_exists($sid, $resultCache) && array_key_exists($srid, $resultCache[$sid])) {
            return $resultCache[$sid][$srid];
        }
        $completed = false;

        if (Yii::app()->db->schema->getTable($this->tableName())) {
            $data = Yii::app()->db->createCommand()
                ->select("submitdate")
                ->from($this->tableName())
                ->where('id=:id', array(':id'=>$srid))
                ->queryRow();
            if ($data && $data['submitdate']) {
                $completed = true;
            }
        }
        $resultCache[$sid][$srid] = $completed;
        return $completed;
    }


    /**
     * For grid list
     * @return string
     */
    public function getCompleted()
    {
        return ($this->submitdate != '') ? '<span class="text-success fa fa-check"></span>' : '<span class="text-warning fa fa-times"></span>';
    }

    /**
     * Get buttons HTML for response browse view.
     * @return string HTML
     */
    public function getButtons()
    {
        $sViewUrl     = App()->createUrl("/admin/responses/sa/view/surveyid/".self::$sid."/id/".$this->id);
        $sViewPDFUrl = App()->createUrl("/admin/responses/sa/viewquexmlpdf/surveyid/".self::$sid."/id/".$this->id);
        $sEditUrl     = App()->createUrl("admin/dataentry/sa/editdata/subaction/edit/surveyid/".self::$sid."/id/".$this->id);
        $sDownloadUrl = App()->createUrl("admin/responses", array("sa"=>"actionDownloadfiles", "surveyid"=>self::$sid, "sResponseId"=>$this->id));
        $sDeleteUrl   = App()->createUrl("admin/responses", array("sa"=>"actionDelete", "surveyid"=>self::$sid));
        $sAttachmentDeleteUrl = App()->createUrl("admin/responses", array("sa"=>"actionDeleteAttachments"));
        $button       = "";

        // View detail icon
        $button .= '<a class="btn btn-default btn-xs" href="'.$sViewUrl.'" target="_blank" role="button" data-toggle="tooltip" title="'.gT("View response details").'"><span class="fa fa-list-alt" ></span></a>';

        // View quexMLPDF icon
        $button .= '<a class="btn btn-default btn-xs" href="'.$sViewPDFUrl.'" target="_blank" role="button" data-toggle="tooltip" title="'.gT("View response details as queXML PDF").'"><span class="fa fa-file-o" ></span></a>';

        // Edit icon
        if (Permission::model()->hasSurveyPermission(self::$sid, 'responses', 'update')) {
            $button .= '<a class="btn btn-default btn-xs" href="'.$sEditUrl.'" target="_blank" role="button" data-toggle="tooltip" title="'.gT("Edit this response").'"><span class="fa fa-pencil text-success" ></span></a>';
        }

        $responseHasFiles = Response::model(self::$sid)->findByPk($this->id)->someFileExists();

        // Download icon
        if (hasFileUploadQuestion(self::$sid) && $responseHasFiles) {
            if (Response::model(self::$sid)->findByPk($this->id)->getFiles()) {
                $button .= '<a class="btn btn-default btn-xs" href="'.$sDownloadUrl.'" target="_blank" role="button" data-toggle="tooltip" title="'.gT("Download all files in this response as a zip file").'"><i class="fa fa-download downloadfile text-success" ></i></a>';
            }
        } else {
            $button .= '<a class="btn btn-default btn-xs invisible" href="#" role="button"><span class="glyphicon glyphicon-download-alt downloadfile text-success" ></span></a>';
        }

        $aPostDatas = json_encode(
            array(
                'surveyid' => self::$sid,
                'sResponseId' => $this->id
            )
        );

        // Delete icon
        if (Permission::model()->hasSurveyPermission(self::$sid, 'responses', 'delete')) {
            $aPostDatas = json_encode(array('sResponseId'=>$this->id));
            $button .= "<a class='deleteresponse btn btn-default btn-xs' data-ajax-url='".$sDeleteUrl."' data-gridid='responses-grid' role='button' data-toggle='modal' data-post='".$aPostDatas."' data-target='#confirmation-modal' data-tooltip='true' title='".sprintf(gT('Delete response %s'), $this->id)."'><span class='fa fa-trash text-danger' ></span></a>";
        }

        // Delete all uploaded attachments from one response.
        if (Permission::model()->hasSurveyPermission(self::$sid, 'responses', 'delete')) {
            if (hasFileUploadQuestion(self::$sid) && $responseHasFiles) {
                $button .= sprintf(
                    "<a class='deleteattachments btn btn-danger btn-xs text-danger' data-ajax-url='%s' data-gridid='responses-grid' data-toggle='modal' data-post='%s' data-target='#confirmation-modal' data-tooltip='true' title='%s'>
                        <span class='fa fa-paperclip'></span>
                        </a>",
                    $sAttachmentDeleteUrl,
                    $aPostDatas,
                    gT('Delete all attachments for this response')
                );
            }
        }

        return $button;
    }


    /**
     * @param string $colName
     * @param string $sLanguage
     * @param string $base64jsonFieldMap
     * @return string
     */
    public function getExtendedData($colName, $sLanguage, $base64jsonFieldMap)
    {
        $oFieldMap = json_decode(base64_decode($base64jsonFieldMap));
        $value     = $this->$colName;

        $sFullValue = strip_tags(getExtendedAnswer(self::$sid, $oFieldMap->fieldname, $value, $sLanguage));
        if (strlen($sFullValue) > 50) {
            $sElipsizedValue = ellipsize($sFullValue, $this->ellipsize_question_value);
            $sValue          = '<span data-toggle="tooltip" data-placement="left" title="'.quoteText($sFullValue).'">'.$sElipsizedValue.'</span>';
        } else {
            $sValue          = $sFullValue;
        }

        // Upload question
        if ($oFieldMap->type == '|' && strpos($oFieldMap->fieldname, 'filecount') === false) {

            $sSurveyEntry = "<table class='table table-condensed upload-question'><tr>";
            $aQuestionAttributes = QuestionAttribute::model()->getQuestionAttributes($oFieldMap->qid);
            $aFilesInfo = json_decode_ls($this->$colName);
            for ($iFileIndex = 0; $iFileIndex < $aQuestionAttributes['max_num_of_files']; $iFileIndex++) {
                $sSurveyEntry .= '<tr>';
                if (isset($aFilesInfo[$iFileIndex])) {
                    $sSurveyEntry .= '<td>'.CHtml::link(CHtml::encode(rawurldecode($aFilesInfo[$iFileIndex]['name'])), App()->createUrl("/admin/responses", array("sa"=>"actionDownloadfile", "surveyid"=>self::$sid, "iResponseId"=>$this->id, "iQID"=>$oFieldMap->qid, "iIndex"=>$iFileIndex))).'</td>';
                    $sSurveyEntry .= '<td>'.sprintf('%s Mb', round($aFilesInfo[$iFileIndex]['size'] / 1000, 2)).'</td>';

                    if ($aQuestionAttributes['show_title']) {
                        if (!isset($aFilesInfo[$iFileIndex]['title'])) {
                            $aFilesInfo[$iFileIndex]['title'] = '';
                        }
                        $sSurveyEntry .= '<td>'.htmlspecialchars($aFilesInfo[$iFileIndex]['title'], ENT_QUOTES, 'UTF-8').'</td>';
                    }
                    if ($aQuestionAttributes['show_comment']) {
                        if (!isset($aFilesInfo[$iFileIndex]['comment'])) {
                            $aFilesInfo[$iFileIndex]['comment'] = '';
                        }
                        $sSurveyEntry .= '<td>'.htmlspecialchars($aFilesInfo[$iFileIndex]['comment'], ENT_QUOTES, 'UTF-8').'</td>';
                    }
                }
                $sSurveyEntry .= '</tr>';
            }
            $sSurveyEntry .= '</table>';
            $sValue = $sSurveyEntry;
        }

        return $sValue;
    }

    /**
     * Return true if actual response exist in database
     *
     * @param integer $srid : actual save survey id
     *
     * @return boolean
     */
    public function exist($srid)
    {
        $exist = false;

        if (Yii::app()->db->schema->getTable($this->tableName())) {
            $data = Yii::app()->db->createCommand()
                ->select("id")
                ->from($this->tableName())
                ->where('id=:id', array(':id'=>$srid))
                ->queryRow();
            if ($data) {
                $exist = true;
            }
        }
        return $exist;
    }

    /**
     * Return next id if next response exist in database
     *
     * @param integer $srId : actual save survey id
     * @param boolean $useFilterState
     *
     * @return integer
     */
    public function next($srId, $useFilterState = false)
    {
        $next = false;
        if ($useFilterState && incompleteAnsFilterState() == 'incomplete') {
            $whereFilterState = 'submitdate IS NULL';
        } elseif ($useFilterState && incompleteAnsFilterState() == 'complete') {
            $whereFilterState = 'submitdate IS NOT NULL';
        } else {
            $whereFilterState = '1=1';
        }

        if (Yii::app()->db->schema->getTable($this->tableName())) {
            $data = Yii::app()->db->createCommand()
                ->select("id")
                ->from($this->tableName())
                ->where(array('and', $whereFilterState, 'id > :id'), array(':id'=>$srId))
                ->order('id ASC')
                ->queryRow();
            if ($data) {
                $next = $data['id'];
            }
        }
        return $next;
    }

    /**
     * Return previous id if previous response exist in database
     *
     * @param integer $srId : actual save survey id
     * @param boolean $useFilterState
     *
     * @return integer
     */
    public function previous($srId, $useFilterState = false)
    {
        $previous = false;
        if ($useFilterState && incompleteAnsFilterState() == 'incomplete') {
            $whereFilterState = 'submitdate IS NULL';
        } elseif ($useFilterState && incompleteAnsFilterState() == 'complete') {
            $whereFilterState = 'submitdate IS NOT NULL';
        } else {
            $whereFilterState = '1=1';
        }

        if (Yii::app()->db->schema->getTable($this->tableName())) {
            $data = Yii::app()->db->createCommand()
                ->select("id")
                ->from($this->tableName())
                ->where(array('and', $whereFilterState, 'id < :id'), array(':id'=>$srId))
                ->order('id DESC')
                ->queryRow();
            if ($data) {
                $previous = $data['id'];
            }
        }
        return $previous;
    }

    /**
     * Function that returns a time-line of the surveys submissions
     *
     * @param string $sType
     * @param string $dStart
     * @param string $dEnd
     *
     * @access public
     * @return array|boolean
     */
    public function timeline($sType, $dStart, $dEnd)
    {

        $sid = self::$sid;
        $oSurvey = Survey::model()->findByPk($sid);
        if ($oSurvey['datestamp'] != 'Y') {
            return false;
        } else {
            $criteria = new CDbCriteria;
            $criteria->select = 'submitdate';
            $criteria->addCondition('submitdate >= :dstart');
            $criteria->addCondition('submitdate <= :dend');
            $criteria->order = "submitdate";

            $criteria->params[':dstart'] = $dStart;
            $criteria->params[':dend'] = $dEnd;
            $oResult = $this->findAll($criteria);

            if ($sType == "hour") {
                $dFormat = "Y-m-d_G";
            } else {
                $dFormat = "Y-m-d";
            }

            $aRes = array();
            foreach ($oResult as $sResult) {
                $aRes[] = date($dFormat, strtotime($sResult['submitdate']));
            }

            return array_count_values($aRes);
        }
    }

    /**
     * @return bool
     */
    private function getbHaveToken()
    {
        if (!isset($this->bHaveToken)) {
            $this->bHaveToken = tableExists('tokens_'.self::$sid) && Permission::model()->hasSurveyPermission(self::$sid, 'tokens', 'read'); // Boolean : show (or not) the token;
        }
        return $this->bHaveToken;
    }


    /**
     * @return string
     */
    public function getFirstNameForGrid()
    {
        if (is_object($this->tokens)) {
            return '<strong>'.$this->tokens->firstname.'</strong>';
        }

    }

    /**
     * @return string
     */
    public function getLastNameForGrid()
    {
        if (is_object($this->tokens)) {
            return '<strong>'.$this->tokens->lastname.'</strong>';
        }
    }

    /**
     * @return string
     */
    public function getTokenForGrid()
    {
        if (is_object($this->tokens) && !is_null($this->tokens->tid)) {
            $sToken = "<a class='btn btn-default btn-xs edit-token' href='#' data-sid='".self::$sid."' data-tid='".$this->tokens->tid."'  data-url='".App()->createUrl("admin/tokens", array("sa"=>"edit", "iSurveyId"=>self::$sid, "iTokenId"=>$this->tokens->tid, 'ajax'=>'true'))."' data-toggle='tooltip' title='".gT("Edit this survey participant")."'>".strip_tags($this->token)."&nbsp;&nbsp;&nbsp;<span class='fa fa-pencil'></span></a>";
        } else {
            $sToken = '<span class="badge badge-success">'.strip_tags($this->token).'</span>';
        }

        return $sToken;
    }

    /**
     * Get the list of default columns for surveys
     * @return string[]
     */
    public function getDefaultColumns()
    {
        return array('id', 'token', 'submitdate', 'lastpage', 'startlanguage', 'completed', 'seed');
    }

    /**
     * Define what value to use to ellipsize the headers of the grid
     * It's using user state/default config, like for pagination
     * @see: http://www.yiiframework.com/wiki/324/cgridview-keep-state-of-page-and-sort/
     * @see: http://www.yiiframework.com/forum/index.php?/topic/8994-dropdown-for-pagesize-in-cgridview
     */
    public function getEllipsize_header_value()
    {
        return Yii::app()->user->getState('defaultEllipsizeHeaderValue', Yii::app()->params['defaultEllipsizeHeaderValue']);
    }

    /**
     * Define what value to use to ellipsize the question in the grid
     * It's using user state/default config, like for pagination.
     * @see: http://www.yiiframework.com/wiki/324/cgridview-keep-state-of-page-and-sort/
     * @see: http://www.yiiframework.com/forum/index.php?/topic/8994-dropdown-for-pagesize-in-cgridview
     */
    public function getEllipsize_question_value()
    {
        return Yii::app()->user->getState('defaultEllipsizeQuestionValue', Yii::app()->params['defaultEllipsizeQuestionValue']);
    }

    /**
     * @return CActiveDataProvider
     */
    public function search()
    {

        $pageSize = Yii::app()->user->getState('pageSize', Yii::app()->params['defaultPageSize']);
        $criteria = new CDbCriteria;
        $sort     = new CSort;
        $sort->defaultOrder = 'id ASC';

        // Make all the model's columns sortable (default behaviour)
        $sort->attributes = array(
            '*',
        );

        // Join the survey participants table and filter tokens if needed
        if ($this->bHaveToken && $this->survey->anonymized != 'Y') {
            $this->joinWithToken($criteria, $sort);
        }

        // Basic filters
        $criteria->compare('t.lastpage', empty($this->lastpage) ? null : $this->lastpage, false);
        $criteria->compare('t.id', empty($this->id) ? null : $this->id, false);
        $criteria->compare('t.submitdate', $this->submitdate, true);
        $criteria->compare('t.startlanguage', $this->startlanguage, true);

        // Completed filters
        if ($this->completed_filter == "Y") {
            $criteria->addCondition('t.submitdate IS NOT NULL');
        }

        if ($this->completed_filter == "N") {
            $criteria->addCondition('t.submitdate IS NULL');
        }

        // When selection of responses come from statistics
        // TODO: This provide a first step to enable the old jQgrid selector system, and could be use for users and tokens
        if (Yii::app()->user->getState('sql_'.self::$sid) != null) {
            $criteria->condition .= Yii::app()->user->getState('sql_'.self::$sid);
        }

        $this->filterColumns($criteria);


        $dataProvider = new CActiveDataProvider('SurveyDynamic', array(
            'sort'=>$sort,
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>$pageSize,
            ),
        ));

        return $dataProvider;
    }

    /**
     * @param CDbCriteria $criteria
     * @param CSort $sort
     * @return void
     */
    protected function joinWithToken(CDbCriteria $criteria, CSort $sort)
    {
        $criteria->compare('t.token', $this->token, true);
        $criteria->join = "LEFT JOIN {{tokens_".self::$sid."}} as tokens ON t.token = tokens.token";
        $criteria->compare('tokens.firstname', $this->firstname_filter, true);
        $criteria->compare('tokens.lastname', $this->lastname_filter, true);
        $criteria->compare('tokens.email', $this->email_filter, true);

        // Add the related token model's columns sortable
        $aSortVirtualAttributes = array(
            'tokens.firstname'=>array(
                'asc'=>'tokens.firstname ASC',
                'desc'=>'tokens.firstname DESC',
            ),
            'tokens.lastname' => array(
                'asc'=>'lastname ASC',
                'desc'=>'lastname DESC'
            ),
            'tokens.email' => array(
                'asc'=>'email ASC',
                'desc'=>'email DESC'
            ),
        );

        $sort->attributes = array_merge($sort->attributes, $aSortVirtualAttributes);
    }

    /**
     * Loop through columns and add filter if any value is given for this column
     * Used in responses grid
     * @param CdbCriteria $criteria
     * @return void
     */
    protected function filterColumns(CDbCriteria $criteria)
    {
        $dateFormatDetails = getDateFormatData(Yii::app()->session['dateformat']);

        // Filters for responses
        foreach ($this->metaData->columns as $column) {
            $isNotDefaultColumn = !in_array($column->name, $this->defaultColumns);
            if ($isNotDefaultColumn) {
                $c1 = (string) $column->name;
                $columnHasValue = !empty($this->$c1);
                if ($columnHasValue) {
                    $isDatetime = strpos($column->dbType, 'timestamp') !== false || strpos($column->dbType, 'datetime') !== false;
                    if ($column->dbType == 'decimal') {
                        $this->$c1 = (float) $this->$c1;
                        $criteria->compare(Yii::app()->db->quoteColumnName($c1), $this->$c1, false);
                    } else if ($isDatetime) {
                        $s = DateTime::createFromFormat($dateFormatDetails['phpdate'], $this->$c1);
                        if ($s === false) {
                            // This happens when date is in wrong format
                            continue;
                        }
                        $s2 = $s->format('Y-m-d');
                        $criteria->addCondition('cast('.Yii::app()->db->quoteColumnName($c1).' as date) = \''.$s2.'\'');
                    } else {
                        $criteria->compare(Yii::app()->db->quoteColumnName($c1), $this->$c1, true);
                    }
                }
            }
        }
    }

    /**
     * Get an array to find question data responsively
     *
     * @param Question $oQuestion
     * @param SurveyDynamic $oResponses
     * @param boolean $bHonorConditions
     * @param boolean $subquestion
     * @param boolean $getComment
     * @return array | boolean
     */
    public function getQuestionArray($oQuestion, $oResponses, $bHonorConditions, $subquestion = false, $getComment = false)
    {

        $attributes = QuestionAttribute::model()->getQuestionAttributes($oQuestion->qid);

        if (!(LimeExpressionManager::QuestionIsRelevant($oQuestion->qid) || $bHonorConditions == false) && $attributes['hidden'] === 1) {
            return false;
        }

        $aQuestionAttributes = $oQuestion->attributes;

        if (count($oQuestion->subquestions) > 0) {
            $aQuestionAttributes['subquestions'] = array();
            foreach ($oQuestion->subquestions as $oSubquestion) {
                //dont collect scale_id > 0
                if ($oSubquestion->scale_id > 0) {
                    continue;
                }

                $subQuestionArray = $this->getQuestionArray($oSubquestion, $oResponses, $bHonorConditions, true);
                if ($oQuestion->type == "P") {
                    $subQuestionArray['comment'] = $this->getQuestionArray($oSubquestion, $oResponses, $bHonorConditions, true, true);
                }

                $aQuestionAttributes['subquestions'][$oSubquestion->qid] = $subQuestionArray;
                
                
            }
            //Get other options
            if (in_array($oQuestion->type, ["M", "P"]) && $oQuestion->other == "Y") {
                $oOtherQuestion = new Question($oQuestion->attributes);
                $oOtherQuestion->setAttributes(array(
                    "sid" => $oQuestion->sid,
                    "gid" => $oQuestion->gid,
                    "type" => "T",
                    "parent_qid" => $oQuestion->qid,
                    "qid" => "other",
                    "question" => "other",
                    "title" => "other"
                ), false);
                $aQuestionAttributes['subquestions']["other"] = $this->getQuestionArray($oOtherQuestion, $oResponses, $bHonorConditions, true);
                if ($oQuestion->type == "P") {
                    $aQuestionAttributes['subquestions']["other"]['comment'] = $this->getQuestionArray($oOtherQuestion, $oResponses, $bHonorConditions, true, true);
                }
            }
        }

        $fieldname = $oQuestion->basicFieldName;
        
        //If question is of any Array-Type  or a subquestion
        if (in_array($oQuestion->type, ["F", "A", "B", "E", "C", "H", "Q", "K"]) 
            || ($oQuestion->type=='T' && $oQuestion->parent_qid != 0) 
        ) {
            $fieldname .= $oQuestion->title;
        }
        
        if ($getComment === true) {
            $fieldname .= 'comment';
        }

        $aQuestionAttributes['fieldname'] = $fieldname;
        $aQuestionAttributes['questionclass'] = Question::getQuestionClass($oQuestion->type);

        if ($oQuestion->scale_id == 1) {
            return  $aQuestionAttributes;
        }

        if ($aQuestionAttributes['questionclass'] === 'date') {
            $aQuestionAttributes['dateformat'] = getDateFormatDataForQID($aQuestionAttributes, array_merge(self::$survey->attributes, $oQuestion->survey->languagesettings[$oQuestion->language]->attributes));
        }

        $aQuestionAttributes['answervalue'] = isset($oResponses[$fieldname]) ? $oResponses[$fieldname] : null;
        
        if ($aQuestionAttributes['questionclass'] === 'language') {
            $languageArray = getLanguageData(false, $aQuestionAttributes['answervalue']);
            $aQuestionAttributes['languageArray'] = $languageArray[$aQuestionAttributes['answervalue']];
        }
        
        if ($aQuestionAttributes['questionclass'] === 'upload-files') {
            $aQuestionAttributes['fileinfo'] = json_decode($aQuestionAttributes['answervalue'], true);
        }

        
        if ($oQuestion->parent_qid != 0 && $oQuestion->parents['type'] === "1") {
            $tempFieldname = $fieldname.'#0';
            $aQuestionAttributes['answervalues'][0] = isset($oResponses[$tempFieldname]) ? $oResponses[$tempFieldname] : null;
            $tempFieldname = $fieldname.'#1';
            $aQuestionAttributes['answervalues'][1] = isset($oResponses[$tempFieldname]) ? $oResponses[$tempFieldname] : null;        
        }

        if ($aQuestionAttributes['questionclass'] === 'ranking') {
            $aQuestionAttributes['answervalues'] = array();
            $iterator = 1;
            do {
                $currentResponse = $oResponses[$fieldname.$iterator];
                $aQuestionAttributes['answervalues'][] = $currentResponse;
                $iterator++;
            } while (isset($oResponses[$fieldname.$iterator]));
        
        }
        
        if ($oQuestion->parent_qid != 0 && in_array($oQuestion->parents['type'], [";", ":"])) {
            foreach (Question::model()->findAllByAttributes(array('parent_qid' => $aQuestionAttributes['parent_qid'], 'scale_id' => ($oQuestion->parents['type'] == '1' ? 2 : 1))) as $oScaleSubquestion) {
                $tempFieldname = $fieldname.'_'.$oScaleSubquestion->title;
                $aQuestionAttributes['answervalues'][$oScaleSubquestion->title] = isset($oResponses[$tempFieldname]) ? $oResponses[$tempFieldname] : null;
            }
        }

        return $aQuestionAttributes;
    }

    public function getPrintAnswersArray($sSRID, $sLanguageCode, $bHonorConditions = true)
    {

        $oSurvey = self::$survey;
        $aGroupArray = array();
        $oResponses = SurveyDynamic::model($oSurvey->sid)->findByAttributes(array('id'=>$sSRID));     
        $oGroupList = $oSurvey->groups;

        foreach ($oGroupList as $oSurveyGroup) {

            if (!(LimeExpressionManager::GroupIsRelevant($oSurveyGroup->gid) || $bHonorConditions == false)) {
                continue; 
            }

            $aAnswersArray = array();

            foreach ($oSurveyGroup->questions as $oQuestion) {
                $aQuestionArray = $this->getQuestionArray($oQuestion, $oResponses, $bHonorConditions);

                if ($aQuestionArray === false) {
                    continue;
                }

                $aAnswersArray[$oQuestion->qid] = $aQuestionArray;
            }

            $aGroupAttributes = $oSurveyGroup->attributes;
            $aGroupAttributes['answerArray'] = $aAnswersArray;
            $aGroupAttributes['debug'] = $oResponses->attributes;
            $aGroupArray[$oSurveyGroup->gid] = $aGroupAttributes;
            
        }

        return $aGroupArray;
    }
}

