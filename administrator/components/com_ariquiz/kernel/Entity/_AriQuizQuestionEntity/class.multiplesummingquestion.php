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
    define('ZQUIZ_MSQ_DOC_TAG', 'answers');
    define('ZQUIZ_MSQ_ITEM_TAG', 'answer');
    define('ZQUIZ_MSQ_RANDOM_ATTR', 'random');
    define('ZQUIZ_MSQ_ID_ATTR', 'id');
    define('ZQUIZ_MSQ_SCORE_ATTR', 'score');
    AriKernel::import('Entity._AriQuizQuestionEntity.QuestionBase');
    AriKernel::import('Entity._AriQuizQuestionEntity._Templates.QuestionTemplates');
    class MultipleSummingQuestion extends AriQuizQuestionBase {
        function isScoreSpecific() {
            return true;
        }
        function calculateMaximumScore($score, $xml, $overrideXml = null) {
            $score = 0;
            $data = $this->getDataFromXml($xml, $overrideXml);
            if (is_array($data)) {
                foreach($data as $dataItem) {
                    if (isset($dataItem['tbxMSQScore'])) {
                        $answerScore = @intval($dataItem['tbxMSQScore'], 10);
                        if ($answerScore>0) $score+= $answerScore;
                    }
                }
            }
            return $score;
        }
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            $data = $this->getDataFromXml($xml, $decodeHtmlEntity);
            $clientData = array();
            if ($data) {
                $extraData = $this->getExtraDataFromXml($xml);
                foreach($data as $dataItem) {
                    $item = array();
                    foreach($dataItem as $key=>$value) {
                        if ($key != 'tbxMSQScore') {
                            $item[$key] = $value;
                        }
                    }
                    $clientData[] = $item;
                }
                if ($extraData['randomizeOrder']) shuffle($clientData);
                $clientData = $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
            }
            return $clientData;
        }
        function applyUserData($data, $userXml, $decodeHtmlEntity = false) {
            if (empty($data) || empty($userXml)) return $data;
            $userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
            if (is_array($userData) && count($userData) >0) {
                $userAns = array();
                foreach($userData as $userDataItem) {
                    $userAns[] = $userDataItem['hidQueId'];
                }
                if (count($userAns) >0) {
                    for ($i = 0;$i<count($data);$i++) {
                        $dataItem = &$data[$i];
                        if (in_array($dataItem['hidQueId'], $userAns)) {
                            $dataItem['selected'] = true;
                        }
                    }
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
                if ($xmlDoc->name() != ZQUIZ_MSQ_DOC_TAG) return $data;
                $childs = $xmlDoc->children();
                if (!empty($childs)) {
                    $xDataMap = array();
                    if (!empty($overrideXml)) {
                        $xData = $this->getDataFromXml($overrideXml, $htmlSpecialChars);
                        if (!empty($xData)) {
                            foreach($xData as $xDataItem) {
                                $xDataMap[$xDataItem['hidQueId']] = $xDataItem['tbxMSQScore'];
                            }
                        }
                    }
                    $data = array();
                    foreach($childs as $child) {
                        if ($child->name() != ZQUIZ_MSQ_ITEM_TAG) continue;
                        $answer = $child->data();
                        if ($htmlSpecialChars) $answer = AriWebHelper::htmlSpecialChars($answer);
                        $id = $child->attributes(ZQUIZ_MSQ_ID_ATTR);
                        $score = @intval($child->attributes(ZQUIZ_MSQ_SCORE_ATTR), 10);
                        $data[] = array('tbxAnswer'=>$answer, 'hidQueId'=>$id, 'tbxMSQScore'=>$score, 'hidScore'=>$score);
                        if (isset($xDataMap[$id])) {
                            $dataItem['chkOverride'] = true;
                            $dataItem['tbxMSQScore'] = $xDataMap[$id];
                        }
                    }
                }
            }
            return $data;
        }
        function getExtraDataFromXml($xml) {
            $data = array('randomizeOrder'=>false);
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_MSQ_DOC_TAG) return $data;
                $data['randomizeOrder'] = AriUtils::parseValueBySample($xmlDoc->attributes(ZQUIZ_MSQ_RANDOM_ATTR), false);
            }
            return $data;
        }
        function getFrontXml($questionId) {
            $selectedAnswers = AriRequest::getParam('selectedAnswer_'.$questionId, array());
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_MSQ_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            if (!is_array($selectedAnswers)) {
                $selectedAnswers = array($selectedAnswers);
            }
            foreach($selectedAnswers as $answerId) {
                $answerId = trim($answerId);
                if (!empty($answerId)) {
                    $xmlItem = &$xmlDoc->addChild(ZQUIZ_MSQ_ITEM_TAG);
                    $xmlItem->addAttribute(ZQUIZ_MSQ_ID_ATTR, $answerId);
                }
            }
            return $xmlDoc->toString();
        }
        function getScore($xml, $baseXml, $score, $overrideXml = null) {
            $userScore = 0;
            if (!empty($xml) && !empty($baseXml)) {
                $data = $this->getDataFromXml($baseXml);
                $scoreMapping = array();
                if (!empty($data)) {
                    foreach($data as $dataItem) {
                        if (!empty($dataItem['tbxMSQScore'])) {
                            $scoreMapping[$dataItem['hidQueId']] = @intval($dataItem['tbxMSQScore'], 10);
                        }
                    }
                }
                if (count($scoreMapping) >0) {
                    $xData = $this->getDataFromXml($xml);
                    if ($xData) {
                        foreach($xData as $dataItem) {
                            $selId = $dataItem['hidQueId'];
                            if (key_exists($selId, $scoreMapping)) {
                                $userScore+= $scoreMapping[$selId];
                            }
                        }
                    }
                    if ($userScore<0) $userScore = 0;
                else if ($userScore>$score) $userScore = $score;
            }
        }
        return $userScore;
    }
    function getOverrideXml() {
        $xmlStr = null;
        return $xmlStr;
    }
    function getXml() {
        $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxMSQScore', 'hidQueId'), null, true);
        $xmlStr = null;
        if (!empty($answers)) {
            $idList = array();
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_MSQ_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            $randomizeOrder = AriUtils::parseValueBySample(AriRequest::getParam('chkMSQRandomizeOrder', null), false);
            if ($randomizeOrder) {
                $xmlDoc->addAttribute(ZQUIZ_MSQ_RANDOM_ATTR, 'true');
            }
            foreach($answers as $answerItem) {
                $answer = trim($answerItem['tbxAnswer']);
                if (!strlen($answer)) continue;
                $xmlItem = &$xmlDoc->addChild(ZQUIZ_MSQ_ITEM_TAG);
                $xmlItem->setData(AriWebHelper::translateValue($answer));
                $score = AriUtils::parseValueBySample(AriUtils::getParam($answerItem, 'tbxMSQScore', 0), 0);
                if ($score != 0) {
                    $xmlItem->addAttribute(ZQUIZ_MSQ_SCORE_ATTR, $score);
                }
                $id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) ? $answerItem['hidQueId'] : uniqid('', true);
                $xmlItem->addAttribute(ZQUIZ_MSQ_ID_ATTR, $id);
            }
            $xmlStr = $xmlDoc->toString();
        }
        return $xmlStr;
    }
    };
?>