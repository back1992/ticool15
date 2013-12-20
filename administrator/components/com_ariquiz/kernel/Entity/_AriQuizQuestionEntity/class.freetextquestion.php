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
    define('ZQUIZ_FT_DOC_TAG', 'answers');
    define('ZQUIZ_FT_ITEM_TAG', 'answer');
    define('ZQUIZ_FT_CI_ATTR', 'ci');
    define('ZQUIZ_FT_ID_ATTR', 'id');
    define('ZQUIZ_FT_SCORE_ATTR', 'score');
    AriKernel::import('String.String');
    AriKernel::import('Entity._AriQuizQuestionEntity.QuestionBase');
    AriKernel::import('Entity._AriQuizQuestionEntity._Templates.QuestionTemplates');
    class FreeTextQuestion extends AriQuizQuestionBase {
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            $data = $this->applyUserData(null, $userXml, $decodeHtmlEntity);
            return $data;
        }
        function applyUserData($data, $userXml, $decodeHtmlEntity = false) {
            if (empty($userXml)) return $data;
            $userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
            if (is_array($userData) && count($userData) >0) {
                $tbxAnswer = $userData[0]['tbxAnswer'];
                if ($tbxAnswer) {
                    if (empty($data)) $data = array();
                    $data['answer'] = $tbxAnswer;
                }
            }
            return $data;
        }
        function getDataFromXml($xml, $htmlSpecialChars = true, $overrideXml = null) {
            $data = null;
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_FT_DOC_TAG) return $data;
                $childs = $xmlDoc->children();
                if (!empty($childs)) {
                    $xDataMap = array();
                    if (!empty($overrideXml)) {
                        $xData = $this->getDataFromXml($overrideXml, $htmlSpecialChars);
                        if (!empty($xData)) {
                            foreach($xData as $xDataItem) {
                                $xDataMap[$xDataItem['hidQueId']] = $xDataItem['tbxScore'];
                            }
                        }
                    }
                    $data = array();
                    foreach($childs as $child) {
                        $answer = $child->data();
                        if ($htmlSpecialChars) $answer = AriWebHelper::htmlSpecialChars($answer);
                        $id = $child->attributes(ZQUIZ_FT_ID_ATTR);
                        $score = $child->attributes(ZQUIZ_FT_SCORE_ATTR);
                        $score = !is_null($score) ? @intval($score, 10) : 100;
                        $dataItem = array('tbxAnswer'=>$answer, 'hidQueId'=>$id, 'cbCI'=>$child->attributes(ZQUIZ_FT_CI_ATTR), 'tbxScore'=>$score, 'hidScore'=>$score);
                        if (isset($xDataMap[$id])) {
                            $dataItem['chkOverride'] = true;
                            $dataItem['tbxScore'] = $xDataMap[$id];
                        }
                        $data[] = $dataItem;
                    }
                }
            }
            return $data;
        }
        function getFrontXml($questionId) {
            $tbxAnswer = AriRequest::getParam('tbxAnswer_'.$questionId, '');
            if (get_magic_quotes_gpc()) {
                $tbxAnswer = stripslashes($tbxAnswer);
            }
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_FT_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            $xmlItem = &$xmlDoc->addChild(ZQUIZ_FT_ITEM_TAG);
            $xmlItem->setData($tbxAnswer);
            return $xmlDoc->toString();
        }
        function getScore($xml, $baseXml, $score, $overrideXml = null) {
            $userScore = 0;
            if (!empty($xml) && !empty($baseXml)) {
                $data = $this->getDataFromXml($baseXml);
                $xData = $this->getDataFromXml($xml);
                $answer = !empty($xData) && count($xData) >0 ? trim($xData[0]['tbxAnswer']) : '';
                $xDataMap = array();
                if (!empty($overrideXml)) {
                    $oData = $this->getDataFromXml($overrideXml);
                    if ($oData) {
                        foreach($oData as $dataItem) {
                            $xDataMap[$dataItem['hidQueId']] = $dataItem['tbxScore'];
                        }
                    }
                }
                if (!empty($data) && !empty($answer)) {
                    foreach($data as $dataItem) {
                        $id = $dataItem['hidQueId'];
                        $correctAnswer = $dataItem['tbxAnswer'];
                        if (!empty($dataItem['cbCI'])) {
                            $isCorrect = (utf8_strcasecmp($answer, $correctAnswer) === 0);
                        } else {
                            $isCorrect = strcmp($correctAnswer, $answer) === 0;
                        }
                        if ($isCorrect) {
                            $scorePercent = isset($xDataMap[$id]) ? $xDataMap[$id] : $dataItem['tbxScore'];
                            $scorePercent = $this->correctPercent(@intval($scorePercent, 10));
                            $userScore = round(($score*$scorePercent) /100);
                            break;
                        }
                    }
                }
            }
            return $userScore;
        }
        function getOverrideXml() {
            $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'chkOverride', 'hidQueId'), null, true);
            $xmlStr = null;
            if (!empty($answers)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_FT_DOC_TAG));
                $xmlDoc = $xmlHandler->document;
                foreach($answers as $answerItem) {
                    $id = isset($answerItem['hidQueId']) ? $answerItem['hidQueId'] : null;
                    if (empty($id)) continue;
                    if ($answerItem['chkOverride']) {
                        $xmlItem = &$xmlDoc->addChild(ZQUIZ_FT_ITEM_TAG);
                        $xmlItem->addAttribute(ZQUIZ_FT_ID_ATTR, $id);
                        $score = trim($answerItem['tbxScore']);
                        if (strlen($score) >0) {
                            $score = @intval($score, 10);
                            if ($score>-1 && $score<100) {
                                $xmlItem->addAttribute(ZQUIZ_FT_SCORE_ATTR, $score);
                            }
                        }
                    }
                }
                $xmlStr = $xmlDoc->toString();
            }
            return $xmlStr;
        }
        function getXml() {
            $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'cbCI', 'hidQueId'), null, true);
            $xmlStr = null;
            if (!empty($answers)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_FT_DOC_TAG));
                $xmlDoc = $xmlHandler->document;
                foreach($answers as $answerItem) {
                    $answer = trim($answerItem['tbxAnswer']);
                    if (strlen($answer)) {
                        $xmlItem = &$xmlDoc->addChild(ZQUIZ_FT_ITEM_TAG);
                        $xmlItem->setData(AriWebHelper::translateValue($answer));
                        if ($answerItem['cbCI']) {
                            $xmlItem->addAttribute(ZQUIZ_FT_CI_ATTR, 'true');
                        }
                        $id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) ? $answerItem['hidQueId'] : uniqid('', TRUE);
                        $xmlItem->addAttribute(ZQUIZ_FT_ID_ATTR, $id);
                        $score = trim($answerItem['tbxScore']);
                        if (strlen($score) >0) {
                            $score = @intval($score, 10);
                            if ($score>-1 && $score<100) {
                                $xmlItem->addAttribute(ZQUIZ_FT_SCORE_ATTR, $score);
                            }
                        }
                    }
                }
                $xmlStr = $xmlDoc->toString();
            }
            return $xmlStr;
        }
    };
?>