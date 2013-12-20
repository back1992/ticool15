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
		JRequest::setVar( 'view', 'stats' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	function sayg()
	{
		JRequest::setVar( 'view', 'sayg' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	function myquizzes()
	{
		JRequest::setVar( 'view', 'myquizzes' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
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

	function getQuestions()
	{
	    $db =& JFactory::getDBO();

	    $catid	= JRequest::getVar( 'catid', 0, '', 'int' );

		// get questions
		if($catid == 0){
			$query = 'SELECT b.*,  cc.title AS category_name'
								. ' FROM #__bfquiztrial AS b'
								. ' LEFT JOIN #__categories AS cc ON cc.id = b.catid'
								. ' WHERE b.published'
								. ' ORDER BY b.catid, b.ordering'
			;

		}else{
		    $query = "SELECT * FROM #__bfquiztrial where catid=".$catid." and published ORDER BY ordering";
		}

		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		return $rows;

	}

	function getQuestions2()
	{
	    $db =& JFactory::getDBO();

	    $catid	= JRequest::getVar( 'catid', 0, '', 'int' );

		// get questions

			$query = 'SELECT b.*,  cc.title AS category_name'
								. ' FROM #__bfquiztrial AS b'
								. ' LEFT JOIN #__categories AS cc ON cc.id = b.catid'
								. ' WHERE b.published and cc.title = "Front End"'
								. ' ORDER BY b.catid, b.ordering'
			;


		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		return $rows;

	}

	function getQuestionsResponse($catid)
	{
	    $db =& JFactory::getDBO();

	    // get questions
		$query = "SELECT * FROM #__bfquiztrial where `catid`=".$catid." AND `published`=1 ORDER BY ordering";

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


	function getNextQuestion($question, $response)
	{
		$db =& JFactory::getDBO();

		// get questions
		$catid	= JRequest::getVar( 'catid', 0, '', 'int' );
		if($catid == 0){
		   $query = "SELECT * FROM #__bfquiztrial where id=".$question."";
		}else{
		   $query = "SELECT * FROM #__bfquiztrial where catid=".$catid." and id=".$question."";
		}
		//echo $query;
		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		$next_question=0;

		if($rows[0]->question_type == 0){  // textbox
		   $next_question = $rows[0]->next_question1;
		}

		if($rows[0]->option1 == $response){
		   $next_question = $rows[0]->next_question1;
		}else if($rows[0]->option2 == $response){
		   $next_question = $rows[0]->next_question2;
		}else if($rows[0]->option3 == $response){
		   $next_question = $rows[0]->next_question3;
		}else if($rows[0]->option4 == $response){
		   $next_question = $rows[0]->next_question4;
		}else if($rows[0]->option5 == $response){
		   $next_question = $rows[0]->next_question5;
		}else if($rows[0]->option6 == $response){
		   $next_question = $rows[0]->next_question6;
		}else if($rows[0]->option7 == $response){
		   $next_question = $rows[0]->next_question7;
		}else if($rows[0]->option8 == $response){
		   $next_question = $rows[0]->next_question8;
		}else if($rows[0]->option9 == $response){
		   $next_question = $rows[0]->next_question9;
		}else if($rows[0]->option10 == $response){
		   $next_question = $rows[0]->next_question10;
		}else if($rows[0]->option11 == $response){
		   $next_question = $rows[0]->next_question11;
		}else if($rows[0]->option12 == $response){
		   $next_question = $rows[0]->next_question12;
		}else if($rows[0]->option13 == $response){
		   $next_question = $rows[0]->next_question13;
		}else if($rows[0]->option14 == $response){
		   $next_question = $rows[0]->next_question14;
		}else if($rows[0]->option15 == $response){
		   $next_question = $rows[0]->next_question15;
		}else if($rows[0]->option16 == $response){
		   $next_question = $rows[0]->next_question16;
		}else if($rows[0]->option17 == $response){
		   $next_question = $rows[0]->next_question17;
		}else if($rows[0]->option18 == $response){
		   $next_question = $rows[0]->next_question18;
		}else if($rows[0]->option19 == $response){
		   $next_question = $rows[0]->next_question19;
		}else if($rows[0]->option20 == $response){
		   $next_question = $rows[0]->next_question20;
		}

		return $next_question;

	}

	function getQuestion($question)
	{
		$db =& JFactory::getDBO();

		// get question
		$query = "SELECT question FROM #__bfquiztrial where id=".$question."";

		$db->setQuery( $query);
		$result=$db->loadResult();

	    return $result;
    }

	function save($name,$email,$table)
	{
	    $db =& JFactory::getDBO();

		// todays date
		$now =& JFactory::getDate();
		$DateCompleted = $now->toMySQL();
		$dateReceived=$_SESSION["dateReceived"];
		$ip = bfquiztrialController::getIP();

		// save data
		$query = "INSERT INTO ".$table." ( `id` , `Name`, `Email`, `DateReceived`,`ip`,`DateCompleted`) values ('','$name','$email','$dateReceived','$ip','$DateCompleted')";

		$db->setQuery( $query );
		if (!$db->query())
		{
			echo $db->getErrorMsg();
			return false;
		}

		return $db->insertid();
	}

	function checkIP($table,$ip){
	   $db =& JFactory::getDBO();

	   $query = "SELECT count(ip) from ".$table." where `ip`='".$ip."'";

	   $db->setQuery( $query);
	   $result=$db->loadResult();

	   return $result;

	}

	function checkEmail($table,$myemail){
	   $db =& JFactory::getDBO();

	   if($myemail == ""){
	      // don't check anonymous responses
	      return 0;
	   }else{
	      $query = "SELECT count(Email) from ".$table." where `Email`='".$myemail."'";

	      $db->setQuery( $query);
	      $result=$db->loadResult();

	      return $result;
	   }
	}

	function checkUID($table,$myUID){
	   $db =& JFactory::getDBO();

	   if($myUID == "" | $myUID == 0){
	      // don't check anonymous responses
	      return 0;
	   }else{
	      $query = "SELECT count(uid) from ".$table." where `uid`='".$myUID."'";

	      $db->setQuery( $query);
	      $result=$db->loadResult();

	      return $result;
	   }
	}

	function getField($id,$field_name,$table)
	{
	    $db =& JFactory::getDBO();

		// get answer
		$query = "SELECT `".$field_name."` FROM ".$table." where id=".$id."";

		$db->setQuery( $query);
		$result=$db->loadResult();

	    return $result;
	}

	function saveField($id,$field_name,$answer,$table)
	{
	    $db =& JFactory::getDBO();

		//add delimiter before appostrophe
		//$answer = ereg_replace("'", "\'", $answer);
		//$answer = addcslashes($answer,"'");

		// save data
		$query = 'UPDATE '.$table.' SET `'.$field_name.'`="'.$answer.'" where `id`='.$id;

		$db->setQuery( $query );
		if (!$db->query())
		{
			echo $db->getErrorMsg();
			return false;
		}

		return true;
	}

	function updateOnePage()
	{
		//get parameters
		$Itemid = JRequest::getVar('Itemid');
		$menu =& JMenu::getInstance('site');
		$config = & $menu->getParams( $Itemid );

		$registeredUsers = $config->get( 'registeredUsers' );
		$preventMultipleEmail = $config->get( 'preventMultipleEmail' );
		$preventMultipleUID = $config->get( 'preventMultipleUID' );
		$scoringMethod = $config->get( 'scoringMethod' );
		$thankyouText = $config->get( 'thankyouText' );
		$authorEmail = $config->get( 'authorEmail' );

		global $mainframe;
		$qntable=$mainframe->getCfg('dbprefix')."bfquiztrial";
		$fullname = JRequest::getVar( 'fullname', "", 'post', 'string' );
		$email = JRequest::getVar( 'email', "", 'post', 'string' );
		$catid = JRequest::getVar( 'catid', 0, '', 'int' );
		$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

		$emailcount = bfquiztrialController::checkEmail($table,$email);

		$user = &JFactory::getUser();
		if($registeredUsers == "1"){
		   $uidcount = bfquiztrialController::checkUID($table,$user->id);
		}else{
		   $uidcount = 0;
		}

		if($emailcount < 1 | $preventMultipleEmail != "1"){  // check if email address has already completed quiz

	    // save basic details to db, and generate id
		$id = bfquiztrialController::save($fullname,$email,$table);

		//save uid
		bfquiztrialController::saveField($id,"uid",$user->id,$table);

		$body = "\n\nName: ".$fullname."\nEmail: ".$email."\n";
		$score=0;
		$answerSeq="";

		$items =& bfquiztrialController::getQuestions();

		$total_qns=count( $items );

		for ($i=0; $i < $total_qns; $i++)
		{
		    $check_msg = "";
			$row = $items[$i];
			$fieldName = $row->field_name;

		    if($row->question_type == 1){ // radio
		      if(JRequest::getVar($fieldName) == "_OTHER_"){
		         $temp = $fieldName;
		         $temp .= '_OTHER_';
		         $answer = JRequest::getVar($temp);
		      }else{
		         $answer = JRequest::getVar($fieldName);
		      }
			}else if($row->question_type == "2"){  // Checkbox
		   		$name = JRequest::getVar( ''.$fieldName.'', array(), 'post', 'array' );
				if($name == ""){
				   //do nothing
				}else{
		   			foreach($name as $value) {
		    		   	if($value == "_OTHER_"){
		    		      	$temp = $fieldName;
		        	        $temp .= '_OTHER_';
					      	$value = JRequest::getVar($temp);
       				   	}
			 		   $check_msg .= "$value\n";
			 		}
		   		}
			    $answer = $check_msg;
			}else{
			   $answer = JRequest::getVar($fieldName);
			}

			if($answer == ""){
				    // do nothing
	 	    }else{
	 	       //uncomment line below if you want all responses included in email
			   //$body .= "\n".$row->question.": \n".JText::_("Answer").": ".$answer."\n";
   			}

			if($fieldName == ""){
			   // do nothing
			}else{
  			   bfquiztrialController::saveField($id,$fieldName,$answer,$table);
  			   $score=$score+bfquiztrialController::getScore($fieldName,$qntable,$answer);
  		       if($scoringMethod == 1){
			      $answerSeq.=bfquiztrialController::getAnswerSeq($fieldName,$answer);
      		   }
  			}
		}

		//save score
		bfquiztrialController::saveField($id,"score",$score,$table);

		//save answer sequence
		bfquiztrialController::saveField($id,"answerseq",$answerSeq,$table);

		$body .= "\n".JText::_("Congratulations, your score is: ").": \n".$score."\n";

		echo "<div class=\"bfquiztrialOptions\">";
		echo $thankyouText;
		echo "</div>";
		echo "<br>";
		bfquiztrialController::showResults($score);
		echo "<br>";
		$myIncorrect=bfquiztrialController::showIncorrect($id,$table);
		$body .= "\n".JText::_("Please review your incorrect answers below: ")."\n".$myIncorrect."\n";

		bfquiztrialController::sendEmail($body);

		if($authorEmail == "1" & $email!=""){
		   bfquiztrialController::sendEmailAuthor($body,$email);
		}

		if($scoringMethod == 1){
		   bfquiztrialController::checkABCD($fieldName,$answerSeq,$id,$table);
		}else if($scoringMethod == 2){
		   bfquiztrialController::checkscorerange($fieldName,$score,$id,$table);
		}

		}else{
		   echo JText::_( "Error!<br>" );
		   echo JText::_( "Your email address has already completed this quiz.");
      	}
	}

    function sendEmail($body){
    	$Itemid = JRequest::getVar('Itemid');
		$menu =& JMenu::getInstance('site');
		$config = & $menu->getParams( $Itemid );

    	$allowEmail = $config->get( 'allowEmail' );
    	$sendEmailTo = $config->get( 'sendEmailTo' );
    	$emailSubject = $config->get( 'emailSubject' );
		$emailBody = $config->get( 'emailBody' );

	   if($allowEmail){
	       // Send email
		   bfquiztrialController::sendNotificationEmail($body, $sendEmailTo, $emailSubject, $emailBody);
		}else{
		   // do nothing
		}
    }

	function sendEmailAuthor($body,$author){
    	$Itemid = JRequest::getVar('Itemid');
		$menu =& JMenu::getInstance('site');
		$config = & $menu->getParams( $Itemid );

    	$allowEmail = $config->get( 'allowEmail' );

      if($allowEmail){
          // Send email
         $sendEmailTo = $author;
    	 $emailSubject = $config->get( 'emailSubject' );
		 $emailBody = $config->get( 'emailBody' );
         bfquiztrialController::sendNotificationEmail($body, $sendEmailTo, $emailSubject, $emailBody);
      }else{
         // do nothing
      }
    }

    function sendNotificationEmail($body, $sendEmailTo, $emailSubject, $emailBody)
	{
	    $mailer =& JFactory::getMailer();

	    $mailer->addRecipient($sendEmailTo);
		$mailer->setSubject($emailSubject);
		$mailer->setBody($emailBody.$body.'');

		if ($mailer->Send() !== true)
		{
		    // an error has occurred
		    // a notice will have been raised by $mailer
		}
	}

	function getIP() {

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip=$_SERVER['REMOTE_ADDR'];
		return $ip;
	}

	function getStatsCheckbox($question, $response, $catid){

		$db =& JFactory::getDBO();
		global $mainframe;
		$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

		// get answers
		$config =& JComponentHelper::getParams( 'com_bfquiztrial_stats' );

		//add delimiter before appostrophe
		$response = ereg_replace("'", "\'", $response);  // added 11-09-08 to fix appostrophe issue

		$query = "SELECT * FROM ".$table." where `".$question."` like'%".$response."%'";

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

	function getStats($question, $response, $catid){

			$db =& JFactory::getDBO();
			global $mainframe;
			$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

			// get answers
			$config =& JComponentHelper::getParams( 'com_bfquiztrial_stats' );

			//add delimiter before appostrophe
			$response = ereg_replace("'", "\'", $response);  // added 11-09-08 to fix appostrophe issue

		  	$query = "SELECT * FROM ".$table." where `".$question."`='".$response."'";

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

	/***********************
	 *	Captcha functions!
	 ***********************/
	function displaycaptcha() {
		global $mainframe;

		$catid = JRequest::getVar('catid', 0, '', 'int');
		$use_captcha = JRequest::getVar('use_captcha', 0, '', 'int');

		if ($use_captcha) {
			$Ok = null;
			$mainframe->triggerEvent('onCaptcha_Display', array($Ok));
			if (!$Ok) {
				echo "<br/>Error displaying Captcha<br/>";
			}
		}
	}

	/**
	@return boolean
	*/
	function _checkCaptcha() {
		global $mainframe;

		$catid = JRequest::getVar('catid', 0, '', 'int');
		$use_captcha = JRequest::getVar('use_captcha', 1, '', 'int');


		// not using captcha!
		if (!$use_captcha) {
			return true;
		}
		$return = false;
		$word = JRequest::getVar('word', false, '', 'CMD');

		$mainframe->triggerEvent('onCaptcha_confirm', array($word, &$return));
		if ($return) {
			return true;
		} else return false;
	}


	/**********************
	 * Scoring functions
	 **********************/
	 function showResults($score){
	    $db =& JFactory::getDBO();
		global $mainframe;
		//$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

		// get menu parameters
		$Itemid = JRequest::getVar('Itemid');
		$menu =& JMenu::getInstance('site');
		$config = & $menu->getParams( $Itemid );

		$showResults = $config->get( 'showResults' );

	    if($showResults){
	       ?>
		      <div class="bfquiztrialOptions"><strong><?php echo JText::_("恭喜！您的得分是："); ?><?php echo $score; ?></strong></div>
		      <br>
		  <?php
	    }else{
	       // do nothing - show results menu parameter set to no
	    }
	 }

	 function getScore($field_name,$table,$answer)
	 {
	        $db =& JFactory::getDBO();

	       $query = "SELECT `id` FROM ".$table." where field_name='".$field_name."'";

	       $db->setQuery( $query);
	       $result=$db->loadResult();
	       $id=$result;

	       // get answer
	       $query = "SELECT * FROM ".$table." where id=".$id."";

	       $db->setQuery( $query);
	       $rows = $db->loadObjectList();

	       $myresult=&$rows[0];
	       $score=0;

	       // get menu parameters
		   $Itemid = JRequest::getVar('Itemid');
		   $menu =& JMenu::getInstance('site');
		   $config = & $menu->getParams( $Itemid );

		   $scoringMethod = $config->get( 'scoringMethod' );

		   $view=JRequest::getVar('view');
		   if($view=="stats" & !(is_numeric($myresult->score1))){
		      $scoringMethod =1;
		   }

		   if($scoringMethod == 1){
		      $score=1;
		   }else{
		      if(is_numeric($myresult->score1)){

			  $numanswers=0;
			  //special case for checkbox question type
			  if($myresult->question_type == 2){
			     //how many correct answers?
				 $numanswers=(int)($myresult->answer1)+(int)($myresult->answer2)+(int)($myresult->answer3)+(int)($myresult->answer4)+(int)($myresult->answer5)+(int)($myresult->answer6)+(int)($myresult->answer7)+(int)($myresult->answer8)+(int)($myresult->answer9)+(int)($myresult->answer10)+(int)($myresult->answer11)+(int)($myresult->answer12)+(int)($myresult->answer13)+(int)($myresult->answer14)+(int)($myresult->answer15)+(int)($myresult->answer16)+(int)($myresult->answer17)+(int)($myresult->answer18)+(int)($myresult->answer19)+(int)($myresult->answer20);
			  }

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

	       //special case for multiple answers
	       if($numanswers>1){

			  //get correct answer
			  $correctanswer = "";
			  for ($z=0; $z < 20; $z++){
			     $tempvalue="answer".($z+1);
			     $tempvalue2="option".($z+1);
			     if($myresult->$tempvalue == 1){
			        $correctanswer .= $myresult->$tempvalue2;
			        $correctanswer.=" "; //add space between correct answers
			     }
			  }

			  //remove all whitespace
			  $myanswer=preg_replace('/\s+/','',$answer);
		      $correctanswer=preg_replace('/\s+/','',$correctanswer);

	          $score=0;
	          for($i=0; $i < 20; $i++){
	             $myoption="option".($i+1);
	             $myscore="score".($i+1);

	             if($myresult->$myoption){
	                //does answer contain this option
	                $answer=" ".$answer;
	                if(strpos(strtoupper($answer), strtoupper($myresult->$myoption) )){
	                   //only assign score if all correct answers are selected
	                   if(trim(strtoupper($myanswer))==trim(strtoupper($correctanswer)) | $myresult->suppressQuestion == 1){
			              $score=$score+(int)($myresult->$myscore);
			           }
			  	    }
			  	 }
	          }
	       }

		      }else{
		         if($myresult->suppressQuestion == 1){
		            $score=0;
		         }else{
		            echo "Error, score must be numeric unless you are using ABCD scoring";
		         }
		      }

	       } // end else

	        return $score;
	 }


function showIncorrect($id,$table){
	    $db =& JFactory::getDBO();
		global $mainframe;

		// get menu parameters
		$Itemid = JRequest::getVar('Itemid');
		$menu =& JMenu::getInstance('site');
		$config = & $menu->getParams( $Itemid );

		$showIncorrect = $config->get( 'showIncorrect' );
		$myIncorrect = ""; //for email


	    if($showIncorrect){

	       //get questions
		   $items =& bfquiztrialController::getQuestions();
		   $total_qns=count( $items );

		   echo '<table width="100%">';

		   for ($i=0; $i < $total_qns; $i++)
		   {
		      $row = &$items[$i];
		      $fieldName = $row->field_name;

			  $numanswers=0;
			  //special case for checkbox question type
			  if($row->question_type == 2){
			     //how many correct answers?
				 $numanswers=(int)($row->answer1)+(int)($row->answer2)+(int)($row->answer3)+(int)($row->answer4)+(int)($row->answer5)+(int)($row->answer6)+(int)($row->answer7)+(int)($row->answer8)+(int)($row->answer9)+(int)($row->answer10)+(int)($row->answer11)+(int)($row->answer12)+(int)($row->answer13)+(int)($row->answer14)+(int)($row->answer15)+(int)($row->answer16)+(int)($row->answer17)+(int)($row->answer18)+(int)($row->answer19)+(int)($row->answer20);
			  }

			  //get correct answer
			  $correctanswer = "";
			  for ($z=0; $z < 20; $z++){
			     $tempvalue="answer".($z+1);
			     $tempvalue2="option".($z+1);
			     if($row->$tempvalue == 1){
			        $correctanswer .= $row->$tempvalue2;
			        if($numanswers > 1){
			           $correctanswer.=" "; //add space between correct answers
			        }
			     }
			  }

		      //get answer
			  $answer=bfquiztrialController::getField($id,$fieldName,$table);

		      if($row->question_type == 2){ //checkbox
		         //(remove whitespace)
		         $answer=preg_replace('/\s+/','',$answer);
		         $correctanswer=preg_replace('/\s+/','',$correctanswer);
		      }

			  //was answer correct?
		      if(trim(strtoupper($answer))==trim(strtoupper($correctanswer)) | $row->suppressQuestion == 1){
			     //do nothing as answer was correct or question suppressed
			  }else{
				 //first one
				 if($i==0){
				    ?>
				    <div class="bfquiztrialOptions">
				    <?php echo JText::_( "Please review your incorrect answers below: " ); ?></div>
				    <br>
				    <?php
				 }

	             //show solution
			  	 ?>
				<tr>
	    			<th>
	    			   <div class="bfquiztrialQuestion"><?php echo JText::_( $row->question ); ?></div>
	    			</th>
				</tr>
				<tr>
				    <th>
			      		<div class="bfquiztrialOptions">
			      		<font color="red">
			      		<?php echo JText::_("Your answer of "); ?>
			      		<?php echo $answer; ?>
			      		<?php echo JText::_(" was incorrect."); ?>
			      		<br>
			      		<?php echo JText::_("Correct answer is: "); ?>
			      		<strong><?php echo $correctanswer; ?></strong>
			      		</font>
			      		<br><br>
			      		<?php echo $row->solution; ?>
			      	    </div>
			      	</th>
		      	</tr>
		      	<tr>
		      	   <td>&nbsp;</td>
		      	</tr>
				<?php

				//now prepare above for email
				$myIncorrect.="\n".JText::_( $row->question ).": \n".JText::_('Your answer of ')."".$answer."".JText::_(' was incorrect.')."\n".JText::_('Correct answer is: ')."".$correctanswer."\n\n";
	          }
	       }
	    }else{
	       // do nothing - show incorrect menu parameter set to no
	    }

	    if($showIncorrect){
	       echo "</table>";
	    }

	    return $myIncorrect;
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

		  if(is_numeric($rows[$i]->score1)){
	         for($z=0; $z < 20; $z++){
	            $score = 'score'.$z;
	            $tempScore=$rows[$i]->$score;
	            if($tempScore > $tempMax){
	               $tempMax = $tempScore;
	            }
	         }
	         $maxScore = $maxScore + $tempMax;
	      }else{
	         $maxScore++; //for ABCD scoring.
	      }
	   }

	   return $maxScore;
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
	         $tempScore = bfquiztrialController::getScore($fieldName,$table,$rows2[$i]->$fieldName);
	         $totalTempScore = $totalTempScore + $tempScore;
	      }
	      $totalScore = $totalScore + $totalTempScore;
	   }

	   if($n2 >0){
	      $average = round($totalScore / $n2,2);
	   }else{
	      $average = 0;
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
	         $tempScore = bfquiztrialController::getScore($fieldName,$table,$rows2[$i]->$fieldName);
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
	         $tempScore = bfquiztrialController::getScore($fieldName,$table,$rows2[$i]->$fieldName);
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
	      $tempScore = bfquiztrialController::getScore($fieldName,$table,$rows2[0]->$fieldName);
		  $totalTempScore = $totalTempScore + $tempScore;
	   }

	   return $totalTempScore;

	}

   function checkABCD($field_name,$answerSeq, $resultid, $resulttable){
      //see which answer matrix matches
      $db =& JFactory::getDBO();

      global $mainframe;
	  $qntable=$mainframe->getCfg('dbprefix')."bfquiztrial";
	  $matrixtable=$mainframe->getCfg('dbprefix')."bfquiztrial_matrix";

      //get category id
      $query = "SELECT `catid` FROM ".$qntable." where field_name='".$field_name."'";

      $db->setQuery( $query);
	  $result=$db->loadResult();
	  $catid=$result;

	  //get all ABCD answer matrix for this category
	  $query = "SELECT * FROM ".$matrixtable." where catid=".$catid."";
	  $db->setQuery( $query);
	  $rows = $db->loadObjectList();

	  //now we need to see if any ABCD answer matrix match

	  $n = count($rows);

	  $match=0;
	  for($z=0; $z < $n; $z++){
	     $exactMatch = $rows[$z]->exactMatch;
	     $redirectURL = $rows[$z]->redirectURL;
	     $resultText = $rows[$z]->resultText;

         if($exactMatch!=""){
 	        if($answerSeq==$exactMatch){
	           //we found a match

	           //save matrixid
			   bfquiztrialController::saveField($resultid,"matrixid",$rows[$z]->id,$resulttable);

	           if($redirectURL==""){
	              //no redirect so show resultText
	              echo $resultText;
	           }else{
		   	      //redirect to the url
	              $msg="";
	              global $mainframe;
			      $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
	           }

			   //don't bother looking at other matrix since we found match
			   $match=1;
	           $z = $n;
	        }
	     }else{  //no exactMatch set

            //need to see if other condtions match
		    $score1 = $rows[$z]->score1;
		    $score2 = $rows[$z]->score2;
		    $score3 = $rows[$z]->score3;
		    $score4 = $rows[$z]->score4;
		    $score5 = $rows[$z]->score5;
		    $condition1 = $rows[$z]->condition1;
		    $condition2 = $rows[$z]->condition2;
		    $condition3 = $rows[$z]->condition3;
		    $condition4 = $rows[$z]->condition4;
		    $condition5 = $rows[$z]->condition5;
		    $qty1 = $rows[$z]->qty1;
		    $qty2 = $rows[$z]->qty2;
		    $qty3 = $rows[$z]->qty3;
		    $qty4 = $rows[$z]->qty4;
		    $qty5 = $rows[$z]->qty5;
		    $operator1 = $rows[$z]->operator1;
		    $operator2 = $rows[$z]->operator2;
		    $operator3 = $rows[$z]->operator3;
		    $operator4 = $rows[$z]->operator4;
		    $operator5 = $rows[$z]->operator5;

			$line1=0;
			$line2=0;
			$line3=0;
			$line4=0;
			$line5=0;
			$numlines=0;

			for($i=1; $i < 6; $i++){
			   $score="score".$i;
			   $line="line".$i;
			   $qty="qty".$i;
			   $condition="condition".$i;

			   if($$score != ""){
			      $numlines++;
		          //does it match the criteria?
		          switch($$condition){
		             case 0: if(bfquiztrialController::timesFound($answerSeq,$$score) == $$qty){    //is equal to
		                        $$line=1;
		                     }else{
		                        $$line=0;
		                     }
		                     break;
		             case 1: if(bfquiztrialController::timesFound($answerSeq,$$score) < $$qty){    //is less than
			         		      $$line=1;
			         		   }else{
			         		      $$line=0;
			         		   }
		                     break;
		             case 2: if(bfquiztrialController::timesFound($answerSeq,$$score) > $$qty){    //is greater than
			         		      $$line=1;
			         		   }else{
			         		      $$line=0;
			         		   }
		                     break;
		             case 3: if(bfquiztrialController::timesFound($answerSeq,$$score) != $$qty){    //is not equal to
			         		      $$line=1;
			         		   }else{
			         		      $$line=0;
			         		   }
		                     break;
		             default: $$line=0;
		          } // end switch
		       }else{
		          $i=6; // don't bother checking other lines
		       }//end if
		    }

            //now need to use operator to combine conditions for each line
            switch($numlines){
               case 5: if($line1==1 & $line2==1 & $line3==1 &line4==1 & $line5==1){
                          $match=1;
               		   }
               		   break;
               case 4: if($line1==1 & $line2==1 & $line3==1 &line4==1){
                          $match=1;
               		   }
               		   break;
			   case 3: if($line1==1 & $line2==1 & $line3==1){
                          $match=1;
               		   }
               		   break;
			   case 2: if($line1==1 & $line2==1){
   					      $match=1;
               		   }
               		   break;
			   case 1: if($line1==1){
   					      $match=1;
               		   }
               		   break;
               default: $match=0;
            }

            if($match==1){
			  //we found a match

			  //save matrixid
			  bfquiztrialController::saveField($resultid,"matrixid",$rows[$z]->id,$resulttable);

	          if($redirectURL==""){
			  	//no redirect so show resultText
			  	echo $resultText;
			  }else{
			  	//redirect to the url
			  	$msg="";
			  	global $mainframe;
			    $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
   		      }
   			  //don't bother looking at other matrix since we found match
			  $match=1;
			  $z = $n;
            }

		 }// end else exactMatch
	  }

	  if($match==0){  // no match found, so use default for that category
	     //get default ABCD answer matrix for this category
		 $query = "SELECT * FROM ".$matrixtable." where catid=".$catid." and `default`=1";
		 $db->setQuery( $query);
	     $rows = $db->loadObjectList();

	     $redirectURL = $rows[0]->redirectURL;
	     $resultText = $rows[0]->resultText;

 		 //save matrixid
 		 bfquiztrialController::saveField($resultid,"matrixid",$rows[0]->id,$resulttable);

	     if($redirectURL==""){
		    //no redirect so show resultText
		    echo $resultText;
		 }else{
		    //redirect to the url
		    $msg="";
		    global $mainframe;
		    $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
	     }
	  }

   }

   function timesFound($searchIn, $searchFor){
      //this function returns how many times a character appears in a string.
      $result = 0;

      for($i = 0; $i < strlen($searchIn); $i++)
      {
         if($searchIn[$i] == $searchFor){
            $result++;
         }
      }

      return $result;
   }

   function getAnswerSeq($field_name,$answer)
   {
   		   //get answer sequence, eg ACBAD
	       $db =& JFactory::getDBO();
	       global $mainframe;
		   $qntable=$mainframe->getCfg('dbprefix')."bfquiztrial";

	       $query = "SELECT `id` FROM ".$qntable." where field_name='".$field_name."'";

	       $db->setQuery( $query);
	       $result=$db->loadResult();
	       $id=$result;

	       // get answer
	       $query = "SELECT * FROM ".$qntable." where id=".$id."";

	       $db->setQuery( $query);
	       $rows = $db->loadObjectList();

	       $myresult=&$rows[0];
	       $score="";

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


   function checkscorerange($field_name,$score, $resultid, $resulttable){
      //see which score range matrix matches
      $db =& JFactory::getDBO();

      global $mainframe;
	  $qntable=$mainframe->getCfg('dbprefix')."bfquiztrial";
	  $scorerangetable=$mainframe->getCfg('dbprefix')."bfquiztrial_scorerange";

      //get category id
      $query = "SELECT `catid` FROM ".$qntable." where field_name='".$field_name."'";

      $db->setQuery( $query);
	  $result=$db->loadResult();
	  $catid=$result;

	  //get all score ranges matrix for this category
	  $query = "SELECT * FROM ".$scorerangetable." where catid=".$catid."";
	  $db->setQuery( $query);
	  $rows = $db->loadObjectList();

	  //now we need to see if any score range matrix match

	  $n = count($rows);

	  $match=0;
	  for($z=0; $z < $n; $z++){
	     $scoreStart = $rows[$z]->scoreStart;
	     $scoreEnd = $rows[$z]->scoreEnd;
	     $redirectURL = $rows[$z]->redirectURL;
	     $resultText = $rows[$z]->resultText;

		 if($scoreStart == ""){  //no minimum score
		    //is score less than max
		    if($score <= $scoreEnd){
		       //we found a match

			   //save matrixid
			   bfquiztrialController::saveField($resultid,"matrixid",$rows[$z]->id,$resulttable);
		       $match=1;
		       $z = $n;

			   if($redirectURL==""){
	              //no redirect so show resultText
	              echo $resultText;
	           }else{
		   	      //redirect to the url
	              $msg="";
	              global $mainframe;
			      $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
	           }
		    }
		 }

		 if($scoreEnd == ""){  //no maximum score
		    //is score more than minimum
		    if($score >= $scoreStart){
		       //we found a match

			   //save matrixid
			   bfquiztrialController::saveField($resultid,"matrixid",$rows[$z]->id,$resulttable);
		       $match=1;
		       $z = $n;

			   if($redirectURL==""){
	              //no redirect so show resultText
	              echo $resultText;
	           }else{
		   	      //redirect to the url
	              $msg="";
	              global $mainframe;
			      $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
	           }
		    }
		 }

		 //is score between min & max
		 if($score >= $scoreStart & $score <= $scoreEnd){
		    //we found a match

		   //save matrixid
		   bfquiztrialController::saveField($resultid,"matrixid",$rows[$z]->id,$resulttable);
		   $match=1;
		   $z = $n;

		   if($redirectURL==""){
	           //no redirect so show resultText
	           echo $resultText;
	        }else{
		       //redirect to the url
	           $msg="";
	           global $mainframe;
		       $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
	        }
		 }

	  } // end for

	  if($match==0){  // no match found, so use default for that category
	     //get default score range matrix for this category
		 $query = "SELECT * FROM ".$scorerangetable." where catid=".$catid." and `default`=1";
		 $db->setQuery( $query);
		 $rows = $db->loadObjectList();

		 $redirectURL = $rows[0]->redirectURL;
		 $resultText = $rows[0]->resultText;

		 //save matrixid
		 bfquiztrialController::saveField($resultid,"matrixid",$rows[0]->id,$resulttable);

		 if($redirectURL==""){
		    //no redirect so show resultText
		    echo $resultText;
		 }else{
		    //redirect to the url
		    $msg="";
		    global $mainframe;
		    $mainframe->redirect( JRoute::_($redirectURL, false), $msg );
		 }
	  }
   }

	function myresponse()
	{
		JRequest::setVar( 'view', 'response' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
	}

	function myresults()
	{
		JRequest::setVar( 'view', 'results' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();
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

		return $rows;
	}


	/**
	* displays the results of completed quiz for currently logged in user
	*/
	function getmyquizzes($uid)
	{
		$db =& JFactory::getDBO();

		//get all quiz category id numbers
		$query = "SELECT id, title FROM `#__categories` WHERE `section`='com_bfquiztrial'";

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		$query2 = "";

		$i=0;
		foreach($rows as $row){
		   if($i==0){

		      $query2 .= "SELECT id, uid, DATE_FORMAT(DateReceived,'%D %M %Y') as DateReceived, score, DateCompleted, '".$row->title."' AS title, TIMEDIFF(DateCompleted, DateReceived) as TimeTaken FROM #__bfquiztrial_".$row->id." WHERE uid=".$uid."";
		   }else{
		      $query2 .= " UNION SELECT id, uid, DATE_FORMAT(DateReceived,'%D %M %Y') as DateReceived, score, DateCompleted, '".$row->title."' AS title, TIMEDIFF(DateCompleted, DateReceived) as TimeTaken FROM #__bfquiztrial_".$row->id." WHERE uid=".$uid."";
		   }
		$i++;
		}

		//echo $query2;

		$db->setQuery( $query2);
		$rows2 = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		return $rows2;
	}

	/**
	* displays the response of a completed quiz
	*/
	function response($catid,$cid)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__bfquiztrial_".$catid." where `id`=".$cid."";

		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		return $rows;
	}

	function getOneQuestion($question)
	{
		$db =& JFactory::getDBO();

		// get question
		$query = "SELECT * FROM #__bfquiztrial where id=".$question."";

		$db->setQuery( $query);
		$rows = $db->loadObjectList();

	    return $rows;
    }

	function getNumChildren($pid)
	{
	    global $mainframe;
	    $db =& JFactory::getDBO();

		//find out how many children
		$query = 'SELECT COUNT(id) as count'
			. ' FROM #__bfquiztrial'
			. ' WHERE published AND parent='.$pid
		;

		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		return $rows[0]->count;
	}

}
?>
