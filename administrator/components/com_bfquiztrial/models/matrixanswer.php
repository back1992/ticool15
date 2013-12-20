<?php
/**
 * Matrix Answer Model for bfquiztrial Component
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

jimport('joomla.application.component.model');

/**
 * Matrix Answer Model
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialModelMatrixAnswer extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the Matrix Answer identifier
	 *
	 * @access	public
	 * @param	int Matrix Answer identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}


	/**
	 * Method to get a Matrix Answer
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__bfquiztrial_matrix '.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->question = null;
		}
		return $this->_data;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store()	{
		$row =& $this->getTable();

		$data = JRequest::get( 'post' );
		//allow html code in resultText field.
		$data['resultText']= JRequest::getVar( 'resultText', '', 'post', 'string', JREQUEST_ALLOWHTML );

		// Bind the form fields to the Question table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Set default for this category
		if ($row->default == "") {
		   // see if default exists
		   $query = ' SELECT * FROM #__bfquiztrial_matrix WHERE `catid`='.$row->catid.' AND `default`=1';
		   $this->_db->setQuery( $query );
		   $this->_mydata = $this->_db->loadObject();
		   	if(!count($this->_mydata)){
		      $row->default=1;
			}
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

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids ))		{
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}


}
?>
