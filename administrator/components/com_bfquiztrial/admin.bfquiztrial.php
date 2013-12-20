<?php
/**
 * bfquiztrial entry point file for BF Quiz component
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

require_once( JPATH_COMPONENT.DS.'controller.php' );

// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bfquiztrial'.DS.'tables');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

if(JRequest::getCmd('task') == 'report') {
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller');
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category',true);
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats');
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix');
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange');
}else if(JRequest::getCmd('task') == 'category') {
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller');
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category',true);
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats');
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix');
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange');
}else if(JRequest::getCmd('task') == 'show' | JRequest::getCmd('task') == 'stats') {
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller');
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category');
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats',true);
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix');
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange');
}else if($controller == 'results' | $controller == 'response' | $controller == 'resultscategory' ) {
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller');
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category');
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats');
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory',true);
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix');
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange');
}else if($controller == 'matrix' | JRequest::getCmd('task') == 'matrix' | JRequest::getCmd('task') == 'matrixanswer' | JRequest::getCmd('task') == 'savematrix') {
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller');
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category');
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats');
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix',true);
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange');
}else if($controller == 'scorerange' | JRequest::getCmd('task') == 'scorerange' | JRequest::getCmd('task') == 'scorerangeanswer' | JRequest::getCmd('task') == 'savescorerange') {
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller');
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category');
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats');
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix');
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange',true);
}else{
   JSubMenuHelper::addEntry(JText::_('Questions'), 'index.php?option=com_bfquiztrial&controller=controller',true);
   JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_categories&section=com_bfquiztrial');
   JSubMenuHelper::addEntry(JText::_('Report'), 'index.php?option=com_bfquiztrial&task=category');
   JSubMenuHelper::addEntry(JText::_('Stats'), 'index.php?option=com_bfquiztrial&task=stats');
   JSubMenuHelper::addEntry(JText::_('Results'), 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
   JSubMenuHelper::addEntry(JText::_('ABCD Answer Matrix'), 'index.php?option=com_bfquiztrial&controller=matrix&task=matrix');
   JSubMenuHelper::addEntry(JText::_('Score Range Matrix'), 'index.php?option=com_bfquiztrial&controller=scorerange&task=scorerange');
}

$document =& JFactory::getDocument();
$cssFile = './components/com_bfquiztrial/css/bfquiztrial.css';
$document->addStyleSheet($cssFile, 'text/css', null, array());

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

// Create the controller
$classname	= 'bfquiztrialController'.$controller;
$controller = new $classname( );

// Check the task parameter and execute appropriate function
switch( JRequest::getCmd('task')) {
    case "cancel":
        $controller->cancel();
        break;
    case "edit":
	    $controller->edit();
    	break;
    case "add":
    	$controller->edit();
    	break;
    case "save":
    	$controller->save();
    	break;
    case 'remove':
    	$controller->remove();
    	break;
    case 'publish':
		$controller->publishQuestion( );
		break;
	case 'unpublish':
		$controller->unPublishQuestion( );
		break;
    case 'orderup':
		$controller->moveUpQuestion( );
		break;
	case 'orderdown':
		$controller->moveDownQuestion( );
		break;
	case 'saveorder':
		$controller->saveOrder( );
		break;
    case 'orderupmatrix':
		$controller->moveUpMatrix( );
		break;
	case 'orderdownmatrix':
		$controller->moveDownMatrix( );
		break;
    case 'orderupscorerange':
		$controller->moveUpscorerange( );
		break;
	case 'orderdownscorerange':
		$controller->moveDownscorerange( );
		break;
	case 'saveorder':
		$controller->saveOrder( );
		break;
	case 'choose_css':
	    $controller->chooseCSS( );
	    break;
	case 'edit_css':
		$controller->editCSS();
		break;
    case 'save_css':
    	$controller->saveCSS();
    	break;
    case 'report':
	  	$controller->report();
    	break;
    case 'category':
		$controller->category();
    	break;
    case 'copy':
		$controller->copy();
    	break;
    case "show":
		$controller->show();
    	break;
    case "stats":
		$controller->stats();
    	break;
    case "matrix":
        $controller->matrix();
        break;
    case "matrixanswer":
        $controller->matrixanswer();
        break;
    case "savematrix":
    	$controller->savematrix();
    	break;
	case "scorerange":
        $controller->scorerange();
        break;
    case "scorerangeanswer":
        $controller->scorerangeanswer();
        break;
    case "savescorerange":
    	$controller->savescorerange();
    	break;
    case "setdefault":
        $controller->setdefault();
        break;
    default:
        $controller->display();
        break;
}

// Redirect if set by the controller
$controller->redirect();

?>
