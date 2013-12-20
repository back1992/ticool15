<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: controller.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * CommunityQuiz Component Controller
 */
class CommunityQuizController extends JController {
	function display() {
        // Make sure we have a default view
        if( !JRequest::getVar( 'view' )) {
		    JRequest::setVar('view', 'communityquiz' );
        }
		parent::display();
	}
}
?>