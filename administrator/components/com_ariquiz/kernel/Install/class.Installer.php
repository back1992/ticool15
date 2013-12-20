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
    define('_ARI_INSTALL_ERROR_EXECUTEQUERY', 'Couldn\'t execute query. Error: %s.');
    define('_ARI_INSTALL_ERROR_CHMOD', 'Couldn\'t change permission for directory "%s" permission "%s".');
    define('_ARI_INSTALL_SUCCESFULLY', 'Component succesfully installed');
    define('_ARI_INSTALL_FAILED', 'Component installation failed');
    AriKernel::import('Install.XmlInstaller');
    AriKernel::import('File.FileManager');
    AriKernel::import('System.System');
    class AriInstallerBase extends AriObject {
        var $option;
        var $adminPath;
        var $_installErrors;
        var $_xmlInstaller;
        function __construct($options) {
            global $mosConfig_absolute_path;
            $this->bindProperties($options);
            $this->basePath = $mosConfig_absolute_path.'/components/'.$this->option.'/';
            $this->adminPath = $mosConfig_absolute_path.'/administrator/components/'.$this->option.'/';
            $this->_xmlInstaller = new AriXmlInstaller();
        }
        function errorHandler($errNo, $errStr, $errFile, $errLine) {
            parent::errorHandler($errNo, $errStr, $errFile, $errLine);
            if ($this->_isError(false, false)) {
                $this->_installErrors.= "\r\n".$this->_lastError->error;
                $this->_lastError = null;
            }
        }
        function install() {
            @set_time_limit(9999);
            @ini_set('display_errors', true);
            error_reporting(E_ALL);
            ignore_user_abort(true);
            AriSystem::setOptimalMemoryLimit('16M', '16M', '48M');
            $this->_installErrors = '';
            $this->_registerErrorHandler();
            $result = $this->installSteps();
            restore_error_handler();
            return $this->_getInstallationResult();
        }
        function isSuccess() {
            return empty($this->_installErrors);
        }
        function _getInstallationResult() {
            $success = empty($this->_installErrors);
            $return = '';
            if ($success) {
                $return = sprintf('<div style="color: green; font-weight: bold; text-align: center;">%s</div>', _ARI_INSTALL_SUCCESFULLY);
            } else {
                $return = sprintf('<div style="color: red; font-weight: bold; text-align: center;">%s</div><div style="color: red;">%s</div>', _ARI_INSTALL_FAILED, $this->_installErrors);
            }
            return $return;
        }
        function installSteps() {
            return true;
        }
        function isDbSupportUtf8() {
            global $database;
            $query = 'SHOW CHARACTER SET LIKE "utf8"';
            $database->setQuery($query);
            $result = $database->loadAssocList();
            if ($database->getErrorNum()) {
                $error = sprintf(_ARI_INSTALL_ERROR_EXECUTEQUERY, $database->getErrorMsg());
                trigger_error($error, E_USER_ERROR);
                return false;
            }
            return (!empty($result) && count($result) >0);
        }
        function doInstallFile($file) {
            $this->_xmlInstaller->doInstallFile($file, $this->option);
        }
        function setPermissions($dirForChmod) {
            $errors = array();
            foreach($dirForChmod as $dir=>$perm) {
                if (!AriFileManager::setPermissions($dir, $perm)) {
                    $errors[] = sprintf(_ARI_INSTALL_ERROR_CHMOD, $dir, $perm);
                }
            }
            if (count($errors) >0) {
                trigger_error(join("\r\n", $errors), E_USER_ERROR);
                return false;
            }
            return true;
        }
        function updateMenuIcons($menuInfo) {
            global $database;
            $queryList = array();
            foreach($menuInfo as $menuInfoItem) {
                $link = $menuInfoItem['link'];
                $img = $menuInfoItem['image'];
                $queryList[] = sprintf('UPDATE #__components'.' SET admin_menu_img=%s'.' WHERE admin_menu_link=%s', $database->Quote($img), $database->Quote($link));
            }
            $database->setQuery(join($queryList, ';'));
            if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
        else $database->query_batch();
        if ($database->getErrorNum()) {
            trigger_error(sprintf(_ARI_INSTALL_ERROR_EXECUTEQUERY, $database->getErrorMsg()), E_USER_ERROR);
            return false;
        }
        return true;
    }
    function installMambots($mambots) {
        global $database, $mosConfig_absolute_path;
        $isJ15 = AriJoomlaBridge::isJoomla1_5();
        $sysFolder = $isJ15 ? 'plugins' : 'mambots';
        $sysTable = $isJ15 ? '#__plugins' : '#__mambots';
        $existsMambots = array();
        foreach($mambots as $key=>$value) {
            $existsMambots[] = "'".$key."'";
        }
        if (!empty($existsMambots)) {
            $query = sprintf('SELECT DISTINCT element FROM '.$sysTable.' WHERE element IN (%s)', join(',', $existsMambots));
            $database->setQuery($query);
            $existsMambots = $database->loadResultArray();
        }
        if (empty($existsMambots)) $existsMambots = array();
        $query = 'INSERT INTO `'.$sysTable.'` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES ("%s", "%s", "%s", 0, 2, 1, 0, 0, 0, "0000-00-00 00:00:00", "")';
        $queryList = array();
        $notExistsMambots = array();
        foreach($mambots as $key=>$value) {
            if (!in_array($key, $existsMambots)) {
                $notExistsMambots[] = $key;
                $queryList[] = sprintf($query, $value['name'], $key, $value['folder']);
            }
        }
        $database->setQuery(join($queryList, ';'));
        if ($isJ15) $database->queryBatch();
    else $database->query_batch();
    if ($database->getErrorNum()) {
        trigger_error(sprintf(_ARI_INSTALL_ERROR_EXECUTEQUERY, $database->getErrorMsg()), E_USER_ERROR);
        return false;
    }
    $files = array();
    foreach($mambots as $key=>$value) {
        $mambotFiles = $value['files'];
        $files[$key] = array('folder'=>$value['folder'], 'files'=>$mambotFiles);
    }
    $baseBotDir = $mosConfig_absolute_path.'/'.$sysFolder.'/';
    foreach($files as $key=>$value) {
        $files = $value['files'];
        $folder = $value['folder'];
        $botDir = $baseBotDir.$folder.'/';
        foreach($files as $file) {
            $fileName = basename($file);
            $mambotPath = $botDir.$fileName;
            if (@file_exists($mambotPath)) @AriFileManager::deleteFile($mambotPath);
            @AriFileManager::copy($this->adminPath.$file, $mambotPath);
            if (!@file_exists($mambotPath)) {
            }
        }
    }
    return true;
    }
    function installModules($modules, $addToMenu = false) {
        global $database, $mosConfig_absolute_path;
        $isJ15 = AriJoomlaBridge::isJoomla1_5();
        $existsModules = array();
        foreach($modules as $key=>$value) {
            $existsModules[] = "'".$key."'";
        }
        if (!empty($existsModules)) {
            $query = sprintf('SELECT DISTINCT module FROM #__modules WHERE module IN (%s)', join(',', $existsModules));
            $database->setQuery($query);
            $existsModules = $database->loadResultArray();
        }
        if (empty($existsModules)) $existsModules = array();
        $isGroupExists = $this->_isColumnExists('#__modules', 'groups');
        $query = $isGroupExists ? 'INSERT INTO `#__modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `groups`) VALUES ("%s", "", 2, "left", 0, "0000-00-00 00:00:00", 1, "%s", 0, 0, 0, "", 0, 0, "")' : 'INSERT INTO `#__modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ("%s", "", 2, "left", 0, "0000-00-00 00:00:00", 1, "%s", 0, 0, 0, "", 0, 0)';
        $queryList = array();
        $notExistsModule = array();
        foreach($modules as $key=>$value) {
            if (!in_array($key, $existsModules)) {
                $notExistsModule[] = $key;
                $queryList[] = sprintf($query, $value['description'], $key);
            }
        }
        $database->setQuery(join($queryList, ';'));
        if ($isJ15) $database->queryBatch();
    else $database->query_batch();
    if ($database->getErrorNum()) {
        trigger_error(sprintf(_ARI_INSTALL_ERROR_EXECUTEQUERY, $database->getErrorMsg()), E_USER_ERROR);
        return false;
    }
    if ($addToMenu && !empty($notExistsModule)) {
        $query = 'SELECT id FROM #__menu WHERE link LIKE "%option='.$this->option.'%"';
        $database->setQuery($query);
        $menuIdList = $database->loadResultArray();
        if (!$database->getErrorNum() && !empty($menuIdList)) {
            $qNotExistModule = array();
            foreach($notExistsModule as $module) {
                $qNotExistModule[] = $database->Quote($module);
            }
            $query = sprintf('SELECT id FROM #__modules WHERE module IN (%s)', join($qNotExistModule, ','));
            $database->setQuery($query);
            $moduleIdList = $database->loadResultArray();
            if (!$database->getErrorNum() && !empty($moduleIdList)) {
                $queryList = array();
                foreach($moduleIdList as $moduleId) {
                    foreach($menuIdList as $menuId) {
                        $queryList[] = sprintf('INSERT INTO #__modules_menu SET moduleid = %d, menuid = %d', $moduleId, $menuId);
                    }
                }
                $database->setQuery(join($queryList, ';'));
                if ($isJ15) $database->queryBatch();
            else $database->query_batch();
        }
    }
    }
    $files = array();
    foreach($modules as $key=>$value) {
        $modFiles = $value['files'];
        $files[$key] = $modFiles;
    }
    $baseModDir = $mosConfig_absolute_path.'/modules/';
    foreach($files as $key=>$value) {
        $modDir = $baseModDir;
        if ($isJ15) {
            $modDir.= $key.'/';
            if (!@file_exists($modDir)) {
                AriFileManager::createFolder($modDir, 0777);
            }
        }
        foreach($value as $file) {
            $fileName = basename($file);
            $modulePath = $modDir.$fileName;
            if (@file_exists($modulePath)) @AriFileManager::deleteFile($modulePath);
            AriFileManager::copy($this->adminPath.$file, $modulePath);
            if (!@file_exists($modulePath)) {
            }
        }
    }
    return true;
    }
    function _isColumnExists($table, $column) {
        global $database;
        $query = sprintf('SHOW COLUMNS FROM %s LIKE "%s"', $table, $column);
        $database->setQuery($query);
        $columnsList = $database->loadObjectList();
        $isColumnExists = (!empty($columnsList) && count($columnsList) >0);
        return $isColumnExists;
    }
    function _isIndexExists($table, $index) {
        global $database;
        $query = 'SHOW INDEX FROM '.$table;
        $database->setQuery($query);
        $keys = $database->loadAssocList();
        if (is_array($keys)) {
            foreach($keys as $keyInfo) {
                if (isset($keyInfo['Key_name']) && $keyInfo['Key_name'] == $index) {
                    return true;
                }
            }
        }
        return false;
    }
    function _applyUpdates($version) {
        $updateSig = '_updateTo_';
        $lowerUpdateSig = strtolower($updateSig);
        $methods = get_class_methods(get_class($this));
        $updateMethods = array();
        foreach($methods as $method) {
            $lowerMethod = strtolower($method);
            if (strpos($lowerMethod, $lowerUpdateSig) === 0) {
                $methodVer = str_replace(array($updateSig, $lowerUpdateSig, '_'), array('', '', '.'), $method);
                if (version_compare($methodVer, $version, '>')) {
                    $updateMethods[$methodVer] = $method;
                }
            }
        }
        if (count($updateMethods) >0) {
            uksort($updateMethods, 'version_compare');
            foreach($updateMethods as $updateMethod) {
                $this->$updateMethod();
            }
        }
    }
    };
?>