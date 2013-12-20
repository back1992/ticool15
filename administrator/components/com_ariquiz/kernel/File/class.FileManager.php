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
    class AriFileManager extends AriObject {
        function &getInterface() {
            static $interface;
            if (is_null($interface)) {
                if (AriJoomlaBridge::isJoomla1_5()) {
                    AriKernel::import('File.FileManagerInterfaces.J15FileManager');
                    $interface = new AriJ15FileManagerProvider();
                } else {
                    AriKernel::import('File.FileManagerInterfaces.FileManagerBase');
                    $interface = new AriFileManagerBaseProvider();
                }
            }
            return $interface;
        }
        function ensureDirExists($filePath, $baseDir, $mode = 0777) {
            $interface = &AriFileManager::getInterface();
            return $interface->ensureDirExists($filePath, $baseDir, $mode);
        }
        function deleteFiles($path, $recursive = true, $delSubDirs = true, $delRoot = true) {
            $interface = &AriFileManager::getInterface();
            return $interface->deleteFiles($path, $recursive, $delSubDirs, $delRoot);
        }
        function ensureEndWithSlash($dir) {
            $interface = &AriFileManager::getInterface();
            return $interface->ensureEndWithSlash($dir);
        }
        function getImageFileList($dir, $recursive = false, $exts = null) {
            $interface = &AriFileManager::getInterface();
            return $interface->getImageFileList($dir, $recursive, $exts);
        }
        function getFolderList($dir, $recursive = false, $fullPath = false) {
            $interface = &AriFileManager::getInterface();
            return $interface->getFolderList($dir, $recursive, $fullPath);
        }
        function getFileList($dir, $recursive = false, $exts = null) {
            $interface = &AriFileManager::getInterface();
            return $interface->getFileList($dir, $recursive, $exts);
        }
        function createFolder($path, $mode = 0755) {
            $interface = &AriFileManager::getInterface();
            return $interface->createFolder($path, $mode);
        }
        function move($src, $dest, $path = '') {
            $interface = &AriFileManager::getInterface();
            return $interface->move($src, $dest);
        }
        function copy($src, $dest, $path = null) {
            $interface = &AriFileManager::getInterface();
            return $interface->copy($src, $dest);
        }
        function deleteFile($path) {
            $interface = &AriFileManager::getInterface();
            return $interface->deleteFile($path);
        }
        function setPermissions($path, $mode = 0777) {
            $interface = &AriFileManager::getInterface();
            return $interface->setPermissions($path, $mode);
        }
    };
?>