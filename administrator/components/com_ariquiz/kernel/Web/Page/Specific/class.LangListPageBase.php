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
    AriKernel::import('Web.Page.Specific.AdminPageBase');
    AriKernel::import('Security.LicenseUtils');
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Config.ConfigWrapper');
    AriKernel::import('Web.Controls.Data.MultiPageDataTable');
    class AriLangListPageBase extends AriAdminPageBase {
        var $_fileController;
        var $_fileGroup;
        var $_defaultFileKey;
        var $_task;
        var $_addTask;
        var $_savedIndex = array('ShortDescription');
        var $_dtId;
        function _init() {
            $this->_fileController = new AriFileController(AriGlobalPrefs::getFileTable());
            parent::_init();
        }
        function execute() {
            $config = AriConfigWrapper::getConfig();
            $this->setResTitle('Title.LangList');
            $defaultLang = isset($config[$this->_defaultFileKey]) ? $config[$this->_defaultFileKey] : -1;
            $dataTable = $this->_createDataTable($defaultLang);
            $this->addVar('currentTask', $this->_task);
            $this->addVar('addTask', $this->_addTask);
            $this->addVar('dataTable', $dataTable);
            parent::execute();
        }
        function _createDataTable($defaultLang) {
            global $option;
            $dsUrl = 'index2.php?option='.$option.'&task='.$this->executionTask.'$ajax|getLangList';
            $columns = array(new AriDataTableControlColumn(array('key'=>'FileId', 'label'=>'<input type="checkbox" class="adtCtrlCheckbox" />', 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatCheckbox', 'className'=>'dtCenter dtColMin')), new AriDataTableControlColumn(array('key'=>'ShortDescription', 'label'=>AriWebHelper::translateResValue('Label.Name'), 'sortable'=>true)), new AriDataTableControlColumn(array('key'=>'DefFileId', 'label'=>AriWebHelper::translateResValue('Label.Default'), 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatRadioButton', 'className'=>'dtCenter dtColMin')));
            $dataTable = new AriMultiPageDataTableControl($this->_dtId, $columns, array('dataUrl'=>$dsUrl));
            $objParams = sprintf('{name: "%s", selected: %d}', 'DefFileId', $defaultLang);
            $dataTable->subscribe('initEvent', 'YAHOO.ARISoft.widgets.dataTable.radiobuttonHelper.setSelected', $objParams);
            $dataTable->subscribe('renderEvent', 'YAHOO.ARISoft.widgets.dataTable.radiobuttonHelper.setSelected', $objParams);
            return $dataTable;
        }
        function _registerAjaxHandlers() {
            $this->_registerAjaxHandler('getLangList', 'ajaxGetLangList');
            $this->_registerAjaxHandler('delete', 'ajaxDelete');
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('export', 'clickExport');
            $this->_registerEventHandler('import', 'clickImport');
            $this->_registerEventHandler('default', 'clickDefault');
        }
        function ajaxGetLangList() {
            $filter = new AriDataFilter(array('startOffset'=>0, 'limit'=>10, 'sortField'=>'ShortDescription', 'dir'=>'asc'), true, $this->_dtId);
            $fileController = new AriFileController(AriGlobalPrefs::getFileTable());
            $totalCnt = $fileController->call('getFileCount', $this->_fileGroup, null, $filter);
            $filter->fixFilter($totalCnt);
            $templates = $fileController->call('getFileList', $this->_fileGroup, null, false, $filter);
            $this->_extendLangTemplates($templates);
            $data = AriMultiPageDataTableControl::createDataInfo($templates, $filter, $totalCnt);
            AriResponse::sendJsonResponse($data);
        }
        function clickDefault($eventArgs) {
            $rbLangDefault = intval(AriRequest::getParam('DefFileId', 0));
            if ($rbLangDefault>0) {
                AriConfigWrapper::setConfigValue($this->_defaultFileKey, $rbLangDefault);
            }
            AriWebHelper::preCompleteAction('Complete.SetLangDefault', array('task'=>$this->_task));
        }
        function ajaxDelete() {
            $fileIdList = AriRequest::getParam('FileId', array());
            $rbLangDefault = intval(AriRequest::getParam('rbLangDefault', 0));
            if (in_array($rbLangDefault, $fileIdList)) {
                AriConfigWrapper::removeConfigKey($this->_defaultFileKey);
            }
            $result = ($this->_fileController->call('deleteFile', $fileIdList, $this->_fileGroup) && !$this->_isError());
            AriResponse::sendJsonResponse($result);
        }
        function _extendLangTemplates(&$templates) {
            if (empty($templates)) return;
            global $option;
            $cnt = count($templates);
            for ($i = 0;$i<$cnt;$i++) {
                $dataItem = &$templates[$i];
                $dataItem->DefFileId = $dataItem->FileId;
                $dataItem->ShortDescription = sprintf('<a href="index2.php?option=%s&hidemainmenu=1&task=%s&fileId=%d">%s</a>', $option, $this->_addTask, $dataItem->FileId, $dataItem->ShortDescription);
            }
        }
        function clickImport($eventArgs) {
            $file = $this->_saveFile();
            if ($file) {
                AriWebHelper::preCompleteAction('Complete.FileImport', array('task'=>$this->_task));
            } else {
                AriWebHelper::preCompleteAction('Validator.FileIncorrectFormat', array('task'=>$this->_task));
            }
        }
        function clickExport($eventArgs) {
            $fileId = AriRequest::getParam('FileId', array());
            if (is_array($fileId)) {
                $fileId = count($fileId) >0 ? $fileId[0] : 0;
            }
            $fileId = intval($fileId);
            if ($fileId>0) {
                $file = $this->_fileController->call('getFile', $fileId, $this->_fileGroup);
                if ($file && $file->Content) {
                    AriResponse::sendContentAsAttach($file->Content, 'lang.xml');
                }
            }
        }
        function _saveFile() {
            global $my, $mosConfig_absolute_path, $option;
            $file = AriUtils::getFilteredParam($_FILES, 'fileLang', null);
            $res = array();
            if (!empty($file) && $file['size']>0) {
                $fileName = $file['tmp_name'];
                if (file_exists($fileName)) {
                    $handle = fopen($fileName, "rb");
                    $content = fread($handle, filesize($fileName));
                    fclose($handle);
                    $res = array();
                    $nullValue = null;
                    $cacheDir = AriGlobalPrefs::getCacheDir();
                    $baseRes = ArisI18NHelper::parseXmlFromFile($cacheDir.$this->_fileGroup.'/en.xml', $nullValue);
                    $res = ArisI18NHelper::parseXmlFromString($content, $nullValue);
                    if (!empty($res)) {
                        $res = ArisI18NHelper::mergeResources($baseRes, $res);
                    }
                }
            }
            if (empty($res)) return null;
            $xml = ArisI18NHelper::createXmlFromData($res);
            $xmlStr = ARI_I18N_TEMPLATE_XML.$xml->document->toString();
            $ownerId = $my->get('id');
            $reqFields = AriWebHelper::translateRequestValues('zLang');
            $fields = array();
            foreach($this->_savedIndex as $index) {
                $fields[$index] = isset($reqFields[$index]) ? $reqFields[$index] : '';
            }
            $fields['Group'] = $this->_fileGroup;
            $fields['Content'] = $xmlStr;
            $fields['Extension'] = 'xml';
            $file = $this->_fileController->call('saveFile', 0, $fields, $ownerId);
            if ($file) {
                $cacheDir = AriGlobalPrefs::getCacheDir();
                AriFileCache::recacheFile($cacheDir, $this->_fileGroup, $file->FileId, $file->Extension);
            }
            return $file;
        }
    };
?>