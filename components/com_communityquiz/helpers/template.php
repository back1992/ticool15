<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: helper.php 2010-01-02 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Quiz
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CTemplateManager {

    function getLayoutPath($template, $layout) {
        if(strlen($layout) <= 0) {
            return false;
        }
        $templatePath = CTemplateManager::getTemplatePath($template);
        if(JFile::exists($templatePath . $layout . '.php')){
            return $templatePath . $layout . '.php';
        }else{
            return false;
        }
    }

    function getTemplatePath($template){
        if(strlen($template) <= 0) {
            $templatePath =  CQ_DEFAULT_TEMPLATE_PATH . DS . "default" . DS;
        }else if(JFolder::exists(CQ_TEMPLATE_OVERRIDES_PATH . DS . $template )) {
            $templatePath =  CQ_TEMPLATE_OVERRIDES_PATH . DS . $template . DS;
        }else if(JFolder::exists(CQ_DEFAULT_TEMPLATE_PATH . DS . $template )) {
            $templatePath =  CQ_DEFAULT_TEMPLATE_PATH . DS . $template . DS;
        }else {
            $templatePath =  CQ_DEFAULT_TEMPLATE_PATH . DS . "default" . DS;
        }

        return $templatePath;
    }

    function getTemplateUrlPath($template){
        if(strlen($template) <= 0) {
            $templatePath =  CQ_DEFAULT_TEMPLATE_URL . "default";
        }else if(JFolder::exists(CQ_TEMPLATE_OVERRIDES_PATH . DS . $template )) {
            $templatePath =  CQ_TEMPLATE_OVERRIDES_URL . $template;
        }else if(JFolder::exists(CQ_DEFAULT_TEMPLATE_PATH . DS . $template )) {
            $templatePath =  CQ_DEFAULT_TEMPLATE_URL . $template;
        }else {
            $templatePath =  CQ_DEFAULT_TEMPLATE_URL . "default";
        }

        return $templatePath;
    }

    function renderModules($position, $attribs = array()) {
        jimport( 'joomla.application.module.helper' );
        $modules = JModuleHelper::getModules( $position );
        $modulehtml = '';

        foreach($modules as $module) {
            $params = new JParameter( $module->params );
            $moduleClassSuffix 	= $params->get('moduleclass_sfx', '');

            $modulehtml .= '<div class="moduletable'.$moduleClassSuffix.'">';
            $modulehtml .= JModuleHelper::renderModule($module, $attribs);
            $modulehtml .= '</div>';
        }

        echo $modulehtml;
    }
}