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

// Import Joomla! libraries
jimport('joomla.application.component.model');

class CommunityQuizModelQuiz extends JModel {
    function __construct() {
		parent::__construct();
    }
    
    function &get_quizzes($status=0, $limitstart=0, $limit=0){
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest( Q_APP_NAME.".filter_order","filter_order","a.created","cmd" );
        $filter_order_Dir = $app->getUserStateFromRequest( Q_APP_NAME.".filter_order_Dir","filter_order_Dir","DESC","word" );
        
        if(!$limitstart){
        	$limitstart = $app->getUserStateFromRequest( Q_APP_NAME.".limitstart",'limitstart','','int' );
        }
        
        if(!$limit){
            $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        }
        $search = JRequest::getVar("search",null,"post","string");

        $where = array();
        
        if(!$status){
        	$status = JRequest::getVar('status', 0, '', 'int');
        }
        switch ($status) {
        	case '0':
        		$where[] = '(a.published = 0 or a.published = 1 or a.published = 2)';
        		break;
        	case '1':
        		$where[] = 'a.published = 1';
        		break;
        	case '2':
        		$where[] = 'a.published = 0';
        		break;
        	case '3':
        		$where[] = 'a.published = 2';
        		break;
			default:
	        	break;
        }
        
        $catid = JRequest::getInt('catid', 0);
        if($catid){
        	$where[] = 'a.catid='.$catid;
        }
        
        if($search){
        	$tsearch = "%".$this->_db->getEscaped( $search, true )."%";
        	$where[] = "(a.title like ".$this->_db->quote( $tsearch, false ).")";
        }
        
        $where = ((count($where) > 0) ? " where " . implode(" and ", $where):"");
        $order = " order by " . $filter_order . " " . $filter_order_Dir;
        
        $query = 'select count(*) from ' . T_QUIZ_QUIZZES . ' a ' . $where;
        $this->_db->setQuery( $query );
        $total = $this->_db->loadResult();
        
        jimport('joomla.html.pagination');
        $pagination = new JPagination( $total, $limitstart, $limit );

        $query = "select a.id, a.title, a.alias, a.created_by, a.created, a.responses, a.published, u.name, u.username, c.title as category "
        	. " from ".T_QUIZ_QUIZZES." a left join #__users u on a.created_by=u.id"
        	. " left join ".T_QUIZ_CATEGORIES." c on a.catid=c.id"
        	. $where.$order;
        	
        $quizzes = $this->_getList($query, $pagination->limitstart, $pagination->limit);
        
        $lists['order'] = $filter_order;
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['search'] = $search;
        $lists['status'] = $status;
        
        $result->quizzes = $quizzes;
        $result->lists = $lists;
        $result->pagination = $pagination;
        return $result;
    }
    
    function set_status($id, $status){
    	$query = 'update '.T_QUIZ_QUIZZES.' set published = '.($status ? 1 : 0).' where id in ('.$id.')';
    	$this->_db->setQuery($query);
    	if(!$this->_db->query()){
    		return false;
    	}else{
    		if($count = $this->_db->getAffectedRows()){
    			$query = 'update '.T_QUIZ_CATEGORIES.' a '
    				. ' set quizzes=(select count(*) from '.T_QUIZ_QUIZZES.' b where published=1 and b.catid=a.id group by b.catid)'
    				. ' where a.parent_id>0';
    			$this->_db->setQuery($query);
    			if(!$this->_db->query()){
    				return false;
    			}
    		}
    		return true;
    	}
    }
    
    function delete_quizzes($id){
    	$queries = array();
    	$queries[] = 'delete from '.T_QUIZ_RESPONSE_DETAILS.' where response_id in (select id from '.T_QUIZ_RESPONSES.' where quiz_id in ('.$id.'))';
    	$queries[] = 'delete from '.T_QUIZ_RESPONSES.' where quiz_id in ('.$id.')';
    	$queries[] = 'delete from '.T_QUIZ_ANSWERS.' where quiz_id in ('.$id.')';
    	$queries[] = 'delete from '.T_QUIZ_QUESTIONS.' where quiz_id in ('.$id.')';
    	$queries[] = 'delete from '.T_QUIZ_PAGES.' where quiz_id in ('.$id.')';
    	$queries[] = 'update '.T_QUIZ_CATEGORIES.' set quizzes=quizzes-1 where quizzes > 0 and id in (select catid from '.T_QUIZ_QUIZZES.' where id in ('.$id.'))';
    	$queries[] = 'delete from '.T_QUIZ_QUIZZES.' where id in ('.$id.')';
    	
    	foreach ($queries as $query){
    		$this->_db->setQuery($query);
    		if(!$this->_db->query()){
    			return false;
    		}
    	}
    	return true;
    }
    
    function get_quiz_details($id){
    	$query = 'select a.id, a.title, a.alias, a.description, a.catid, a.created_by, a.created, a.responses, a.ip_address, a.duration,'
    		. ' a.show_answers, a.show_template, a.multiple_responses, c.title as category, c.alias as calias, u.name, u.username'
    		. ' from '.T_QUIZ_QUIZZES.' a '
    		. ' left join '.T_QUIZ_CATEGORIES.' c on a.catid=c.id'
    		. ' left join #__users u on a.created_by=u.id'
    		. ' where a.id='.$id;
    	$this->_db->setQuery($query);
    	return $this->_db->loadObject();
    }
    
    function get_questions($quiz_id, $page_id=0){
		$user = &JFactory::getUser ();
		$where = '';
		if($page_id){
			$where = ' and page_number=' . $page_id;
		}
		$query = 'select id, quiz_id, description, answer_explanation, question_type, page_number, responses, sort_order, include_custom, mandatory, title from ' . T_QUIZ_QUESTIONS .
                ' where quiz_id=' . $quiz_id . $where . ' order by page_number, sort_order asc';
		$this->_db->setQuery ( $query );
		$questions = $this->_db->loadObjectList ( 'id' );
		if ($questions) {
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
}
?>