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
    define('ZQUIZ_MQ_DOC_TAG', 'answers');
    define('ZQUIZ_MQ_ITEM_TAG', 'answer');
    define('ZQUIZ_MQ_CORRECT_ATTR', 'correct');
    define('ZQUIZ_MQ_RANDOM_ATTR', 'random');
    define('ZQUIZ_MQ_ID_ATTR', 'id');
    define('ZQUIZ_MQ_SCORES_TAG', 'scores');
    define('ZQUIZ_MQ_SCORE_TAG', 'score');
    define('ZQUIZ_MQ_SCORE_ATTR', 'score');
    define('ZQUIZ_MQ_SCORE_ID_ATTR', 'id');
    define('ZQUIZ_MQ_SCORE_CORRECT_TAG', 'correct');
    AriKernel::import('Entity._AriQuizQuestionEntity.QuestionBase');
    AriKernel::import('Entity._AriQuizQuestionEntity._Templates.QuestionTemplates');
    class MultipleQuestion extends AriQuizQuestionBase {
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            $data = $this->getDataFromXml($xml, $decodeHtmlEntity);
            $clientData = array();
            if ($data) {
                $extraData = $this->getExtraDataFromXml($xml);
                foreach($data as $dataItem) {
                    $item = array();
                    foreach($dataItem as $key=>$value) {
                        if ($key != 'cbCorrect') {
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
        function getDataFromXml($xml, $htmlSpecialChars = TRUE) {
            $data = null;
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_MQ_DOC_TAG) return $data;
                $childs = $xmlDoc->children();
                if (!empty($childs)) {
                    $data = array();
                    foreach($childs as $child) {
                        if ($child->name() != ZQUIZ_MQ_ITEM_TAG) continue;
                        $answer = $child->data();
                        if ($htmlSpecialChars) $answer = AriWebHelper::htmlSpecialChars($answer);
                        $data[] = array('tbxAnswer'=>$answer, 'hidQueId'=>$child->attributes(ZQUIZ_MQ_ID_ATTR), 'cbCorrect'=>$child->attributes(ZQUIZ_MQ_CORRECT_ATTR));
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
                if ($xmlDoc->name() != ZQUIZ_MQ_DOC_TAG) return $data;
                $data['randomizeOrder'] = AriUtils::parseValueBySample($xmlDoc->attributes(ZQUIZ_MQ_RANDOM_ATTR), false);
            }
            return $data;
        }
        function getScoreDataFromXml($xml, $overrideXml = null) {
            $data = null;
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_MQ_DOC_TAG) return $data;
                $tagName = ZQUIZ_MQ_SCORES_TAG;
                if (!isset($xmlDoc->$tagName)) {
                    return $data;
                } else {
                    $scoreItems = $xmlDoc->$tagName;
                    if (!count($scoreItems)) return $data;
                }
                $scores = $xmlDoc->$tagName;
                $scores = $scores[0];
                $childs = $scores->children();
                if (!empty($childs)) {
                    $xDataMap = array();
                    if ($overrideXml) {
                        $xData = $this->getScoreDataFromXml($overrideXml);
                        if ($xData) {
                            foreach($xData as $xDataItem) {
                                $xDataMap[$xDataItem['id']] = $xDataItem['score'];
                            }
                        }
                    }
                    $data = array();
                    foreach($childs as $child) {
                        if ($child->name() != ZQUIZ_MQ_SCORE_TAG) continue;
                        $score = @intval($child->attributes(ZQUIZ_MQ_SCORE_ATTR), 10);
                        $id = $child->attributes(ZQUIZ_MQ_SCORE_ID_ATTR);
                        if ($score<1 || $score>100) continue;
                        $dataItem = array('id'=>$id, 'score'=>$score, 'bankScore'=>$score, 'correct'=>array());
                        $tagName = ZQUIZ_MQ_SCORE_CORRECT_TAG;
                        if (isset($child->$tagName) && count($child->$tagName) >0) {
                            $correctNodes = $child->$tagName;
                            foreach($correctNodes as $correctNode) {
                                $ansId = $correctNode->attributes(ZQUIZ_MQ_ID_ATTR);
                                if (!empty($id)) $dataItem['correct'][] = $ansId;
                            }
                        }
                        if (isset($xDataMap[$id])) {
                            $dataItem['override'] = true;
                            $dataItem['score'] = $xDataMap[$id];
                        }
                        $data[] = $dataItem;
                    }
                }
            }
            return $data;
        }
        function getFrontXml($questionId) {
            $selectedAnswers = AriRequest::getParam('selectedAnswer_'.$questionId, array());
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_MQ_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            if (!is_array($selectedAnswers)) {
                $selectedAnswers = array($selectedAnswers);
            }
            foreach($selectedAnswers as $answerId) {
                $answerId = trim($answerId);
                if (!empty($answerId)) {
                    $xmlItem = &$xmlDoc->addChild(ZQUIZ_MQ_ITEM_TAG);
                    $xmlItem->addAttribute(ZQUIZ_MQ_ID_ATTR, $answerId);
                }
            }
            return $xmlDoc->toString();
        }
        function getScore($xml, $baseXml, $score, $overrideXml = null) {
            $userScore = 0;
            if (!empty($xml) && !empty($baseXml)) {
                $data = $this->getDataFromXml($baseXml);
                $correctHashMap = array();
                if (!empty($data)) {
                    $correctIdList = array();
                    foreach($data as $dataItem) {
                        if (!empty($dataItem['cbCorrect'])) {
                            $correctIdList[] = $dataItem['hidQueId'];
                        }
                    }
                    $hash = $this->getIdListHash($correctIdList);
                    if (!is_null($hash)) $correctHashMap[$hash] = 100;
                }
                $scoreData = $this->getScoreDataFromXml($baseXml, $overrideXml);
                if (!empty($scoreData)) {
                    foreach($scoreData as $scoreDataItem) {
                        $percentScore = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
                        if ($percentScore<1) continue;
                        $correctList = AriUtils::getParam($scoreDataItem, 'correct', null);
                        $hash = $this->getIdListHash($correctList);
                        if (!isset($correctHashMap[$hash])) $correctHashMap[$hash] = $percentScore;
                    }
                }
                if (count($correctHashMap) >0) {
                    $userHash = null;
                    $xData = $this->getDataFromXml($xml);
                    if ($xData) {
                        $selIdList = array();
                        foreach($xData as $dataItem) {
                            $selIdList[] = $dataItem['hidQueId'];
                        }
                        $userHash = $this->getIdListHash($selIdList);
                    }
                    if (isset($correctHashMap[$userHash])) {
                        $scorePercent = $this->correctPercent($correctHashMap[$userHash]);
                        $userScore = round(($score*$scorePercent) /100);
                    }
                }
            }
            return $userScore;
        }
        function getIdListHash($idList) {
            $hash = null;
            if (is_array($idList) && count($idList) >0) {
                sort($idList);
                $hash = md5(join(' ', $idList));
            }
            return $hash;
        }
        function getOverrideXml() {
            $xmlStr = null;
            $scoreData = AriRequest::getParam('hidPercentScore');
            if (!empty($scoreData)) {
                $scoreData = AriJSONHelper::decode($scoreData);
                if (is_array($scoreData)) {
                    $xmlHandler = new AriSimpleXML();
                    $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_MQ_DOC_TAG));
                    $xmlDoc = $xmlHandler->document;
                    $scoresXmlItem = null;
                    foreach($scoreData as $scoreDataItem) {
                        $override = AriUtils::parseValueBySample(AriUtils::getParam($scoreDataItem, 'override', false), false);
                        if (!$override) continue;
                        $score = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
                        if ($score<1 || $score>100) continue;
                        $id = AriUtils::getParam($scoreDataItem, 'id', null);
                        if (empty($id)) continue;
                        if (is_null($scoresXmlItem)) $scoresXmlItem = &$xmlDoc->addChild(ZQUIZ_MQ_SCORES_TAG);
                        $xmlItem = &$scoresXmlItem->addChild(ZQUIZ_MQ_SCORE_TAG);
                        $xmlItem->addAttribute(ZQUIZ_MQ_SCORE_ATTR, $score);
                        $xmlItem->addAttribute(ZQUIZ_MQ_SCORE_ID_ATTR, $id);
                    }
                }
                $xmlStr = $xmlDoc->toString();
            }
            return $xmlStr;
        }
        function getXml() {
            $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'cbCorrect', 'hidQueId'), null, true);
            $xmlStr = null;
            if (!empty($answers)) {
                $idList = array();
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_MQ_DOC_TAG));
                $xmlDoc = $xmlHandler->document;
                $randomizeOrder = AriUtils::parseValueBySample(AriRequest::getParam('chkMQRandomizeOrder', null), false);
                if ($randomizeOrder) {
                    $xmlDoc->addAttribute(ZQUIZ_MQ_RANDOM_ATTR, 'true');
                }
                foreach($answers as $answerItem) {
                    $id = '';
                    $answer = trim($answerItem['tbxAnswer']);
                    if (strlen($answer)) {
                        $xmlItem = &$xmlDoc->addChild(ZQUIZ_MQ_ITEM_TAG);
                        $xmlItem->setData(AriWebHelper::translateValue($answer));
                        $correct = isset($answerItem['cbCorrect']);
                        if ($correct) {
                            $xmlItem->addAttribute(ZQUIZ_MQ_CORRECT_ATTR, 'true');
                        }
                        $id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) ? $answerItem['hidQueId'] : uniqid('', TRUE);
                        $xmlItem->addAttribute(ZQUIZ_MQ_ID_ATTR, $id);
                    }
                    $idList[] = $id;
                }
                $scoreData = AriRequest::getParam('hidPercentScore');
                if (!empty($scoreData)) {
                    $scoreData = AriJSONHelper::decode($scoreData);
                    if (is_array($scoreData)) {
                        $scoresXmlItem = null;
                        foreach($scoreData as $scoreDataItem) {
                            $score = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
                            if ($score<1 || $score>100) continue;
                            $id = AriUtils::getParam($scoreDataItem, 'id', null);
                            if (empty($id)) $id = uniqid('mqs_', true);
                            if (is_null($scoresXmlItem)) $scoresXmlItem = &$xmlDoc->addChild(ZQUIZ_MQ_SCORES_TAG);
                            $xmlItem = &$scoresXmlItem->addChild(ZQUIZ_MQ_SCORE_TAG);
                            $xmlItem->addAttribute(ZQUIZ_MQ_SCORE_ATTR, $score);
                            $xmlItem->addAttribute(ZQUIZ_MQ_SCORE_ID_ATTR, $id);
                            $correctList = AriUtils::getParam($scoreDataItem, 'correct', null);
                            if (!is_array($correctList)) continue;
                            for ($i = 0;$i<count($correctList) && $i<count($answers);$i++) {
                                if ($correctList[$i]) {
                                    $id = $idList[$i];
                                    if (!empty($id)) {
                                        $correctXmlItem = &$xmlItem->addChild(ZQUIZ_MQ_SCORE_CORRECT_TAG);
                                        $correctXmlItem->addAttribute(ZQUIZ_MQ_ID_ATTR, $id);
                                    }
                                }
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