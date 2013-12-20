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
    AriKernel::import('TextTemplates.TextTemplateController');
    AriKernel::import('Controllers.AriQuiz.PropertyController');
    AriKernel::import('Controllers.AriQuiz.QuestionCategoryController');
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Data.DDLManager');
    AriKernel::import('SimpleTemplate.SimpleTemplate');
    class AriQuizControllerConstants extends AriClassConstants {
        var $Status = array('Active'=>1, 'Inactive'=>2, 'Delete'=>4);
        var $QuestionOrderType = array('Numeric'=>'Numeric', 'AlphaLower'=>'AlphaLower', 'AlphaUpper'=>'AlphaUpper');
        var $AnonymousStatus = array('Yes'=>'Yes', 'No'=>'No', 'ByUser'=>'ByUser');
        var $FullStatisticsType = array('Never'=>'Never', 'Always'=>'Always', 'OnLastAttempt'=>'OnLastAttempt');
        function getClassName() {
            return strtolower('AriQuizControllerConstants');
        }
    }
    new AriQuizControllerConstants();
    class AriQuizController extends AriControllerBase {
        function _applyQuizFilter($query, $filter) {
            if ($filter) {
                $filterPredicates = $filter->getConfigValue('filter');
                if (!empty($filterPredicates['CategoryId'])) $query.= ' AND QQC.CategoryId='.intval($filterPredicates['CategoryId'], 10);
                if (!empty($filterPredicates['Status'])) $query.= ' AND Status='.intval($filterPredicates['Status'], 10);
            }
            return $query;
        }
        function getQuizList($filter = null) {
            global $database;
            $query = sprintf('SELECT Q.QuizId, Q.QuizName, Q.Status, QC.CategoryName'.' FROM #__ariquiz Q LEFT JOIN #__ariquizquizcategory QQC'.' 	ON Q.QuizId = QQC.QuizId'.' LEFT JOIN #__ariquizcategory QC'.' 	ON QQC.CategoryId = QC.CategoryId'.' WHERE Status <> '.AriConstantsManager::getVar('Status.Delete', AriQuizControllerConstants::getClassName()));
            $query = $this->_applyQuizFilter($query, $filter);
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get quiz list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function getQuizCount($filter = null) {
            global $database;
            $query = sprintf('SELECT COUNT(*)'.' FROM #__ariquiz Q LEFT JOIN #__ariquizquizcategory QQC'.' 	ON Q.QuizId = QQC.QuizId'.' LEFT JOIN #__ariquizcategory QC'.' 	ON QQC.CategoryId = QC.CategoryId'.' WHERE Q.Status <> '.AriConstantsManager::getVar('Status.Delete', AriQuizControllerConstants::getClassName()));
            $query = $this->_applyQuizFilter($query, $filter);
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get quiz count.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function changeQuizStatus($idList, $status) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $status = intval($status);
            $query = sprintf('UPDATE #__ariquiz SET Status = %d WHERE QuizId IN (%s)', $status, join(',', $this->_quoteValues($idList)));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt change quiz status.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function deleteQuiz($idList) {
            global $database;
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            $updIdList = array();
            foreach($idList as $id) {
                $id = intval($id, 10);
                if ($id>0) $updIdList[] = $id;
            }
            if (empty($updIdList)) return true;
            $query = sprintf('DELETE Q,QA,EQ,QQC,QQUEC,QQ,QQV,QQVF,QSI,QS,QSA,QPV,TEM FROM'.' #__ariquiz Q LEFT JOIN #__ariquizaccess QA'.'	ON Q.QuizId = QA.QuizId'.' LEFT JOIN #__ariquiz_export_quiz EQ'.'	ON Q.QuizId = EQ.QuizId'.' LEFT JOIN #__ariquizquizcategory QQC'.'	ON Q.QuizId = QQC.QuizId'.' LEFT JOIN #__ariquiz_property_value QPV'.'	ON Q.QuizId = QPV.EntityKey'.' LEFT JOIN #__ariquiz_property QP'.'	ON QP.PropertyId = QPV.PropertyId'.' LEFT JOIN #__arigenerictemplateentitymap TEM'.'	ON Q.QuizId = TEM.EntityId'.' LEFT JOIN #__ariquizquestioncategory QQUEC'.'	ON Q.QuizId = QQUEC.QuizId'.' LEFT JOIN #__ariquizquestion QQ'.'	ON Q.QuizId = QQ.QuizId'.' LEFT JOIN #__ariquizquestionversion QQV'.'	ON QQ.QuestionId = QQV.QuestionId'.' LEFT JOIN #__ariquiz_question_version_files QQVF'.'	ON QQV.QuestionVersionId = QQVF.QuestionVersionId'.' LEFT JOIN #__ariquizstatisticsinfo QSI'.'	ON Q.QuizId = QSI.QuizId'.' LEFT JOIN #__ariquizstatistics QS'.'	ON QSI.StatisticsInfoId = QS.StatisticsInfoId'.' LEFT JOIN #__ariquizstatistics_attempt QSA'.'	ON QS.StatisticsId = QSA.StatisticsId'.' WHERE Q.QuizId IN (%1$s) AND (QP.Entity IS NULL OR QP.Entity = %2$s) AND (TEM.EntityName IS NULL OR TEM.EntityName = %2$s)', join(',', $this->_quoteValues($updIdList)), $database->Quote(AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName())));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt delete quiz.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function activateQuiz($idList) {
            $complete = $this->changeQuizStatus($idList, AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()));
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt activate quiz.', E_USER_ERROR);
                return false;
            }
            return $complete;
        }
        function deactivateQuiz($idList) {
            $complete = $this->changeQuizStatus($idList, AriConstantsManager::getVar('Status.Inactive', AriQuizControllerConstants::getClassName()));
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt deactivate quiz.', E_USER_ERROR);
                return false;
            }
            return $complete;
        }
        function getQuiz($quizId, $fullLoad = true) {
            global $database;
            $error = 'ARI: Couldnt get quiz.';
            $quizId = intval($quizId, 10);
            $quiz = AriEntityFactory::createInstance('AriQuizEntity', AriGlobalPrefs::getEntityGroup());
            $quiz->load($quizId);
            if (!$fullLoad) return $quiz;
            $query = sprintf('SELECT CategoryId FROM #__ariquizquizcategory WHERE QuizId = %d', $quizId);
            $database->setQuery($query);
            $quiz->CategoryList = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $quiz->AccessList = $this->getQuizAccessList($quizId);
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            return $quiz;
        }
        function getQuizAccessList($quizId) {
            global $database;
            $query = sprintf('SELECT GroupId AS value FROM #__ariquizaccess WHERE QuizId = %d', $quizId);
            $database->setQuery($query);
            $accessList = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get quiz access list.', E_USER_ERROR);
                return null;
            }
            return $accessList;
        }
        function copyQuizzes($idList, $quizName, $ownerId) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $error = 'ARI: Couldnt copy quizzes.';
            $now = ArisDate::getDbUTC();
            $quizEntityKey = AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName());
            foreach($idList as $quizId) {
                $quiz = $this->getQuiz($quizId);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                if (empty($quiz) || !$quiz->QuizId) continue;
                $copyQuiz = AriEntityFactory::createInstance('AriQuizEntity', AriGlobalPrefs::getEntityGroup());
                $copyQuiz->bind($quiz->getPublicFields());
                $copyQuiz->QuizId = 0;
                $copyQuiz->Created = $now;
                $copyQuiz->CreatedBy = $ownerId;
                $copyQuiz->Modified = null;
                $copyQuiz->ModifiedBy = null;
                $copyQuiz->QuizName = AriSimpleTemplate::parse($quizName, array('QuizName'=>$quiz->QuizName));
                if (!$copyQuiz->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return false;
                }
                $copyQuizId = $copyQuiz->QuizId;
                $categoryList = $quiz->CategoryList;
                $accessList = $quiz->AccessList;
                if (!empty($categoryList)) {
                    $query = sprintf('INSERT INTO #__ariquizquizcategory (CategoryId,QuizId) VALUES(%%s,%s)', $copyQuizId);
                    foreach($categoryList as $category) {
                        $categoryId = $category->CategoryId;
                        if ($categoryId) {
                            $database->setQuery(sprintf($query, $database->Quote($categoryId)));
                            $database->query();
                        }
                    }
                }
                if (!empty($accessList)) {
                    $query = sprintf('INSERT INTO #__ariquizaccess (QuizId,GroupId) VALUES(%d,%%d)', $copyQuizId);
                    foreach($accessList as $group) {
                        $groupId = $group->value;
                        if ($groupId>0) {
                            $database->setQuery(sprintf($query, $groupId));
                            $database->query();
                        }
                    }
                }
                $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName()));
                $textTemplateList = $templateController->call('getEntitySingleTemplate', $quizEntityKey, $quizId);
                $templateController->setEntitySingleTemplate($quizEntityKey, $copyQuizId, $textTemplateList, true);
                $entityKey = AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName());
                $propertyController = $this->_createPropertyController();
                $props = $propertyController->getSimpleProperties($entityKey, $quizId);
                $propertyController->saveProperties($entityKey, $copyQuizId, $props, true);
                $questionCatController = new AriQuizQuestionCategoryController();
                $catMapping = $questionCatController->copy($quizId, $copyQuizId, $ownerId);
                $questionController = new AriQuizQuestionController();
                $questionList = $questionController->getSimpleQuestionList($quizId, new AriDataFilter(array('sortField'=>'QuestionCategoryId', 'sortDirection'=>'asc')));
                if (is_array($questionList) && count($questionList) >0) {
                    $prevCatId = -1;
                    $catQuestionList = array();
                    $catQuestionListMapping = array();
                    foreach($questionList as $question) {
                        $queCatId = !is_null($question->QuestionCategoryId) ? $question->QuestionCategoryId : 0;
                        if ($prevCatId != $queCatId) {
                            $newCatId = $queCatId>0 ? (isset($catMapping[$queCatId]) ? $catMapping[$queCatId] : -1):
                                0;
                                if ($newCatId>-1) {
                                    $catQuestionListMapping[$newCatId] = array();
                                    $catQuestionList = &$catQuestionListMapping[$newCatId];
                                }
                                $prevCatId = $queCatId;
                            }
                            $catQuestionList[] = $question->QuestionId;
                        }
                        foreach($catQuestionListMapping as $categoryId=>$idList) {
                            $questionController->copy($idList, $copyQuizId, $categoryId, $ownerId);
                        }
                    }
                }
                return true;
            }
            function updateQuiz($dataConfigFile, $idList, $fields, $categoryList, $textTemplateList, $props, $ownerId) {
                if (empty($fields)) $fields = array();
                $idList = $this->_fixIdList($idList);
                if (empty($idList)) return true;
                global $database;
                $ddlManager = new AriDDLManager($dataConfigFile);
                $errorMessage = 'ARI: Couldnt update quizzes.';
                $entity = 'quiz';
                $queryParts = array();
                $catStr = join(',', $this->_quoteValues($idList));
                $fields['Modified'] = ArisDate::getDbUTC();
                $fields['ModifiedBy'] = $ownerId;
                foreach($fields as $key=>$value) {
                    $fieldInfo = $ddlManager->getFieldInfo($entity, $key);
                    if (is_null($fieldInfo)) continue;
                    if ($ddlManager->isBool($entity, $key)) {
                        $value = @intval($value, 10);
                        if ($value) $value = 1;
                    } else if ($ddlManager->isNumberField($entity, $key)) {
                        $value = @intval($value, 10);
                    }
                    $queryParts[] = sprintf('`%s`=%s', $key, $database->Quote($value));
                }
                $queryParts = join(',', $queryParts);
                $query = sprintf('UPDATE #__ariquiz SET %s WHERE QuizId IN (%s)', $queryParts, $catStr);
                $database->setQuery($query);
                $database->query();
                if ($database->getErrorNum()) {
                    trigger_error($errorMessage, E_USER_ERROR);
                    return false;
                }
                if (!empty($categoryList)) {
                    $query = sprintf('DELETE FROM #__ariquizquizcategory WHERE QuizId IN (%s)', $catStr);
                    $database->setQuery($query);
                    $database->query();
                    $queryParts = array();
                    foreach($idList as $quizId) {
                        foreach($categoryList as $categoryId) {
                            if (!$categoryId) continue;
                            $queryParts[] = sprintf('(%d,%d)', $categoryId, $quizId);
                        }
                    }
                    if (count($queryParts) >0) {
                        $query = sprintf('INSERT INTO #__ariquizquizcategory (CategoryId,QuizId) VALUES%s', join(',', $queryParts));
                        $database->setQuery(sprintf($query, $database->Quote($categoryId)));
                        $database->query();
                        if ($database->getErrorNum()) {
                            trigger_error($errorMessage, E_USER_ERROR);
                            return false;
                        }
                    }
                }
                if (!empty($textTemplateList)) {
                    $resultScaleId = @intval(AriUtils::getParam($fields, 'ResultScaleId', 0), 10);
                    if ($resultScaleId>0) {
                        $adminEmailIndex = AriConstantsManager::getVar('TextTemplates.AdminEmail', AriQuizComponent::getCodeName());
                        if (isset($textTemplateList[$adminEmailIndex])) {
                            $textTemplateList = array($adminEmailIndex=>$textTemplateList[$adminEmailIndex]);
                        } else {
                            $textTemplateList = null;
                        }
                    }
                    if (!empty($textTemplateList)) {
                        foreach($idList as $quizId) {
                            $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName()));
                            $templateController->setEntitySingleTemplate(AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName()), $quizId, $textTemplateList, true);
                        }
                    }
                }
                $entityKey = AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName());
                $propertyController = $this->_createPropertyController();
                foreach($idList as $quizId) {
                    $propertyController->saveProperties($entityKey, $quizId, $props, true);
                }
                return true;
            }
            function saveQuiz($quizId, $fields, $ownerId, $categoryList, $accessList, $textTemplateList, $props = null) {
                global $database;
                $error = 'ARI: Couldnt save quiz.';
                $quizId = intval($quizId);
                $isUpdate = ($quizId>0);
                $row = $isUpdate ? $this->getQuiz($quizId) : AriEntityFactory::createInstance('AriQuizEntity', AriGlobalPrefs::getEntityGroup());
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                if (!$row->bind($fields)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                if ($isUpdate) {
                    $query = sprintf('DELETE FROM #__ariquizquizcategory WHERE QuizId = %d', $quizId);
                    $database->setQuery($query);
                    $database->query();
                    $query = sprintf('DELETE FROM #__ariquizaccess WHERE QuizId = %d', $quizId);
                    $database->setQuery($query);
                    $database->query();
                    $row->Modified = ArisDate::getDbUTC();
                    $row->ModifiedBy = $ownerId;
                } else {
                    $row->Created = ArisDate::getDbUTC();
                    $row->CreatedBy = $ownerId;
                }
                $statusList = AriConstantsManager::getVar('Status', AriQuizControllerConstants::getClassName());
                $qOrderTypes = AriConstantsManager::getVar('QuestionOrderType', AriQuizControllerConstants::getClassName());
                $qOrderTypesValues = array_values($qOrderTypes);
                $statsTypes = AriConstantsManager::getVar('FullStatisticsType', AriQuizControllerConstants::getClassName());
                $statsTypesValues = array_values($statsTypes);
                $anonStatuses = AriConstantsManager::getVar('AnonymousStatus', AriQuizControllerConstants::getClassName());
                $anonStatusesValues = array_values($anonStatuses);
                $row->Status = !empty($fields['Active']) ? $statusList['Active'] : $statusList['Inactive'];
                $row->ParsePluginTag = !empty($fields['ParsePluginTag']) ? 1 : 0;
                $row->CanSkip = !empty($fields['CanSkip']) ? 1 : 0;
                $row->CanStop = !empty($fields['CanStop']) ? 1 : 0;
                $row->RandomQuestion = !empty($fields['RandomQuestion']) ? 1 : 0;
                $row->UseCalculator = !empty($fields['UseCalculator']) ? 1 : 0;
                $row->ShowCorrectAnswer = !empty($fields['ShowCorrectAnswer']) ? 1 : 0;
                $row->ShowExplanation = !empty($fields['ShowExplanation']) ? 1 : 0;
                if (!in_array($row->QuestionOrderType, $qOrderTypesValues)) $row->QuestionOrderType = $qOrderTypes['Numeric'];
                if (!in_array($row->FullStatistics, $statsTypesValues)) $row->FullStatistics = $statsTypes['Never'];
                if (!in_array($row->Anonymous, $anonStatusesValues)) $row->Anonymous = $anonStatuses['Yes'];
                if (!$row->store()) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
                if ($row->ResultScaleId) {
                    $adminEmailIndex = AriConstantsManager::getVar('TextTemplates.AdminEmail', AriQuizComponent::getCodeName());
                    if (isset($textTemplateList[$adminEmailIndex])) {
                        $textTemplateList = array($adminEmailIndex=>$textTemplateList[$adminEmailIndex]);
                    } else {
                        $textTemplateList = null;
                    }
                }
                $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName()));
                $templateController->setEntitySingleTemplate(AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName()), $row->QuizId, $textTemplateList);
                if (!empty($categoryList)) {
                    $query = sprintf('INSERT INTO #__ariquizquizcategory (CategoryId,QuizId) VALUES(%%s,%s)', $row->QuizId);
                    foreach($categoryList as $categoryId) {
                        if ($categoryId) {
                            $database->setQuery(sprintf($query, $database->Quote($categoryId)));
                            $database->query();
                        }
                    }
                }
                if (!empty($accessList)) {
                    $query = sprintf('INSERT INTO #__ariquizaccess (QuizId,GroupId) VALUES(%d,%%d)', $row->QuizId);
                    foreach($accessList as $groupId) {
                        $groupId = intval($groupId);
                        if ($groupId>0) {
                            $database->setQuery(sprintf($query, $groupId));
                            $database->query();
                        }
                    }
                }
                $entityKey = AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName());
                $propertyController = $this->_createPropertyController();
                $propertyController->saveProperties($entityKey, $row->QuizId, $props);
                return $row;
            }
            function isUniqueQuizName($name, $id = null) {
                $isUnique = $this->_isUniqueField('#__ariquiz', 'QuizName', $name, 'QuizId', $id);
                if ($this->_isError(true, false)) {
                    trigger_error('ARI: Couldnt check unique quiz name.', E_USER_ERROR);
                    return false;
                }
                return $isUnique;
            }
            function getSimpleProperties($quizId) {
                $props = $this->getProperties($quizId);
                if (empty($props)) return null;
                $simpleProps = array();
                foreach($props as $propItem) {
                    $simpleProps[$propItem->PropertyName] = $propItem->PropertyValue;
                }
                return $simpleProps;
            }
            function getProperties($quizId) {
                $propertyController = $this->_createPropertyController();
                $entityKey = AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName());
                $props = $propertyController->call('getProperties', $entityKey, $quizId);
                return $props;
            }
            function _createPropertyController() {
                $propertyTables = AriConstantsManager::getVar('PropertyTable', AriQuizComponent::getCodeName());
                $propertyController = new AriPropertyController($propertyTables['Property'], $propertyTables['PropertyValue']);
                return $propertyController;
            }
            function isHasNotFinishedQuiz($quizId, $userId) {
                global $database;
                $cnt = 0;
                if (!empty($userId)) {
                    $query = sprintf('SELECT COUNT(QSI.*)'.' FROM #__ariquizstatisticsinfo QSI'.' WHERE (QSI.Status = "Process" OR QSI.Status = "Prepare") AND UserId = %d AND QuizId = %d GROUP BY QuizId', $userId, $quizId);
                    $database->setQuery($query);
                    $cnt = $database->loadResult();
                    if ($database->getErrorNum()) {
                        trigger_error('ARI: Couldnt check not finished quiz.', E_USER_ERROR);
                        return TRUE;
                    }
                }
                return ($cnt>0);
            }
        };
?>