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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Web.Page.Specific.AdminSecurePageBase'); AriKernel::import('Web.Controls.Data.MultiPageDataTable');AriKernel::import('Controllers.AriQuiz.QuestionController'); class qtemplate_listAriPage extends AriAdminSecurePageBase { var $_questionController; var $_persistanceKey = 'dtQTemplates'; function _init() {$this->_questionController = new AriQuizQuestionController(); } function execute() { $dataTable = $this->_createDataTable(); $this->addVar('dataTable', $dataTable); $this->setResTitle('Title.TemplateList'); parent::execute(); }function _createDataTable() { global $option; $dsUrl = 'index2.php?option=' . $option . '&task=' . $this->executionTask . '$ajax|getQTemplateList'; $columns = array(new AriDataTableControlColumn(array('key' => '', 'label' => AriWebHelper::translateResValue('Label.NumberPos'), 'formatter' => 'YAHOO.ARISoft.widgets.dataTable.formatters.formatPosition', 'className' => 'dtCenter dtColMin')),new AriDataTableControlColumn(array('key' => 'TemplateId', 'label' => '<input type="checkbox" class="adtCtrlCheckbox" />', 'formatter' => 'YAHOO.ARISoft.widgets.dataTable.formatters.formatCheckbox', 'className' => 'dtCenter dtColMin')),new AriDataTableControlColumn(array('key' => 'TemplateName', 'label' => AriWebHelper::translateResValue('Label.Template'), 'sortable' => true, 'formatter' => 'YAHOO.ARISoft.Quiz.formatters.formatQTemplate')),new AriDataTableControlColumn(array('key' => 'QuestionType', 'label' => AriWebHelper::translateResValue('Label.QuestionType'), 'sortable' => true, 'className' => 'dtCenter dtColTiny')), ); $dataTable = new AriMultiPageDataTableControl($this->_persistanceKey, $columns, array('dataUrl' => $dsUrl)); return $dataTable; } function _registerAjaxHandlers() { $this->_registerAjaxHandler('getQTemplateList', 'ajaxGetQTemplateList'); $this->_registerAjaxHandler('delete', 'ajaxDelete');} function ajaxDelete() { $result = ($this->_questionController->call('deleteQuestionTemplate', AriRequest::getParam('TemplateId', array())) && !$this->_isError()); AriResponse::sendJsonResponse($result); } function ajaxGetQTemplateList() {$filter = new AriDataFilter( array('startOffset' => 0, 'limit' => 10, 'sortField' => 'TemplateName', 'dir' => 'asc'), true, $this->_persistanceKey); $totalCnt = $this->_questionController->call('getQuestionTemplateCount', $filter);$filter->fixFilter($totalCnt); $questions = $this->_questionController->call('getQuestionTemplateList', $filter); $data = AriMultiPageDataTableControl::createDataInfo($questions, $filter, $totalCnt); AriResponse::sendJsonResponse($data); } } 

 ;

;
?>