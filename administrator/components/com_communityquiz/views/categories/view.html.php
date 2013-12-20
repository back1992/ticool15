<?php
/**
 * Joomla! 1.5 component quiz
 *
 * @version $Id: view.html.php 2010-06-26 22:11:56 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage categories
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class CommunityQuizViewCategories extends JView {
	function display($tpl = null) {
		JToolBarHelper::title(JText::_('TITLE_COMMUNITY_QUIZ').": <small><small>[".JText::_("LBL_CATEGORIES")."]</small></small>", 'quiz.png');
		$model = $this->getModel('categories');
		if($this->getLayout() == 'list') {
			JToolBarHelper::custom('refresh','refresh.png','refresh.png',JText::_('LBL_REFRESH_CATEGORIES'),false, false);
			JToolBarHelper::addNewX();
		}else if($this->getLayout() == 'add') {
			$id = JRequest::getVar('id',0,'','INT');
			$model = $this->getModel('categories');
			if($id){
				$category = $model->get_category($id);
				$this->assignRef('category', $category);
			}
			JToolBarHelper::save();
			JToolBarHelper::cancel();
		}
		$categories = &$model->get_categories();
		$this->assignRef('categories',$categories);
		parent::display($tpl);
	}
}
?>