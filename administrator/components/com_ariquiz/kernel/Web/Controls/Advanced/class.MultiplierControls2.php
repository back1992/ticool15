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
    defined('_VALID_MOS') or die ('Direct Access to this location is not allowed.');
    class WebControls_MultiplierControls2 {
        function getData($containerTree, $inputData = null) {
            if (is_null($inputData)) $inputData = $_REQUEST;
            $data = array();
            if (!is_array($inputData)) return $data;
            WebControls_MultiplierControls2::_getDataRecurive($containerTree, $inputData, $data);
            return $data;
        }
        function _getDataRecurive($containerTree, $inputData, &$data, $prefix = '') {
            foreach($containerTree as $key=>$value) {
                $data[$key] = array();
                $idList = WebControls_MultiplierControls2::_getTemplateIdList($prefix, $key, $inputData);
                $childs = isset($value['childs']) ? $value['childs'] : null;
                foreach($idList as $id) {
                    $newPrefix = WebControls_MultiplierControls2::_getPrefixByTemplateId($id, $key);
                    $subData = array();
                    if ($childs) {
                        WebControls_MultiplierControls2::_getDataRecurive($childs, $inputData, $subData, $newPrefix);
                    }
                    $data[$key][] = array('data'=>WebControls_MultiplierControls2::_getItemData($inputData, $newPrefix, $value['keys']), 'childs'=>$subData);
                }
            }
        }
        function _getTemplateIdList($prefix, $templateId, $inputData) {
            $key = sprintf('%s%s_hdnStatus', $prefix, $templateId);
            $idList = array();
            if (isset($inputData[$key])) {
                $idList = explode(':', $inputData[$key]);
            }
            return $idList;
        }
        function _getItemData($inputData, $prefix, $keys) {
            $data = array();
            if ($keys) {
                foreach($keys as $key) {
                    $inputKey = $prefix.$key;
                    $data[$key] = isset($inputData[$inputKey]) ? $inputData[$inputKey] : null;
                }
            }
            return $data;
        }
        function _getPrefixByTemplateId($rTemplateId, $templateId) {
            $prefix = substr($rTemplateId, 0, strlen($rTemplateId) -strlen($templateId));
            return $prefix;
        }
    };
?>