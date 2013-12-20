<?php
/**
 * Joomla! 1.5 component quiz
 *
 * @version $Id: controller.php 2010-04-05 04:20:33 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage quiz
 * @license GNU/GPL
 *
 * Community Survey allows authorized users to create and manage surveys.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

class CommunityQuizControllerQuiz extends JController {
	/**
	 * Constructor
	 * @access private
	 * @subpackage quiz
	 */
	function __construct() {
		parent::__construct();
		$this->registerDefaultTask('get_quiz_list');
		$this->registerTask('list','get_quiz_list');
		$this->registerTask('publish','publish_quiz');
		$this->registerTask('unpublish','unpublish_quiz');
		$this->registerTask('remove','remove_quizzes');
		$this->registerTask('cancel','get_quiz_list');
		$this->registerTask('details','view_quiz_details');
	}

	function get_quiz_list(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->setLayout('list');
		$view->display();
	}

	function publish_quiz(){
		$id = JRequest::getVar('cid',array(),'','array');
		$model = &$this->getModel('quiz');
		if(empty($id)){
			$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_INVALID_ID'));
		}else{
			JArrayHelper::toInteger($id);
			$id = implode(',', $id);
			$model->set_status($id, true);
			$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_COMPLETED'));
		}
	}

	function unpublish_quiz(){
		$id = JRequest::getVar('cid',array(),'','array');
		$model = &$this->getModel('quiz');
		if(empty($id)){
			$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_INVALID_ID'));
		}else{
			JArrayHelper::toInteger($id);
			$id = implode(',', $id);
			$model->set_status($id, false);
			$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_COMPLETED'));
		}
	}

    function remove_quizzes(){
		$ids = JRequest::getVar('cid',array(),'','array');
		JArrayHelper::toInteger($ids);
		if(empty($ids)){
			$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_INVALID_ID'));
		}else{
			$id = implode(',', $ids);
			$model = &$this->getModel('quiz');
			if($model->delete_quizzes($id)){
				$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_COMPLETED'));
			}else{
				$this->setRedirect('index.php?option='.Q_APP_NAME.'&view=quiz&task=list', JText::_('MSG_ERROR'));
			}
		}
    }
    
    function view_quiz_details(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->setLayout('details');
		$view->display();
    }
}
?>