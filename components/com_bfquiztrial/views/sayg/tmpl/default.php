<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

$Itemid = JRequest::getVar('Itemid');
$menu =& JMenu::getInstance('site');
$config = & $menu->getParams( $Itemid );

/*********************** TIMED QUIZ ******************************/
$timedQuiz = $config->get( 'timedQuiz' );
$timeLimit = $config->get( 'timeLimit' );
$timerText = $config->get( 'timerText' );
$timerCompleteText = $config->get( 'timerCompleteText' );

// $minutes and $seconds are added together to get total time.
$minutes = $timeLimit; // Enter minutes
$seconds = 0; // Enter seconds
$time_limit = ($minutes * 60) + $seconds + 1; // Convert total time into seconds
if(!isset($_SESSION["start_time".$Itemid.""])){$_SESSION["start_time".$Itemid.""] = mktime(date('G'),date('i'),date('s'),date('m'),date('d'),date('Y')) + $time_limit;} // Add $time_limit (total time) to start time. And store into session variable.
/*****************************************************************/

if(!isset($_SESSION["dateReceived"])){
   $now =& JFactory::getDate();
   $_SESSION["dateReceived"] = $now->toMySQL();
}

$introText = $config->get( 'introText' );
$quizTitle = $config->get( 'quizTitle' );
$thankyouText = $config->get( 'thankyouText' );
$emailText = $config->get( 'emailText' );
$nameText = $config->get( 'nameText' );
$allowEmail = $config->get( 'allowEmail' );
$authorEmail = $config->get( 'authorEmail' );
$sendEmailTo = $config->get( 'sendEmailTo' );

$emailSubject = $config->get( 'emailSubject' );
$emailBody = $config->get( 'emailBody' );
$submitText = $config->get( 'submitText' );

$anonymous = $config->get( 'anonymous' );
$anonymousText = $config->get( 'anonymousText' );
$anonymousYes = $config->get( 'anonymousYes' );
$anonymousNo = $config->get( 'anonymousNo' );
$nameText = $config->get( 'nameText' );
$emailText = $config->get( 'emailText' );

$showName = $config->get( 'showName' );
$showEmail = $config->get( 'showEmail' );

$errorText = $config->get( 'errorText' );
$use_captcha = $config->get( 'use_captcha' );

$registeredUsers = $config->get( 'registeredUsers' );
$preventMultiple = $config->get( 'preventMultiple' );
$preventMultipleEmail = $config->get( 'preventMultipleEmail' );
$preventMultipleUID = $config->get( 'preventMultipleUID' );

$scoringMethod = $config->get( 'scoringMethod' );
$randomOptions = $config->get( 'randomOptions' );

$user="";
$emailcount=0;
$uidcount=0;

if($anonymous == ""){
   $anonymous = "1";
}

if($showName == ""){
   $showName = "1";
}

if($showEmail == ""){
   $showEmail = "1";
}

$catid = JRequest::getVar( 'catid', 0, '', 'int' );

if($catid == 0){
   echo "Error: You must select a category in menu parameters!";
}

$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

$ip = bfquiztrialController::getIP();
$ipcount = bfquiztrialController::checkIP($table,$ip);

//increment page no
$page_no = JRequest::getVar( 'page_no' );
if(!empty($page_no)){
   $start_qn = JRequest::getVar( 'start_qn', 0, 'post', 'int' );
   $end_qn = JRequest::getVar( 'end_qn', 0, 'post', 'int' );

   // get answers to last question(s)
   $numberQns = $end_qn - $start_qn;
   if($numberQns < 1){
      $numberQns = 1;
   }

   $answerSeq = $_SESSION['answerSeq'];
   $body = $_SESSION['body'];
   $score = $_SESSION['score'];

   global $mainframe;
   $qntable=$mainframe->getCfg('dbprefix')."bfquiztrial";

   if($page_no - $numberQns == 0){  // second page
      $_SESSION['fullname'] = $_POST['fullname'];
      $_SESSION['email'] = $_POST['email'];
      $_SESSION['sendEmailTo'] = $sendEmailTo;
      $_SESSION['uid'] = $_POST['uid'];
      $score=0;

      if($_SESSION['fullname'] == ""){
         $_SESSION['fullname'] = "Anonymous";
      }

      $preventMultipleEmail = JRequest::getVar( 'preventMultipleEmail', 0, '' );
      $emailcount = bfquiztrialController::checkEmail($table,$_SESSION['email']);

		/**
		Captcha
   		*/
   		if ($use_captcha) {
    		$correct=bfquiztrialController::_checkCaptcha();
      		if (!$correct) {
      			JError::raiseWarning("666","You have entered the wrong CAPTCHA sequence. Please try again.");
      			$page_no=1;
      			$start_qn = -1;
      			$end_qn = 0;
      		}else{
      		   // captcha is correct
   			}
   		}

   		// save basic details to db, and generate id
   		$result = bfquiztrialController::save($_SESSION['fullname'],$_SESSION['email'],$table);
   		//save userid
   		bfquiztrialController::saveField($result,"uid",$_SESSION['uid'],$table);
   		$_SESSION['result']=$result;

   		$body = "\n\nName: ".$_SESSION['fullname']."\nEmail: ".$_SESSION['email']."\n";
		$answerSeq="";
   }

   for($y = ($page_no-$numberQns)+1;$y < $page_no+1; $y++){
	   // get answers to last question
	   $qid = $_POST['question'.$y];
	   $_SESSION['question'.$y] = $qid;
	   $_SESSION['questiontype'.$y] = $_POST['question_type'.$y];
	   if(isset($_POST['q'.$qid])){
	      $answer = $_POST['q'.$qid];
	   }else{
	      $answer="";
	   }
	   if(isset($_POST['q'.$qid.'_OTHER_'])){
	      $_SESSION['q'.$qid.'_OTHER_'] = $_POST['q'.$qid.'_OTHER_'];
	   }else{
	      $_SESSION['q'.$qid.'_OTHER_']="";
	   }
	   $fieldname = $_POST['field_name'.$y];
	   $_SESSION['page_no'] = $y;


	   //now save the last page answers to database
	   $question = bfquiztrialController::getQuestion($qid);

	   if($_SESSION['questiontype'.$y] == 1){ // radio
	      if($answer == "_OTHER_"){
	         $answer = $_SESSION['q'.$qid.'_OTHER_'];
	      }
	   }

   	   if($_SESSION['questiontype'.$y] == 2){ // checkbox
   	   if($answer==""){
          // do nothing
       }else{
           foreach($answer as $value) {
              if($value == "_OTHER_"){
                 $value = $_SESSION['q'.$qid.'_OTHER_'];
              }
                 $check_msg .= "$value\n";
              }
           $answer = $check_msg;
           }
       }

	   if($answer == ""){
	      // do nothing
	   }else{
	      //uncomment this line if you want email to show all results, even if correct.
	      //$body .= "\n".$question.": \n".JText::_("Answer").": ".$answer."\n";
	   }

	   if($fieldname == "" | $fieldname == "NULL"){
	      // do nothing
	   }else{ // save data to db
	      bfquiztrialController::saveField($_SESSION['result'],$fieldname,$answer,$table);
	      $score=$score+bfquiztrialController::getScore($fieldname,$qntable,$answer);
	      if($scoringMethod == 1){
	         $answerSeq.=bfquiztrialController::getAnswerSeq($fieldname,$answer);
	      }
	   }

	}

   $_SESSION['answerSeq'] = $answerSeq;
   $_SESSION['body'] = $body;
   $_SESSION['score'] = $score;

   $page_no = $page_no + 1;

}else{
   // defaults for first page
   //session_start();
   $page_no = 1;
   $start_qn = -1;
   $end_qn = 0;
   $score=0;
}

$total_qns=count( $this->items );

if($total_qns > 5){
	$total_qns = 5;
}

//Use conditional branching to determine next question
if($page_no > 1){
   if($qid == ""){
      $next_question = 0;
   }else{
      $next_question = bfquiztrialController::getNextQuestion($qid, $_SESSION['q'.$qid]);
   }
}else{
   $next_question=0;
}

if($next_question == 0){
   // default to next question
   if($start_qn == -1){
      //first page
      $start_qn = $start_qn +1;
   }else{
      $start_qn = $end_qn;
   }
   $end_qn = $end_qn + 1;
}else if($next_question == -1){ // end of quiz
   $start_qn = $total_qns;

   // null answers to all other questions
   for($i=$page_no; $i < $total_qns+1; $i++){
      $_SESSION['question'.$i] = "";
      $_SESSION['questiontype'.$i] = "";
   }
}else{
   $page=0;
   for($i=0; $i < $total_qns; $i++){

      //blank out questions that were skipped
      $mypage=$i;
      if($mypage > $page_no-1){
   	     $_SESSION['question'.$mypage] = "";
   	     $_SESSION['questiontype'.$mypage] = "";
      }

      $row = &$this->items[$i];
      if($row->id == $next_question){
         $i = $total_qns+1;
      }

      $page++;
   }
   $start_qn = $page-1;
   $end_qn = $page;
   $page_no = $page;
}

?>

<?php
 $user = &JFactory::getUser();

 if($registeredUsers == "1"){
    $emailcount = bfquiztrialController::checkEmail($table,$user->email);
    $uidcount = bfquiztrialController::checkUID($table,$user->id);
 }

 if (($user->id) | $registeredUsers != "1") { // test if registerd users only

    if($ipcount < 1 | $preventMultiple != "1"){  // check if IP address has already completed quiz

        if($emailcount < 1 | $preventMultipleEmail != "1"){  // check if email address has already completed quiz

        	if($uidcount < 1 | $preventMultipleUID != "1"){  // check if UID has already completed quiz
?>

<?php if($start_qn > $total_qns-1)
{
// no more questions left

global $mainframe;
$qntable=$mainframe->getCfg('dbprefix')."bfquiztrial";

$answerSeq = $_SESSION['answerSeq'];
$body = $_SESSION['body'];
$score = $_SESSION['score'];
$result = $_SESSION['result'];

//save score
bfquiztrialController::saveField($result,"score",$score,$table);

//save answer sequence
bfquiztrialController::saveField($result,"answerseq",$answerSeq,$table);

$body .= "\n".JText::_("Congratulations, your score is: ").": \n".$score."\n";

echo "<div class=\"bfquiztrialOptions\">";
echo $thankyouText;
echo "</div>";
echo "<br>";
bfquiztrialController::showResults($score);
echo "<br>";
$myIncorrect=bfquiztrialController::showIncorrect($result,$table);
$body .= "\n".JText::_("Please review your incorrect answers below: ")."\n".$myIncorrect."\n";

bfquiztrialController::sendEmail($body);

if($authorEmail == "1" & $_SESSION['email']!=""){
   bfquiztrialController::sendEmailAuthor($body,$_SESSION['email']);
}

if($scoringMethod == 1){
   bfquiztrialController::checkABCD($_SESSION['field_name1'],$answerSeq,$result,$table);
}else if($scoringMethod == 2){
   bfquiztrialController::checkscorerange($_SESSION['field_name1'],$score,$result,$table);
}

?>

<?php
}else{
?>

<script language="Javascript">
<!--
   // get variable from php
   var showName = "<?php echo $showName ?>";
   var showEmail = "<?php echo $showEmail ?>";
   var timedQuiz = "<?php echo $timedQuiz ?>";
   var timerCompleteText = "<?php echo $timerCompleteText ?>";
   var total_qns = "<?php echo $total_qns ?>";

   var toggle = 1;

   function ToggleDetails(){
   		if(toggle == 1){
   		   // hide details
   		   if(showName=="1"){
		      document.getElementById("MyName").style.display = 'none';
		      document.getElementById("fullname").className = 'none';
		   }

           if(showEmail=="1"){
              document.getElementById("email").className = 'none';
              document.getElementById("MyEmail").style.display = 'none';
           }
           toggle=0;

   		}else{
           // show details
           if(showName=="1"){
		      document.getElementById("MyName").style.display = '';
		      document.getElementById("fullname").className = 'required';
		   }

		   if(showEmail=="1"){
              document.getElementById("email").className = 'required validate-email';
              document.getElementById("MyEmail").style.display = '';
           }
		   toggle=1;
        }
   }

   if(timedQuiz=="1"){
      var ct = setInterval("calculate_time()",100); // Start clock.
   }

   function calculate_time()
   {
      var end_time = "<?php echo $_SESSION["start_time".$Itemid.""]; ?>"; // Get end time from session variable (total time in seconds).
      var dt = new Date(); // Create date object.
      var time_stamp = dt.getTime()/1000; // Get current minutes (converted to seconds).
      var total_time = end_time - Math.round(time_stamp); // Subtract current seconds from total seconds to get seconds remaining.
      var mins = Math.floor(total_time / 60); // Extract minutes from seconds remaining.
      var secs = total_time - (mins * 60); // Extract remainder seconds if any.
      if(secs < 10){secs = "0" + secs;} // Check if seconds are less than 10 and add a 0 in front.
      document.getElementById("txt").value = mins + ":" + secs; // Display remaining minutes and seconds.
      // Check for end of time, stop clock and display message.
      if(mins <= 0)
      {
        if(secs <= 0 || mins < 0)
        {
           clearInterval(ct);
           document.getElementById("txt").value = "0:00";
           document.getElementById("start_qn").value = total_qns;
           document.mysurvey.submit();
           alert(timerCompleteText);
        }
      }
   }
//-->
</script>



<?php
// Check that the class exists before trying to use it
if (class_exists('BFBehavior')) {
 BFBehavior::formbfvalidation();
}else{
   JHTML::_('behavior.formvalidation');
}
?>

<script language="javascript">
function myValidate(f) {
	if (document.formvalidator.isValid(f)) {
	    f.check='';
		f.check.value='<?php echo JUtility::getToken(); ?>';//send token
		return true;
	}
	else {
		alert('<?php echo $errorText ?>');
	}
	return false;
}
function imposeMaxLength(Object, MaxLen)
{
  return (Object.value.length < MaxLen);
}
</script>

<form action="index.php?option=com_bfquiztrial&view=sayg&Itemid=<?php echo $Itemid; ?>" method="POST" name="mysurvey" id="mysurvey" class="form-validate" class="form-validate" onSubmit="return myValidate(this);">

<input type="hidden" name="catid" value="<?php echo $catid ?>" />
<input type="hidden" name="score" value="<?php echo $score ?>" />

<table width="100%">
	<thead>
		<tr>
			<th>
				<div class="bfquiztrialTitle"><?php echo JText::_( $quizTitle ); ?></div>
			</th>
		</tr>
	</thead>

<?php if($timedQuiz == "1"){ ?>
<tr>
   <td>
      <div class="timeremaining"><?php echo JText::_($timerText); ?> <input id="txt" readonly></div>
   </td>
</tr>
<?php } ?>

<?php if($page_no == 1){
   // only show this bit on first page
?>

<tr>
    <td>
    <div class="bfquiztrialIntro">
    <?php echo JText::_( $introText ); ?>
    </div>
    <div class="bfquiztrialQuestionFooter">
	</div>
    </td>
</tr>

<?php if($showName == "1"){ ?>
<tr>
    <th>
       <?php if($anonymous == "1"){ ?>
       <table>
       <tr>
           <td>
               <div class="bfquiztrialCustomerQuestion">
               <?php echo JText::_( $anonymousText ); ?>
               </div>
           </td>
           <td>
               <div class="bfquiztrialCustomerOptions">
               <label for="anon1"><input type="radio" name="anonymous" id="anon1" value="No" checked onchange='ToggleDetails()' ><?php echo JText::_( $anonymousNo ); ?></label>
               <label for="anon2"><input type="radio" name="anonymous" id="anon2" value="Yes" onchange='ToggleDetails()'><?php echo JText::_( $anonymousYes ); ?></label>
               </div>
           </td>
       </tr>
       </table>
       <?php
       }else{
          // do nothing, anonymous responses not allowed!
       }?>
    </th>
</tr>
<?php } ?>

<?php if($showName == "1"){ ?>
<tr>
    <th>
        <DIV ID="MyName">
        <table>
        <tr>
            <td width="70">
                <div class="bfquiztrialCustomerQuestion">
                <?php echo JText::_( $nameText ); ?>
                </div>
            </td>
            <td>
                <div class="bfquiztrialCustomerOptions">
                <input name="fullname" id="fullname" size='55' <?php echo 'class="required"'; ?> value='<?php echo $user->name; ?>' >
   				<input type="hidden" name="uid" id="uid" value='<?php echo $user->id; ?>' >
   				</div>
            </td>
        </tr>
        </table>
        </DIV>
    </th>
</tr>
<?php }else{
?>
   <input type="hidden" name="fullname" id="fullname" value='<?php echo $user->name; ?>' >
   <input type="hidden" name="uid" id="uid" value='<?php echo $user->id; ?>' >
<?php
      }
?>

<?php if($showEmail == "1"){ ?>
<tr>
    <th>
        <DIV ID="MyEmail">
        <table>
		       <tr>
		           <td width="70">
		               <div class="bfquiztrialCustomerQuestion">
		               <?php echo JText::_( $emailText ); ?>
		               </div>
		           </td>
		           <td>
		               <div class="bfquiztrialCustomerOptions">
		               <input name="email" id="email" size='55' <?php echo 'class="required validate-email"'; ?> value='<?php echo $user->email; ?>' >
		               </div>
		           </td>
		       </tr>
       </table>
       </DIV>
    </th>
</tr>
<?php }else{
?>
   <input type="hidden" name="email" id="email" value='<?php  echo $user->email; ?>' >
<?php
      }
?>

<tr>
<td>
<?php if ($use_captcha) {
	// Check that the class exists before trying to use it
	if (class_exists('plgSystemBigocaptcha')) {
	?>
	<!-- Bigo Captcha -->
	<img src="index.php?option=com_bfquiztrial&task=displaycaptcha&catid=<?php echo $catid; ?>&use_captcha=<?php echo $use_captcha; ?>">
	<br />
	<input type="text" name="word"/><br>
	<?php echo JText::_( "(Input Word from the image)"); ?>
	<?php
	}else{
	   echo JText::_( "Error: You must install and publish Bigo Captcha plugin to use CAPTCHA!");
	}
	?>
<?php } ?>
</td>
</tr>

<?php
   }  // end only show on first page
?>

<?php
$k = 0;

for ($i=$start_qn; $i < $end_qn; $i++)
{
	$row = &$this->items[$i];

	$numChildren=0;

	//is this a parent item?
	if($row->parent == 0){
	   $numChildren=bfquiztrialController::getNumChildren($row->id);
	   $end_qn = $end_qn + $numChildren;
	   $page_no = $page_no + $numChildren;
	}
?>

    <?php if($row->suppressQuestion != "1"){ ?>
	<tr>
	    <th>
	       <div class="bfquiztrialQuestion"><?php echo JText::_( $row->question ); ?></div>
	    </th>
	</tr>
	<?php } ?>

	<tr>
	    <th>
	      <?php if($row->suppressQuestion != "1"){ ?>
	         <div class="bfquiztrialOptions">
	       <?php }else{ ?>
	         <div>
	       <?php } ?>

	       <?php
	       if($row->helpText == ""){
	          // do nothing
	       }else{
	          echo $row->helpText;
	          echo "<br>";
	       }

	       if(!isset($row->prefix)){
	          $row->prefix = "";
	       }else{
	          $row->prefix.=" "; //add extra space
	       }

		   if(!isset($row->suffix)){
	          $row->suffix = "";
	       }

	       $sequence = $row->id;

	       if($row->question_type == "0"){  //text
	           $mylength="65";
	           if($row->fieldSize < 65){
	              $mylength=$row->fieldSize;
	           }
	           if($row->mandatory){
	              if($row->suppressQuestion != "1"){
	                 echo "".$row->prefix."<INPUT id='q".$sequence."' name='q".$sequence."' size=".$mylength." MAXLENGTH=".$row->fieldSize." class=\"required\"> ".$row->suffix."";
				  }else{
	              ?>
		             <div class="bfquiztrialSuppressQuestion">
	                 <?php echo $row->prefix; ?>
	                 </div>
	                 <div class="bfquiztrialSuppressOptions">
	                 <?php
	                    echo "<INPUT id='q".$sequence."' name='q".$sequence."' size=".$mylength." MAXLENGTH=".$row->fieldSize." class=\"required\"> ".$row->suffix."";
	                 ?>
	                 </div>
       			  <?php
       			  }
	           }else{
	              if($row->suppressQuestion != "1"){
	                 echo "".$row->prefix."<INPUT id='q".$sequence."' name='q".$sequence."' size=".$mylength." MAXLENGTH=".$row->fieldSize."> ".$row->suffix."";
	              }else{
	              ?>
	                <div class="BFSurveySuppressQuestion">
	                <?php echo $row->prefix; ?>
	                </div>
	                <div class="BFSurveySuppressOptions">
	                <?php
	                   echo "<INPUT id='q".$sequence."' name='q".$sequence."' size=".$mylength." MAXLENGTH=".$row->fieldSize."> ".$row->suffix."";
	                ?>
	                </div>
       			  <?php
       			  }
	           }
	       }else if($row->question_type == "1"){  // Radio

		      if($randomOptions != "1"){

	       		    for($z=0; $z < 20; $z++)
	       		    {
	       		        $tempvalue="option".($z+1);
	       		        $tempnext="next_question".($z+1);

	       		        if($row->$tempvalue != ""){
				  			if($row->mandatory & class_exists('BFBehavior')){
				  			   ?>

							   <?php if($row->horizontal == "1"){
		 	  			          $myclass="bfradiohorizontal";
		 	  			       }else{
		 	  			          $myclass="bfradio";
		 	  			       } ?>

				  			   <?php echo JText::_($row->prefix); ?>
				  			   <label for="q<?php echo $sequence; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="q<?php echo $sequence; ?><?php echo $z; ?>" name="q<?php echo $sequence; ?>" value="<?php echo $row->$tempvalue; ?>" class="required validate-radio"><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
				  			   <?php
				  			}else{
				  			   ?>
				  			   <?php echo JText::_($row->prefix); ?>
				  			   <label for="q<?php echo $sequence; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="q<?php echo $sequence; ?><?php echo $z; ?>" name="q<?php echo $sequence; ?>" value="<?php echo $row->$tempvalue; ?>"><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
				  			   <?php
			  				}

			  				if($row->horizontal == "1"){
					    	    echo "&nbsp;&nbsp;&nbsp;";
		   		   	    	}else{
		   		   	    	    echo "<br>";
		   		   	    	}
	       	        	}
	       	     	}

	       	  }else{
				//-------------------------------random options--------------------------------------------------
				$numofoptions=0;
                $myoptions = array();
                //how many options are there?
                for($x=0; $x < 20; $x++)
                {
                   $numofoptions = $x;
                   $tempvalue="option".($x+1);

                   if($row->$tempvalue == ""){
                      $x=20;
                   }else{
                      $myoptions[$x]= $x;
                   }
                }

                //randomly reorder questions
                shuffle($myoptions);

                for($y=0; $y < 20; $y++)
                {
                    if($y+1 > $numofoptions){
                       $z = $y;
                    }else{
                       $z = $myoptions[$y];
                    }

                    $tempvalue="option".($z+1);
                    $tempnext="next_question".($z+1);

                    if($row->$tempvalue != ""){
                       if($row->mandatory & class_exists('BFBehavior')){
                          ?>

                          <?php if($row->horizontal == "1"){
                             $myclass="bfradiohorizontal";
                          }else{
                             $myclass="bfradio";
                          } ?>

                          <?php echo JText::_($row->prefix); ?>
                          <label for="q<?php echo $sequence; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="q<?php echo $sequence; ?><?php echo $z; ?>" name="q<?php echo $sequence; ?>" value="<?php echo $row->$tempvalue; ?>" class="required validate-radio"><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
                          <?php
                       }else{
                          ?>
                          <?php echo JText::_($row->prefix); ?>
                          <label for="q<?php echo $sequence; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="q<?php echo $sequence; ?><?php echo $z; ?>" name="q<?php echo $sequence; ?>" value="<?php echo $row->$tempvalue; ?>"><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
                          <?php
                       }

                       if($row->horizontal == "1"){
                          echo "&nbsp;&nbsp;&nbsp;";
                       }else{
                          echo "<br>";
                       }
                     }
                  }
	              //-------------------------------end random options--------------------------------------------------

	       	  }

			}else if($row->question_type == "2"){  // Checkbox

				if($row->horizontal == "1"){
				   $myclass="bfradiohorizontal";
				}else{
				   $myclass="bfradio";
				}

			    for($z=0; $z < 20; $z++)
			   {
			       $tempvalue="option".($z+1);
	   	           if($row->$tempvalue != ""){
	   	              if($row->$tempvalue == "_OTHER_"){
	   	                 if($row->mandatory & class_exists('BFBehavior')){
	       	                echo ''.$row->prefix.'<label for="q'.$sequence.''.$z.'" class='.$myclass.'><input type="checkbox" name="q'.$sequence.'[]" value="'.$row->$tempvalue.'" id="q'.$sequence.''.$z.'" value="'.$row->$tempvalue.'" class="required validate-checkbox" onchange="MakeOtherMandatory('.$sequence.','.$z.')">'.$row->otherprefix.'</label><INPUT id="q'.$sequence.''.$z.'_other" name="q'.$sequence.'_OTHER_" class="">'.$row->othersuffix.' '.$row->suffix.'';
	       	             }else{
	       	                echo ''.$row->prefix.'<label for="q'.$sequence.''.$z.'" class='.$myclass.'><input type="checkbox" name="q'.$sequence.'[]" value="'.$row->$tempvalue.'" id="q'.$sequence.''.$z.'" value="'.$row->$tempvalue.'">'.$row->otherprefix.'</label><INPUT id="q'.$sequence.''.$z.'" name="q'.$sequence.'_OTHER_">'.$row->othersuffix.' '.$row->suffix.'';
	       	             }
	       	          }else{
			             if($row->mandatory & class_exists('BFBehavior')){
	   	                    echo ''.$row->prefix.'<label for="q'.$sequence.''.$z.'" class='.$myclass.'><input type="checkbox" name="q'.$sequence.'[]" value="'.$row->$tempvalue.'" id="q'.$sequence.''.$z.'" class="required validate-checkbox">'.$row->$tempvalue.'</label> '.$row->suffix.'';
				         }else{
	   	                    echo ''.$row->prefix.'<label for="q'.$sequence.''.$z.'" class='.$myclass.'><input type="checkbox" name="q'.$sequence.'[]" value="'.$row->$tempvalue.'" id="q'.$sequence.''.$z.'" >'.$row->$tempvalue.'</label> '.$row->suffix.'';
	   	                 }
				   	  }
			  		  if($row->horizontal == "1"){
					      echo "&nbsp;&nbsp;&nbsp;";
		   		   	  }else{
		   		   	      echo "<br>";
		   		   	  }
	   	           }
	   	        }
	       }

		   echo '<input type="hidden" name="question'.($i+1).'" value="'.$sequence.'" />';
		   echo '<input type="hidden" name="question_type'.($i+1).'" value="'.$row->question_type.'" />';
		   echo '<input type="hidden" name="field_name'.($i+1).'" value="'.$row->field_name.'">';
	       ?>
	       </div>
	       <div class="bfquiztrialQuestionFooter">

	       </div>
	    </th>
	</tr>
	<?php
	$k = 1 - $k;
}
?>
</table>


<?php
$num=count( $this->items );
?>

<input type="hidden" name="page_no" value="<?php echo $page_no ?>" />
<input type="hidden" name="start_qn" value="<?php echo $start_qn ?>" />
<input type="hidden" name="end_qn" value="<?php echo $end_qn ?>" />
<input type="hidden" name="num" value="<?php echo $num ?>" />
<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="task" value="add" />
<input type="hidden" name="allowEmail" value="<?php echo $allowEmail ?>" />
<input type="hidden" name="emailSubject" value="<?php echo $emailSubject ?>" />
<input type="hidden" name="emailBody" value="<?php echo $emailBody ?>" />
<input type="submit" name="task_button" class="button" value="<?php echo JText::_( $submitText ); ?>" />
</form>

<table width="200" border=1>
<tr>
<td>
<div class="progressbar_color_1" style="height:5px;width:<?php echo (($page_no/$total_qns)*100) ?>%"></div>
</td>
</tr>
</table>

<?php
//*****************************************************************************
//* Hi Hackers,
//*	   Congratulations, you are smart enough to decrypt this code!
//* Please don't decrypt my software and distribute a nullified version.
//* You should encourage people to purchase Joomla extensions so that developers
//* like me can continue to make our product better and continue to provide a
//* high quality of support at a reasonable cost.
//*
//* regards
//* Tim
//******************************************************************************
?>
<br>
<table width="100%">
<tr>
<td><?php echo JText::_('Powered by'); ?> <a href="http://www.tamlyncreative.com.au/software/" target="_blank">BF Quiz</a></td>
<td><a href="http://www.tamlyncreative.com.au/software/" target="_blank"><img src="./components/com_bfquiztrial/images/bflogo.jpg" width="125" height="42" align="right" border="0" ></a></td>
</tr>
</table>


<?php
}  // end check start_qn
?>

<?php
	      }else{
	  	     echo JText::_( "Your Joomla account (UID) has already completed this quiz.");
	      }

      }else{
  	     echo JText::_( "Your email address has already completed this quiz.");
      }

   }else{
      echo JText::_( "Your IP address has already completed this quiz.");
   }

}else{
   echo JText::_( "You must log in before you can use this system.");
}
?>
