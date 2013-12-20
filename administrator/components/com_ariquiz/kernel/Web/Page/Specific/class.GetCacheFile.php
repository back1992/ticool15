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
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Cache.FileCache');
    class GetCacheFileAriPage extends AriPageBase {
        var $_fileGroup;
        var $_fileTable = null;
        var $_contentType = 'application/octet-stream';
        function execute() {
            $fileId = AriRequest::getParam('fileId', 0);
            $content = $this->_getFileContent($fileId);
            $this->sendBinaryRespose($content, $this->_contentType);
        }
        function _getFileContent($fileId) {
            $content = '';
            $cacheDir = AriGlobalPrefs::getCacheDir() .$this->_fileGroup.'/';
            $cacheFile = null;
            if (empty($cacheFile) || !file_exists($cacheFile)) {
                $fileController = new AriFileController($this->_fileTable);
                $fileList = $fileController->call('getFileList', $this->_fileGroup, array($fileId), true);
                if (!empty($fileList) && count($fileList) >0) {
                    $file = $fileList[0];
                    $content = $file->Content;
                    if (!file_exists($cacheDir.$file->FileId.'.'.$file->Extension)) {
                        AriFileCache::saveBinaryFile($file->Content, $cacheDir.$file->FileId.'.'.$file->Extension);
                    }
                }
            } else {
                $oldMQR = get_magic_quotes_runtime();
                set_magic_quotes_runtime(0);
                $handle = fopen($hotSpotImg, "rb");
                $content = fread($handle, filesize($cacheFile));
                fclose($handle);
                set_magic_quotes_runtime($oldMQR);
            }
            return $content;
        }
    };
?>