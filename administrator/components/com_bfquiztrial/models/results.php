<?php
/**
 * bfquiztrial Model for bfquiztrial Component
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
 * bfquiztrial Model
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialModelResults extends JModel
{
    /**
     * Hellos data array
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
        global $mainframe;
	    $db =& JFactory::getDBO();
	    $catid=$_SESSION['catid'];


		$where = array();

		$where		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
		$orderby	= ' ORDER BY cc.id';

        $query = ' SELECT * '
            . ' FROM #__bfquiztrial_'.$catid.' as cc '
			. $where
			. $orderby
        ;

        return $query;
      }

    /**
     * Retrieves the
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
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
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



	function getQuestions()
	{
	    $db =& JFactory::getDBO();
		// get questions
		$query = "SELECT * FROM #__bfquiztrial";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		return $rows;

	}

	function getAnswer()
	{
	    $id = JRequest::getVar( 'response_id' );
	    $db =& JFactory::getDBO();

		// get answers
		$query = "SELECT * FROM #__bfquiztrial_data where `customer_id`=$id";
		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

    	return $rows;

	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$db =& JFactory::getDBO();

		//$row =& $this->getTable();

		if (count( $cids ))
		{
			foreach($cids as $cid) {
				// get questions
				$query = "DELETE FROM #__bfquiztrial_data where `id`=$cid";
				$db->setQuery( $query );
				$result = $db->loadObjectList();
				if ($db->getErrorNum())
				{
					echo $db->stderr();
					return false;
				}

				$query2 = "DELETE FROM #__bfquiztrial_data where `customer_id`=$cid";
				$db->setQuery( $query2 );
				$result2 = $db->loadObjectList();
				if ($db->getErrorNum())
				{
					echo $db->stderr();
					return false;
				}
			}
		}
		return true;
	}
}