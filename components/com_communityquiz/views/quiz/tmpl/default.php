<?php 
defined('_JEXEC') or die('Restricted access'); 
$app = &JFactory::getApplication();
$cqConfig = &CommunityQuizHelper::getConfig();
$tmpl = $app->getUserStateFromRequest(Q_APP_NAME.".theme", 'theme', (isset($this->theme) ? $this->theme : $cqConfig[CQ_DEFAULT_TEMPLATE]));
$layoutPath = CTemplateManager::getLayoutPath($tmpl,$this->layoutPath);
$templateUrlPath = CTemplateManager::getTemplateUrlPath($tmpl);
$document = &JFactory::getDocument();
if($layoutPath) {
    $templatePath = CTemplateManager::getTemplatePath($tmpl);
    if($templatePath){
    	$document->addScript($templateUrlPath.'/scripts/jquery.inlineFieldLabel.js');
    	$document->addScript($templateUrlPath.'/scripts/jquery.scrollto-min.js');
    	$document->addScript($templateUrlPath.'/scripts/jquery.ui.stars.min.js');
    	$document->addScript($templateUrlPath.'/scripts/quiz.js');
    	$document->addStyleSheet($templateUrlPath.'/css/jquery.ui.stars.min.css');
    	$document->addStyleSheet($templateUrlPath.'/css/quiz.css');
    	$document->addStyleDeclaration('.toolbar .submenu{margin: 0; padding: 0; vertical-align: top;} .toolbar .submenu li {line-height: 15px;}');
//     	echo '<link rel="stylesheet" href="'.$templateUrlPath.'/css/quiz.css'.'" />';
    }
    echo '<div id="cqwrapper">';
    include_once $layoutPath;
    echo '</div>';
}else {
    JError::raiseError( 403, JText::_('Access Forbidden. Error Code: 10002.') );
    return;
}
?>