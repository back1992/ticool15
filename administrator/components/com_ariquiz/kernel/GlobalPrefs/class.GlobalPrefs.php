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
    AriKernel::import('Constants.ClassConstants');
    class AriGlobalPrefsConstants extends AriClassConstants {
        var $Namespace = '_agpc';
        var $Keys = array('I18N'=>'i18n', 'DbCharset'=>'dbCharset', 'CacheDir'=>'cacheDir', 'EntityGroup'=>'entityGroup', 'ConfigGroup'=>'configGroup', 'FileTable'=>'fileTable', 'ConfigTable'=>'configTable', 'PersistanceTable'=>'persistanceTable', 'Option'=>'option');
        function getClassName() {
            return strtolower('AriGlobalPrefsConstants');
        }
    }
    new AriGlobalPrefsConstants();
    class AriGlobalPrefs extends AriObject {
        function setPersistanceTable($persistanceTable) {
            AriGlobalPrefs::_setProperty('PersistanceTable', $persistanceTable);
        }
        function getPersistanceTable() {
            return AriGlobalPrefs::_getProperty('PersistanceTable');
        }
        function setOption($option) {
            AriGlobalPrefs::_setProperty('Option', $option);
        }
        function getOption() {
            return AriGlobalPrefs::_getProperty('Option');
        }
        function setConfigTable($configTable) {
            AriGlobalPrefs::_setProperty('ConfigTable', $configTable);
        }
        function getConfigTable() {
            return AriGlobalPrefs::_getProperty('ConfigTable');
        }
        function setFileTable($fileTable) {
            AriGlobalPrefs::_setProperty('FileTable', $fileTable);
        }
        function getFileTable() {
            return AriGlobalPrefs::_getProperty('FileTable');
        }
        function setConfigGroup($configGroup) {
            AriGlobalPrefs::_setProperty('ConfigGroup', $configGroup);
        }
        function getConfigGroup() {
            return AriGlobalPrefs::_getProperty('ConfigGroup');
        }
        function setEntityGroup($entityGroup) {
            AriGlobalPrefs::_setProperty('EntityGroup', $entityGroup);
        }
        function getEntityGroup() {
            return AriGlobalPrefs::_getProperty('EntityGroup');
        }
        function setCacheDir($cacheDir) {
            AriGlobalPrefs::_setProperty('CacheDir', $cacheDir);
        }
        function getCacheDir() {
            return AriGlobalPrefs::_getProperty('CacheDir');
        }
        function setDbCharset($dbCharset) {
            AriGlobalPrefs::_setProperty('DbCharset', $dbCharset);
        }
        function getDbCharset() {
            return AriGlobalPrefs::_getProperty('DbCharset');
        }
        function setI18N(&$i18n) {
            AriGlobalPrefs::_setPropertyObj('I18N', $i18n);
        }
        function &getI18N() {
            return AriGlobalPrefs::_getPropertyObj('I18N');
        }
        function _setPropertyObj($key, &$value) {
            $ns = AriGlobalPrefsConstants::getClassName();
            $realKey = AriConstantsManager::getVar('Keys.'.$key, $ns);
            $realNs = AriConstantsManager::getVar('Namespace', $ns);
            $GLOBALS[ARI_ROOT_NAMESPACE][$realNs][$realKey] = &$value;
        }
        function &_getPropertyObj($key) {
            $ns = AriGlobalPrefsConstants::getClassName();
            $realKey = AriConstantsManager::getVar('Keys.'.$key, $ns);
            $realNs = AriConstantsManager::getVar('Namespace', $ns);
            $obj = null;
            if (isset($GLOBALS[ARI_ROOT_NAMESPACE][$realNs][$realKey])) $obj = &$GLOBALS[ARI_ROOT_NAMESPACE][$realNs][$realKey];
            return $obj;
        }
        function _setProperty($key, $value) {
            $ns = AriGlobalPrefsConstants::getClassName();
            $realKey = AriConstantsManager::getVar('Keys.'.$key, $ns);
            $realNs = AriConstantsManager::getVar('Namespace', $ns);
            $GLOBALS[ARI_ROOT_NAMESPACE][$realNs][$realKey] = $value;
        }
        function _getProperty($key) {
            $ns = AriGlobalPrefsConstants::getClassName();
            $realKey = AriConstantsManager::getVar('Keys.'.$key, $ns);
            $realNs = AriConstantsManager::getVar('Namespace', $ns);
            $obj = null;
            if (isset($GLOBALS[ARI_ROOT_NAMESPACE][$realNs][$realKey])) $obj = $GLOBALS[ARI_ROOT_NAMESPACE][$realNs][$realKey];
            return $obj;
        }
    };
?>