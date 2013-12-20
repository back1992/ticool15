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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); require_once dirname(__FILE__) . '/base/categoryListPageBase.php'; AriKernel::import('Controllers.AriQuiz.CategoryController');class category_listAriPage extends categoryListAriPage { function _init() { parent::_init(); $this->_ajaxDTHandler = $this->executionTask . '$ajax|getCategoryList'; $this->_persistanceKey = 'dtCategories';$this->_categoryController = new AriQuizCategoryController(); $this->_categoryListPage = 'category_list'; $this->_titleResKey = 'Title.CategoryList'; $this->_categoryFormatter = 'YAHOO.ARISoft.Quiz.formatters.formatCategory'; }function _registerAjaxHandlers() { $this->_registerAjaxHandler('getCategoryList', 'ajaxGetCategoryList'); parent::_registerAjaxHandlers(); } function ajaxGetCategoryList() { $filter = new AriDataFilter(array('startOffset' => 0, 'limit' => 10, 'sortField' => 'CategoryName', 'dir' => 'asc'), true, $this->_persistanceKey); $totalCnt = $this->_categoryController->call('getCategoryCount', $filter); $filter->fixFilter($totalCnt);$categories = $this->_categoryController->call('getCategoryList', $filter); $data = AriMultiPageDataTableControl::createDataInfo($categories, $filter, $totalCnt); AriResponse::sendJsonResponse($data); } } 

 ;;
?>