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
class bfquiztrialController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}

	function stats()
	{
		JRequest::setVar( 'view', 'statscategory' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	function show()
	{
		JRequest::setVar( 'view', 'stats' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* Used to import Question from BF Quiz
	*/
	function import()
	{
		JRequest::setVar( 'view', 'import' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* Matrix view
	*/
	function matrix()
	{
		JRequest::setVar( 'view', 'matrix' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* MatrixAnswer view
	*/
	function matrixanswer()
	{
		JRequest::setVar( 'view', 'matrixanswer' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* Score Range view
	*/
	function scorerange()
	{
		JRequest::setVar( 'view', 'scorerange' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* scorerangeAnswer view
	*/
	function scorerangeanswer()
	{
		JRequest::setVar( 'view', 'scorerangeanswer' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	* Publishes one or more modules
	*/
	function publishQuestion(  ) {
		bfquiztrialController::changePublishQuestion( 1 );
	}

	/**
	* Unpublishes one or more modules
	*/
	function unPublishQuestion(  ) {
		bfquiztrialController::changePublishQuestion( 0 );
	}

	/**
	* Publishes or Unpublishes one or more modules
	* @param integer 0 if unpublishing, 1 if publishing
	*/
	function changePublishQuestion( $publish )
	{
		global $mainframe;

		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 		=& JFactory::getDBO();
		$user 		=& JFactory::getUser();

		$cid		= JRequest::getVar('cid', array(), '', 'array');
		$option		= JRequest::getCmd('option');
		JArrayHelper::toInteger($cid);

		if (empty( $cid )) {
			JError::raiseWarning( 500, 'No items selected' );
			$mainframe->redirect( 'index.php?option='. $option );
		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__bfquiztrial'
		. ' SET published = '.(int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		$mainframe->redirect( 'index.php?option='. $option );
    }


/**
* Moves the record up one position
*/
function moveUpQuestion(  ) {
	bfquiztrialController::orderQuestion( -1 );
}

/**
* Moves the record down one position
*/
function moveDownQuestion(  ) {
	bfquiztrialController::orderQuestion( 1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function orderQuestion( $inc )
{
	global $mainframe;

	// Check for request forgeries
	//JRequest::checkToken() or jexit( 'Invalid Token' );

    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bfquiztrial'.DS.'tables');
	$row =& JTable::getInstance('question', 'Table');

	$db		=& JFactory::getDBO();
	$cid	= JRequest::getVar('cid', array(0), '', 'array');
	$option = JRequest::getCmd('option');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart = JRequest::getVar( 'limitstart', 0, '', 'int' );
	$catid 		= JRequest::getVar( 'catid', 0, '', 'int' );

	$row =& JTable::getInstance( 'question', 'Table' );
	$row->load( $cid[0] );
	$row->move( $inc, 'catid = '.(int) $row->catid.' AND published != 0' );

	$mainframe->redirect( 'index.php?option='. $option );
}

function saveOrder( )
{
	$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	// Initialize variables
	$db			=& JFactory::getDBO();
	$total		= count( $cid );
	$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =& JTable::getInstance('question', 'Table');
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->catid;

		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError(500, $db->getErrorMsg() );
			}
		}
	}

	// execute updateOrder for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('catid = '.(int) $group);
	}

	$msg 	= 'New ordering saved';
	$mainframe->redirect( 'index.php?option=com_bfquiztrial', $msg );
}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'question' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * display the report form
	 * @return void
	 */
	function report()
	{
		JRequest::setVar( 'view', 'report' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	 * display the category form
	 * @return void
	 */
	function category()
	{
		JRequest::setVar( 'view', 'category' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('question');

		if ($model->store($post)) {
			$msg = JText::_( 'Record Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Record' );
		}

		$msg = $cid[0];

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_bfquiztrial';
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function removematrix()
	{
		$model = $this->getModel('matrixanswer');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More ABCD Answer Matrix Could not be Deleted' );
		} else {
			$msg = JText::_( 'ABCD Answer Matrix(s) Deleted' );
		}

		$this->setRedirect( 'index.php?option=com_bfquiztrial', $msg );
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function removescorerange()
	{
		$model = $this->getModel('scorerangeanswer');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More Score Ranges Could not be Deleted' );
		} else {
			$msg = JText::_( 'Score Range(s) Deleted' );
		}

		$this->setRedirect( 'index.php?option=com_bfquiztrial', $msg );
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('question');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More Questions Could not be Deleted' );
		} else {
			$msg = JText::_( 'Question(s) Deleted' );
		}

		$this->setRedirect( 'index.php?option=com_bfquiztrial', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_bfquiztrial', $msg );
	}

	/**
	  Copies one or more questions
	 */
	function copy()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_bfquiztrial' );

		$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
		$db		=& JFactory::getDBO();

		$table	=& JTable::getInstance('question', 'Table');

		$user	= &JFactory::getUser();
		$n		= count( $cid );

		if ($n > 0)
		{
			foreach ($cid as $id)
			{
				if ($table->load( (int)$id ))
				{
				   $table->id					= "";
					$table->question			= 'Copy of ' . $table->question;
					$table->published 			= 0;

					$now =& JFactory::getDate();
					$table->date			= $now->toMySQL();
					$table->field_name 			="";

					if (!$table->store()) {
						return JError::raiseWarning( $table->getError() );
					}
				}else{
					return JError::raiseWarning( 500, $table->getError() );
			    }
			}
		}else{
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		$this->setMessage( JText::sprintf( 'Items copied', $n ) );
	}


	/**
	 * view to allow user to select css file to edit
	 * @return void
	 */
	function chooseCSS()
	{
		JToolBarHelper::title(   JText::_( 'Choose CSS file' ), 'bfquiztrial_toolbar_title');
		JToolBarHelper::custom( 'edit_css', 'edit', 'edit', 'Edit', true );
		JToolBarHelper::cancel();

	    global $mainframe;

		// Initialize some variables
		$option     = JRequest::getCmd('option');
		$template    = JRequest::getVar('id', '', 'method', 'cmd');
		$client        =& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		// Determine CSS directory
		$dir = JPATH_SITE.DS.'components'.DS.'com_bfquiztrial'.DS.'css';

		// List .css files
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($dir, '\.css$', false, false);

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		require_once  (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_templates'.DS.'admin.templates.html.php');
        TemplatesView::chooseCSSFiles($template, $dir, $files, $option, $client);

	}

	/**
	 * form to allow user to edit css file
	 * @return void
	 */
	function editCSS()
	      {
	      	  JToolBarHelper::title(   JText::_( 'Edit CSS file' ), 'bfquiztrial_toolbar_title');
	      	  JToolBarHelper::save( 'save_css' );
	      	  JToolBarHelper::cancel();

	          global $mainframe;

	          // Initialize some variables
	          $option        = JRequest::getCmd('option');
	          $client        =& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
	          $template    = "com_bfquiztrial";
	          $filename    = JRequest::getVar('filename', '', 'method', 'cmd');

	          jimport('joomla.filesystem.file');

	          if (JFile::getExt($filename) !== 'css') {
	              $msg = JText::_('Wrong file type given, only CSS files can be edited.');
	              $mainframe->redirect('index.php?option='.$option.'&client='.$client->id.'&task=choose_css&id='.$template, $msg, 'error');
	          }

	          $content = JFile::read($client->path.DS.'components'.DS.$template.DS.'css'.DS.$filename);

	          if ($content !== false)
	          {
	              // Set FTP credentials, if given
	              jimport('joomla.client.helper');
	              $ftp =& JClientHelper::setCredentialsFromRequest('ftp');

	              $content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

	              require_once  (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_templates'.DS.'admin.templates.html.php');
	              TemplatesView::editCSSSource($template, $filename, $content, $option, $client, $ftp);
	          }
	          else
	          {
	              $msg = JText::sprintf('Operation Failed Could not open', $client->path.$filename);
	              $mainframe->redirect('index.php?option='.$option.'&client='.$client->id, $msg);
	          }
	      }

			/**
			* save css file changes
			* @return void
			*/
	      function saveCSS()
	      {
	          global $mainframe;

	          // Check for request forgeries
	          JRequest::checkToken() or jexit( 'Invalid Token' );

	          // Initialize some variables
	          $option            = JRequest::getCmd('option');
	          $client            =& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
	          $template    = "com_bfquiztrial";
	          $filename        = JRequest::getVar('filename', '', 'post', 'cmd');
	          $filecontent    = JRequest::getVar('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

	          if (!$template) {
	              $mainframe->redirect('index.php?option='.$option.'&client='.$client->id, JText::_('Operation Failed').': '.JText::_('No template specified.'));
	          }

	          if (!$filecontent) {
	              $mainframe->redirect('index.php?option='.$option.'&client='.$client->id, JText::_('Operation Failed').': '.JText::_('Content empty.'));
	          }

	          // Set FTP credentials, if given
	          jimport('joomla.client.helper');
	          JClientHelper::setCredentialsFromRequest('ftp');
	          $ftp = JClientHelper::getCredentials('ftp');

	          $file = $client->path.DS.'components'.DS.$template.DS.'css'.DS.$filename;

	          // Try to make the css file writeable
	          if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
	              JError::raiseNotice('SOME_ERROR_CODE', JText::_('Could not make the css file writable'));
	          }

	          jimport('joomla.filesystem.file');
	          $return = JFile::write($file, $filecontent);

	          // Try to make the css file unwriteable
	          if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
	              JError::raiseNotice('SOME_ERROR_CODE', JText::_('Could not make the css file unwritable'));
	          }

			  if ($return)
			  {
			      $msg = JText::_( 'File Saved' );
			  }else{
			  	  $msg = JText::_( 'Failed to open file for writing' );
			  }


			  $this->setRedirect( JRoute::_('index.php?option=com_bfquiztrial&task=complete', false), $msg );
      }


      function getCategory()
	  	{
	  		$db = &JFactory::getDBO();

	  			$query = 'SELECT a.id, a.title'
	  			. ' FROM #__categories AS a'
	  			. ' WHERE a.published = 1 and a.section="com_bfquiztrial"'
	  			. ' ORDER BY a.title'
	  			;


	  		$db->setQuery( $query );
	  		$options = $db->loadObjectList( );

	  	    return $options;
	}


		function getQuestions($catid)
		{
		    $db =& JFactory::getDBO();
			global $mainframe;
			$table=$mainframe->getCfg('dbprefix')."bfquiztrial";

		    // get questions
			$query = "SELECT * FROM ".$table." where `catid`=".$catid." AND `published`=1 ORDER BY ordering";


			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			if ($db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			return $rows;

		}

		function getAnswers($catid)
		{
		    $db =& JFactory::getDBO();
			global $mainframe;
			$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

		    // get questions
			$query = "SELECT * FROM ".$table."";


			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			if ($db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			return $rows;

		}

	function getStats($fieldName, $response, $catid){

			$db =& JFactory::getDBO();
			global $mainframe;
			$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

			// get answers
			$config =& JComponentHelper::getParams( 'com_bfquiztrial_stats' );

			//add delimiter before appostrophe
			$response = ereg_replace("'", "\'", $response);  // added 11-09-08 to fix appostrophe issue

		  	$query = "SELECT * FROM ".$table." where `".$fieldName."`='".$response."'";

			//echo $query;
			$db->setQuery( $query);
			$rows = $db->loadObjectList();
			if ($db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			$n = count($rows);
			return $n;
	}

	function getNumberResponses($catid){
	   $db =& JFactory::getDBO();
	   global $mainframe;
	   $table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

	   $query = "SELECT id FROM ".$table."";

	   //echo $query;
	   $db->setQuery($query);
	   $rows = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }
	   $n = count($rows);
	   return $n;
	}

	function getMaxScore($catid){
        $db =& JFactory::getDBO();
		global $mainframe;
	    $table=$mainframe->getCfg('dbprefix')."bfquiztrial";

	    $query = "SELECT * FROM ".$table." where `catid`=".$catid." AND `published`=1 ORDER BY ordering";

	   //echo $query;
	   $db->setQuery($query);
	   $rows = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }
	   $n = count($rows);

	   $maxScore=0;
	   for($i=0; $i < $n; $i++){
	      $tempMax=0;
	      for($z=0; $z < 20; $z++){
	         $score = 'score'.$z;
	         if(isset($rows[$i]->$score)){
	            $tempScore=$rows[$i]->$score;
	            if($tempScore > $tempMax){
	               $tempMax = $tempScore;
	            }
	         }
	      }
	      $maxScore = $maxScore + $tempMax;
	   }

	   return $maxScore;
	}

	 function getScore($id,$field_name,$table,$answer)
	 {
	        $db =& JFactory::getDBO();

	       $query = "SELECT `id` FROM jos_bfquiztrial where field_name='".$field_name."'";

	       $db->setQuery( $query);
	       $rows = $db->loadObjectList();
	       $result=$db->loadResult();
	       $id=$result;

	       // get answer
	       $query = "SELECT * FROM jos_bfquiztrial where id=".$id."";

	       $db->setQuery( $query);
	       $rows = $db->loadObjectList();

	       $myresult=&$rows[0];


	       if(strtoupper($answer)==strtoupper($myresult->option1)){
	          $score=$myresult->score1;
	       }else if($answer==$myresult->option2){
	          $score=$myresult->score2;
	       }else if($answer==$myresult->option3){
	          $score=$myresult->score3;
	       }else if($answer==$myresult->option4){
	          $score=$myresult->score4;
	       }else if($answer==$myresult->option5){
	          $score=$myresult->score5;
	       }else if($answer==$myresult->option6){
	          $score=$myresult->score6;
	       }else if($answer==$myresult->option7){
	          $score=$myresult->score7;
	       }else if($answer==$myresult->option8){
	          $score=$myresult->score8;
	       }else if($answer==$myresult->option9){
	          $score=$myresult->score9;
	       }else if($answer==$myresult->option10){
	          $score=$myresult->score10;
	       }else if($answer==$myresult->option11){
	          $score=$myresult->score11;
	       }else if($answer==$myresult->option12){
	          $score=$myresult->score12;
	       }else if($answer==$myresult->option13){
	          $score=$myresult->score13;
	       }else if($answer==$myresult->option14){
	          $score=$myresult->score14;
	       }else if($answer==$myresult->option15){
	          $score=$myresult->score15;
	       }else if($answer==$myresult->option16){
	          $score=$myresult->score16;
	       }else if($answer==$myresult->option17){
	          $score=$myresult->score17;
	       }else if($answer==$myresult->option18){
	          $score=$myresult->score18;
	       }else if($answer==$myresult->option19){
	          $score=$myresult->score19;
	       }else if($answer==$myresult->option20){
	          $score=$myresult->score20;
	       }

	        return $score;
	 }


	function getAverageScore($catid){
	   $db =& JFactory::getDBO();
	   global $mainframe;
	   $table=$mainframe->getCfg('dbprefix')."bfquiztrial";
	   $table2=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

	   $query = "SELECT * FROM ".$table." where `catid`=".$catid." AND `published`=1 ORDER BY ordering";
	   $query2 = "SELECT * FROM ".$table2."";

	   $db->setQuery($query);
	   $rows = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $db->setQuery($query2);
	   $rows2 = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $n = count($rows);
	   $n2 = count($rows2);

	   $totalScore=0;
	   for($i=0; $i < $n2; $i++){
	      $totalTempScore=0;

	      for($z=0; $z < $n; $z++){
	         $fieldName = $rows[$z]->field_name;

	         $tempScore = bfquiztrialController::getScore($rows[$z]->id,$fieldName,$table,$rows2[$i]->$fieldName);

	         $totalTempScore = $totalTempScore + $tempScore;
	      }
	      $totalScore = $totalScore + $totalTempScore;
	   }

	   if($n2 > 0){
	      $average = round($totalScore / $n2,2);
	   }else{
	      $average=0;
	   }

	   return $average;
	}

	function getHighestScore($catid){
	   $db =& JFactory::getDBO();
	   global $mainframe;
	   $table=$mainframe->getCfg('dbprefix')."bfquiztrial";
	   $table2=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

	   $query = "SELECT * FROM ".$table." where `catid`=".$catid." AND `published`=1 ORDER BY ordering";
	   $query2 = "SELECT * FROM ".$table2."";

	   $db->setQuery($query);
	   $rows = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $db->setQuery($query2);
	   $rows2 = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $n = count($rows);
	   $n2 = count($rows2);

	   $highScore=0;
	   for($i=0; $i < $n2; $i++){
	      $totalTempScore=0;

	      for($z=0; $z < $n; $z++){
	         $fieldName = $rows[$z]->field_name;

	         $tempScore = bfquiztrialController::getScore($rows[$z]->id,$fieldName,$table,$rows2[$i]->$fieldName);

	         $totalTempScore = $totalTempScore + $tempScore;
	      }
	      if($totalTempScore > $highScore){
	         $highScore = $totalTempScore;
	      }
	   }

	   return $highScore;

	}

	function getLowestScore($catid){
	   $db =& JFactory::getDBO();
	   global $mainframe;
	   $table=$mainframe->getCfg('dbprefix')."bfquiztrial";
	   $table2=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

	   $query = "SELECT * FROM ".$table." where `catid`=".$catid." AND `published`=1 ORDER BY ordering";
	   $query2 = "SELECT * FROM ".$table2."";

	   $db->setQuery($query);
	   $rows = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $db->setQuery($query2);
	   $rows2 = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $n = count($rows);
	   $n2 = count($rows2);

	   $lowScore=0;
	   for($i=0; $i < $n2; $i++){
	      $totalTempScore=0;

	      for($z=0; $z < $n; $z++){
	         $fieldName = $rows[$z]->field_name;

	         $tempScore = bfquiztrialController::getScore($rows[$z]->id,$fieldName,$table,$rows2[$i]->$fieldName);

	         $totalTempScore = $totalTempScore + $tempScore;
	      }

	      if($i==0){
		     $lowScore = $totalTempScore;
	      }

	      if($totalTempScore < $lowScore){
	         $lowScore = $totalTempScore;
	      }
	   }

	   return $lowScore;

	}

	function getIndividualScore($id,$catid){
	   $db =& JFactory::getDBO();
	   global $mainframe;
	   $table=$mainframe->getCfg('dbprefix')."bfquiztrial";
	   $table2=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

	   $query = "SELECT * FROM ".$table." where `catid`=".$catid." AND `published`=1 ORDER BY ordering";
	   $query2 = "SELECT * FROM ".$table2." where `id`=".$id."";

	   $db->setQuery($query);
	   $rows = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $db->setQuery($query2);
	   $rows2 = $db->loadObjectList();
	   if ($db->getErrorNum())
	   {
	   	  echo $db->stderr();
	      return false;
	   }

	   $n = count($rows);
       $totalTempScore=0;

	   for($z=0; $z < $n; $z++){
	      $fieldName = $rows[$z]->field_name;

	      $tempScore = bfquiztrialController::getScore($rows[$z]->id,$fieldName,$table,$rows2[0]->$fieldName);

		  $totalTempScore = $totalTempScore + $tempScore;
	   }

	   return $totalTempScore;

	}

/**
* Moves the record up one position
*/
function moveUpMatrix(  ) {
	bfquiztrialController::orderMatrix( -1 );
}

/**
* Moves the record down one position
*/
function moveDownMatrix(  ) {
	bfquiztrialController::orderMatrix( 1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function orderMatrix( $inc )
{
	global $mainframe;

	// Check for request forgeries
	//JRequest::checkToken() or jexit( 'Invalid Token' );

    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bfquiztrial'.DS.'tables');
	$row =& JTable::getInstance('matrix', 'Table');

	$db		=& JFactory::getDBO();
	$cid	= JRequest::getVar('cid', array(0), '', 'array');
	$option = JRequest::getCmd('option');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart = JRequest::getVar( 'limitstart', 0, '', 'int' );
	$catid 		= JRequest::getVar( 'catid', 0, '', 'int' );

	$row =& JTable::getInstance( 'matrix', 'Table' );
	$row->load( $cid[0] );
	$row->move( $inc, 'catid = '.(int) $row->catid.' AND published != 0' );

	$mainframe->redirect( 'index.php?option='. $option. "&controller=matrix&task=matrix" );
}

/**
* Moves the record up one position
*/
function moveUpscorerange(  ) {
	bfquiztrialController::orderscorerange( -1 );
}

/**
* Moves the record down one position
*/
function moveDownscorerange(  ) {
	bfquiztrialController::orderscorerange( 1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function orderscorerange( $inc )
{
	global $mainframe;

	// Check for request forgeries
	//JRequest::checkToken() or jexit( 'Invalid Token' );

    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bfquiztrial'.DS.'tables');
	$row =& JTable::getInstance('scorerange', 'Table');

	$db		=& JFactory::getDBO();
	$cid	= JRequest::getVar('cid', array(0), '', 'array');
	$option = JRequest::getCmd('option');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart = JRequest::getVar( 'limitstart', 0, '', 'int' );
	$catid 		= JRequest::getVar( 'catid', 0, '', 'int' );

	$row =& JTable::getInstance( 'scorerange', 'Table' );
	$row->load( $cid[0] );
	$row->move( $inc, 'catid = '.(int) $row->catid.' AND published != 0' );

	$mainframe->redirect( 'index.php?option='. $option. "&controller=scorerange&task=scorerange" );
}

}
?>
