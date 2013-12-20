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
    class AriCheckBoxWebControl extends AriWebControl {
        var $_checked = false;
        function __construct($id, $config = null) {
            parent::__construct($id, $config);
            if ($this->getStoreState()) {
                $value = AriRequest::getParam($this->getName());
                $this->setChecked(!empty($value));
            }
        }
        function getChecked() {
            return $this->_checked;
        }
        function setChecked($checked) {
            $this->_checked = $checked;
        }
        function setValue($value) {
            $this->setChecked(AriUtils::parseValueBySample($value, true));
        }
        function getValidateValue() {
            return $this->getChecked();
        }
        function render($attrs = null) {
            if (!$this->getVisible()) return '';
            $renderAttrs = array('type'=>'checkbox');
            if ($this->getChecked()) $renderAttrs['checked'] = 'true';
            $renderAttrs = array_merge($renderAttrs, $attrs);
            $attrsHtml = $this->_getAttributeHtml($renderAttrs);
            printf('<input %s />', $attrsHtml);
        }
    };
?>