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
    AriKernel::import('Web.Page.Specific.AdminPageBase');
    class debugAriPage extends AriAdminPageBase {
        function execute() {
            $dbDate = $this->getDbUTCDate();
            $phpDate = ArisDate::getDbUTC();
            $this->addVar('dbDate', $dbDate);
            $this->addVar('phpDate', $phpDate);
            $this->setTitle('Debug');
            parent::execute();
        }
        function getDbUTCDate() {
            global $database;
            $query = 'SELECT UNIX_TIMESTAMP(UTC_TIMESTAMP())';
            $database->setQuery($query);
            $ts = $database->loadResult();
            return date('Y-m-d H:i:s', $ts);
        }
    };
?>