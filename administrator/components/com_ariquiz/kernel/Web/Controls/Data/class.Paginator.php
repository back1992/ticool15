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
    AriKernel::import('Web.JSON.JSONHelper');
    class AriPaginatorControl extends AriObject {
        var $id;
        var $_options = array('alwaysVisible'=>true, 'containers'=>null, 'containerClass'=>'yui-pg-container', 'initialPage'=>1, 'pageLinksStart'=>1, 'recordOffset'=>0, 'firstPageLinkLabel'=>null, 'lastPageLinkLabel'=>null, 'nextPageLinkLabel'=>null, 'previousPageLinkLabel'=>null, 'pageReportTemplate'=>null, 'rowsPerPageDropdownClass'=>'text_area', 'rowsPerPage'=>10, 'template'=>'Display#: {RowsPerPageDropdown} {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} {CurrentPageReport}', 'totalRecords'=>0, 'updateOnChange'=>false, 'rowsPerPageOptions'=>array(5, 10, 15, 20, 25, 30, 50, 100));
        function __construct($options = null) {
            $this->id = uniqid('pag');
            $this->_bindResources();
            $this->bindPropertiesToProperty($options, $this->_options);
        }
        function _bindResources() {
            $this->bindPropertiesToProperty(array('firstPageLinkLabel'=>AriWebHelper::translateResValue('Controls.DTFirstPage'), 'lastPageLinkLabel'=>AriWebHelper::translateResValue('Controls.DTLastPage'), 'nextPageLinkLabel'=>AriWebHelper::translateResValue('Controls.DTNextPage'), 'previousPageLinkLabel'=>AriWebHelper::translateResValue('Controls.DTPrevPage'), 'pageReportTemplate'=>AriWebHelper::translateResValue('Controls.DTPageReportTemplate'), 'template'=>AriWebHelper::translateResValue('Controls.DTTemplate')), $this->_options);
        }
        function getDef() {
            return 'new YAHOO.widget.Paginator('.AriJSONHelper::encode($this->_options) .')';
        }
    };
?>