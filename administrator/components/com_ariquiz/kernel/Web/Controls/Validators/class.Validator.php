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
    class AriValidatorWebControl extends AriObject {
        var $_isValid = true;
        var $_id;
        var $_configProps = array('enabled'=>true, 'errorMessageResourceKey'=>null, 'errorMessage'=>null, 'controlToValidate'=>null, 'enableJsValidation'=>true, 'groups'=>array());
        function __construct($id, $config = null) {
            $this->setId($id);
            $this->bindConfig($config);
            $pageHelper = &AriPageHelper::getInstance();
            $currentPage = &$pageHelper->getCurrentPage();
            $currentPage->addValidator($this);
        }
        function inGroup($group) {
            $groups = $this->getGroups();
            $hasGroups = (is_array($groups) && count($groups) >0);
            return (!$hasGroups && empty($group)) || ($hasGroups && in_array($group, $groups));
        }
        function getGroups() {
            return $this->getConfigValue('groups');
        }
        function setGroups($groups) {
            $this->setConfigValue('groups', $groups);
        }
        function setErrorMessage($errorMessage) {
            $this->setConfigValue('errorMessage', $errorMessage);
        }
        function getErrorMessage() {
            $resId = $this->getErrorMessageResourceKey();
            return ($resId != null) ? AriWebHelper::translateResValue($resId) : $this->getConfigValue('errorMessage');;
        }
        function setErrorMessageResourceKey($errorMessageResourceKey) {
            $this->setConfigValue('errorMessageResourceKey', $errorMessageResourceKey);
        }
        function getErrorMessageResourceKey() {
            return $this->getConfigValue('errorMessageResourceKey');
        }
        function getIsValid() {
            return $this->_isValid;
        }
        function setIsValid($isValid) {
            $this->_isValid = $isValid;
        }
        function getId() {
            return $this->_id;
        }
        function setId($id) {
            $this->_id = $id;
        }
        function getEnabled() {
            return $this->getConfigValue('enabled');
        }
        function setEnabled($enabled) {
            $this->setConfigValue('enabled', $enabled);
        }
        function setEnableJsValidation($enabled) {
            $this->setConfigValue('enableJsValidation', $enabled);
        }
        function getEnableJsValidation() {
            return $this->getConfigValue('enableJsValidation');
        }
        function getControlToValidateId() {
            return $this->getConfigValue('controlToValidate');
        }
        function &getControlToValidate() {
            $control = null;
            $ctrlId = $this->getConfigValue('controlToValidate');
            if ($ctrlId) {
                $pageHelper = &AriPageHelper::getInstance();
                $currentPage = &$pageHelper->getCurrentPage();
                $control = &$currentPage->getControl($ctrlId);
            }
            return $control;
        }
        function setControlToValidate($ctrlId) {
            $this->setConfigValue('controlToValidate', $ctrlId);
        }
        function validate() {
            return true;
        }
        function renderJsValidator() {
            if (!$this->getEnableJsValidation()) return;
            $this->_renderJsValidator();
        }
        function _renderJsValidator() {
        }
    };
?>