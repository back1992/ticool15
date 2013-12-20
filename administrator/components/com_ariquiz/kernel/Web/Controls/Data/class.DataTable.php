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
    AriKernel::import('Web.HtmlHelper');
    AriKernel::import('Web.Controls.Data.DataSource');
    AriKernel::import('Web.Controls.Data.DataTableColumn');
    AriKernel::import('Web.Controls.Data.DataTableSortOptions');
    AriKernel::import('Web.Controls.Data.Paginator');
    AriKernel::import('Web.JSON.JSONHelper');
    AriKernel::import('Web.Controls.Data._Templates.DataTableTemplates');
    class AriDataTableControl extends AriObject {
        var $id;
        var $_columns;
        var $_dataSource;
        var $_paginator;
        var $_scrolling;
        var $_events = array();
        var $_options = array('dynamicData'=>true, 'paginationEventHandler'=>null, 'caption'=>null, 'draggableColumns'=>false, 'height'=>null, 'width'=>null, 'initLoad'=>true, 'generateRequest'=>null, 'initialRequest'=>null, 'MSG_EMPTY'=>'No records found.');
        var $onloadJsHandler = null;
        function __construct($id, $columns, $dataSource, $options = null, $paginator = null, $scrolling = false) {
            $this->id = $id;
            $this->bindPropertiesToProperty($options, $this->_options);
            $this->_columns = $columns;
            $this->_dataSource = $dataSource;
            $this->_paginator = $paginator;
            $this->_scrolling = $scrolling;
        }
        function _getColumnsDef() {
            $jsColumns = array();
            foreach($this->_columns as $column) {
                $jsColumns[] = $column->getDef();
            }
            return '['.join(',', $jsColumns) .']';
        }
        function _getConfigDef() {
            $jsConfigData = array();
            $safeConfKeys = array('generateRequest', 'paginationEventHandler');
            foreach($this->_options as $key=>$value) {
                $isNeedEncode = true;
                if (in_array($key, $safeConfKeys) && !empty($value)) $isNeedEncode = false;
                $ajaxVal = $isNeedEncode ? AriJSONHelper::encode($value) : $value;
                $jsConfigData[$key] = $key.': '.$ajaxVal;
            }
            if ($this->_paginator) {
                $jsConfigData['paginator'] = 'paginator: '.$this->_paginator->getDef();
            }
            return sprintf('{%s}', join(',', $jsConfigData));
        }
        function _getEventsDef() {
            $jsEvents = array();
            if (count($this->_events) == 0) return '';
            foreach($this->_events as $key=>$events) {
                foreach($events as $event) {
                    $jsEvents[] = sprintf('%s.subscribe("%s", %s, %s)', $this->id, $key, $event['handler'], $event['obj']);
                }
            }
            return join(';', $jsEvents);
        }
        function render($attrs = array()) {
            $dsDef = $this->_dataSource->getDefenition();
            $colsDef = $this->_getColumnsDef();
            $confDef = $this->_getConfigDef();
            $eventsDef = $this->_getEventsDef();
            $onloadHandler = $this->onloadJsHandler ? $this->onloadJsHandler : '';
            printf(ARI_DATATABLE_TEMPLATE, $this->id, $colsDef, $dsDef, $confDef, $eventsDef, $onloadHandler, $this->_scrolling ? 'ScrollingDataTable' : 'DataTable');
            $this->_renderHtml($attrs);
        }
        function _renderHtml($attrs = array()) {
            $attrs['class'] = !empty($attrs['class']) ? $attrs['class'].' ' : '';
            $attrs['class'].= 'yui-skin-sam';
            printf('<div%s><div id="%s"></div></div>', AriHtmlHelper::getAttrStr($attrs), $this->id);
        }
        function subscribe($event, $handler, $obj = null) {
            if (!isset($this->_events[$event])) {
                $this->_events[$event] = array();
            }
            $this->_events[$event][] = array('handler'=>$handler, 'obj'=>$obj);
        }
    };
?>