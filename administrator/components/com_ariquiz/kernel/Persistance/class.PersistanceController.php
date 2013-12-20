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
    AriKernel::import('Controllers.ControllerBase');
    class AriPersistanceControllerConstants extends AriClassConstants {
        var $UniqueKey = 'ariPUniqueKey';
        function getClassName() {
            return strtolower('AriPersistanceControllerConstants');
        }
    }
    class AriPersistanceController extends AriControllerBase {
        function overwritePersistance($key, $props, $overwritePropNames = null) {
            global $database, $my;
            $userId = $my->get('id');
            $ownerKey = $this->_getOwnerKey(true);
            if (empty($ownerKey)) return true;
            $errorMsg = 'ARI: Couldnt overwrite persistance.';
            $table = $this->_getTable();
            $query = sprintf('DELETE FROM %1$s WHERE OwnerKey = %2$s AND `Key` = %3$s AND (UserId = %4$d OR IFNULL(%4$d, 0) = 0)', $table, $database->Quote($ownerKey), $database->Quote($key), $userId);
            if (is_array($overwritePropNames) && count($overwritePropNames) >0) {
                $qPropNames = $this->_quoteValues($overwritePropNames);
                $query.= sprintf(' AND `Key` IN (%s)', join(',', $qPropNames));
            }
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error($errorMsg, E_USER_ERROR);
                return false;
            }
            if (!is_array($props) || count($props) <1) return true;
            $query = sprintf('INSERT INTO %s (OwnerKey,UserId,`Key`,Name,Value) VALUES ', $table);
            $qOwnerKey = $database->Quote($ownerKey);
            $qKey = $database->Quote($key);
            $addComma = false;
            foreach($props as $name=>$value) {
                $val = null;
                if (is_array($value) || is_object($value)) {
                    $val = serialize($value);
                } else {
                    $val = $value;
                }
                if ($addComma) $query.= ',';
                $query.= sprintf('(%s,%d,%s,%s,%s)', $qOwnerKey, $userId, $qKey, $database->Quote($name), $database->Quote($val));
                $addComma = true;
            }
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error($errorMsg, E_USER_ERROR);
                return false;
            }
            return true;
        }
        function getPersistance($key) {
            global $database, $my;
            $userId = $my->get('id');
            $ownerId = $this->_getOwnerKey();
            if (empty($ownerId)) return null;
            $table = $this->_getTable();
            $query = sprintf('SELECT Name,Value FROM %1$s WHERE OwnerKey = %2$s AND `Key` = %3$s AND (UserId = %3$d OR IFNULL(%3$d, 0) =0)', $table, $database->Quote($ownerId), $database->Quote($key), $userId);
            $database->setQuery($query);
            $props = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldn\'t get persistance state.', E_USER_ERROR);
                return null;
            }
            $retProps = array();
            if ($props) {
                foreach($props as $prop) {
                    $retProps[$prop->Name] = $prop->Value;
                }
            }
            return $retProps;
        }
        function _getTable() {
            $persTable = AriGlobalPrefs::getPersistanceTable();
            return $persTable;
        }
        function _getOwnerKey($generateNew = false) {
            global $my, $mosConfig_live_site;
            $userId = $my->get('id');
            $key = null;
            if (empty($userId)) {
                $ns = AriPersistanceControllerConstants::getClassName();
                $uniqueKey = AriConstantsManager::getVar('UniqueKey', $ns);
                if (isset($_COOKIE[$uniqueKey])) {
                    $key = $_COOKIE[$uniqueKey];
                } else if (@headers_sent()) {
                    $key = AriUtils::generateUniqueId();
                    if (!@setcookie($uniqueKey, $key, time() +155520000, '/')) {
                        $key = null;
                    }
                }
            } else {
                $key = md5($userId);
            }
            return $key;
        }
    };
?>