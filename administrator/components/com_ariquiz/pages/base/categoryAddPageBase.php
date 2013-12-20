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
    AriKernel::import('Web.Controls.Editor');
    AriKernel::import('Web.Controls.TextBox');
    AriKernel::import('Web.Controls.Validators.RequiredValidator');
    AriKernel::import('Web.Controls.Validators.CustomValidator');
    class categoryAddAriPage extends AriAdminSecurePageBase {
        var $_categoryController;
        var $_tbxCategoryName;
        var $_edDescription;
        var $_categoryResKey;
        var $_entityName;
        var $_categoryListTask;
        var $_categoryTask;
        function execute() {
            $categoryId = intval(AriRequest::getParam('categoryId', 0));
            $category = $this->_getCategory($categoryId);
            $this->addVar('categoryId', $categoryId);
            $this->_bindControls($category);
            $this->addVar('categoryTask', $this->_categoryTask);
            $this->setTitle(AriWebHelper::translateResValue($this->_categoryResKey) .' : '.AriWebHelper::translateResValue($categoryId ? 'Label.UpdateItem' : 'Label.AddItem'));
            parent::execute();
        }
        function _getCategory($categoryId) {
            $category = null;
            if ($categoryId != 0) {
                $category = $this->_categoryController->call('getCategory', $categoryId);
            } else {
                $category = AriEntityFactory::createInstance($this->_entityName, AriGlobalPrefs::getEntityGroup());
            }
            return $category;
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('save', 'clickSave');
            $this->_registerEventHandler('apply', 'clickApply');
            $this->_registerEventHandler('cancel', 'clickCancel');
        }
        function clickCancel($eventArgs) {
            AriWebHelper::cancelAction($this->_categoryListTask);
        }
        function clickSave($eventArgs) {
            $category = $this->_saveCategory();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.CategorySave', array('task'=>$this->_categoryListTask));
            }
        }
        function clickApply($eventArgs) {
            $category = $this->_saveCategory();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.CategorySave', array('task'=>$this->_categoryTask, 'categoryId'=>$category->CategoryId, 'hidemainmenu'=>1));
            }
        }
        function _saveCategory() {
            global $my;
            $ownerId = $my->get('id');
            $fields = AriWebHelper::translateRequestValues('zCategory');
            return $this->_categoryController->call('saveCategory', AriRequest::getParam('categoryId', 0), $fields, $ownerId);
        }
        function _createControls() {
            $this->_tbxCategoryName = &new AriTextBoxWebControl('tbxCategoryName', array('name'=>'zCategory[CategoryName]', 'maxLength'=>85));
            $this->_edDescription = &new AriEditorWebControl('edDescription', array('name'=>'zCategory[Description]'));
        }
        function _createValidators() {
            new AriRequiredValidatorWebControl('arqvCategoryName', array('controlToValidate'=>'tbxCategoryName', 'errorMessageResourceKey'=>'Validator.NameRequired'));
            $validate = array(&$this, 'cvCategoryName');
            new AriCustomValidatorWebControl('acvQuizName', $validate, array('controlToValidate'=>'tbxCategoryName', 'clientValidateFunc'=>'YAHOO.ARISoft.page.categoryNameValidate', 'errorMessageResourceKey'=>'Validator.NameNotUnique'));
        }
        function cvCategoryName() {
            return true;
        }
        function _bindControls($category) {
            $this->_tbxCategoryName->setText(AriWebHelper::translateDbValue($category->CategoryName));
            $this->_edDescription->setText(AriWebHelper::translateDbValue($category->Description));
        }
    };
?>