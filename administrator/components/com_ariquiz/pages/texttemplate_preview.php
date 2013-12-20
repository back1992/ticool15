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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Web.Page.Specific.AdminSecurePageBase'); AriKernel::import('Controllers.AriQuiz.ResultController');AriKernel::import('TextTemplates.TextTemplateController'); AriKernel::import('Utils.Utils'); AriKernel::import('Date.Date'); AriKernel::import('Components.AriQuiz.Util'); AriKernel::import('Components.AriQuiz.CDHelper');class texttemplate_previewAriPage extends AriAdminSecurePageBase { function _init() { $this->isSimple = true; parent::_init(); } function execute() { $sid = AriRequest::getParam('sid', 0); $templateId = AriRequest::getParam('templateId', 0);if (!empty($templateId)) { $templateController = new AriTextTemplateController(AriConstantsManager::getVar('TextTemplateTable', AriQuizComponent::getCodeName())); $template = $templateController->call('getTemplate', $templateId);if ($template && $template->TemplateId) { $resultController = new AriQuizResultController(); $result = $resultController->call('getFormattedFinishedResultById', $sid);$cssFile = AriQuizUtils::getCssFile(isset($result['CssTemplateId']) ? $result['CssTemplateId'] : null); echo '<link rel="stylesheet" type="text/css" href="'. $cssFile . '" />'; if (AriQuizCDHelper::hasCDKey('CD_f7767f66ef91e0eac408f94055d0f975') &&strpos($template->Value, 'StatByCategories') !== false) { $result['StatByCategories'] = AriQuizUtils::getStatByCategoriesHtml($resultController->call('getFinishedInfoByCategory', $result['StatisticsInfoId'])); }$resText = $template->parse($result); AriWebHelper::displayDbValue($resText, false); } } parent::execute(); } } 
 ;

;
?>