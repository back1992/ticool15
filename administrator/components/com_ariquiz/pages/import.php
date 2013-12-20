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
**/

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Web.Page.Specific.AdminPageBase'); AriKernel::import('Data.Import.ImportController'); AriKernel::import('Security.LicenseUtils');AriKernel::import('Web.Controls.TextBox'); AriKernel::import('Web.Controls.Validators.RequiredValidator'); AriKernel::import('Controllers.AriQuiz.LMSImportController'); class importAriPage extends AriAdminPageBase { var $_tbxImportDir;var $_tbxLMSImportDir; var $VG_IMPORT_DIR = 'ImportDir'; var $VG_IMPORT_UPLOAD = 'ImportUpload'; var $VG_LMSIMPORT_DIR = 'LMSImportDir'; function execute() { $this->setResTitle('Title.ImportData'); $this->_bindControls(); parent::execute(); }function _createControls() { $this->_tbxImportDir =& new AriTextBoxWebControl('tbxImportDir'); $this->_tbxLMSImportDir =& new AriTextBoxWebControl('tbxLMSImportDir'); } function _createValidators() {new AriRequiredValidatorWebControl('arqvImportDir', array('controlToValidate' => 'tbxImportDir', 'errorMessageResourceKey' => 'Validator.ImportDirRequired', 'groups' => array($this->VG_IMPORT_DIR)));new AriRequiredValidatorWebControl('arqvLMSImportDir', array('controlToValidate' => 'tbxLMSImportDir', 'errorMessageResourceKey' => 'Validator.ImportDirRequired', 'groups' => array($this->VG_LMSIMPORT_DIR))); } function _bindControls() {global $mosConfig_absolute_path; $this->_tbxImportDir->setText($mosConfig_absolute_path); $this->_tbxLMSImportDir->setText($mosConfig_absolute_path); } function _registerEventHandlers() {$this->_registerEventHandler('uploadImport', 'clickUploadImport'); $this->_registerEventHandler('importFromDir', 'clickImportFromDir'); $this->_registerEventHandler('importLMSFromDir', 'clickImportLMSFromDir'); }function clickImportLMSFromDir($eventArgs) { $result = false; $exportFile = $this->_tbxLMSImportDir->getText(); if (@file_exists($exportFile) && @is_file($exportFile)) { global $my; $lmsImporter = new AriQuizLMSImportController();$result = $lmsImporter->call('import', $exportFile, $my->get('id')); } $this->setInfoMessage(AriWebHelper::translateResValue( $result ? 'Complete.DataImport' : 'Complete.DataImportFailed')); } function clickImportFromDir($eventArgs) {$result = false; $exportFile = $this->_tbxImportDir->getText(); if (@file_exists($exportFile) && @is_file($exportFile)) { $dataConfigFile = AriUtils::resolvePath('administrator/components/' . AriQuizComponent::getCodeName() . '/config/data.xml');$importController = new AriImportDataController($dataConfigFile); $result = $importController->call('import', $exportFile); } $this->setInfoMessage(AriWebHelper::translateResValue( $result ? 'Complete.DataImport' : 'Complete.DataImportFailed'));} function clickUploadImport($eventArgs) { $file = AriUtils::getFilteredParam($_FILES, 'importDataFile', null); $res = array(); $result = false; if (!empty($file) && $file['size'] > 0) { $fileName = $file['tmp_name'];if (@file_exists($fileName)) { $dataConfigFile = AriUtils::resolvePath('administrator/components/' . AriQuizComponent::getCodeName() . '/config/data.xml'); $importController = new AriImportDataController($dataConfigFile);$result = $importController->call('import', $fileName); } } $this->setInfoMessage(AriWebHelper::translateResValue( $result ? 'Complete.DataImport' : 'Complete.DataImportFailed')); } }

 ;

;
?>