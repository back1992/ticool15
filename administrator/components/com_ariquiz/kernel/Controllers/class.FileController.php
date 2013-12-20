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
    AriKernel::import('Controllers.ControllerBase');
    AriKernel::import('Entity.EntityFactory');
    AriKernel::import('Date.Date');
    class AriFileController extends AriControllerBase {
        var $_table;
        function __construct($table = null) {
            if (is_null($table)) $table = AriGlobalPrefs::getFileTable();
            $this->_table = $table;
        }
        function getFileCount($group, $idList = array(), $filter = null) {
            global $database;
            if (!empty($idList)) {
                $idList = $this->_fixIdList($idList);
                if (empty($idList)) return 0;
                $fileStr = join(',', $this->_quoteValues($idList));
                $query = sprintf('SELECT COUNT(*) FROM %s WHERE `Group` = %s AND FileId IN (%s)', $this->_table, $database->Quote($group), $fileStr);
            } else {
                $query = sprintf('SELECT COUNT(*) FROM %s WHERE `Group` = %s', $this->_table, $database->Quote($group));
            }
            $database->setQuery($query);
            $cnt = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get file count.', E_USER_ERROR);
                return 0;
            }
            return $cnt;
        }
        function getFileList($group, $idList = array(), $fullLoad = false, $filter = null) {
            global $database;
            if (is_null($filter)) {
                $filter = new AriDataFilter(array('startOffset'=>0, 'limit'=>null, 'sortField'=>'ShortDescription', 'dir'=>'asc'));
            }
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            $query = '';
            if (!empty($idList)) {
                $idList = $this->_fixIdList($idList);
                if (empty($idList)) return null;
                $fileStr = join(',', $this->_quoteValues($idList));
                $query = sprintf('SELECT FileId,FileName,Extension,Flags,ShortDescription,Created%s FROM %s WHERE `Group` = %s AND FileId IN (%s)', $fullLoad ? ',Content' : '', $this->_table, $database->Quote($group), $fileStr);
                $query = $this->_applyFilter($query, $filter);
            } else {
                $query = sprintf('SELECT FileId,FileName,Extension,Flags,ShortDescription,Created%s FROM %s WHERE `Group` = %s', $fullLoad ? ',Content' : '', $this->_table, $database->Quote($group));
                $query = $this->_applyFilter($query, $filter);
            }
            $database->setQuery($query);
            $results = $database->loadObjectList();
            set_magic_quotes_runtime($oldMQR);
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get file list.', E_USER_ERROR);
                return null;
            }
            return $results;
        }
        function deleteFile($idList, $group = null) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $fileStr = join(',', $this->_quoteValues($idList));
            $query = sprintf('DELETE FROM %s WHERE FileId IN (%s) AND (%s IS NULL OR `Group` = %s)', $this->_table, $fileStr, $this->_normalizeValue($group), $database->Quote($group));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt delete files.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function getFile($fileId, $group = null) {
            $fileId = intval($fileId);
            if (empty($fileId)) return null;
            $file = AriEntityFactory::createInstance('AriFileEntity');
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            if (!$file->load($fileId) || ($group !== null && $file->Group != $group)) {
                return null;
            }
            set_magic_quotes_runtime($oldMQR);
            return $file;
        }
        function saveFileFromFile($fileId, $fields, $fileName, $ownerId = 0) {
            $file = null;
            if (file_exists($fileName)) {
                $oldMQR = get_magic_quotes_runtime();
                set_magic_quotes_runtime(0);
                $handle = fopen($fileName, "rb");
                $fields['Content'] = fread($handle, filesize($fileName));
                fclose($handle);
                set_magic_quotes_runtime($oldMQR);
                $file = $this->saveFile($fileId, $fields, $ownerId);
            }
            return $file;
        }
        function saveFile($fileId, $fields, $ownerId = 0) {
            global $database;
            $error = 'ARI: Couldnt save file.';
            $fileId = intval($fileId);
            $isUpdate = ($fileId>0);
            $row = $isUpdate ? $this->getFile($fileId) : AriEntityFactory::createInstance('AriFileEntity');
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if (!$row->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $row->Content = addslashes($row->Content);
            $row->Size = strlen($row->Content);
            if ($isUpdate) {
                $row->Modified = ArisDate::getDbUTC();
                $row->ModifiedBy = $ownerId;
            } else {
                $row->Created = ArisDate::getDbUTC();
                $row->CreatedBy = $ownerId;
            }
            $extension = $row->Extension;
            if ($row->FileName) {
                $info = pathinfo($row->FileName);
                $extension = $info['extension'];
            }
            $row->Extension = $extension;
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            $hexData = bin2hex($fields['Content']);
            $query = sprintf('UPDATE %s SET Content = 0x%s WHERE FileId=%d', $this->_table, $hexData, $row->FileId);
            $database->setQuery($query);
            $database->query();
            set_magic_quotes_runtime($oldMQR);
            return $row;
        }
    };
?>