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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the CommunityQuiz component
 */
class CommunityQuizViewQuiz extends JView {
	function display($tpl = null) {
		$app = & JFactory::getApplication ();
		$user = & JFactory::getUser ();
		$document = &JFactory::getDocument();
		$config = &CommunityQuizHelper::getConfig ();
		$pathway =& $app->getPathway();
		$params = $app->getParams ();

		JPluginHelper::importPlugin ( 'corejoomla' );
		$dispatcher = & JDispatcher::getInstance ();
		$dispatcher->trigger ( 'onCallIncludeJQuery', array (array ("jquery", "jqueryui", "jqueryform", "jqueryvalidate" ) ) );
		
		$menu = &JSite::getMenu(); 
        $mnuitems = $menu->getItems('link', 'index.php?option='.Q_APP_NAME.'&view=quiz');
        $itemid = isset($mnuitems[0]->id) ? $mnuitems[0]->id : JRequest::getVar('Itemid');
        $itemid = ($itemid)? $itemid : 0;
		
		$catid = JRequest::getInt('catid', 0);
		
		$model = $this->getModel();
		$quiz = null;
		$page_header = null;
		switch ($this->action){
			case 'quiz_intro':
				$model = $this->getModel ();
				$quiz_id = JRequest::getInt('id', 0);
				$quiz = $model->get_quiz_details($quiz_id);
				if (empty($quiz)) {
					$msg = $model->getError();
					if(empty($msg)){
						$msg = JText::_('MSG_UNAUTHORIZED');
					}
					$app->enqueueMessage($msg);
					$this->set_home_page_params($model);
				} else {
					$this->assignRef ( "quiz", $quiz );
					$hide_template = $this->is_hide_template($config[CQ_HIDE_TEMPLATE], $quiz->show_template);
					if ($hide_template) {
						JRequest::setVar ( 'tmpl', 'component' );
						JRequest::setVar ( 'format', 'raw' );
						$this->assign ( 'hide_template', '1' );
					}
				}
				break;
			case 'quiz_response':
				$quiz = &$model->do_create_update_response();
				if (! $quiz || ! $quiz->id || ! $quiz->response_id) {
					$msg = $model->getError();
					if(empty($msg)){
						$msg = JText::_('MSG_UNAUTHORIZED');
					}
					$app->enqueueMessage($msg);
					$this->set_home_page_params($model);
					$this->action = 'quiz_list';
				} else {
					if($model->is_response_expired($quiz->id, $quiz->response_id)){
						
					}
					$result = &$model->get_next_page($quiz->id, $quiz->current_page);
					$quiz->pid = (!empty($result) && isset($result[0])) ? $result[0]->id : 0;
					$quiz->page_number = (!empty($result) && isset($result[0])) ? $result[0]->sort_order : 0;
					$quiz->final_page = (!empty($result) && isset($result[1])) ? false : true;
					$quiz->questions = &$model->get_questions($quiz->id, $quiz->pid);
					$this->assignRef ( "quiz", $quiz );
					$hide_template = $this->is_hide_template($config[CQ_HIDE_TEMPLATE], $quiz->show_template);
					if ($hide_template) {
						JRequest::setVar ( 'tmpl', 'component' );
						JRequest::setVar ( 'format', 'raw' );
						$this->assign ( 'hide_template', '1' );
					}
				}
				break;
			case 'quiz_choose_answers':
				$quiz_id = JRequest::getInt('id', 0);
				if($quiz_id && $model->authorize_quiz($quiz_id)){
					$quiz = &$model->get_quiz_details($quiz_id);
					if (! $quiz || ! $quiz->id) {
						$app->enqueueMessage($model->getError() ? $model->getError() : JText::_('MSG_UNAUTHORIZED'));
						$this->set_home_page_params($model);
					} else {
						$quiz->questions = &$model->get_questions($quiz->id);
						$this->assignRef ( "quiz", $quiz );
					}
				}else{
					$app->enqueueMessage(JText::_('MSG_UNAUTHORIZED'));
				}
				break;
			case 'quiz_results':
				$response_id = JRequest::getInt('id', 0);
				$quiz_id = $model->get_quiz_id($response_id);
				$quiz = &$model->get_quiz_details($quiz_id);
				if (! $quiz || ! $quiz->id) {
					$app->enqueueMessage($model->getError() ? $model->getError() : JText::_('MSG_UNAUTHORIZED'));
					$this->set_home_page_params($model);
				} else if($quiz->show_answers != '1'){
					$this->set_home_page_params($model);
				} else {
					$quiz->questions = &$model->get_questions($quiz_id);
					$responses = &$model->get_response_details($response_id);
					if(!empty($responses) && !empty($quiz->questions)){
						foreach($quiz->questions as $question){
							foreach ($responses as $response){
								if($question->id == $response->question_id){
									$question->responses[] = $response;
								}
							}
						}
					}
					$this->assignRef ( "quiz", $quiz );
					$hide_template = $this->is_hide_template($config[CQ_HIDE_TEMPLATE], $quiz->show_template);
					if ($hide_template) {
						JRequest::setVar ( 'tmpl', 'component' );
						JRequest::setVar ( 'format', 'raw' );
						$this->assign ( 'hide_template', '1' );
					}
				}
				break;
			case 'create_edit_quiz':
				$id = JRequest::getInt('id', 0);
				if($id){
					if($model->authorize_quiz($id)){
						$quiz = &$model->get_quiz_details($id);
						$this->assignRef('quiz', $quiz);
					}else{
						$app->enqueueMessage(JText::_('MSG_UNAUTHORIZED'));
					}
				}
				$categories = &$model->get_categories();
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_CREATE_EDIT_QUIZ');
				break;
			case 'quiz_form':
				$quiz_id = JRequest::getInt('id', 0);
				$page_id = JRequest::getInt('id', 1);
				$quiz = &$model->get_quiz_details($quiz_id);
				$quiz->pages = &$model->get_pages($quiz_id);
				$this->assignRef('quiz', $quiz);
				break;
			case 'home_page':
				$this->set_home_page_params($model);
				break;
			case 'latest_quizzes':
				$result = &$model->get_latest_quizzes();
				$this->assignRef('list', $result->rows);
				$this->assignRef('pagination', $result->pagination);
				$this->action = 'quiz_list';
				$categories = &$model->get_category_flat_list($catid);
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_LATEST_QUIZZES');
				$this->page = 1;
				break;
			case 'popular_quizzes':
				$result = &$model->get_popular_quizzes();
				$this->assignRef('list', $result->rows);
				$this->assignRef('pagination', $result->pagination);
				$this->action = 'quiz_list';
				$categories = &$model->get_category_flat_list($catid);
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_MOST_POPULAR_QUIZZES');
				$this->page = 2;
				break;
			case 'top_rated_quizzes':
				$result = &$model->get_top_rated_quizzes();
				$this->assignRef('list', $result->rows);
				$this->assignRef('pagination', $result->pagination);
				$this->action = 'quiz_list';
				$categories = &$model->get_category_flat_list($catid);
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_TOP_RATED_QUIZZES');
				$this->page = 3;
				break;
			case 'user_quizzes':
				$result = &$model->get_user_quizzes();
				$this->assignRef('list', $result->rows);
				$this->assignRef('pagination', $result->pagination);
				$this->action = 'quiz_list';
				$categories = &$model->get_category_flat_list($catid);
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_MY_QUIZZES');
				$this->assign('user_quizzes', true);
				$this->page = 0;
				break;
			case 'user_responses':
				$result = &$model->get_user_responses();
				$this->assignRef('list', $result->rows);
				$this->assignRef('pagination', $result->pagination);
				$this->action = 'quiz_list';
				$categories = &$model->get_category_flat_list($catid);
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_MY_RESPONSES');
				$this->assign('user_responses', true);
				$this->page = 0;
				break;
			case 'search_quizzes':
				$title = JRequest::getString('searchkey','');
				$result = &$model->search($title);
				$this->assignRef('list', $result);
				$this->action = 'quiz_list';
				$categories = &$model->get_category_flat_list($catid);
				$this->assignRef('categories', $categories);
				$this->list_header = JText::_('TXT_SEARCH');
				$this->page = 0;
				break;
			case 'quiz_reports':
				$this->assignRef('quiz', $this->quiz);
				$this->list_header = JText::_('LBL_REPORTS');
				break;
			case 'quiz_report_details':
				$response_id = JRequest::getInt('response_id', 0);
				$quiz_id = JRequest::getInt('id', 0);
				$quiz = &$model->get_quiz_details($quiz_id);
				if (! $quiz || ! $quiz->id) {
					$app->enqueueMessage($model->getError() ? $model->getError() : JText::_('MSG_UNAUTHORIZED'));
					$this->set_home_page_params($model);
				} else {
					$quiz->questions = &$model->get_questions($quiz_id);
					$responses = &$model->get_response_details($response_id, $quiz_id);
					if(!empty($responses) && !empty($quiz->questions)){
						foreach($quiz->questions as $question){
							foreach ($responses as $response){
								if($question->id == $response->question_id){
									$question->responses[] = $response;
								}
							}
						}
					}
					$this->assignRef ( "quiz", $quiz );
				}
				break;
			default:
				JError::raiseError ( 401, '10000 - Unauthorized access!' );
				break;
		}
		
		if($quiz && $quiz->catid){
			$catid = $quiz->catid;
		}
		
		if($catid){
			$page_header = $model->get_category_name($catid);
			$this->assignRef('page_header', $page_header);
			$breadcrumbs = &$model->get_breadcrumbs($catid);
			if($breadcrumbs){
				foreach ($breadcrumbs as $breadcrumb){
					if($breadcrumb->parent_id > 0){
						$pathway->addItem($breadcrumb->title, 'index.php?option='.Q_APP_NAME.'&view=quiz&task=latest&catid='.$breadcrumb->id.':'.$breadcrumb->alias.'&Itemid='.$itemid);
					}
				}
			}
		}
		
		if($quiz && $quiz->id && $quiz->title){
			$pathway->addItem($quiz->title, 'index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz->id.':'.$quiz->alias.'&Itemid='.$itemid);
            $keywords = explode(" ", $quiz->title);
            $meta = array();
            foreach ($keywords as $keyword){
            	if(strlen($keyword) > 2){
            		$meta[] = $this->escape($keyword);
            	}
            }
            $document->setMetadata("keywords", implode(',', $meta));
            $catid = $quiz->catid;
            $this->list_header = $quiz->title;
		}
		
		$document->setTitle((isset($mnuitems[0]->id)?APP_VERSION == '1.5' ? $mnuitems[0]->name : $mnuitems[0]->title:'').($page_header ? ' - '. $page_header:'').($this->list_header ? ' - '.$this->list_header:''));
		
		$this->assignRef ( 'params', $params );
		$this->assignRef ( 'itemid', $itemid );
		$this->assignRef ( 'layoutPath', $this->action );

		parent::display ( $tpl );
	}

	function set_home_page_params($model){
		// Return to home page
		JRequest::setVar('catid', 0);
		$list = &$model->get_latest_quizzes(10, 0, true);
		$popular = &$model->get_popular_quizzes(10, 0, true);
		$categories = &$model->get_category_flat_list(0);
		$this->assignRef('list', $list->rows);
		$this->assignRef('popular', $popular->rows);
		$this->assignRef('categories', $categories);
		$this->list_header = JText::_('TXT_LATEST_QUIZZES');
		$this->action = 'quiz_list';
		$this->page = 0;
	}
	
	function is_hide_template($config_value, $user_value){
		if($config_value == '1'){ // force hide
			return true;
		} else if($config_value == '2'){ // force show
			return false;
		} else if(isset($user_value) && $user_value == '0'){ //user selectible
			return ($user_value == '0');
		} else {
			return false;
		}
	}
}
?>