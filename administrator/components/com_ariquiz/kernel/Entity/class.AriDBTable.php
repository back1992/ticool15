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
    class AriDBTable extends mosDBTable {
        function AriDBTable($table, $keyField, &$database) {
            $this->mosDBTable($table, $keyField, $database);
        }
        function bind($data, $ignoreArray = array()) {
            if (is_array($data)) {
                $vars = get_class_vars(get_class($this));
                if ($vars) {
                    foreach($vars as $name=>$value) {
                        if (isset($data[$name]) && empty($data[$name])) {
                            $data[$name] = $value;
                        }
                    }
                }
            }
            return parent::bind($data, $ignoreArray);
        }
        function getPublicFields($ignoreArray = array()) {
            $fields = array();
            foreach(get_class_vars(get_class($this)) as $key=>$val) {
                if (substr($key, 0, 1) != '_') {
                    $value = $this->$key;
                    if (!is_object($value) && !in_array($key, $ignoreArray)) $fields[$key] = $value;
                }
            }
            return $fields;
        }
    };
?>