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
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Entity._AriQuizQuestionEntity.QuestionBase');
    AriKernel::import('Entity._AriQuizQuestionEntity._Templates.QuestionTemplates');
    define('ARI_QUIZ_HOTSPOT_DOC_TAG', 'answers');
    define('ARI_QUIZ_HOTSPOT_ITEM_TAG', 'answer');
    define('ARI_QUIZ_HOTSPOT_X1', 'x1');
    define('ARI_QUIZ_HOTSPOT_Y1', 'y1');
    define('ARI_QUIZ_HOTSPOT_X2', 'x2');
    define('ARI_QUIZ_HOTSPOT_Y2', 'y2');
    class HotSpotQuestion extends AriQuizQuestionBase {
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            $clientData = array();
            $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
            return $clientData;
        }
        function applyUserData($data, $userXml, $decodeHtmlEntity = false) {
            if (empty($data) || empty($userXml)) return $data;
            $userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
            if ($userData) {
                $data['x'] = $userData[ARI_QUIZ_HOTSPOT_X1];
                $data['y'] = $userData[ARI_QUIZ_HOTSPOT_Y1];
            }
            return $data;
        }
        function getDataFromXml($xml, $htmlSpecialChars = true) {
            $data = null;
            if (!empty($xml)) {
                $xmlHandler = new AriSimpleXML();
                $xmlHandler->loadString($xml);
                $xmlDoc = &$xmlHandler->document;
                if ($xmlDoc->name() != ARI_QUIZ_HOTSPOT_DOC_TAG) return $data;
                $childs = $xmlDoc->children();
                if (!empty($childs) && count($childs) >0) {
                    $data = array();
                    $child = $childs[0];
                    $data[ARI_QUIZ_HOTSPOT_X1] = $child->attributes(ARI_QUIZ_HOTSPOT_X1);
                    $data[ARI_QUIZ_HOTSPOT_X2] = $child->attributes(ARI_QUIZ_HOTSPOT_X2);
                    $data[ARI_QUIZ_HOTSPOT_Y1] = $child->attributes(ARI_QUIZ_HOTSPOT_Y1);
                    $data[ARI_QUIZ_HOTSPOT_Y2] = $child->attributes(ARI_QUIZ_HOTSPOT_Y2);
                }
            }
            return $data;
        }
        function isCorrect($xml, $baseXml) {
            $isCorrect = false;
            if (!empty($xml) && !empty($baseXml)) {
                $data = $this->getDataFromXml($baseXml);
                $xData = $this->getDataFromXml($xml);
                if ($data[ARI_QUIZ_HOTSPOT_X1] <= $xData[ARI_QUIZ_HOTSPOT_X1] && $xData[ARI_QUIZ_HOTSPOT_X1] <= $data[ARI_QUIZ_HOTSPOT_X2] && $data[ARI_QUIZ_HOTSPOT_Y1] <= $xData[ARI_QUIZ_HOTSPOT_Y1] && $xData[ARI_QUIZ_HOTSPOT_Y1] <= $data[ARI_QUIZ_HOTSPOT_Y2]) {
                    $isCorrect = true;
                }
            }
            return $isCorrect;
        }
        function getFrontXml($questionId) {
            $x = intval(AriRequest::getParam('hidAriHotSpotX_'.$questionId, -1), 10);
            $y = intval(AriRequest::getParam('hidAriHotSpotY_'.$questionId, -1), 10);
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ARI_QUIZ_HOTSPOT_DOC_TAG));
            $xmlDoc = $xmlHandler->document;
            if ($x>-1 && $y>-1) {
                $xmlItem = &$xmlDoc->addChild(ARI_QUIZ_HOTSPOT_ITEM_TAG);
                $xmlItem->addAttribute(ARI_QUIZ_HOTSPOT_X1, $x);
                $xmlItem->addAttribute(ARI_QUIZ_HOTSPOT_Y1, $y);
            }
            return $xmlDoc->toString();
        }
        function getXml() {
            $xmlStr = null;
            $x1 = AriRequest::getParam('hidHotSpotX1', -1);
            $y1 = AriRequest::getParam('hidHotSpotY1', -1);
            $x2 = AriRequest::getParam('hidHotSpotX2', -1);
            $y2 = AriRequest::getParam('hidHotSpotY2', -1);
            $fileId = AriRequest::getParam('zQuizFiles[hotspot_image]', 0);
            if ($x1>-1 && $x2>-1 && $y1>-1 && $y2>-1) {
                $fileController = new AriFileController();
                $file = $fileController->call('getFile', $fileId, AriConstantsManager::getVar('FileGroup.HotSpot', AriQuizComponent::getCodeName()));
                if (!empty($file)) {
                    $xmlHandler = new AriSimpleXML();
                    $xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), ARI_QUIZ_HOTSPOT_DOC_TAG));
                    $xmlDoc = $xmlHandler->document;
                    $xmlItem = &$xmlDoc->addChild(ARI_QUIZ_HOTSPOT_ITEM_TAG);
                    $xmlItem->addAttribute(ARI_QUIZ_HOTSPOT_X1, $x1);
                    $xmlItem->addAttribute(ARI_QUIZ_HOTSPOT_X2, $x2);
                    $xmlItem->addAttribute(ARI_QUIZ_HOTSPOT_Y1, $y1);
                    $xmlItem->addAttribute(ARI_QUIZ_HOTSPOT_Y2, $y2);
                    $xmlStr = $xmlDoc->toString();
                }
            }
            return $xmlStr;
        }
    };
?>