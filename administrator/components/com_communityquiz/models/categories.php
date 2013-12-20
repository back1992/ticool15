<?php
/**
 * Joomla! 1.5 component quiz
 *
 * @version $Id: categories.php 2010-06-26 22:11:56 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage quiz
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CommunityQuizModelCategories extends JModel {
	private $tree;
	
    function __construct() {
        parent::__construct();
        $this->tree = new QuizCategories($this->_db, T_QUIZ_CATEGORIES);
    }
    
    function &get_categories(){
        return $this->tree->get_category_tree();
    }

    function get_category($id){
        $query = "select id, title, alias, parent_id from " . T_QUIZ_CATEGORIES . " where id=" . $id;
        $this->_db->setQuery($query);
        return $this->_db->loadObject();
    }

    function delete($id){
    	return $this->tree->delete_category($id);
    }

    function save(){
        $id = JRequest::getVar('id',0,'post','INT');
        $title = trim(JRequest::getVar('title',null,'post','STRING'));
        $alias = trim(JRequest::getVar('alias',null,'post','STRING'));
        $parent_id = JRequest::getVar('category', 0, 'post', 'INT');
        
        if(!$alias){
			jimport( 'joomla.filter.output' );
			$alias = JFilterOutput::stringURLSafe($title);
        }
        
        if(empty ($title) || ($id && !$parent_id)){
            return false;
        }else{
            if($id){
            	if(!$this->tree->update_category($id, $title, $alias, $parent_id)){
            		return false;
            	}
            }else{
        		if(!$this->tree->add_category($title, $alias, $parent_id)){
        			return false;
        		}
            }
        }
        return true;
    }

    function movedown($id){
    	$query = 'select id, parent_id, nlevel, norder from '.T_QUIZ_CATEGORIES.' where id='.$id;
    	$this->_db->setQuery($query);
    	$source = $this->_db->loadObject();
    	
    	$query = 'select id, parent_id, nlevel, norder from '.T_QUIZ_CATEGORIES
    		. ' where parent_id='.$source->parent_id.' and norder>'.$source->norder.' order by norder limit 1';
    	$this->_db->setQuery($query);
    	$target = $this->_db->loadObject();
    	
    	if($target){
    		$query = 'update '.T_QUIZ_CATEGORIES.' set norder='.$source->norder.' where id='.$target->id;
    		$this->_db->setQuery($query);
    		if($this->_db->query()){
    			$query = 'update '.T_QUIZ_CATEGORIES.' set norder='.$target->norder.' where id='.$source->id;
    			$this->_db->setQuery($query);
    			if(!$this->_db->query()){
    				$this->setError($this->_db->getErrorMsg());
    			}
    			return $this->tree->rebuild();
    		}else{
    			$this->setError($this->_db->getErrorMsg());
    			return false;
    		}
    	}else{
    		$this->setError($this->_db->getErrorMsg());
    		return false;
    	}
    }
    
    function moveup($id){
    	$query = 'select id, parent_id, nlevel, norder from '.T_QUIZ_CATEGORIES.' where id='.$id;
    	$this->_db->setQuery($query);
    	$source = $this->_db->loadObject();
    	
    	$query = 'select id, parent_id, nlevel, norder from '.T_QUIZ_CATEGORIES
    		. ' where parent_id='.$source->parent_id.' and norder<'.$source->norder.' order by norder desc limit 1';
    	$this->_db->setQuery($query);
    	$target = $this->_db->loadObject();
    	
    	if($target){
    		$query = 'update '.T_QUIZ_CATEGORIES.' set norder='.$source->norder.' where id='.$target->id;
    		$this->_db->setQuery($query);
    		if($this->_db->query()){
    			$query = 'update '.T_QUIZ_CATEGORIES.' set norder='.$target->norder.' where id='.$source->id;
    			$this->_db->setQuery($query);
    			if(!$this->_db->query()){
    				$this->setError($this->_db->getErrorMsg());
    			}
    			return $this->tree->rebuild();
    		}else{
    			$this->setError($this->_db->getErrorMsg());
    			return false;
    		}
    	}else{
    		$this->setError($this->_db->getErrorMsg());
    		return false;
    	}
    }
    
    function sort($id, $new_parent){
        $query = "select nleft, nright from " . T_QUIZ_CATEGORIES . " where id=" . $new_parent;
        $this->_db->setQuery($query);
        $parent = $this->_db->loadObject();
        
        if($parent->nleft && $parent->nright){
	        $query = 'update ' . T_QUIZ_CATEGORIES .
	                ' set parent_id=' . $new_parent . ' where id='.$id .
	                ' and not(' . $parent->nleft . ' between nleft and nright)'.
	                ' and not(' . $parent->nright . ' between nleft and nright)';
        }else{
	        $query = 'update ' . T_QUIZ_CATEGORIES .
	                ' set parent_id=' . $new_parent . ' where id='.$id;
        }
        $this->_db->setQuery($query);
        if($this->_db->query()){
            return $this->tree->rebuild();
        }else{
            return false;
        }
    }
    
    function rebuild_categories(){
    	$query = 'update '.T_QUIZ_CATEGORIES.' a '
    		. ' set a.quizzes=(select count(*) from '.T_QUIZ_QUIZZES.' b where b.catid=a.id and b.published=1 group by b.catid)';
    	$this->_db->setQuery($query);
    	$this->_db->query();
    	
    	return $this->tree->rebuild();
    }
}
?>