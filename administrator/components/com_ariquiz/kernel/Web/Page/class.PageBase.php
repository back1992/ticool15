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
    AriKernel::import('Web.Request');
    AriKernel::import('Web.Page.PageHelper');
    class AriPageBase extends AriObject {
        var $executionTask;
        var $_infoMessage = '';
        var $_template;
        var $_variables = array();
        var $_eventsMapping = array();
        var $_ajaxMapping = array();
        var $_validators = array();
        var $_controls = array();
        function __construct($executionTask, $template = null, $event = null, $eventArgs = null) {
            $pageHelper = &AriPageHelper::getInstance();
            $pageHelper->setCurrentPage($this);
            $this->executionTask = $executionTask;
            $this->_template = $template;
            $this->_registerAjaxHandlers();
            $this->_registerEventHandlers();
            $this->_registerErrorHandler();
            $this->_init();
            $this->_callAjaxHandler($this->_getAjaxEvent($event), $eventArgs);
            $this->_preLoad();
            $this->_checkAriMsg();
            $this->_raiseEvent($event, $eventArgs);
        }
        function _isAjaxEvent($event) {
            return (strpos($event, 'ajax|') === 0);
        }
        function _getAjaxEvent($event) {
            return $this->_isAjaxEvent($event) ? substr($event, strlen('ajax|')) : null;
        }
        function _checkAriMsg() {
            $msgId = AriRequest::getParam('arimsg', '');
            if (!empty($msgId)) {
                $this->setInfoMessage(AriWebHelper::translateResValue($msgId));
            }
        }
        function setInfoMessage($message) {
            $this->_infoMessage = $message;
        }
        function getInfoMessage() {
            return $this->_infoMessage;
        }
        function sendResponse($data, $charset = null) {
            while (@ob_end_clean());
            if ($charset) header('Content-type: text/html; charset='.$charset);
            echo $data;
            exit ();
        }
        function sendBinaryRespose($data, $type = 'application/octet-stream') {
            while (@ob_end_clean());
            if ($type) header('Content-Type: '.$type);
            echo $data;
            exit ();
        }
        function addValidator(&$validator) {
            $this->_validators[$validator->getId() ] = &$validator;
        }
        function validate($valGroup = null) {
            $isValid = true;
            if (!is_array($this->_validators) || count($this->_validators) <1) return $isValid;
            foreach($this->_validators as $id=>$val) {
                $validator = &$this->_validators[$id];
                if ($validator->getEnabled() && $validator->inGroup($valGroup)) {
                    if (!$validator->validate()) {
                        $isValid = false;
                    }
                }
            }
            return $isValid;
        }
        function &getFailedValidators($valGroup = null) {
            $validators = array();
            if (!is_array($this->_validators) || count($this->_validators) <1) return $validators;
            foreach($this->_validators as $id=>$val) {
                $validator = &$this->_validators[$id];
                if ($validator->getEnabled() && $validator->inGroup($valGroup) && !$validator->getIsValid()) {
                    $validators[] = &$validator;
                }
            }
            return $validators;
        }
        function execute() {
            $processPage = &$this;
            if (!empty($this->_template) && file_exists($this->_template)) {
                require_once $this->_template;
                $this->_registerJsValidators();
            }
        }
        function addVar($name, &$value) {
            $this->_variables[$name] = &$value;
        }
        function &getVar($name) {
            $var = null;
            if (isset($this->_variables[$name])) {
                $var = &$this->_variables[$name];
            } else if (isset($GLOBALS[$name])) {
                $var = $GLOBALS[$name];
            }
            return $var;
        }
        function addControl(&$control) {
            $this->_controls[$control->getId() ] = &$control;
        }
        function &getControl($id) {
            $control = null;
            if (isset($this->_controls[$id])) $control = &$this->_controls[$id];
            return $control;
        }
        function renderControl($id, $attrs = null) {
            $control = &$this->getControl($id);
            if ($control) $control->render($attrs);
        }
        function _registerEventHandler($event, $handler) {
            $this->_eventsMapping[$event] = $handler;
        }
        function _registerEventHandlers() {
        }
        function _raiseEvent($event, $eventArgs) {
            if ($event && isset($this->_eventsMapping[$event])) {
                $handler = $this->_eventsMapping[$event];
                $this->$handler($eventArgs);
            }
        }
        function _registerAjaxHandler($method, $handler) {
            $this->_ajaxMapping[$method] = $handler;
        }
        function _registerAjaxHandlers() {
        }
        function _callAjaxHandler($method, $eventArgs) {
            if ($method && isset($this->_ajaxMapping[$method])) {
                while (@ob_end_clean());
                $handler = $this->_ajaxMapping[$method];
                $this->$handler($eventArgs);
                exit ();
            }
        }
        function _registerErrorHandler() {
            set_error_handler(array(&$this, 'errorHandler'));
        }
        function _isError($clear = TRUE, $raised = TRUE) {
            $error = $this->_lastError;
            $isError = $error !== null;
            if ($isError) {
                if ($clear) {
                    $this->_lastError = null;
                }
                if ($raised) {
                    $this->_raiseError($error);
                }
            }
            return $isError;
        }
        function _raiseError($error) {
            echo '<script language="javascript" type="text/javascript">alert("'.str_replace("'", "\\'", $error->error) .'")</script>';
        }
        function _createControls() {
        }
        function _createValidators() {
        }
        function _init() {
        }
        function _preLoad() {
            $this->loadControls();
        }
        function loadControls() {
            static $isLoaded;
            if ($isLoaded) return;
            $this->_createControls();
            $this->_createValidators();
            $isLoaded = true;
        }
        function _registerJsValidators() {
            if ($this->_validators && count($this->_validators) >0) {
                echo '<script type="text/javascript" language="javascript">';
                foreach($this->_validators as $validator) {
                    $validator->renderJsValidator();
                    echo ';';
                }
                echo '</script>';
            }
        }
    };
?>