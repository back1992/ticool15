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
    AriKernel::import('Config.ConfigWrapper');
    AriKernel::import('Controllers.TextTemplateController');
    AriKernel::import('MailTemplates.MailTemplatesController');
    AriKernel::import('Controllers.AriQuiz.QuizController');
    AriKernel::import('Controllers.AriQuiz.UserQuizController');
    AriKernel::import('Controllers.AriQuiz.ResultController');
    AriKernel::import('Controllers.AriQuiz.ResultScaleController');
    AriKernel::import('Components.AriQuiz.Util');
    AriKernel::import('Web.Controls.Data.MultiPageDataTable');
    AriKernel::import('Components.AriQuiz.CDHelper');
    AriKernel::import('Event.EventController');
    class quiz_finishedAriPage extends AriPageBase {
        var $_templates = null;
        var $_result = null;
        var $_statByCategories = null;
        var $_templateController;
        var $_userQuizController;
        var $_quizController;
        var $_resultController;
        var $_stopExecution = false;
        var $_isQuizFinished = null;
        var $_ticketId = null;
        function _initFullResults($ticketId = null) {
            static $init = false;
            if (!$init && (AriQuizCDHelper::hasCDKey('CD_f7767f66ef91e0eac408f52855d0f975') || AriQuizCDHelper::hasCDKey('CD_f7767f66ef91e0eac408f52855d0f971'))) {
                if (is_null($ticketId)) $ticketId = $this->_getTicketId();
                $statInfoId = $this->_userQuizController->call('getStatisticsInfoIdByTicketId', $ticketId);
                $results = $this->_resultController->call('getBaseView', $statInfoId);
                $this->addVar('cssFile', AriQuizUtils::getCssFile(null));
                $this->addVar('fullResults', $results);
                $init = true;
            }
        }
        function _getTicketId() {
            if (is_null($this->_ticketId)) {
                $this->_ticketId = AriRequest::getParam('ticketId', '');
            }
            return $this->_ticketId;
        }
        function _init() {
            $this->_quizController = new AriQuizController();
            $this->_userQuizController = new AriUserQuizController();
            $this->_resultController = new AriQuizResultController();
            $this->_templateController = new AriTextTemplateController();
            parent::_init();
        }
        function _checkQuizFinished($ticketId, $woRedirect = false) {
            global $option, $Itemid;
            if ($this->_isQuizFinished) return;
            $this->_isQuizFinished = $this->_userQuizController->call('isQuizFinishedByTicketId', $ticketId);
            if (!$this->_isQuizFinished) {
                if (!$woRedirect) AriResponse::redirect(AriJoomlaBridge::getLink('index.php?option='.$option.'&task=question&ticketId='.$ticketId.'&Itemid='.$Itemid));
                return false;
            }
        }
        function _sendResultToAdmin($result = null) {
            $ticketId = $this->_getTicketId();
            $sendResultInfo = $this->_resultController->call('sendResultInfo', $ticketId);
            if (empty($sendResultInfo)) return false;
            $email = trim($sendResultInfo[0]);
            if (empty($result)) {
                $result = $this->_getResult();
            }
            $templateKey = AriConstantsManager::getVar('TextTemplates.AdminEmail', AriQuizComponent::getCodeName());
            if (!$this->_isVisibleCtrl($templateKey, $ticketId)) return false;
            $resultText = $this->_getResultText($templateKey, $ticketId, $result);
            if (!empty($resultText)) {
                $body = AriWebHelper::translateDbValue($resultText, false);
                if (AriQuizCDHelper::hasCDKey('CD_f7767f66ef91e0eac408f52855d0f971')) {
                    $this->_initFullResults();
                    @ob_start();
                    $processPage = &$this;
                    require dirname($this->_template) .'/stats/stats1/full_result.html.php';
                    $body.= ob_get_contents();
                    @ob_clean();
                    @ob_end_flush();
                }
                $mailTemplate = $this->_getMailTemplate($templateKey, $ticketId);
                $subject = AriWebHelper::translateDbValue(AriUtils::getParam($mailTemplate, 'Subject', ''));
                if (empty($subject)) $subject = AriWebHelper::translateResValue('Label.EmailQuizResult');
                $isSend = AriJoomlaBridge::sendMail(AriUtils::getParam($mailTemplate, 'From', ''), AriUtils::getParam($mailTemplate, 'FromName', ''), $email, $subject, $body, true);
                if ($isSend) {
                    $this->_resultController->call('markResultSend', $ticketId);
                }
                return $isSend;
            }
            return false;
        }
        function execute() {
            if ($this->_stopExecution) return;
            global $option, $my;
            $ticketId = $this->_getTicketId();
            $this->_checkQuizFinished($ticketId);
            $isQuizMarkAsFinished = $this->_userQuizController->call('markQuizAsFinished', $ticketId);
            $userId = $my->get('id');
            $result = $this->_getResult();
            if (empty($result)) AriQuizUtils::redirectToInfo('Error.ResultPage');
            $isPassed = !empty($result['_Passed']);
            $this->_sendResultToAdmin($result);
            if ($isQuizMarkAsFinished) {
                AriEventController::raiseEvent('onEndQuiz', $result);
            }
            $templateKey = $isPassed ? AriConstantsManager::getVar('TextTemplates.Successful', AriQuizComponent::getCodeName()) : AriConstantsManager::getVar('TextTemplates.Failed', AriQuizComponent::getCodeName());
            $resultText = $this->_getResultText($templateKey, $ticketId, $result);
            $emailVisible = !empty($result['Email']) && $this->_isVisibleCtrl($isPassed ? AriConstantsManager::getVar('TextTemplates.SuccessfulEmail', AriQuizComponent::getCodeName()) : AriConstantsManager::getVar('TextTemplates.FailedEmail', AriQuizComponent::getCodeName()), $ticketId);
            $printVisible = $this->_isVisibleCtrl($isPassed ? AriConstantsManager::getVar('TextTemplates.SuccessfulPrint', AriQuizComponent::getCodeName()) : AriConstantsManager::getVar('TextTemplates.FailedPrint', AriQuizComponent::getCodeName()), $ticketId);
            $this->addVar('ticketId', $ticketId);
            $this->addVar('resultText', $resultText);
            $this->addVar('result', $result);
            $this->addVar('emailVisible', $emailVisible);
            $this->addVar('printVisible', $printVisible);
            $this->addVar('cssFile', AriQuizUtils::getCssFile($result['CssTemplateId']));
            $ver = AriConfigWrapper::getConfigKey(AriConstantsManager::getVar('Config.Version', AriQuizComponent::getCodeName()), '1.0.0');
            $this->addVar('version', $ver);
            if ($this->_isStatisticsShow()) {
                $dataTable = $this->_createDataTable($result['StatisticsInfoId'], $result['ParsePluginTag']);
                $this->addVar('dataTable', $dataTable);
            }
            $this->_initFullResults();
            parent::execute();
        }
        function _isStatisticsShow() {
            global $my;
            $userId = $my->get('id');
            $isShowStat = false;
            $fullStat = $this->_getResultField('FullStatistics', 'Never');
            if ($fullStat != 'Never') {
                if ($fullStat == 'Always' || !$userId) return true;
                $attemptCount = $this->_getResultField('AttemptCount', 0);
                if ($attemptCount<1) return true;
                $quizId = $this->_getResultField('QuizId', 0);
                $passedQuizCount = $this->_resultController->call('getPassedQuizCount', $quizId, $userId);
                if ($passedQuizCount >= $attemptCount) return true;
            }
            return $isShowStat;
        }
        function _createDataTable($statisticsInfoId, $parseTag) {
            global $option, $mosConfig_live_site;
            $totalCnt = $this->_resultController->call('getStatCount', $statisticsInfoId);
            $rowsPerPage = array(1);
            if ($totalCnt>1) {
                $pageCnt = floor($totalCnt/5);
                for ($i = 0;$i<$pageCnt;$i++) {
                    $rowsPerPage[] = 5*($i+1);
                }
                if ($totalCnt%5>0) $rowsPerPage[] = $totalCnt;
            }
            $pagRowsPerPage = $totalCnt<5 ? $totalCnt : 5;
            $dsUrl = $mosConfig_live_site.'/index.php?option='.$option.'&task='.$this->executionTask.'$ajax|getStatList&sid='.$statisticsInfoId;
            if (!$parseTag) $dsUrl.= '&parseTag=0';
            $columns = array(new AriDataTableControlColumn(array('key'=>'QuestionData', 'label'=>AriWebHelper::translateResValue('Label.QuizStatistics'), 'formatter'=>'YAHOO.ARISoft.Quiz.formatters.formatQuestionStatData')), new AriDataTableControlColumn(array('key'=>'QuestionIndex', 'label'=>'', 'hidden'=>true)),);
            $dataTable = new AriMultiPageDataTableControl('dtQuizStat', $columns, array('dataUrl'=>$dsUrl), array('rowsPerPageOptions'=>$rowsPerPage, 'rowsPerPage'=>$pagRowsPerPage));
            return $dataTable;
        }
        function _getMailTemplate($templateKey, $ticketId) {
            $mailTemplate = null;
            $templates = $this->_getTemplates($ticketId);
            $resultText = '';
            if (isset($templates[$templateKey])) {
                $templateId = $templates[$templateKey];
                $codeName = AriQuizComponent::getCodeName();
                $mailTemplateController = new AriMailTemplatesController(AriConstantsManager::getVar('MailTemplateTable', $codeName), AriConstantsManager::getVar('TextTemplateTable', $codeName));
                $mailTemplate = $mailTemplateController->call('getTemplateByTextId', $templateId, null, false);
            }
            return $mailTemplate;
        }
        function _getResultText($templateKey, $ticketId, $result) {
            $templates = $this->_getTemplates($ticketId);
            $resultText = '';
            if (isset($templates[$templateKey])) {
                $templateId = $templates[$templateKey];
                $template = $this->_templateController->call('getTemplate', $templateId);
                if ($template) {
                    if (AriQuizCDHelper::hasCDKey('CD_f7767f66ef91e0eac408f94055d0f975') && strpos($template->Value, 'StatByCategories') !== false) {
                        $result['StatByCategories'] = $this->_getStatByCategories($result['StatisticsInfoId']);
                    }
                    $resultText = $template->parse($result);
                }
            }
            return $resultText;
        }
        function _getStatByCategories($statInfoId) {
            if (is_null($this->_statByCategories)) {
                $this->_statByCategories = AriQuizUtils::getStatByCategoriesHtml($this->_resultController->call('getFinishedInfoByCategory', $statInfoId));
            }
            return $this->_statByCategories;
        }
        function _getTemplates($ticketId) {
            if ($this->_templates === null) {
                global $my;
                $templates = array();
                $userId = $my->get('id');
                $result = $this->_getResult();
                if ($result['ResultScaleId']) {
                    $rsController = new AriQuizResultScaleController();
                    $scaleItem = $rsController->call('getScaleItemByScore', $result['ResultScaleId'], $result['PercentScore']);
                    if ($scaleItem) {
                        $templateKeys = AriConstantsManager::getVar('TextTemplates', AriQuizComponent::getCodeName());
                        if ($scaleItem->TextTemplateId) {
                            $templates[$templateKeys['Successful']] = $templates[$templateKeys['Failed']] = $scaleItem->TextTemplateId;
                        }
                        if ($scaleItem->PrintTemplateId) {
                            $templates[$templateKeys['SuccessfulPrint']] = $templates[$templateKeys['FailedPrint']] = $scaleItem->PrintTemplateId;
                        }
                        if ($scaleItem->MailTemplateId) {
                            $templates[$templateKeys['SuccessfulEmail']] = $templates[$templateKeys['FailedEmail']] = $scaleItem->MailTemplateId;
                        }
                    }
                    $quizId = $this->_getResultField('QuizId', 0);
                    $quizTemplates = $this->_templateController->call('getEntitySingleTemplate', AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName()), $quizId);
                    if (isset($quizTemplates[$templateKeys['AdminEmail']])) $templates[$templateKeys['AdminEmail']] = $quizTemplates[$templateKeys['AdminEmail']];
                } else {
                    $quizId = $this->_getResultField('QuizId', 0);
                    $templates = $this->_templateController->call('getEntitySingleTemplate', AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName()), $quizId);
                    if (empty($templates)) $templates = array();
                }
                $this->_templates = $templates;
            }
            return $this->_templates;
        }
        function _getResult() {
            if (is_null($this->_result)) {
                global $my;
                $ticketId = $this->_getTicketId();
                $userId = $my->get('id');
                $result = $this->_resultController->call('getFormattedFinishedResult', $ticketId, $userId, array('UserName'=>'Guest'));
                if (!is_array($result)) $result = array();
                $this->_result = $result;
            }
            return $this->_result;
        }
        function _getResultField($field, $defValue = null) {
            $result = $this->_getResult();
            return AriUtils::getParam($result, $field, $defValue);
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('email', 'clickEmail');
            $this->_registerEventHandler('print', 'clickPrint');
        }
        function _isVisibleCtrl($templateKey, $ticketId) {
            $templates = $this->_getTemplates($ticketId);
            return isset($templates[$templateKey]);
        }
        function clickPrint($eventArgs) {
            global $my, $option, $Itemid;
            $ticketId = $this->_getTicketId();
            $this->_checkQuizFinished($ticketId);
            $userId = $my->get('id');
            $result = $this->_getResult();
            $isPassed = !empty($result['_Passed']);
            $templateKey = $isPassed ? AriConstantsManager::getVar('TextTemplates.SuccessfulPrint', AriQuizComponent::getCodeName()) : AriConstantsManager::getVar('TextTemplates.FailedPrint', AriQuizComponent::getCodeName());
            if (!$this->_isVisibleCtrl($templateKey, $ticketId)) {
                AriResponse::redirect(AriJoomlaBridge::getLink('index.php?option='.$option.'&task=quiz_finished&ticketId='.$ticketId.'&Itemid='.$Itemid));
            }
            $resultText = $this->_getResultText($templateKey, $ticketId, $result);
            $this->_initFullResults();
            AriWebHelper::displayDbValue($resultText, false);
            $processPage = &$this;
            require_once dirname($this->_template) .'/stats/stats1/full_result.html.php';
            $this->_stopExecution = true;
        }
        function clickEmail($eventArgs) {
            global $my;
            $ticketId = $this->_getTicketId();
            $this->_checkQuizFinished($ticketId);
            $result = $this->_getResult();
            if (!empty($result['Email'])) {
                $email = $result['Email'];
                $result = $this->_getResult();
                $isPassed = !empty($result['_Passed']);
                $templateKey = $isPassed ? AriConstantsManager::getVar('TextTemplates.SuccessfulEmail', AriQuizComponent::getCodeName()) : AriConstantsManager::getVar('TextTemplates.FailedEmail', AriQuizComponent::getCodeName());
                if (!$this->_isVisibleCtrl($templateKey, $ticketId)) return;
                $resultText = $this->_getResultText($templateKey, $ticketId, $result);
                if (!empty($resultText)) {
                    $mailTemplate = $this->_getMailTemplate($templateKey, $ticketId);
                    $subject = AriWebHelper::translateDbValue(AriUtils::getParam($mailTemplate, 'Subject', ''));
                    if (empty($subject)) $subject = AriWebHelper::translateResValue('Label.EmailQuizResult');
                    $body = AriWebHelper::translateDbValue($resultText, false);
                    $isSend = AriJoomlaBridge::sendMail(AriUtils::getParam($mailTemplate, 'From', ''), AriUtils::getParam($mailTemplate, 'FromName', ''), $email, $subject, $body, true);
                    $msg = AriWebHelper::translateResValue($isSend ? 'Label.EmailSend' : 'Label.EmailNotSend');
                    $this->addVar('infoMsg', $msg);
                }
            }
        }
        function _registerAjaxHandlers() {
            $this->_registerAjaxHandler('getStatList', 'ajaxGetStatList');
            $this->_registerAjaxHandler('email', 'ajaxSendMail');
        }
        function ajaxSendMail() {
            global $my;
            $res = false;
            $ticketId = $this->_getTicketId();
            if (!$this->_checkQuizFinished($ticketId, true)) AriResponse::sendJsonResponse($res);
            $userId = $my->get('id');
            if (!empty($userId)) {
                $email = $my->get('email');
                if (!empty($email)) {
                    $result = $this->_getResult();
                    $isPassed = !empty($result['_Passed']);
                    $templateKey = $isPassed ? AriConstantsManager::getVar('TextTemplates.SuccessfulEmail', AriQuizComponent::getCodeName()) : AriConstantsManager::getVar('TextTemplates.FailedEmail', AriQuizComponent::getCodeName());
                    if ($this->_isVisibleCtrl($templateKey, $ticketId)) {
                        $resultText = $this->_getResultText($templateKey, $ticketId, $result);
                        if (!empty($resultText)) {
                            $mailTemplate = $this->_getMailTemplate($templateKey, $ticketId);
                            $subject = AriWebHelper::translateDbValue(AriUtils::getParam($mailTemplate, 'Subject', ''));
                            if (empty($subject)) $subject = AriWebHelper::translateResValue('Label.EmailQuizResult');
                            $body = AriWebHelper::translateDbValue($resultText, false);
                            $res = AriJoomlaBridge::sendMail(AriUtils::getParam($mailTemplate, 'From', ''), AriUtils::getParam($mailTemplate, 'FromName', ''), $email, $subject, $body, true);
                        }
                    }
                }
            }
            AriResponse::sendJsonResponse($res);
        }
        function ajaxGetStatList() {
            $sid = AriRequest::getParam('sid', 0);
            $filter = new AriDataFilter(array('startOffset'=>0, 'limit'=>15, 'sortField'=>'QuestionIndex', 'sortDirection'=>'asc'), true, null, array('QuestionIndex'));
            $totalCnt = $this->_resultController->call('getStatCount', $sid, $filter);
            if ($totalCnt<$filter->getConfigValue('limit')) $filter->setConfigValue('limit', $totalCnt);
            $filter->fixFilter($totalCnt);
            $parseTag = AriUtils::parseValueBySample(AriRequest::getParam('parseTag', true), true);
            $questions = $this->_resultController->call('getJsonStatList', $sid, $filter, $parseTag);
            $data = AriMultiPageDataTableControl::createDataInfo($questions, $filter, $totalCnt);
            AriResponse::sendJsonResponse($data);
        }
    };
?>