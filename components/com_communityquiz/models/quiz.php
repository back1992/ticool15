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

jimport('joomla.application.component.model');

class CommunityQuizModelQuiz extends JModel {
	
	function __construct() {
		parent::__construct();
    }
    
    function get_latest_quizzes($limit=0, $limitstart=0, $listonly=false){
    	return $this->get_quizzes(1, $limit, $limitstart, $listonly);
    }
    
    function get_popular_quizzes($limit=0, $limitstart=0, $listonly=false){
    	return $this->get_quizzes(2, $limit, $limitstart, $listonly);
    }
    
    function get_top_rated_quizzes($limit=0, $limitstart=0, $listonly=false){
    	return $this->get_quizzes(3, $limit, $limitstart, $listonly);
    }
    
    function get_user_quizzes($limit=0, $limitstart=0, $listonly=false){
    	return $this->get_quizzes(4, $limit, $limitstart, $listonly);
    }
    
    function get_user_responses($limit=0, $limitstart=0, $listonly=false){
    	return $this->get_quizzes(5, $limit, $limitstart, $listonly);
    }
    
    function &get_quizzes($action=1, $limit=0, $limitstart=0, $listonly=false){
		$user = &JFactory::getUser();
		$app = JFactory::getApplication();
		$config = CommunityQuizHelper::getConfig();
		if (!$limitstart) {
			$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		}
		if (!$limit) {
			$limit = $config[CQ_LIST_LIMIT];
		}
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$catid = JRequest::getVar('catid', 0, '', 'INT');

		$username = empty($config[CQ_USER_NAME])?'username':$config[CQ_USER_NAME];

		$wheres = array();

		if($catid){
			$wheres[] = 'a.catid='.$catid;
		}
		$order = '';
		switch ($action) {
			case 1: // Latest Quizzes
				$wheres[] = ' a.published=1';
				$order = ' order by a.created desc';
				break;
			case 2: // Most popular
				$wheres[] = ' a.published=1';
				$wheres[] = ' a.responses > 1';
				$order = ' order by a.responses desc';
				break;
			case 3: // Top rated quizzes
				$order = ' order by rtg.rating desc';
				break;
			case 4: // User quizzes
				$wheres[] = ' a.created_by='.$user->id;
				break;
			case 5: // User responses
				$wheres[] = ' r.created_by='.$user->id;
				break;
		}

		$where = '';
		if(count($wheres) > 0){
			$where = ' where ' . implode(' and ', $wheres);
		}
		
		if (empty($order)) {
			$order = ' order by a.created desc';
		}

		$result = new stdClass();
		if (!$listonly) {
			$query = 'select count(*) from ' . T_QUIZ_QUIZZES . ' a' 
				. (($action == 5) ? ' left join ' . T_QUIZ_RESPONSES.' r on a.id=r.quiz_id' : '')
				. $where;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
			jimport('joomla.html.pagination');
			$result->pagination = new JPagination($total, $limitstart, $limit);
		}

		$query = 'select a.id, a.title, a.alias, a.description, a.catid, a.created_by, a.created, a.responses,'
				. ' a.show_answers, a.ip_address, a.show_template, a.published, rtg.rating,'
                . ' c.title as category, c.alias as calias, u.'.$config[CQ_USER_NAME].' as username'
                . (($action == 5) ? ', r.id as response_id, r.created as responded_on' : '')
                . ' from ' . T_QUIZ_QUIZZES . ' a '
                . ' left join '.T_QUIZ_CATEGORIES.' c ON a.catid=c.id '
                . ' left join '.T_CJ_RATING.' rtg on rtg.asset_id='.CQ_ASSET_ID.' and rtg.item_id=a.id'
                . ' left join #__users u ON a.created_by=u.id'
                . (($action == 5) ? ' left join ' . T_QUIZ_RESPONSES.' r on a.id=r.quiz_id' : '') 
                . $where . $order;
		$this->_db->setQuery($query, $limitstart, $limit);
		$result->rows = $this->_db->loadObjectList();
		return $result;
    }
	
	function search($search, $limit=20, $pagination=0, $limitstart=0, $method='any', $ordering='newest') {
		$text = trim($search);
		$config = CommunityQuizHelper::getConfig();
		
		if(empty($text)){
			return array();
		}
		$wheres = array();
		$where = '';
		switch ($method) {
			//search exact
			case 'exact':
				$text			= $this->_db->Quote( '%'.$this->_db->getEscaped( $text, true ).'%', false );
				$wheres2		= array();
				$wheres2[]		= 'LOWER(a.title) LIKE '.$text;
				$where			= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;

				//search all or any
			case 'all':
			case 'any':
			default:
				$words         = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word){
					if(strpos($config[CQ_FILTERED_KEYWORDS], $word) > 0){
						continue;
					}
					$word        = $this->_db->Quote( '%'.$this->_db->getEscaped( $word, true ).'%', false );
					$wheres2     = array();
					$wheres2[]   = 'LOWER(a.title) LIKE '.$word;
					$wheres[]    = implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($method == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}

		//ordering of the results
		switch ( $ordering ) {
			//alphabetic, ascending
			case 'alpha':
				$order = 'a.title ASC';
				break;

				//oldest first
			case 'oldest':
				$order = 'a.created ASC';
				break;
					
				//popular first
			case 'popular':
				$order = 'a.responses DESC';
				break;
					
				//newest first
			case 'newest':
				$order = 'a.created DESC';
				break;
					
				//default setting: alphabetic, ascending
			default:
				$order = 'a.title ASC';
		}

		$query = 'select a.id, a.title, a.alias, a.description, a.catid, a.created_by, a.created, a.responses, a.show_answers, a.ip_address, a.show_template, '
                . ' rtg.rating, c.title as category, c.alias as calias, u.'.$config[CQ_USER_NAME].' as username'
                . ' from ' . T_QUIZ_QUIZZES . ' a '
                . ' left join ' . T_QUIZ_CATEGORIES . ' c ON a.catid=c.id '
                . ' left join '.T_CJ_RATING.' rtg on rtg.asset_id='.CQ_ASSET_ID.' and rtg.item_id=a.id'
                . ' left join #__users u ON a.created_by=u.id'
                . ' where ' . $where 
				. ' group by a.id'
                . ' order by ' . $order;
		$this->_db->setQuery( $query, $limitstart, $limit );
		$result = $this->_db->loadObjectList();

		return $result;
	}
    
    function get_quiz_details($id){
    	$config = &CommunityQuizHelper::getConfig();
    	$query = 'select a.id, a.title, a.alias, a.description, a.catid, a.created_by, a.created, a.responses, a.ip_address, a.duration, a.published,'
    		. ' a.show_answers, a.show_template, a.multiple_responses, c.title as category, c.alias as calias, u.'.$config[CQ_USER_NAME].' as username, rtg.rating'
    		. ' from '.T_QUIZ_QUIZZES.' a '
    		. ' left join '.T_QUIZ_CATEGORIES.' c on a.catid=c.id'
    		. ' left join #__users u on a.created_by=u.id'
    		. ' left join '.T_CJ_RATING.' rtg on rtg.asset_id='.CQ_ASSET_ID.' and rtg.item_id=a.id'
    		. ' where a.id='.$id;
    	$this->_db->setQuery($query);
    	return $this->_db->loadObject();
    }
    
    function create_edit_quiz(){
    	$user = &JFactory::getUser();
    	$config = &CommunityQuizHelper::getConfig();
    	
    	$id = JRequest::getVar('id', 0, 'post', 'int');
    	$title = JRequest::getVar('title', null, 'post', 'string');
    	$alias = JRequest::getVar('alias', null, 'post', 'string');
    	$catid = JRequest::getVar('category', 0, 'post', 'int');
    	$show_answers = JRequest::getVar('show-result', 0, 'post', 'int');
    	$show_template = JRequest::getVar('show-template', 1, 'post', 'int');
    	$multiple_responses = JRequest::getVar('multiple_responses', 1, 'post', 'int');
    	$duration = JRequest::getVar('duration', 0, 'post', 'int');
    	
    	if(empty($title) || empty($catid)){
    		JFactory::getApplication()->enqueueMessage(JText::_('MSG_REQUIRED_FIELDS_MISSING'));
    		return false;
    	}
    	
    	$description = '';
		if (CAuthorization::authorise('quiz.wysiwyg')) {
			$description = JRequest::getVar('description', null, 'post', 'string', JREQUEST_ALLOWRAW);
		} else {
			$description = JRequest::getVar('description', null, 'post', 'string');
			$description = nl2br($description);
		}
		$description = CommunityQuizHelper::filterData($description);
		if(!$alias){
			$alias = JFilterOutput::stringURLSafe($title);
		}
		$ip_address = CommunityQuizHelper::get_user_ip();
		$query = '';
		if($id){
			if(!$this->authorize_quiz($id)){
				return false;
			}
			
			$query = 'update '.T_QUIZ_QUIZZES.' set'
				. ' title = '.$this->_db->quote($title) . ','
				. ' alias = '.$this->_db->quote($alias) . ','
				. ' description = '.$this->_db->quote($description) . ','
				. ' catid = '.$catid . ','
				. ' duration = '.$duration.','
				. ' show_answers = '.$show_answers . ','
				. ' show_template = '.$show_template . ','
				. ' multiple_responses = '.$multiple_responses
				. ' where id='.$id;
		}else{
			$createdate = JFactory::getDate();
			$createdate = $createdate->toMySQL();
			
			$query = 'insert into '.T_QUIZ_QUIZZES.'(title, alias, description, created, catid, created_by, '
				. 'show_answers, show_template, multiple_responses, ip_address, duration, published) values ('
				. $this->_db->quote($title) . ','
				. $this->_db->quote($alias) . ','
				. $this->_db->quote($description) . ','
				. $this->_db->quote($createdate) . ','
				. $catid . ','
				. $user->id . ','
				. $show_answers . ','
				. $show_template . ','
				. $multiple_responses . ','
				. $this->_db->quote($ip_address) . ','
				. $duration . ','
				. '3' 
				. ')';
		}
		$this->_db->setQuery($query);
		if($this->_db->query()){
			if(!$id){ //New quiz
				$id = $this->_db->insertid();
				$this->create_page($id);
				
				if($config[CQ_ENABLE_MODERATION] != '1'){
					$query = 'update '.T_QUIZ_CATEGORIES.' set quizzes=quizzes+1 where id='.$catid;
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
			return $id;
		}
		return false;
    }
    
    function get_categories(){
		$tree = new QuizCategories($this->_db, T_QUIZ_CATEGORIES);
		return $tree->get_category_tree();
    }

	function get_category_flat_list($parent=0) {
		$tree = new QuizCategories($this->_db, T_QUIZ_CATEGORIES);
		return $tree->get_category_flat_list($parent);
	}

	function get_category_name($catid) {
		$db = &JFactory::getDBO();
		$query = 'select title from ' . T_QUIZ_CATEGORIES . ' where id=' . $catid;
		$db->setQuery($query);
		return $db->loadResult();
	}

	function get_breadcrumbs($catid){
		$db = &JFactory::getDBO();
		$tree = new QuizCategories($db, T_QUIZ_CATEGORIES);
		return $tree->get_breadcrumbs($catid);
	}
	
    function get_questions($quiz_id, $page_id=0){
		$user = &JFactory::getUser ();
		$where = '';
		if($page_id){
			$where = ' and page_number=' . $page_id;
		}
		$query = 'select id, quiz_id, question_type, page_number, sort_order, include_custom, mandatory, title, description, answer_explanation from ' . T_QUIZ_QUESTIONS .
                ' where quiz_id=' . $quiz_id . $where . ' order by page_number, sort_order asc';
		$this->_db->setQuery ( $query );
		$questions = $this->_db->loadObjectList ( 'id' );
		if (!empty($questions)) {
			$query = 'select id, question_id, answer_type, title, correct_answer from ' . T_QUIZ_ANSWERS .
                    ' where quiz_id=' . $quiz_id . ' and question_id in (select id from ' . T_QUIZ_QUESTIONS . 
                    	' where quiz_id=' . $quiz_id . $where . ')' .
                    ' order by id asc';
			$this->_db->setQuery ( $query );
			$answers = $this->_db->loadObjectList ();
			if ($answers && (count ( $answers ) > 0)) {
				foreach ( $answers as $answer ) {
					$questions [$answer->question_id]->answers [] = $answer;
				}
				return $questions;
			} else {
				$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10075<br>query: ' . $query . '<br><br>' );
				return false;
			}
		} else {
			$error = $this->_db->getErrorMsg ();
			if (! empty ( $error )) {
				$this->setError ( $error . '<br><br> Error code: 10076<br>query: ' . $query . '<br><br>' );
			}
			return false;
		}
    }

	function create_page($quiz_id) {
		if(!$this->authorize_quiz($quiz_id)){
			$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10020<br>query: ' . $query . '<br><br>' );
			return false;
		}
		// Now safe to create new page
		$this->_db = &JFactory::getDBO ();
		$query = 'select max(sort_order)+1  as sort_order from ' . T_QUIZ_PAGES . ' where quiz_id=' . $quiz_id;
		$this->_db->setQuery ( $query );
		$sort_order = $this->_db->loadResult ();
		if (! $sort_order) {
			$sort_order = 1;
		}
		$query = 'insert into ' . T_QUIZ_PAGES . ' (quiz_id, sort_order) values (' . $quiz_id . ',' . $sort_order . ')';
		$this->_db->setQuery ( $query );
		if (! $this->_db->query ()) {
			$this->setError (
			$this->_db->getErrorMsg () . '<br><br> Error code: 10021<br>query: ' . $query . '<br><br>' );
			return false;
		} else {
			return $this->_db->insertid ();
		}
	}

	function remove_page($quiz_id, $pid) {
		if(!$this->authorize_quiz($quiz_id)){
			$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10030<br>query: ' . $query . '<br><br>' );
			return false;
		}
		
		$query = 'delete from '.T_QUIZ_ANSWERS.' where question_id in (select id from '.T_QUIZ_QUESTIONS.' where page_number='.$pid.')';
		$this->_db->setQuery($query);
		if($this->_db->query()){
			$query = 'delete from '.T_QUIZ_QUESTIONS.' where page_number='.$pid;
			$this->_db->setQuery($query);
			if($this->_db->query()){
				$query = 'delete from ' . T_QUIZ_PAGES . ' where quiz_id=' . $quiz_id . ' and id=' . $pid . ' and sort_order > 1';
				$this->_db->setQuery ( $query );
				if ($this->_db->query ()) {
					return true;
				}
			}
		}
		$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10031<br>query: ' . $query . '<br><br>' );
		return false;
	}
	
    function get_pages($quiz_id){
		$query = 'select id from ' . T_QUIZ_PAGES . ' where quiz_id=' . $quiz_id . ' order by sort_order asc';
		$this->_db->setQuery ( $query );
		return $this->_db->loadResultArray ();
    }
	
	function delete_question($quiz_id, $pid, $qid){
		if(!$this->authorize_quiz($quiz_id)){
			$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10050<br>query: ' . $query . '<br><br>' );
			return false;
		}
		$query = 'delete from '.T_QUIZ_ANSWERS.' where quiz_id='.$quiz_id.' and question_id='.$qid;
		$this->_db->setQuery($query);
		if($this->_db->query()){
			$query = 'delete from '.T_QUIZ_QUESTIONS.' where quiz_id='.$quiz_id.' and id='.$qid;
			$this->_db->setQuery($query);
			if($this->_db->query()){
				$query = 'select id from '.T_QUIZ_QUESTIONS.' where quiz_id='.$quiz_id.' and page_number='.$pid.' order by sort_order asc';
				$this->_db->setQuery($query);
				$questions = $this->_db->loadResultArray();
				$order = 1;
				foreach ($questions as $question){
					$query = 'update '.T_QUIZ_QUESTIONS.' set sort_order='.$order.' where id='.$question;
					$this->_db->setQuery($query);
					$this->_db->query();
					$order++;
				}
				return true;
			}
		}
		$this->setError($this->_db->getErrorMsg());
		return false;
	}
	
	function reorder_question($quiz_id,$qid,$direction){
		if(!$this->authorize_quiz($quiz_id)){
			$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10051<br>query: ' . $query . '<br><br>' );
			return false;
		}
		$query = 'select sort_order from '.T_QUIZ_QUESTIONS. ' where id='.$qid. ' and quiz_id='.$quiz_id;
		$this->_db->setQuery($query);
		$my_order = (int)$this->_db->loadResult();
		$swap = null;
		if($direction == 1){
			$query = 'select id, sort_order from '.T_QUIZ_QUESTIONS
				. ' where quiz_id='.$quiz_id.' and sort_order < '.$my_order
				. ' order by sort_order desc';
			$this->_db->setQuery($query, 0, 1);
			$swap = $this->_db->loadObject();
		}else{
			$query = 'select id, sort_order from '.T_QUIZ_QUESTIONS
				. ' where quiz_id='.$quiz_id.' and sort_order > '.$my_order
				. ' order by sort_order asc';
			$this->_db->setQuery($query, 0, 1);
			$swap = $this->_db->loadObject();
		}
		
		if($swap && $swap->id){
			$query = 'update '.T_QUIZ_QUESTIONS.' set sort_order='.$my_order.' where id='.$swap->id;
			$this->_db->setQuery($query);
			if($this->_db->query()){
				$query = 'update '.T_QUIZ_QUESTIONS.' set sort_order='.$swap->sort_order.' where id='.$qid.' and quiz_id='.$quiz_id;
				$this->_db->setQuery($query);
				if($this->_db->query()){
					return $swap->sort_order;
				}
			}
		}
		$this->setError($this->_db->getErrorMsg());
		return false;
	}
    
	function save_question() {
		$user = JFactory::getUser ();
		$query = '';
		// Request parameters
		$quiz_id = JRequest::getVar ( 'id', 0, 'post', 'int' );
		$pid = JRequest::getVar ( 'pid', 0, 'post', 'int' );
		$qid = JRequest::getVar ( 'qid', 0, 'post', 'int' );
		$order = JRequest::getVar ( 'order', 0, 'post', 'int' );
		$qtype = JRequest::getVar ( 'qtype', 0, 'post', 'int' );
		$title = JRequest::getVar ( 'question_title', null, 'post', 'string' );
		$mandatory = JRequest::getVar ( 'optmandatory', 0, 'post', 'int' );
		$custom_choice = JRequest::getVar ( 'optcustomchoice', 0, 'post', 'int' );
		$description = JRequest::getVar ( 'question_description', null, 'post', 'string' );
		if(CAuthorization::authorise('quiz.wysiwyg')){
			$description = JRequest::getVar ( 'question_description', '', 'post', 'string', JREQUEST_ALLOWRAW );
			$description = CommunityQuizHelper::filterData($description);
		}
		
		if ($quiz_id == 0 || $pid == 0 || $qtype > 9 || $qtype <= 0 || $user->guest || strlen ( $title ) <= 0) {
			$this->setError (JText::_ ( 'MSG_UNAUTHORIZED' ) . '<br><br>Error code: 10001<br>id: ' . $quiz_id . '<br>pid: ' . $pid . '<br>qtype: ' . $qtype . '<br>title: ' . $title . '<br><br>' );
			return false;
		}
		if ($qtype == 2 || $qtype == 3 || $qtype == 4) {
			// Check if question type is choice, if yes update question type accordingly
			$choicetype = JRequest::getVar ( 'optchoicetype', 2, 'post', 'int' );
			$qtype = ($choicetype == 3) ? QT_CHOICE_CHECKBOX : (($choicetype == 4) ? QT_CHOICE_SELECT : QT_CHOICE_RADIO);
		} else if ($qtype == 7 || $qtype == 8 || $qtype == 9) {
			// Check if question type is free text, if yes update question type accordingly
			$choicetype = JRequest::getVar ( 'opttexttype', 7, 'post', 'int' );
			$qtype = ($choicetype == 8) ? QT_FREE_TEXT_MULTILINE : (($choicetype == 9) ? QT_FREE_TEXT_PASSWORD : (($choicetype == 10) ? QT_FREE_TEXT_RICH_TEXT : QT_FREE_TEXT_SINGLE_LINE));
		}
		
		if($qtype == 1){
			$order = 1;
		}else{
			if($order < 2){
				$order = 2;
			}
		}
		
		if(!$this->authorize_quiz($quiz_id)){
			$this->setError ($this->_db->getErrorMsg () . '<br><br> Error code: 10002<br>query: ' . $query . '<br><br>' );
			return false;
		}
		
		// First manipulate with question related changes
		if ($qid > 0) {
			// Be on safe side, check if user sending manipulated values.
			$query = 'select count(*) from ' . T_QUIZ_QUESTIONS . ' where id=' . $qid . ' and quiz_id='.$quiz_id;
			$this->_db->setQuery ( $query );
			$count = ( int )$this->_db->loadResult ();
			if ( !$count ) {
				$this->setError ( $this->_db->getErrorMsg () . '<br><br> Error code: 10003<br>query: ' . $query . '<br><br>' );
				return false;
			}
			
			// Genuine request, update question
			$query = 'update ' . T_QUIZ_QUESTIONS 
				. ' set title=' . $this->_db->quote ( $title )
				. ', description='.$this->_db->quote( $description )
				. ', mandatory=' . $mandatory 
				. ', question_type=' . $qtype 
				. ', include_custom=' . $custom_choice 
				. ' where id=' . $qid;
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError (
				$this->_db->getErrorMsg () . '<br><br> Error code: 10004<br>query: ' . $query . '<br><br>' );
				return false;
			} else {
				$query = 'delete from ' . T_QUIZ_ANSWERS . ' where question_id=' . $qid;
				$this->_db->setQuery ( $query );
				if (! $this->_db->query ()) {
					$this->setError (
					$this->_db->getErrorMsg () . '<br><br> Error code: 10005<br>query: ' . $query . '<br><br>' );
					return false;
				}
			}
		} else {
			// New question, insert
			$query = 'insert into ' . T_QUIZ_QUESTIONS . ' (title,description,quiz_id,page_number,question_type,created_by,mandatory,include_custom,sort_order) values (' 
				. $this->_db->quote ( $title ) . ','
				. $this->_db->quote ( $description ) . ',' 
				. $quiz_id . ',' 
				. $pid . ',' 
				. $qtype . ',' 
				. $user->id . ',' 
				. $mandatory . ',' 
				. $custom_choice . ',' 
				. $order . ')';
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError (
				$this->_db->getErrorMsg () . '<br><br> Error code: 10006<br>query: ' . $query . '<br><br>' );
				return false;
			} else {
				// Success, get the inserted id for further operation
				$qid = $this->_db->insertid ();
			}
		}

		// Move on to question answers (do not confuse with responses please, these are answers for questions like labels for multiple choice)
		switch ($qtype) {
			case QT_PAGE_HEADER : // Page header
				$note = JRequest::getVar ( 'description', null, 'post', 'string' );
				$query = 'insert into ' . T_QUIZ_ANSWERS . '(quiz_id,question_id,answer_type,title) values (' .
				$quiz_id . ',' .
				$qid . ',' .
				$this->_db->quote ( 'note' ) . ',' .
				$this->_db->quote ( $note ) . ')';
				$this->_db->setQuery ( $query );
				if (! $this->_db->query ()) {
					$this->setError (
					$this->_db->getErrorMsg () . '<br><br> Error code: 10007<br>query: ' . $query . '<br><br>' );
					return false;
				} else {
					return $qid;
				}
				break;
			case QT_CHOICE_RADIO : // Multiple choice - radio
			case QT_CHOICE_CHECKBOX : // Multiple choice - checkbox
			case QT_CHOICE_SELECT : // Multiple choice - select
				$choices = JRequest::getVar ( 'choices', array (), 'post', 'array' );
				if (count ( $choices ) <= 0) {
					$this->setError ( JText::_ ( 'MSG_NO_CHOICES_FOUND' ) . ' Error code: 10008' );
					return false;
				}
				$query = 'insert into ' . T_QUIZ_ANSWERS . '(quiz_id,question_id,answer_type,title) values ';
				foreach ( $choices as $choice ) {
					if (! empty ( $choice )) {
						$query = $query . '(' . $quiz_id . ',' . $qid . ',' . $this->_db->quote ( 'x' ) . ',' . $this->_db->quote ( $choice ) . '),';
					}
				}
				$query = substr ( $query, 0, - 1 );
				$this->_db->setQuery ( $query );
				if (! $this->_db->query ()) {
					$this->setError (
					$this->_db->getErrorMsg () . '<br><br> Error code: 10009<br>query: ' . $query . '<br><br>' );
					return false;
				} else {
					return $qid;
				}
				break;
			case QT_GRID_RADIO : // Grid Radio
			case QT_GRID_CHECKBOX : // Grid Checkbox
				$grid_rows = JRequest::getVar ( 'grid_rows', array (), 'post', 'array' );
				$grid_columns = JRequest::getVar ( 'grid_columns', array (), 'post', 'array' );
				if ((count ( $grid_rows ) <= 0) || (count ( $grid_columns ) <= 0)) {
					$this->setError ( JText::_ ( 'MSG_NO_CHOICES_FOUND' ) . ' Error code: 10010' );
					return false;
				}
				$query = 'insert into ' . T_QUIZ_ANSWERS . '(quiz_id,question_id,answer_type,title) values ';
				foreach ( $grid_rows as $choice ) {
					if (! empty ( $choice )) {
						$query = $query . '(' . $quiz_id . ',' . $qid . ',' . $this->_db->quote ( 'x' ) . ',' . $this->_db->quote ( $choice ) . '),';
					}
				}
				foreach ( $grid_columns as $choice ) {
					if (! empty ( $choice )) {
						$query = $query . '(' . $quiz_id . ',' . $qid . ',' . $this->_db->quote ( 'y' ) . ',' . $this->_db->quote ( $choice ) . '),';
					}
				}
				$query = substr ( $query, 0, - 1 );
				$this->_db->setQuery ( $query );
				if (! $this->_db->query ()) {
					$this->setError (
					$this->_db->getErrorMsg () . '<br><br> Error code: 10011<br>query: ' . $query . '<br><br>' );
					return false;
				} else {
					return $qid;
				}
				break;
			case QT_FREE_TEXT_SINGLE_LINE : // Free text - Single line
			case QT_FREE_TEXT_MULTILINE : // Free text - Multiline
			case QT_FREE_TEXT_PASSWORD : // Free text - Password
			case QT_FREE_TEXT_RICH_TEXT : // Free text - Rich Text
				$query = 'insert into ' . T_QUIZ_ANSWERS . '(quiz_id,question_id,answer_type) values ' . '(' . $quiz_id . ',' . $qid . ',' . $this->_db->quote ( 'text' ) . ')';
				$this->_db->setQuery ( $query );
				if (! $this->_db->query ()) {
					$this->setError (
					$this->_db->getErrorMsg () . '<br><br> Error code: 10012<br>query: ' . $query . '<br><br>' );
					return false;
				} else {
					return $qid;
				}
				break;
		}
	}
	
	function finalize_quiz($id){
		$user = &JFactory::getUser();
		$config = &CommunityQuizHelper::getConfig();
		if($id){
			if(!$this->authorize_quiz($id)){
				return false;
			}
			
			$query = '';
			if($config[CQ_ENABLE_MODERATION] == '1'){
				$query = 'update '.T_QUIZ_QUIZZES.' set published = 2 where id='.$id.' and published != 1';
				$this->_db->setQuery($query);
				if($this->_db->query()){
					return true;
				}
			}else{
				$query = 'update '.T_QUIZ_QUIZZES.' set published = 1 where id='.$id;
				$this->_db->setQuery($query);
				if($this->_db->query()){
					$query = 'update '.T_QUIZ_CATEGORIES.' set quizzes=quizzes+1'
						. ' where id=(select catid from '.T_QUIZ_QUIZZES.' where id='.$id.' and published != 1)';
					$this->_db->setQuery($query);
					if($this->_db->query()){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	function do_create_update_response(){
		$app = JFactory::getApplication();
		$user = &JFactory::getUser();
		$quiz_id = JRequest::getInt('id', 0);
		$response_id = JRequest::getInt('rid', 0);
		$page_id = JRequest::getVar('page_number', 0, 'post', 'int');
		$created = 'now';
		
		if($quiz_id && !$response_id){
			if(!$user->guest){
				$query = 'select id, created from '.T_QUIZ_RESPONSES.' where created_by='.$user->id.' and quiz_id='.$quiz_id.' and completed=0';
				$this->_db->setQuery($query);
				$response = $this->_db->loadObject();
				if(!empty($response)){
					$response_id = $response->id;
					$created = $response->created;
				}
				$page_id = 0;
			}
		} 
		if($response_id){
			$query = 'select created, quiz_id from '.T_QUIZ_RESPONSES.' where id='.$response_id;
			$this->_db->setQuery($query);
			$response = $this->_db->loadObject();
			
			if(!empty($response)){
				$quiz_id = $response->quiz_id;
				$created = $response->created;
			}
		}
		if(!$quiz_id){
			$this->setError(JText::_('MSG_NO_QUIZ_FOUND'));
			return false;
		}
		
		// Check if quiz is published
		$query = 'select count(*) from '.T_QUIZ_QUIZZES.' where published=1 and id='.$quiz_id;
		$this->_db->setQuery($query);
		$result = (int)$this->_db->loadResult();
		if(!$result){
			$this->setError(JText::_('MSG_UNAUTHORIZED'));
			return false;
		}
		
		// Get the quiz details
		$quiz = &$this->get_quiz_details($quiz_id);
		
		if($quiz->multiple_responses != '1' && !$user->guest){
			$query = 'select count(*) from '.T_QUIZ_RESPONSES.' where quiz_id='.$quiz->id.' and created_by='.$user->id.' and completed=1';
			$this->_db->setQuery($query);
			$count = (int)$this->_db->loadResult();
			if($count > 0){
				$this->setError(JText::_('MSG_ALREADY_TAKEN'));
				return false;
			}
		}
		
		// Create new response if not exists
		if(!$response_id){
			$ip_address = CommunityQuizHelper::get_user_ip();
			$location = CommunityQuizHelper::get_user_location($ip_address);
			$created = JFactory::getDate()->toMySQL();
			$query = 'insert into '.T_QUIZ_RESPONSES.'(quiz_id, created_by, completed, created, ip_address, country, browser_info) values ('
				. $quiz_id . ','
				. $user->id . ','
				. '0,'
				. $this->_db->quote($created) . ','
				. $this->_db->quote($ip_address) . ','
				. $this->_db->quote($location['country_code']) . ','
				. $this->_db->quote($_SERVER['HTTP_USER_AGENT'])
				. ')';
			$this->_db->setQuery($query);
			if(!$this->_db->query()){
				$this->setError(JText::_('MSG_UNABLE_TO_CREATE_RESPONSE'));
				return false;
			}
			$response_id = $this->_db->insertid();
			$page_id = 0;
		}
		
		$quiz->response_id = $response_id;
		$quiz->response_created = $created;
		$quiz->current_page = $page_id;
		
		return $quiz;
	}
	
	function get_next_page($quiz_id, $current_page){
		$wheres = array();
		
		$wheres[] = 'quiz_id='.$quiz_id;
		if(empty($current_page)){
			$wheres[] = 'sort_order > 0';
		}else{
			$wheres[] = 'sort_order > '.$current_page;
		}
		$wheres[] = 'id in (select page_number from '.T_QUIZ_QUESTIONS.' where quiz_id='.$quiz_id.')';
		$where = ' where '.implode(' and ', $wheres);
		
		$query = 'select id, sort_order from '.T_QUIZ_PAGES.$where.' order by sort_order asc';
		$this->_db->setQuery($query, 0, 2);
		return $this->_db->loadObjectList();
	}
	
	function is_response_expired($quiz_id, $response_id){
		$query = 'select duration from '.T_QUIZ_QUIZZES.' where id='.$quiz_id;
		$this->_db->setQuery($query);
		$duration = (int)$this->_db->loadResult();
		if($duration == 0) return false;
		
		$query = 'select created from '.T_QUIZ_RESPONSES.' where id='.$response_id;
		$this->_db->setQuery($query);
		$created = $this->_db->loadResult();

		if(empty($created)) return true;
		
		jimport('joomla.utilities.date');
		$created = new JDate($created);
		$now = new JDate();
		
		if(($now->toUnix() - $created->toUnix()) > ($duration*60 + 10)){
			return true;
		}
		
		return false;
	}
	
	function save_response($quiz_id, $pid, $rid){
		$user = &JFactory::getUser ();	
		$questions = &$this->get_questions( $quiz_id, $pid );
		if (! empty ( $questions )) {
			$answers = array ();
			foreach ( $questions as $question ) {
				$free_text = null;
				switch ($question->question_type) {
					case 2 : // Choice - Radio
					case 4 : // Choice - Select box
						$answer_id = JRequest::getVar ( 'answer' . $question->id, 0, 'post', 'int' );
						$free_text = JRequest::getVar ( 'free_text' . $question->id, null, 'post', 'string' );
						if ($answer_id) {
							$answer = array ();
							$answer['question_id'] = $question->id ;
							$answer['answer_id'] = $answer_id;
							$answer['column_id'] = 0;
							$answer['free_text'] = null;
							array_push ( $answers, $answer );
						}
						break;
					case 3 : // Choice - Checkbox
						$answer_ids = JRequest::getVar ( 'answer' . $question->id, array (), 'post', 'array' );
						$free_text = JRequest::getVar ( 'free_text' . $question->id, null, 'post', 'string' );
						JArrayHelper::toInteger ( $answer_ids );
						if (! empty ( $answer_ids )) {
							foreach ( $answer_ids as $answer_id ) {
								$answer = array ();
								$answer ['question_id'] = $question->id;
								$answer ['answer_id'] = $answer_id;
								$answer ['column_id'] = 0;
								$answer ['free_text'] = null;
								array_push ( $answers, $answer );
							}
						}
						break;
					case 5 : // Grid - Radio
						$rows = array ();
						$columns = array ();
						foreach ( $question->answers as $answer ) {
							if ($answer->answer_type == 'x') {
								$rows [] = $answer;
							} else if ($answer->answer_type == 'y') {
								$columns [] = $answer;
							}
						}
						$free_text = JRequest::getVar ( 'free_text' . $question->id, null, 'post', 'string' );
						foreach ( $rows as $row ) {
							$column_id = JRequest::getVar ( 'answer' . $question->id . $row->id, 0, 'post', 'int' );
							if ($column_id) {
								$answer = array ();
								$answer ['question_id'] = $question->id;
								$answer ['answer_id'] = $row->id;
								$answer ['column_id'] = $column_id;
								$answer ['free_text'] = null;
								array_push ( $answers, $answer );
							}
						}
						break;
					case 6 : // Grid - Checkbox
						$rows = array ();
						$columns = array ();
						foreach ( $question->answers as $answer ) {
							if ($answer->answer_type == 'x') {
								$rows [] = $answer;
							} else if ($answer->answer_type == 'y') {
								$columns [] = $answer;
							}
						}
						$free_text = JRequest::getVar ( 'free_text' . $question->id, null, 'post', 'string' );
						foreach ( $rows as $row ) {
							$column_ids = JRequest::getVar ( 'answer' . $question->id . $row->id, array (), 'post', 'array' );
							JArrayHelper::toInteger ( $column_ids );
							if (! empty ( $column_ids )) {
								foreach ( $column_ids as $column_id ) {
									$answer = array ();
									$answer ['question_id'] = $question->id;
									$answer ['answer_id'] = $row->id;
									$answer ['column_id'] = $column_id;
									$answer ['free_text'] = null;
									array_push ( $answers, $answer );
								}
							}
						}
						break;
					case 7 : // Freetext - Singleline
					case 8 : // Freetext - Multiline
					case 9 : // Freetext - Password
						$free_text = JRequest::getVar ( 'free_text'.$question->id, null, 'post', 'string' );
						break;
					case 10 : // Freetext - Password
						$free_text = JRequest::getVar ( 'free_text'.$question->id, null, 'post', 'string' );
						if(CAuthorization::authorise('quiz.wysiwyg')){
							$free_text = JRequest::getVar ( 'free_text'.$question->id, '', 'post', 'string', JREQUEST_ALLOWRAW );
						}
						$free_text = CommunityQuizHelper::filterData($free_text);
						break;
				}
				if($free_text) {
					$answer = array ();
					$answer ['question_id'] = $question->id;
					$answer ['answer_id'] = 0;
					$answer ['column_id'] = 0;
					$answer ['free_text'] = $free_text;
					array_push ( $answers, $answer );
				}
			}

			$query = 'delete from '.T_QUIZ_RESPONSE_DETAILS . ' where response_id='.$rid.' and question_id in ' .
                    '(select id from '.T_QUIZ_QUESTIONS.' where quiz_id='.$quiz_id.' and page_number='.$pid.')';
			$this->_db->setQuery($query);
			if($this->_db->query()){
				
				$query = '';
				foreach ( $answers as $answer ) {
					if (empty ( $answer ['free_text'] )) {
						$answer ['free_text'] = 'null';
					} else {
						$answer ['free_text'] = $this->_db->quote ( $answer ['free_text'] );
					}
					$query = $query . '(' . $rid . ',' . $answer ['question_id'] . ',' . $answer ['answer_id'] . ',' . $answer ['column_id'] . ',' . $answer ['free_text'] . '),';
				}
				if(!empty($query)){
					$query = 'insert into ' . T_QUIZ_RESPONSE_DETAILS . '(response_id, question_id, answer_id, column_id, free_text) values '.$query;
					$query = substr($query, 0, -1);
					$this->_db->setQuery($query);
					if($this->_db->query()){
						return true;
					}
				}else{
					return true;
				}
			}
		}
		return false;
	}
	
	function save_correct_answers($quiz_id){
		$user = &JFactory::getUser ();
		$questions = &$this->get_questions( $quiz_id );
		if(!$this->authorize_quiz($quiz_id)){
			return false;
		}
		if (! empty ( $questions )) {
			$answers = array ();
			$queries = array();
			$queries[] = 'update '.T_QUIZ_ANSWERS.' set correct_answer=0 where quiz_id='.$quiz_id;
			foreach ( $questions as $question ) {
				switch ($question->question_type) {
					case 2 : // Choice - Radio
					case 4 : // Choice - Select box
						$answer_id = JRequest::getVar ( 'answer' . $question->id, 0, 'post', 'int' );
						$queries[] = 'update '.T_QUIZ_ANSWERS.' set correct_answer=1 where id='.$answer_id;
						break;
					case 3 : // Choice - Checkbox
						$answer_ids = JRequest::getVar ( 'answer' . $question->id, array (), 'post', 'array' );
						JArrayHelper::toInteger ( $answer_ids );
						if (! empty ( $answer_ids )) {
							$queries[] = 'update '.T_QUIZ_ANSWERS.' set correct_answer=1 where id in ('.implode(',', $answer_ids).')';
						}
						break;
					case 5 : // Grid - Radio
						$rows = array ();
						$columns = array ();
						foreach ( $question->answers as $answer ) {
							if ($answer->answer_type == 'x') {
								$rows [] = $answer;
							} else if ($answer->answer_type == 'y') {
								$columns [] = $answer;
							}
						}
						foreach ( $rows as $row ) {
							$column_id = JRequest::getVar ( 'answer' . $question->id . $row->id, 0, 'post', 'int' );
							$queries[] = 'update '.T_QUIZ_ANSWERS.' set correct_answer='.$column_id.' where id='.$row->id;
						}
						break;
					case 6 : // Grid - Checkbox
						$rows = array ();
						$columns = array ();
						foreach ( $question->answers as $answer ) {
							if ($answer->answer_type == 'x') {
								$rows [] = $answer;
							} else if ($answer->answer_type == 'y') {
								$columns [] = $answer;
							}
						}
						foreach ( $rows as $row ) {
							$column_ids = JRequest::getVar ( 'answer' . $question->id . $row->id, array (), 'post', 'array' );
							JArrayHelper::toInteger ( $column_ids );
							if (! empty ( $column_ids )) {
								$queries[] = 'update '.T_QUIZ_ANSWERS.' set correct_answer='.implode(',', $column_ids).' where id='.$row->id;
							}
						}
						break;
				}
				
				$explanation = JRequest::getVar ( 'explanation' . $question->id, '', 'post', 'string', JREQUEST_ALLOWRAW );
				$explanation = CommunityQuizHelper::filterData($explanation);
				$queries[] = 'update '.T_QUIZ_QUESTIONS.' set answer_explanation='.$this->_db->quote($explanation).' where id='.$question->id;
			}
			foreach ($queries as $query){
				$this->_db->setQuery($query);
				if(!$this->_db->query()){
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}
	
	function finalize_response($quiz_id, $response_id){
		$createdate = JFactory::getDate();
		$createdate = $createdate->toMySQL();
		$score = $this->get_score($quiz_id, $response_id);
		$query = 'update '.T_QUIZ_RESPONSES.' set score='.$score.', completed=1, finished='.$this->_db->quote($createdate).' where id='.$response_id;
		$this->_db->setQuery($query);
		if($this->_db->query()){
			$query = 'update '.T_QUIZ_QUIZZES.' set responses=responses+1 where id=(select quiz_id from '.T_QUIZ_RESPONSES.' where id='.$response_id.')';
			$this->_db->setQuery($query);
			$this->_db->query();
			
			$config = CommunityQuizHelper::getConfig();
			if($config[CQ_ENABLE_RATINGS]){
				$rating = JRequest::getInt('quiz-rating', 0);
				if($rating){
					$user = JFactory::getUser();
					$query = 'insert into '.T_CJ_RATING_DETAILS.'(asset_id, item_id, action_id, rating, created_by, created) values'
						. ' ('.CQ_ASSET_ID.','.$quiz_id .','.$response_id.','.$rating.','.$user->id.','.$this->_db->quote($createdate).')';
					$this->_db->setQuery($query);
					if($this->_db->query()){
						$query = 'insert into '.T_CJ_RATING.'(item_id, asset_id, total_ratings, sum_rating, rating) values'
						. ' ('.$quiz_id .','.CQ_ASSET_ID.', 1, '.$rating.','.$rating.')'
						. ' on duplicate key update total_ratings=total_ratings+1, rating=(sum_rating+'.$rating.')/total_ratings, sum_rating=sum_rating+'.$rating;
						$this->_db->setQuery($query);
						$this->_db->query();
					}
				}
			}
			
			return true;
		}
		return false;
	}
	
	function get_score($quiz_id, $response_id){
		$score = 0; $flag = false;
		$questions = $this->get_questions($quiz_id);
		
		if(empty($questions)) return $score;
		
		$query = 'select question_id, answer_id, column_id from '.T_QUIZ_RESPONSE_DETAILS.' where response_id='.$response_id.' order by question_id';
		$this->_db->setQuery($query);
		$response_details = $this->_db->loadObjectList();
		if(empty($response_details)) return $score;

		foreach ($questions as $question_id=>$question){
			switch ($question->question_type){
				case 2 : // Choice - Radio
				case 4 : // Choice - Select box
					$flag = false;
					foreach ($response_details as $response){
						if($response->question_id == $question_id){
							foreach ($question->answers as $answer){
								if($answer->correct_answer == 1){
									if($response->answer_id == $answer->id){
										$score++;
										$flag = true;
										break;
									}else{
										$flag = true;
										break;
									}
								}
							}
						}
						if($flag){
							break;
						}
					}
					break;
				case 3 : // Choice - Checkbox
					$flag = true;
					$flag2 = false;
					$found = false;
					foreach ($response_details as $response){
						if($response->question_id == $question_id){
							$found = true;
							foreach ($question->answers as $answer){
								if($response->answer_id == $answer->id){
									if($answer->correct_answer != 1){
										$flag = false;
										break;
									}else{
										$flag2 = true;
									}
								}
							}							
						}
						if(!$flag){
							break;
						}
					}
					
					if($found && $flag && $flag2){
						$score++;
					}
					break;
				case 5 : // Grid - Radio
					$rows = array ();
					$columns = array ();
					$flag = true;
					$flag2 = false;
					$found = false;
					foreach ( $question->answers as $answer ) {
						if ($answer->answer_type == 'x') {
							$rows [] = $answer;
						} else if ($answer->answer_type == 'y') {
							$columns [] = $answer;
						}
					}
					foreach ($response_details as $response){
						if($response->question_id == $question_id){
							$found = true;
							foreach ($rows as $row){
								if($response->answer_id == $row->id){
									if($response->column_id != $row->correct_answer){
										$flag = false;
										break;
									}else{
										$flag2 = true;
									}
								}
							}
						}
						if(!$flag){
							break;
						}
					}
					if($found && $flag && $flag2){
						$score++;
					}
					break;
			}
		}
		
		return $score;
	}
	
	function get_quiz_id($response_id){
		$query = 'select quiz_id from '.T_QUIZ_RESPONSES.' where id='.$response_id;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	function get_response_details($response_id, $quiz_id=0){
		$user = &JFactory::getUser();
		$query = 'select created_by from '.T_QUIZ_RESPONSES.' where id='.$response_id;
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		if($user->id != $result && $quiz_id > 0){
			$query = 'select created_by from '.T_QUIZ_QUIZZES.' where id='.$quiz_id;
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();
		}
		
		if(($user->id == $result) || CAuthorization::authorise('quiz.manage')){
			$query = 'select a.question_id, a.answer_id, a.column_id, a.free_text'
				. ' from '.T_QUIZ_RESPONSE_DETAILS.' a'
				. ' where a.response_id='.$response_id.' order by a.question_id';
			$this->_db->setQuery($query);
			$responses = $this->_db->loadObjectList();
			return $responses;
		}
		return false;
	}
	
	function authorize_quiz($id){
		$user = &JFactory::getUser();
		if(CAuthorization::authorise('quiz.manage')){
			return true;
		}else{
			$query = 'select count(*) from '.T_QUIZ_QUIZZES.' where id='.$id.' and created_by='.$user->id;
			$this->_db->setQuery($query);
			$count = (int)$this->_db->loadResult();
			if($count){
				return true;
			}
		}
		return false;
	}
	
	function get_quiz_statistics($id){
		$user = JFactory::getUser();
		$config = CommunityQuizHelper::getConfig();
		$quiz = $this->get_quiz_details($id);
		if(empty($quiz) || $quiz->published != 1 || (($quiz->created_by != $user->id) && !CAuthorization::authorise('quiz.manage'))){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'select a.id, a.created_by, a.created, a.ip_address, a.completed, a.score, c.country_name, a.browser_info, u.'.$config[CQ_USER_NAME].' as username'
			. ' from '.T_QUIZ_RESPONSES.' a'
			. ' left join '.T_QUIZ_COUNTRIES.' c on a.country=c.country_code'
			. ' left join #__users u on a.created_by=u.id'
			. ' where a.quiz_id='.$id
			. ' order by a.created desc';
		$this->_db->setQuery($query);
		$quiz->responselist = $this->_db->loadObjectList();
		$this->setError($this->_db->getErrorMsg());
		return $quiz;  
	}
	
	function get_reponse_data_for_csv($id){
		$user = &JFactory::getUser();
		$config = CommunityQuizHelper::getConfig();
		
		$query = 'select created_by from '.T_QUIZ_QUIZZES.' where id='.$id;
		$this->_db->setQuery($query);
		$survey = $this->_db->loadObject();
		if(($survey->created_by != $user->id) && !CAuthorization::authorise('quiz.manage')) {
			$this->setError ( 'Error: 10295 - '.JText::_('MSG_ERROR_PROCESSING') );
			return false;
		}
		
		$query = 'select r.response_id, r.question_id, q.title as question, a.title as answer, b.title as answer2, r.free_text from '.T_QUIZ_RESPONSE_DETAILS.' r'
			. ' left join '.T_QUIZ_QUESTIONS.' q on r.question_id=q.id'
			. ' left join '.T_QUIZ_ANSWERS.' a on r.answer_id=a.id'
			. ' left join '.T_QUIZ_ANSWERS.' b on r.column_id=b.id'
			. ' where r.response_id in (select id from '.T_QUIZ_RESPONSES.' where quiz_id='.$id.')'
			. ' order by r.response_id';
		$this->_db->setQuery($query);
		$entries = $this->_db->loadObjectList();
		
		$query = 'select q.id, q.title from '.T_QUIZ_QUESTIONS.' q where q.quiz_id='.$id. ' order by page_number, sort_order';
		$this->_db->setQuery($query);
		$questions = $this->_db->loadObjectList();
		
		$query = 'select r.id, r.created_by, r.created, u.username, u.name from '.T_QUIZ_RESPONSES.' r left join #__users u on r.created_by=u.id where r.quiz_id='.$id;
		$this->_db->setQuery($query);
		$responses = $this->_db->loadObjectList();
		
		$return = new stdClass();
		$return->entries = $entries;
		$return->questions = $questions;
		$return->responses = $responses;
		
		return $return;
	}
}
?>