<?php
/**
 * bfquiztrial matrix controller
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

jimport('joomla.application.component.controller');

/**
 * bfquiztrial matrix Controller
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialControllerMatrix extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}

	/**
	* Matrix view
	*/
	function matrix()
	{
		JRequest::setVar( 'view', 'matrix' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* MatrixAnswer view
	*/
	function matrixanswer()
	{
		JRequest::setVar( 'view', 'matrixanswer' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'matrixanswer' );
		JRequest::setVar( 'layout', 'default'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	* Publishes one or more modules
	*/
	function publishQuestion(  ) {
		bfquiztrialControllerMatrix::changePublishQuestion( 1 );
	}

	/**
	* Unpublishes one or more modules
	*/
	function unPublishQuestion(  ) {
		bfquiztrialControllerMatrix::changePublishQuestion( 0 );
	}

	/**
	* Publishes or Unpublishes one or more modules
	* @param integer 0 if unpublishing, 1 if publishing
	*/
	function changePublishQuestion( $publish )
	{
		global $mainframe;

		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 		=& JFactory::getDBO();
		$user 		=& JFactory::getUser();

		$cid		= JRequest::getVar('cid', array(), '', 'array');
		$option		= JRequest::getCmd('option');
		JArrayHelper::toInteger($cid);

		if (empty( $cid )) {
			JError::raiseWarning( 500, 'No items selected' );
			$mainframe->redirect( 'index.php?option='. $option .'&controller=matrix&task=matrix' );
		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__bfquiztrial_matrix'
		. ' SET published = '.(int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		$mainframe->redirect( 'index.php?option='. $option .'&controller=matrix&task=matrix' );
    }


/**
* Moves the record up one position
*/
function moveUpMatrix(  ) {
	bfquiztrialControllerMatrix::orderMatrix( -1 );
}

/**
* Moves the record down one position
*/
function moveDownMatrix(  ) {
	bfquiztrialControllerMatrix::orderMatrix( 1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function orderMatrix( $inc )
{
	global $mainframe;

	// Check for request forgeries
	//JRequest::checkToken() or jexit( 'Invalid Token' );

    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bfquiztrial'.DS.'tables');
	$row =& JTable::getInstance('matrixanswer', 'Table');

	$db		=& JFactory::getDBO();
	$cid	= JRequest::getVar('cid', array(0), '', 'array');
	$option = JRequest::getCmd('option');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart = JRequest::getVar( 'limitstart', 0, '', 'int' );
	$catid 		= JRequest::getVar( 'catid', 0, '', 'int' );

	$row =& JTable::getInstance( 'matrixanswer', 'Table' );
	$row->load( $cid[0] );
	$row->move( $inc, 'catid = '.(int) $row->catid.' AND published != 0' );

	$mainframe->redirect( 'index.php?option='. $option. "&controller=matrix&task=matrix" );
}


	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('matrixanswer');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More ABCD Answer Matrix Could not be Deleted' );
		} else {
			$msg = JText::_( 'ABCD Answer Matrix(s) Deleted' );
		}

		$this->setRedirect( 'index.php?option=com_bfquiztrial&task=matrix', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_bfquiztrial&task=matrix', $msg );
	}

	/**
	  Copies one or more questions
	 */
	function copy()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_bfquiztrial&task=matrix' );

		$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
		$db		=& JFactory::getDBO();

		$table	=& JTable::getInstance('matrixanswer', 'Table');

		$user	= &JFactory::getUser();
		$n		= count( $cid );

		if ($n > 0)
		{
			foreach ($cid as $id)
			{
				if ($table->load( (int)$id ))
				{
				   $table->id					= "";
					$table->description			= 'Copy of ' . $table->description;
					$table->published 			= 0;
					$table->default				= 0;

					if (!$table->store()) {
						return JError::raiseWarning( $table->getError() );
					}
				}else{
					return JError::raiseWarning( 500, $table->getError() );
			    }
			}
		}else{
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		$this->setMessage( JText::sprintf( 'Items copied', $n ) );
	}



      function getCategory()
	  	{
	  		$db = &JFactory::getDBO();

	  			$query = 'SELECT a.id, a.title'
	  			. ' FROM #__categories AS a'
	  			. ' WHERE a.published = 1 and a.section="com_bfquiztrial"'
	  			. ' ORDER BY a.title'
	  			;


	  		$db->setQuery( $query );
	  		$options = $db->loadObjectList( );

	  	    return $options;
	}


	/**
	* Sets default ABCD  answer matrix
	*/
	function setdefault()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 		=& JFactory::getDBO();
		$user 		=& JFactory::getUser();
		$cid		= JRequest::getVar('cid', array(), '', 'array');
		$option		= JRequest::getCmd('option');
		JArrayHelper::toInteger($cid);

		if (empty( $cid )) {
			JError::raiseWarning( 500, 'No items selected' );
			$mainframe->redirect( 'index.php?option='. $option .'&controller=matrix&task=matrix' );
		}

		$cids = implode( ',', $cid );

		//get category id
		$query = 'SELECT `catid` FROM #__bfquiztrial_matrix'
		. ' WHERE `id` = ( '. $cid[0] .' )'
		;
		$db->setQuery( $query );
		$catid=$db->loadResult();
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		//remove all defaults for that category
		$query = 'UPDATE #__bfquiztrial_matrix'
		. ' SET `default` = 0 where `catid`=( '. $catid .' ) '
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		//set new default
		$query = 'UPDATE #__bfquiztrial_matrix'
		. ' SET `default` = 1 WHERE `id` = ( '. $cid[0] .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		$mainframe->redirect( 'index.php?option='. $option .'&controller=matrix&task=matrix' );
    }

	/**
	 * Save the new order given by user
	 */
	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		$row		=& JTable::getInstance('matrixanswer', 'Table');
		$total		= count( $cid );
		$conditions	= array();

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
				// remember to reorder this category
				$condition = 'catid = '.(int) $row->catid;
				$found = false;
				foreach ($conditions as $cond) {
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}
				if (!$found) {
					$conditions[] = array ( $row->bid, $condition );
				}
			}
		}

		// execute reorder for each category
		foreach ($conditions as $cond)
		{
			$row->load( $cond[0] );
			$row->reorder( $cond[1] );
		}

		// Clear the component's cache
		$cache =& JFactory::getCache('com_bfquiztrial');
		$cache->clean();

		$this->setMessage( JText::_('New ordering saved') );
	}

}
?>
