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
    class AriConstantsManager {
        function getVar($varName, $namespace) {
            static $constCache;
            $nullVal = null;
            if (isset($constCache[$namespace][$varName])) return $constCache[$namespace][$varName];
            $consts = &AriConstantsManager::_getConstantsObject($namespace);
            if ($consts == null) return $nullVal;
            $varParts = explode('.', $varName);
            $val = $varParts[0];
            if (!isset($consts->$val)) return $nullVal;
            $val = &$consts->$val;
            array_shift($varParts);
            foreach($varParts as $part) {
                if (!isset($val[$part])) return $nullVal;
                $val = &$val[$part];
            }
            if (!isset($constCache[$namespace])) $constCache[$namespace] = array();
            $constCache[$namespace][$varName] = $val;
            return $val;
        }
        function &_getConstantsObject($namespace) {
            static $constObjCache;
            $null = null;
            if (empty($namespace)) return $null;
            if (!isset($constObjCache[$namespace])) {
                if (!isset($GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONSTANTS_NAMESPACE][$namespace])) return $null;
                $constObjCache[$namespace] = &$GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONSTANTS_NAMESPACE][$namespace];
            }
            return $constObjCache[$namespace];
        }
        function registerConstantsObject(&$obj, $namespace) {
            if (!empty($namespace)) {
                $GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONSTANTS_NAMESPACE][$namespace] = &$obj;
            }
        }
    };
?>