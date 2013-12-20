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
    AriKernel::import('Web.Controls.Data.DataTable');
    class AriMultiPageDataTableControl extends AriDataTableControl {
        var $_specOptions;
        function __construct($id, $columns, $options = null, $paginatorOptions = null, $scrolling = null, $mainTable = true) {
            if (!isset($options[''])) $this->_specOptions = $options;
            if (!$paginatorOptions) $paginatorOptions = array();
            $paginatorOptions['containers'] = $id.'_pag';
            $paginator = new AriPaginatorControl($paginatorOptions);
            $dataSource = $this->_createDataSource($columns);
            $initialRequest = $this->_getOptionValue('initialRequest', '&adtInit=1');
            $initialRequest.= '&t='.time();
            parent::__construct($id, $columns, $dataSource, array('initialRequest'=>$initialRequest, 'generateRequest'=>$this->_getOptionValue('generateRequest', 'YAHOO.ARISoft.widgets.dataTable.generateRequest'), 'paginationEventHandler'=>$this->_getOptionValue('paginationEventHandler', 'YAHOO.widget.DataTable.handleDataSourcePagination'), 'width'=>$this->_getOptionValue('width'), 'height'=>$this->_getOptionValue('height'), 'MSG_EMPTY'=>$this->_getOptionValue('MSG_EMPTY', AriWebHelper::translateResValue('Controls.DTNoRecords'))), $paginator, $scrolling);
            $this->onloadJsHandler = sprintf('%2$s;%1$s.doBeforeLoadData = YAHOO.ARISoft.widgets.dataTable.doBeforeLoadData;YAHOO.ARISoft.widgets.dataTable.checkboxHelpers.initSelectAll(%1$s);', $id, $mainTable ? sprintf('%1$s.mainJoomlaTable = true; var cont = %1$s;if (cont) YAHOO.util.Dom.addClass(cont, "mainJoomlaTable");', $id) : '');
        }
        function _createDataSource($columns) {
            $dsConstKey = AriDataSourceControlConstants::getClassName();
            $dataFields = $this->_getOptionValue('dataFields', null);
            if (is_null($dataFields) && is_array($columns)) {
                $dataFields = array();
                foreach($columns as $column) {
                    $dataFields[] = $column->getConfigValue('key');
                }
            }
            if (!is_array($dataFields)) $dataFields = array();
            $dataSource = new AriDataSourceControl('"'.$this->_getOptionValue('dataUrl', '') .'"', array('responseType'=>AriConstantsManager::getVar('ResponseType.TYPE_JSON', $dsConstKey), 'responseShema'=>'{resultsList: "records", fields: '.AriJSONHelper::encode($dataFields) .', metaFields: {totalRecords: "totalRecords", paginationRecordOffset: "startIndex", paginationRowsPerPage: "limit", sortKey: "sort", sortDir: "dir"}}'));
            return $dataSource;
        }
        function _getOptionValue($key, $defaultValue = null) {
            return isset($this->_specOptions[$key]) ? $this->_specOptions[$key] : $defaultValue;
        }
        function _renderHtml($attrs = array()) {
            $attrs['class'] = !empty($attrs['class']) ? $attrs['class'].' ' : '';
            $attrs['class'].= 'yui-skin-sam';
            printf('<div%2$s><div id="%1$s"></div><div id="%1$s_pag"></div></div>', $this->id, AriHtmlHelper::getAttrStr($attrs));
        }
        function createDataInfo($data, $filter, $cnt) {
            return array('records'=>$data, 'totalRecords'=>intval($cnt), 'startIndex'=>intval($filter->getConfigValue('startOffset')), 'limit'=>intval($filter->getConfigValue('limit')), 'sort'=>$filter->getConfigValue('sortField'), 'dir'=>$filter->getConfigValue('sortDirection'));
        }
    };
?>