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
    AriKernel::import('Controllers.FileController');
    class AriFileCache {
        function recacheFile($cacheDir, $fileGroup, $fileId, $binary = true, $fileTable = null) {
            AriFileCache::cacheFile($cacheDir, $fileGroup, $fileId, null, $binary, true, $fileTable);
        }
        function recacheFileWithName($cacheDir, $fileGroup, $fileId, $binary = true, $fileTable = null) {
            AriFileCache::cacheFileWithName($cacheDir, $fileGroup, $fileId, null, $binary, true, $fileTable);
        }
        function cacheFile($cacheDir, $fileGroup, $fileId, $ext, $binary = true, $recache = false, $fileTable = null) {
            AriFileCache::_cacheFile($cacheDir, $fileGroup, $fileId, $ext, null, false, $binary, $recache, $fileTable);
        }
        function cacheFileWithName($cacheDir, $fileGroup, $fileId, $fileName = null, $binary = true, $recache = false, $fileTable = null) {
            AriFileCache::_cacheFile($cacheDir, $fileGroup, $fileId, $fileName, true, $binary, $recache, $fileTable);
        }
        function _cacheFile($cacheDir, $fileGroup, $fileId, $ext = null, $fileName = null, $useFileName = false, $binary = true, $recache = false, $fileTable = null) {
            $cacheDir.= $fileGroup.'/';
            if (!$recache) {
                if (!is_null($fileName) && file_exists($cacheDir.$fileName)) return;
                if (!is_null($ext) && file_exists($cacheDir.$fileId.'.'.$ext)) return;
            }
            $fileTable = AriFileCache::_getFileTable($fileTable);
            $fileController = new AriFileController($fileTable);
            $file = $fileController->call('getFile', $fileId, $fileGroup);
            if (!empty($file)) {
                $content = $file->Content;
                $fileName = $cacheDir;
                $fileName.= ($useFileName && $file->FileName) ? $file->FileName : $fileId.($file->Extension ? '.'.$file->Extension : '');
                if ($binary) {
                    AriFileCache::saveBinaryFile($content, $fileName);
                } else {
                    AriFileCache::saveTextFile($content, $fileName);
                }
            }
        }
        function _getFileTable($fileTable) {
            if (empty($fileTable)) $fileTable = AriGlobalPrefs::getFileTable();
            return $fileTable;
        }
        function saveBinaryFile($content, $fileName) {
            return AriFileCache::_saveFile($content, $fileName, 'b');
        }
        function saveTextFile($content, $fileName) {
            return AriFileCache::_saveFile($content, $fileName);
        }
        function _saveFile($content, $fileName, $mode = 't') {
            $ret = true;
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            $handle = fopen($fileName, 'w+'.$mode);
            if (!$handle) {
                $ret = false;
            } else {
                if (fwrite($handle, $content) === false) {
                    $ret = false;
                }
                fclose($handle);
            }
            set_magic_quotes_runtime($oldMQR);
            return $ret;
        }
    };
?>