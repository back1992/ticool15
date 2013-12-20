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
    AriKernel::import('Web.Page.Specific.AdminSecurePageBase');
    AriKernel::import('MailTemplates.MailTemplatesController');
    AriKernel::import('Web.Controls.Editor');
    AriKernel::import('Web.Controls.CheckBox');
    AriKernel::import('Web.Controls.ListBox');
    AriKernel::import('Web.Controls.TextBox');
    AriKernel::import('Web.Controls.Validators.RequiredValidator');
    AriKernel::import('Web.Controls.Validators.RangeValidator');
    AriKernel::import('Web.Controls.Validators.RegExValidator');
    AriKernel::import('Web.Controls.Validators.CustomValidator');
    class mailTemplateAriPage extends AriAdminSecurePageBase {
        var $_tbxTemplateName;
        var $_tbxSubject;
        var $_tbxFrom;
        var $_tbxFromName;
        var $_chkAllowHtml;
        var $_edTemplate;
        var $_mailTemplateList;
        var $_textTemplateController;
        var $_mailTemplateController;
        var $_mailTemplate;
        var $_mailTemplateGroup;
        var $_mailTemplateId;
        function _init() {
            $this->_textTemplateController = $this->_mailTemplateController->call('getTextTemplateController');
            parent::_init();
        }
        function execute() {
            $params = $this->_getParams();
            $this->_bindControls($this->_getMailTemplate());
            $this->addVar('params', $params);
            parent::execute();
        }
        function _getParams() {
            $params = $this->_textTemplateController->call('getParamsByGroup', AriConstantsManager::getVar('TemplateGroup.Results', AriQuizComponent::getCodeName()));
            return $params;
        }
        function _getMailTemplate() {
            if (is_null($this->_mailTemplate)) {
                $this->_mailTemplate = $this->_mailTemplateController->call('createMailTemplateInstance');
            }
            return $this->_mailTemplate;
        }
        function _createControls() {
            $this->_tbxTemplateName = &new AriTextBoxWebControl('tbxTemplateName', array('name'=>'zTemplate[TemplateName]'));
            $this->_tbxSubject = &new AriTextBoxWebControl('tbxSubject', array('name'=>'zMailTemplate[Subject]'));
            $this->_tbxFrom = &new AriTextBoxWebControl('tbxFrom', array('name'=>'zMailTemplate[From]'));
            $this->_tbxFromName = &new AriTextBoxWebControl('tbxFromName', array('name'=>'zMailTemplate[FromName]'));
            $this->_edTemplate = &new AriEditorWebControl('edTemplate', array('name'=>'zTemplate[Value]'));
            $this->_chkAllowHtml = &new AriCheckBoxWebControl('chkAllowHtml', array('name'=>'zMailTemplate[AllowHtml]'));
        }
        function _createValidators() {
            new AriRequiredValidatorWebControl('rvTemplateName', array('controlToValidate'=>'tbxTemplateName', 'errorMessageResourceKey'=>'Validator.NameRequired'));
            new AriRegExValidatorWebControl('arevFrom', '/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+$/i', array('controlToValidate'=>'tbxFrom', 'clientRegEx'=>'/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+$/i', 'errorMessageResourceKey'=>'Validator.EmailIncorrect'));
            $validate = array(&$this, 'cvTemplate');
            new AriCustomValidatorWebControl('acvTemplate', $validate, array('controlToValidate'=>'edTemplate', 'clientValidateFunc'=>'YAHOO.ARISoft.page.templateValidate', 'errorMessageResourceKey'=>'Validator.TextRequired'));
        }
        function cvTemplate() {
            return true;
        }
        function _bindControls($template) {
            $textTemplate = $template->TextTemplate;
            $this->_tbxTemplateName->setText(AriWebHelper::translateDbValue($textTemplate->TemplateName));
            $this->_tbxSubject->setText(AriWebHelper::translateDbValue($template->Subject));
            $this->_tbxFrom->setText(AriWebHelper::translateDbValue($template->From));
            $this->_tbxFromName->setText(AriWebHelper::translateDbValue($template->FromName));
            $this->_edTemplate->setText(AriWebHelper::translateDbValue($textTemplate->Value));
            $this->_chkAllowHtml->setChecked(!!($template->AllowHtml));
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('save', 'clickSave');
            $this->_registerEventHandler('apply', 'clickApply');
            $this->_registerEventHandler('cancel', 'clickCancel');
        }
        function clickCancel($eventArgs) {
            AriWebHelper::cancelAction($this->_mailTemplateList);
        }
        function clickSave($eventArgs) {
            $template = $this->_saveTemplate();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.TemplateSave', array('task'=>$this->_mailTemplateList));
            }
        }
        function clickApply($eventArgs) {
            $template = $this->_saveTemplate();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.TemplateSave', array('task'=>$this->executionTask, 'mailTemplateId'=>$template->MailTemplateId, 'hidemainmenu'=>1));
            }
        }
        function _saveTemplate() {
            global $my;
            $ownerId = $my->get('id');
            $fields = AriWebHelper::translateRequestValues('zMailTemplate');
            $templateFields = AriWebHelper::translateRequestValues('zTemplate');
            $fields['AllowHtml'] = 1;
            return $this->_mailTemplateController->call('saveTemplate', $this->_mailTemplateId, $fields, $templateFields, $this->_mailTemplateGroup, $ownerId);
        }
    };
?>