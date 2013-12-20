<?php
/**
 * Question table class
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

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Question Table class
 *
 * @package    Joomla
 * @subpackage Components
 */
class TableQuestion extends JTable
{
	/** Primary Key @var int */
	var $id = null;

    /** @var int */
	var $catid				= null;

	/** @var string */
	var $question = '';

	/** @var string */
	var $question_type = null;

	/** @var string */
	var $checked_out		= 0;

	/** @var time */
	var $checked_out_time	= 0;

	/** @var boolean */
	var $published			= 0;

	/** @var date */
	var $date				= null;

	/** @var int(11) */
	var $parent		 		= 0;
    var $ordering			= 0;

	/** @var varchar(20) */
	var $prefix				= null;
	var $suffix				= null;

	/** @var varchar(150) */
	var $option1			= null;
	var $option2			= null;
	var $option3			= null;
	var $option4			= null;
	var $option5			= null;
	var $option6			= null;
	var $option7			= null;
	var $option8			= null;
	var $option9			= null;
	var $option10			= null;
	var $option11			= null;
	var $option12			= null;
	var $option13			= null;
	var $option14			= null;
	var $option15			= null;
	var $option16			= null;
	var $option17			= null;
	var $option18			= null;
	var $option19			= null;
	var $option20			= null;

	/* @var tinyint */
	var $answer1			= 0;
	var $answer2			= 0;
	var $answer3			= 0;
	var $answer4			= 0;
	var $answer5			= 0;
	var $answer6			= 0;
	var $answer7			= 0;
	var $answer8			= 0;
	var $answer9			= 0;
	var $answer10			= 0;
	var $answer11			= 0;
	var $answer12			= 0;
	var $answer13			= 0;
	var $answer14			= 0;
	var $answer15			= 0;
	var $answer16			= 0;
	var $answer17			= 0;
	var $answer18			= 0;
	var $answer19			= 0;
	var $answer20			= 0;
	var $suppressQuestion	= 0;

	/* @var int(10) */
	var $score1				= 0;
	var $score2				= 0;
	var $score3				= 0;
	var $score4				= 0;
	var $score5				= 0;
	var $score6				= 0;
	var $score7				= 0;
	var $score8				= 0;
	var $score9				= 0;
	var $score10			= 0;
	var $score11			= 0;
	var $score12			= 0;
	var $score13			= 0;
	var $score14			= 0;
	var $score15			= 0;
	var $score16			= 0;
	var $score17			= 0;
	var $score18			= 0;
	var $score19			= 0;
	var $score20			= 0;

	/** @var int(11) */
	var $next_question1		= null;
	var $next_question2		= null;
	var $next_question3		= null;
	var $next_question4		= null;
	var $next_question5		= null;
	var $next_question6		= null;
	var $next_question7		= null;
	var $next_question8		= null;
	var $next_question9		= null;
	var $next_question10	= null;
	var $next_question11	= null;
	var $next_question12	= null;
	var $next_question13	= null;
	var $next_question14	= null;
	var $next_question15	= null;
	var $next_question16	= null;
	var $next_question17	= null;
	var $next_question18	= null;
	var $next_question19	= null;
	var $next_question20	= null;

	var $field_name 		= null;
	/** @var boolean */
	var $mandatory			= 0;
	var $horizontal			= 0;

	/** @var text */
	var $helpText			= null;
	var $solution			= null;

	/** @var int(5)	*/
	var $fieldSize			= 255;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableQuestion(& $db) {
		parent::__construct('#__bfquiztrial', 'id', $db);

		$now =& JFactory::getDate();
		$this->set( 'date', $now->toMySQL() );
	}
}
?>
