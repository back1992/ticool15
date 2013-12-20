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
    define('ARI_CONFIG_NAMESPACE', '_Config');
    AriKernel::import('Config._Templates.ConfigTemplates');
    AriKernel::import('Controllers.ConfigController');
    AriKernel::import('Cache.FileCache');
    class AriConfigWrapper extends AriObject {
        function setConfigValue($key, $value, $configGroup = null) {
            $configController = new AriConfigController(AriGlobalPrefs::getConfigTable());
            $configController->call('setConfigValue', $key, $value);
            if (!$this->_isError(true, false)) {
                AriConfigWrapper::_createConfigCache($configGroup);
            }
        }
        function _createConfigCache($configGroup = null) {
            $configGroup = AriConfigWrapper::_getConfigGroup($configGroup);
            $configFile = AriConfigWrapper::_getConfigFilePath();
            $configController = new AriConfigController(AriGlobalPrefs::getConfigTable());
            $config = $configController->call('getConfig');
            $GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONFIG_NAMESPACE][$configGroup] = $config;
            $configContent = sprintf(ARI_CONFIG_CACHE_TEMPLATE, $configGroup, var_export($config, true));
            AriFileCache::saveTextFile($configContent, $configFile);
        }
        function _getConfigFilePath() {
            $cacheDir = AriGlobalPrefs::getCacheDir();
            return $cacheDir.'config.php';
        }
        function getConfig($configGroup = null) {
            static $isLoaded = false;
            $configGroup = AriConfigWrapper::_getConfigGroup($configGroup);
            if (!$isLoaded) {
                $configFile = AriConfigWrapper::_getConfigFilePath();
                if (!file_exists($configFile)) {
                    AriConfigWrapper::_createConfigCache($configGroup);
                }
                if (file_exists($configFile)) require_once $configFile;
                $isLoaded = true;
            }
            return isset($GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONFIG_NAMESPACE][$configGroup]) ? $GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONFIG_NAMESPACE][$configGroup] : array();
        }
        function getConfigKey($key, $defaultValue = null, $configGroup = null) {
            $config = AriConfigWrapper::getConfig($configGroup);
            return isset($config[$key]) ? $config[$key] : $defaultValue;
        }
        function removeConfigKey($key, $configGroup = null) {
            $configController = new AriConfigController(AriGlobalPrefs::getConfigTable());
            $configController->call('removeConfigKey', $key);
            if (!$this->_isError(true, false)) {
                AriConfigWrapper::_createConfigCache($configGroup);
            }
        }
        function init() {
            if (!isset($GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONFIG_NAMESPACE])) $GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONFIG_NAMESPACE] = array();
        }
        function _getConfigGroup($configGroup = null) {
            if (empty($configGroup)) $configGroup = AriGlobalPrefs::getConfigGroup();
            return $configGroup;
        }
    }
    AriConfigWrapper::init();
?>