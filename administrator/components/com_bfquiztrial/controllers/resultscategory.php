<?php
/**
 * Results Category default controller
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
 * Results Category Component Controller
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialControllerResultsCategory extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		JRequest::setVar( 'view', 'resultscategory' );
		JRequest::setVar( 'layout', 'default'  );
	}


	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
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

}
?>
