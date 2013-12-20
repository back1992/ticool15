<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: quiz.php 2010-01-02 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Quiz
 * @license GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class CommunityQuizControllerQuiz extends JController {
	
	function __construct() {
		parent::__construct();
		$this->registerDefaultTask('get_home_page');
		
		/* Listings */
		$this->registerTask('home', 'get_home_page');
		$this->registerTask('my_quizzes', 'get_user_quizzes');
		$this->registerTask('my_responses', 'get_user_responses');
		$this->registerTask('latest', 'get_latest_quizzes');
		$this->registerTask('popular', 'get_popular_quizzes');
		$this->registerTask('toprated', 'get_top_rated_quizzes');
		$this->registerTask('search', 'search_quizzes');
		
		/* create quiz */
		$this->registerTask('create', 'create_edit_quiz');
		$this->registerTask('edit', 'create_edit_quiz');
		$this->registerTask('loadqn','fetch_questions');
		$this->registerTask('save', 'save_quiz');
		$this->registerTask('saveqn','save_question');
		$this->registerTask('newpage','new_page');
		$this->registerTask('removepage','remove_page');
		$this->registerTask('finish', 'finalize_quiz');
		$this->registerTask('choose_answers', 'choose_answers');
		$this->registerTask('save_answers', 'save_answers');
		$this->registerTask('delete_qn', 'delete_question');
		$this->registerTask('move_up', 'move_question_up');
		$this->registerTask('move_down', 'move_question_down');
		
		/* Responses */
		$this->registerTask('respond', 'quiz_intro');
		$this->registerTask('response_form', 'quiz_response');
		$this->registerTask('save_response', 'save_response');
		
		/* Results */
		$this->registerTask('results', 'get_quiz_results');
		
		/* Reports */
		$this->registerTask('reports', 'get_quiz_statistics');
		$this->registerTask('report_details', 'get_report_details');
		$this->registerTask('csvdownload', 'get_csv_report');
	}

	function get_home_page(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'home_page');
		$view->display();
	}
	
	function get_latest_quizzes(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'latest_quizzes');
		$view->display();
	}
	
	function get_top_rated_quizzes(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'top_rated_quizzes');
		$view->display();
	}
	
	function get_user_quizzes(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'user_quizzes');
		$view->display();
	}
	
	function get_user_responses(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'user_responses');
		$view->display();
	}
	
	function get_popular_quizzes(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'popular_quizzes');
		$view->display();
	}
	
	function search_quizzes(){
		$view = &$this->getView('quiz', 'html');
		$model = &$this->getModel('quiz');
		$view->setModel($model, true);
		$view->assign('action', 'search_quizzes');
		$view->display();
	}
	
	function create_edit_quiz(){
		$user = &JFactory::getUser();
		if($user->guest) {
			$itemid = CommunityQuizHelper::getItemId();
			$url = base64_encode(JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=create".$itemid));
			$link = "index.php?option=com_users&view=login".$itemid;
			$msg = JText::_('MSG_NOT_LOGGED_IN');
			$this->setRedirect($link."&return=".$url, $msg);
		}else {
			$id = JRequest::getInt('id', 0);
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
			}else if($id && !CAuthorization::authorise('quiz.edit')){
				JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
			}else{
				$view = &$this->getView('quiz', 'html');
				$model = &$this->getModel('quiz');
				$view->setModel($model, true);
				$view->assign('action', 'create_edit_quiz');
				$view->display();
			}
		}
	}
	
	function save_quiz(){
		if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$id = $model->create_edit_quiz();
			if(!$id){
				JFactory::getApplication()->enqueueMessage(JText::_('MSG_ERROR_PROCESSING'));
				$this->populate_item_id();
				return $this->create_edit_quiz();
			}else{
				JRequest::setVar('id', $id);
				$view = &$this->getView('quiz', 'html');
				$view->setModel($model, true);
				$view->assign('action', 'quiz_form');
				$view->display();
			}
		}
	}

	function fetch_questions() {
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				echo json_encode(array('error'=>JText::_('MSG_UNAUTHORIZED')));
			}else{
				$quiz_id = JRequest::getVar("id",0,"","int");
				$pid = JRequest::getVar("pid",0,"","int");
				if(!$quiz_id || !$pid) {
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}else {
					$model = &$this->getModel('quiz');
					$questions = $model->get_questions($quiz_id, $pid);
					$error = $model->getError();
					if($questions) {
						echo json_encode(array('questions'=>$questions));
					}else if(!empty($error)) {
						echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
					}else {
						echo json_encode(array('questions'=>array()));
					}
				}
			}
		}
		jexit();
	}
	
	function save_question() {
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
			}else{
				$model = &$this->getModel('quiz');
				if($qid = $model->save_question()) {
					echo json_encode(array('data'=>$qid));
				}else {
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}
			}
		}
		jexit();
	}

	function new_page() {
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				echo json_encode(array('error'=>JText::_('MSG_UNAUTHORIZED')));
			}else{
				$model = &$this->getModel('quiz');
				$quiz_id = JRequest::getVar("id",0,"post","int");
				if($quiz_id && ($pid = $model->create_page($quiz_id))) {
					echo json_encode(array('page'=>$pid));
				}else {
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}
			}
		}
		jexit();
	}

	function remove_page() {
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				echo json_encode(array('error'=>JText::_('MSG_UNAUTHORIZED')));
			}else{
				$model = &$this->getModel('quiz');
				$quiz_id = JRequest::getVar("id",0,"post","int");
				$pid = JRequest::getVar("pid",0,"post","int");
				if($pid && $model->remove_page($quiz_id,$pid)) {
					echo json_encode(array('page'=>$pid));
				}else {
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}
			}
		}
		jexit();
	}
	
	function delete_question(){
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				echo json_encode(array('error'=>JText::_('MSG_UNAUTHORIZED')));
			}else{
				$model = &$this->getModel('quiz');
				$sid = JRequest::getVar('id',0,'post','int');
				$qid = JRequest::getVar('qid',0,'post','int');
				$pid = JRequest::getVar('pid',0,'post','int');
				if($sid && $pid && $qid && $model->delete_question($sid,$pid,$qid)) {
					echo json_encode(array('data'=>1));
				}else {
					echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
				}
			}
		}
		jexit();
	}
	
	function move_question_up(){
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				echo json_encode(array('error'=>JText::_('MSG_UNAUTHORIZED')));
			}else{
				$model = &$this->getModel('quiz');
				$sid = JRequest::getVar('id',0,'post','int');
				$qid = JRequest::getVar('qid',0,'post','int');
				if($qid && $sid){
					$order = $model->reorder_question($sid,$qid,1);
					if($order !== false) {
						echo json_encode(array('data'=>$order));
					}else {
						echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING').$model->getError()));
					}
				}
			}
		}
		jexit();
	}
	
	function move_question_down(){
		$user = &JFactory::getUser();
		if($user->guest) {
			echo json_encode(array('error'=>JText::_('MSG_NOT_LOGGED_IN')));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				echo json_encode(array('error'=>JText::_('MSG_UNAUTHORIZED')));
			}else{
				$model = &$this->getModel('quiz');
				$sid = JRequest::getVar('id',0,'post','int');
				$qid = JRequest::getVar('qid',0,'post','int');
				if($qid && $sid){
					$order = $model->reorder_question($sid,$qid,0);
					if($order !== false) {
						echo json_encode(array('data'=>$order));
					}else {
						echo json_encode(array('error'=>JText::_('MSG_ERROR_PROCESSING')));
					}
				}
			}
		}
		jexit();
	}
	
	function finalize_quiz(){
		$user = &JFactory::getUser();
		if(!CAuthorization::authorise('quiz.create')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$id = JRequest::getInt('id', 0);
			if(!$model->finalize_quiz($id)){
				JFactory::getApplication()->enqueueMessage(JText::_('MSG_ERROR_PROCESSING'));
				$this->populate_item_id();
				return $this->get_user_quizzes();
			}else{
				$config = &CommunityQuizHelper::getConfig();
				if($config[CQ_ENABLE_MODERATION] == '1'){
					JFactory::getApplication()->enqueueMessage(JText::_('MSG_SENT_FOR_REVIEW'));
					if($config[ENABLE_ADMIN_NOTIFICATIONS]){
						CommunityQuizHelper::sendMail(
							$config[CQ_NOTIF_SENDER_NAME], 
							$config[CQ_NOTIF_SENDER_EMAIL],
							$config[CQ_NOTIF_ADMIN_EMAIL],
							JText::_('MSG_MAIL_PENDING_REVIEW_SUBJECT'),
							JText::_('MSG_MAIL_PENDING_REVIEW_BODY'), 
							1);
					}
				}else{
					$quiz = $model->get_quiz_details($id);
					CommunityQuizHelper::awardPoints($user->id, 1, $id, JText::_('AWARD_POINTS_NEW_QUIZ'));
					CommunityQuizHelper::streamActivity(1, $quiz);
					JFactory::getApplication()->enqueueMessage(JText::_('MSG_SUCCESSFULLY_SAVED'));
				}
				$itemid = CommunityQuizHelper::getItemId(); 
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=choose_answers&id='.$id.$itemid, false), JText::_('MSG_CHOOSE_CORRECT_ANSWERS'));
			}
		}
	}
	
	function choose_answers(){
		if(!CAuthorization::authorise('quiz.create')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$view = &$this->getView('quiz', 'html');
			$view->setModel($model, true);
			$view->assign('action', 'quiz_choose_answers');
			$view->display();
		}
	}
	
	function save_answers(){
		if(!CAuthorization::authorise('quiz.create')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$id = JRequest::getVar('id', 0, 'post', 'int');
			if($model->save_correct_answers($id)){
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=home&id='.$id.$itemid, false), JText::_('MSG_ANSWERS_UPDATED'));
			}else{
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=choose_answers&id='.$id.$itemid, false), JText::_('MSG_ERROR_PROCESSING').$model->getError());
			}
		}
	}

	function quiz_intro() {
		if(!CAuthorization::authorise('quiz.respond')){
			$itemid = CommunityQuizHelper::getItemId();
			$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid, false), JText::_('MSG_NOT_ALLOWED_TO_RESPOND'));
		}else{
			$view = &$this->getView('quiz', 'html');
			$model = &$this->getModel('quiz');
			$view->setModel($model, true);
			$view->assign('action','quiz_intro');
			$view->display();
		}
	}
	
	function quiz_response(){
		if(!CAuthorization::authorise('quiz.respond')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$view = &$this->getView('quiz', 'html');
			$model = &$this->getModel('quiz');
			$view->setModel($model, true);
			$view->assign('action','quiz_response');
			$view->display();
		}
	}
	
	function save_response(){
		if(!CAuthorization::authorise('quiz.respond')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$quiz_id = JRequest::getVar ( 'id', 0, 'post', 'int' );
			$pid = JRequest::getVar ( 'pid', 0, 'post', 'int' );
			$rid = JRequest::getVar('rid', 0, 'post', 'int');
			$page_number = JRequest::getVar ( 'page_number', 0, 'post', 'int' );
			$finalize = JRequest::getInt('finalize', 0);
			$itemid = CommunityQuizHelper::getItemId();
			
			if($model->is_response_expired($quiz_id, $rid) || ($finalize > 0)){
				$this->finalize_response($quiz_id, $rid);
			}else{
				if($model->save_response($quiz_id, $pid, $rid)){
					$result = &$model->get_next_page($quiz_id, $page_number);
					if(!$result || !$result[0]){
						$this->finalize_response($quiz_id, $rid);
					}else{
						$view = &$this->getView('quiz', 'html');
						$view->setModel($model, true);
						$view->assign('action','quiz_response');
						$view->display();
					}
				}else{
					$quiz_id = JRequest::getInt('id');
					$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz_id.$itemid, false), JText::_('MSG_ERROR_PROCESSING'));
				}
			}
		}
	}
	
	function finalize_response($quiz_id, $response_id){
		$user = JFactory::getUser();
		$itemid = CommunityQuizHelper::getItemId();
		$model = &$this->getModel('quiz');
		if($model->finalize_response($quiz_id, $response_id)){
			$quiz = $model->get_quiz_details($quiz_id);
			CommunityQuizHelper::awardPoints($user->id, 2, $quiz_id, JText::_('AWARD_POINTS_NEW_RESPONSE'));
			CommunityQuizHelper::streamActivity(2, $quiz);
			if($quiz->show_answers == '1'){
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=results&id='.$response_id.$itemid, false), JText::_('MSG_RESPONSE_EXPIRED'));
			}else{
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid, false), JText::_('MSG_RESPONSE_EXPIRED'));
			}
		}else{
			$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz_id.$itemid, false), JText::_('MSG_ERROR_PROCESSING'));
		}
	}
	
	function get_quiz_results(){
		if(!CAuthorization::authorise('quiz.respond')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$view = &$this->getView('quiz', 'html');
			$model = &$this->getModel('quiz');
			$view->setModel($model, true);
			$view->assign('action','quiz_results');
			$view->display();
		}
	}
	
	function get_quiz_statistics(){
		$id = JRequest::getInt('id', 0);
		if(!$id || !CAuthorization::authorise('quiz.respond')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$view = &$this->getView('quiz', 'html');
			$view->setModel($model, true);
			
			$quiz = $model->get_quiz_statistics($id);
			if(empty($quiz)){
				$itemid = CommunityQuizHelper::getItemId();
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid, false), JText::_('MSG_ERROR_PROCESSING').$model->getError());
			}else{
				$view->assignRef('quiz', $quiz);
				$view->assign('action','quiz_reports');
				$view->display();
			}
		}
	}
	
	function get_report_details(){
		$id = JRequest::getInt('id', 0);
		$response_id = JRequest::getInt('response_id', 0);
		if(!$id || !$response_id || !CAuthorization::authorise('quiz.respond')){
			JError::raiseError(401, JText::_('MSG_UNAUTHORIZED'));
		}else{
			$model = &$this->getModel('quiz');
			$view = &$this->getView('quiz', 'html');
			$view->setModel($model, true);
			$view->assign('action','quiz_report_details');
			$view->display();
		}
	}

	function get_csv_report(){
		$itemid = CommunityQuizHelper::getItemId();
		$user = &JFactory::getUser();
		if($user->guest) {
			$redirectUrl = base64_encode(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=reports&id='.$sid.$itemid));
			$link = APP_VERSION == '1.5'
				? JRoute::_("index.php?option=com_user&view=login".$itemid."&return=".$redirectUrl)
				: JRoute::_("index.php?option=com_users&view=login".$itemid."&return=".$redirectUrl);
			$this->setRedirect($link, JText::_('MSG_NOT_LOGGED_IN'));
		}else {
			if(!CAuthorization::authorise('quiz.create') && !CAuthorization::authorise('quiz.manage')){
				$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid), JText::_('MSG_UNAUTHORIZED'));
			}else{
				$quiz_id = JRequest::getInt('id',0);
				if(!$quiz_id) {
					$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid), JText::_('MSG_UNAUTHORIZED'));
				}else{
					$model = $this->getModel('quiz');
					$return = $model->get_reponse_data_for_csv($quiz_id);
					if(empty($return)){
						$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=reports&id='.$sid.$itemid), JText::_('MSG_ERROR_PROCESSING').$model->getError());
					}else{
						$responses = array();
						foreach ($return->responses as $response){
							$responses[$response->id] = new stdClass();
							$responses[$response->id]->created_by = $response->created_by;
							$responses[$response->id]->created = $response->created;
							$responses[$response->id]->username = $response->username;
							$responses[$response->id]->name = $response->name;
							$responses[$response->id]->questions = array();
								
							foreach ($return->questions as $question){
								$responses[$response->id]->questions[$question->id] = new stdClass();
								$responses[$response->id]->questions[$question->id]->answer = '';
							}
						}

						if(!empty($return->entries)){
							foreach ($return->entries as $entry){
								if(isset($responses[$entry->response_id]) && isset($responses[$entry->response_id]->questions[$entry->question_id])){
									if(!empty($entry->answer)){
										if(empty($responses[$entry->response_id]->questions[$entry->question_id]->answer)){
											$responses[$entry->response_id]->questions[$entry->question_id]->answer = $entry->answer;
										}else{
											$responses[$entry->response_id]->questions[$entry->question_id]->answer .= ('|'.$entry->answer);
										}
									}
										
									if(!empty($entry->answer2)){
										if(empty($responses[$entry->response_id]->questions[$entry->question_id]->answer2)){
											$responses[$entry->response_id]->questions[$entry->question_id]->answer = $entry->answer2;
										}else{
											$responses[$entry->response_id]->questions[$entry->question_id]->answer .= ('|'.$entry->answer2);
										}
									}
										
									if(!empty($entry->free_text)){
										if(empty($responses[$entry->response_id]->questions[$entry->question_id]->free_text)){
											$responses[$entry->response_id]->questions[$entry->question_id]->answer = $entry->free_text;
										}else{
											$responses[$entry->response_id]->questions[$entry->question_id]->answer .= ('|'.$entry->free_text);
										}
									}
								}
							}
						}

						$csv_array = array();
						$string = 'Response ID, User ID, Response Date, Username, User Display Name';
						foreach ($return->questions as $question){
							$string = $string.',"'.$question->title.'"';
						}
						array_push($csv_array, $string);

						foreach ($responses as $id => $response){
							$string = $id.','.$response->created_by.','.$response->created.',"'.$response->username.'","'.$response->name.'"';
							foreach ($response->questions as $id=>$question){
								$string = $string.',"'.$question->answer.'"';
							}
							array_push($csv_array, $string);
						}

						$filename = 'Quiz_'.$quiz_id.'_'.date("d-m-Y").'.csv';
						$file = JPATH_ROOT.DS.'tmp'.DS.$filename;

						$exts = array(".php",".htm",".html",".ph4",".ph5");
						$found = false;
						foreach($exts as $l=>$ext){
							if (file_exists('index'.$ext)) {
								$found = true;
							}
						}
						if(!$found){
							$this->setRedirect(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=reports&id='.$sid.$itemid), JText::_('MSG_ERROR_PROCESSING'));
						}else{
							$fh = fopen($file, 'w') or die("can't open ".$filename." file");
							foreach($csv_array as $line){
								fwrite($fh, $line."\n");
							}
							fclose($fh);

							if(!file_exists($file)) die("I'm sorry, the file doesn't seem to exist.");

							header("Content-type: text/csv");
							header("Content-Disposition: attachment;filename=".$filename);
					
							readfile($file);
						}
					}
				}
			}
		}
	}
	
	function populate_item_id(){
		$menu = &JSite::getMenu();
		$mnuitem = $menu->getItems('link', 'index.php?option='.Q_APP_NAME.'&view=quiz', true);
		$itemid = JRequest::getInt('Itemid');
		JRequest::setVar('Itemid', $itemid);
		return $itemid;
	}
}
?>
