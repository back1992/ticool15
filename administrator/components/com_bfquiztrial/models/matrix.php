<?php
/**
 * Matrix Model for bfquiztrial Component
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
 * Matrix Model
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialModelmatrix extends JModel
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
	  * Pagination object
	  * @var object
	  */
	 var $_pagination = null;



	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$query = 'SELECT b.*,  cc.title AS category_name'
						. ' FROM #__bfquiztrial_matrix AS b'
						. ' LEFT JOIN #__categories AS cc ON cc.id = b.catid'
						. ' ORDER BY b.catid, b.ordering'
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
		    $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;

	}


	function __construct()
	  {
	 	parent::__construct();

		global $mainframe, $option;

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	    $filter_ord = $mainframe->getUserStateFromRequest($option.'filter_order', 'filter_order', 'orderdate');
		$filter_ord_Dir = strtoupper($mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', 'DESC' ));
	  }

      function getTotal()
	    {
	   	// Load the content if it doesn't already exist
	   	if (empty($this->_total)) {
	   	    $query = $this->_buildQuery();
	   	    $this->_total = $this->_getListCount($query);
	   	}
	   	return $this->_total;
	  }

      function getPagination()
	    {
	   	// Load the content if it doesn't already exist
	   	if (empty($this->_pagination)) {
	   	    jimport('joomla.html.pagination');
	   	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
	   	}
	   	return $this->_pagination;
	  }

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function storematrix()
	{
		$row =& $this->getTable();
		dump($row,"row");

		$data = JRequest::get( 'post' );

		// Bind the form fields to the Question table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Set order if 0 or blank
		if ($row->ordering == "0" | $row->ordering == "") {
		   // get next ordering
		   $query = ' SELECT MAX(ordering) as ordering FROM #__bfquiztrial_matrix';
		   $this->_db->setQuery( $query );
		   $this->_mydata = $this->_db->loadObject();
		   $row->ordering = intval($this->_mydata->ordering)+1;
		}

		// Make sure the bfquiztrial Matrix Answer record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		return true;
	}

}
