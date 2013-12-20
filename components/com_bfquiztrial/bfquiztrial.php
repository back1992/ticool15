<?php
/**
 * bfquiztrial entry point file for bfquiztrial Component
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

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_bfquiztrial'.DS.'helpers'.DS.'helper.php' );

$config =& JComponentHelper::getParams( 'com_bfquiztrial' );
$useCSS = $config->get( 'useCSS' );
if($useCSS == "0"){
   //Use template CSS
}else{
   $document =& JFactory::getDocument();
   $cssFile = "./components/com_bfquiztrial/css/style.css";
   $document->addStyleSheet($cssFile, 'text/css', null, array());
}

// Create the controller
$classname	= 'bfquiztrialController'.$controller;
$controller = new $classname( );

// Check the task parameter and execute appropriate function
switch( JRequest::getCmd('task')) {
    case "cancel":
        $controller->cancel();
        break;
    case "update":
        $controller->update();
        break;
    case "updateOnePage":
        $controller->updateOnePage();
        break;
    case "stats":
        $controller->stats();
        break;
    case "displaycaptcha":
        $controller->displaycaptcha();
        break;
    case "response":
        $controller->myresponse();
        break;
    case "results":
        $controller->myresults();
        break;
    case "sayg":
        $controller->sayg();
        break;
    case "myquizzes":
        $controller->myquizzes();
        break;
    default:
        $controller->display();
        break;
}

// Redirect if set by the controller
$controller->redirect();

?>
