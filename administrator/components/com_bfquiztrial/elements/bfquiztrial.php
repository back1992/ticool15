<?php
/**
 * Elements for BF Quiz - to populate the select category drop down.
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
defined('_JEXEC') or die( 'Restricted access' );

class JElementbfquiztrial extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'bfquiztrial';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();

		$query = 'SELECT a.id, a.title'
		. ' FROM #__categories AS a'
		. ' WHERE a.published = 1 and a.section="com_bfquiztrial"'
		. ' ORDER BY a.title'
		;

		$db->setQuery( $query );
		$options = $db->loadObjectList( );

		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Category').' -', 'id', 'title'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'title', $value, $control_name.$name );
	}
}