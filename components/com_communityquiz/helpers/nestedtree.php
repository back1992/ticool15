<?php
/**
 * @version		$Id: nestedtree.php 01 2011-01-11 11:37:09Z maverick $
 * @package		CoreJoomla16.Quiz
 * @subpackage	Components
 * @copyright	Copyright (C) 2009 - 2010 corejoomla.com. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

class QuizCategories{
	
	private $db;
	private $table;
	
	function QuizCategories(&$db, $table){
		$this->db = $db;
		$this->table = $table;
	}
	
	function &get_category($id){
		$query = 'select id, title, alias, parent_id, norder from '.$this->table.' where id='.$id;
		$this->db->setQuery($query);
		return $this->db->loadObject();
	}
	
	function &get_category_tree(){
		$query = 'select a.id, a.title, a.alias, a.parent_id, a.quizzes, (count(b.id) - 1) as nlevel, a.norder'
			. ' from '.$this->table.' as a, '.$this->table.' as b'
			. ' where a.nleft between b.nleft and b.nright'
			. ' group BY a.id'
			. ' order BY a.nleft';
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		return $rows;
	}
	
	function get_category_flat_list($parent){
		if(!$parent){
			$query = 'select id from '.$this->table.' where parent_id=0';
			$this->db->setQuery($query);
			$parent = $this->db->loadResult();
		}
		$query = 'select a.id, a.title, a.alias, a.quizzes, (COUNT(b.id) - (d.nlevel + 1)) AS nlevel, a.parent_id'
			. ' from '.$this->table.' AS a, '.$this->table.' AS b, '.$this->table.' AS c,'
			. '( select e.id, (COUNT(f.id) - 1) AS nlevel'
				. ' from '.$this->table.' AS e, '.$this->table.' AS f'
				. ' where e.nleft between f.nleft and f.nright and e.id = '.$parent
				. ' group by e.id'
				. ' order by e.nleft'
			. ' ) AS d'
			. ' where a.nleft between b.nleft and b.nright and a.nleft between c.nleft and c.nright and c.id = d.id'
			. ' group by a.id'
			. ' having nlevel <= 1'
			. ' order by a.nleft';
		$this->db->setQuery($query);
		$categories = $this->db->loadObjectList();
		return $categories;
	}
	
	function get_breadcrumbs($id){
		$query = 'select b.id, b.title, b.alias, b.parent_id'
			. ' from '.$this->table.' a, '.$this->table.' b'
			. ' where a.nleft between b.nleft and b.nright and a.id='.$id
			. ' order by b.nleft';
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
	
	function add_category($title, $alias, $parent){
		$left = 1;
		if(!empty($parent)){
			$query = 'select nleft from '.$this->table.' where id='.$parent;
			$this->db->setQuery($query);
			$left = $this->db->loadResult();
		}
		
		$queries = array();
		//$queries[] = 'lock table '.$this->table.' write';
		$queries[] = 'update '.$this->table.' set nright=nright + 2 where nright > '.$left;
		$queries[] = 'update '.$this->table.' set nleft=nleft + 2 where nleft > '.$left;
		$queries[] = 'insert into '.$this->table.'(title, alias, nleft, nright, parent_id) values ('
			. $this->db->quote($title) . ','
			. $this->db->quote($alias) . ','
			. ( $left + 1 ) . ','
			. ( $left + 2 ) . ','
			. $parent . ')';
		//$queries[] = 'unlock tables';
		
		foreach ($queries as $query){
			$this->db->setQuery($query);
			if(!$this->db->query()){
//				$query = 'unlock tables';
//				$this->db->setQuery($query);
//				$this->db->query();
				return false;
			}
		}
		return $this->rebuild();
	}
	
	function update_category($id, $title, $alias, $parent){
		if($id == (int)$parent){
			return false;
		}
		
		$query = 'update '.$this->table.' set'
			. ' title='.$this->db->quote($title).','
			. ' alias='.$this->db->quote($alias).','
			. ' parent_id='.$parent
			. ' where id='.$id;
		$this->db->setQuery($query);
		if($this->db->query()){
			return $this->rebuild();
		}
		return false;
	}
	
	function delete_category($id){
		$query = 'select nleft, nright, nright-nleft+1 as nwidth from '.$this->table.' where id='.$id;
		$this->db->setQuery($query);
		$category = $this->db->loadObject();
		
		$queries = array();
		//$queries[] = 'lock table '.$this->table.' write';
		$queries[] = 'delete from '.$this->table.' where nleft between '.$category->nleft.' and '.$category->nright;
		$queries[] = 'update '.$this->table.' set nright = nright - '.$category->nwidth.' where nright > '.$category->nright;
		$queries[] = 'update '.$this->table.' set nleft = nleft - '.$category->nwidth.' where nleft > '.$category->nright;
		//$queries[] = 'unlock tables';
		
		foreach ($queries as $query){
			$this->db->setQuery($query);
			if(!$this->db->query()){
				$query = 'unlock tables';
				$this->db->setQuery($query);
				$this->db->query();
				return false;
			}
		}
		return true;
	}
	
	function rebuild(){
		$query = 'select id from '.$this->table.' where parent_id=0 order by nleft asc';
		$this->db->setQuery($query);
		$parent = $this->db->loadResult();
		$this->rebuild_tree($parent, 1);
		
		$query = 'select id, parent_id, norder from '.$this->table.' order by parent_id, norder  asc';
		$this->db->setQuery($query);
		$categories = $this->db->loadObjectList();
		
		if($categories){
			$parent = -1;
			$norder = 0;
			foreach ($categories as $category){
				if($category->parent_id != $parent){
					$parent = $category->parent_id;
					$norder = 1;
				}
				$query = 'update '.$this->table.' set norder='.$norder.' where id='.$category->id;
				$this->db->setQuery($query);
				if(!$this->db->query()){
					return false;
					$this->setError($this->_db->getErrorMsg());
				}
				$norder++;
			}
		}else{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	private function rebuild_tree($parent, $left){
		$right = $left+1; 
		$query = 'select id from '.$this->table.' where parent_id='.$parent.' order by norder asc';
		$this->db->setQuery($query);
		$nodes = $this->db->loadResultArray();
		if(count($nodes)){
			foreach ($nodes as $node) {
				$right = $this->rebuild_tree($node, $right);
			}
		}
		
		$query = 'update '.$this->table.' set nleft='.$left.', nright='.$right.' where id='.$parent;
		$this->db->setQuery($query);
		$this->db->query();
		return $right + 1;
	}
}
?>