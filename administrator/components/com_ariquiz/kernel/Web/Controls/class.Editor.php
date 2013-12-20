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
    AriKernel::import('Web.Controls.WebControl');
    class AriEditorWebControl extends AriWebControl {
        var $_text;
        function __construct($id, $config = null) {
            $this->extendConfig(array('translateText'=>true, 'trimValue'=>true, 'maxLength'=>null));
            parent::__construct($id, $config);
        }
        function setValue($value) {
            $this->setText($value);
        }
        function setText($text) {
            $this->_text = $text;
        }
        function getText() {
            $text = $this->isTrimValue() ? trim($this->_text) : $this->_text;
            $maxLength = $this->getMaxLength();
            if (!is_null($maxLength)) $text = substr($text, 0, $maxLength);
            return $text;
        }
        function getMaxLength() {
            return $this->getConfigValue('maxLength');
        }
        function setMaxLength($maxLength) {
            $this->setConfigValue('maxLength', $maxLength);
        }
        function isTrimValue() {
            return $this->getConfigValue('trimValue');
        }
        function setIsTrimValue($isTrimValue) {
            $this->setConfigValue('trimValue', $isTrimValue ? true : false);
        }
        function getValidateValue() {
            return $this->getText();
        }
        function getContent() {
            if (AriJoomlaBridge::isJoomla1_5()) {
                $correctedName = $this->getCorrectedName();
                $editor = &JFactory::getEditor();
                $content = $editor->getContent($correctedName);
                $content = str_replace('tinyMCE.getContent()', sprintf('tinyMCE.getContent("%s")', $correctedName), $content);
                $content = str_replace('tinyMCE.activeEditor.getContent()', sprintf('tinyMCE.get("%s").getContent()', $correctedName), $content);
                $content = str_replace(sprintf('JContentEditor.getContent(\'%s\')', $correctedName), sprintf('(tinyMCE.get("%s") ? tinyMCE.get("%1$s").getContent() : JContentEditor.getContent(\'%1$s\'))', $correctedName), $content);
                return $content;
            } else {
                $script = '';
                @ob_start();
                getEditorContents($this->getId(), $this->getName());
                $script = ob_get_contents();
                @ob_clean();
                @ob_end_flush();
                return sprintf('(function(){%s; var el = document.getElementById("%s"); return el ? el.value : null; })()', $script, $this->getName());
            }
        }
        function getCorrectedName() {
            return str_replace(array('[', ']'), array('_', ''), $this->getName());
        }
        function render($attrs = null) {
            $width = AriUtils::getParam($attrs, 'width', '100%;');
            $height = AriUtils::getParam($attrs, 'height', '250');
            $rows = AriUtils::getParam($attrs, 'rows', '20');
            $cols = AriUtils::getParam($attrs, 'cols', '60');
            if (AriJoomlaBridge::isJoomla1_5()) {
                $ctrlName = $this->getName();
                $needHack = (strpos($ctrlName, '[') !== false);
                $correctedCtrlName = $this->getCorrectedName();
                $editor = &JFactory::getEditor();
                echo $editor->display($correctedCtrlName, $this->getText(), $width, $height, $cols, $rows);
                if ($needHack) {
                    printf('<textarea name="%1$s" id="%2$s" style="display: none !important;"></textarea>', $ctrlName, $this->getId());
                    $document = &JFactory::getDocument();
                    $document->addScriptDeclaration(sprintf('window.addEvent("domready", function() {var oldSubmitHandler = submitform; submitform = function() { $("%1$s").value = %2$s; oldSubmitHandler.apply(this, arguments); } });', $this->getId(), $this->getContent()));
                }
            } else {
                editorArea($this->getId(), $this->getText(), $this->getName(), $width, $height, $cols, $rows);
            }
        }
    };
?>