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
    class AriQuizStatisticsEntity extends AriDBTable {
        var $StatisticsId;
        var $QuestionId;
        var $QuestionVersionId;
        var $StatisticsInfoId;
        var $Data;
        var $StartDate = null;
        var $EndDate = null;
        var $SkipDate = null;
        var $SkipCount = 0;
        var $QuestionIndex = 0;
        var $Question;
        var $Score = null;
        var $QuestionTime = null;
        var $QuestionCategoryId;
        var $UsedTime = 0;
        var $IpAddress = null;
        var $BankQuestionId = 0;
        var $BankVersionId = 0;
        var $InitData = null;
        var $AttemptCount = 0;
        function AriQuizStatisticsEntity(&$_db) {
            $this->AriDBTable('#__ariquizstatistics', 'StatisticsId', $_db);
        }
        function bind($fields, $ignoreArray = array(), $bindChilds = false) {
            $result = parent::bind($fields, $ignoreArray);
            if (!$bindChilds) return $result;
            $question = AriEntityFactory::createInstance('AriQuizQuestionEntity', AriGlobalPrefs::getEntityGroup());
            $question->bind(AriUtils::getParam($fields, 'Question'), array(), true);
            $this->Question = $question;
            return $result;
        }
    };
?>