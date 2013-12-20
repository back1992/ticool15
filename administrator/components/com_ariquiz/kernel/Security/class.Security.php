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
    class AriUserAccessHelperConstants extends AriClassConstants {
        var $Groups = array('Users'=>'USERS', 'Registered'=>'Registered', 'Frontend'=>'Public Frontend');
        var $Id = array('Registered'=>18);
        function getClassName() {
            return strtolower('AriUserAccessHelperConstants');
        }
    }
    new AriUserAccessHelperConstants();
    class AriUserAccessHelper extends AriObject {
        var $_acl;
        function __construct($acl) {
            $this->_acl = $acl;
        }
        function getGroupsFlatTree($group = null, $addEmpty = true) {
            if (is_null($group)) $group = AriConstantsManager::getVar('Groups.Users', AriUserAccessHelperConstants::getClassName());
            $gTree = $this->_acl->get_group_children_tree(null, $group, true);
            return $gTree;
        }
        function isChildOfGroupByName($childGroup, $parentGroup) {
            $acl = $this->_acl;
            return $acl->is_group_child_of($childGroup, $parentGroup);
        }
        function isChildOfGroup($childGroup, $parentGroupId) {
            $acl = $this->_acl;
            return $acl->is_group_child_of($childGroup, $acl->get_group_name($parentGroupId));
        }
        function isGroupOrChildOfGroup($childGroup, $parentGroupId) {
            $acl = $this->_acl;
            $parentGroup = $acl->get_group_name($parentGroupId);
            if ($parentGroup == $childGroup || $this->isChildOfGroupByName($childGroup, $parentGroup)) {
                return true;
            }
            return false;
        }
    };
?>