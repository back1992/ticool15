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
    define('ZQUIZ_CQ_DOC_TAG', 'items');
    define('ZQUIZ_CQ_ITEM_TAG', 'item');
    define('ZQUIZ_CQ_RANDOM_ATTR', 'random');
    define('ZQUIZ_CQ_LBLITEM_TAG', 'label');
    define('ZQUIZ_CQ_ANSITEM_TAG', 'answer');
    define('ZQUIZ_CQ_ID_ATTR', 'id');
    AriKernel::import('Entity._AriQuizQuestionEntity.QuestionBase');
    AriKernel::import('Entity._AriQuizQuestionEntity._Templates.QuestionTemplates');
    class CorrelationQuestion extends AriQuizQuestionBase {
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            $data = $this->getDataFromXml($xml, $decodeHtmlEntity);
            $clientData = array('labels'=>array(), 'answers'=>array());
            if ($data) {
                $extraData = $this->getExtraDataFromXml($xml);
                foreach($data as $dataItem) {
                    $clientData['labels'][] = array('id'=>$dataItem['hidLabelId'], 'label'=>$dataItem['tbxLabel']);
                    $clientData['answers'][] = array('id'=>$dataItem['hidAnswerId'], 'answer'=>$dataItem['tbxAnswer']);
                }
                if ($extraData['randomizeOrder']) shuffle($clientData['labels']);
                shuffle($clientData['answers']);
                $clientData = $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
            }
            return $clientData;
        }
        function applyUserData($data, $userXml, $decodeHtmlEntity = false) {
            if (empty($data) || empty($userXml)) return $data;
            $userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
            if (is_array($userData) && count($userData) >0) {
                $correlations = array();
                foreach($userData as $userDataItem) {
                    $correlations[$userDataItem['hidLabelId']] = $userDataItem['hidAnswerId'];
                }
                $data['correlations'] = $correlations;
            }
            return $data;
        }
        function getDataFromXml($xml, $htmlSpecialChars = true) {
            $data = null;
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ZQUIZ_CQ_DOC_TAG) return $data;
                $childs = $xmlDoc->children();
                if (!empty($childs)) {
                    $data = array();
                    foreach($childs as $child) {
                        $answerTag = ZQUIZ_CQ_ANSITEM_TAG;
                        $labelTag = ZQUIZ_CQ_LBLITEM_TAG;
                        $answer = $child->$answerTag;
                        $answer = $answer ? $answer[0] : null;
                        $label = $child->$labelTag;
                        $label = $label ? $label[0] : null;
                        $answerStr = $answer->data();
                        $labelStr = $label->data();
                        if ($htmlSpecialChars) {
                            $answerStr = AriWebHelper::htmlSpecialChars($answerStr);
                            $labelStr = AriWebHelper::htmlSpecialChars($labelStr);
                        }
                        $data[] = array('tbxLabel'=>$labelStr, 'tbxAnswer'=>$answerStr, 'hidLabelId'=>$label->attributes(ZQUIZ_CQ_ID_ATTR), 'hidAnswerId'=>$answer->attributes(ZQUIZ_CQ_ID_ATTR));
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
                if ($xmlDoc->name() != ZQUIZ_CQ_DOC_TAG) return $data;
                $data['randomizeOrder'] = AriUtils::parseValueBySample($xmlDoc->attributes(ZQUIZ_CQ_RANDOM_ATTR), false);
            }
            return $data;
        }
        function getFrontXml($questionId) {
            $ddlVariant = AriRequest::getParam('ddlVariant_'.$questionId, array());
            return $this->_createFrontXml($ddlVariant);
        }
        function _createFrontXml($correlation) {
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_CQ_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            if (is_array($correlation)) {
                foreach($correlation as $key=>$value) {
                    if (get_magic_quotes_gpc()) {
                        $key = stripslashes($key);
                        $value = stripslashes($value);
                    }
                    $xmlItem = &$xmlDoc->addChild(ZQUIZ_CQ_ITEM_TAG);
                    $subXmlItem = &$xmlItem->addChild(ZQUIZ_CQ_ANSITEM_TAG);
                    $subXmlItem->addAttribute(ZQUIZ_CQ_ID_ATTR, $value);
                    $subXmlItem = &$xmlItem->addChild(ZQUIZ_CQ_LBLITEM_TAG);
                    $subXmlItem->addAttribute(ZQUIZ_CQ_ID_ATTR, $key);
                }
            }
            return $xmlDoc->toString();
        }
        function isCorrect($xml, $baseXml) {
            $isCorrect = false;
            if (!empty($xml) && !empty($baseXml)) {
                $data = $this->getDataFromXml($baseXml);
                $xData = $this->getDataFromXml($xml);
                if (is_array($data) && is_array($xData)) {
                    $prepareXData = array();
                    foreach($xData as $dataItem) {
                        $prepareXData[$dataItem['hidLabelId']] = $dataItem['hidAnswerId'];
                    }
                    $isCorrect = true;
                    foreach($data as $dataItem) {
                        $lblId = $dataItem['hidLabelId'];
                        $ansId = $dataItem['hidAnswerId'];
                        if (!key_exists($lblId, $prepareXData) || $prepareXData[$lblId] != $ansId) {
                            $isCorrect = false;
                            break;
                        }
                    }
                }
            }
            return $isCorrect;
        }
        function getXml() {
            $answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxLabel', 'hidQueId'), null, true);
            $xmlStr = null;
            if (!empty($answers)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ZQUIZ_CQ_DOC_TAG));
                $xmlDoc = $xmlHandler->document;
                $randomizeOrder = AriUtils::parseValueBySample(AriRequest::getParam('chkCQRandomizeOrder', null), false);
                if ($randomizeOrder) {
                    $xmlDoc->addAttribute(ZQUIZ_CQ_RANDOM_ATTR, 'true');
                }
                foreach($answers as $answerItem) {
                    $answer = trim($answerItem['tbxAnswer']);
                    $label = trim($answerItem['tbxLabel']);
                    if (strlen($answer) && strlen($label)) {
                        $xmlItem = &$xmlDoc->addChild(ZQUIZ_CQ_ITEM_TAG);
                        $subXmlItem = &$xmlItem->addChild(ZQUIZ_CQ_ANSITEM_TAG);
                        $subXmlItem->setData(AriWebHelper::translateValue($answer));
                        $id = isset($answerItem['hidAnswerId']) && !empty($answerItem['hidAnswerId']) ? $answerItem['hidAnswerId'] : uniqid('', true);
                        $subXmlItem->addAttribute(ZQUIZ_CQ_ID_ATTR, $id);
                        $subXmlItem = &$xmlItem->addChild(ZQUIZ_CQ_LBLITEM_TAG);
                        $subXmlItem->setData(AriWebHelper::translateValue($label));
                        $id = isset($answerItem['hidLabelId']) && !empty($answerItem['hidLabelId']) ? $answerItem['hidLabelId'] : uniqid('', true);
                        $subXmlItem->addAttribute(ZQUIZ_CQ_ID_ATTR, $id);
                    }
                }
                $xmlStr = $xmlDoc->toString();
            }
            return $xmlStr;
        }
    };
?>