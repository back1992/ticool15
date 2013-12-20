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
    AriKernel::import('Data.DDLManager');
    AriKernel::import('Xml.SimpleXml');
    AriKernel::import('System.System');
    class AriImportDataController extends AriControllerBase {
        var $INSERT_COUNT = 10;
        var $_ddlManager;
        function __construct($configFile) {
            $this->_ddlManager = new AriDDLManager($configFile);
        }
        function _loadXml($file) {
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            $xmlStr = trim(file_get_contents($file));
            set_magic_quotes_runtime($oldMQR);
            $xmlHandler = new AriSimpleXML();
            $xmlHandler->loadString($xmlStr);
            return $xmlHandler->document;
        }
        function &_getUsedEntities($recordsNode) {
            $entities = array();
            if (empty($recordsNode)) return $entities;
            $struct = $this->_ddlManager->getStructure();
            $configEntitites = array_keys($struct);
            $uEntities = array();
            foreach($configEntitites as $entity) {
                $entityNodes = AriSimpleXmlHelper::getNode($recordsNode, $entity);
                if (empty($entityNodes)) continue;
                $uEntities[] = $entity;
            }
            foreach($uEntities as $entity) {
                if (in_array($entity, $entities)) continue;
                $baseEntity = $this->_ddlManager->getBaseEntity($entity);
                if ($baseEntity != $entity) {
                    if (!in_array($baseEntity, $entities)) $entities[$baseEntity] = $this->_getUsedEntityInfo($baseEntity);
                    $entities[$entity] = &$entities[$baseEntity];
                } else {
                    $entities[$entity] = $this->_getUsedEntityInfo($entity);
                }
            }
            return $entities;
        }
        function _getUsedEntityInfo($entity) {
            global $database;
            $pFields = $this->_ddlManager->getPrimaryFields($entity);
            $intPFields = array();
            foreach($pFields as $pField) {
                if ($this->_ddlManager->isNumberField($entity, $pField)) $intPFields[] = $pField;
            }
            $info = array('keys'=>array(), 'keyMapping'=>array());
            if (count($intPFields) == 0) return $info;
            $qParts = array();
            foreach($intPFields as $pField) {
                $qParts[] = sprintf('(IFNULL(MAX(`%1$s`), 0) + 1) AS `%1$s`', $pField);
                $info['keyMapping'][$pField] = array();
            }
            $query = sprintf('SELECT %s FROM %s LIMIT 0,1', join(',', $qParts), $this->_ddlManager->getEntityTable($entity));
            $database->setQuery($query);
            $result = $database->loadAssocList();
            if ($database->getErrorNum()) {
                return $info;
            }
            $result = count($result) >0 ? $result[0] : array();
            foreach($result as $key=>$value) {
                $info['keys'][$key] = $value;
            }
            return $info;
        }
        function _importEntityRecords(&$records, $entity, &$usedEntities) {
            global $database;
            if (empty($records)) return true;
            $fields = $this->_ddlManager->getEntityFields($entity);
            $foreignRefs = $this->_ddlManager->getForeignReferences($entity);
            $fieldNames = array_keys($fields);
            $fieldsStr = array();
            foreach($fieldNames as $fieldName) {
                $fieldsStr[] = '`'.$fieldName.'`';
            }
            $fieldsStr = join(',', $fieldsStr);
            $insertQuery = sprintf('INSERT INTO %1$s (%2$s) VALUES %%s ON DUPLICATE KEY UPDATE `%3$s`=`%3$s`', $this->_ddlManager->getEntityTable($entity), $fieldsStr, $fieldNames[0]);
            $usedEntityInfo = &$usedEntities[$entity];
            $emptyStrValue = $database->Quote('');
            $values = array();
            $i = 0;
            $recordCount = count($records);
            foreach($records as $record) {
                $valueInsertSql = array();
                foreach($fieldNames as $fieldName) {
                    $valueNode = &AriSimpleXmlHelper::getSingleNode($record, strtolower($fieldName));
                    $value = null;
                    if ($valueNode) {
                        $value = $valueNode->data();
                        if (is_null($value) || strlen($value) == 0) {
                            $isNull = $valueNode->attributes('isNull');
                            if ($isNull) {
                                $value = null;
                            } else {
                                $value = $emptyStrValue;
                            }
                        }
                    } else {
                        $value = $this->_ddlManager->getFieldDefaultValue($entity, $fieldName);
                    }
                    if (isset($usedEntityInfo['keys'][$fieldName])) {
                        if (!isset($usedEntityInfo['keyMapping'][$fieldName][$value])) {
                            $usedEntityInfo['keyMapping'][$fieldName][$value] = $usedEntityInfo['keys'][$fieldName];
                            ++$usedEntityInfo['keys'][$fieldName];
                        }
                        $value = $usedEntityInfo['keyMapping'][$fieldName][$value];
                    } else if (!empty($value) && isset($foreignRefs[$fieldName])) {
                        $foreignRef = $foreignRefs[$fieldName];
                        $foreignField = $foreignRef['foreignField'];
                        $baseForeignEntity = $this->_ddlManager->getBaseEntity($foreignRef['foreignEntity']);
                        $usedForeignEntityInfo = &$usedEntities[$baseForeignEntity];
                        if (!isset($usedForeignEntityInfo['keyMapping'][$foreignField][$value])) {
                            $usedForeignEntityInfo['keyMapping'][$foreignField][$value] = $usedForeignEntityInfo['keys'][$foreignField];
                            ++$usedForeignEntityInfo['keys'][$foreignField];
                        }
                        $value = $usedForeignEntityInfo['keyMapping'][$foreignField][$value];
                    }
                    if (is_null($value)) $value = 'NULL';
                    $valueInsertSql[] = $value;
                }
                $values[] = '('.join(',', $valueInsertSql) .')';
                ++$i;
                if ($i%$this->INSERT_COUNT == 0 || $i == $recordCount) {
                    $recordsQuery = sprintf($insertQuery, join(',', $values));
                    $database->setQuery($recordsQuery);
                    $database->query();
                    if ($database->getErrorNum()) {
                        return false;
                    }
                    $values = array();
                }
            }
            return true;
        }
        function import($dataFile) {
            if (!@file_exists($dataFile)) return false;
            @set_time_limit(9999);
            AriSystem::setOptimalMemoryLimit('16M', '16M', '128M');
            ignore_user_abort(true);
            $xmlDoc = $this->_loadXml($dataFile);
            $xmlDoc = &AriSimpleXmlHelper::getSingleNode($xmlDoc, 'records');
            if (empty($xmlDoc)) return false;
            $usedEntities = &$this->_getUsedEntities($xmlDoc);
            foreach($usedEntities as $entity=>$entityInfo) {
                $entityRecords = &AriSimpleXmlHelper::getNode($xmlDoc, $entity);
                $this->_importEntityRecords($entityRecords, $entity, $usedEntities);
            }
            return true;
        }
    };
?>