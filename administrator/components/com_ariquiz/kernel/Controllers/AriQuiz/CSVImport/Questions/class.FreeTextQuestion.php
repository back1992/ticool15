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
    class AriQuizCSVImportFreeTextQuestion extends AriQuizCSVImportQuestionBase {
        var $_type = 'FreeTextQuestion';
        function getXml($data) {
            $request = $_REQUEST;
            $childs = $data['_Childs'];
            $correct = false;
            $i = 0;
            foreach($childs as $child) {
                $answer = trim(AriUtils::getParam($child, 'Answers', ''));
                if (empty($answer)) continue;
                $score = intval(AriUtils::getParam($child, 'Score'), 10);
                $ci = AriUtils::parseValueBySample(AriUtils::getParam($child, 'Case Insensitive'), false);
                $_REQUEST['tbxAnswer_'.$i] = $answer;
                $_REQUEST['tbxScore_'.$i] = $score;
                $_REQUEST['tblQueContainer_hdnstatus_'.$i] = '';
                if ($ci) $_REQUEST['cbCI_'.$i] = 'true';
                ++$i;
            }
            $xml = $this->_question->getXml();
            $_REQUEST = $request;
            return $xml;
        }
    };
?>