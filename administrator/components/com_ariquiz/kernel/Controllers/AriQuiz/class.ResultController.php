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
    AriKernel::import('Date.Date');
    AriKernel::import('Controllers.ControllerBase');
    AriKernel::import('Controllers.AriQuiz.QuizController');
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Controllers.AriQuiz._Templates.ResultTemplates');
    AriKernel::import('Web.JSON.JSONHelper');
    AriKernel::import('Mambot.MambotProcessHelper');
    class AriQuizResultController extends AriControllerBase {
        function getFinishedInfoByCategory($statisticsInfoId) {
            global $database;
            $statisticsInfoId = @intval($statisticsInfoId, 10);
            $query = sprintf('SELECT QC.CategoryName,IFNULL(SUM(QS.Score), 0) AS UserScore,SUM(IF(QV.Score, QV.Score, QV2.Score)) AS MaxScore'.' FROM #__ariquizstatistics QS LEFT JOIN #__ariquizquestioncategory QC'.'	ON QS.QuestionCategoryId = QC.QuestionCategoryId'.' INNER JOIN #__ariquizquestionversion QV'.'	ON QS.QuestionVersionId = QV.QuestionVersionId'.' LEFT JOIN #__ariquizquestionversion QV2'.' 	ON QS.BankVersionId = QV2.QuestionVersionId'.' WHERE QS.StatisticsInfoId = %d AND QS.QuestionCategoryId > 0'.' GROUP BY QS.QuestionCategoryId'.' ORDER BY QC.CategoryName', $statisticsInfoId);
            $database->setQuery($query);
            $result = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get statistics by question category.', E_USER_ERROR);
                return null;
            }
            if ($result) {
                for ($i = 0, $c = count($result);$i<$c;$i++) {
                    $resultItem = $result[$i];
                    $userScore = intval($resultItem['UserScore'], 10);
                    $maxScore = intval($resultItem['MaxScore'], 10);
                    $result[$i]['PercentScore'] = $maxScore>0 ? sprintf('%.2f', 100*($userScore/$maxScore)) : 0;
                }
            }
            return $result;
        }
        function getStatCount($statisticsInfoId, $filter = null) {
            global $database;
            $query = sprintf('SELECT COUNT(*) '.' FROM #__ariquizstatistics S'.' WHERE StatisticsInfoId = %d', $statisticsInfoId);
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get stat count.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function getStatList($statisticsInfoId, $filter = null, $getFiles = true) {
            global $database;
            $query = sprintf('SELECT IF(S.BankVersionId = 0, S.QuestionVersionId, S.BankVersionId) AS BaseQuestionVersionId,S.Data AS UserData,S.QuestionIndex,S.Score AS UserScore,IF(QV.Score, QV.Score, QV2.Score) AS MaxScore,IF(QV2.QuestionId,QV2.Question,QV.Question) AS Question,IF(QV2.QuestionId,QV2.Note,QV.Note) AS QuestionNote,IF(QV2.QuestionId,QV2.Data,QV.Data) AS QuestionData,IF(QV2.QuestionId, QV2.QuestionTypeId,QV.QuestionTypeId) AS QuestionTypeId,QT.ClassName AS QuestionClassName,QT.QuestionType,QC.CategoryName,(IFNULL(UNIX_TIMESTAMP(IF(S.StartDate IS NOT NULL, IFNULL(S.EndDate,SI.EndDate), NULL)) - UNIX_TIMESTAMP(S.StartDate), 0) + S.UsedTime) AS TotalTime, S.QuestionTime, SI.TotalTime AS QuizTotalTime '.' FROM #__ariquizstatisticsinfo SI INNER JOIN #__ariquizstatistics S'.' 	ON SI.StatisticsInfoId = S.StatisticsInfoId'.' INNER JOIN #__ariquizquestionversion QV'.'	ON S.QuestionVersionId = QV.QuestionVersionId'.' LEFT JOIN #__ariquizquestionversion QV2'.' 	ON S.BankVersionId = QV2.QuestionVersionId'.' INNER JOIN #__ariquizquestiontype QT'.'	ON IFNULL(QV2.QuestionTypeId, QV.QuestionTypeId) = QT.QuestionTypeId'.' LEFT JOIN #__ariquizquestioncategory QC'.' 	ON S.QuestionCategoryId = QC.QuestionCategoryId'.' WHERE SI.StatisticsInfoId = %d', $statisticsInfoId);
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $statList = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get statistics list.', E_USER_ERROR);
                return null;
            }
            $cnt = $statList ? count($statList) : 0;
            if ($cnt) {
                $quizTotalTime = $statList[0]->QuizTotalTime;
                $isIgnoreTime = empty($quizTotalTime);
                $sumTotalTime = 0;
                $qVersionIdList = array();
                for ($i = 0;$i<$cnt;$i++) {
                    $statItem = &$statList[$i];
                    $specificQuestion = AriEntityFactory::createInstance($statItem->QuestionClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                    $statItem->UserData = $specificQuestion->getDataFromXml($statItem->UserData);
                    $statItem->QuestionData = $specificQuestion->getDataFromXml($statItem->QuestionData, false);
                    $queTotalTime = $statItem->TotalTime ? $statItem->TotalTime : $statItem->QuestionTime;
                    if (!$isIgnoreTime && !empty($queTotalTime)) {
                        if (($sumTotalTime+$queTotalTime) <$quizTotalTime) {
                            $sumTotalTime+= $queTotalTime;
                        } else {
                            $queTotalTime = $quizTotalTime-$sumTotalTime;
                            $sumTotalTime = $quizTotalTime;
                        }
                    }
                    $statItem->TotalTime = $queTotalTime;
                    $qVersionIdList[] = $statItem->BaseQuestionVersionId;
                }
                if ($getFiles) {
                    $queController = new AriQuizQuestionController();
                    $files = $queController->call('getQuestionsFiles', $qVersionIdList);
                    for ($i = 0;$i<$cnt;$i++) {
                        $statItem = &$statList[$i];
                        $qId = $statItem->BaseQuestionVersionId;
                        $statItem->Files = (isset($files[$qId])) ? $files[$qId] : null;
                    }
                }
            }
            return $statList;
        }
        function getJsonStatList($statisticsInfoId, $filter = null, $processMambots = false) {
            $statList = $this->getStatList($statisticsInfoId, $filter);
            if ($processMambots && $statList) {
                for ($i = 0, $cnt = count($statList);$i<$cnt;$i++) {
                    $statList[$i]->Question = AriMambotProcessHelper::processMambotTags($statList[$i]->Question, true);
                }
            }
            $jsonStatList = array();
            if ($statList) {
                foreach($statList as $item) {
                    $jsonStatList[] = array('QuestionData'=>AriJSONHelper::encode($item), 'QuestionIndex'=>$item->QuestionIndex);
                }
            }
            return $jsonStatList;
        }
        function deleteResult($idList) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $qIdList = join(',', $this->_quoteValues($idList));
            $queryList = array();
            $queryList[] = sprintf('DELETE FROM #__ariquizstatistics WHERE StatisticsInfoId IN (%s)', $qIdList);
            $queryList[] = sprintf('DELETE FROM #__ariquizstatisticsinfo WHERE StatisticsInfoId IN (%s)', $qIdList);
            $database->setQuery(join($queryList, ';'));
            if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
        else $database->query_batch();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt delete quiz results.', E_USER_ERROR);
            return false;
        }
        return true;
    }
    function deleteAllResult() {
        global $database;
        $query = 'DELETE S,SI FROM #__ariquizstatisticsinfo SI INNER JOIN #__ariquizstatistics S'.' ON SI.StatisticsInfoId = S.StatisticsInfoId'.' WHERE SI.`Status` = "Finished"';
        $database->setQuery($query);
        $database->query();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt delete all quiz results.', E_USER_ERROR);
            return false;
        }
        return true;
    }
    function sendResultInfo($ticketId) {
        global $database;
        $ticketId = trim($ticketId);
        if (empty($ticketId)) return null;
        $textTemplateTable = AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName());
        $query = sprintf('SELECT Q.AdminEmail'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.'	ON QSI.QuizId = Q.QuizId'.' INNER JOIN %sentitymap GTEM'.'	ON GTEM.EntityId = Q.QuizId'.' WHERE QSI.TicketId = %s AND (Q.AdminEmail IS NOT NULL AND LENGTH(Q.AdminEmail) > 0) AND GTEM.EntityName = %s AND GTEM.TemplateType = %s AND QSI.ResultEmailed <> 1'.' LIMIT 0,1', $textTemplateTable, $database->Quote($ticketId), $database->Quote(AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName())), $database->Quote(AriConstantsManager::getVar('TextTemplates.AdminEmail', AriQuizComponent::getCodeName())));
        $database->setQuery($query);
        $info = $database->loadResultArray();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt check result.', E_USER_ERROR);
            return null;
        }
        return $info;
    }
    function markResultSend($ticketId) {
        global $database;
        $ticketId = trim($ticketId);
        if (empty($ticketId)) return;
        $query = sprintf('UPDATE #__ariquizstatisticsinfo SET ResultEmailed = 1 WHERE TicketId = %s', $database->Quote($ticketId));
        $database->setQuery($query);
        $database->query();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt mark result is sent.', E_USER_ERROR);
            return;
        }
    }
    function getQuizTopResultsCount($quizId, $ignoreGuest = true, $filter = null) {
        global $database;
        $quizId = intval($quizId, 10);
        $query = sprintf('SELECT COUNT(*)'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' LEFT JOIN #__users U'.'  ON QSI.UserId = U.id'.' WHERE '.($ignoreGuest ? ' U.id > 0 AND ' : '') .' QSI.Status = "Finished" AND Q.Status = %d AND Q.QuizId = %d ', AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()), $quizId);
        $query = $this->_applyDbCountFilter($query, $filter);
        $database->setQuery($query);
        $cnt = $database->loadResult();
        if ($database->getErrorNum()) {
            return 0;
        }
        return $cnt;
    }
    function getQuizTopResults($quizId, $ignoreGuest = true, $filter = null) {
        global $database;
        $quizId = intval($quizId, 10);
        $results = null;
        $query = sprintf('SELECT U.name AS UserName, Q.QuizName, QSI.UserScore, QSI.MaxScore, ((QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore, (UNIX_TIMESTAMP(QSI.EndDate) - UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate,QSI.StartDate)) + QSI.UsedTime) AS QuizTotalTime'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' LEFT JOIN #__users U'.'  ON QSI.UserId = U.id'.' WHERE '.($ignoreGuest ? ' U.id > 0 AND ' : '') .' QSI.Status = "Finished" AND Q.Status = %d AND Q.QuizId = %d'.' GROUP BY QSI.StatisticsInfoId', AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()), $quizId);
        $query = $this->_applyFilter($query, $filter);
        $database->setQuery($query);
        $results = $database->loadAssocList();
        if ($database->getErrorNum()) {
            return null;
        }
        return $results;
    }
    function getAdvTopResults($quizIdList, $revertQuizId = false, $count = 5, $ignoreGuest = true, $minScore = 0) {
        global $database;
        $minScore = @intval($minScore, 10);
        $count = @intval($count, 10);
        $quizIdList = $this->_fixIdList($quizIdList);
        $results = null;
        $query = sprintf('SELECT U.name AS UserName, Q.QuizName, MAX(QSI.UserScore) AS UserScore, (MAX(QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' LEFT JOIN #__users U'.'  ON QSI.UserId = U.id'.' WHERE '.($ignoreGuest ? ' U.id > 0 AND ' : '') .' QSI.Status = "Finished" AND Q.Status = %d'.(count($quizIdList) >0 ? ' AND Q.QuizId '.($revertQuizId ? ' NOT' : '') .' IN ('.join(',', $this->_quoteValues($quizIdList)) .')':
            '') .($minScore>0 ? ' AND ((QSI.UserScore / QSI.MaxScore) * 100) >= '.$minScore : '') .' GROUP BY QSI.QuizId,QSI.UserId'.' ORDER BY Q.QuizName ASC,PercentScore DESC,U.name ASC,QSI.EndDate ASC'.($count>0 ? (' LIMIT 0,'.$count) : ''), AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()));
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                return null;
            }
            return $results;
        }
        function getTopResults($count = 5, $ignoreGuest = true, $categoryId = null, $aggregateUserResults = false, $startDate = null, $endDate = null) {
            global $database;
            $count = @intval($count, 10);
            if (!is_null($categoryId)) $categoryId = $this->_fixIdList($categoryId);
            $results = null;
            $query = sprintf('SELECT U.name AS UserName, U.username AS LoginName, Q.QuizName, QSI.UserScore, ((QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' '.($ignoreGuest ? 'INNER' : 'LEFT') .' JOIN #__users U'.'  ON QSI.UserId = U.id'.(!empty($categoryId) ? ' LEFT JOIN #__ariquizquizcategory QC ON QSI.QuizId = QC.QuizId' : '') .' WHERE '.' QSI.Status = "Finished" AND Q.Status = %d'.(!empty($categoryId) ? ' AND IFNULL(QC.CategoryId, 0) IN ('.join(',', $categoryId) .')' : '') .(!empty($startDate) ? ' AND QSI.EndDate >= '.$database->Quote(ArisDate::getDbUTC($startDate)) : '') .(!empty($endDate) ? ' AND QSI.EndDate <= '.$database->Quote(ArisDate::getDbUTC($endDate)) : '') .($aggregateUserResults ? ' GROUP BY U.id' : '') .' ORDER BY PercentScore DESC,IF(U.id, 1, 0) DESC, EndDate ASC'.($count>0 ? (' LIMIT 0,'.$count) : ''), AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()));
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                return null;
            }
            return $results;
        }
        function getAggregateTopResults($count = 5, $ignoreGuest = true, $categoryId = null, $startDate = null, $endDate = null) {
            global $database;
            $count = @intval($count, 10);
            if (!is_null($categoryId)) $categoryId = $this->_fixIdList($categoryId);
            $results = null;
            $query = sprintf('SELECT U.name AS UserName, U.username AS LoginName, Q.QuizName, QSI.UserScore, (MAX(QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' '.($ignoreGuest ? 'INNER' : 'LEFT') .' JOIN #__users U'.'  ON QSI.UserId = U.id'.' INNER JOIN'.' (SELECT'.' 	MAX(TQSI.UserScore / TQSI.MaxScore) AS RealScore,'.' 	TQSI.QuizId AS QuizId'.' FROM'.'	#__ariquizstatisticsinfo TQSI INNER JOIN #__ariquiz TQ'.'		ON TQSI.QuizId = TQ.QuizId'.'	'.($ignoreGuest ? 'INNER' : 'LEFT') .' JOIN #__users TU'.'		ON TQSI.UserId = TU.id'.' WHERE'.'	TQSI.Status = "Finished"'.'	AND'.'	TQ.Status = 1'.(!empty($startDate) ? ' AND TQSI.EndDate >= '.$database->Quote(ArisDate::getDbUTC($startDate)) : '') .(!empty($endDate) ? ' AND TQSI.EndDate <= '.$database->Quote(ArisDate::getDbUTC($endDate)) : '') .' GROUP BY TQSI.QuizId'.' ORDER BY NULL'.' ) T'.'	ON QSI.QuizId = T.QuizId'.(!empty($categoryId) ? ' LEFT JOIN #__ariquizquizcategory QC ON QSI.QuizId = QC.QuizId' : '') .' WHERE '.' QSI.Status = "Finished" AND Q.Status = %d AND (QSI.UserScore / QSI.MaxScore) >= (T.RealScore - 0.0001)'.(!empty($categoryId) ? ' AND IFNULL(QC.CategoryId, 0) IN ('.join(',', $categoryId) .')' : '') .(!empty($startDate) ? ' AND QSI.EndDate >= '.$database->Quote(ArisDate::getDbUTC($startDate)) : '') .(!empty($endDate) ? ' AND QSI.EndDate <= '.$database->Quote(ArisDate::getDbUTC($endDate)) : '') .' GROUP BY QSI.QuizId'.' ORDER BY PercentScore DESC'.($count>0 ? (' LIMIT 0,'.$count) : ''), AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()));
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                return null;
            }
            return $results;
        }
        function getLastResults($count = 5, $ignoreGuest = true, $categoryId = null) {
            global $database;
            if (!is_null($categoryId)) $categoryId = $this->_fixIdList($categoryId);
            $results = null;
            $query = sprintf('SELECT U.name AS UserName, U.username AS LoginName, Q.QuizName, QSI.UserScore, ((QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' '.($ignoreGuest ? 'INNER' : 'LEFT') .' JOIN #__users U'.'   ON QSI.UserId = U.id'.(!empty($categoryId) ? ' LEFT JOIN #__ariquizquizcategory QC ON QSI.QuizId = QC.QuizId' : '') .' WHERE '.' QSI.Status = "Finished" AND Q.Status = %d'.(!empty($categoryId) ? ' AND IFNULL(QC.CategoryId, 0) IN ('.join(',', $categoryId) .')' : '') .' ORDER BY QSI.EndDate DESC'.' LIMIT 0,%d', AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()), $count);
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                return null;
            }
            return $results;
        }
        function getLastUserResults($userId, $count = 5) {
            global $database;
            $userId = intval($userId);
            $results = null;
            if ($userId>0) {
                $query = sprintf('SELECT Q.QuizName, QSI.UserScore, ((QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' WHERE UserId = %d AND QSI.Status = "Finished" AND Q.Status = %d'.' ORDER BY QSI.EndDate DESC'.' LIMIT 0,%d', $userId, AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()), $count);
                $database->setQuery($query);
                $results = $database->loadObjectList();
                if ($database->getErrorNum()) {
                    return null;
                }
            }
            return $results;
        }
        function getTopUserResults($userId, $count = 5) {
            global $database;
            $userId = intval($userId);
            $results = null;
            if ($userId>0) {
                $query = sprintf('SELECT Q.QuizName, MAX(QSI.UserScore) AS UserScore, (MAX(QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q'.' 	ON QSI.QuizId = Q.QuizId'.' WHERE UserId = %d AND QSI.Status = "Finished" AND Q.Status = %d'.' GROUP BY QSI.QuizId'.' ORDER BY PercentScore DESC'.' LIMIT 0,%d', $userId, AriConstantsManager::getVar('Status.Active', AriQuizControllerConstants::getClassName()), $count);
                $database->setQuery($query);
                $results = $database->loadObjectList();
                if ($database->getErrorNum()) {
                    return null;
                }
            }
            return $results;
        }
        function getFormattedFinishedResultById($statisticsInfoId, $defaults = array()) {
            $error = 'ARI: Couldnt get formatted finished result.';
            $statisticsInfo = $this->getStatisticsInfo($statisticsInfoId);
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $result = null;
            if ($statisticsInfo && $statisticsInfo->StatisticsInfoId) {
                $result = $this->getFormattedFinishedResult($statisticsInfo->TicketId, $statisticsInfo->UserId, $defaults);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
            }
            return $result;
        }
        function getFinishedResultById($statisticsInfoId, $defaults = array()) {
            $error = 'ARI: Couldnt get finished result.';
            $statisticsInfo = $this->getStatisticsInfo($statisticsInfoId);
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $result = null;
            if ($statisticsInfo && $statisticsInfo->StatisticsInfoId) {
                $result = $this->getFinishedResult($statisticsInfo->TicketId, $statisticsInfo->UserId, $defaults);
                if ($this->_isError(true, false)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
            }
            return $result;
        }
        function getStatisticsInfo($statisticsInfoId) {
            $statisticsInfo = AriEntityFactory::createInstance('AriQuizStatisticsInfoEntity', AriGlobalPrefs::getEntityGroup());
            if (!$statisticsInfo->load($statisticsInfoId)) {
                trigger_error('ARI: Couldnt get statistics info entity', E_USER_ERROR);
                return null;
            }
            return $statisticsInfo;
        }
        function getFormattedFinishedResult($ticketId, $userId, $defaults = array()) {
            global $mosConfig_live_site;
            $result = $this->getFinishedResult($ticketId, $userId, $defaults);
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt get formatted finished result.', E_USER_ERROR);
                return $result;
            }
            if (empty($result)) return null;
            if (empty($result['UserName'])) $result['UserName'] = AriWebHelper::translateResValue('Label.Guest');
            $result['Passed'] = AriWebHelper::translateResValue($result['Passed'] ? 'Label.Passed' : 'Label.NoPassed');
            $result['StartDate'] = ArisDate::formatDate($result['StartDate']);
            $result['EndDate'] = ArisDate::formatDate($result['EndDate']);
            $result['SpentTime'] = ArisDateDuration::toString(AriUtils::getValue($result['SpentTime'], 0), AriQuizUtils::getShortPeriods(), ' ', true);
            $result['ResultsLink'] = $mosConfig_live_site.'/index.php?option=com_ariquiz&task=quiz_finished&ticketId='.$ticketId;
            return $result;
        }
        function getFinishedResult($ticketId, $userId, $defaults = array()) {
            global $database;
            $userId = intval($userId);
            $query = sprintf('SELECT Q.CssTemplateId,Q.AttemptCount,Q.ParsePluginTag,Q.FullStatistics,QSI.StatisticsInfoId,QSI.ExtraData,QSI.UserId,U.email AS Email,U.Name AS UserName, Q.ResultScaleId, Q.QuizName, QSI.PassedScore, QSI.MaxScore, QSI.UserScore, ((QSI.UserScore / QSI.MaxScore) * 100) AS PercentScore, QSI.Passed, QSI.Passed AS _Passed, QSI.StartDate, QSI.EndDate, (UNIX_TIMESTAMP(QSI.EndDate) - UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate,QSI.StartDate))+ QSI.UsedTime) AS SpentTime, QSI.QuizId'.' FROM #__ariquizstatisticsinfo QSI'.' INNER JOIN #__ariquiz Q ON QSI.QuizId = Q.QuizId'.' LEFT JOIN #__users U ON QSI.UserId = U.Id'.' WHERE QSI.TicketId = %s AND QSI.Status = "Finished" AND (QSI.UserId = 0 OR QSI.UserId = %d)'.' GROUP BY QSI.StatisticsInfoId'.' ORDER BY NULL'.' LIMIT 0,1', $database->Quote($ticketId), $userId);
            $database->setQuery($query);
            $obj = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get finished result.', E_USER_ERROR);
                return null;
            }
            $obj = (!empty($obj) && count($obj) >0) ? $obj[0] : null;
            if ($obj != null) {
                if (!empty($defaults)) {
                    foreach($defaults as $key=>$value) {
                        if (key_exists($key, $obj) && empty($obj[$key])) {
                            $obj[$key] = $value;
                        }
                    }
                }
                $statisticsInfo = AriEntityFactory::createInstance('AriQuizStatisticsInfoEntity', AriGlobalPrefs::getEntityGroup());
                $obj['ExtraData'] = $statisticsInfo->parseExtraDataXml($obj['ExtraData']);
                if (!$obj['UserId']) $obj = array_merge($obj, $obj['ExtraData']);
                $obj['PercentScore'] = sprintf('%.2f', $obj['PercentScore']);
                $obj['PassedScore'] = sprintf('%.2f', $obj['PassedScore']);
            }
            return $obj;
        }
        function getFinishedInfo($statisticsInfoId) {
            global $database;
            $statisticsInfoId = intval($statisticsInfoId);
            $query = sprintf('SELECT IFNULL(SUM(IF(QQV.Score, QQV.Score, QQV2.Score)), 0) AS MaxScore, SUM(QS.Score) AS UserScore, (100 * (SUM(QS.Score) / IFNULL(SUM(IF(QQV.Score, QQV.Score, QQV2.Score)), 0)) >= QSI.PassedScore) AS Passed'.' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquizstatistics QS'.'	ON QSI.StatisticsInfoId = QS.StatisticsInfoId'.' INNER JOIN #__ariquizquestionversion QQV'.'	ON QS.QuestionVersionId = QQV.QuestionVersionId'.' LEFT JOIN #__ariquizquestionversion QQV2'.'	ON QS.BankVersionId = QQV2.QuestionVersionId'.' WHERE QSI.StatisticsInfoId = %d'.' GROUP BY QSI.StatisticsInfoId'.' LIMIT 0,1', $statisticsInfoId);
            $database->setQuery($query);
            $obj = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get finished info.', E_USER_ERROR);
                return null;
            }
            $obj = (!empty($obj) && count($obj) >0) ? $obj[0] : null;
            return $obj;
        }
        function getFinishedQuizList() {
            global $database;
            $query = sprintf('SELECT Q.QuizId, Q.QuizName'.' FROM #__ariquizstatisticsinfo SSI INNER JOIN  #__ariquiz Q ON SSI.QuizId = Q.QuizId'.' WHERE SSI.Status = "Finished"'.' GROUP BY Q.QuizId'.' ORDER BY Q.QuizName ASC');
            $database->setQuery($query);
            $quizList = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get finished quiz list.', E_USER_ERROR);
                return null;
            }
            return $quizList;
        }
        function getFinishedUserList($quizId = 0, $addAnonymousUser = true, $params = array()) {
            global $database;
            $query = sprintf('SELECT U.Id, U.Name'.' FROM #__ariquizstatisticsinfo SSI INNER JOIN  #__users U'.' 	ON SSI.UserId = U.Id'.' WHERE SSI.Status = "Finished" AND (%d = 0 OR SSI.QuizId = %d)'.' GROUP BY U.Id'.' ORDER BY U.UserName ASC', $quizId, $quizId);
            $database->setQuery($query);
            $userList = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get user list.', E_USER_ERROR);
                return null;
            }
            if ($addAnonymousUser) {
                $anonUser = new stdClass();
                $anonUser->Id = '0';
                $anonUser->Name = isset($params['Anonymous']) ? $params['Anonymous'] : '';
                if (empty($userList)) $userList = array();
                array_unshift($userList, $anonUser);
            }
            return $userList;
        }
        function _applyResultsFilter($query, $filter, $ignoreQuizId = false) {
            global $database;
            if ($filter) {
                $filterPredicates = $filter->getConfigValue('filter');
                if (!$ignoreQuizId && !empty($filterPredicates['QuizId'])) $query.= ' AND SSI.QuizId='.intval($filterPredicates['QuizId'], 10);
                if (isset($filterPredicates['UserId']) && $filterPredicates['UserId'] != -1) $query.= ' AND SSI.UserId='.intval($filterPredicates['UserId'], 10);
                $tz = ArisDate::getTimeZone() *60*60;
                if (!empty($filterPredicates['StartDate'])) $query.= ' AND SSI.EndDate >= '.$database->Quote(ArisDate::getDbUTC($filterPredicates['StartDate']-$tz));
                if (!empty($filterPredicates['EndDate'])) $query.= ' AND SSI.EndDate <= '.$database->Quote(ArisDate::getDbUTC($filterPredicates['EndDate']-$tz));
            }
            return $query;
        }
        function _isOnlyGuests($filter) {
            $isOnlyGuest = false;
            if ($filter) {
                $filterPredicates = $filter->getConfigValue('filter');
                if (isset($filterPredicates['UserId']) && $filterPredicates['UserId'] == 0) $isOnlyGuest = true;
            }
            return $isOnlyGuest;
        }
        function getResultsCount($quizId = 0, $userId = 0, $filter = null) {
            global $database;
            $quizId = intval($quizId);
            $query = sprintf('SELECT COUNT(*) FROM '.'#__ariquizstatisticsinfo SSI INNER JOIN #__ariquiz S ON SSI.QuizId = S.QuizId '.' LEFT JOIN #__users U'.' 	ON SSI.UserId = U.id '.' WHERE (%1$d = 0 OR SSI.QuizId = %1$d) AND SSI.Status = "Finished" AND (%2$s IS NULL OR SSI.UserId = %3$d)', $quizId, $this->_normalizeValue($userId), intval($userId));
            $query = $this->_applyResultsFilter($query, $filter, !!($quizId));
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get result count.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function getResults($quizId = 0, $userId = 0, $filter = null) {
            global $database;
            $quizId = intval($quizId);
            if ($filter) $filter = clone($filter);
            if ($this->_isOnlyGuests($filter)) {
                $sortField = $filter->getConfigValue('sortField');
                switch ($sortField) {
                    case 'Name':
                        $filter->setConfigValue('sortField', 'SUBSTR(ExtraData, LOCATE(\'<item name="UserName">\', ExtraData) + 22, LOCATE(\'</\', ExtraData) - LOCATE(\'<item name="UserName">\', ExtraData) - 22)');
                    break;
                    case 'Email':
                        $filter->setConfigValue('sortField', 'SUBSTR(ExtraData, LOCATE(\'<item name="Email">\', ExtraData) + 19, LOCATE(\'</\', ExtraData, LOCATE(\'<item name="Email">\', ExtraData)) - LOCATE(\'<item name="Email">\', ExtraData) - 19)');
                    break;
                }
            }
            $query = sprintf('SELECT QC.CategoryName,SSI.TicketId,SSI.StatisticsInfoId, SSI.Passed, SSI.UserId, SSI.ExtraData, SSI.UserScore, SSI.MaxScore, U.Name, U.username AS Login, U.email AS Email, U.Id, S.QuizName, S.QuizId, SSI.StartDate, SSI.EndDate,ROUND(IF(SSI.MaxScore > 0, 100 * SSI.UserScore / SSI.MaxScore, 0), 2) AS PercentScore'.' FROM #__ariquizstatisticsinfo SSI INNER JOIN #__ariquiz S'.' 	ON SSI.QuizId = S.QuizId'.' LEFT JOIN #__ariquizquizcategory QQC'.'	ON QQC.QuizId = S.QuizId'.' LEFT JOIN #__ariquizcategory QC'.'	ON QQC.CategoryId = QC.CategoryId'.' LEFT JOIN #__users U'.' 	ON SSI.UserId = U.id '.' WHERE (%1$d = 0 OR SSI.QuizId = %1$d) AND SSI.Status = "Finished" AND (%2$s IS NULL OR SSI.UserId = %3$d)', $quizId, $this->_normalizeValue($userId), intval($userId));
            $query = $this->_applyResultsFilter($query, $filter, !!($quizId));
            $query.= ' GROUP BY SSI.StatisticsInfoId ';
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get results.', E_USER_ERROR);
                return null;
            }
            $results = $this->applyExtraData($results);
            return $results;
        }
        function applyExtraData($results, $needToRemove = true) {
            if (is_array($results) && count($results) >0) {
                reset($results);
                $vars = array_keys(get_object_vars(current($results)));
                $nameVarExists = in_array('Name', $vars);
                $statisticsInfo = AriEntityFactory::createInstance('AriQuizStatisticsInfoEntity', AriGlobalPrefs::getEntityGroup());
                foreach($results as $key=>$value) {
                    $result = &$results[$key];
                    $extraData = $result->ExtraData;
                    if ($needToRemove) unset($result->ExtraData);
                    if ($result->UserId || empty($extraData)) continue;
                    $extraData = $statisticsInfo->parseExtraDataXml($extraData);
                    foreach($extraData as $key=>$value) {
                        if (in_array($key, $vars)) {
                            $result->$key = $value;
                        } else if ($key == 'UserName' && $nameVarExists) {
                            $result->Name = $value;
                        }
                    }
                }
            }
            return $results;
        }
        function getBaseView($statisticsInfoId) {
            global $database;
            $idList = $this->_fixIdList($statisticsInfoId);
            if (empty($idList)) return null;
            $idStr = join(',', $this->_quoteValues($idList));
            $query = sprintf('SELECT IF(QQV.Score, QQV.Score, QQV2.Score) AS MaxScore,SSI.StartDate AS QuizStartDate, SSI.EndDate AS QuizEndDate, SSI.UserId, SSI.ExtraData, SS.QuestionIndex, SSI.UserScore AS QuizUserScore,SSI.QuestionCount,SSI.MaxScore AS QuizMaxScore,SSI.Passed,SSI.PassedScore AS QuizPassedScore, U.name AS UserName, U.email AS Email, Q.QuizName, Q.QuizId, SS.Score, SS.StatisticsInfoId, SS.StatisticsId, IF(QQV2.QuestionId,QQV2.Question,QQV.Question) AS Question, IF(QQV2.QuestionId,QQV2.Data,QQV.Data) AS BaseData, SS.Data, QQC.CategoryName, QQT.QuestionType, QQT.ClassName, SS.QuestionVersionId, SS.IpAddress, SS.StartDate, SS.EndDate,  (UNIX_TIMESTAMP(SS.EndDate) - UNIX_TIMESTAMP(SS.StartDate) + SS.UsedTime) AS TotalTime'.' FROM #__ariquizstatisticsinfo SSI INNER JOIN #__ariquizstatistics SS ON SSI.StatisticsInfoId = SS.StatisticsInfoId'.' INNER JOIN #__ariquiz Q ON SSI.QuizId = Q.QuizId'.' INNER JOIN #__ariquizquestionversion QQV'.'	ON SS.QuestionVersionId = QQV.QuestionVersionId'.' LEFT JOIN #__ariquizquestionversion QQV2'.'	ON SS.BankVersionId = QQV2.QuestionVersionId'.' INNER JOIN #__ariquizquestiontype QQT ON IFNULL(QQV2.QuestionTypeId,QQV.QuestionTypeId) = QQT.QuestionTypeId'.' LEFT JOIN #__ariquizquestioncategory QQC ON QQV.QuestionCategoryId = QQC.QuestionCategoryId'.' LEFT JOIN #__users U ON SSI.UserId = U.id'.' WHERE SSI.Status = "Finished" AND SS.StatisticsInfoId IN (%s)'.' ORDER BY SS.StatisticsInfoId, SS.QuestionIndex', $idStr);
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get statistics.', E_USER_ERROR);
                return null;
            }
            $results = $this->applyExtraData($results);
            return $results;
        }
        function getSimpleBaseView($statisticsInfoId) {
            global $database;
            $idList = $this->_fixIdList($statisticsInfoId);
            if (empty($idList)) return null;
            $idStr = join(',', $this->_quoteValues($idList));
            $query = sprintf('SELECT SSI.StatisticsInfoId,SSI.UserScore AS QuizUserScore,SSI.QuestionCount,SSI.ExtraData,SSI.UserId,SSI.MaxScore AS QuizMaxScore,SSI.Passed,SSI.PassedScore AS QuizPassedScore, U.name AS UserName, U.email AS Email, Q.QuizName, Q.QuizId, SSI.StartDate, SSI.EndDate,  (UNIX_TIMESTAMP(SSI.EndDate) - UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)) + SSI.UsedTime) AS TotalTime'.' FROM #__ariquizstatisticsinfo SSI INNER JOIN #__ariquiz Q ON SSI.QuizId = Q.QuizId'.' LEFT JOIN #__users U ON SSI.UserId = U.id'.' WHERE SSI.Status = "Finished" AND SSI.StatisticsInfoId IN (%s)'.' ORDER BY SSI.StatisticsInfoId', $idStr);
            $database->setQuery($query);
            $results = $database->loadObjectList('StatisticsInfoId');
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get statistics.', E_USER_ERROR);
                return null;
            }
            $results = $this->applyExtraData($results);
            return $results;
        }
        function getCSVView($statisticsInfoId, $params = array(), $periods = null) {
            $fields = array('#', 'Quiz Name', 'User', 'Email', 'Question Count', 'Passed', 'Start Date', 'End Date', 'Spent Time', 'User Score', 'User Score Percent', 'Max Score', 'Passing Score');
            $fields = array_map(create_function('$v', 'return \'"\' . $v . \'"\';'), $fields);
            $csv = join("\t", $fields);
            $results = $this->getSimpleBaseView($statisticsInfoId);
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt export to CSV.', E_USER_ERROR);
                return '';
            }
            if (!empty($results)) {
                $anonymous = isset($params['Anonymous']) ? $params['Anonymous'] : '';
                $passed = isset($params['Passed']) ? $params['Passed'] : '';
                $noPassed = isset($params['NoPassed']) ? $params['NoPassed'] : '';
                $i = 1;
                foreach($results as $result) {
                    $csv.= "\r\n";
                    $userScorePercent = $result->QuizMaxScore ? round(100*$result->QuizUserScore/$result->QuizMaxScore) : 100;
                    $rowData = array($i, $result->QuizName, !empty($result->UserName) ? $result->UserName : $anonymous, $result->Email, $result->QuestionCount, $result->Passed ? $passed : $noPassed, ArisDate::formatDate($result->StartDate), ArisDate::formatDate($result->EndDate), ArisDateDuration::toString($result->TotalTime, $periods, ' ', true), $result->QuizUserScore, $userScorePercent.'%', $result->QuizMaxScore, $result->QuizPassedScore.'%');
                    foreach($rowData as $key=>$dataItem) {
                        $rowData[$key] = str_replace("\t", ' ', $dataItem);
                    }
                    $csv.= join("\t", $rowData);
                    ++$i;
                }
            }
            if (function_exists('iconv')) {
                $csv = chr(255) .chr(254) .@iconv('UTF-8', 'UTF-16LE', $csv);
            } else if (function_exists('mb_convert_encoding')) {
                $csv = chr(255) .chr(254) .@mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
            }
            return $csv;
        }
        function getHtmlView($statisticsInfoId, $params = array(), $periods = null) {
            $htmlView = ARI_RESULT_HTML_TEMPLATE;
            $htmlPageBreak = '<br clear="all" style="page-break-before:always" />';
            $htmlQuizDataHeader = ARI_RESULT_HTML_QUIZ_DATA_HEADER;
            $htmlQuizHeader = ARI_RESULT_HTML_QUIZ_HEADER;
            $results = $this->getBaseView($statisticsInfoId);
            $simpleResults = $this->getSimpleBaseView($statisticsInfoId);
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt export to HTML.', E_USER_ERROR);
                return '';
            }
            $data = '';
            if (!empty($results)) {
                $prevId = 0;
                $subData = '';
                $anonymous = isset($params['Anonymous']) ? $params['Anonymous'] : '';
                $passed = isset($params['Passed']) ? $params['Passed'] : '';
                $noPassed = isset($params['NoPassed']) ? $params['NoPassed'] : '';
                foreach($results as $result) {
                    if ($prevId != $result->StatisticsInfoId) {
                        if (!empty($prevId)) {
                            $data.= sprintf($htmlQuizDataHeader, $subData);
                            $data.= $htmlPageBreak;
                        }
                        $quizTotalTime = isset($simpleResults[$result->StatisticsInfoId]) ? $simpleResults[$result->StatisticsInfoId]->TotalTime : 0;
                        $header = sprintf($htmlQuizHeader, $result->QuizName, ArisDate::formatDate($result->QuizStartDate), ArisDate::formatDate($result->QuizEndDate), ArisDateDuration::toString($quizTotalTime, $periods, ' ', true), intval($result->QuizUserScore), intval($result->QuizMaxScore), intval($result->QuizPassedScore) .'%', $result->Passed ? $passed : $noPassed, !empty($result->UserName) ? $result->UserName : $anonymous, $result->Email);
                        $data.= $header;
                        $subData = '';
                    }
                    $index = $result->QuestionIndex+1;
                    $ip = long2ip($result->IpAddress);
                    $subData.= sprintf(ARI_RESULT_HTML_DATA_ROW, $index, $result->Question, intval($result->Score), intval($result->MaxScore));
                    $prevId = $result->StatisticsInfoId;
                }
                $data.= sprintf($htmlQuizDataHeader, $subData);
            }
            $htmlView = sprintf($htmlView, $data);
            return $htmlView;
        }
        function getExcelView($statisticsInfoId, $params = array(), $periods = null) {
            $results = $this->getSimpleBaseView($statisticsInfoId);
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt export to Excel.', E_USER_ERROR);
                return '';
            }
            $data = '';
            if (!empty($results)) {
                $i = 1;
                $anonymous = isset($params['Anonymous']) ? $params['Anonymous'] : '';
                $passed = isset($params['Passed']) ? $params['Passed'] : '';
                $noPassed = isset($params['NoPassed']) ? $params['NoPassed'] : '';
                foreach($results as $result) {
                    $userScorePercent = $result->QuizMaxScore ? round(100*$result->QuizUserScore/$result->QuizMaxScore) : 100;
                    $data.= sprintf(ARI_RESULT_HTML_EXCEL_ROW, $i, $result->QuizName, !empty($result->UserName) ? $result->UserName : $anonymous, $result->Email, $result->QuestionCount, $result->Passed ? $passed : $noPassed, ArisDate::formatDate($result->StartDate), ArisDate::formatDate($result->EndDate), ArisDateDuration::toString($result->TotalTime, $periods, ' ', true), $result->QuizUserScore, $userScorePercent.'%', $result->QuizMaxScore, $result->QuizPassedScore.'%');
                    ++$i;
                }
            }
            $excel = sprintf(ARI_RESULT_HTML_EXCEL_TEMPLATE, $data);
            return $excel;
        }
        function getWordView($statisticsInfoId, $params = array(), $periods = null) {
            $result = $this->getHtmlView($statisticsInfoId, $params, $periods);
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt export to word.', E_USER_ERROR);
                return $result;
            }
            return $result;
        }
        function getPassedQuizCount($quizId, $userId) {
            $quizId = intval($quizId, 10);
            $userId = intval($userId, 10);
            if ($quizId<1 || $userId<1) return 0;
            global $database;
            $query = sprintf('SELECT COUNT(*) FROM #__ariquizstatisticsinfo WHERE UserId = %d AND QuizId = %d AND `Status` = "Finished" LIMIT 0,1', $userId, $quizId);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                return 0;
            }
            return $count;
        }
    };
?>