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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Web.Page.Specific.LangListPageBase'); class lang_frontendAriPage extends AriLangListPageBase { function _init() {$codeName = AriGlobalPrefs::getOption(); $this->_fileGroup = AriConstantsManager::getVar('FileGroup.FrontendLang', $codeName); $this->_defaultFileKey = AriConstantsManager::getVar('Config.FrontendLang', $codeName); $this->_task = 'lang_frontend';$this->_addTask = 'flang_add'; $this->_dtId = 'dtFrontendLang'; parent::_init(); } } 

;

;
?>