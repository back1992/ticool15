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
    class AriQuizCategoryControllerBase extends AriControllerBase {
        var $_tableName;
        var $_entityName;
        function isUniqueCategoryName($name, $id = null) {
            $isUnique = $this->_isUniqueField($this->_tableName, 'CategoryName', $name, 'CategoryId', $id);
            if ($this->_isError(true, false)) {
                trigger_error('ARI: Couldnt check unique category name.', E_USER_ERROR);
                return false;
            }
            return $isUnique;
        }
        function getCategoryMapping($categoryNames) {
            global $database;
            $categoryMapping = array();
            if (!is_array($categoryNames) || count($categoryNames) == 0) return $categoryMapping;
            $query = sprintf('SELECT CategoryId,CategoryName FROM `%s` WHERE CategoryName IN (%s)', $this->_tableName, join(',', $this->_quoteValues($categoryNames)));
            $database->setQuery($query);
            $categories = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get categories mapping.', E_USER_ERROR);
                return $categoryMapping;
            }
            foreach($categories as $category) {
                $categoryMapping[$category['CategoryName']] = $category['CategoryId'];
            }
            return $categoryMapping;
        }
        function saveCategory($categoryId, $fields, $ownerId) {
            global $database;
            $error = 'ARI: Couldnt save category.';
            $categoryId = intval($categoryId);
            $isUpdate = ($categoryId>0);
            $row = $isUpdate ? $this->getCategory($categoryId) : AriEntityFactory::createInstance($this->_entityName, AriGlobalPrefs::getEntityGroup());
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if (!$row->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if ($isUpdate) {
                $row->Modified = ArisDate::getDbUTC();
                $row->ModifiedBy = $ownerId;
            } else {
                $row->Created = ArisDate::getDbUTC();
                $row->CreatedBy = $ownerId;
            }
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            return $row;
        }
        function getCategory($categoryId) {
            global $database;
            $categoryId = intval($categoryId);
            $category = AriEntityFactory::createInstance($this->_entityName, AriGlobalPrefs::getEntityGroup());
            if (!$category->load($categoryId)) {
                trigger_error('ARI: Couldnt get category.', E_USER_ERROR);
                return null;
            }
            return $category;
        }
        function deleteCategory($idList) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $queryList = array();
            $catStr = join(',', $this->_quoteValues($idList));
            $queryList[] = sprintf('DELETE FROM %s WHERE CategoryId IN (%s)', $this->_tableName, $catStr);
            $queryList[] = sprintf('DELETE FROM %s WHERE CategoryId IN (%s)', $this->_tableName, $catStr);
            $database->setQuery(join($queryList, ';'));
            if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
        else $database->query_batch();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt delete category.', E_USER_ERROR);
            return false;
        }
        return true;
    }
    function getCategoryCount($filter = null) {
        $count = $this->_getRecordCount($this->_tableName);
        if ($this->_isError(true, false)) {
            trigger_error('ARI: Couldnt get category count.', E_USER_ERROR);
        }
        return $count;
    }
    function getCategoryList($filter = null) {
        global $database;
        $query = 'SELECT CategoryId, CategoryName'.' FROM '.$this->_tableName.' ';
        $query = $this->_applyFilter($query, $filter);
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt get category list.', E_USER_ERROR);
            return null;
        }
        return $rows;
    }
    };
?>