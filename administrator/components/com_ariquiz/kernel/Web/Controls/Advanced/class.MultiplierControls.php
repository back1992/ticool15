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
    AriKernel::import('Web.JSON.JSON');
    class WebControls_MultiplierControls {
        function getData($containerId, $keys, $idKey = null, $stripSlashes = false) {
            $i = 0;
            $data = array();
            while (WebControls_MultiplierControls::isSetTemplateItem($containerId, $i)) {
                $dataItem = array();
                if (!empty($keys)) {
                    foreach($keys as $key) {
                        $itemKey = WebControls_MultiplierControls::getTemplateItemKey($key, $i);
                        $dataItem[$key] = null;
                        if (isset($_REQUEST[$itemKey])) {
                            $dValue = $_REQUEST[$itemKey];
                            if ($stripSlashes && get_magic_quotes_gpc()) {
                                $dValue = stripslashes($dValue);
                            }
                            $dataItem[$key] = $dValue;
                        } else if (isset($_FILES[$itemKey])) {
                            $dataItem[$key] = $_FILES[$itemKey];
                        }
                    }
                }
                $itemIdKey = !empty($idKey) ? WebControls_MultiplierControls::getTemplateItemKey($idKey, $i) : null;
                if (!empty($itemIdKey) && isset($dataItem[$itemIdKey])) {
                    $data[$dataItem[$itemIdKey]] = $dataItem;
                } else {
                    $data[] = $dataItem;
                }
                ++$i;
            }
            return $data;
        }
        function isSetTemplateItem($containerId, $index) {
            return isset($_REQUEST[$containerId.'_hdnstatus_'.$index]);
        }
        function getTemplateItemKey($key, $index) {
            return $key.'_'.$index;
        }
        function dataToJson($data) {
            $jsonHandler = new Services_JSON();
            return $jsonHandler->encode($data);
        }
    };
?>