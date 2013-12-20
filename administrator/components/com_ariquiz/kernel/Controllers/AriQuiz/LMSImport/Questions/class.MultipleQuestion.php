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
    AriKernel::import('Xml.SimpleXmlHelper');
    AriKernel::import('Web.Controls.Advanced.MultiplierControls');
    class AriQuizLMSImportMultipleQuestion extends AriQuizLMSImportQuestionBase {
        var $_type = 'MultipleQuestion';
        function getXml($questionId, &$questionNode, &$optionsNode) {
            $request = $_REQUEST;
            $options = $this->getOptions($questionId, $optionsNode);
            $i = 0;
            foreach($options as $option) {
                $answer = $option['answer'];
                $correct = $option['correct'];
                $_REQUEST['tbxAnswer_'.$i] = $answer;
                $_REQUEST['tblQueContainer_hdnstatus_'.$i] = '';
                if ($correct) $_REQUEST['cbCorrect_'.$i] = 'true';
                ++$i;
            }
            $xml = $this->_question->getXml();
            $_REQUEST = $request;
            return $xml;
        }
        function getOptions($questionId, &$optionsNode) {
            $options = array();
            if ($questionId<1 || empty($optionsNode)) return $options;
            $choicesNode = &AriSimpleXmlHelper::getSingleNode($optionsNode, 'choice_data');
            if (empty($choicesNode)) return $options;
            $choicesNode = &AriSimpleXmlHelper::getNode($choicesNode, 'quest_choice');
            if (empty($choicesNode)) return $options;
            foreach($choicesNode as $choiceNode) {
                if ($choiceNode->attributes('c_question_id') != $questionId) continue;
                $answer = AriSimpleXmlHelper::getData($choiceNode, 'choice_text');
                if (empty($answer)) continue;
                $options[@intval($choiceNode->attributes('ordering'), 10) ] = array('answer'=>$answer, 'correct'=>AriUtils::parseValueBySample($choiceNode->attributes('c_right'), false));
            }
            return $options;
        }
    };
?>