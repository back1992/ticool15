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
    class AriQuizResultScaleController extends AriControllerBase {
        function getScaleList($filter = null) {
            global $database;
            $query = 'SELECT ScaleId,ScaleName FROM #__ariquiz_result_scale';
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $scales = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get result scale list.', E_USER_ERROR);
                return null;
            }
            return $scales;
        }
        function getScaleCount($filter = null) {
            global $database;
            $query = 'SELECT COUNT(*) FROM #__ariquiz_result_scale';
            $query = $this->_applyDbCountFilter($query, $filter);
            $database->setQuery($query);
            $cnt = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get result scale count.', E_USER_ERROR);
                return 0;
            }
            return $cnt;
        }
        function createScaleInstance() {
            return AriEntityFactory::createInstance('AriQuizScaleEntity', AriGlobalPrefs::getEntityGroup());
        }
        function createScaleItemInstance() {
            return AriEntityFactory::createInstance('AriQuizScaleItemEntity', AriGlobalPrefs::getEntityGroup());
        }
        function getScale($scaleId, $fullLoad = true) {
            $scaleId = @intval($scaleId, 10);
            $errorMessage = 'ARI: Couldnt get result scale.';
            $scale = $this->createScaleInstance();
            if ($scaleId>0) {
                if (!$scale->load($scaleId)) {
                    trigger_error($errorMessage, E_USER_ERROR);
                    return null;
                }
                $scale->ScaleItems = array();
                if ($fullLoad) {
                    global $database;
                    $query = sprintf('SELECT * FROM #__ariquiz_result_scale_item WHERE ScaleId = %d ORDER BY BeginPoint', $scaleId);
                    $database->setQuery($query);
                    $scaleItems = $database->loadAssocList();
                    if ($database->getErrorNum()) {
                        trigger_error($errorMessage, E_USER_ERROR);
                        return null;
                    }
                    if ($scaleItems) {
                        foreach($scaleItems as $scaleItem) {
                            $scaleItemEntity = $this->createScaleItemInstance();
                            $scaleItemEntity->bind($scaleItem);
                            $scale->ScaleItems[] = $scaleItemEntity;
                        }
                    }
                }
            }
            return $scale;
        }
        function saveScale($scaleId, $fields, $subFields, $ownerId) {
            global $database;
            $error = 'ARI: Couldnt save result scale.';
            $scaleId = @intval($scaleId, 10);
            $isUpdate = ($scaleId>0);
            if ($scaleId>0) {
                if (!$this->deleteScaleItems($scaleId)) {
                    trigger_error($error, E_USER_ERROR);
                    return null;
                }
            }
            $row = $isUpdate ? $this->getScale($scaleId) : $this->createScaleInstance();
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
            if (!$this->saveScaleItems($row->ScaleId, $subFields)) {
                trigger_error($error, E_USER_ERROR);
            }
            return $row;
        }
        function saveScaleItems($scaleId, $fields) {
            global $database;
            $scaleId = @intval($scaleId, 10);
            if ($scaleId<1 || !is_array($fields) || count($fields) <1) return true;
            $query = 'INSERT INTO #__ariquiz_result_scale_item (ScaleItemId,ScaleId,BeginPoint,`EndPoint`,TextTemplateId,MailTemplateId,PrintTemplateId) VALUES ';
            $values = array();
            foreach($fields as $item) {
                $beginPoint = @intval($item['tbxStartPoint'], 10);
                $endPoint = @intval($item['tbxEndPoint'], 10);
                if ($beginPoint<1 && $endPoint<1) continue;
                if ($beginPoint>$endPoint) {
                    $tPoint = $beginPoint;
                    $beginPoint = $endPoint;
                    $endPoint = $tPoint;
                }
                $emailTemplateId = @intval($item['lbEmailTemplate'], 10);
                $printTemplateId = @intval($item['lbPrintTemplate'], 10);
                $textTemplateId = @intval($item['lbTextTemplate'], 10);
                $values[] = sprintf('(NULL,%d,%d,%d,%d,%d,%d)', $scaleId, $beginPoint, $endPoint, $textTemplateId, $emailTemplateId, $printTemplateId);
            }
            if (empty($values)) return true;
            $query.= join(',', $values);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                echo $database->getErrorMsg();
                exit ();
                trigger_error('ARI: Couldnt save result scale items.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function deleteScaleItems($scaleId) {
            $scaleId = @intval($scaleId, 10);
            if ($scaleId<1) return true;
            global $database;
            $query = sprintf('DELETE FROM #__ariquiz_result_scale_item WHERE ScaleId=%d', $scaleId);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldt delete result scale items.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function deleteScale($idList) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $queryList = array();
            $idStr = join(',', $this->_quoteValues($idList));
            $queryList[] = sprintf('DELETE FROM #__ariquiz_result_scale WHERE ScaleId IN (%s)', $idStr);
            $queryList[] = sprintf('DELETE FROM #__ariquiz_result_scale_item WHERE ScaleId IN (%s)', $idStr);
            $database->setQuery(join($queryList, ';'));
            if (AriJoomlaBridge::isJoomla1_5()) $database->queryBatch();
        else $database->query_batch();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt delete result scales.', E_USER_ERROR);
            return false;
        }
        return true;
    }
    function getScaleItemByScore($scaleId, $score) {
        global $database;
        $scaleId = @intval($scaleId, 10);
        $score = @intval($score, 10);
        $scaleItem = $this->createScaleItemInstance();
        $query = sprintf('SELECT * FROM #__ariquiz_result_scale_item WHERE ScaleId = %1$d AND %2$s >= BeginPoint AND %2$s <= EndPoint ORDER BY BeginPoint DESC LIMIT 0,1', $scaleId, $score);
        $database->setQuery($query);
        $list = $database->loadAssocList();
        if ($database->getErrorNum()) {
            trigger_error('ARI: Couldnt get scale item by score.', E_USER_ERROR);
            return null;
        }
        if (!empty($list) && count($list) >0) $scaleItem->bind($list[0]);
        return $scaleItem;
    }
    };
?>