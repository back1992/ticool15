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
    class AriQuizQuestionControllerConstants extends AriClassConstants {
        var $Status = array('Active'=>1, 'Delete'=>2);
        function getClassName() {
            return strtolower('AriQuizQuestionControllerConstants');
        }
    }
    new AriQuizQuestionControllerConstants();
    class AriQuizQuestionController extends AriControllerBase {
        function getQuestionCount($quizId, $filter = null) {
            $filter = $this->_getDbCountFilter($filter);
            $count = $this->_getRecordCount('#__ariquizquestion', array('QuizId'=>$quizId, 'Status'=>AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName())));
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt get question count.', E_USER_ERROR);
            }
            return $count;
        }
        function getQuestionList($quizId, $filter = null) {
            global $database;
            $query = sprintf('SELECT S.QuizName, S.QuizId, SQ.QuestionId, SQV.Question, QQT.QuestionType, SQV.Created, SQC.QuestionCategoryId, SQC.CategoryName, SQ.QuestionIndex AS QuestionIndex2'.' FROM #__ariquiz S INNER JOIN #__ariquizquestion SQ'.' 	ON S.QuizId = SQ.QuizId'.' LEFT JOIN #__ariquizquestion SQ2'.' 	ON SQ.BankQuestionId = SQ2.QuestionId'.' INNER JOIN #__ariquizquestionversion SQV'.'	ON IFNULL(SQ2.QuestionVersionId, SQ.QuestionVersionId) = SQV.QuestionVersionId'.' LEFT JOIN #__ariquizquestiontype QQT'.'	ON IFNULL(SQ2.QuestionTypeId, SQ.QuestionTypeId) = QQT.QuestionTypeId'.' LEFT JOIN #__ariquizquestioncategory SQC'.' 	ON SQ.QuestionCategoryId = SQC.QuestionCategoryId'.' WHERE SQ.Status = %d AND SQ.QuizId = %s', AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName()), $database->Quote($quizId));
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get question list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function getSimpleQuestionList($quizId, $filter = null) {
            global $database;
            $query = sprintf('SELECT SQ.QuestionId,SQ.QuestionCategoryId'.' FROM #__ariquiz S INNER JOIN #__ariquizquestion SQ'.' 	ON S.QuizId = SQ.QuizId'.' LEFT JOIN #__ariquizquestioncategory SQC'.' 	ON SQ.QuestionCategoryId = SQC.QuestionCategoryId'.' WHERE SQ.Status = %d AND SQ.QuizId = %s', AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName()), $database->Quote($quizId));
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get simple question list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function getQuestionType($questionTypeId) {
            global $database;
            $questionTypeId = intval($questionTypeId);
            $questionType = AriEntityFactory::createInstance('AriQuizQuestionTypeEntity', AriGlobalPrefs::getEntityGroup());
            if (!$questionType->load($questionTypeId) && $questionType->getError()) {
                trigger_error('ARI: Couldnt get question type.', E_USER_ERROR);
                return null;
            }
            return $questionType;
        }
        function getQuestionTypeList($forTemplate = false) {
            global $database;
            $query = sprintf('SELECT QuestionTypeId, QuestionType, ClassName FROM #__ariquizquestiontype'.' %s ORDER BY `Default` DESC, QuestionType ASC', $forTemplate ? 'WHERE CanHaveTemplate = 1' : '');
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get question type list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function getMaxQuestionIndex($quizId) {
            global $database;
            $index = -1;
            $quizId = @intval($quizId, 10);
            if ($quizId<0) return $index;
            $database->setQuery(sprintf('SELECT MAX(QuestionIndex) FROM #__ariquizquestion QQ WHERE QQ.QuizId = %d LIMIT 0,1', $quizId));
            $index = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get max question index.', E_USER_ERROR);
                return -1;
            }
            if (is_null($index)) $index = -1;
            return $index;
        }
        function copyToBank($idList, $categoryId, $ownerId, $basedOnBank = false) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $error = 'ARI: Couldnt copy questions to bank.';
            $qIndex = $this->getMaxQuestionIndex(0) +1;
            $now = ArisDate::getDbUTC();
            foreach($idList as $questionId) {
                $question = $this->getQuestion($questionId, true, false);
                if (empty($question) || $question->BankQuestionId) continue;
                $bankQuestion = AriEntityFactory::createInstance('AriQuizQuestionEntity', AriGlobalPrefs::getEntityGroup());
                $bankQuestion->bind($question->getPublicFields());
                $bankQuestion->QuestionId = 0;
                $bankQuestion->QuestionVersionId = 0;
                $bankQuestion->Modified = null;
                $bankQuestion->ModifiedBy = null;
                $bankQuestion->Created = $now;
                $bankQuestion->CreatedBy = $ownerId;
                $bankQuestion->QuestionCategoryId = $categoryId;
                $bankQuestion->QuizId = 0;
                $bankQuestion->BankQuestionId = 0;
                $bankQuestion->QuestionIndex = $qIndex;
                if (!$bankQuestion->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $bankQuestionVersion = AriEntityFactory::createInstance('AriQuizQuestionVersionEntity', AriGlobalPrefs::getEntityGroup());
                $bankQuestionVersion->bind($question->QuestionVersion);
                $bankQuestionVersion->QuestionVersionId = 0;
                $bankQuestionVersion->Created = $now;
                $bankQuestionVersion->CreatedBy = $ownerId;
                $bankQuestionVersion->BankQuestionId = null;
                if (!is_null($categoryId)) $bankQuestionVersion->QuestionCategoryId = $categoryId;
                if (!$bankQuestionVersion->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $bankQuestion->QuestionVersionId = $bankQuestionVersion->QuestionVersionId;
                if (!$bankQuestion->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $files = $this->getSimpleQuestionFiles($question->QuestionVersion->QuestionVersionId);
                $this->saveQuestionFiles($bankQuestion->QuestionId, $bankQuestionVersion->QuestionVersionId, $files);
                if ($basedOnBank) {
                    $questionVersion = AriEntityFactory::createInstance('AriQuizQuestionVersionEntity', AriGlobalPrefs::getEntityGroup());
                    $questionVersion->QuestionId = $question->QuestionId;
                    $questionVersion->BankQuestionId = $bankQuestion->QuestionId;
                    $questionVersion->QuestionCategoryId = $question->QuestionCategoryId;
                    $questionVersion->QuestionTypeId = $question->QuestionTypeId;
                    $questionVersion->Created = $now;
                    $questionVersion->CreatedBy = $ownerId;
                    $questionVersion->Score = $bankQuestionVersion->Score;
                    if (!$questionVersion->store()) {
                        trigger_error($error, E_USER_ERROR);
                        return false;
                    }
                    $question->BankQuestionId = $bankQuestion->QuestionId;
                    $question->QuestionVersionId = $questionVersion->QuestionVersionId;
                    $question->Modified = $now;
                    $question->ModifiedBy = $ownerId;
                    if (!$question->store()) {
                        trigger_error($error, E_USER_ERROR);
                        return false;
                    }
                }
                ++$qIndex;
            }
            return true;
        }
        function move($idList, $quizId, $questionCategoryId, $ownerId) {
            if ($quizId<1) return true;
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            $error = 'ARI: Couldnt move questions.';
            $qIndex = $this->getMaxQuestionIndex($quizId) +1;
            $now = ArisDate::getDbUTC();
            foreach($idList as $questionId) {
                $question = $this->getQuestion($questionId, true, false);
                if (empty($question) || !$question->QuestionId) continue;
                $oldQuestionVersionId = $question->QuestionVersion->QuestionVersionId;
                $questionVersion = $question->QuestionVersion;
                $questionVersion->QuestionVersionId = 0;
                $questionVersion->Created = $now;
                $questionVersion->CreatedBy = $ownerId;
                $questionVersion->QuestionCategoryId = $questionCategoryId;
                if (!$questionVersion->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $question->QuizId = $quizId;
                $question->QuestionVersionId = $questionVersion->QuestionVersionId;
                $question->Modified = $now;
                $question->ModifiedBy = $ownerId;
                $question->QuestionCategoryId = $questionCategoryId;
                $question->QuestionIndex = $qIndex;
                if (!$question->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $files = $this->getSimpleQuestionFiles($oldQuestionVersionId);
                $this->saveQuestionFiles($question->QuestionId, $question->QuestionVersionId, $files);
                ++$qIndex;
            }
            return true;
        }
        function copy($idList, $quizId, $questionCategoryId, $ownerId) {
            if ($quizId<1) return true;
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            $error = 'ARI: Couldnt copy questions.';
            $qIndex = $this->getMaxQuestionIndex($quizId) +1;
            $now = ArisDate::getDbUTC();
            foreach($idList as $questionId) {
                $question = $this->getQuestion($questionId, true, false);
                if (empty($question) || !$question->QuestionId) continue;
                $oldQuestionVersionId = $question->QuestionVersion->QuestionVersionId;
                $question->QuestionId = 0;
                $question->QuizId = $quizId;
                $question->QuestionVersionId = 0;
                $question->Created = $now;
                $question->CreatedBy = $ownerId;
                $question->Modified = null;
                $question->ModifiedBy = null;
                $question->QuestionCategoryId = $questionCategoryId;
                $question->QuestionIndex = $qIndex;
                if (!$question->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $questionVersion = $question->QuestionVersion;
                $questionVersion->QuestionId = $question->QuestionId;
                $questionVersion->QuestionVersionId = 0;
                $questionVersion->Created = $now;
                $questionVersion->CreatedBy = $ownerId;
                $questionVersion->QuestionCategoryId = $questionCategoryId;
                if (!$questionVersion->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $question->QuestionVersionId = $questionVersion->QuestionVersionId;
                if (!$question->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $files = $this->getSimpleQuestionFiles($oldQuestionVersionId);
                $this->saveQuestionFiles($question->QuestionId, $question->QuestionVersionId, $files);
                ++$qIndex;
            }
            return true;
        }
        function updateQuestion($idList, $score, $categoryId, $ownerId) {
            if (is_null($score) && is_null($categoryId)) return true;
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $error = 'ARI: Couldnt update questions.';
            $now = ArisDate::getDbUTC();
            foreach($idList as $questionId) {
                $question = $this->getQuestion($questionId, true, false);
                if (empty($question)) continue;
                $questionVersion = $question->QuestionVersion;
                $questionVersionId = $questionVersion->QuestionVersionId;
                $files = $this->getSimpleQuestionFiles($questionVersionId);
                $questionType = $questionVersion->QuestionType;
                $questionObj = AriEntityFactory::createInstance($questionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                if (is_null($categoryId) && (is_null($score) || $questionObj->isScoreSpecific())) {
                    continue;
                }
                $questionVersion->QuestionVersionId = 0;
                $questionVersion->Created = $now;
                $questionVersion->CreatedBy = $ownerId;
                if (!is_null($categoryId)) $questionVersion->QuestionCategoryId = $categoryId;
                if (!is_null($score) && !$questionObj->isScoreSpecific()) $questionVersion->Score = $score;
                if (!$questionVersion->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $question->Modified = $now;
                $question->ModifiedBy = $ownerId;
                $question->QuestionVersionId = $questionVersion->QuestionVersionId;
                if (!is_null($categoryId)) $question->QuestionCategoryId = $categoryId;
                if (!$question->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $this->saveQuestionFiles($question->QuestionId, $questionVersion->QuestionVersionId, $files);
            }
            return true;
        }
        function saveQuestion($questionId, $quizId, $questionTypeId, $ownerId, $fields, $data, $files = null) {
            global $database;
            $error = 'ARI: Couldnt save question.';
            $row = AriEntityFactory::createInstance('AriQuizQuestionEntity', AriGlobalPrefs::getEntityGroup());
            $isUpdate = ($questionId>0);
            if ($isUpdate) {
                $row = $this->getQuestion($questionId);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                $row->Modified = ArisDate::getDbUTC();
                $row->ModifiedBy = $ownerId;
            } else {
                $row->QuizId = $quizId;
                $row->Created = ArisDate::getDbUTC();
                $row->CreatedBy = $ownerId;
                $row->QuestionIndex = $this->_getRecordCount('#__ariquizquestion', array('QuizId'=>$quizId));
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                $row->Status = AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName());
            }
            if (!$row->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $row->QuestionTypeId = $questionTypeId;
            if (!$isUpdate) {
                if (!$row->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
            }
            $subRow = AriEntityFactory::createInstance('AriQuizQuestionVersionEntity', AriGlobalPrefs::getEntityGroup());
            if (!$subRow->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $subRow->QuestionId = $row->QuestionId;
            $subRow->Data = $data;
            $subRow->Created = ArisDate::getDbUTC();
            $subRow->CreatedBy = $ownerId;
            $subRow->QuestionTypeId = $questionTypeId;
            if (!$subRow->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $row->QuestionVersionId = $subRow->QuestionVersionId;
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $this->saveQuestionFiles($row->QuestionId, $row->QuestionVersionId, $files);
            return $row;
        }
        function saveQuestionFiles($questionId, $questionVersionId, $files) {
            global $database;
            if (!is_array($files) || count($files) <1) return true;
            $data = array();
            foreach($files as $alias=>$fileId) {
                $data[] = sprintf("(%d,%d,%d,%s)", $fileId, $questionVersionId, $questionId, $database->Quote($alias));
            }
            $query = 'INSERT INTO #__ariquiz_question_version_files (FileId,QuestionVersionId,QuestionId,Alias) VALUES'.join(',', $data);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                return false;
            }
            return true;
        }
        function getSimpleQuestionFiles($questionVersionId) {
            $simpleFiles = array();
            $files = $this->getQuestionFiles($questionVersionId);
            if (empty($files)) return $simpleFiles;
            foreach($files as $alias=>$dataItem) {
                $simpleFiles[$alias] = $dataItem['FileId'];
            }
            return $simpleFiles;
        }
        function getQuestionsFiles($idList) {
            $files = array();
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return $files;
            global $database;
            $query = sprintf('SELECT QuestionVersionId,FileId,Alias FROM #__ariquiz_question_version_files WHERE QuestionVersionId IN (%s)', join(',', $this->_quoteValues($idList)));
            $database->setQuery($query);
            $data = $database->loadAssocList();
            if ($database->getErrorNum()) {
                return $files;
            }
            foreach($data as $dataItem) {
                $qId = $dataItem['QuestionVersionId'];
                if (!isset($files[$qId])) $files[$qId] = array();
                $files[$qId][$dataItem['Alias']] = $dataItem['FileId'];
            }
            return $files;
        }
        function getLatestQuestionFiles($questionId) {
            global $database;
            $files = array();
            if (empty($questionId)) return $files;
            $fileTable = AriConstantsManager::getVar('FileTable', AriQuizComponent::getCodeName());
            $query = sprintf('SELECT QQVF.FileId,QQVF.Alias,QF.Extension,QF.FileName'.' FROM #__ariquizquestion QQ INNER JOIN #__ariquiz_question_version_files QQVF'.'	ON QQ.QuestionVersionId = QQVF.QuestionVersionId'.' INNER JOIN %2$s QF ON QQVF.FileId = QF.FileId'.' WHERE QQ.QuestionId = %1$d', $questionId, $fileTable);
            $database->setQuery($query);
            $data = $database->loadAssocList();
            if ($database->getErrorNum()) {
                return $files;
            }
            foreach($data as $dataItem) {
                $files[$dataItem['Alias']] = $dataItem;
            }
            return $files;
        }
        function getQuestionFiles($questionVersionId) {
            global $database;
            $files = array();
            if (empty($questionVersionId)) return $files;
            $fileTable = AriConstantsManager::getVar('FileTable', AriQuizComponent::getCodeName());
            $query = sprintf('SELECT QQVF.FileId,QQVF.Alias,QF.Extension,QF.FileName FROM #__ariquiz_question_version_files QQVF INNER JOIN %2$s QF ON QQVF.FileId = QF.FileId WHERE QQVF.QuestionVersionId = %1$d', $questionVersionId, $fileTable);
            $database->setQuery($query);
            $data = $database->loadAssocList();
            if ($database->getErrorNum()) {
                return $files;
            }
            foreach($data as $dataItem) {
                $files[$dataItem['Alias']] = $dataItem;
            }
            return $files;
        }
        function getQuestion($questionId, $loadQuestionVersion = true, $mergeBank = true) {
            global $database;
            $error = 'ARI: Couldnt get question.';
            $entityGroup = AriGlobalPrefs::getEntityGroup();
            $questionEntity = AriEntityFactory::createInstance('AriQuizQuestionEntity', $entityGroup);
            $questionVersionEntity = AriEntityFactory::createInstance('AriQuizQuestionVersionEntity', $entityGroup);
            $questionTypeEntity = AriEntityFactory::createInstance('AriQuizQuestionTypeEntity', $entityGroup);
            $fieldsStr = $loadQuestionVersion ? ','.$this->_getModifiedFields($questionVersionEntity, 'QQV', '_1') .','.$this->_getModifiedFields($questionTypeEntity, 'QQT', '_2') .($mergeBank ? ','.$this->_getModifiedFields($questionVersionEntity, 'QQVB', '_3') : ''):
                '';
                $query = sprintf('SELECT QQ.*'.$fieldsStr.' FROM'.' #__ariquizquestion QQ'.($loadQuestionVersion ? ' INNER JOIN #__ariquizquestionversion QQV'.'	ON QQ.QuestionVersionId = QQV.QuestionVersionId'.' LEFT JOIN #__ariquizquestion QQB'.'	ON QQ.BankQuestionId = QQB.QuestionId'.' LEFT JOIN #__ariquizquestionversion QQVB'.'	ON QQB.QuestionVersionId = QQVB.QuestionVersionId'.' INNER JOIN #__ariquizquestiontype QQT'.'	ON QQT.QuestionTypeId = IFNULL(QQVB.QuestionTypeId,QQV.QuestionTypeId)' : '') .' WHERE QQ.QuestionId = %d'.' LIMIT 0,1', $questionId);
                $database->setQuery($query);
                $fields = $database->loadAssocList();
                if ($database->getErrorNum()) {
                    trigger_error($error, E_USER_ERROR);
                    return 0;
                }
                if (empty($fields) || count($fields) <1) return null;
                $fields = $loadQuestionVersion ? $this->_modifySource($fields[0], array(array('Entity'=>$questionVersionEntity, 'Postfix'=>'_1', 'Key'=>'QuestionVersion'), array('Entity'=>$questionTypeEntity, 'Postfix'=>'_2', 'Key'=>'QuestionType', 'Parent'=>array('QuestionVersion', 'BankQuestionVersion')), array('Entity'=>$questionVersionEntity, 'Postfix'=>'_3', 'Key'=>'BankQuestionVersion'),)) : $fields[0];
                if (empty($fields['BankQuestionId'])) $fields['BankQuestionVersion'] = null;
                $questionEntity->bind($fields, array(), $loadQuestionVersion);
                return $questionEntity;
            }
            function getQuestionTemplate($templateId) {
                global $database;
                $error = 'ARI: Couldnt get question template.';
                $templateId = intval($templateId);
                $template = AriEntityFactory::createInstance('AriQuizQuestionTemplateEntity', AriGlobalPrefs::getEntityGroup());
                if (!$template->load($templateId)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                $questionType = $this->getQuestionType($template->QuestionTypeId);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                $template->QuestionType = $questionType;
                return $template;
            }
            function getQuestionTemplateCount($filter = null) {
                global $database;
                $query = sprintf('SELECT COUNT(QQT.QuestionTypeId) '.' FROM #__ariquizquestiontemplate QQT'.' INNER JOIN #__ariquizquestiontype QQTY ON QQT.QuestionTypeId = QQTY.QuestionTypeId');
                $query = $this->_applyDbCountFilter($query, $filter);
                $database->setQuery($query);
                $count = $database->loadResult();
                if ($database->getErrorNum()) {
                    trigger_error('ARI: Couldnt get question template count.', E_USER_ERROR);
                    return 0;
                }
                return $count;
            }
            function getQuestionTemplateList($filter = null) {
                global $database;
                $joinType = 'LEFT';
                if (!empty($filter)) {
                    $sortField = $filter->getConfigValue('sortField');
                    if ($sortField == 'QuestionType') $joinType = 'RIGHT';
                }
                $query = sprintf('SELECT QQT.QuestionTypeId, QQT.TemplateName, QQT.TemplateId, QQT.Created, QQT.Modified, QQTY.QuestionType '.' FROM #__ariquizquestiontemplate QQT'.' '.$joinType.' JOIN #__ariquizquestiontype QQTY ON QQT.QuestionTypeId = QQTY.QuestionTypeId');
                $query = $this->_applyFilter($query, $filter);
                $database->setQuery($query);
                $rows = $database->loadObjectList();
                if ($database->getErrorNum()) {
                    trigger_error('ARI: Couldnt get question template list.', E_USER_ERROR);
                    return null;
                }
                return $rows;
            }
            function deleteQuestion($idList) {
                $complete = $this->changeQuestionStatus($idList, AriConstantsManager::getVar('Status.Delete', AriQuizQuestionControllerConstants::getClassName()));
                if ($this->_isError(true, false)) {
                    trigger_error('ARI: Couldnt delete question.', E_USER_ERROR);
                    return false;
                }
                return $complete;
            }
            function changeQuestionStatus($idList, $status) {
                $idList = $this->_fixIdList($idList);
                if (empty($idList)) return true;
                global $database;
                $status = intval($status);
                $query = sprintf('UPDATE #__ariquizquestion SET Status = %d WHERE QuestionId IN (%s)', $status, join(',', $this->_quoteValues($idList)));
                $database->setQuery($query);
                $database->query();
                if ($database->getErrorNum()) {
                    trigger_error('ARI: Couldnt change question status.', E_USER_ERROR);
                    return false;
                }
                return true;
            }
            function changeQuestionOrder($questionId, $dir) {
                global $database;
                $error = 'ARI: Couldnt change question order.';
                $questionId = intval($questionId);
                $dir = intval($dir);
                $query = sprintf('SELECT QQ1.QuestionId, QQ.QuestionIndex AS OldIndex, QQ1.QuestionIndex AS NewIndex '.' FROM #__ariquizquestion QQ LEFT JOIN #__ariquizquestion QQ1'.' 	ON QQ.QuizId = QQ1.QuizId'.' WHERE QQ.QuestionId = %d AND QQ1.QuestionIndex %s QQ.QuestionIndex AND QQ1.Status = %d ORDER BY QQ1.QuestionIndex %s LIMIT 0,1', $questionId, $dir>0 ? '>' : '<', AriConstantsManager::getVar('Status.Active', AriQuizQuestionControllerConstants::getClassName()), $dir>0 ? 'ASC' : 'DESC');
                $database->setQuery($query);
                $obj = $database->loadAssocList();
                if ($database->getErrorNum()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                if (!empty($obj) && count($obj) >0) {
                    $obj = $obj[0];
                    $queryList = array();
                    $queryList[] = sprintf('UPDATE #__ariquizquestion SET QuestionIndex = %d WHERE QuestionId = %d', $obj['NewIndex'], $questionId);
                    $queryList[] = sprintf('UPDATE #__ariquizquestion SET QuestionIndex = %d WHERE QuestionId = %d', $obj['OldIndex'], $obj['QuestionId']);
                    $database->setQuery(join($queryList, ';'));
                    if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
                else $database->query_batch();
                if ($database->getErrorNum()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
            }
            return true;
        }
        function saveQuestionTemplate($templateId, $questionTypeId, $ownerId, $fields, $data) {
            global $database;
            $error = 'ARI: Couldnt save question template.';
            $row = AriEntityFactory::createInstance('AriQuizQuestionTemplateEntity', AriGlobalPrefs::getEntityGroup());
            $isUpdate = ($templateId>0);
            if ($isUpdate) {
                $row = $this->getQuestionTemplate($templateId);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                $row->Modified = ArisDate::getDbUTC();
                $row->ModifiedBy = $ownerId;
            } else {
                $row->Created = ArisDate::getDbUTC();
                $row->CreatedBy = $ownerId;
            }
            $row->DisableValidation = !empty($fields['DisableValidation']);
            if (!$row->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $row->QuestionTypeId = $questionTypeId;
            $row->Data = $data;
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            return $row;
        }
        function deleteQuestionTemplate($idList) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $idStr = join(',', $this->_quoteValues($idList));
            $query = sprintf('DELETE FROM #__ariquizquestiontemplate WHERE TemplateId IN (%s)', $idStr);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt delete question template.', E_USER_ERROR);
                return false;
            }
            return true;
        }
    };
?>