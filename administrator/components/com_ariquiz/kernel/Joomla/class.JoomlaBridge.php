<?php

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

class AriJoomlaBridge extends AriObject {

    function isJoomla1_5() {
        static $isJoomla1_5 = null;
        if (is_null($isJoomla1_5)) {
            $isJoomla1_5 = class_exists('jconfig');
        } return $isJoomla1_5;
    }

    function getLink($link, $xhtml = false, $clearItemId = true, $addTmpl = true) {
        if (!AriJoomlaBridge::isJoomla1_5()) {
            if (function_exists('sefRelToAbs'))
                $link = sefRelToAbs($link); if (!$xhtml)
                $link = str_replace('&amp;', '&', $link);
        } else {
            $app = &JFactory::getApplication();
            $router = &$app->getRouter();
            if ($router->getMode() == JROUTER_MODE_SEF && $clearItemId) {
                $itemidPos = strpos($link, 'Itemid');
                if ($itemidPos !== false) {
                    $link = preg_replace('/Itemid(?:=[^&;]*)?/', '', $link);
                }
            } if ($addTmpl && strpos($link, 'tmpl=') === false) {
                $tmpl = AriRequest::getParam('tmpl');
                if ($tmpl) {
                    if (strpos($link, '&') !== false)
                        $link .= '&'; else if (strpos($link, '?') === false)
                        $link .= '?'; $link .= 'tmpl=' . $tmpl;
                }
            } $link = JRoute::_($link, $xhtml);
        } return $link;
    }

    function doCompatibility() {
        if (AriJoomlaBridge::isJoomla1_5()) {
            AriJoomlaBridge::_doCompatibility1_0();
        } else {
            AriJoomlaBridge::_doCompatibility1_5();
        }
    }

    function _doCompatibility1_5() {
        defined('_JEXEC') or define('_JEXEC', 1);
    }

    function _doCompatibility1_0() {
        defined('_VALID_MOS') or define('_VALID_MOS', 1);
        $mainframe = & JFactory::getApplication();
        $GLOBALS['mainframe'] = & $mainframe;
        $user = & JFactory::getUser();
        $GLOBALS['my'] = & $user;
        $acl = & JFactory::getACL();
        $GLOBALS['acl'] = & $acl;
        $database = &JFactory::getDBO();
        $GLOBALS['database'] = & $database;
        $jconfig = new JConfig();
        foreach (get_object_vars($jconfig) as $k => $v) {
            $name = 'mosConfig_' . $k;
            $$name = $GLOBALS[$name] = $v;
        } if ($mainframe->isAdmin()) {
            $mosConfig_live_site = $GLOBALS['mosConfig_live_site'] = substr_replace($mainframe->getSiteURL(), '', -1, 1);
        } else {
            $mosConfig_live_site = $GLOBALS['mosConfig_live_site'] = substr_replace(JURI::base(), '', -1, 1);
        } $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'] = JPATH_SITE;
        $language = & JFactory::getLanguage();
        $mosConfig_lang = $GLOBALS['mosConfig_lang'] = $language->getBackwardLang();
        if (!class_exists('mosDBTable')) {
            eval('class mosDBTable extends JTable { function mosDBTable($table, $keyField, &$database){parent::__construct($table, $keyField, $database);} }');
        }
    }

    function isAdmin() {
        global $mainframe;
        $isAdmin = false;
        if (method_exists($mainframe, 'isAdmin')) {
            $isAdmin = $mainframe->isAdmin();
        } else if (function_exists('adminSide')) {
            $isAdmin = adminSide();
        } return $isAdmin;
    }

    function loadOverlib() {
        if (!AriJoomlaBridge::isJoomla1_5()) {
            mosCommonHTML::loadOverlib();
        } else {
            JHTML::_('behavior.tooltip');
        }
    }

    function toolTip($tooltip, $title = '', $width = '', $image = 'tooltip.png', $text = '', $href = '', $link = 1) {
        if (!AriJoomlaBridge::isJoomla1_5()) {
            return mosTooltip($tooltip, $title, $width, $image, $text, $href, $link);
        } else {
            static $init;
            if (!$init) {
                JHTML::_('behavior.tooltip');
                $init = true;
            } return JHTML::_('tooltip', $tooltip, $title, $image, $text, $href, $link);
        }
    }

    function sendMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null, $replyto = null, $replytoname = null) {
        if (!AriJoomlaBridge::isJoomla1_5()) {
            return mosMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
        } else {
            if ($recipient && is_string($recipient))
                $recipient = explode(';', $recipient);
            return JUTility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
        }
    }

}

AriJoomlaBridge::doCompatibility();
?>