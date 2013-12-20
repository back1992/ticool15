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
    class AriQuizEntity extends AriDBTable {
        var $QuizId = null;
        var $QuizName = '';
        var $CreatedBy = 0;
        var $Created;
        var $ModifiedBy = 0;
        var $Modified = null;
        var $AccessType = null;
        var $Status;
        var $TotalTime = 0;
        var $PassedScore = 0;
        var $QuestionCount = 0;
        var $QuestionTime = 0;
        var $CategoryList;
        var $AccessList;
        var $Description = '';
        var $CanSkip = 0;
        var $CanStop = 0;
        var $RandomQuestion = 0;
        var $UseCalculator = 0;
        var $AttemptCount = 0;
        var $LagTime = 0;
        var $CssTemplateId = 0;
        var $AdminEmail = '';
        var $ResultScaleId = 0;
        var $ParsePluginTag = 0;
        var $ShowCorrectAnswer = 0;
        var $ShowExplanation = 0;
        var $Anonymous = 'Yes';
        var $QuestionOrderType = 'Numeric';
        var $FullStatistics = 'Never';
        function AriQuizEntity(&$_db) {
            $this->AriDBTable('#__ariquiz', 'QuizId', $_db);
        }
    };
?>