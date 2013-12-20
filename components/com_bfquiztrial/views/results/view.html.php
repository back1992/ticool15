<?php
/**
 * bfquiztrialViewResults View for bfquiztrial Component
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
 * bfquiztrialViewResults View
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialViewResults extends JView
{
    /**
     * Hellos view display method
     * @return void
     **/
    function display($tpl = null)
    {
        $Itemid = JRequest::getVar('Itemid');
        if($Itemid == 0){
           jexit( 'Access denied!' );
        }
        $menu =& JMenu::getInstance('site');
		$config = & $menu->getParams( $Itemid );

		//get $catid from menu parameter to prevent forgery
		$menuitem =& $menu->getItem( $Itemid );
		$catid = $menuitem->query["catid"];

        $items =& bfquiztrialController::results($catid);

	    if(!$items){
	      global $mainframe;
	      JError::raiseWarning( 500, '此目录下尚未有答案解析' );
		  $mainframe->redirect( 'index.php' );
	    }

        $this->assignRef( 'items', $items );
        $this->assignRef( 'catid', $catid );

        parent::display($tpl);
    }
}