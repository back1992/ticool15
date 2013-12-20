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
    define('ZQUIZ_SQ_DOC_TAG', 'answers');
    define('ZQUIZ_SQ_ITEM_TAG', 'answer');
    define('ZQUIZ_SQ_RANDOM_ATTR', 'random');
    define('ZQUIZ_SQ_VIEW_ATTR', 'view');
    define('ZQUIZ_SQ_ID_ATTR', 'id');
    define('ZQUIZ_SQ_CORRECT_ATTR', 'correct');
    define('ZQUIZ_SQ_SCORE_ATTR', 'score');
    define('ZQUIZ_SQ_VIEWTYPE_RADIO', '0');
    define('ZQUIZ_SQ_VIEWTYPE_DROPDOWN', '1');
    AriKernel::import('Entity._AriQuizQuestionEntity.QuestionBase');
    AriKernel::import('Entity._AriQuizQuestionEntity._Templates.QuestionTemplates');
    AriKernel::import('Web.Controls.Advanced.MultiplierControls');
    class SingleQuestion extends AriQuizQuestionBase {
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            $data = $this->getDataFromXml($xml, $decodeHtmlEntity);
            $clientData = array();
            if ($data) {
                $extraData = $this->getExtraDataFromXml($xml);
                $ignoreIndex = array('tbxScore', 'hidScore', 'chkOverride');
                $queData = array();
                foreach($data as $dataItem) {
                    $item = array();
                    foreach($dataItem as $key=>$value) {
                        if (!in_array($key, $ignoreIndex)) {
                            $item[$key] = $value;
                        }
                    }
                    $queData[] = $item;
                }
                if ($extraData['randomizeOrder']) shuffle($queData);
                $clientData['data'] = $queData;
                $clientData['view'] = $extraData['view'];
                $clientData = $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
            }
            return $clientData;
        }
        function applyUserData($data, $userXml, $decodeHtmlEntity = false) {
            if (empty($data['data']) || empty($userXml)) return $data;
            $userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
            if (is_array($userData) && count($userData) >0) {
                $queData = &$data['data'];
                $userDataItem = $userData[0];
                $id = $userDataItem['hidQueId'];
                for ($i = 0;$i<count($queData);$i++) {
                    $dataItem = &$queData[$i];
                    if ($dataItem['hidQueId'] == $id) {
                        $dataItem['selected'] = true;
                        break;
                    }
                }
            }
            return $data;
        }
        function getExtraDataFromXml($xml) {
            $data = array('randomizeOrder'=>false, 'view'=>0);
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_SQ_DOC_TAG) return $data;
                $data['randomizeOrder'] = AriUtils::parseValueBySample($xmlDoc->attributes(ZQUIZ_SQ_RANDOM_ATTR), false);
                $data['view'] = AriUtils::parseValueBySample($xmlDoc->attributes(ZQUIZ_SQ_VIEW_ATTR), '');
            }
            return $data;
        }
        function getDataFromXml($xml, $htmlSpecialChars = true, $overrideXml = null) {
            $data = null;
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_SQ_DOC_TAG) return $data;
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
                        $id = $child->attributes(ZQUIZ_SQ_ID_ATTR);
                        $score = $child->attributes(ZQUIZ_SQ_SCORE_ATTR);
                        $dataItem = array('tbxAnswer'=>$answer, 'hidQueId'=>$id, 'hidCorrect'=>$child->attributes(ZQUIZ_SQ_CORRECT_ATTR), 'tbxScore'=>$score, 'hidScore'=>$score);
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
            $selectedAnswer = AriRequest::getParam('selectedAnswer_'.$questionId, '');
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_SQ_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            if (!empty($selectedAnswer)) {
                $xmlItem = &$xmlDoc->addChild(ZQUIZ_SQ_ITEM_TAG);
                $xmlItem->addAttribute(ZQUIZ_SQ_ID_ATTR, $selectedAnswer);
            }
            return $xmlDoc->toString();
        }
        function getScore($xml, $baseXml, $score, $overrideXml = null) {
            $userScore = 0;
            if (!empty($xml) && !empty($baseXml)) {
                $data = $this->getDataFromXml($baseXml);
                $scoreMap = array();
                $correctId = null;
                if (!empty($data)) {
                    foreach($data as $dataItem) {
                        if (!empty($dataItem['hidCorrect'])) {
                            $correctId = $dataItem['hidQueId'];
                        }
                        if (!empty($dataItem['tbxScore'])) {
                            $scoreMap[$dataItem['hidQueId']] = @intval($dataItem['tbxScore']);
                        }
                    }
                }
                if (!empty($overrideXml)) {
                    $data = $this->getDataFromXml($overrideXml);
                    if (!empty($data)) {
                        foreach($data as $dataItem) {
                            $id = $dataItem['hidQueId'];
                            $scoreMap[$id] = @intval($dataItem['tbxScore']);
                        }
                    }
                }
                if ($correctId) $scoreMap[$correctId] = 100;
                $xData = $this->getDataFromXml($xml);
                if (!empty($xData) && isset($xData[0]['hidQueId']) && key_exists($xData[0]['hidQueId'], $scoreMap)) {
                    $scorePercent = $this->correctPercent($scoreMap[$xData[0]['hidQueId']]);
                    $userScore = round(($score*$scorePercent) /100);
                }
            }
            return $userScore;
        }
        function getOverrideXml() {
            $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'chkOverride', 'hidQueId'), null, true);
            $xmlStr = null;
            if (!empty($answers)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_SQ_DOC_TAG));
                $xmlDoc = $xmlHandler->document;
                foreach($answers as $answerItem) {
                    $id = isset($answerItem['hidQueId']) ? $answerItem['hidQueId'] : null;
                    if (empty($id)) continue;
                    if ($answerItem['chkOverride']) {
                        $xmlItem = &$xmlDoc->addChild(ZQUIZ_SQ_ITEM_TAG);
                        $xmlItem->addAttribute(ZQUIZ_SQ_ID_ATTR, $id);
                        $score = @intval(trim($answerItem['tbxScore']), 10);
                        $xmlItem->addAttribute(ZQUIZ_SQ_SCORE_ATTR, $score);
                    }
                }
                $xmlStr = $xmlDoc->toString();
            }
            return $xmlStr;
        }
        function getXml() {
            $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'chkOverride', 'cbCorrect', 'hidQueId', 'hidCorrect'), null, true);
            $xmlStr = null;
            if (!empty($answers)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_SQ_DOC_TAG));
                $xmlDoc = $xmlHandler->document;
                $randomizeOrder = AriUtils::parseValueBySample(AriRequest::getParam('chkSQRandomizeOrder', null), false);
                if ($randomizeOrder) {
                    $xmlDoc->addAttribute(ZQUIZ_SQ_RANDOM_ATTR, 'true');
                }
                $view = AriUtils::parseValueBySample(AriRequest::getParam('ddlSQView', ''), '');
                if ($view != ZQUIZ_SQ_VIEWTYPE_RADIO) {
                    $xmlDoc->addAttribute(ZQUIZ_SQ_VIEW_ATTR, $view);
                }
                $isSetCorrect = false;
                foreach($answers as $answerItem) {
                    $answer = trim($answerItem['tbxAnswer']);
                    if (strlen($answer)) {
                        $xmlItem = &$xmlDoc->addChild(ZQUIZ_SQ_ITEM_TAG);
                        $xmlItem->setData(AriWebHelper::translateValue($answer));
                        $id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) ? $answerItem['hidQueId'] : uniqid('', true);
                        $xmlItem->addAttribute(ZQUIZ_SQ_ID_ATTR, $id);
                        if (!$isSetCorrect && !empty($answerItem['hidCorrect'])) {
                            $xmlItem->addAttribute(ZQUIZ_SQ_CORRECT_ATTR, 'true');
                            $isSetCorrect = true;
                        } else {
                            $score = @intval(trim($answerItem['tbxScore']), 10);
                            if ($score>0) {
                                $xmlItem->addAttribute(ZQUIZ_SQ_SCORE_ATTR, $score);
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