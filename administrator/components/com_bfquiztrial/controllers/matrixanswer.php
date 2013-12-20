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
class bfquiztrialControllerMatrixAnswer extends JController
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
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix', $msg );
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
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function savematrix()
	{
		$model = $this->getModel('matrixanswer');

		if ($model->store($post)) {
			$msg = JText::_( 'Record Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Record' );
		}

		$msg = $cid[0];

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix';
		$this->setRedirect($link, $msg);
	}

}
?>
