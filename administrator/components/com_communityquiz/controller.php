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

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * CommunityQuiz Controller
 *
 * @package Joomla
 * @subpackage CommunityQuiz
 */
class CommunityQuizController extends JController {
    /**
     * Constructor
     * @access private
     * @subpackage CommunityQuiz
     */
    function __construct() {
        //Get View
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'default');
        }
        $this->item_type = 'Default';
        parent::__construct();
    }
}
?>