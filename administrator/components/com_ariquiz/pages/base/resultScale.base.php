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
    AriKernel::import('Controllers.AriQuiz.ResultScaleController');
    AriKernel::import('Web.Controls.TextBox');
    AriKernel::import('Web.Controls.ListBox');
    AriKernel::import('Web.Controls.Validators.RequiredValidator');
    AriKernel::import('Web.Controls.Validators.CustomValidator');
    AriKernel::import('Web.Controls.Advanced.MultiplierControls');
    AriKernel::import('TextTemplates.TextTemplateController');
    AriKernel::import('MailTemplates.MailTemplatesController');
    class resultScaleAriPage extends AriAdminSecurePageBase {
        var $_scaleController;
        var $_tbxScaleName;
        var $_lbEmailTemplate;
        var $_lbPrintTemplate;
        var $_lbTextTemplate;
        var $_scale;
        var $_updateTask;
        function _init() {
            $this->_scaleController = new AriQuizResultScaleController();
            parent::_init();
        }
        function _createControls() {
            $quizTextList = AriConstantsManager::getVar('TextTemplates', AriQuizComponent::getCodeName());
            $this->_tbxScaleName = &new AriTextBoxWebControl('tbxScaleName', array('name'=>'zTemplate[ScaleName]'));
            $this->_lbEmailTemplate = &new AriListBoxWebControl('lbEmailTemplate');
            $this->_lbPrintTemplate = &new AriListBoxWebControl('lbPrintTemplate');
            $this->_lbTextTemplate = &new AriListBoxWebControl('lbTextTemplate');
        }
        function _getScale() {
            if (is_null($this->_scale)) {
                $this->_scale = $this->_scaleController->call('createScaleInstance');
            }
            return $this->_scale;
        }
        function _getScaleId() {
            return 0;
        }
        function _createValidators() {
            new AriRequiredValidatorWebControl('rvScaleName', array('controlToValidate'=>'tbxScaleName', 'errorMessageResourceKey'=>'Validator.NameRequired'));
            $validate = array(&$this, 'cvPoints');
            new AriCustomValidatorWebControl('acvPoints', $validate, array('clientValidateFunc'=>'YAHOO.ARISoft.page.pointsValidate', 'errorMessageResourceKey'=>'Validator.TextRequired'));
        }
        function cvPoints() {
            return true;
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('save', 'clickSave');
            $this->_registerEventHandler('apply', 'clickApply');
            $this->_registerEventHandler('cancel', 'clickCancel');
        }
        function _bindControls($scale) {
            $quizTextList = AriConstantsManager::getVar('TextTemplates', AriQuizComponent::getCodeName());
            $textTemplates = $this->_getTextTemplateList();
            $mailTemplates = $this->_getMailTemplateList();
            $this->_tbxScaleName->setText(AriWebHelper::translateDbValue($scale->ScaleName));
            $this->_lbEmailTemplate->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
            $this->_lbEmailTemplate->dataBind($mailTemplates, 'TemplateName', 'TextTemplateId');
            $this->_lbPrintTemplate->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
            $this->_lbPrintTemplate->dataBind($textTemplates, 'TemplateName', 'TemplateId');
            $this->_lbTextTemplate->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
            $this->_lbTextTemplate->dataBind($textTemplates, 'TemplateName', 'TemplateId');
        }
        function _getTextTemplateList() {
            $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName()));
            $templateList = $templateController->call('getTemplateList', AriConstantsManager::getVar('TemplateGroup.Results', AriQuizComponent::getCodeName()));
            return $templateList;
        }
        function _getMailTemplateList() {
            $codeName = AriQuizComponent::getCodeName();
            $mailTemplateController = new AriMailTemplatesController(AriConstantsManager::getVar('MailTemplateTable', $codeName), AriConstantsManager::getVar('TextTemplateTable', $codeName));
            $templateList = $mailTemplateController->call('getTemplateList', null, AriConstantsManager::getVar('TemplateGroup.MailResults', $codeName));
            return $templateList;
        }
        function execute() {
            $scale = $this->_getScale();
            $scaleItemData = $this->_getScaleItemsData();
            $this->addVar('scaleItemData', $scaleItemData);
            $this->_bindControls($scale);
            parent::execute();
        }
        function clickCancel($eventArgs) {
            AriWebHelper::cancelAction('resultscale_list');
        }
        function clickSave($eventArgs) {
            $scale = $this->_saveScale();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.ScaleSave', array('task'=>'resultscale_list'));
            }
        }
        function clickApply($eventArgs) {
            $scale = $this->_saveScale();
            if (!$this->_isError()) {
                $updateTask = $this->_updateTask ? $this->_updateTask : $this->executionTask;
                AriWebHelper::preCompleteAction('Complete.ScaleSave', array('task'=>$updateTask, 'scaleId'=>$scale->ScaleId, 'hidemainmenu'=>1));
            }
        }
        function _saveScale() {
            global $my;
            $ownerId = $my->get('id');
            $fields = AriWebHelper::translateRequestValues('zTemplate');
            $subFields = WebControls_MultiplierControls::getData('tblScaleContainer', array('tbxStartPoint', 'tbxEndPoint', $this->_lbEmailTemplate->getId(), $this->_lbPrintTemplate->getId(), $this->_lbTextTemplate->getId()), null, true);
            return $this->_scaleController->call('saveScale', $this->_getScaleId(), $fields, $subFields, $ownerId);
        }
        function _getScaleItemsData() {
            $scale = $this->_getScale();
            if (!$scale || !is_array($scale->ScaleItems) || count($scale->ScaleItems) <1) return null;
            $data = array();
            foreach($scale->ScaleItems as $scaleItem) {
                $data[] = array('tbxStartPoint'=>$scaleItem->BeginPoint, 'tbxEndPoint'=>$scaleItem->EndPoint, $this->_lbEmailTemplate->getId() =>$scaleItem->MailTemplateId, $this->_lbPrintTemplate->getId() =>$scaleItem->PrintTemplateId, $this->_lbTextTemplate->getId() =>$scaleItem->TextTemplateId);
            }
            return $data;
        }
    };
?>