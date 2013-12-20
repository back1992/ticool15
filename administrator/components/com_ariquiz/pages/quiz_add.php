<?php

/** ARI Soft copyright
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
 * */
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');
AriKernel::import('Web.Page.Specific.AdminSecurePageBase');
AriKernel::import('Web.Controls.Editor');
AriKernel::import('Web.Controls.CheckBox');
AriKernel::import('Web.Controls.ListBox');
AriKernel::import('Web.Controls.TextBox');
AriKernel::import('Web.Controls.Validators.RequiredValidator');
AriKernel::import('Web.Controls.Validators.RangeValidator');
AriKernel::import('Web.Controls.Validators.RegExValidator');
AriKernel::import('Web.Controls.Validators.CustomValidator');
AriKernel::import('Controllers.AriQuiz.QuizController');
AriKernel::import('Controllers.AriQuiz.CategoryController');
AriKernel::import('Controllers.FileController');
AriKernel::import('Security.Security');
AriKernel::import('Entity.EntityFactory');
AriKernel::import('TextTemplates.TextTemplateController');
AriKernel::import('MailTemplates.MailTemplatesController');
AriKernel::import('Controllers.AriQuiz.PropertyController');
AriKernel::import('Web.Controls.ControlFactory');
AriKernel::import('Controllers.AriQuiz.ResultScaleController');

class quiz_addAriPage extends AriAdminSecurePageBase {

    var $_fileController;
    var $_quizController;
    var $_propertyController;
    var $_tbxQuizName;
    var $_tbxTotalTime;
    var $_tbxPassedScore;
    var $_tbxQuestionCount;
    var $_tbxQuestionTime;
    var $_tbxAdminEmail;
    var $_tbxLagTime;
    var $_tbxAttemptCount;
    var $_lbCategories;
    var $_lbQuestionOrderType;
    var $_lbSucEmail;
    var $_lbFailEmail;
    var $_lbSucPrint;
    var $_lbFailPrint;
    var $_lbSuc;
    var $_lbFail;
    var $_lbAdminEmail;
    var $_lbCss;
    var $_lbAccess;
    var $_lbScale;
    var $_lbAnonymous;
    var $_lbFullStatisticsType;
    var $_chkStatus;
    var $_chkParsePluginTag;
    var $_chkCanSkip;
    var $_chkCanStop;
    var $_chkUseCalc;
    var $_chkRandom;
    var $_chkShowCorrectAnswer;
    var $_chkShowExplanation;
    var $_edDescription;

    function _init() {
        $this->_quizController = new AriQuizController();
        $this->_fileController = new AriFileController();
        $this->_propertyController = new AriPropertyController('#__ariquiz_property', '#__ariquiz_property_value');
        parent::_init();
    }

    function execute() {
        global $option, $act, $mosConfig_live_site;
        $quizId = AriRequest::getParam('quizId', 0);
        $quiz = $this->_getQuiz($quizId);
        $props = $this->_getExtraProperties($quizId);
        $this->_bindControls($quiz);
        $this->addVar('props', $props);
        $this->addVar('quizId', $quizId);
        $this->addVar('quiz', $quiz);
        $this->setTitle(AriWebHelper::translateResValue('Label.Quiz') . ' : ' . AriWebHelper::translateResValue($quizId ? 'Label.UpdateItem' : 'Label.AddItem'));
        parent::execute();
    }

    function _getCssTemplateList() {
        $cssTemplates = $this->_fileController->call('getFileList', AriConstantsManager::getVar('FileGroup.CssTemplate', AriQuizComponent::getCodeName()));
        return $cssTemplates;
    }

    function _getAccessTree() {
        global $acl;
        $uah = new AriUserAccessHelper($acl);
        $accessTree = $uah->getGroupsFlatTree(AriConstantsManager::getVar('Groups.Registered', AriUserAccessHelperConstants::getClassName()));
        return $accessTree;
    }

    function _getCategoryList() {
        $categoryController = new AriQuizCategoryController();
        $categoryList = $categoryController->call('getCategoryList');
        return $categoryList;
    }

    function _getQuizTextTemplateList($quizId) {
        $templateList = array();
        if ($quizId > 0) {
            $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName()));
            $templateList = $templateController->call('getEntitySingleTemplate', AriConstantsManager::getVar('EntityKey', AriQuizComponent::getCodeName()), $quizId);
        } return $templateList;
    }

    function _getMailTemplateList() {
        $codeName = AriQuizComponent::getCodeName();
        $mailTemplateController = new AriMailTemplatesController(AriConstantsManager::getVar('MailTemplateTable', $codeName), AriConstantsManager::getVar('TextTemplateTable', $codeName));
        $templateList = $mailTemplateController->call('getTemplateList', null, AriConstantsManager::getVar('TemplateGroup.MailResults', $codeName));
        return $templateList;
    }

    function _getTextTemplateList() {
        $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName()));
        $templateList = $templateController->call('getTemplateList', AriConstantsManager::getVar('TemplateGroup.Results', AriQuizComponent::getCodeName()));
        return $templateList;
    }

    function _getScaleList() {
        $scaleController = new AriQuizResultScaleController();
        $filter = new AriDataFilter(array('startOffset' => null, 'limit' => null, 'sortField' => 'ScaleName', 'dir' => 'asc'));
        return $scaleController->call('getScaleList', $filter);
    }

    function _getExtraProperties($quizId) {
        $props = $this->_quizController->call('getProperties', $quizId);
        if ($props) {
            foreach ($props as $propItem) {
                $control = & AriWebControlFactory::createControl($propItem->ControlType, 'QuizProp[' . $propItem->PropertyName . ']', array('name' => 'QuizProp[' . $propItem->PropertyName . ']'));
                $control->setValue($propItem->PropertyValue);
            }
        } return $props;
    }

    function _getQuiz($quizId) {
        $quiz = null;
        if ($quizId != 0) {
            $quiz = $this->_quizController->call('getQuiz', $quizId);
        } else {
            $quiz = AriEntityFactory::createInstance('AriQuizEntity', AriGlobalPrefs::getEntityGroup());
        } return $quiz;
    }

    function _registerEventHandlers() {
        $this->_registerEventHandler('save', 'clickSave');
        $this->_registerEventHandler('apply', 'clickApply');
        $this->_registerEventHandler('cancel', 'clickCancel');
    }

    function clickCancel($eventArgs) {
        AriWebHelper::cancelAction('quiz_list');
    }

    function clickSave($eventArgs) {
        $quiz = $this->_saveQuiz();
        if (!$this->_isError()) {
            AriWebHelper::preCompleteAction('Complete.QuizSave', array('task' => 'quiz_list'));
        }
    }

    function clickApply($eventArgs) {
        $quiz = $this->_saveQuiz();
        if (!$this->_isError()) {
            AriWebHelper::preCompleteAction('Complete.QuizSave', array('task' => 'quiz_add', 'quizId' => $quiz->QuizId, 'hidemainmenu' => 1));
        }
    }

    function _saveQuiz() {
        global $my;
        $ownerId = $my->get('id');
        $fields = AriWebHelper::translateRequestValues('zQuiz');
        return $this->_quizController->call('saveQuiz', AriRequest::getParam('quizId', 0), $fields, $ownerId, AriRequest::getParam('Category', array()), AriRequest::getParam('AccessGroup', array()), AriRequest::getParam('zTextTemplate', array()), AriRequest::getParam('QuizProp', null));
    }

    function _createControls() {
        $quizTextList = AriConstantsManager::getVar('TextTemplates', AriQuizComponent::getCodeName());
        $this->_tbxQuizName = & new AriTextBoxWebControl('tbxQuizName', array('name' => 'zQuiz[QuizName]'));
        $this->_tbxTotalTime = & new AriTextBoxWebControl('tbxTotalTime', array('name' => 'zQuiz[TotalTime]', 'maxLength' => 85));
        $this->_tbxPassedScore = & new AriTextBoxWebControl('tbxPassedScore', array('name' => 'zQuiz[PassedScore]', 'maxLength' => 85));
        $this->_tbxQuestionCount = & new AriTextBoxWebControl('tbxQuestionCount', array('name' => 'zQuiz[QuestionCount]', 'maxLength' => 85));
        $this->_tbxQuestionTime = & new AriTextBoxWebControl('tbxQuestionTime', array('name' => 'zQuiz[QuestionTime]', 'maxLength' => 85));
        $this->_tbxAdminEmail = & new AriTextBoxWebControl('tbxAdminEmail', array('name' => 'zQuiz[AdminEmail]'));
        $this->_tbxLagTime = & new AriTextBoxWebControl('tbxLagTime', array('name' => 'zQuiz[LagTime]'));
        $this->_tbxAttemptCount = & new AriTextBoxWebControl('tbxAttemptCount', array('name' => 'zQuiz[AttemptCount]'));
        $this->_lbCategories = & new AriListBoxWebControl('lbCategories', array('name' => 'Category[]'));
        $this->_lbQuestionOrderType = & new AriListBoxWebControl('lbQueOrderType', array('name' => 'zQuiz[QuestionOrderType]', 'translateText' => false));
        $this->_lbAnonymous = & new AriListBoxWebControl('lbAnonymous', array('name' => 'zQuiz[Anonymous]', 'translateText' => false));
        $this->_lbFullStatisticsType = & new AriListBoxWebControl('lbFullStatisticsType', array('name' => 'zQuiz[FullStatistics]', 'translateText' => false));
        $this->_lbCss = & new AriListBoxWebControl('lbCss', array('name' => 'zQuiz[CssTemplateId]'));
        $this->_lbSucEmail = & new AriListBoxWebControl('lbSucEmail', array('name' => 'zTextTemplate[' . $quizTextList['SuccessfulEmail'] . ']'));
        $this->_lbFailEmail = & new AriListBoxWebControl('lbFailEmail', array('name' => 'zTextTemplate[' . $quizTextList['FailedEmail'] . ']'));
        $this->_lbSucPrint = & new AriListBoxWebControl('lbSucPrint', array('name' => 'zTextTemplate[' . $quizTextList['SuccessfulPrint'] . ']'));
        $this->_lbFailPrint = & new AriListBoxWebControl('lbFailPrint', array('name' => 'zTextTemplate[' . $quizTextList['FailedPrint'] . ']'));
        $this->_lbSuc = & new AriListBoxWebControl('lbSuc', array('name' => 'zTextTemplate[' . $quizTextList['Successful'] . ']'));
        $this->_lbFail = & new AriListBoxWebControl('lbFail', array('name' => 'zTextTemplate[' . $quizTextList['Failed'] . ']'));
        $this->_lbAdminEmail = & new AriListBoxWebControl('lbAdminEmail', array('name' => 'zTextTemplate[' . $quizTextList['AdminEmail'] . ']'));
        $this->_lbAccess = & new AriListBoxWebControl('lbAccess', array('name' => 'AccessGroup[]', 'translateText' => false));
        $this->_lbScale = & new AriListBoxWebControl('lbScale', array('name' => 'zQuiz[ResultScaleId]'));
        $this->_chkStatus = & new AriCheckBoxWebControl('chkStatus', array('name' => 'zQuiz[Active]'));
        $this->_chkParsePluginTag = & new AriCheckBoxWebControl('chkParsePluginTag', array('name' => 'zQuiz[ParsePluginTag]'));
        $this->_chkCanSkip = & new AriCheckBoxWebControl('chkCanSkip', array('name' => 'zQuiz[CanSkip]'));
        $this->_chkCanStop = & new AriCheckBoxWebControl('chkCanStop', array('name' => 'zQuiz[CanStop]'));
        $this->_chkRandom = & new AriCheckBoxWebControl('chkRandom', array('name' => 'zQuiz[RandomQuestion]'));
        $this->_chkUseCalc = & new AriCheckBoxWebControl('chkUseCalc', array('name' => 'zQuiz[UseCalculator]'));
        $this->_chkShowCorrectAnswer = & new AriCheckBoxWebControl('chkShowCorrectAnswer', array('name' => 'zQuiz[ShowCorrectAnswer]'));
        $this->_chkShowExplanation = & new AriCheckBoxWebControl('chkShowExplanation', array('name' => 'zQuiz[ShowExplanation]'));
        $this->_edDescription = & new AriEditorWebControl('edDescription', array('name' => 'zQuiz[Description]'));
    }

    function _createValidators() {
        new AriRequiredValidatorWebControl('arqvQuizName', array('controlToValidate' => 'tbxQuizName', 'errorMessageResourceKey' => 'Validator.NameRequired'));
        new AriRangeValidatorWebControl('aravTotalTime', array('controlToValidate' => 'tbxTotalTime', 'minValue' => 0, 'errorMessageResourceKey' => 'Validator.TotalTime'));
        new AriRangeValidatorWebControl('aravPassedScore', array('controlToValidate' => 'tbxPassedScore', 'minValue' => 0, 'maxValue' => 100, 'errorMessageResourceKey' => 'Validator.PassedScore'));
        new AriRangeValidatorWebControl('aravQuestionCount', array('controlToValidate' => 'tbxQuestionCount', 'minValue' => 0, 'errorMessageResourceKey' => 'Validator.QuestionCount'));
        new AriRangeValidatorWebControl('aravQuestionTime', array('controlToValidate' => 'tbxQuestionTime', 'minValue' => 0, 'errorMessageResourceKey' => 'Validator.QuestionTime'));
        new AriRangeValidatorWebControl('aravLagTime', array('controlToValidate' => 'tbxLagTime', 'minValue' => 0, 'errorMessageResourceKey' => 'Validator.LagTime'));
        new AriRangeValidatorWebControl('aravAttempCount', array('controlToValidate' => 'tbxAttempCount', 'minValue' => 0, 'errorMessageResourceKey' => 'Validator.AttemptCount'));
        new AriRegExValidatorWebControl('arevAdminEmail', '/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+([;.](([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+)*$/i', array('controlToValidate' => 'tbxAdminEmail', 'clientRegEx' => '/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+([;.](([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+)*$/i', 'errorMessageResourceKey' => 'Validator.EmailIncorrect'));
        $validate = array(&$this, 'cvQuizName');
        new AriCustomValidatorWebControl('acvQuizName', $validate, array('emptyValidate' => false, 'controlToValidate' => 'tbxQuizName', 'clientValidateFunc' => 'YAHOO.ARISoft.page.quizNameValidate', 'errorMessageResourceKey' => 'Validator.NameNotUnique'));
    }

    function cvQuizName() {
        return true;
    }

    function _getFullStatisticsTypes() {
        $statTypes = AriConstantsManager::getVar('FullStatisticsType', AriQuizControllerConstants::getClassName());
        $data = array();
        foreach ($statTypes as $key => $value) {
            $data[$value] = AriWebHelper::translateResValue('Label.FST.' . $key);
        } return $data;
    }

    function _getQuestionOrderTypes() {
        $qOrderTypes = AriConstantsManager::getVar('QuestionOrderType', AriQuizControllerConstants::getClassName());
        $data = array();
        foreach ($qOrderTypes as $key => $value) {
            $data[$value] = AriWebHelper::translateResValue('Label.QOT.' . $key);
        }return $data;
    }

    function _getAnonymousStatuses() {
        $statuses = AriConstantsManager::getVar('AnonymousStatus', AriQuizControllerConstants::getClassName());
        $data = array();
        foreach ($statuses as $key => $value) {
            $data[$value] = AriWebHelper::translateResValue('Label.AnonStatus.' . $key);
        } return $data;
    }

    function _bindControls($quiz) {
        $this->_tbxQuizName->setText(AriWebHelper::translateDbValue($quiz->QuizName));
        $this->_tbxTotalTime->setText($quiz->TotalTime);
        $this->_tbxPassedScore->setText($quiz->PassedScore);
        $this->_tbxQuestionCount->setText($quiz->QuestionCount);
        $this->_tbxQuestionTime->setText($quiz->QuestionTime);
        $this->_tbxAdminEmail->setText($quiz->AdminEmail);
        $this->_tbxLagTime->setText($quiz->LagTime);
        $this->_tbxAttemptCount->setText($quiz->AttemptCount);
        $this->_lbCategories->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbCategories->dataBind($this->_getCategoryList(), 'CategoryName', 'CategoryId');
        $this->_lbCategories->setSelectedValue(!empty($quiz->CategoryList) && count($quiz->CategoryList) > 0 ? $quiz->CategoryList[0]->CategoryId : null);
        $this->_lbQuestionOrderType->dataBind($this->_getQuestionOrderTypes());
        $this->_lbQuestionOrderType->setSelectedValue($quiz->QuestionOrderType);
        $this->_lbAnonymous->dataBind($this->_getAnonymousStatuses());
        $this->_lbAnonymous->setSelectedValue($quiz->Anonymous);
        $this->_lbFullStatisticsType->dataBind($this->_getFullStatisticsTypes());
        $this->_lbFullStatisticsType->setSelectedValue($quiz->FullStatistics);
        $this->_lbCss->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbCss->dataBind($this->_getCssTemplateList(), 'ShortDescription', 'FileId');
        $this->_lbCss->setSelectedValue($quiz->CssTemplateId);
        $this->_lbAccess->setEmptyRow(AriWebHelper::translateResValue('Label.Guest'), 0);
        $this->_lbAccess->dataBind($this->_getAccessTree(), 'text', 'value');
        $this->_lbAccess->setSelectedValue(is_array($quiz->AccessList) && count($quiz->AccessList) > 0 ? $quiz->AccessList[0]->value : 0);
        $quizTextList = AriConstantsManager::getVar('TextTemplates', AriQuizComponent::getCodeName());
        $quizTextTemplates = $this->_getQuizTextTemplateList($quiz->QuizId);
        $textTemplates = $this->_getTextTemplateList();
        $mailTemplates = $this->_getMailTemplateList();
        $this->_lbSucEmail->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbSucEmail->dataBind($mailTemplates, 'TemplateName', 'TextTemplateId');
        $this->_lbSucEmail->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['SuccessfulEmail'], 0));
        $this->_lbFailEmail->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbFailEmail->dataBind($mailTemplates, 'TemplateName', 'TextTemplateId');
        $this->_lbFailEmail->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['FailedEmail'], 0));
        $this->_lbSucPrint->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbSucPrint->dataBind($textTemplates, 'TemplateName', 'TemplateId');
        $this->_lbSucPrint->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['SuccessfulPrint'], 0));
        $this->_lbFailPrint->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbFailPrint->dataBind($textTemplates, 'TemplateName', 'TemplateId');
        $this->_lbFailPrint->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['FailedPrint'], 0));
        $this->_lbSuc->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbSuc->dataBind($textTemplates, 'TemplateName', 'TemplateId');
        $this->_lbSuc->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['Successful'], 0));
        $this->_lbFail->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbFail->dataBind($textTemplates, 'TemplateName', 'TemplateId');
        $this->_lbFail->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['Failed'], 0));
        $this->_lbAdminEmail->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
        $this->_lbAdminEmail->dataBind($mailTemplates, 'TemplateName', 'TextTemplateId');
        $this->_lbAdminEmail->setSelectedValue(AriUtils::getParam($quizTextTemplates, $quizTextList['AdminEmail'], 0));
        $this->_lbScale->setEmptyRow(AriWebHelper::translateResValue('Label.ChooseScale'), 0);
        $this->_lbScale->dataBind($this->_getScaleList(), 'ScaleName', 'ScaleId');
        $this->_lbScale->setSelectedValue($quiz->ResultScaleId, 0);
        $statusList = AriConstantsManager::getVar('Status', AriQuizControllerConstants::getClassName());
        $this->_chkStatus->setChecked(($quiz->Status & $statusList['Active']) > 0);
        $this->_chkParsePluginTag->setChecked($quiz->ParsePluginTag > 0);
        $this->_chkCanSkip->setChecked($quiz->CanSkip > 0);
        $this->_chkCanStop->setChecked($quiz->CanStop > 0);
        $this->_chkUseCalc->setChecked($quiz->UseCalculator > 0);
        $this->_chkRandom->setChecked($quiz->RandomQuestion > 0);
        $this->_chkShowCorrectAnswer->setChecked($quiz->ShowCorrectAnswer > 0);
        $this->_chkShowExplanation->setChecked($quiz->ShowExplanation > 0);
        $this->_edDescription->setText(AriWebHelper::translateDbValue($quiz->Description));
    }

    function _registerAjaxHandlers() {
        $this->_registerAjaxHandler('checkQuizName', 'ajaxCheckQuizName');
    }

    function ajaxCheckQuizName() {
        $name = AriRequest::getParam('name', '');
        $quizId = @intval(AriRequest::getParam('quizId', 0));
        $isUnique = $this->_quizController->call('isUniqueQuizName', $name, $quizId);
        AriResponse::sendJsonResponse($isUnique);
    }

}

;

;
?>