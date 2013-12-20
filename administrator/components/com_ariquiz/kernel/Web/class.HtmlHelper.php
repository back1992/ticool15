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
    class AriHtmlHelper extends AriObject {
        function getAttrStr($attrs, $leadSpace = true) {
            $str = '';
            if (empty($attrs) || !is_array($attrs)) return $str;
            $str = array();
            foreach($attrs as $key=>$value) {
                if (is_null($value)) continue;
                if (is_array($value)) {
                    $subAttrs = array();
                    foreach($value as $subKey=>$subValue) {
                        if (is_null($subValue)) continue;
                        $subAttrs[] = sprintf('%s:%s', $subKey, str_replace('"', '\\"', $subValue));
                    }
                    if (count($subAttrs) >0) {
                        $str[] = sprintf('%s="%s"', $key, join(';', $subAttrs));
                    }
                } else {
                    $str[] = sprintf('%s="%s"', $key, str_replace('"', '\\"', $value));
                }
            }
            $str = join(' ', $str);
            if (!empty($str) && $leadSpace) $str = ' '.$str;
            return $str;
        }
    };
?>