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
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    class AriQuizQuestionBankController extends AriControllerBase {
        function importQuestionsFromBank($bankQuestionIds, $quizId, $ownerId, $categoryId = 0, $score = null) {
            $bankQuestionIds = $this->_fixIdList($bankQuestionIds);
            if (empty($bankQuestionIds)) return true;
            $error = 'ARI: Couldnt import questions from bank.';
            $questionController = new AriQuizQuestionController();
            $result = true;
            foreach($bankQuestionIds as $bankId) {
                $bankId = @intval($bankId, 10);
                if ($bankId<1) continue;
                $bankQuestion = $questionController->call('getQuestion', $bankId, true, false);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    $result = false;
                    break;
                }
                if (empty($bankQuestion)) continue;
                $questionType = $bankQuestion->QuestionVersion->QuestionType;
                $questionObj = AriEntityFactory::createInstance($questionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                $fields = array('BankQuestionId'=>$bankId, 'QuestionCategoryId'=>$categoryId);
                if (!is_null($score) && !$questionObj->isScoreSpecific()) $fields['Score'] = $score;
                $questionController->call('saveQuestion', AriRequest::getParam('questionId', 0), $quizId, $bankQuestion->QuestionTypeId, $ownerId, $fields, null);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    $result = false;
                    break;
                }
            }
            return $result;
        }
        function _isNotLoadUsedQuestions($filter) {
            $notLoadUsedQuestions = false;
            if ($filter) {
                $filterPredicates = $filter->getConfigValue('filter');
                if (!empty($filterPredicates['QuizId'])) {
                    $notLoadUsedQuestions = !empty($filterPredicates['NotLoadUsedQuestions']);
                }
            }
            return $notLoadUsedQuestions;
        }
        function _applyBankFilter($query, $filter) {
            $quizId = 0;
            $notLoadUsedQuestions = false;
            if ($filter) {
                $filterPredicates = $filter->getConfigValue('filter');
                if (isset($filterPredicates['CategoryId']) && $filterPredicates['CategoryId'] !== null && $filterPredicates['CategoryId'] !== '') {
                    $categoryId = @intval($filterPredicates['CategoryId'], 10);
                    if ($categoryId) {
                        $query.= ' AND QBC.CategoryId='.$categoryId;
                    } else {
                        $query.= ' AND (IFNULL(QBC.CategoryId, 0) = 0)';
                    }
                }
                if (!empty($filterPredicates['QuizId'])) {
                    $quizId = $filterPredicates['QuizId'];
                    $notLoadUsedQuestions = !empty($filterPredicates['NotLoadUsedQuestions']);
                    if ($notLoadUsedQuestions) $query.= ' AND IFNULL(T.QuestionCount, 0) = 0';
                }
            }
            $query = sprintf($query, $quizId);
            return $query;
        }
        function getQuestionCount($filter = null) {
            global $database;
            $notLoadUsedQuestions = $this->_isNotLoadUsedQuestions($filter);
            $query = sprintf('SELECT COUNT(*)'.' FROM #__ariquizquestion SQ'.($notLoadUsedQuestions ? ' LEFT JOIN '.' (SELECT SQ1.QuestionId,COUNT(SQ1.QuestionId) AS QuestionCount'.' 	FROM'.' 		#__ariquizquestion SQ1 INNER JOIN #__ariquizquestion BQ'.'			ON SQ1.QuestionId = BQ.BankQuestionId'.'	WHERE'.'   	BQ.`Status` = %1$d AND (1 = 0 OR BQ.QuizId = %%1$s)'.'	GROUP BY SQ1.QuestionId'.') T'.'	ON SQ.QuestionId = T.QuestionId' : '') .' INNER JOIN #__ariquizquestionversion SQV'.' 	ON SQ.QuestionVersionId = SQV.QuestionVersionId'.' INNER JOIN #__ariquizquestiontype QQT'.'	ON SQV.QuestionTypeId = QQT.QuestionTypeId'.' LEFT JOIN #__ariquizbankcategory QBC'.'	ON SQ.QuestionCategoryId = QBC.CategoryId'.' WHERE SQ.Status = %1$d AND SQ.QuizId = 0', AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName()));
            $query = $this->_applyBankFilter($query, $filter);
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get count bank question.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function getQuestionList($filter = null) {
            global $database;
            $notLoadUsedQuestions = $this->_isNotLoadUsedQuestions($filter);
            $query = sprintf('SELECT SQ.QuestionId, SQ.QuestionId AS BankQuestionId, SQV.Question, QQT.QuestionType, SQV.Created, QBC.CategoryName'.' FROM #__ariquizquestion SQ'.($notLoadUsedQuestions ? ' LEFT JOIN '.' (SELECT SQ1.QuestionId,COUNT(SQ1.QuestionId) AS QuestionCount'.' 	FROM'.' 		#__ariquizquestion SQ1 INNER JOIN #__ariquizquestion BQ'.'			ON SQ1.QuestionId = BQ.BankQuestionId'.'	WHERE'.'   	BQ.`Status` = %1$d AND (1 = 0 OR BQ.QuizId = %%1$s)'.'	GROUP BY SQ1.QuestionId'.'	ORDER BY NULL) T'.'	ON SQ.QuestionId = T.QuestionId' : '') .' INNER JOIN #__ariquizquestionversion SQV'.' 	ON SQ.QuestionVersionId = SQV.QuestionVersionId'.' INNER JOIN #__ariquizquestiontype QQT'.'	ON SQV.QuestionTypeId = QQT.QuestionTypeId'.' LEFT JOIN #__ariquizbankcategory QBC'.'	ON SQ.QuestionCategoryId = QBC.CategoryId'.' WHERE SQ.Status = %1$d AND SQ.QuizId = 0', AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName()));
            $query = $this->_applyBankFilter($query, $filter);
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get bank question list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function deleteQuestion($idList) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $error = 'ARI: Couldnt delete questions from bank.';
            $statusList = AriConstantsManager::getVar('Status', AriQuizQuestionControllerConstants::getClassName());
            $query = sprintf('SELECT QQ.BankQuestionId'.' FROM #__ariquizquestion QQ'.' WHERE'.' QQ.BankQuestionId IN (%1$s)'.' AND'.' QQ.Status <> %2$d'.' GROUP BY QQ.BankQuestionId'.' ORDER BY NULL', join(',', $this->_quoteValues($idList)), $statusList['Delete']);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error($error, E_USER_ERROR);
                return false;
            }
            $usedBankQuestions = $database->loadResultArray();
            $newIdList = array();
            if (!is_array($usedBankQuestions) || count($usedBankQuestions) == 0) {
                $newIdList = $idList;
            } else {
                foreach($idList as $key=>$value) {
                    if (!in_array($value, $usedBankQuestions)) $newIdList[] = $value;
                }
                if (count($newIdList) == 0) return true;
            }
            $query = sprintf('UPDATE #__ariquizquestion QQ'.' SET QQ.Status = %1$d'.' WHERE QQ.QuestionId IN (%2$s) AND QQ.QuizId = 0', $statusList['Delete'], join(',', $this->_quoteValues($newIdList)), $statusList['Active']);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error($error, E_USER_ERROR);
                return false;
            }
            return true;
        }
    };
?>