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
    AriKernel::import('Web.Page.PageBase');
    AriKernel::import('Controllers.AriQuiz.UserQuizController');
    AriKernel::import('Web.Controls.TextBox');
    AriKernel::import('Web.Controls.Validators.RequiredValidator');
    AriKernel::import('Web.Controls.Validators.RegExValidator');
    AriKernel::import('Components.AriQuiz.Util');
    AriKernel::import('Components.AriQuiz.CDHelper');
    class quizAriPage extends AriPageBase {
        var $_tbxGuestName;
        var $_tbxGuestMail;
        var $_rvGuestName;
        var $_rvGuestEmail;
        var $_revGuestEmail;
        var $_quizController;
        var $_userQuizController;
        var $_quiz;
        function _init() {
            $this->_userQuizController = new AriUserQuizController();
            $this->_quizController = new AriQuizController();
            parent::_init();
        }
        function _createControls() {
            $this->_tbxGuestName = &new AriTextBoxWebControl('tbxGuestName', array('name'=>'extraData[UserName]'));
            $this->_tbxGuestMail = &new AriTextBoxWebControl('tbxGuestMail', array('name'=>'extraData[Email]'));
        }
        function _getQuiz() {
            if (is_null($this->_quiz)) {
                $quizId = intval(AriRequest::getParam('quizId', 0));
                $this->_quiz = $this->_quizController->call('getQuiz', $quizId);
            }
            return $this->_quiz;
        }
        function _createValidators() {
            global $my;
            $quiz = $this->_getQuiz();
            if (!$my->get('id')) {
                if ($quiz->Anonymous == 'No') {
                    $this->_rvGuestName = &new AriRequiredValidatorWebControl('rvGuestName', array('controlToValidate'=>'tbxGuestName', 'errorMessageResourceKey'=>'Validator.Name'));
                    $this->_rvGuestEmail = &new AriRequiredValidatorWebControl('rvGuestEmail', array('controlToValidate'=>'tbxGuestMail', 'errorMessageResourceKey'=>'Validator.Email'));
                }
                if ($quiz->Anonymous != 'Yes') {
                    $this->_revGuestEmail = &new AriRegExValidatorWebControl('revGuestEmail', '/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+([;.](([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+)*$/i', array('controlToValidate'=>'tbxGuestMail', 'clientRegEx'=>'/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+([;.](([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+)*$/i', 'errorMessageResourceKey'=>'Validator.EmailFormat'));
                }
            }
        }
        function execute() {
            global $my;
            $userId = $my->get('id');
            $takeQuizTask = 'get_ticket';
            $quiz = $this->_getQuiz();
            $errorCodeList = AriConstantsManager::getVar('ErrorCode.TakeQuiz', AriUserQuizControllerConstants::getClassName());
            $errorCode = !empty($quiz->QuizId) ? $this->_userQuizController->call('canTakeQuiz2', $quiz, $userId, $my->get('usertype')) : $errorCodeList['UnknownError'];
            if ($errorCodeList['HasPausedQuiz'] == $errorCode) {
                $takeQuizTask = 'resume_quiz';
                $errorCode = $errorCodeList['None'];
            }
            $canTakeQuiz = ($errorCodeList['None'] == $errorCode);
            $errorMessage = $this->_getErrorMessageResKey($errorCode);
            if ($canTakeQuiz && !$userId && $quiz->Anonymous != 'Yes') {
                $loadFromCookie = true;
                if (isset($_COOKIE['ariQuizTicketId'])) {
                    $statistics = $this->_userQuizController->call('getStatisticsInfoByTicketId', $_COOKIE['ariQuizTicketId'], 0, array('Process', 'Prepare'), $quiz->QuizId);
                    if (!empty($statistics)) {
                        $extraData = $statistics->parseExtraDataXml($statistics->ExtraData);
                        $this->_tbxGuestMail->setText(AriUtils::getParam($extraData, 'Email', ''));
                        $this->_tbxGuestName->setText(AriUtils::getParam($extraData, 'UserName', ''));
                        $this->_enableControls(false);
                        $loadFromCookie = false;
                    }
                }
                if ($loadFromCookie) {
                    $this->_tbxGuestMail->setText(AriUtils::getParam($_COOKIE, 'aq_email', ''));
                    $this->_tbxGuestName->setText(AriUtils::getParam($_COOKIE, 'aq_name', ''));
                }
            }
            $ver = AriConfigWrapper::getConfigKey(AriConstantsManager::getVar('Config.Version', AriQuizComponent::getCodeName()), '1.0.0');
            $this->addVar('cssFile', AriQuizUtils::getCssFile(null));
            $this->addVar('version', $ver);
            $this->addVar('ticketId', AriRequest::getParam('ticketId', ''));
            $this->addVar('quiz', $quiz);
            $this->addVar('canTakeQuiz', $canTakeQuiz);
            $this->addVar('takeQuizTask', $takeQuizTask);
            $this->addVar('errorMessage', $errorMessage);
            parent::execute();
        }
        function _enableControls($enabled) {
            $this->_tbxGuestMail->setEnabled($enabled);
            $this->_tbxGuestName->setEnabled($enabled);
            if ($this->_rvGuestEmail) $this->_rvGuestEmail->setEnableJsValidation($enabled);
            if ($this->_rvGuestName) $this->_rvGuestName->setEnableJsValidation($enabled);
            if ($this->_revGuestEmail) $this->_revGuestEmail->setEnableJsValidation($enabled);
        }
        function _getErrorMessageResKey($code) {
            $errorCodeList = AriConstantsManager::getVar('ErrorCode.TakeQuiz', AriUserQuizControllerConstants::getClassName());
            if ($code == $errorCodeList['None']) return null;
            $resKey = 'FrontEnd.QuizNotAvailable';
            switch ($code) {
                case $errorCodeList['AttemptCount']:
                    $resKey = 'Error.QuizAttemptCount';
                break;
                case $errorCodeList['LagTime']:
                    $resKey = 'Error.QuizLagTime';
                break;
                case $errorCodeList['NotRegistered']:
                    $resKey = 'Error.QuizNotRegistered';
                break;
                case $errorCodeList['NotHavePermissions']:
                    $resKey = 'Error.QuizNotHavePermissions';
                break;
            }
            return $resKey;
        }
    };
?>