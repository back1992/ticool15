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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Web.Page.Specific.AdminSecurePageBase'); AriKernel::import('Web.Controls.Data.MultiPageDataTable');AriKernel::import('Controllers.AriQuiz.ResultScaleController'); class resultscale_listAriPage extends AriAdminSecurePageBase { var $_scaleController; function _init() { $this->_scaleController = new AriQuizResultScaleController();parent::_init(); } function execute() { $dataTable = $this->_createDataTable(); $this->addVar('dataTable', $dataTable); $this->setResTitle('Title.ResultScaleList'); parent::execute(); } function _getPersistanceKey() {return 'dtResultScaleList'; } function _createDataTable() { global $option; $dsUrl = 'index2.php?option=' . $option . '&task=' . $this->executionTask . '$ajax|getScaleList'; $columns = array(new AriDataTableControlColumn(array('key' => '', 'label' => AriWebHelper::translateResValue('Label.NumberPos'), 'formatter' => 'YAHOO.ARISoft.widgets.dataTable.formatters.formatPosition', 'className' => 'dtCenter dtColMin')),new AriDataTableControlColumn(array('key' => 'ScaleId', 'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 'formatter' => 'YAHOO.ARISoft.widgets.dataTable.formatters.formatCheckbox', 'className' => 'dtCenter dtColMin')),new AriDataTableControlColumn(array('key' => 'ScaleName', 'label' => AriWebHelper::translateResValue('Label.Name'), 'sortable' => true, 'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatScale')) ); $dataTable = new AriMultiPageDataTableControl($this->_getPersistanceKey(), $columns, array('dataUrl' => $dsUrl)); return $dataTable; } function _registerAjaxHandlers() { $this->_registerAjaxHandler('getScaleList', 'ajaxGetScaleList'); $this->_registerAjaxHandler('delete', 'ajaxDelete'); }function ajaxGetScaleList() { $filter = new AriDataFilter( array('startOffset' => 0, 'limit' => 10, 'sortField' => 'ScaleName', 'dir' => 'asc'), true, $this->_getPersistanceKey());$totalCnt = $this->_scaleController->call('getScaleCount', $filter); $filter->fixFilter($totalCnt); $scales = $this->_scaleController->call('getScaleList', $filter); $data = AriMultiPageDataTableControl::createDataInfo($scales, $filter, $totalCnt);AriResponse::sendJsonResponse($data); } function ajaxDelete() { $result = ($this->_scaleController->call('deleteScale', AriRequest::getParam('ScaleId', 0)) && !$this->_isError()); AriResponse::sendJsonResponse($result); } } 
 ;

;
?>