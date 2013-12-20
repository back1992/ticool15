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
    class AriToolbarAbstract extends AriObject {
        function resourceTitle($resKey, $img = 'generic.png') {
            $this->title(AriWebHelper::translateResValue($resKey), $img);
        }
        function title($title, $icon = 'generic.png') {
        }
        function startToolbar() {
        }
        function endToolbar() {
        }
        function spacer($width = '') {
        }
        function divider() {
        }
        function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false) {
        }
        function customX($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true) {
        }
        function preview($url = '', $updateEditors = false) {
        }
        function help($ref, $com = false) {
        }
        function back($alt = 'Back', $href = 'javascript:history.back();') {
        }
        function media_manager($directory = '', $alt = 'Upload') {
        }
        function addNew($task = 'add', $alt = 'New') {
        }
        function addNewX($task = 'add', $alt = 'New') {
        }
        function publish($task = 'publish', $alt = 'Publish') {
        }
        function publishList($task = 'publish', $alt = 'Publish') {
        }
        function makeDefault($task = 'default', $alt = 'Default') {
        }
        function assign($task = 'assign', $alt = 'Assign') {
        }
        function unpublish($task = 'unpublish', $alt = 'Unpublish') {
        }
        function unpublishList($task = 'unpublish', $alt = 'Unpublish') {
        }
        function save($task = 'save', $alt = 'Save') {
        }
        function apply($task = 'apply', $alt = 'Apply') {
        }
        function cancel($task = 'cancel', $alt = 'Cancel') {
        }
        function deleteList($msg = '', $task = 'remove', $alt = 'Delete') {
        }
        function deleteListX($msg = '', $task = 'remove', $alt = 'Delete') {
        }
    };
?>