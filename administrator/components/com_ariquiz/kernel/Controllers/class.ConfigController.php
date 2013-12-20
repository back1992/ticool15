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
    class AriConfigController extends AriControllerBase {
        var $_table;
        function __construct($table) {
            $this->_table = $table;
            parent::__construct();
        }
        function getConfig() {
            global $database;
            $config = array();
            $query = 'SELECT ParamName,ParamValue FROM '.$this->_table;
            $database->setQuery($query);
            $list = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt load config.', E_USER_ERROR);
                return $config;
            }
            if (!empty($list)) {
                foreach($list as $row) {
                    $config[$row['ParamName']] = $row['ParamValue'];
                }
            }
            return $config;
        }
        function getConfigValue($key) {
            global $database;
            $query = sprintf('SELECT ParamValue FROM '.$this->_table.' WHERE ParamName = %s LIMIT 0,1', $database->Quote($key));
            $database->setQuery($query);
            $value = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get config value.', E_USER_ERROR);
                return null;
            }
            return $value;
        }
        function setConfigValue($key, $value) {
            global $database;
            $query = sprintf('INSERT INTO '.$this->_table.' (ParamName,ParamValue) VALUES(%s,%s) ON DUPLICATE KEY UPDATE ParamValue = %2$s', $database->Quote($key), $database->Quote($value));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt store config value.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function removeConfigKey($key) {
            global $database;
            $query = sprintf('DELETE FROM '.$this->_table.' WHERE ParamName = %s', $database->Quote($key));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt remove config key.', E_USER_ERROR);
                return false;
            }
            return true;
        }
    };
?>