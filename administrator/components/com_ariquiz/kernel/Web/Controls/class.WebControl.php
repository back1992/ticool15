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
    class AriWebControl extends AriObject {
        var $_id;
        var $_attributes = array();
        var $_configProps = array('name'=>null, 'storeState'=>true, 'enabled'=>true, 'visible'=>true, 'cssClass'=>null, 'attributes'=>array());
        function __construct($id, $config = null) {
            $this->setId($id);
            $this->bindConfig($config);
            $pageHelper = &AriPageHelper::getInstance();
            $currentPage = &$pageHelper->getCurrentPage();
            $currentPage->addControl($this);
        }
        function getId() {
            return $this->_id;
        }
        function setId($id) {
            $this->_id = $id;
        }
        function getName() {
            $name = $this->getConfigValue('name');
            return !empty($name) ? $name : str_replace('.', '_', $this->getId());
        }
        function setName($name) {
            $this->setConfigValue('name', $name);
        }
        function getStoreState() {
            return $this->getConfigValue('storeState');
        }
        function setStoreState($storeState) {
            $this->setConfigValue('storeState', $storeState);
        }
        function getEnabled() {
            return $this->getConfigValue('enabled');
        }
        function setEnabled($enabled) {
            $this->setConfigValue('enabled', $enabled);
        }
        function getVisible() {
            return $this->getConfigValue('visible');
        }
        function setVisible($visible) {
            $this->setConfigValue('visible', $visible);
        }
        function getCssClass() {
            return $this->getConfigValue('cssClass');
        }
        function setCssClass($cssClass) {
            $this->setConfigValue('cssClass', $cssClass);
        }
        function addAttribute($name, $value) {
            $this->_attributes[$name] = $value;
        }
        function removeAttribute($name) {
            unset($this->_attributes[$name]);
        }
        function render($attrs = null) {
        }
        function getValidateValue() {
            return null;
        }
        function setValue($value) {
        }
        function _getAttributeHtml($overrideAttrs = null) {
            $attrs = $this->_attributes;
            if (is_array($overrideAttrs)) {
                foreach($overrideAttrs as $key=>$value) {
                    $attrs[$key] = $value;
                }
            }
            $attrs['id'] = $this->getId();
            $attrs['name'] = $this->getName();
            $class = $this->getCssClass();
            if (!empty($class)) {
                $attrs['class'] = $this->getCssClass();
            }
            if (!$this->getEnabled()) $attrs['disabled'] = 'disabled';
            $attrsHtml = '';
            foreach($attrs as $key=>$value) {
                if (is_null($value)) continue;
                $attrsHtml.= sprintf(' %s="%s" ', $key, $value);
            }
            return $attrsHtml;
        }
        function _getCustomAttributesHtml($attrs) {
            $attrsHtml = '';
            if (is_array($attrs)) {
                foreach($attrs as $key=>$value) {
                    if (is_null($value)) continue;
                    $attrsHtml.= sprintf(' %s="%s" ', $key, $value);
                }
            }
            return $attrsHtml;
        }
    };
?>