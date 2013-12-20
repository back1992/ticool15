<?php
    /**
    ARI Soft copyright
    * Copyright (C) 2008 ARI Soft.
    * All Rights Reserved.  No use, copying or distribution of this
    * work may be made except in accordance with a valid license
    * agreement from ARI Soft. This notice must be included on
    * all copies, modifications and derivatives of this work.
    *
    * ARI Soft products are provided "as is" without warranty of
    * any kind, either expressed or implied. In no event shall our
    * juridical person be liable for any damages including, but
    * not limited to, direct, indirect, special, incidental or
    * consequential damages or other losses arising out of the use
    * of or inability to use our products.
    *
    *
    */
    defined('ARI_FRAMEWORK_LOADED') or die ('Direct Access to this location is not allowed.');
    AriKernel::import('Web.JSON.JSONHelper');
    class AriQuizCSVImportMultipleQuestion extends AriQuizCSVImportQuestionBase {
        var $_type = 'MultipleQuestion';
        function getXml($data) {
            $request = $_REQUEST;
            $random = AriUtils::parseValueBySample(AriUtils::getParam($data, 'Randomize'), false);
            if ($random) $_REQUEST['chkMQRandomizeOrder'] = '1';
            $childs = $data['_Childs'];
            $i = 0;
            foreach($childs as $child) {
                $answer = trim(AriUtils::getParam($child, 'Answers', ''));
                if (empty($answer)) continue;
                $score = intval(AriUtils::getParam($child, 'Score'), 10);
                $correct = AriUtils::parseValueBySample(AriUtils::getParam($child, 'Correct'), false);
                $_REQUEST['tbxAnswer_'.$i] = $answer;
                $_REQUEST['tblQueContainer_hdnstatus_'.$i] = '';
                if ($correct) $_REQUEST['cbCorrect_'.$i] = 'true';
                ++$i;
            }
            $i = 1;
            $additionalData = array();
            while (array_key_exists('Correct_'.$i, $data) && array_key_exists('Score_'.$i, $data)) {
                $correctList = array();
                $score = intval(AriUtils::getParam($data, 'Score_'.$i), 10);
                $correctKey = 'Correct_'.$i;
                foreach($childs as $child) {
                    $answer = trim(AriUtils::getParam($child, 'Answers', ''));
                    if (empty($answer)) continue;
                    $correct = AriUtils::parseValueBySample(AriUtils::getParam($child, $correctKey), false);
                    $correctList[] = $correct;
                }
                $additionalData[] = array('correct'=>$correctList, 'override'=>false, 'score'=>$score);
                ++$i;
            }
            $_REQUEST['hidPercentScore'] = AriJSONHelper::encode($additionalData);
            $xml = $this->_question->getXml();
            $_REQUEST = $request;
            return $xml;
        }
    };
?>