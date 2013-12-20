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
    require_once dirname(__FILE__) .'/utf8/utf8/utf8.php';
    if (AriJoomlaBridge::isJoomla1_5()) {
        if (!function_exists('utf8_to_unicode')) jimport('phputf8.utils.unicode');
        if (!function_exists('utf8_is_valid')) jimport('phputf8.utils.validation');
        if (!function_exists('utf8_strcasecmp')) jimport('phputf8.strcasecmp');
        if (!function_exists('utf8_ireplace')) jimport('phputf8.str_ireplace');
    } else {
        if (!function_exists('utf8_to_unicode')) require_once dirname(__FILE__) .'/utf8/utf8/utils/unicode.php';
        if (!function_exists('utf8_is_valid')) require_once dirname(__FILE__) .'/utf8/utf8/utils/validation.php';
        if (!function_exists('utf8_strcasecmp')) require_once dirname(__FILE__) .'/utf8/utf8/strcasecmp.php';
        if (!function_exists('utf8_ireplace')) require_once dirname(__FILE__) .'/utf8/utf8/str_ireplace.php';
    }
    require_once dirname(__FILE__) .'/convertTable.php';
    class AriString {
        function strToHex($data) {
            return array_shift(unpack('H*', $data));
        }
        function hexToStr($data) {
            $data = str_replace(' ', '', $data);
            $data = str_replace('\x', '', $data);
            return pack('H*', $data);
        }
        function stripslashes($value) {
            $ret = '';
            if (is_string($value)) {
                $ret = stripslashes($value);
            } else {
                if (is_array($value)) {
                    $ret = array();
                    foreach($value as $key=>$val) {
                        $ret[$key] = AriString::stripslashes($val);
                    }
                } else {
                    $ret = $value;
                }
            }
            return $ret;
        }
        function detectUTF($str) {
            return utf8_is_valid($str);
        }
        function fromHtmlEntitiesToUTF($str) {
            if ($str) {
                $str = AriString::html_entity_decode($str, ENT_QUOTES, 'UTF-8');
                $isUtf = AriString::detectUTF($str);
                $str = preg_replace('/&#([0-9]+);/e'.($isUtf ? 'u' : ''), "AriString::_uniChr(\\1)", $str);
                $str = preg_replace('/&#x([a-f0-9]+);/mei'.($isUtf ? 'u' : ''), "AriString::_uniChr(0x\\1)", $str);
            }
            return $str;
        }
        function html_entity_decode($str, $quote_style = ENT_COMPAT, $charset = 'ISO-8859-1') {
            if (version_compare(PHP_VERSION, '5.0.0') >-1) return html_entity_decode($str, $quote_style, $charset);
            global $_ariStringHtmlEntityMap;
            $trans_tbl = $_ariStringHtmlEntityMap;
            if ($quote_style&ENT_NOQUOTES) {
                unset($trans_tbl['&quot;']);
            }
            return strtr($str, $trans_tbl);
        }
        function toUTFHtmlEntities($str) {
            $unicode = utf8_to_unicode($str);
            $retStr = '';
            $cnt = count($unicode);
            for ($i = 0;$i<$cnt;$i++) {
                $code = $unicode[$i];
                $retStr.= $code<256 ? chr($code) : '&#'.$unicode[$i].';';
            }
            return $retStr;
        }
        function translateParams($inputCharset, $outputCharset, $var, $group = null) {
            if ($var === null) return;
            $value = null;
            if ($group == null) {
                $value = $var;
            } else if (isset($var[$group]) && is_array($var[$group])) {
                $value = $var[$group];
            }
            if ($value) AriString::_translateParams($inputCharset, $outputCharset, $value);
            return $value;
        }
        function _translateParams($inputCharset, $outputCharset, &$value) {
            if ($value) {
                foreach($value as $key=>$val) {
                    if (is_array($val)) {
                        AriString::_translateParams($inputCharset, $outputCharset, $val);
                        continue;
                    }
                    $value[$key] = AriString::translateParam($inputCharset, $outputCharset, $val);
                }
            }
        }
        function translateParam($inputCharset, $outputCharset, $value) {
            if ($value && $inputCharset != $outputCharset) {
                if ($outputCharset == 'UTF-8') {
                    $notIconv = true;
                    if (function_exists('iconv')) {
                        $value = @iconv($inputCharset, $outputCharset, $value);
                        if ($value !== false) $notIconv = false;
                    }
                    if ($notIconv) {
                        $value = htmlentities($value, ENT_COMPAT, $inputCharset);
                        $value = AriString::fromHtmlEntitiesToUTF($value);
                    }
                } else if ($inputCharset == 'UTF-8') {
                    if (AriString::detectUTF($value)) {
                        $value = AriString::fromHtmlEntitiesToUTF($value);
                        $value = AriString::toUTFHtmlEntities($value);
                    }
                } else if (function_exists('iconv')) {
                    $value = iconv($inputCharset, $outputCharset, $value);
                }
            }
            return $value;
        }
        function _uniChr($c) {
            if ($c <= 0x7F) {
                return chr($c);
            } else if ($c <= 0x7FF) {
                return chr(0xC0|$c>>6) .chr(0x80|$c&0x3F);
            } else if ($c <= 0xFFFF) {
                return chr(0xE0|$c>>12) .chr(0x80|$c>>6&0x3F) .chr(0x80|$c&0x3F);
            } else if ($c <= 0x10FFFF) {
                return chr(0xF0|$c>>18) .chr(0x80|$c>>12&0x3F) .chr(0x80|$c>>6&0x3F) .chr(0x80|$c&0x3F);
            } else {
                return '';
            }
        }
    };
?>