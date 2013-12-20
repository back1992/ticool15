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
    AriKernel::import('Controllers.AriQuiz.QuizController');
    AriKernel::import('Controllers.AriQuiz.CategoryController');
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Controllers.AriQuiz.QuestionCategoryController');
    AriKernel::import('Controllers.AriQuiz.QuestionBankController');
    AriKernel::import('Controllers.AriQuiz.QuestionBankCategoryController');
    AriKernel::import('Controllers.AriQuiz.LMSImport.QuestionLoader');
    AriKernel::import('Xml.SimpleXml');
    AriKernel::import('Xml.SimpleXmlHelper');
    AriKernel::import('System.System');
    AriKernel::import('Security.Security');
    class AriQuizLMSImportController extends AriControllerBase {
        var $ROOT_TAG = 'course_backup';
        var $QUESTIONS_MAPPING = array(2=>'MultipleQuestion');
        function import($lmsFile, $userId = 0) {
            if (@!file_exists($lmsFile) || @!is_file($lmsFile) || !@is_readable($lmsFile)) return false;
            @set_time_limit(9999);
            AriSystem::setOptimalMemoryLimit('16M', '16M', '256M');
            ignore_user_abort(true);
            $xmlDoc = @$this->_loadXml($lmsFile);
            if (empty($xmlDoc) || $xmlDoc->name() != $this->ROOT_TAG) return false;
            $bankCategories = $this->importBankCategories($xmlDoc, $userId);
            $this->importBankQuestions($xmlDoc, $bankCategories, $userId);
            $this->importQuizzes($xmlDoc, $userId);
            return true;
        }
        function importBankQuestions(&$xmlDoc, $categories, $userId) {
            $questionsNode = &AriSimpleXmlHelper::getSingleNode($xmlDoc, 'quizzes_question_pool');
            if (empty($questionsNode)) return;
            $questionsNode = &AriSimpleXmlHelper::getSingleNode($questionsNode, 'question_pool_data');
            if (empty($questionsNode)) return;
            $optionsData = &$questionsNode;
            $questionsNode = &AriSimpleXmlHelper::getSingleNode($questionsNode, 'pool_questions');
            if (empty($questionsNode)) return;
            $questionsNode = &AriSimpleXmlHelper::getNode($questionsNode, 'quiz_question');
            if (empty($questionsNode)) return;
            $types = $this->_getQuestionTypes();
            $bankController = new AriQuizQuestionController();
            foreach($questionsNode as $questionNode) {
                $questionText = AriSimpleXmlHelper::getData($questionNode, 'question_text');
                if (empty($questionText)) continue;
                $questionTypeId = @intval($questionNode->attributes('c_type'), 10);
                if (!array_key_exists($questionTypeId, $this->QUESTIONS_MAPPING)) continue;
                $questionType = $types[$this->QUESTIONS_MAPPING[$questionTypeId]];
                $questionTypeObj = AriQuizLMSImportQuestionLoader::getQuestion($questionType->ClassName);
                $questionId = @intval($questionNode->attributes('c_id'), 10);
                $questionTypeId = $questionType->QuestionTypeId;
                $questionData = $questionTypeObj->getXml($questionId, $questionNode, $optionsData);
                $explanation = AriSimpleXmlHelper::getData($questionNode, 'question_explanation');
                $score = $questionTypeObj->getMaximumQuestionScore(@intval($questionNode->attributes('c_point'), 10), $questionData);
                $ordering = @intval($questionNode->attributes('ordering'), 10);
                $categoryId = @intval($questionNode->attributes('c_qcat'), 10);
                $categoryId = (isset($categories[$categoryId])) ? $categories[$categoryId] : 0;
                $bankController->saveQuestion(0, 0, $questionTypeId, $userId, array('QuestionCategoryId'=>$categoryId, 'QuestionTypeId'=>$questionTypeId, 'Status'=>1, 'QuestionIndex'=>$ordering, 'Question'=>$questionText, 'Score'=>$score, 'Note'=>$explanation), $questionData);
            }
        }
        function importBankCategories(&$xmlDoc, $userId) {
            $categoriesMapping = array();
            $categoriesNode = &AriSimpleXmlHelper::getSingleNode($xmlDoc, 'quizzes_quest_categories');
            if (empty($categoriesNode)) return $categoriesMapping;
            $categoriesNode = &AriSimpleXmlHelper::getNode($categoriesNode, 'quest_category');
            if (empty($categoriesNode)) return $categoriesMapping;
            $catController = new AriQuizQuestionBankCategoryController();
            foreach($categoriesNode as $categoryNode) {
                $categoryId = @intval($categoryNode->attributes('c_id'), 10);
                $categoryName = AriSimpleXmlHelper::getData($categoryNode, 'quest_category_name');
                if ($categoryId<0 || empty($categoryName)) continue;
                $categoryDescription = AriSimpleXmlHelper::getData($categoryNode, 'quest_category_instr');
                $newCategory = $catController->saveCategory(0, array('CategoryName'=>$categoryName, 'Description'=>$categoryDescription), $userId);
                if ($newCategory && $newCategory->CategoryId) $categoriesMapping[$categoryId] = $newCategory->CategoryId;
            }
            return $categoriesMapping;
        }
        function importQuizzes(&$xmlDoc, $userId) {
            $quizzesNode = &AriSimpleXmlHelper::getSingleNode($xmlDoc, 'quizzes');
            if (empty($quizzesNode)) return;
            $quizzesNode = &AriSimpleXmlHelper::getNode($quizzesNode, 'quiz');
            if (empty($quizzesNode)) return;
            $categoryMapping = array();
            $regGroupId = AriConstantsManager::getVar('Id.Registered', AriUserAccessHelperConstants::getClassName());
            $catController = new AriQuizCategoryController();
            $quizController = new AriQuizController();
            foreach($quizzesNode as $quizNode) {
                $quizName = AriSimpleXmlHelper::getData($quizNode, 'quiz_title');
                if (empty($quizName)) continue;
                $categoryId = 0;
                $categoryName = AriSimpleXmlHelper::getData($quizNode, 'quiz_category');
                if (!empty($categoryName)) {
                    if (!array_key_exists($categoryName, $categoryMapping)) {
                        $newCategory = $catController->saveCategory(0, array('CategoryName'=>$categoryName, 'Description'=>''), $userId);
                        $categoryMapping[$categoryName] = $newCategory->CategoryId;
                    }
                    $categoryId = $categoryMapping[$categoryName];
                }
                $description = AriSimpleXmlHelper::getData($quizNode, 'quiz_description');
                $published = AriUtils::parseValueBySample($quizNode->attributes('published'), false);
                $timeLimit = @intval(AriSimpleXmlHelper::getData($quizNode, 'quiz_time_limit'), 10);
                $nextAttemptTimeout = @intval(AriSimpleXmlHelper::getData($quizNode, 'quiz_min_after'), 10);
                $passedScore = @intval(AriSimpleXmlHelper::getData($quizNode, 'quiz_passing_score'), 10);
                $fullStat = AriUtils::parseValueBySample(AriSimpleXmlHelper::getData($quizNode, 'quiz_review'), false);
                $randomQuestion = AriUtils::parseValueBySample(AriSimpleXmlHelper::getData($quizNode, 'quiz_random'), false);
                $guestAccess = AriUtils::parseValueBySample(AriSimpleXmlHelper::getData($quizNode, 'quiz_guest'), false);
                $categoryList = $categoryId>0 ? array($categoryId) : null;
                $accessList = !$guestAccess ? array($regGroupId) : null;
                $quizController->saveQuiz(0, array('QuizName'=>$quizName, 'Description'=>$description, 'Active'=>$published ? '1' : null, 'TotalTime'=>$timeLimit, 'LagTime'=>$nextAttemptTimeout, 'PassedScore'=>$passedScore, 'FullStatistics'=>$fullStat ? 'Always' : 'Never', 'RandomQuestion'=>$randomQuestion ? '1' : null,), $userId, $categoryList, $accessList, null, null);
            }
        }
        function _getQuestionTypes() {
            static $types;
            if (is_null($types)) {
                $types = array();
                $questionController = new AriQuizQuestionController();
                $typeList = $questionController->getQuestionTypeList();
                if (is_array($typeList)) {
                    foreach($typeList as $type) {
                        $types[$type->ClassName] = $type;
                    }
                }
            }
            return $types;
        }
        function _loadXml($file) {
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            $xmlStr = trim(file_get_contents($file));
            set_magic_quotes_runtime($oldMQR);
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString($xmlStr);
            return $xmlHandler->document;
        }
    };
?>