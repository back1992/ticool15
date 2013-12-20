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
    AriKernel::import('Web.Controls.Toolbar.ToolbarAbstract');
    class AriJToolbar10 extends AriToolbarAbstract {
        function startToolbar() {
            mosMenuBar::startTable();
        }
        function endToolbar() {
            mosMenuBar::endTable();
        }
        function spacer($width = '') {
            mosMenuBar::spacer($width);
        }
        function divider() {
            mosMenuBar::divider();
        }
        function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false) {
            mosMenuBar::custom($task, $icon, $iconOver, $alt, $listSelect);
        }
        function customX($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true) {
            mosMenuBar::customX($task, $icon, $iconOver, $alt, $listSelect);
        }
        function preview($url = '', $updateEditors = false) {
            mosMenuBar::preview($url, $updateEditors);
        }
        function help($ref, $com = false) {
            mosMenuBar::help($ref, $com);
        }
        function back($alt = 'Back', $href = 'javascript:history.back();') {
            mosMenuBar::back($alt, $href);
        }
        function media_manager($directory = '', $alt = 'Upload') {
            mosMenuBar::media_manager($directory, $alt);
        }
        function addNew($task = 'new', $alt = 'New') {
            mosMenuBar::addNew($task, $alt);
        }
        function addNewX($task = 'new', $alt = 'New') {
            mosMenuBar::addNewX($task, $alt);
        }
        function publish($task = 'publish', $alt = 'Publish') {
            mosMenuBar::publish($task, $alt);
        }
        function publishList($task = 'publish', $alt = 'Publish') {
            mosMenuBar::publishList($task, $alt);
        }
        function makeDefault($task = 'default', $alt = 'Default') {
            mosMenuBar::makeDefault($task, $alt);
        }
        function assign($task = 'assign', $alt = 'Assign') {
            mosMenuBar::assign($task, $alt);
        }
        function unpublish($task = 'unpublish', $alt = 'Unpublish') {
            mosMenuBar::unpublish($task, $alt);
        }
        function unpublishList($task = 'unpublish', $alt = 'Unpublish') {
            mosMenuBar::unpublishList($task, $alt);
        }
        function save($task = 'save', $alt = 'Save') {
            mosMenuBar::save($task, $alt);
        }
        function apply($task = 'apply', $alt = 'Apply') {
            mosMenuBar::apply($task, $alt);
        }
        function cancel($task = 'cancel', $alt = 'Cancel') {
            mosMenuBar::cancel($task, $alt);
        }
        function deleteList($msg = '', $task = 'remove', $alt = 'Delete') {
            mosMenuBar::deleteList($msg, $task, $alt);
        }
        function deleteListX($msg = '', $task = 'remove', $alt = 'Delete') {
            mosMenuBar::deleteListX($msg, $task, $alt);
        }
    };
?>