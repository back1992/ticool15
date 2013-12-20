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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); require_once dirname(__FILE__) . '/base/resultScale.base.php'; class resultscale_updateAriPage extends resultScaleAriPage { function execute() {$this->setTitle( AriWebHelper::translateResValue('Title.ResultScale') . ' : ' . AriWebHelper::translateResValue('Label.UpdateItem')); $scaleId = $this->_getScaleId(); $this->addVar('scaleId', $scaleId); parent::execute(); } function _getScale(){ if (is_null($this->_scale)) { $this->_scale = $this->_scaleController->call('getScale', $this->_getScaleId()); } return $this->_scale; } function _getScaleId() { return @intval(AriRequest::getParam('scaleId', 0), 10); } } 

 ;

;
?>