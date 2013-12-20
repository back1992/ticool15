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
    AriKernel::import('Web.JSON.JSONHelper');
    AriKernel::import('Config.ConfigWrapper');
    AriKernel::import('Components.AriQuiz.Util');
    AriKernel::import('Controllers.AriQuiz.QuizController');
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Controllers.AriQuiz.UserQuizController');
    AriKernel::import('Mambot.MambotProcessHelper');
    AriKernel::import('Event.EventController');
    class questionAriPage extends AriPageBase {
        var $_userQuizController;
        var $_quizController;
        var $_quizStorage;
        var $_ticketId;
        function _init() {
            $this->_userQuizController = new AriUserQuizController();
            $this->_quizController = new AriQuizController();
            parent::_init();
        }
        function execute() {
            $this->_loadQuestion();
            $ver = AriConfigWrapper::getConfigKey(AriConstantsManager::getVar('Config.Version', AriQuizComponent::getCodeName()), '1.0.0');
            $this->addVar('version', $ver);
            parent::execute();
        }
        function getQuizStorage() {
            if (is_null($this->_quizStorage)) {
                global $my;
                $this->_quizStorage = AriEntityFactory::createInstance('AriQuizStorageEntity', AriGlobalPrefs::getEntityGroup(), $this->getTicketId(), $my);
            }
            return $this->_quizStorage;
        }
        function getTicketId() {
            if (is_null($this->_ticketId)) {
                $this->_ticketId = AriRequest::getParam('ticketId', '');
            }
            return $this->_ticketId;
        }
        function _loadQuestion($ticketId = null) {
            global $option, $my, $Itemid;
            $quizStorage = $this->getQuizStorage();
            $sid = $quizStorage->get('StatisticsInfoId');
            $ticketId = $quizStorage->TicketId;
            $userId = $my->get('id');
            if (!$quizStorage->isQuizAvailable()) {
                AriQuizUtils::redirectToInfo('FrontEnd.QuizNotAvailable');
                exit ();
            }
            $statistics = $this->_userQuizController->call('getNextQuestion', $sid);
            if (empty($statistics) || empty($statistics->StatisticsId)) {
                $quizStorage->clear();
                $isQuizFinished = $this->_userQuizController->call('isQuizFinished', $sid);
                if ($isQuizFinished) {
                    AriResponse::redirect(AriJoomlaBridge::getLink('index.php?option='.$option.'&task=quiz_finished&ticketId='.$ticketId.'&Itemid='.$Itemid, false, false));
                    return;
                } else {
                    AriResponse::redirect(AriJoomlaBridge::getLink('index.php?option='.$option.'&task=quiz_list&ItemId='.$Itemid, false, false));
                    return;
                }
            }
            $quizInfo = $this->_userQuizController->call('getUserQuizInfo', $sid);
            $completedCount = $this->_userQuizController->call('getUserCompletedQuestion', $sid);
            $questionCount = $quizInfo->QuestionCount;
            $totalTime = null;
            if ($quizInfo->TotalTime) {
                $totalTime = $quizInfo->TotalTime;
                if ($quizInfo->StartDate) {
                    $totalTime = $quizInfo->TotalTime-$quizInfo->Now-$quizInfo->UsedTime+$quizInfo->StartDate;
                }
                --$totalTime;
            }
            $props = $this->_quizController->call('getSimpleProperties', $quizInfo->QuizId);
            if (!is_array($props)) $props = array();
            if ($props && isset($props['HistoryText'])) {
                $props['HistoryText'] = AriMambotProcessHelper::processMambotTags($props['HistoryText']);
            }
            $props['AnswersOrderType'] = $quizInfo->QuestionOrderType;
            $this->addVar('totalTime', $totalTime);
            $this->addVar('cssFile', AriQuizUtils::getCssFile(!empty($quizInfo) ? $quizInfo->CssTemplateId : null));
            $this->addVar('completedCount', $completedCount);
            $this->addVar('questionCount', $questionCount);
            $this->addVar('progressPercent', $progressPercent);
            $this->addVar('ticketId', $ticketId);
            $this->addVar('questionVersion', $questionVersion);
            $this->addVar('questionVersionId', $questionVersionId);
            $this->addVar('quizInfo', $quizInfo);
            $this->addVar('quizProps', $props);
            $this->addVar('userId', $userId);
            $this->addVar('quizStorage', $quizStorage);
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('stopExit', 'clickStopExit');
            $this->_registerEventHandler('autoStopExit', 'clickAutoStopExit');
        }
        function clickAutoStopExit() {
            $result = $this->_stopQuiz();
            if ($result) {
                AriQuizUtils::redirectToInfo('Label.QuizAutoStopped');
            } else {
                AriQuizUtils::redirectToInfo('FrontEnd.QuizNotAvailable');
            }
        }
        function clickStopExit() {
            $result = $this->_stopQuiz();
            if ($result) {
                AriQuizUtils::redirectToInfo('Label.QuizStopped');
            } else {
                AriQuizUtils::redirectToInfo('FrontEnd.QuizNotAvailable');
            }
        }
        function _registerAjaxHandlers() {
            $this->_registerAjaxHandler('skipQuestion', 'ajaxSkipQuestion');
            $this->_registerAjaxHandler('getQuestion', 'ajaxGetQuestion');
            $this->_registerAjaxHandler('getExplanation', 'ajaxGetExplanation');
            $this->_registerAjaxHandler('saveQuestion', 'ajaxSaveQuestion');
            $this->_registerAjaxHandler('stopQuiz', 'ajaxStopQuiz');
            $this->_registerAjaxHandler('getCorrectAnswer', 'ajaxGetCorrectAnswer');
            $this->_registerAjaxHandler('resumeQuiz', 'ajaxResumeQuiz');
        }
        function ajaxSkipQuestion() {
            global $my;
            $quizStorage = $this->getQuizStorage();
            $sid = $quizStorage->get('StatisticsInfoId');
            $userId = $my->get('id');
            $skipDate = ArisDate::getDbUTC();
            $result = false;
            if ($quizStorage->isQuizAvailable()) {
                $qid = AriRequest::getParam('qid', 0);
                $statistics = $this->_userQuizController->call('getCurrentQuestion', $sid, $userId);
                if (!empty($statistics) && $statistics->QuestionId == $qid) {
                    if ($quizStorage->get('CanSkip')) {
                        $questionVersion = $statistics->Question->QuestionVersion;
                        $questionEntity = AriEntityFactory::createInstance($questionVersion->QuestionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                        $data = $questionEntity->getFrontXml($qid);
                        if ($this->_userQuizController->call('skipQuestion', $statistics->StatisticsId, $skipDate, $data)) {
                            AriEventController::raiseEvent('onSkipQuestion', array('QuizId'=>$quizStorage->get('QuizId'), 'TicketId'=>$quizStorage->TicketId, 'UserId'=>$userId, 'QuestionId'=>$qid, 'QuestionVersionId'=>$statistics->QuestionVersionId));
                        }
                    }
                }
                $result = true;
            }
            AriResponse::sendJsonResponse($result);
        }
        function ajaxGetQuestion() {
            global $my;
            $quizStorage = $this->getQuizStorage();
            $sid = $quizStorage->get('StatisticsInfoId');
            $ticketId = $quizStorage->TicketId;
            $parseTag = $quizStorage->get('ParsePluginTag');
            $userId = $my->get('id');
            $ret = null;
            if ($quizStorage->isQuizAvailable()) {
                $statistics = $this->_userQuizController->call('getNextQuestion', $sid);
                if (!empty($statistics->StatisticsId)) {
                    $questionVersionId = $statistics->Question->QuestionVersionId;
                    $questionVersion = $statistics->Question->QuestionVersion;
                    $questionEntity = AriEntityFactory::createInstance($questionVersion->QuestionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                    $questionData = null;
                    if ($statistics->InitData) {
                        $questionData = $questionEntity->applyUserData(@unserialize($statistics->InitData), $statistics->Data);
                    } else {
                        $questionData = $questionEntity->getClientDataFromXml($questionVersion->Data, $statistics->Data);
                    }
                    $questionTime = null;
                    if (!empty($statistics->QuestionTime)) {
                        $questionTime = !empty($statistics->StartDate) ? strtotime($statistics->StartDate) +$statistics->QuestionTime-strtotime(ArisDate::getDbUTC()) -$statistics->UsedTime : $statistics->QuestionTime-$statistics->UsedTime;
                        --$questionTime;
                    }
                    $ret = new stdClass();
                    $ret->questionData = $questionData;
                    $ret->questionTime = $questionTime;
                    $ret->questionId = $statistics->Question->QuestionId;
                    $ret->questionText = $parseTag ? AriMambotProcessHelper::processMambotTags($statistics->Question->QuestionVersion->Question, true) : $statistics->Question->QuestionVersion->Question;
                    $ret->questionType = $questionVersion->QuestionType->ClassName;
                    $ret->questionIndex = $statistics->QuestionIndex;
                    $quizId = $statistics->Question->QuizId;
                    $startDate = ArisDate::getDbUTC();
                    if (empty($statistics->StartDate)) {
                        $statistics->IpAddress = AriRequest::getIP();
                        $statistics->StartDate = $startDate;
                        if (empty($statistics->InitData)) $statistics->InitData = @serialize($questionData);
                        $this->_userQuizController->call('updateStatisticsInfo', $statistics);
                        if (!$quizStorage->get('IsStartDateSet')) {
                            $this->_userQuizController->call('setSafeQuizStartDate', $sid, $startDate);
                            $quizStorage->set('IsStartDateSet', true);
                        }
                    }
                    AriEventController::raiseEvent('onLoadQuestion', array('Question'=>&$ret, 'QuizId'=>$quizId, 'TicketId'=>$ticketId, 'UserId'=>$userId));
                }
            }
            AriResponse::sendJsonResponse($ret);
        }
        function ajaxSaveQuestion() {
            global $my;
            $quizStorage = $this->getQuizStorage();
            $sid = $quizStorage->get('StatisticsInfoId');
            $userId = $my->get('id');
            $ticketId = $quizStorage->TicketId;
            $retResult = array('result'=>false, 'moveToNext'=>false, 'showExplanation'=>false);
            if ($quizStorage->isQuizAvailable()) {
                $qid = AriRequest::getParam('qid', 0);
                $statistics = $this->_userQuizController->call('getCurrentQuestion', $sid, $userId);
                if (!empty($statistics) && $statistics->Question->QuestionId == $qid) {
                    $questionVersion = $statistics->Question->QuestionVersion;
                    $questionEntity = AriEntityFactory::createInstance($questionVersion->QuestionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                    $data = $questionEntity->getFrontXml($qid);
                    $score = $questionEntity->getScore($data, $questionVersion->Data, $questionVersion->Score, $questionVersion->_OverrideData); {
                        $statistics->EndDate = ArisDate::getDbUTC();
                        $statistics->Data = $data;
                        $statistics->Score = $score;
                        $this->_userQuizController->call('updateStatisticsInfo', $statistics);
                        $retResult['result'] = true;
                        $retResult['moveToNext'] = true;
                        $retResult['showExplanation'] = $quizStorage->get('ShowExplanation');
                    }
                }
            }
            AriResponse::sendJsonResponse($retResult);
        }
        function ajaxStopQuiz() {
            $result = $this->_stopQuiz();
            AriResponse::sendJsonResponse($result);
        }
        function ajaxResumeQuiz() {
            global $my;
            $quizStorage = $this->getQuizStorage();
            $userId = $my->get('id');
            $result = false;
            if ($quizStorage->isQuizAvailable(false)) {
                $result = $this->_userQuizController->call('resumeQuizById', $quizStorage->TicketId, $userId);
            }
            AriResponse::sendJsonResponse($result);
        }
        function _stopQuiz() {
            global $my;
            $quizStorage = $this->getQuizStorage();
            $userId = $my->get('id');
            $ticketId = $quizStorage->TicketId;
            $result = false;
            if ($quizStorage->isQuizAvailable()) {
                if ($quizStorage->get('CanStop')) {
                    $result = $this->_userQuizController->call('stopQuiz', $quizStorage->get('StatisticsInfoId'), $userId);
                    if ($result) {
                        $quizStorage->clear();
                        AriEventController::raiseEvent('onStopQuiz', array('QuizId'=>$quizStorage->get('QuizId'), 'TicketId'=>$ticketId, 'UserId'=>$userId));
                    }
                }
            }
            return $result;
        }
        function ajaxGetCorrectAnswer() {
            global $my;
            $quizStorage = $this->getQuizStorage();
            $ret = null;
            if ($quizStorage->isQuizAvailable() && $quizStorage->get('ShowCorrectAnswer')) {
                $sid = $quizStorage->get('StatisticsInfoId');
                $statistics = $this->_userQuizController->call('getNextQuestion', $sid);
                if (!empty($statistics->StatisticsId)) {
                    $ticketId = $quizStorage->TicketId;
                    $parseTag = $quizStorage->get('ParsePluginTag');
                    $userId = $my->get('id');
                    $questionVersionId = $statistics->Question->QuestionVersionId;
                    $questionVersion = $statistics->Question->QuestionVersion;
                    $className = $questionVersion->QuestionType->ClassName;
                    $questionEntity = AriEntityFactory::createInstance($className, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                    $frontXml = $questionEntity->getFrontXml($statistics->QuestionId);
                    $ret = new stdClass();
                    $ret->QuestionClassName = $className;
                    $ret->QuestionData = $questionEntity->getDataFromXml($questionVersion->Data);
                    $ret->UserData = $questionEntity->getDataFromXml($frontXml);
                    $ret->MaxScore = $questionVersion->Score;
                    $ret->UserScore = $questionEntity->getScore($frontXml, $questionVersion->Data, $questionVersion->Score);
                    $ret->Note = $questionVersion->Note;
                    if ($className == 'HotSpotQuestion') {
                        $qc = new AriQuizQuestionController();
                        $ret->Files = $qc->call('getSimpleQuestionFiles', $statistics->QuestionVersionId);
                    }
                }
            }
            AriResponse::sendJsonResponse($ret);
        }
        function ajaxGetExplanation() {
            global $my;
            $ret = null;
            $quizStorage = $this->getQuizStorage();
            if ($quizStorage->isQuizAvailable()) {
                $sid = $quizStorage->get('StatisticsInfoId');
                $qid = AriRequest::getParam('qid', 0);
                $statistics = $this->_userQuizController->call('getStatisticsByQuestionId', $sid, $qid, $my->get('id'));
                if (!empty($statistics->StatisticsId)) {
                    $ticketId = $quizStorage->TicketId;
                    $parseTag = $quizStorage->get('ParsePluginTag');
                    $userId = $my->get('id');
                    $questionVersionId = $statistics->Question->QuestionVersionId;
                    $questionVersion = $statistics->Question->QuestionVersion;
                    $className = $questionVersion->QuestionType->ClassName;
                    $questionEntity = AriEntityFactory::createInstance($className, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
                    $ret = new stdClass();
                    $ret->QuestionClassName = $className;
                    $ret->QuestionData = $questionEntity->getDataFromXml($questionVersion->Data);
                    $ret->UserData = $questionEntity->getDataFromXml($statistics->Data);
                    $ret->MaxScore = $questionVersion->Score;
                    $ret->UserScore = $statistics->Score;
                    $ret->Note = $questionVersion->Note;
                    if ($className == 'HotSpotQuestion') {
                        $qc = new AriQuizQuestionController();
                        $ret->Files = $qc->call('getSimpleQuestionFiles', $statistics->QuestionVersionId);
                    }
                }
            }
            AriResponse::sendJsonResponse($ret);
        }
    };
?>