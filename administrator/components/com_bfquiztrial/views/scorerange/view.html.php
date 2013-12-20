<?php
/**
 * scorerange View for bfquiztrial Component
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

jimport( 'joomla.application.component.view' );

/**
 * Questions View
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialViewscorerange extends JView
{
	/**
	 * scorerange view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'BF Quiz Score Range Matrix' ), 'bfquiztrial_toolbar_title');
		JToolBarHelper::makeDefault('setdefault');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		//JToolBarHelper::addNewX();
		JToolBarHelper::customX( 'scorerangeanswer', 'new.png', 'new_f2.png', 'New', false, false );
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );

	    // Get data from the model
	    $items =& $this->get('Data');
	    $pagination =& $this->get('Pagination');

	    // push data into the template
	    $this->assignRef('items', $items);
  	    $this->assignRef('pagination', $pagination);

		parent::display($tpl);
	}

}
