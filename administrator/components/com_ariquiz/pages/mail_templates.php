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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); require_once dirname(__FILE__) . '/base/mailTemplateList.base.php'; class mail_templatesAriPage extends mailTemplateListAriPage { function _init() {$codeName = AriQuizComponent::getCodeName(); $this->_titleResKey = 'Label.MailTemplates'; $this->_templateFormatter = 'YAHOO.ARISoft.Quiz.formatters.formatMailTemplate';$this->_mailTemplateGroup = AriConstantsManager::getVar('TemplateGroup.MailResults', $codeName);; $this->_persistanceKey = 'dtQuizResultMTemplates'; $this->_mailTemplateController = new AriMailTemplatesController(AriConstantsManager::getVar('MailTemplateTable', $codeName), AriConstantsManager::getVar('TextTemplateTable', $codeName)); parent::_init(); } } 

 ;

;
?>