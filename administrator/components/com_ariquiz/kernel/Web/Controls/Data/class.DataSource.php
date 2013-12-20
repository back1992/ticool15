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
    AriKernel::import('Web.Controls.Data._Templates.DataSourceTemplates');
    class AriDataSourceControlConstants extends AriClassConstants {
        var $ResponseType = array('TYPE_HTMLTABLE'=>'YAHOO.util.DataSource.TYPE_HTMLTABLE', 'TYPE_JSARRAY'=>'YAHOO.util.DataSource.TYPE_JSARRAY', 'TYPE_JSON'=>'YAHOO.util.DataSource.TYPE_JSON', 'TYPE_TEXT'=>'YAHOO.util.DataSource.TYPE_TEXT', 'TYPE_XML'=>'YAHOO.util.DataSource.TYPE_XML');
        function getClassName() {
            return strtolower('AriDataSourceControlConstants');
        }
    }
    new AriDataSourceControlConstants();
    class AriDataSourceControl extends AriObject {
        var $connMethodPost = false;
        var $responseType;
        var $responseShema;
        var $id;
        var $_data;
        function __construct($data, $options) {
            $this->id = uniqid('ds');
            $this->bindProperties($options);
            $this->_data = $data;
        }
        function render() {
            $def = $this->getDefenition();
            printf('var %s = %s;', $this->id, $def);
        }
        function getDefenition() {
            return sprintf(ARI_DATASOURCEDEF_TEMPLATE, $this->_data, $this->responseType, $this->responseShema);
        }
    };
?>