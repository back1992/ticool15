<?php
/**
 * scorerangeAnswer View for bfquiztrial Component
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
 * Question View
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialViewscorerangeAnswer extends JView
{
	/**
	 * display method of view
	 * @return void
	 **/
	function display($tpl = null)
	{
		$bfquiztrial		=& $this->get('Data');
		$isNew		= ($bfquiztrial->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'BF Quiz- Score Range Matrix' ).': <small><small>[ ' . $text.' ]</small></small>', 'bfquiztrial_toolbar_title' );
		JToolBarHelper::save('savescorerange','Save');
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('bfquiztrial',		$bfquiztrial);

		global $option;

		if(!isset($this->bfquiztrial->catid)){
		   $this->bfquiztrial->catid = "";
		}
		$lists['catid'] = JHTML::_('list.category', 'catid', $option, intval( $this->bfquiztrial->catid ) );
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}
