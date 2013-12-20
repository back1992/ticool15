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
    class ArisDate {
        function getDbUTC($timestamp = null) {
            if (is_null($timestamp)) {
                $timestamp = time();
            }
            return gmdate('Y-m-d H:i:s', $timestamp);
        }
        function formatUTCTimestamp($timestamp, $format = '', $tz = null) {
            $format = ArisDate::_getFormat($format);
            $tz = ArisDate::getTimeZone($tz);
            $timestamp+= $tz*60*60;
            return strftime($format, $timestamp);
        }
        function formatDate($date, $format = null, $tz = null) {
            $format = ArisDate::_getFormat($format);
            $tz = ArisDate::getTimeZone($tz);
            if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs)) {
                $date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
                $date = $date>-1 ? strftime($format, $date+($tz*60*60)) : '-';
            }
            return $date;
        }
        function _getFormat($format = null) {
            if (empty($format)) {
                $format = '%Y-%m-%d %H:%M:%S';
            }
            return $format;
        }
        function getTimeZone($tz = null) {
            global $mosConfig_offset_user, $mosConfig_offset, $my;
            if ($tz === null) {
                $userId = $my->get('id');
                if (AriJoomlaBridge::isJoomla1_5()) {
                    $jconfig = new JConfig();
                    if ($userId>0) {
                        $tz = $my->getParam('timezone', 0);
                    } else {
                        $tz = !empty($jconfig->offset) ? $jconfig->offset : 0;
                    }
                } else {
                    if (!isset($mosConfig_offset_user) || is_null($mosConfig_offset_user)) {
                        $tz = !empty($mosConfig_offset) ? $mosConfig_offset : 0;
                    } else {
                        $tz = $mosConfig_offset_user;
                    }
                }
            }
            return $tz;
        }
    }
    define('ARIS_DATE_YSC', 31556926);
    define('ARIS_DATE_MSC', 2629743);
    define('ARIS_DATE_WSC', 604800);
    define('ARIS_DATE_DSC', 86400);
    define('ARIS_DATE_HSC', 3600);
    define('ARIS_DATE_MINSC', 60);
    define('ARIS_DATE_SSC', 1);
    class ArisDateDuration {
        function getPeriods() {
            return array('years'=>ARIS_DATE_YSC, 'months'=>ARIS_DATE_MSC, 'weeks'=>ARIS_DATE_WSC, 'days'=>ARIS_DATE_DSC, 'hours'=>ARIS_DATE_HSC, 'minutes'=>ARIS_DATE_MINSC, 'seconds'=>ARIS_DATE_SSC);
        }
        function getShortDayPeriods() {
            return array('d'=>ARIS_DATE_DSC, 'h'=>ARIS_DATE_HSC, 'min'=>ARIS_DATE_MINSC, 'sec'=>ARIS_DATE_SSC);
        }
        function toString($duration, $periods = null, $spliter = ' ', $ignorePlural = false) {
            if (!is_array($duration)) {
                $duration = ArisDateDuration::intToArray($duration, $periods);
            }
            return ArisDateDuration::arrayToString($duration, $spliter, $ignorePlural);
        }
        function intToArray($seconds, $periods = null) {
            if (!is_array($periods)) {
                $periods = ArisDateDuration::getPeriods();
            }
            $values = array();
            if ($seconds == 0 && is_array($periods) && count($periods) >0) {
                $intPeriods = array_values($periods);
                sort($intPeriods);
                $key = array_search($intPeriods[0], $periods);
                $values[$key] = 0;
                return $values;
            }
            $seconds = (float)$seconds;
            foreach($periods as $period=>$value) {
                $count = floor($seconds/$value);
                if ($count == 0) continue;
                $values[$period] = $count;
                $seconds = $seconds%$value;
            }
            if (empty($values)) {
                $values = null;
            }
            return $values;
        }
        function arrayToString($duration, $spliter = ', ', $ignorePlural = false) {
            if (!is_array($duration)) {
                return false;
            }
            foreach($duration as $key=>$value) {
                $segment_name = $ignorePlural ? $key : substr($key, 0, -1);
                $segment = $value.' '.$segment_name;
                if (!$ignorePlural && $value>1) {
                    $segment.= 's';
                }
                $array[] = $segment;
            }
            return implode($spliter, $array);
        }
    };
?>