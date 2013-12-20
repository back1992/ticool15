<?php
/**
 * scorerangeanswer table class
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
 * scorerangeanswer Table class
 *
 * @package    Joomla
 * @subpackage Components
 */
class TablescorerangeAnswer extends JTable
{
	/** Primary Key @var int */
	var $id = null;

    /** @var int */
	var $catid				= null;

	/** @var string */
	var $description = '';

	/** @var boolean */
	var $published			= 0;
	var $default			= 0;

	/** @var int(11) */
    var $ordering			= 0;

	/** @var varchar(255) */
	var $redirectURL		= null;

	/** @var varchar(10) */
	var $scoreStart			= null;
	var $scoreEnd			= null;

	/** @var text */
	var $resultText			= null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Tablescorerangeanswer(& $db) {
		parent::__construct('#__bfquiztrial_scorerange', 'id', $db);
	}
}
?>
