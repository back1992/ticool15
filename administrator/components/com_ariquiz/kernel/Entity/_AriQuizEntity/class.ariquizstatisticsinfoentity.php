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
    AriKernel::import('Xml.SimpleXml');
    AriKernel::import('Xml._Templates.SimpleXmlTemplates');
    class AriQuizStatisticsInfoEntity extends AriDBTable {
        var $StatisticsInfoId;
        var $QuizId;
        var $UserId = null;
        var $Status = 'Process';
        var $TicketId;
        var $StartDate = null;
        var $EndDate = null;
        var $PassedScore = 0;
        var $UserScore = 0;
        var $MaxScore = 0;
        var $Passed = 0;
        var $CreatedDate;
        var $ResultEmailed = 0;
        var $QuestionCount = 0;
        var $TotalTime = 0;
        var $ExtraData = null;
        var $CurrentStatisticsId = null;
        var $ModifiedDate = null;
        function AriQuizStatisticsInfoEntity(&$_db) {
            $this->AriDBTable('#__ariquizstatisticsinfo', 'StatisticsInfoId', $_db);
        }
        function getExtraDataXml($extraData) {
            $xml = null;
            if (empty($extraData)) return $xml;
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString(sprintf(ARI_SIMPLEXML_TEMPLATE_XML, AriGlobalPrefs::getDbCharset(), 'extraData'));
            $xmlDoc = &$xmlHandler->document;
            foreach($extraData as $key=>$value) {
                $xmlItem = &$xmlDoc->addChild('item');
                $xmlItem->addAttribute('name', $key);
                $xmlItem->setData($value);
            }
            $xml = $xmlDoc->toString();
            return $xml;
        }
        function parseExtraDataXml($xml) {
            $extraData = array();
            if (empty($xml)) return $extraData;
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString($xml);
            $xmlDoc = &$xmlHandler->document;
            $tagName = 'item';
            $childs = &$xmlDoc->$tagName;
            if (!empty($childs)) {
                foreach($childs as $child) {
                    $extraData[$child->attributes('name') ] = $child->data();
                }
            }
            return $extraData;
        }
    };
?>