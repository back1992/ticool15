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
    AriKernel::import('Controllers.ControllerBase');
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Controllers.AriQuiz.QuestionCategoryController');
    AriKernel::import('Controllers.AriQuiz.QuestionBankCategoryController');
    AriKernel::import('CSV.CSVParser');
    AriKernel::import('Controllers.AriQuiz.CSVImport.QuestionLoader');
    class AriQuizCSVQuestionImportController extends AriControllerBase {
        var $_questionController;
        function __construct() {
            $this->_questionController = new AriQuizQuestionController();
        }
        function getQuestionTypes() {
            static $types;
            if (is_null($types)) {
                $types = array();
                $typeList = $this->_questionController->getQuestionTypeList();
                if (is_array($typeList)) {
                    foreach($typeList as $type) {
                        $types[$type->ClassName] = $type;
                    }
                }
            }
            return $types;
        }
        function parseCSVFile($filePath) {
            $data = array();
            if (empty($filePath) || !@file_exists($filePath) || !@is_file($filePath)) return $data;
            $csvParser = new AriCSVParser();
            $csvParser->auto($filePath);
            $csvData = $csvParser->data;
            if (empty($csvData)) return $data;
            $question = null;
            foreach($csvData as $csvDataItem) {
                if (!empty($csvDataItem['Type'])) {
                    if (!is_null($question)) $data[] = $question;
                    $question = $csvDataItem;
                    $question['_Childs'] = array();
                    continue;
                }
                if (!is_null($question)) $question['_Childs'][] = $csvDataItem;
            }
            if (!is_null($question)) $data[] = $question;
            return $data;
        }
        function importBankQuestions($filePath, $userId = 0) {
            $result = true;
            $data = $this->parseCSVFile($filePath);
            if (empty($data)) return false;
            $categoryMapping = $this->getBankCategoryMapping($data, $userId);
            foreach($data as $dataItem) {
                $categoryName = trim(AriUtils::getParam($dataItem, 'Category', ''));
                $categoryId = !empty($categoryName) && isset($categoryMapping[$categoryName]) ? $categoryMapping[$categoryName] : 0;
                if (!$this->importQuestion($dataItem, $userId, 0, $categoryId)) $result = false;
            }
            return $result;
        }
        function getQuizCategoryMapping($data, $quizId, $userId = 0) {
            $categoryMapping = array();
            $categoryList = $this->getCategoryList($data);
            $qcc = new AriQuizQuestionCategoryController();
            $mapping = $qcc->getCategoryMapping($categoryList, $quizId);
            foreach($categoryList as $categoryName) {
                $categoryId = 0;
                if (!isset($mapping[$categoryName])) {
                    $cat = $qcc->saveQuestionCategory(0, array('CategoryName'=>$categoryName), $quizId, $userId);
                    if ($cat) $categoryId = $cat->QuestionCategoryId;
                } else {
                    $categoryId = $mapping[$categoryName];
                }
                $categoryMapping[$categoryName] = $categoryId;
            }
            return $categoryMapping;
        }
        function getBankCategoryMapping($data, $userId = 0) {
            $categoryMapping = array();
            $categoryList = $this->getCategoryList($data);
            $cc = new AriQuizQuestionBankCategoryController();
            $mapping = $cc->getCategoryMapping($categoryList);
            foreach($categoryList as $categoryName) {
                $categoryId = 0;
                if (!isset($mapping[$categoryName])) {
                    $cat = $cc->saveCategory(0, array('CategoryName'=>$categoryName), $userId);
                    if ($cat) $categoryId = $cat->CategoryId;
                } else {
                    $categoryId = $mapping[$categoryName];
                }
                $categoryMapping[$categoryName] = $categoryId;
            }
            return $categoryMapping;
        }
        function getCategoryList($data) {
            $categoryList = array();
            if (is_array($data)) {
                foreach($data as $dataItem) {
                    $categoryName = trim(AriUtils::getParam($dataItem, 'Category', ''));
                    if (!empty($categoryName)) $categoryList[] = $categoryName;
                }
            }
            return array_unique($categoryList);
        }
        function importQuizQuestions($filePath, $quizId, $userId = 0) {
            if ($quizId<1) return false;
            $result = true;
            $data = $this->parseCSVFile($filePath);
            $categoryMapping = $this->getQuizCategoryMapping($data, $quizId, $userId);
            foreach($data as $dataItem) {
                $categoryName = trim(AriUtils::getParam($dataItem, 'Category', ''));
                $categoryId = !empty($categoryName) && isset($categoryMapping[$categoryName]) ? $categoryMapping[$categoryName] : 0;
                if (!$this->importQuestion($dataItem, $userId, $quizId, $categoryId)) $result = false;
            }
            return $result;
        }
        function importQuestion($questionData, $userId, $quizId = 0, $categoryId = 0) {
            $qc = $this->_questionController;
            $typeClass = $questionData['Type'];
            $types = $this->getQuestionTypes();
            if (!isset($types[$typeClass])) return false;
            $type = $types[$typeClass];
            $question = AriUtils::getParam($questionData, 'Question');
            if (empty($question)) return false;
            $importQuestionWrapper = AriQuizCSVImportQuestionLoader::getQuestion($typeClass);
            if (is_null($importQuestionWrapper)) return false;
            $score = @intval(AriUtils::getParam($questionData, 'Score'), 10);
            if ($score<0) $score = 0;
            $data = $importQuestionWrapper->getXml($questionData);
            $fields = array('Score'=>$importQuestionWrapper->getMaximumQuestionScore($score, $data), 'Question'=>$question, 'Note'=>AriUtils::getParam($questionData, 'Note', ''), 'QuestionCategoryId'=>$categoryId);
            return $qc->saveQuestion(0, $quizId, $type->QuestionTypeId, $userId, $fields, $data);
        }
    };
?>