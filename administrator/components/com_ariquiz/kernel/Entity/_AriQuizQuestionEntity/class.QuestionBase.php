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
    class AriQuizQuestionBase {
        function applyUserData($data, $userXml) {
            return $data;
        }
        function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false) {
            return $this->getDataFromXml($xml, $decodeHtmlEntity);
        }
        function getDataFromXml($xml, $decodeHtmlEntity = false) {
            return null;
        }
        function getFrontXml($questionId) {
            return null;
        }
        function getXml() {
            return null;
        }
        function getOverrideXml() {
            return null;
        }
        function isCorrect($xml, $baseXml) {
            return false;
        }
        function getScore($xml, $baseXml, $score) {
            return $this->isCorrect($xml, $baseXml) ? $score : 0;
        }
        function correctPercent($percent) {
            $percent = @intval($percent, 10);
            return $percent>100 ? 100 : ($percent<0 ? 0 : $percent);
        }
        function getMaximumQuestionScore($score, $xml) {
            return $this->isScoreSpecific() ? $this->calculateMaximumScore($score, $xml) : $score;
        }
        function calculateMaximumScore($score, $xml) {
            return $score;
        }
        function isScoreSpecific() {
            return false;
        }
    };
?>