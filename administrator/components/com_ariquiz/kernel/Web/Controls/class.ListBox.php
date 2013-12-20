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
    class AriListBoxWebControl extends AriWebControl {
        var $_dataSource = null;
        var $_valueMember;
        var $_textMember;
        var $_selectedValue;
        var $_emptyText = null;
        var $_emptyValue = null;
        function __construct($id, $config = null) {
            $this->extendConfig(array('translateText'=>true));
            parent::__construct($id, $config);
            if ($this->getStoreState()) $this->setSelectedValue(AriRequest::getParam($this->getName()));
        }
        function setEmptyRow($emptyText, $emptyValue = false) {
            $this->_emptyValue = ($emptyValue === false ? $emptyText : $emptyValue);
            $this->_emptyText = $emptyText;
        }
        function dataBind($dataSource, $textMember = null, $valueMember = null) {
            $this->_dataSource = $this->_createDataSource($dataSource, $textMember, $valueMember);
            $this->_valueMember = $valueMember;
            $this->_textMember = $textMember;
        }
        function getSelectedValue() {
            $ret_val = null;
            if (!empty($this->_dataSource) || $this->_emptyValue !== null) {
                if (isset($this->_dataSource[$this->_selectedValue])) {
                    $ret_val = $this->_selectedValue;
                } else if ($this->_selectedValue == $this->_emptyValue) {
                    $ret_val = $this->_emptyValue;
                }
            }
            return $ret_val;
        }
        function setSelectedValue($selectedValue) {
            $this->_selectedValue = $selectedValue;
        }
        function setValue($value) {
            $this->setSelectedValue($value);
        }
        function _createDataSource($dataSource, $textMember, $valueMember) {
            $retDataSource = array();
            if (empty($dataSource)) return $retDataSource;
            reset($dataSource);
            foreach($dataSource as $key=>$item) {
                $optionAttrs = array();
                $optPropName = 'OptionAttrs';
                if (is_array($item) && isset($item[$optPropName])) {
                    $optionAttrs = $item[$optPropName];
                } else if (is_object($item) && isset($item-> {
                    $optPropName
                })) {
                    $optionAttrs = $item-> {
                        $optPropName
                    };
                }
                $value = $key;
                if (!empty($valueMember)) {
                    if (is_array($item)) {
                        $value = $item[$valueMember];
                    } else if (is_object($item)) {
                        $value = $item->$valueMember;
                    }
                }
                $text = $item;
                if (!empty($textMember)) {
                    if (is_array($item)) {
                        $text = $item[$textMember];
                    } else if (is_object($item)) {
                        $text = $item->$textMember;
                    }
                }
                if ($this->getConfigValue('translateText')) $text = AriWebHelper::translateDbValue($text);
                $retDataSource[$value] = array('text'=>$text, 'optionAtrrs'=>$optionAttrs);
            }
            return $retDataSource;
        }
        function getValidateValue() {
            return $this->getSelectedValue();
        }
        function render($attrs = null) {
            if (!$this->getVisible()) return '';
            $attrsHtml = $this->_getAttributeHtml($attrs);
            $ddlOptionsHtml = '';
            $selValue = $this->getSelectedValue();
            if (!is_null($this->_emptyText)) {
                $emptyValue = !is_null($this->_emptyValue) ? $this->_emptyValue : $this->_emptyText;
                $selected = $emptyValue == $selValue;
                $ddlOptionsHtml.= sprintf('<option value="%s"%s>%s</option>', $emptyValue, $selected ? ' selected="selected"' : '', $this->_emptyText);
            }
            if ($this->_dataSource) {
                foreach($this->_dataSource as $key=>$value) {
                    $optAttrs = $value['optionAtrrs'];
                    if (!is_array($optAttrs)) $optAttrs = array();
                    $optAttrs['value'] = $key;
                    if ($key == $selValue) $optAttrs['selected'] = 'selected';
                    $ddlOptionsHtml.= sprintf('<option %s>%s</option>', $this->_getCustomAttributesHtml($optAttrs), $value['text']);
                }
            }
            printf('<select %s>%s</select>', $attrsHtml, $ddlOptionsHtml);
        }
    };
?>