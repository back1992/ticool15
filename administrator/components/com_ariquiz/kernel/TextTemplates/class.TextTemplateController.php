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
    class AriTextTemplateController extends AriControllerBase {
        var $_tblPrefix;
        function __construct($tblPrefix = '#__arigenerictemplate') {
            $this->_tblPrefix = $tblPrefix;
        }
        function getTablePrefix() {
            return $this->_tblPrefix;
        }
        function createTextTemplateEntity() {
            $textTemplate = AriEntityFactory::createInstance('AriTextTemplateEntity', null, $this->_tblPrefix);
            return $textTemplate;
        }
        function createTextTemplateBaseEntity() {
            $textTemplateBase = AriEntityFactory::createInstance('AriTextTemplateEntity', null, $this->_tblPrefix.'base');
            return $textTemplateBase;
        }
        function getParamsByGroup($group) {
            global $database;
            $query = sprintf('SELECT ParamId, ParamName, ParamDescription, ParamType'.' FROM %2$sbase GTB INNER JOIN %2$sparam GTP'.' 	ON GTB.BaseTemplateId = GTP.BaseTemplateId'.' WHERE GTB.Group = %1$s', $database->Quote($group), $this->_tblPrefix);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get template params.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function getTemplate($templateId, $group = null) {
            global $database;
            $templateId = intval($templateId);
            $template = $this->createTextTemplateEntity();
            if (!$template->load($templateId)) {
                trigger_error('ARI: Couldnt get text template.', E_USER_ERROR);
                return null;
            }
            return $template;
        }
        function saveTemplate($templateId, $fields, $group, $ownerId) {
            global $database;
            $error = 'ARI: Couldnt save text template.';
            $templateId = intval($templateId);
            $isUpdate = ($templateId>0);
            $row = $isUpdate ? $this->getTemplate($templateId) : $this->createTextTemplateEntity();
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
                $templateBase = $this->getTemplateBaseByGroup($group);
                $row->BaseTemplateId = $templateBase->BaseTemplateId;
                $row->Created = ArisDate::getDbUTC();
                $row->CreatedBy = $ownerId;
            }
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            return $row;
        }
        function getTemplateBaseByGroup($group) {
            global $database;
            $error = 'ARI: Couldnt get template base by group name.';
            $template = $this->createTextTemplateBaseEntity();
            $query = sprintf('SELECT GTB.*'.' FROM %2$sbase GTB'.' WHERE GTB.Group = %1$s LIMIT 0,1', $database->Quote($group), $this->_tblPrefix);
            $database->setQuery($query);
            $templateFields = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if (!empty($templateFields) && count($templateFields) >0) {
                if (!$template->bind($templateFields[0])) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
            }
            return $template;
        }
        function getTemplateCount($group, $filter = null) {
            global $database;
            $query = sprintf('SELECT COUNT(TemplateId)'.' FROM %2$s GT INNER JOIN %2$sbase GTB'.' 	ON GT.BaseTemplateId = GTB.BaseTemplateId'.' WHERE GTB.Group = %1$s', $database->Quote($group), $this->_tblPrefix);
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get text template count.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function getTemplateList($group, $filter = null) {
            global $database;
            $query = sprintf('SELECT TemplateId, TemplateName'.' FROM %2$s GT INNER JOIN %2$sbase GTB'.' 	ON GT.BaseTemplateId = GTB.BaseTemplateId'.' WHERE GTB.Group = %1$s', $database->Quote($group), $this->_tblPrefix);
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get template list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function setEntitySingleTemplate($entityName, $entityId, $map, $strictDelete = false) {
            global $database;
            $errorMessage = 'ARI: Couldnt set entity templates.';
            $queryList = array();
            $quoteEntityName = $database->Quote($entityName);
            if (!$strictDelete || !empty($map)) {
                $query = sprintf('DELETE FROM %3$sentitymap'.' WHERE EntityName = %1$s AND EntityId = %2$d', $quoteEntityName, $entityId, $this->_tblPrefix);
                if ($strictDelete) {
                    $query.= sprintf(' AND TemplateType IN (%s)', join(',', $this->_quoteValues(array_keys($map))));
                }
                $database->setQuery($query);
                $database->query();
                if ($database->getErrorNum()) {
                    trigger_error($errorMessage, E_USER_ERROR);
                    return false;
                }
            }
            if (empty($map) || !is_array($map)) return true;
            $query = 'INSERT INTO '.$this->_tblPrefix.'entitymap (TemplateId,EntityName,TemplateType,EntityId) VALUES ';
            $values = array();
            foreach($map as $key=>$value) {
                if ($value) $values[] = sprintf('(%d,%s,%s,%d)', $value, $quoteEntityName, $database->Quote($key), $entityId);
            }
            if (count($values) >0) {
                $query.= join(',', $values);
                $queryList[] = $query;
            }
            $database->setQuery(join($queryList, ';'));
            if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
        else $database->query_batch();
        if ($database->getErrorNum()) {
            trigger_error($errorMessage, E_USER_ERROR);
            return false;
        }
        return true;
    }
    function getEntitySingleTemplate($entityName, $entityId) {
        global $database;
        $query = sprintf('SELECT GTEM.TemplateType, GTEM.TemplateId'.' FROM %3$sentitymap GTEM'.' WHERE GTEM.EntityName = %1$s AND GTEM.EntityId = %2$d'.' GROUP BY GTEM.TemplateType'.' ORDER BY NULL', $database->Quote($entityName), $entityId, $this->_tblPrefix);
        $database->setQuery($query);
        $rows = $database->loadAssocList();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt get entity templates.', E_USER_ERROR);
            return null;
        }
        $res = array();
        if (!empty($rows)) {
            foreach($rows as $row) {
                $res[$row['TemplateType']] = $row['TemplateId'];
            }
        }
        return $res;
    }
    function deleteTemplate($idList, $group) {
        $idList = $this->_fixIdList($idList);
        if (empty($idList)) return true;
        global $database;
        $queryList = array();
        $idStr = join(',', $this->_quoteValues($idList));
        $queryList[] = sprintf('DELETE FROM %2$s WHERE TemplateId IN (%1$s)', $idStr, $this->_tblPrefix);
        $queryList[] = sprintf('DELETE FROM %2$sentitymap WHERE TemplateId IN (%1$s)', $idStr, $this->_tblPrefix);
        $database->setQuery(join($queryList, ';'));
        if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
    else $database->query_batch();
    if ($database->getErrorNum()) {
        trigger_error('ARI: Couldnt delete text templates.', E_USER_ERROR);
        return false;
    }
    return true;
    }
    };
?>