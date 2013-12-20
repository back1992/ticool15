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
    AriKernel::import('Persistance.PersistanceController');
    class AriDataFilterConstants extends AriClassConstants {
        var $SortDir = array('ASC'=>'asc', 'DESC'=>'desc');
        var $RequestKey = array('StartOffset'=>'adtStart', 'Limit'=>'adtLimit', 'SortField'=>'adtSort', 'SortDirection'=>'adtDir', 'Init'=>'adtInit');
        function getClassName() {
            return strtolower('AriDataFilterConstants');
        }
    }
    new AriDataFilterConstants();
    class AriDataFilter extends AriObject {
        var $_configProps = array('sortField'=>null, 'sortDirection'=>'asc', 'secondarySorting'=>null, 'startOffset'=>0, 'limit'=>null, 'filter'=>null);
        var $_allowSortFields = null;
        var $_persistanceKey;
        var $_persistanceController;
        function __construct($config = null, $bindFromRequest = false, $persistanceKey = null, $allowSortFields = null) {
            $this->_allowSortFields = $allowSortFields;
            $this->_persistanceController = new AriPersistanceController();
            $this->_persistanceKey = $persistanceKey;
            $this->bindConfig($config);
            if ($bindFromRequest) {
                $constKey = AriDataFilterConstants::getClassName();
                $initKey = AriConstantsManager::getVar('RequestKey.Init', $constKey);
                $this->restore();
                if (empty($_REQUEST[$initKey])) {
                    $this->bindFromRequest();
                    $this->store();
                }
            }
        }
        function bindFromRequest() {
            $constKey = AriDataFilterConstants::getClassName();
            $reqKey = AriConstantsManager::getVar('RequestKey', $constKey);
            $startOffset = isset($_REQUEST[$reqKey['StartOffset']]) ? intval($_REQUEST[$reqKey['StartOffset']]) : 0;
            if ($startOffset<0) $startOffset = 0;
            $this->setConfigValue('startOffset', $startOffset);
            $limit = isset($_REQUEST[$reqKey['Limit']]) ? intval($_REQUEST[$reqKey['Limit']]) : 10;
            if ($limit<0) $limit = 10;
            $this->setConfigValue('limit', $limit);
            $sortField = isset($_REQUEST[$reqKey['SortField']]) ? $_REQUEST[$reqKey['SortField']] : null;
            $this->setConfigValue('sortField', $sortField);
            $sortDirection = isset($_REQUEST[$reqKey['SortDirection']]) ? $_REQUEST[$reqKey['SortDirection']] : null;
            $sortDirection = !empty($sortDirection) ? $sortDirection : 'asc';
            $this->setConfigValue('sortDirection', $sortDirection);
            $this->_fix();
        }
        function store($persistanceKey = null) {
            if (empty($persistanceKey)) $persistanceKey = $this->_persistanceKey;
            if (empty($persistanceKey)) return;
            $this->_persistanceController->call('overwritePersistance', $persistanceKey, $this->_configProps);
        }
        function restore($persistanceKey = null) {
            if (empty($persistanceKey)) $persistanceKey = $this->_persistanceKey;
            if (empty($persistanceKey)) return;
            $props = $this->_persistanceController->call('getPersistance', $persistanceKey);
            if (!empty($props['filter'])) $props['filter'] = @unserialize($props['filter']);
            if (empty($props['sortField'])) $props['sortField'] = $this->getConfigValue('sortField');
            if (empty($props['sortDirection'])) $props['sortDirection'] = $this->getConfigValue('sortDirection');
            $this->bindConfig($props);
            $this->_fix();
        }
        function _fix() {
            $sortField = $this->getConfigValue('sortField');
            $allowSortFields = $this->_allowSortFields;
            if ($sortField && is_array($allowSortFields) && !in_array($sortField, $allowSortFields)) {
                $this->setConfigValue('sortField', null);
            }
        }
        function fixFilter($cnt) {
            $limit = intval($this->getConfigValue('limit'), 10);
            if (empty($cnt) || $limit == 0) {
                $this->setConfigValue('startOffset', 0);
            } else {
                $startOffset = intval($this->getConfigValue('startOffset'), 10);
                if ($cnt <= $startOffset) {
                    $startOffset = $limit*(ceil($cnt/$limit) -1);
                } else {
                    $startOffset = $limit*floor($startOffset/$limit);
                }
                $this->setConfigValue('startOffset', $startOffset);
            }
        }
    };
?>