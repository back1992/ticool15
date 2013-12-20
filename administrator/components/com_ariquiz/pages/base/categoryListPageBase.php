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
    AriKernel::import('Web.Controls.Data.MultiPageDataTable');
    class categoryListAriPage extends AriAdminSecurePageBase {
        var $_ajaxDTHandler;
        var $_persistanceKey;
        var $_categoryController;
        var $_categoryListPage;
        var $_titleResKey;
        var $_categoryFormatter;
        function execute() {
            $dataTable = $this->_createDataTable();
            $this->setResTitle($this->_titleResKey);
            $this->addVar('dataTable', $dataTable);
            parent::execute();
        }
        function _createDataTable() {
            global $option;
            $dsUrl = 'index2.php?option='.$option.'&task='.$this->_ajaxDTHandler;
            $columns = array(new AriDataTableControlColumn(array('key'=>'', 'label'=>AriWebHelper::translateResValue('Label.NumberPos'), 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatPosition', 'className'=>'dtCenter dtColMin')), new AriDataTableControlColumn(array('key'=>'CategoryId', 'label'=>'<input type="checkbox" class="adtCtrlCheckbox" />', 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatCheckbox', 'className'=>'dtCenter dtColMin')), new AriDataTableControlColumn(array('key'=>'CategoryId', 'label'=>AriWebHelper::translateResValue('Label.ID'), 'className'=>'dtCenter dtColMin')), new AriDataTableControlColumn(array('key'=>'CategoryName', 'label'=>AriWebHelper::translateResValue('Label.Name'), 'sortable'=>true, 'formatter'=>$this->_categoryFormatter)),);
            $dataTable = new AriMultiPageDataTableControl($this->_persistanceKey, $columns, array('dataUrl'=>$dsUrl));
            return $dataTable;
        }
        function _registerAjaxHandlers() {
            $this->_registerAjaxHandler('delete', 'ajaxDelete');
        }
        function ajaxDelete($eventArgs) {
            $result = ($this->_categoryController->call('deleteCategory', AriRequest::getParam('CategoryId', 0)) && !$this->_isError());
            AriResponse::sendJsonResponse($result);
        }
    };
?>