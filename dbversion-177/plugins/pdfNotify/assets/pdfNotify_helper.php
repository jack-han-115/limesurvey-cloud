<?php

/**
 * PDF Notification Copyright by Kai Ravesloot
 * User: kairavesloot
 * Date: 25.03.14
 * Time: 11:29
 */
class pdfNotify_helper
{
    public $surveyId;
    public $pluginId;

    public function __construct($surveyId, $pluginId)
    {
        $this->surveyId = $surveyId;
        $this->pluginId = $pluginId;
    }

    /**
     * this function get the settings for a plugin for certain survey
     * @param $surveyId
     * @param $pluginId
     * @param array $settings
     * @return array
     */
    public static function getPluginSettings($surveyId, $pluginId)
    {

        $data = array();

        $beforeSurveySettings = new PluginEvent('beforeSurveySettings');
        $beforeSurveySettings->set('survey', $surveyId);
        App()->getPluginManager()->dispatchEvent($beforeSurveySettings);
        $data = $beforeSurveySettings->get('surveysettings'); // todo is it possible to filter the plugin-id direct
        $data = $data[$pluginId]['settings']; // todo is it possible to filter the plugin-id direct
        return $data;
    }

    /**
     *
     * this function replace the tokens in the subject line for email
     * @param $surveyId
     * @param $survey_response
     * @param $subject
     * @return mixed
     */
    public static function tokenReplacement($surveyId, $survey_response, $subject)
    {
        // $this->subject = $subject;
        preg_match_all("/\[(.*?)\]/", $subject, $tokens);
        foreach ($tokens[1] as $value) {
            if (array_key_exists($value, $survey_response)) {
                $string = substr($survey_response[$value], 0, 100);
                $subject = preg_replace('/\[' . $value . '\]/', $string, $subject);
            } elseif ($value == 'surveyId') {
                $subject = preg_replace('/\[' . $value . '\]/', $surveyId, $subject); // insert response ID
            }
        }
        return $subject;
    }

    /**
     * This function handles the email input field from plugin and do token replacements
     * @param $emailField
     * @param $survey_response
     * @param $lang
     * @return array
     */

    public function EmailAddressProcess($emailField, $survey_response, $lang, $fSid)
    {
        $aEmails = explode(',', $emailField);
        foreach ($aEmails as $key => $email) {
            preg_match_all("/\[(.*?)\]/", $email, $token); // find tokens
            $isToken = array_filter($token);

            if (!empty($isToken)) {
                $wildcard = $token[1][0];
                // case 1 email adresses in Questiontype single choice
                if ($wildcard == 'pdfnotifyDB') {
                    $qid = array();
                    $qid = $this->getQid($wildcard, $fSid);
                    $qid = $qid['qid'];
                    if ($qid) {
                        $row = $survey_response['pdfnotifyRE'];
                        $text = $this->getAnswerText($qid, $row, $lang);
                        $aEmails[$key] = $text['answer'];
                    }
                }
                // case 2 email address from textfield
                if(!is_numeric($survey_response['pdfnotifyRE'])){
                //if (array_key_exists($wildcard, $survey_response)) {
                    $newEmail = $survey_response[$wildcard];
                    preg_replace('/\s+/', '', $newEmail);
                    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                        $aEmails[$key] = '';
                    } else {
                        $aEmails[$key] = $newEmail;
                    }
                }
            } else { // Case 3 email from plugin input field
                $email = preg_replace('/\s+/', '', $email);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $aEmails[$key] = '';
                } else {
                    $aEmails[$key] = $email;
                }
            }
        }
        return $aEmails;
    }

    /**
     * Validate an email address.
     * Provide email address (raw input)
     * Returns true if the email address has the email
     * address format and the domain exists.
     * todo delete
     */
    public static function validEmail($email)
    {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }


    /**
     *
     * Creates an array with details on a particular response for display purposes
     * Used in Print answers, Detailed response view and Detailed admin notification email
     *
     * @param mixed $iSurveyID
     * @param mixed $iResponseID
     * @param mixed $sLanguageCode
     * @param boolean $bHonorConditions Apply conditions
     */
    public function getFullResponseTable($iSurveyID, $iResponseID, $sLanguageCode, $bHonorConditions = TRUE)
    {
        $aFieldMap = createFieldMap($iSurveyID, 'full', FALSE, FALSE, $sLanguageCode);
        $oLanguage = new Limesurvey_lang($sLanguageCode);

        //Get response data
        $idrow = SurveyDynamic::model($iSurveyID)
            ->findByAttributes(array('id' => $iResponseID));

        // Create array of non-null values - those are the relevant ones
        $aRelevantFields = array();

        foreach ($aFieldMap as $sKey => $fname) {
            if (LimeExpressionManager::QuestionIsRelevant($fname['qid']) || $bHonorConditions == FALSE) {
                $aRelevantFields[$sKey] = $fname;
            }
        }

        $aResultTable = array();
        $oldgid = 0;
        $oldqid = 0;
        foreach ($aRelevantFields as $sKey => $fname) {
            if (!empty($fname['qid'])) {
                $attributes = getQuestionAttributeValues($fname['qid']);
                if (getQuestionAttributeValue($attributes, 'hidden') == 1) {
                    continue;
                }
            }
            $question = $fname['question'];
            $subquestion = '';
            if (isset($fname['gid']) && !empty($fname['gid'])) {
                //Check to see if gid is the same as before. if not show group name
                if ($oldgid !== $fname['gid']) {
                    $oldgid = $fname['gid'];
                    if (LimeExpressionManager::GroupIsRelevant($fname['gid']) || $bHonorConditions == FALSE) {
                        $aResultTable['gid_' . $fname['gid']] = array($fname['group_name']);
                    }
                }
            }
            if (!empty($fname['qid'])) {
                if ($oldqid !== $fname['qid']) {
                    $oldqid = $fname['qid'];
                    if (isset($fname['subquestion']) || isset($fname['subquestion1']) || isset($fname['subquestion2'])) {
                        $aResultTable['qid_' . $fname['sid'] . 'X' . $fname['gid'] . 'X' . $fname['qid']] = array(
                            $fname['question'],
                            '',
                            ''
                        );
                    } else {
                        $answer = getExtendedAnswer($iSurveyID, $fname['fieldname'], $idrow[$fname['fieldname']], $oLanguage);
                        $aResultTable[$fname['fieldname']] = array($question, '', $answer);
                        continue;
                    }
                }
            } else {
                $answer = getExtendedAnswer($iSurveyID, $fname['fieldname'], $idrow[$fname['fieldname']], $oLanguage);
                $aResultTable[$fname['fieldname']] = array($question, '', $answer);
                continue;
            }
            if (isset($fname['subquestion'])) {
                $subquestion = "{$fname['subquestion']}";
            }

            if (isset($fname['subquestion1'])) {
                $subquestion = "{$fname['subquestion1']}";
            }

            if (isset($fname['subquestion2'])) {
                $subquestion .= "[{$fname['subquestion2']}]";
            }

            $answer = getExtendedAnswer($iSurveyID, $fname['fieldname'], $idrow[$fname['fieldname']], $oLanguage);
            $aResultTable[$fname['fieldname']] = array('', $subquestion, $answer);
        }
        return $aResultTable;
    }

    /** This function gets the qid by qcode
     * @param $qcode
     * @return mixed
     */
    public function getQid($qcode, $fSid)
    {
        $qid = Yii::app()->db->createCommand()
            ->select('qid')
            ->from(App()->getDb()->tablePrefix . 'questions')
            ->andwhere('title=:title', array(':title' => $qcode))
            ->andWhere('sid=:sid', array(':sid' => $fSid))
            ->queryRow();
        return $qid;
    }

    /**
     * This function gets the Email-Adress from the answer table
     * @param $qid
     * @param $row
     * @param $lang
     * @return mixed
     */
    public function getAnswerText($qid, $row, $lang)
    {
        $sEmail = Yii::app()->db->createCommand()
            ->select('answer')
            ->from(App()->getDb()->tablePrefix . 'answers')
            ->where('qid=:qid', array(':qid' => $qid))
            ->andWhere('code=:code', array(':code' => $row))
            ->andWhere('language=:lang', array(':lang' => $lang))
            ->queryRow();
        return $sEmail;
    }

}



