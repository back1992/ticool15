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
    AriKernel::import('Web.Controls.WebControl');
    class AriTextBoxWebControlConstants extends AriClassConstants {
        var $Type = array('Single'=>0, 'Multi'=>1, 'Password'=>2);
        function getClassName() {
            return strtolower('AriTextBoxWebControlConstants');
        }
    }
    new AriTextBoxWebControlConstants();
    class AriTextBoxWebControl extends AriWebControl {
        var $_text;
        function __construct($id, $config = null) {
            $constKey = AriTextBoxWebControlConstants::getClassName();
            $this->_configProps = array_merge($this->_configProps, array('type'=>AriConstantsManager::getVar('Type.Single', $constKey), 'trimValue'=>true, 'maxLength'=>null));
            parent::__construct($id, $config);
            if ($this->getStoreState()) $this->setText(AriRequest::getParam($this->getName()));
        }
        function setValue($value) {
            $this->setText($value);
        }
        function setText($text) {
            $this->_text = $text;
        }
        function getText() {
            $text = $this->isTrimValue() ? trim($this->_text) : $this->_text;
            $maxLength = $this->getMaxLength();
            if (!is_null($maxLength)) $text = substr($text, 0, $maxLength);
            return $text;
        }
        function getMaxLength() {
            return $this->getConfigValue('maxLength');
        }
        function setMaxLength($maxLength) {
            $this->setConfigValue('maxLength', $maxLength);
        }
        function getType() {
            return $this->getConfigValue('type');
        }
        function setType($type) {
            $this->setConfigValue('type', $type);
        }
        function isTrimValue() {
            return $this->getConfigValue('trimValue');
        }
        function setIsTrimValue($isTrimValue) {
            $this->setConfigValue('trimValue', $isTrimValue ? true : false);
        }
        function getValidateValue() {
            return $this->getText();
        }
        function render($attrs = null) {
            if (!$this->getVisible()) return '';
            $constKey = AriTextBoxWebControlConstants::getClassName();
            $types = AriConstantsManager::getVar('Type', $constKey);
            $type = $this->getType();
            switch ($type) {
                case $types['Multi']:
                    $this->_renderTextArea($attrs);
                break;
                case $types['Password']:
                    $this->_renderInput('password', $attrs);
                break;
                default:
                    $this->_renderInput('text', $attrs);
                break;
            }
        }
        function _renderInput($type, $attrs) {
            $renderAttrs = array('type'=>$type, 'value'=>$this->getText(), 'maxlength'=>$this->getMaxLength());
            $renderAttrs = array_merge($renderAttrs, $attrs);
            $attrsHtml = $this->_getAttributeHtml($renderAttrs);
            printf('<input %s />', $attrsHtml);
        }
        function _renderTextArea($attrs) {
            $renderAttrs = array('maxlength'=>$this->getMaxLength());
            $renderAttrs = array_merge($renderAttrs, $attrs);
            $attrsHtml = $this->_getAttributeHtml($renderAttrs);
            printf('<textarea %s>%s</textarea>', $attrsHtml, $this->getText());
        }
    };
?>