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

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'constants.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'nestedtree.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'template.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'authorization.php';

jimport('joomla.filesystem.file');
$view = JRequest::getCmd('view','quiz');
if( JFile::exists( JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php' ) ){
    require_once (JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php');
}else{
    JError::raiseError(500, 'View '. JString::ucfirst($view) . ' not found!');
}

$config = &CommunityQuizHelper::getConfig(true);
$auth = new CAuthorization($config);

$jlang =& JFactory::getLanguage();
$jlang->load(Q_APP_NAME, JPATH_SITE, 'en-GB', true);
$jlang->load(Q_APP_NAME, JPATH_SITE, null, true);

// Initialize the controller
$classname = 'CommunityQuizController'.JString::ucfirst($view);
$controller = new $classname( );
$controller->execute( JRequest::getCmd('task') );

// Redirect if set by the controller
$controller->redirect();

if($config[CQ_ENABLE_POWERED_BY]){
	$format = JRequest::getCmd('format');
	if($format != 'raw'){	
	    echo CommunityQuizHelper::getPoweredByLink();
	}
}
?>