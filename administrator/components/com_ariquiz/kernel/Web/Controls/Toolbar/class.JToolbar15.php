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
    class AriJToolbar15 extends AriToolbarAbstract {
        function resourceTitle($resKey, $img = 'generic.png') {
            AriJToolbar15::title(AriWebHelper::translateResValue($resKey), $img);
        }
        function title($title, $icon = 'generic.png') {
            JToolBarHelper::title($title, $icon);
        }
        function spacer($width = '') {
            JToolBarHelper::spacer($width);
        }
        function divider() {
            JToolBarHelper::divider();
        }
        function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false) {
            JToolbarHelper::custom($task, $icon, $iconOver, $alt, $listSelect, $x);
        }
        function customX($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true) {
            JToolbarHelper::custom($task, $icon, $iconOver, $alt, $listSelect, true);
        }
        function preview($url = '', $updateEditors = false) {
            JToolbarHelper::preview($url, $updateEditors);
        }
        function help($ref, $com = false) {
            JToolbarHelper::help($ref, $com);
        }
        function back($alt = 'Back', $href = 'javascript:history.back();') {
            JToolbarHelper::back($alt, $href);
        }
        function media_manager($directory = '', $alt = 'Upload') {
            JToolbarHelper::media_manager($directory, $alt);
        }
        function addNew($task = 'add', $alt = 'New') {
            JToolbarHelper::addNew($task, $alt);
        }
        function addNewX($task = 'add', $alt = 'New') {
            JToolbarHelper::addNewX($task, $alt);
        }
        function publish($task = 'publish', $alt = 'Publish') {
            JToolbarHelper::publish($task, $alt);
        }
        function publishList($task = 'publish', $alt = 'Publish') {
            JToolbarHelper::publishList($task, $alt);
        }
        function makeDefault($task = 'default', $alt = 'Default') {
            JToolbarHelper::makeDefault($task, $alt);
        }
        function assign($task = 'assign', $alt = 'Assign') {
            JToolbarHelper::assign($task, $alt);
        }
        function unpublish($task = 'unpublish', $alt = 'Unpublish') {
            JToolbarHelper::unpublish($task, $alt);
        }
        function unpublishList($task = 'unpublish', $alt = 'Unpublish') {
            JToolbarHelper::unpublishList($task, $alt);
        }
        function save($task = 'save', $alt = 'Save') {
            JToolbarHelper::save($task, $alt);
        }
        function apply($task = 'apply', $alt = 'Apply') {
            JToolbarHelper::apply($task, $alt);
        }
        function cancel($task = 'cancel', $alt = 'Cancel') {
            JToolbarHelper::cancel($task, $alt);
        }
        function deleteList($msg = '', $task = 'remove', $alt = 'Delete') {
            JToolbarHelper::deleteList($msg, $task, $alt);
        }
        function deleteListX($msg = '', $task = 'remove', $alt = 'Delete') {
            JToolbarHelper::deleteListX($msg, $task, $alt);
        }
    };
?>