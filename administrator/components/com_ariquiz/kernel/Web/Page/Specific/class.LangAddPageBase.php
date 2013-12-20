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
    AriKernel::import('Security.LicenseUtils');
    AriKernel::import('Web.Controls.TextBox');
    AriKernel::import('Web.Controls.Editor');
    AriKernel::import('Web.Controls.Validators.RequiredValidator');
    AriKernel::import('Controllers.FileController');
    AriKernel::import('String.String');
    class AriLangAddPageBase extends AriAdminSecurePageBase {
        var $_untitledGroup = 'Untitled';
        var $_savedIndex = array('ShortDescription');
        var $_fileController;
        var $_fileGroup;
        var $_task;
        var $_listTask;
        var $_tbxTemplateName;
        function _init() {
            global $option;
            $this->_fileController = new AriFileController(AriGlobalPrefs::getFileTable());
            parent::_init();
        }
        function execute() {
            $fileId = intval(AriRequest::getParam('fileId', 0));
            $file = $this->_getFile($fileId);
            $res = $this->_getResource($fileId);
            $groups = array_keys($res);
            sort($groups);
            $this->_bindControls($file);
            $this->addVar('res', $res);
            $this->addVar('groups', $groups);
            $this->addVar('fileId', $fileId);
            $this->addVar('currentTask', $this->_task);
            $this->setTitle(AriWebHelper::translateResValue('Title.BLangResource') .' : '.AriWebHelper::translateResValue($fileId ? 'Label.UpdateItem' : 'Label.AddItem'));
            parent::execute();
        }
        function _getResource($fileId) {
            $res = array();
            $nullValue = null;
            $cacheDir = AriGlobalPrefs::getCacheDir();
            $baseRes = ArisI18NHelper::parseXmlFromFile($cacheDir.$this->_fileGroup.'/en.xml', $nullValue);
            if ($fileId != 0) {
                $file = $this->_fileController->call('getFile', $fileId, $this->_fileGroup);
                $res = ArisI18NHelper::parseXmlFromString($file->Content, $nullValue);
                $res = ArisI18NHelper::mergeResources($baseRes, $res);
            } else {
                $res = $baseRes;
            }
            $groupRes = array($this->_untitledGroup=>array());
            foreach($res as $id=>$value) {
                $group = $this->_untitledGroup;
                $type = null;
                if (isset($value[ARI_I18N_ATTRS])) {
                    if (isset($value[ARI_I18N_ATTRS][ARI_I18N_ATTR_GROUP])) {
                        $group = $value[ARI_I18N_ATTRS][ARI_I18N_ATTR_GROUP];
                        if (!isset($groupRes[$group])) $groupRes[$group] = array();
                    }
                    if (isset($value[ARI_I18N_ATTRS]['type'])) {
                        $type = $value[ARI_I18N_ATTRS]['type'];
                    }
                }
                $groupRes[$group][] = array('id'=>$id, 'message'=>$value['message'], 'description'=>isset($value['description']) ? $value['description'] : '', 'type'=>$type);
            }
            if (count($groupRes[$this->_untitledGroup]) == 0) {
                unset($groupRes[$this->_untitledGroup]);
            }
            return $groupRes;
        }
        function _getFile($fileId) {
            $file = null;
            if ($fileId != 0) {
                $file = $this->_fileController->call('getFile', $fileId, $this->_fileGroup);
            } else {
                $file = AriEntityFactory::createInstance('AriFileEntity');
            }
            return $file;
        }
        function _getBaseRes() {
            $nullValue = null;
            $cacheDir = AriGlobalPrefs::getCacheDir();
            return ArisI18NHelper::parseXmlFromFile($cacheDir.$this->_fileGroup.'/en.xml', $nullValue);
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('save', 'clickSave');
            $this->_registerEventHandler('apply', 'clickApply');
            $this->_registerEventHandler('cancel', 'clickCancel');
        }
        function clickCancel($eventArgs) {
            AriWebHelper::cancelAction($this->_listTask);
        }
        function clickSave($eventArgs) {
            $template = $this->_saveFile();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.LangSave', array('task'=>$this->_listTask));
            }
        }
        function clickApply($eventArgs) {
            $file = $this->_saveFile();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.LangSave', array('task'=>$this->_task, 'fileId'=>$file->FileId, 'hidemainmenu'=>1));
            }
        }
        function _saveFile() {
            global $my, $mosConfig_absolute_path, $option;
            $baseRes = $this->_getBaseRes();
            $tbxResMessage = AriWebHelper::translateRequestValues('tbxResMessage');
            $tbxResDescr = AriWebHelper::translateRequestValues('tbxResDescr');
            if (get_magic_quotes_gpc()) {
                reset($tbxResMessage);
                reset($tbxResDescr);
                $tbxResMessage = AriString::stripslashes($tbxResMessage);
                $tbxResDescr = AriString::stripslashes($tbxResDescr);
            }
            $merge = ArisI18NHelper::mergeDataResource($baseRes, array('message'=>$tbxResMessage, 'description'=>$tbxResDescr), array('message', 'description'));
            $xml = ArisI18NHelper::createXmlFromData($merge);
            $xmlStr = ARI_I18N_TEMPLATE_XML.$xml->document->toString();
            $ownerId = $my->get('id');
            $reqFields = AriWebHelper::translateRequestValues('zTemplate');
            $fields = array();
            foreach($this->_savedIndex as $index) {
                $fields[$index] = isset($reqFields[$index]) ? $reqFields[$index] : '';
            }
            $fields['Group'] = $this->_fileGroup;
            $fields['Content'] = $xmlStr;
            $fields['Extension'] = 'xml';
            $file = $this->_fileController->call('saveFile', AriRequest::getParam('fileId', 0), $fields, $ownerId);
            if ($file) {
                $cacheDir = AriGlobalPrefs::getCacheDir();
                AriFileCache::recacheFile($cacheDir, $this->_fileGroup, $file->FileId, $file->Extension);
            }
            return $file;
        }
        function _createControls() {
            $this->_tbxTemplateName = &new AriTextBoxWebControl('tbxTemplateName', array('name'=>'zTemplate[ShortDescription]', 'maxLength'=>85));
        }
        function _createValidators() {
            new AriRequiredValidatorWebControl('arqvTemplateName', array('controlToValidate'=>'tbxTemplateName', 'errorMessageResourceKey'=>'Validator.NameRequired'));
        }
        function _bindControls($file) {
            $this->_tbxTemplateName->setText(AriWebHelper::translateDbValue($file->ShortDescription));
        }
    };
?>