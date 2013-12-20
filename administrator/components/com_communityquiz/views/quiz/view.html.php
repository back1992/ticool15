<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: view.html.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class CommunityQuizViewQuiz extends JView {
    function display($tpl = null) {
    	JToolBarHelper::title(JText::_('TITLE_COMMUNITY_QUIZ').": <small><small>[".JText::_("LBL_QUIZZES")."]</small></small>", 'quiz.png');
    	if($this->getLayout() == 'list'){
            JToolBarHelper::publish();
            JToolBarHelper::unpublish();
            JToolBarHelper::deleteList();
			$model = $this->getModel ('quiz');
			$result = &$model->get_quizzes();
			$this->assignRef ( 'quizzes', $result->quizzes );
			$this->assignRef ( 'pagination', $result->pagination );
			$this->assignRef ( 'lists', $result->lists );
    	}else if($this->getLayout() == 'details'){
    		JToolBarHelper::publish();
    		JToolBarHelper::unpublish();
    		JToolBarHelper::cancel();
    		$model = $this->getModel ('quiz');
    		$id = JRequest::getInt('id', 0);
    		$quiz = &$model->get_quiz_details($id);
    		$quiz->questions = &$model->get_questions($id);
    		$this->assignRef ( 'quiz', $quiz );
    	}
    	parent::display($tpl);
    }
}
?>