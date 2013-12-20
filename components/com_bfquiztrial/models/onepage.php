<?php
/**
 * Onepage Model for bfquiztrial Component
 *
 * @package    Joomla
 * @subpackage Components
 * @link http://www.tamlyncreative.com.au/software
 * @copyright	Copyright (c) 2009 - Tamlyn Creative Pty Ltd.
 * @license		GNU GPL
 *
 *	  BF Quiz is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    BF Quiz is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with BF Quiz.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Author's notes: When GNU speaks of free software, it is referring to freedom, not price.
 * We encourage you to purchase your copy of BF Quiz from the developer (Tamlyn Creative Pty Ltd),
 * so that we can continue to make this product better and continue to provide a high quality of support.
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * Onepage Model
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialModelonepage extends JModel
{
	/**
	 * Questions data array
	 *
	 * @var array
	 */
	var $_data;

	 /**
	  * Items total
	  * @var integer
	  */
	 var $_total = null;



	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
	    global $mainframe;
	    $db =& JFactory::getDBO();
	    $catid = JRequest::getVar('catid',0);

		$query = 'SELECT b.*,  cc.title AS category_name'
						. ' FROM #__bfquiztrial AS b'
						. ' LEFT JOIN #__categories AS cc ON cc.id = b.catid'
						. ' WHERE b.published AND b.catid = '.$catid
						. ' ORDER BY b.parent, b.ordering'
		;

		return $query;
	}

	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// if data hasn't already been obtained, load it
		if (empty($this->_data)) {
		    $query = $this->_buildQuery();
		    $this->_data = $this->_getList( $query );
		}

		// establish the hierarchy of the menu

		$children = array();
		// first pass - collect children
		foreach ($this->_data as $v )
		{
			//get the parent id
			$pt = $v->parent;
			// @ symbol tests to see if $children[parentid] is blank
			// ? ternary operator if first part is true, then $children[$pt] otherwise array()
			$list = @$children[$pt] ? $children[$pt] : array();
			//add current row element to the bottom of list array
			array_push( $list, $v );
			$children[$pt] = $list;
		}

		//second pass - reorder elements
		$mylist = array();
		foreach ($this->_data as $v )
		{
		   if($v->parent==0){
		      array_push($mylist, $v);

		      //now are there any children
		      if(isset($children[$v->id])){
		         foreach ($children[$v->id] as $c ){
		            array_push($mylist, $c);
		         }
		      }
		   }
		}

		return $mylist;
	}


	function __construct()
	  {
	 	parent::__construct();

		global $mainframe, $option;

	  }
}
