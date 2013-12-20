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
    class AriQuizQuestionCategoryController extends AriControllerBase {
        function getCategoryMapping($categoryNames, $quizId) {
            global $database;
            $categoryMapping = array();
            if (!is_array($categoryNames) || count($categoryNames) == 0) return $categoryMapping;
            $query = sprintf('SELECT QuestionCategoryId AS CategoryId,CategoryName FROM #__ariquizquestioncategory WHERE QuizId = %d AND CategoryName IN (%s)', $quizId, join(',', $this->_quoteValues($categoryNames)));
            $database->setQuery($query);
            $categories = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get categories mapping.', E_USER_ERROR);
                return $categoryMapping;
            }
            foreach($categories as $category) {
                $categoryMapping[$category['CategoryName']] = $category['CategoryId'];
            }
            return $categoryMapping;
        }
        function updateQuestionCategory($idList, $fields, $ownerId) {
            if (empty($fields)) return true;
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $queryParts = array();
            $catStr = join(',', $this->_quoteValues($idList));
            $fields['Modified'] = ArisDate::getDbUTC();
            $fields['ModifiedBy'] = $ownerId;
            foreach($fields as $key=>$value) {
                $queryParts[] = sprintf('`%s`=%s', $key, $database->Quote($value));
            }
            $queryParts = join(',', $queryParts);
            $query = sprintf('UPDATE #__ariquizquestioncategory SET %s WHERE QuestionCategoryId IN (%s)', $queryParts, $catStr);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt update question categories.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function saveQuestionCategory($categoryId, $fields, $quizId, $ownerId) {
            global $database;
            $error = 'ARI: Couldnt save question category.';
            $categoryId = intval($categoryId);
            $isUpdate = ($categoryId>0);
            $row = $isUpdate ? $this->getQuestionCategory($categoryId) : AriEntityFactory::createInstance('AriQuizQuestionCategoryEntity', AriGlobalPrefs::getEntityGroup());
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if (!$row->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if ($isUpdate) {
                $row->Modified = ArisDate::getDbUTC();
                $row->ModifiedBy = $ownerId;
            } else {
                $row->QuizId = $quizId;
                $row->Created = ArisDate::getDbUTC();
                $row->CreatedBy = $ownerId;
            }
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            return $row;
        }
        function getQuestionCategory($questionCategoryId, $loadQuiz = false) {
            global $database;
            $error = 'ARI: Couldnt get question category.';
            $questionCategoryId = intval($questionCategoryId);
            $category = AriEntityFactory::createInstance('AriQuizQuestionCategoryEntity', AriGlobalPrefs::getEntityGroup());
            if (!$category->load($questionCategoryId)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if ($loadQuiz) {
                $quizController = new AriQuizController();
                $category->Quiz = $quizController->getQuiz($category->QuizId);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
            }
            return $category;
        }
        function deleteQuestionCategory($idList, $deleteQuestions = false) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $catStr = join(',', $this->_quoteValues($idList));
            $queryList = array();
            $queryList[] = sprintf('DELETE FROM #__ariquizquestioncategory WHERE QuestionCategoryId IN (%s)', $catStr);
            if ($deleteQuestions) {
                $queryList[] = sprintf('UPDATE #__ariquizquestion QQ INNER JOIN #__ariquizquestionversion QQV'.'	 ON QQ.QuestionVersionId = QQV.QuestionVersionId'.' SET QQ.Status = %d,QQ.QuestionCategoryId=0,QQV.QuestionCategoryId=0 WHERE QQ.QuestionCategoryId IN (%s)', ZQUIZ_QUE_STATUS_DELETE, $catStr);
            } else {
                $queryList[] = sprintf('UPDATE #__ariquizquestion QQ INNER JOIN #__ariquizquestionversion QQV'.'	 ON QQ.QuestionVersionId = QQV.QuestionVersionId'.' SET QQ.QuestionCategoryId = 0, QQV.QuestionCategoryId = 0 WHERE QQ.QuestionCategoryId IN (%s)', $catStr);
            }
            $database->setQuery(join($queryList, ';'));
            if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
        else $database->query_batch();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt delete question category.', E_USER_ERROR);
            return false;
        }
        return true;
    }
    function getQuestionCategoryCount($quizId, $filter = null) {
        global $database;
        $quizStatusList = AriConstantsManager::getVar('Status', AriQuizControllerConstants::getClassName());
        $query = sprintf('SELECT COUNT(SQC.QuestionCategoryId)'.' FROM #__ariquizquestioncategory SQC INNER JOIN #__ariquiz S'.' ON SQC.QuizId = S.QuizId '.' WHERE (%1$d = 0 OR SQC.QuizId = %1$d) AND (S.Status = %2$d OR S.Status = %3$d) AND (SQC.Status = %2$d)', $quizId, $quizStatusList['Active'], $quizStatusList['Inactive']);
        $query = $this->_applyDbCountFilter($query, $filter);
        $database->setQuery($query);
        $count = $database->loadResult();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt get question category count.', E_USER_ERROR);
            return 0;
        }
        return $count;
    }
    function getQuestionCategoryList($quizId, $filter = null) {
        global $database;
        $quizId = intval($quizId);
        $quizStatusList = AriConstantsManager::getVar('Status', AriQuizControllerConstants::getClassName());
        $query = sprintf('SELECT SQC.QuestionCategoryId, SQC.CategoryName, SQC.QuestionCount, SQC.QuestionTime, S.QuizName, S.QuizId'.' FROM #__ariquizquestioncategory SQC INNER JOIN #__ariquiz S'.' ON SQC.QuizId = S.QuizId '.' WHERE (%1$d = 0 OR SQC.QuizId = %1$d) AND (S.Status = %2$d OR S.Status = %3$d) AND (SQC.Status = %2$d)', $quizId, $quizStatusList['Active'], $quizStatusList['Inactive']);
        $query = $this->_applyFilter($query, $filter);
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt get question category list.', E_USER_ERROR);
            return null;
        }
        return $rows;
    }
    function copy($sourceQuizId, $destQuizId, $ownerId) {
        $catMapping = array();
        global $database;
        $error = 'ARI: Couldnt copy question categories.';
        $categories = $this->getQuestionCategoryList($sourceQuizId);
        if ($this->_isError(true, false)) {
            trigger_error($error, E_USER_ERROR);
            return null;
        }
        if (empty($categories)) return $catMapping;
        $now = ArisDate::getDbUTC();
        foreach($categories as $category) {
            $copyCategory = $this->getQuestionCategory($category->QuestionCategoryId);
            $copyCategory->QuestionCategoryId = 0;
            $copyCategory->QuizId = $destQuizId;
            $copyCategory->Created = $now;
            $copyCategory->CreatedBy = $ownerId;
            $copyCategory->Modified = null;
            $copyCategory->ModifiedBy = null;
            if (!$copyCategory->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $catMapping[$category->QuestionCategoryId] = $copyCategory->QuestionCategoryId;
        }
        return $catMapping;
    }
    };
?>