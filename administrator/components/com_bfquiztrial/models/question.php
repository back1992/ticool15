<?php
/**
 * Question Model for bfquiztrial Component
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
 * Question Model
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialModelQuestion extends JModel
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
	 * Method to set the Question identifier
	 *
	 * @access	public
	 * @param	int Question identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}


	/**
	 * Method to get a Question
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__bfquiztrial '.
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
		//allow html code in helpText field.
		$data['helpText']= JRequest::getVar( 'helpText', '', 'post', 'string', JREQUEST_ALLOWHTML );

		$data['solution']= JRequest::getVar( 'solution', '', 'post', 'string', JREQUEST_ALLOWHTML );

		//maybe allow html for options in future version
		//$data['option1']= JRequest::getVar( 'option1', '', 'post', 'string', JREQUEST_ALLOWHTML );
		//$data['option2']= JRequest::getVar( 'option2', '', 'post', 'string', JREQUEST_ALLOWHTML );

		$data['option1']=ereg_replace('"', '\'', $data['option1']); // remove speachmark
		$data['option2']=ereg_replace('"', '\'', $data['option2']); // remove speachmark
		$data['option3']=ereg_replace('"', '\'', $data['option3']); // remove speachmark
		$data['option4']=ereg_replace('"', '\'', $data['option4']); // remove speachmark
		$data['option5']=ereg_replace('"', '\'', $data['option5']); // remove speachmark
		$data['option6']=ereg_replace('"', '\'', $data['option6']); // remove speachmark
		$data['option7']=ereg_replace('"', '\'', $data['option7']); // remove speachmark
		$data['option8']=ereg_replace('"', '\'', $data['option8']); // remove speachmark
		$data['option9']=ereg_replace('"', '\'', $data['option9']); // remove speachmark
		$data['option10']=ereg_replace('"', '\'', $data['option10']); // remove speachmark
		$data['option11']=ereg_replace('"', '\'', $data['option11']); // remove speachmark
		$data['option12']=ereg_replace('"', '\'', $data['option12']); // remove speachmark
		$data['option13']=ereg_replace('"', '\'', $data['option13']); // remove speachmark
		$data['option14']=ereg_replace('"', '\'', $data['option14']); // remove speachmark
		$data['option15']=ereg_replace('"', '\'', $data['option15']); // remove speachmark
		$data['option16']=ereg_replace('"', '\'', $data['option16']); // remove speachmark
		$data['option17']=ereg_replace('"', '\'', $data['option17']); // remove speachmark
		$data['option18']=ereg_replace('"', '\'', $data['option18']); // remove speachmark
		$data['option19']=ereg_replace('"', '\'', $data['option19']); // remove speachmark
		$data['option20']=ereg_replace('"', '\'', $data['option20']); // remove speachmark


		// Bind the form fields to the Question table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$oldParent = $row->parent;
		if ($row->id > 0)
		{
			// existing item
			$query		= 'SELECT parent FROM #__bfquiztrial WHERE id = '.(int) $row->id;
			$this->_db->setQuery( $query );
			$oldParent	= $this->_db->loadResult();
			if ($oldParent != $row->parent) {
				//do we have any children
				$query		= 'SELECT id FROM #__bfquiztrial WHERE parent = '.$row->id;
				$this->_db->setQuery( $query );
				$numChildren	= $this->_db->loadResult();
				if(count($numChildren) > 0){
				   JError::raiseWarning( 500, 'This question has children so you can add it as a child on another question' );
				   $row->parent = $oldParent;
				}else{
				   //now reset ordering
			       $row->ordering = 0;
				}
			}

			//see if category changed
			$query		= 'SELECT catid FROM #__bfquiztrial WHERE id = '.(int) $row->id;
			$this->_db->setQuery( $query );
			$oldCatid	= $this->_db->loadResult();
			if($oldCatid != $row->catid){
			   //change category for all children
			   $query	= 'UPDATE #__bfquiztrial SET catid = '.(int) $row->catid
						 .' WHERE parent='.$row->id.';';
	 		   $this->_db->setQuery( $query );
			   $this->_db->query();
			}
		}


		// Set order if 0 or blank
		if ($row->ordering == "0" | $row->ordering == "") {
		   // get next ordering
		   $query = ' SELECT MAX(ordering) as ordering FROM #__bfquiztrial where parent = '.(int) $row->parent.' AND catid='.(int) $row->catid;
		   $this->_db->setQuery( $query );
		   $this->_mydata = $this->_db->loadObject();
		   $row->ordering = intval($this->_mydata->ordering)+1;
		}

		//make sure child is in same category as parent
		if($row->parent != 0){
		   //get category of parent
		   $query = 'SELECT catid FROM #__bfquiztrial where id = '.(int) $row->parent;
		   $this->_db->setQuery( $query );
		   $row->catid = $this->_db->loadObject()->catid;
		}

		// Make sure the bfquiztrial record is valid
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
