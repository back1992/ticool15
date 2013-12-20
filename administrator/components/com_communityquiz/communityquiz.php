<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: communityquiz.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('Q_APP_NAME') or define('Q_APP_NAME', 'com_communityquiz');

if(version_compare(JVERSION,'1.6.0','ge')) {
	define('APP_VERSION', '1.6');
} else {
	define('APP_VERSION', '1.5');
}

require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';
require_once JPATH_ROOT.DS.'components'.DS.Q_APP_NAME.DS.'helpers'.DS.'nestedtree.php';
require_once JPATH_ROOT.DS.'components'.DS.Q_APP_NAME.DS.'helpers'.DS.'constants.php';

$view = JRequest::getCmd('view','cpanel');
jimport('joomla.filesystem.file');
if( JFile::exists( JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php' ) ){
    require_once (JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php');
}else{
    JError::raiseError(500, 'View '. JString::ucfirst($view) . ' not found!');
}
$classname = 'CommunityQuizController' . JString::ucfirst($view);
$controller = new $classname;
$config = &CommunityQuizHelper::getConfig(true);
if(APP_VERSION != '1.5'){
	CommunityQuizHelper::addSubmenu(JRequest::getCmd('view', 'cpanel'), JRequest::getInt('status'));
	JToolBarHelper::preferences('com_communityquiz');
}

$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/'.Q_APP_NAME.'/assets/css/quiz.css');

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>