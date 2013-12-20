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
    class AriMambotProcessHelper extends AriObject {
        function processMambotTags($content, $addOutputContent = false) {
            if (empty($content)) return $content;
            if ($addOutputContent) {
                @ob_start();
            }
            $preContent = '';
            if (AriJoomlaBridge::isJoomla1_5()) {
                if (!class_exists('JDate')) $d = JFactory::getDate();
                $oldHeadData = null;
                if ($addOutputContent) {
                    $document = &JFactory::getDocument();
                    if ($document->getType() == 'html') {
                        $oldHeadData = $document->getHeadData();
                    }
                }
                $content = AriMambotProcessHelper::processJ15MambotTag($content);
                if ($addOutputContent) {
                    $document = &JFactory::getDocument();
                    if ($document->getType() == 'html') {
                        $newHeadData = $document->getHeadData();
                        $newScript = isset($newHeadData['script']) ? $newHeadData['script'] : array();
                        $newScripts = isset($newHeadData['scripts']) ? $newHeadData['scripts'] : array();
                        $newCustom = isset($newHeadData['custom']) ? $newHeadData['custom'] : array();
                        if (!empty($newScript) || !empty($newScripts) || !empty($newCustom)) {
                            if (empty($oldHeadData)) $oldHeadData = array();
                            $oldScript = isset($oldHeadData['script']) ? $oldHeadData['script'] : array();
                            $oldScripts = isset($oldHeadData['scripts']) ? $oldHeadData['scripts'] : array();
                            $oldCustom = isset($oldCustom['custom']) ? $oldCustom['custom'] : array();
                            foreach($newScripts as $script) {
                                if (!in_array($script, $oldScripts)) {
                                    $preContent.= sprintf('<script type="text/javascript" src="%s"></script>', $script);
                                }
                            }
                            foreach($newScript as $script) {
                                if (!in_array($script, $oldScript)) {
                                    $preContent.= sprintf('<script type="text/javascript">%s</script>', $script);
                                }
                            }
                            foreach($newCustom as $customTag) {
                                if (preg_match('~(<script.+?</script>)~si', $customTag) && !in_array($customTag, $oldCustom)) {
                                    $preContent.= $customTag;
                                }
                            }
                        }
                    }
                }
            } else {
                global $mainframe;
                $oldHeadData = null;
                if ($addOutputContent) {
                    $oldHeadData = $mainframe->_head['custom'];
                }
                $content = AriMambotProcessHelper::processJ10MambotTag($content);
                if ($addOutputContent) {
                    $newHeadData = $mainframe->_head['custom'];
                    if ($newHeadData && $oldHeadData) {
                        foreach($newHeadData as $headData) {
                            if (!in_array($headData, $oldHeadData)) {
                                $preContent.= $headData;
                            }
                        }
                    }
                }
            }
            if ($addOutputContent) {
                $content = @ob_get_contents() .$content;
                @ob_end_clean();
            }
            $content = $preContent.$content;
            return $content;
        }
        function processJ10MambotTag($content, $params = null) {
            global $_MAMBOTS;
            $_MAMBOTS->loadBotGroup('content');
            $isObject = is_object($content);
            if (is_null($params)) {
                $params = new mosParameters('');
            }
            $row = $content;
            if (!$isObject) {
                $row = new stdClass();
                $row->title = '';
                $row->text = $content;
            }
            $results = $_MAMBOTS->trigger('onPrepareContent', array(&$row, &$params, 0), true);
            return $isObject ? $row : $row->text;
        }
        function processJ15MambotTag($content, $params = null) {
            $dispatcher = &JDispatcher::getInstance();
            JPluginHelper::importPlugin('content', null, true);
            if (is_null($params)) {
                $params = new JParameter('');
            }
            $isObject = is_object($content);
            $row = $content;
            if (!$isObject) {
                $row = new stdClass();
                $row->title = '';
                $row->text = $content;
            }
            $dispatcher->trigger('onPrepareContent', array(&$row, &$params, 0), true);
            return $isObject ? $row : $row->text;
        }
        function processArticle($row) {
            $params = AriMambotProcessHelper::createParams();
            $params->def('image', 1);
            $params->def('intro_only', 1);
            $modRow = clone($row);
            $modRow->text = $row->introtext;
            if (AriJoomlaBridge::isJoomla1_5()) {
                $modRow = AriMambotProcessHelper::processJ15MambotTag($modRow, $params);
            } else {
                $modRow = AriMambotProcessHelper::processJ10MambotTag($modRow, $params);
            }
            $introText = $modRow->text;
            $modRow = clone($row);
            $params->set('intro_only', 0);
            $modRow->text = $row->fulltext;
            if (AriJoomlaBridge::isJoomla1_5()) {
                $modRow = AriMambotProcessHelper::processJ15MambotTag($modRow, $params);
            } else {
                $modRow = AriMambotProcessHelper::processJ10MambotTag($modRow, $params);
            }
            $row->introtext = $introText;
            $row->fulltext = $modRow->text;
            return $row;
        }
        function createParams() {
            $params = null;
            if (AriJoomlaBridge::isJoomla1_5()) {
                $params = new JParameter('');
            } else {
                $params = new mosParameters('');
            }
            return $params;
        }
    };
?>