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
    class AriUserController extends AriControllerBase {
        function getUserCount($filter = null) {
            global $database;
            $query = 'SELECT COUNT(*) FROM #__users';
            $database->setQuery($query);
            $cnt = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get user count.', E_USER_ERROR);
                return 0;
            }
            return $cnt;
        }
        function getUserList($filter = null) {
            global $database;
            $query = 'SELECT id AS UserId,name AS Name,username AS LoginName FROM #__users';
            $query = $this->_applyFilter($query, $filter);
            $database->setQuery($query);
            $users = $database->loadObjectList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get user list.', E_USER_ERROR);
                return null;
            }
            return $users;
        }
        function getUser($userId) {
            global $database;
            $userId = @intval($userId, 10);
            if ($userId<1) return null;
            $query = 'SELECT * FROM #__users WHERE id='.$userId.' LIMIT 0,1';
            $database->setQuery($query);
            $user = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get user.', E_USER_ERROR);
                return null;
            }
            $user = count($user) >0 ? $user[0] : null;
            return $user;
        }
        function getUserByUsername($username) {
            global $database;
            if ($username) $username = trim($username);
            if (!$username) return null;
            $query = 'SELECT * FROM #__users WHERE username='.$database->Quote($username) .' LIMIT 0,1';
            $database->setQuery($query);
            $user = $database->loadAssocList();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt get user by name.', E_USER_ERROR);
                return null;
            }
            $user = count($user) >0 ? $user[0] : null;
            return $user;
        }
        function hasUsername($username) {
            global $database;
            if ($username) $username = trim($username);
            if (!$username) return false;
            $query = 'SELECT COUNT(*) FROM #__users WHERE username='.$database->Quote($username);
            $database->setQuery($query);
            $cnt = $database->loadResult();
            if ($database->getErrorNum()) {
                trigger_error('ARI: Couldnt check user name.', E_USER_ERROR);
                return false;
            }
            return ($cnt>0);
        }
    };
?>