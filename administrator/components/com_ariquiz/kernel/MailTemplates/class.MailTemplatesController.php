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
    AriKernel::import('TextTemplates.TextTemplateController');
    AriKernel::import('Utils.Utils');
    class AriMailTemplatesController extends AriControllerBase {
        var $_tableName;
        var $_textTemplateController;
        function __construct($tableName, $textTemplateTableName) {
            $this->_tableName = $tableName;
            $this->_textTemplateController = new AriTextTemplateController($textTemplateTableName);
            parent::__construct();
        }
        function getTextTemplateController() {
            return $this->_textTemplateController;
        }
        function createMailTemplateInstance() {
            $mailTemplate = AriEntityFactory::createInstance('AriMailTemplateEntity', null, $this->_tableName);
            $mailTemplate->TextTemplate = $this->_textTemplateController->createTextTemplateEntity();
            return $mailTemplate;
        }
        function getTemplateCount($filter = null, $group = null) {
            global $database;
            $query = sprintf('SELECT COUNT(MailTemplateId)'.' FROM %1$s MT INNER JOIN %2$s GT'.'	ON MT.TextTemplateId = GT.TemplateId'.' INNER JOIN %2$sbase GTB'.' 	ON GT.BaseTemplateId = GTB.BaseTemplateId', $this->_tableName, $this->_textTemplateController->getTablePrefix());
            $group = $this->_fixIdList($group);
            if (!empty($group)) {
                $query.= ' WHERE GTB.Group IN ('.join(',', $this->_quoteValues($group)) .')';
            }
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $count = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get mail template count.', E_USER_ERROR);
                return 0;
            }
            return $count;
        }
        function getTemplateList($filter = null, $group = null) {
            global $database;
            $query = sprintf('SELECT MT.MailTemplateId, MT.TextTemplateId, GT.TemplateName'.' FROM %1$s MT INNER JOIN %2$s GT'.'	ON MT.TextTemplateId = GT.TemplateId'.' INNER JOIN %2$sbase GTB'.' 	ON GT.BaseTemplateId = GTB.BaseTemplateId', $this->_tableName, $this->_textTemplateController->getTablePrefix());
            $group = $this->_fixIdList($group);
            if (!empty($group)) {
                $query.= ' WHERE GTB.Group IN ('.join(',', $this->_quoteValues($group)) .')';
            }
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $rows = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get mail template list.', E_USER_ERROR);
                return null;
            }
            return $rows;
        }
        function getTemplateByTextId($textTemplateId, $group = null, $fullLoad = true) {
            global $database;
            $template = null;
            $textTemplateId = @intval($textTemplateId, 10);
            if ($textTemplateId>0) {
                $query = sprintf('SELECT MailTemplateId FROM %s WHERE TextTemplateId = %d LIMIT 0,1', $this->_tableName, $textTemplateId);
                $database->setQuery($query);
                $templateId = $database->loadResult();
                if ($database->getErrorNum()) {
                    trigger_error('ARI: Couldnt get mail template by text id.', E_USER_ERROR);
                    return null;
                }
                $template = $this->getTemplate($templateId, $group, $fullLoad);
            }
            return $template;
        }
        function getSingleTemplateByGroup($group, $fullLoad = true) {
            global $database;
            $errorMessage = 'ARI: Couldnt get mail template by group.';
            $ttc = $this->getTextTemplateController();
            $template = $this->createMailTemplateInstance();
            $query = sprintf('SELECT MT.*'.' FROM %1$s MT INNER JOIN %2$s TT'.'	ON MT.TextTemplateId = TT.TemplateId'.' INNER JOIN %2$sbase TTB'.'	ON TT.BaseTemplateId = TTB.BaseTemplateId'.' WHERE TTB.Group = %3$s'.' LIMIT 0,1', $this->_tableName, $ttc->getTablePrefix(), $database->Quote($group));
            $database->setQuery($query);
            $fields = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error($errorMessage, E_USER_ERROR);
                return null;
            }
            if (!empty($fields) && count($fields) >0) {
                if (!$template->bind($fields[0])) {
                    trigger_error($errorMessage, E_USER_ERROR);
                    return null;
                }
            }
            if ($template->TextTemplateId && $fullLoad) {
                $textTemplate = $this->_textTemplateController->getTemplate($template->TextTemplateId, $group);
                if ($this->_isError(true, false)) {
                    trigger_error($errorMessage, E_USER_ERROR);
                    return null;
                }
                if (empty($textTemplate)) {
                    return null;
                }
                $template->TextTemplate = $textTemplate;
            }
            return $template;
        }
        function getTemplate($templateId, $group = null, $fullLoad = true) {
            $errorMessage = 'ARI: Couldnt get mail template.';
            $template = $this->createMailTemplateInstance();
            $templateId = intval($templateId, 10);
            if (!$template->load($templateId)) {
                trigger_error($errorMessage, E_USER_ERROR);
                return null;
            }
            if ($fullLoad) {
                $textTemplate = $this->_textTemplateController->getTemplate($template->TextTemplateId, $group);
                if ($this->_isError(true, false)) {
                    trigger_error($errorMessage, E_USER_ERROR);
                    return null;
                }
                if (empty($textTemplate)) {
                    return null;
                }
                $template->TextTemplate = $textTemplate;
            }
            return $template;
        }
        function deleteTemplate($idList, $group = null) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $errorMessage = 'ARI: Couldnt delete mail templates.';
            $queryList = array();
            $idStr = join(',', $this->_quoteValues($idList));
            $query = sprintf('SELECT MT.MailTemplateId,GT.TemplateId'.' FROM %1$s MT INNER JOIN %2$s GT'.'	ON MT.TextTemplateId = GT.TemplateId'.' INNER JOIN %2$sbase GTB'.' 	ON GT.BaseTemplateId = GTB.BaseTemplateId'.' WHERE MT.MailTemplateId IN (%3$s) AND GTB.Group = %4$s', $this->_tableName, $this->_textTemplateController->getTablePrefix(), $idStr, $database->Quote($group));
            $database->setQuery($query);
            $idList = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error($errorMessage, E_USER_ERROR);
                return false;
            }
            if (empty($idList)) return true;
            $mailIdList = array();
            $textIdList = array();
            foreach($idList as $item) {
                $mailIdList[] = $item->MailTemplateId;
                $textIdList[] = $item->TemplateId;
            }
            $this->_textTemplateController->deleteTemplate($textIdList, $group);
            if ($this->_isError(true, false)) {
                trigger_error($errorMessage, E_USER_ERROR);
                return false;
            }
            $idStr = join(',', $this->_quoteValues($mailIdList));
            $query = sprintf('DELETE FROM %s WHERE MailTemplateId IN (%s)', $this->_tableName, $idStr);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error($errorMessage, E_USER_ERROR);
                return false;
            }
            return true;
        }
        function saveTemplate($mailTemplateId, $fields, $templateFields, $group, $ownerId) {
            global $database;
            $error = 'ARI: Couldnt save mail template.';
            $mailTemplateId = intval($mailTemplateId);
            $isUpdate = ($mailTemplateId>0);
            $row = $isUpdate ? $this->getTemplate($mailTemplateId) : $this->createMailTemplateInstance();
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            if (!$row->bind($fields)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $textTemplateController = $this->getTextTemplateController();
            $template = $textTemplateController->saveTemplate(AriUtils::getParam($row, 'TextTemplateId', 0), $templateFields, $group, $ownerId);
            if ($this->_isError(true, false)) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            $row->TextTemplate = $template;
            $row->TextTemplateId = $template->TemplateId;
            $row->AllowHtml = !empty($fields['AllowHtml']) ? 1 : 0;
            if (!$row->store()) {
                trigger_error($error, E_USER_ERROR);
                return null;
            }
            return $row;
        }
        function getTextTemplateGroup($mailTemplateId) {
            global $database;
            $mailTemplateId = @intval($mailTemplateId, 10);
            if ($mailTemplateId<1) return null;
            $query = sprintf('SELECT BTT.Group '.' FROM %1$s MT INNER JOIN %2$s TT'.'	 ON MT.TextTemplateId = TT.TemplateId'.' INNER JOIN %2$sbase BTT'.'	ON TT.BaseTemplateId = BTT.BaseTemplateId'.' WHERE MT.MailTemplateId = %3$d'.' LIMIT 0,1', $this->_tableName, $this->_textTemplateController->getTablePrefix(), $mailTemplateId);
            $database->setQuery($query);
            $group = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get mail template group.', E_USER_ERROR);
                return null;
            }
            return $group;
        }
    };
?>