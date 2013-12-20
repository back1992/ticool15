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
    AriKernel::import('Data.DataFilter');
    class AriControllerBase extends AriObject {
        function call($method) {
            $numArgs = func_num_args();
            $args = func_get_args();
            if ($numArgs>0) {
                array_shift($args);
            }
            set_error_handler(array(&$this, 'errorHandler'));
            $retVal = call_user_func_array(array(&$this, $method), $args);
            restore_error_handler();
            $this->_isError();
            return $retVal;
        }
        function _raiseError($error) {
            trigger_error($error->error, E_USER_ERROR);
        }
        function _applyFilter($query, $filter) {
            if ($filter) {
                $query.= $this->_getOrder($filter);
                $query.= $this->_getLimit($filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
            }
            return $query;
        }
        function _applyDbCountFilter($query, $filter) {
            if ($filter) {
                $filter = $this->_getDbCountFilter($filter);
                $query = $this->_applyFilter($query, $filter);
            }
            return $query;
        }
        function _getFilter($filterInfo) {
            global $database;
            $filter = '';
            if (!empty($filterInfo) && is_array($filterInfo)) {
                $filterParts = array();
                foreach($filterInfo as $field=>$value) {
                    $filterParts[] = sprintf('%s = %s', $field, $database->Quote($value));
                }
                $filter = join(' AND ', $filterParts);
            }
            return $filter;
        }
        function _getOrder($sortInfo) {
            $query = '';
            if (!empty($sortInfo)) {
                $sortField = $sortInfo->getConfigValue('sortField');
                if (!empty($sortField)) {
                    $query = sprintf(' ORDER BY %s %s ', $sortField, $sortInfo->getConfigValue('sortDirection'));
                    $secondarySorting = $sortInfo->getConfigValue('secondarySorting');
                    if ($secondarySorting) {
                        foreach($secondarySorting as $sortingItem) {
                            $sortField = AriUtils::getParam($sortingItem, 'sortField', null);
                            if (empty($sortField)) continue;
                            $sortDir = strtolower(AriUtils::getParam($sortingItem, 'sortDirection', ''));
                            if ($sortDir && $sortDir != 'asc' && $sortDir != 'desc') $sortDir = '';
                            $query.= sprintf(',%s %s', $sortField, $sortDir);
                        }
                    }
                }
            }
            return $query;
        }
        function _getLimit($limitStart, $limit) {
            $query = '';
            if (!is_null($limitStart)) {
                $query.= ' LIMIT '.intval($limitStart);
                $query.= ','.(!is_null($limit) ? intval($limit) : '18446744073709551615');
            }
            return $query;
        }
        function _fixIdList($idList) {
            if (empty($idList)) return array();
            if (!is_array($idList)) {
                $idList = array($idList);
            }
            return $idList;
        }
        function _quoteValues($arr) {
            global $database;
            if (!empty($arr)) {
                foreach($arr as $key=>$value) $arr[$key] = $database->Quote($value);
            }
            return $arr;
        }
        function _normalizeValue($val) {
            global $database;
            return $val === null ? 'NULL' : $database->Quote($val);
        }
        function _getRecordCount($tableName, $predicateFields = array()) {
            global $database;
            $query = 'SELECT COUNT(*) FROM '.$tableName;
            if (!empty($predicateFields)) {
                $query.= ' WHERE ';
                $predQuery = array();
                foreach($predicateFields as $field=>$value) {
                    $predQuery[] = sprintf('`%s` = %s', $field, $database->Quote($value));
                }
                $query.= join($predQuery, ' AND ');
            }
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get count.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function _isUniqueField($tableName, $field, $value, $keyField = null, $key = null) {
            global $database;
            $query = sprintf('SELECT COUNT(%s) FROM %s WHERE %s = %s', $field, $tableName, $field, $database->Quote($value));
            if (!empty($keyField) && $key != null) {
                $query.= sprintf(' AND %s <> %s', $keyField, $database->Quote($key));
            }
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt check unique field.', E_USER_ERROR);
                return false;
            }
            return $count<1;
        }
        function _getDbCountFilter($filter) {
            if ($filter) {
                $filter = clone($filter);
                $filter->setConfigValue('startOffset', null);
                $filter->setConfigValue('limit', null);
                $filter->setConfigValue('sortField', null);
            }
            return $filter;
        }
        function _modifySource($fields, $params, $remove = true) {
            $modifiedFields = array();
            foreach($params as $param) {
                $entity = $param['Entity'];
                $postfix = $param['Postfix'];
                $entityFields = $entity->getPublicFields();
                $entityModFields = array();
                foreach($entityFields as $entityField=>$value) {
                    $queryField = $entityField.$postfix;
                    if (array_key_exists($queryField, $fields)) {
                        $entityModFields[$entityField] = $fields[$queryField];
                        unset($fields[$queryField]);
                    }
                }
                $modifiedFields[$param['Key']] = $entityModFields;
            }
            foreach($params as $param) {
                $key = $param['Key'];
                $parent = AriUtils::getParam($param, 'Parent');
                if (!$parent) continue;
                if (!is_array($parent)) $parent = array($parent);
                foreach($parent as $parentItem) {
                    $modifiedFields[$parentItem][$key] = &$modifiedFields[$key];
                }
            }
            $modifiedFields = array_merge($fields, $modifiedFields);
            return $modifiedFields;
        }
        function _getModifiedFields($entity, $tblAlias, $postfix) {
            $fields = $entity->getPublicFields();
            if (empty($fields)) return '';
            $modFields = array();
            foreach($fields as $key=>$value) {
                $modFields[] = sprintf('%1$s.`%2$s` AS %2$s%3$s', $tblAlias, $key, $postfix);
            }
            return join(',', $modFields);
        }
        function _getDbCount($query, $filter) {
        }
    };
?>