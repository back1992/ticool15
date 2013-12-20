<?php
/**
 * Matrixanswer table class
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
 * matrixanswer Table class
 *
 * @package    Joomla
 * @subpackage Components
 */
class TableMatrixAnswer extends JTable
{
	/** Primary Key @var int */
	var $id = null;

    /** @var int */
	var $catid				= null;

	/** @var string */
	var $description = '';
	var $exactMatch  = '';

	/** @var boolean */
	var $published			= 0;
	var $default			= 0;

	/** @var int(11) */
    var $ordering			= 0;

	/** @var varchar(255) */
	var $redirectURL		= null;

	/** @var varchar(10) */
	var $score1				= null;
	var $score2				= null;
	var $score3				= null;
	var $score4				= null;
	var $score5				= null;
	var $condition1			= null;
	var $condition2			= null;
	var $condition3			= null;
	var $condition4			= null;
	var $condition5			= null;
	var $operator1			= null;
	var $operator2			= null;
	var $operator3			= null;
	var $operator4			= null;
	var $operator5			= null;

	/* @var int(10) */
	var $qty1				= 0;
	var $qty2				= 0;
	var $qty3				= 0;
	var $qty4				= 0;
	var $qty5				= 0;


	/** @var text */
	var $resultText			= null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Tablematrixanswer(& $db) {
		parent::__construct('#__bfquiztrial_matrix', 'id', $db);
	}
}
?>
