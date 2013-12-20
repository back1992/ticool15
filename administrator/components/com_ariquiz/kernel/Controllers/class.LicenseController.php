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
    class AriLicenseController extends AriControllerBase {
        function getCurrentLicense($domain, $component) {
            global $database;
            $query = sprintf('SELECT License FROM #__arilicense WHERE Domain = %s AND Component = %s ORDER BY EndDate DESC LIMIT 0,1', $database->Quote($domain), $database->Quote($component));
            $database->setQuery($query);
            $license = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get current license.', E_USER_ERROR);
                return null;
            }
            return $license;
        }
        function isLicenseExists($license) {
            global $database;
            $query = sprintf('SELECT COUNT(*) FROM #__arilicense WHERE License = %s', $database->Quote($license));
            $database->setQuery($query);
            $result = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt check that license key exists.', E_USER_ERROR);
                return true;
            }
            return !empty($result);
        }
        function getLicenseList($component, $domain = null) {
            global $database;
            $query = sprintf('SELECT LicenseId,Domain,License,Component,EndDate,(UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(EndDate)) AS IsExpired'.' FROM #__arilicense WHERE (LENGTH(%1$s) = 0 OR Domain = %1$s) AND Component = %2$s'.' ORDER BY Domain ASC,EndDate DESC', $database->Quote($domain), $database->Quote($component));
            $database->setQuery($query);
            $results = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get license list.', E_USER_ERROR);
                return null;
            }
            return $results;
        }
        function addLicense($license, $domain, $component, $endDate) {
            global $database;
            $query = sprintf('INSERT INTO #__arilicense (License,Domain,Component,EndDate) VALUES(%s,%s,%s,%s)', $database->Quote($license), $database->Quote($domain), $database->Quote($component), $database->Quote($endDate));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt add license.', E_USER_ERROR);
                return false;
            }
            return true;
        }
        function deleteLicense($idList, $component) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            global $database;
            $idStr = join(',', $this->_quoteValues($idList));
            $query = sprintf('DELETE FROM #__arilicense WHERE LicenseId IN (%s) AND Component = %s', $idStr, $database->Quote($component));
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt delete license.', E_USER_ERROR);
                return false;
            }
            return true;
        }
    };
?>