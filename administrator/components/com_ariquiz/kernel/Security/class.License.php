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
    define('ARI_LICENSE_FLAG_NONE', 1);
    define('ARI_LICENSE_FLAG_DEMO', 2);
    class AriLicense {
        function getKeyInfo($key, $component, $domain) {
            if (empty($key)) return null;
            $key = base64_decode($key);
            if (strlen($key) != 32) return null;
            if (empty($domain)) return null;
            $hash = md5($domain.$component);
            $keyChrs = AriLicense::stringToChars($key);
            $chrs = AriLicense::stringToChars($hash);
            $dateChrs = array();
            $i = 0;
            while ($i<32) {
                if (!$i || $i%5>0) {
                    if ($chrs[$i] != $keyChrs[31-$i] && $i != 1) {
                        return null;
                    }
                } else array_unshift($dateChrs, $keyChrs[31-$i]);
                ++$i;
            }
            $i = 0;
            $date = strrev(AriLicense::charToNum($dateChrs));
            $date = chunk_split($date, 2, '-');
            $date = substr($date, 0, strlen($date) -1);
            $date = strtotime($date);
            $info = array('domain'=>$domain, 'date'=>$date, 'mode'=>ord($keyChrs[30]));
            $i >>= 2;
            $i+= md5($domain);
            return $info;
        }
        function getEndDate($timeStamp) {
            return gmdate(AriLicense::decryptDate('891771681'), $timeStamp);
        }
        function decryptDate($cryptStr) {
            $retStr = '';
            $len = strlen($cryptStr);
            $i = 0;
            while ($i<$len) {
                $part = substr($cryptStr, $i, 3);
                $ord = substr($part, 0, 2);
                if ($part[2]) $ord+= 32;
                $retStr.= chr($ord);
                $i+= 3;
            }
            return $retStr;
        }
        function numToChar($str) {
            $chrs = AriLicense::stringToChars($str);
            $f = create_function('$v', 'return $v + 97;');
            $chrs = array_map($f, $chrs);
            $chrs = array_map('chr', $chrs);
            return join('', $chrs);
        }
        function charToNum($str) {
            $chrs = is_array($str) ? $str : AriLicense::stringToChars($str);
            $f = create_function('$v', 'return ord($v) - 97;');
            $chrs = array_map($f, $chrs);
            return join('', $chrs);
        }
        function stringToChars($str) {
            $str = chunk_split($str, 1, '|');
            $chrs = explode('|', $str);
            array_pop($chrs);
            return $chrs;
        }
    };
?>