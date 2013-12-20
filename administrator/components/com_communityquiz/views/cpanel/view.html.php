<?php
/**
 * Joomla! 1.5 component Community Answers
 *
 * @version $Id: view.html.php 2010-07-10 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Polls
 * @license GNU/GPL
 *
 * Community Answers does the functionality similar to Yahoo Answers
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class CommunityQuizViewCpanel extends JView {

    function display($tpl = null) {

        JToolBarHelper::title(JText::_('TITLE_COMMUNITY_QUIZ').': <small><small>[ ' . JText::_('TITLE_CONTROL_PANEL') .' ]</small></small>', 'quiz.png');

        $model = & $this->getModel('quiz');
        $result = $model->get_quizzes(1, 0, 5);
        $this->assignRef( 'latest', $result->quizzes);
        $result = $model->get_quizzes(3, 0, 5);
        $this->assignRef( 'pending', $result->quizzes);

        $update = CommunityQuizHelper::checkUpdate();
        $this->assignRef( 'update', $update );
        parent::display($tpl);
    }
}
?>