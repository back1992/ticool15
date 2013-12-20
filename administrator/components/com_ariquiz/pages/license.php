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

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Controllers.LicenseController'); AriKernel::import('Security.License'); AriKernel::import('Web.Request');AriKernel::import('Web.Page.Specific.AdminPageBase'); class licenseAriPage extends AriAdminPageBase { var $_licenseController; function _init() { $this->_licenseController = new AriLicenseController(); parent::_init(); } function execute() {$licenseList = $this->_getLicenseList(); $this->addVar('licenseList', $licenseList); $this->setResTitle('Title.License'); parent::execute(); } function _getLicenseList() { global $option;$licenseList = $this->_licenseController->call('getLicenseList', $option); if (!empty($licenseList)) { foreach ($licenseList as $key => $value) { $licenseList[$key]->EndDate = ArisDate::formatDate($value->EndDate, '%Y-%b-%d');$licenseList[$key]->IsExpired = ($value->IsExpired >= 0); } } return $licenseList; } function _registerEventHandlers() { $this->_registerEventHandler('newLicense', 'clickNewLicense'); } function clickNewLicense($eventArgs) { global $option;$licenseKey = trim(AriRequest::getParam('tbxLicenseKey', '')); $domain = trim(AriRequest::getParam('tbxDomain', '')); if (empty($domain)) $domain = AriRequest::getCurrentDomain(true); else $domain = AriRequest::normalizeDomain($domain); $mid = '';if (!empty($licenseKey) && strlen($licenseKey) > 32) { $info = AriLicense::getKeyInfo($licenseKey, $option, $domain); if (!empty($info)) { $isExists = $this->_licenseController->call('isLicenseExists', $licenseKey); if (!$isExists) {$this->_licenseController->call('addLicense', $licenseKey, $domain, $option, ArisDate::getDbUTC($info['date'])); $mid = 'License.AddLicense'; } else { $mid = 'License.LicenseExists'; } } else { $mid = 'License.IncorrectLicense'; } }else { $mid = 'License.SpecifyKey'; } AriWebHelper::preCompleteAction($mid, array('task' => 'license')); } } ;

;
?>