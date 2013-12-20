<?php
/**
 * bfquiztrial default controller
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

jimport('joomla.application.component.controller');

/**
 * bfquiztrial Component Controller
 *
 * @package    Joomla
 * @subpackage Components
 */
class bfquiztrialControllerResults extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		JRequest::setVar( 'view', 'results' );
		JRequest::setVar( 'layout', 'default'  );
	}

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}

   /**
   	 * display the edit form
   	 * @return void
   	 */
   	function edit($id)
   	{
   		JRequest::setVar( 'view', 'response' );
   		JRequest::setVar( 'layout', 'response'  );
		JRequest::setVar( 'response_id', $id  );

   		parent::display();
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_bfquiztrial&controller=resultscategory&task=resultscategory');
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$catid = JRequest::getVar( 'catid');

		$db =& JFactory::getDBO();

		if (count( $cids ))
		{
			foreach($cids as $cid) {
				$query = "DELETE FROM #__bfquiztrial_".$catid." where `id`=".$cid."";

				$db->setQuery( $query );
				if (!$db->query()) {
				    $msg = JText::_( 'Error: One or More Responses Could not be Deleted' );
					JError::raiseError(500, $db->getErrorMsg() );
				}else{
					$msg = JText::_( 'Response(s) Deleted' );
				}
			}
		}

		$this->setRedirect( 'index.php?option=com_bfquiztrial&controller=results&task=results&cid='.$catid.'', $msg );
	}

/**
* displays the results of completed quiz
*/
function results($catid)
{
	global $option, $mainframe;
	$limit = JRequest::getVar('limit',
	$mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);
	$db =& JFactory::getDBO();
	$query = "SELECT count(*) FROM #__bfquiztrial_".$catid."";
	$db->setQuery( $query );
	$total = $db->loadResult();
	$query = "SELECT * FROM #__bfquiztrial_".$catid." ORDER by `id`";

	//$db->setQuery( $query, $limitstart, $limit );
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if ($db->getErrorNum())
	{
		echo $db->stderr();
		return false;
	}
	//jimport('joomla.html.pagination');
	//$pageNav = new JPagination($total, $limitstart, $limit);
	//bfquiztrialsViewbfquiztrials::showResults( $option, $rows, $pageNav );

	return $rows;
}

}
?>
