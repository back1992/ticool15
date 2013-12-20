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
    class AriFileManagerBaseProvider extends AriObject {
        function setPermissions($path, $mode = 0777) {
            return chmod($path, $mode);
        }
        function deleteFolder($path) {
            return rmdir($path);
        }
        function deleteFile($path) {
            $this->setPermissions($path, 0777);
            return unlink($path);
        }
        function copy($src, $dest, $path = null) {
            return copy($src, $dest);
        }
        function move($src, $dest, $path = '') {
            return rename($src, $dest);
        }
        function createFolder($path, $mode = 0755) {
            $ret = false;
            $origmask = @umask(0);
            $ret = @mkdir($path, $mode);
            @umask($origmask);
            return $ret;
        }
        function ensureDirExists($filePath, $baseDir, $mode = 0777) {
            $baseDir = $this->ensureEndWithSlash($baseDir);
            $filePath = dirname($filePath);
            $dirs = explode('/', $filePath);
            if (empty($dirs) || count($dirs) == 0) {
                $dirs = explode('\\', $filePath);
            }
            if (empty($dirs)) return;
            foreach($dirs as $dir) {
                if (empty($dir)) continue;
                $baseDir.= $dir.'/';
                if (!file_exists($baseDir) || !is_dir($baseDir)) {
                    $this->createFolder($baseDir, $mode);
                }
            }
        }
        function deleteFiles($path, $recursive = true, $delSubDirs = true, $delRoot = true) {
            $dir = null;
            $result = true;
            if (!$dir = @dir($path)) return false;
            while ($file = $dir->read()) {
                if ($file === '.' || $file === '..') continue;
                $full = $dir->path.'/'.$file;
                if (@is_dir($full) && $recursive) {
                    if (!$this->deleteFiles($full, $recursive, $delSubDirs)) $result = false;
                    if (!@$this->deleteFolder($full)) $result = false;
                } else if (is_file($full)) {
                    if (!@$this->deleteFile($full)) $result = false;
                }
            }
            $dir->close();
            if ($delRoot) {
                if (!@$this->deleteFolder($dir->path)) $result = false;
            }
            return $result;
        }
        function ensureEndWithSlash($dir) {
            if (empty($dir)) $dir = '';
            $dir = trim($dir);
            $len = strlen($dir);
            if ($len<1 || (strrpos($dir, '/') !== $len-1 && strrpos($dir, '\\') != $len-1)) {
                $dir.= '/';
            }
            return $dir;
        }
        function getImageFileList($dir, $recursive = false, $exts = null) {
            $fileList = $this->getFileList($dir, $recursive, $exts);
            $imageFileList = array();
            $exifExists = function_exists('exif_imagetype');
            foreach($fileList as $file) {
                $isImage = false;
                if ($exifExists && @exif_imagetype($dir.$file)) {
                    $isImage = true;
                } else {
                    $imageInfo = @getimagesize($dir.$file);
                    $isImage = !empty($imageInfo) && $imageInfo[2];
                }
                if ($isImage) $imageFileList[] = $file;
            }
            return $imageFileList;
        }
        function getFolderList($dir, $recursive = false, $fullPath = false) {
            $folders = array();
            if (@is_dir($dir)) {
                if ($handle = opendir($dir)) {
                    $normalizeDir = $this->ensureEndWithSlash($dir);
                    while (false !== ($file = readdir($handle))) {
                        if ($file != '.' && $file != '..') {
                            if (@is_dir($dir.'/'.$file)) {
                                $folders[] = $fullPath ? $normalizeDir.$file : $file;
                                if ($recursive) {
                                    $subFolders = $this->getFolderList($normalizeDir.$file, $recursive, $fullPath);
                                    foreach($subFolders as $subFolder) {
                                        $folders[] = $fullPath ? $subFolder : $file.'/'.$subFolder;
                                    }
                                }
                            }
                        }
                    }
                    closedir($handle);
                }
            }
            return $folders;
        }
        function getFileList($dir, $recursive = false, $exts = null) {
            $fileList = array();
            $this->_getFileList($dir, $recursive, $exts, $fileList);
            return $fileList;
        }
        function _getFileList($dir, $recursive = false, $exts = null, &$fileList, $prefix = '') {
            if (@is_dir($dir)) {
                $extCnt = !empty($exts) ? count($exts) : 0;
                if ($handle = opendir($dir)) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != '.' && $file != '..') {
                            if (@is_dir($dir.'/'.$file)) {
                                if ($recursive) {
                                    $this->_getFileList($dir.'/'.$file, $recursive, $exts, $fileList, $prefix.$file.'/');
                                }
                            } else {
                                if ($extCnt>0) {
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    if (!in_array($ext, $exts)) continue;
                                }
                                $fileList[] = $prefix.$file;
                            }
                        }
                    }
                    closedir($handle);
                }
            }
            return $fileList;
        }
    };
?>