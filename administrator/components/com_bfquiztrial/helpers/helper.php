<?php
/**
 * Helper for BF Quiz - to populate the question types
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

/**
 * @package		Joomla
 * @subpackage	Components
 */
class bfquiztrialHelper
{

	/**
	* build the select list for question type
	*/
	function QuestionType( &$question_type )
	{

		$click[] = JHTML::_('select.option',  '0', JText::_( 'Text' ) );
		$click[] = JHTML::_('select.option',  '1', JText::_( 'Radio' ) );
		$click[] = JHTML::_('select.option',  '2', JText::_( 'Checkbox' ) );

		if($question_type == null){
		   $question_type=1;
		}

		$target = JHTML::_('select.genericlist',   $click, 'question_type', 'class="inputbox" size="3" onchange="hideType()"', 'value', 'text',  intval( $question_type ) );

		return $target;
	}

	/**
	* Show question type
	*/
	function ShowQuestionType( &$question_type ) {
	   switch($question_type){
	      case 0:	echo "Text";
	      			break;
	      case 1:   echo "Radio";
	      			break;
	      case 2:   echo "Checkbox";
	      			break;
	      default:  echo "Unknown";
	   }
	}

	/**
	* build the select list for condition
	*/
	function ConditionType( &$condition, $i )
	{

		$click[] = JHTML::_('select.option',  '0', JText::_( 'is equal to' ) );
		$click[] = JHTML::_('select.option',  '1', JText::_( 'is less than' ) );
		$click[] = JHTML::_('select.option',  '2', JText::_( 'is greater than' ) );
		$click[] = JHTML::_('select.option',  '3', JText::_( 'is NOT equal to' ) );

		if($condition == null){
		   $condition=0;
		}

		$target = JHTML::_('select.genericlist',   $click, 'condition'.$i.'', 'class="inputbox" size="1" onchange="hideType()"', 'value', 'text',  intval( $condition ) );

		return $target;
	}

	/**
	* build the select list for condition
	*/
	function OperatorType( &$operator_type, $i )
	{

		$click[] = JHTML::_('select.option',  '0', JText::_( 'AND' ) );
		//$click[] = JHTML::_('select.option',  '1', JText::_( 'OR' ) );

		if($operator_type == null){
		   $operator_type=0;
		}

		$target = JHTML::_('select.genericlist',   $click, 'operator'.$i.'', 'class="inputbox" size="1" onchange="hideType()"', 'value', 'text',  intval( $operator_type ) );

		return $target;
	}

    /**
	* build the select list for question type
	*/
	function alignType( &$imageAlign )
	{

		$click[] = JHTML::_('select.option',  'left', JText::_( 'left' ) );
		$click[] = JHTML::_('select.option',  'center', JText::_( 'center' ) );
		$click[] = JHTML::_('select.option',  'right', JText::_( 'right' ) );

		if($imageAlign == null){
		   $imageAlign="left";
		}

		$target = JHTML::_('select.genericlist',   $click, 'imageAlign', 'class="inputbox" size="2"', 'value', 'text',  $imageAlign );

		return $target;
	}

    /**
	 * Build the select list for parent question
	 */
	function Parent( &$row )
	{
		$db =& JFactory::getDBO();

		// If a not a new item, lets set the question item id
		if ( $row->id ) {
			$id = ' AND id != '.(int) $row->id;
		} else {
			$id = null;
		}

		// In case the parent was null
		if (!$row->parent) {
			$row->parent = 0;
		}

		// get a list of the question items
		// excluding the current question item and all child elements
		$query = 'SELECT m.*' .
				' FROM #__bfquiztrial m' .
				' WHERE m.parent=0 and m.id<>'.$row->id.'' .
				' ORDER BY parent, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// assemble menu items to the array
		$mitems 	= array();
		$mitems[] 	= JHTML::_('select.option',  '0', JText::_( 'Top' ) );

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, '&nbsp;&nbsp;&nbsp;'. $item->question . ' ' . $item->id );
		}

		$output = JHTML::_('select.genericlist',   $mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', $row->parent );

		return $output;
	}

}