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
$emailText = $config->get( 'emailText' );
$nameText = $config->get( 'nameText' );
$allowEmail = $config->get( 'allowEmail' );

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

//initialise some variables
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
$Itemid = JRequest::getVar( 'Itemid', 0, '', 'int' );


if($catid == 0){
   echo "Error: You must select a category in menu parameters!";
}

$table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

$ip = bfquiztrialController::getIP();
$ipcount = bfquiztrialController::checkIP($table,$ip);

$total_qns=count( $this->items );

if($total_qns > 5){
	$total_qns = 5;
}

?>

<script language="Javascript">
<!--
   // get variable from php
   var showName = "<?php echo $showName ?>";
   var showEmail = "<?php echo $showEmail ?>";
   var timedQuiz = "<?php echo $timedQuiz ?>";
   var timerCompleteText = "<?php echo $timerCompleteText ?>";

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

<?php
$user = &JFactory::getUser();
 if($registeredUsers == "1"){
    $emailcount = bfquiztrialController::checkEmail($table,$user->email);
    $uidcount = bfquiztrialController::checkUID($table,$user->id);
 }

 if (($user->id) | $registeredUsers != "1") {  // test if registerd users only

 	if($ipcount < 1 | $preventMultiple != "1"){  // check if IP address has already completed quiz

       if($emailcount < 1 | $preventMultipleEmail != "1"){  // check if email address has already completed quiz

			if($uidcount < 1 | $preventMultipleUID != "1"){  // check if UID has already completed quiz

?>

<form  method="POST" name="mysurvey" id="mysurvey" class="form-validate" class="form-validate" onSubmit="return myValidate(this);">

<input type="hidden" name="page_no" value="<?php echo $page_no ?>" />
<input type="hidden" name="start_qn" value="<?php echo $start_qn ?>" />
<input type="hidden" name="end_qn" value="<?php echo $end_qn ?>" />

<table >
	<thead>
		<tr>
			<th>
				<div class="bfquiztrialTitle"><?php echo JText::_( $quizTitle ); ?></div>
			</th>
		</tr>
	</thead>
<tr>
    <td>
    <div class="bfquiztrialIntro">
    <?php echo JText::_( $introText ); ?>
    </div>
    <div class="bfquiztrialQuestionFooter">
	</div>
    </td>
</tr>

<?php if($timedQuiz == "1"){ ?>
<tr>
   <td>
      <div class="timeremaining"><?php echo JText::_($timerText); ?> <input id="txt" readonly></div>
   </td>
</tr>
<?php } ?>

<div class="bfquiztrialIntro">
<?php if($showName == "1"){ ?>
<tr>
    <th>
       <?php if($anonymous == "1"){ ?>
       <table align="left">
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
        <table align="left">
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
        <table align="left">
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
       <br><br>
       </DIV>
    </th>
</tr>
<?php }else{
?>
   <input type="hidden" name="email" id="email" value='<?php  echo $user->email; ?>' >
<?php
      }
?>
</div>

<?php
$k = 0;

for ($i=0; $i < $total_qns; $i++)
{
	$row = &$this->items[$i];
	$fieldName = $row->field_name;

    if($fieldName != NULL){
?>
    <input id="field_name" name="field_name" type="hidden" value="<?php echo $row->field_name; ?>">

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
	                 echo "".$row->prefix."<INPUT id='".$fieldName."' name='".$fieldName."' size=".$mylength." MAXLENGTH=".$row->fieldSize." class=\"required\"> ".$row->suffix."";
				  }else{
	              ?>
		             <div class="bfquiztrialSuppressQuestion">
	                 <?php echo $row->prefix; ?>
	                 </div>
	                 <div class="bfquiztrialSuppressOptions">
	                 <?php
	                    echo "<INPUT id='".$fieldName."' name='".$fieldName."' size=".$mylength." MAXLENGTH=".$row->fieldSize." class=\"required\"> ".$row->suffix."";
	                 ?>
	                 </div>
       			  <?php
       			  }
	           }else{
	              if($row->suppressQuestion != "1"){
	                 echo "".$row->prefix."<INPUT id='".$fieldName."' name='".$fieldName."' size=".$mylength." MAXLENGTH=".$row->fieldSize."> ".$row->suffix."";
	              }else{
	              ?>
	                <div class="BFSurveySuppressQuestion">
	                <?php echo $row->prefix; ?>
	                </div>
	                <div class="BFSurveySuppressOptions">
	                <?php
	                   echo "<INPUT id='".$fieldName."' name='".$fieldName."' size=".$mylength." MAXLENGTH=".$row->fieldSize."> ".$row->suffix."";
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

 	  			       if($row->horizontal == "1"){
 	  			          $myclass="bfradiohorizontal";
 	  			       }else{
 	  			          $myclass="bfradio";
 	  			       }

				       if($row->mandatory & class_exists('BFBehavior')){
 	  			          ?>
				     	   <label for="<?php echo $fieldName; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="<?php echo $fieldName; ?><?php echo $z; ?>" name="<?php echo $fieldName; ?>" value="<?php echo $row->$tempvalue; ?>" class="required validate-radio"><?php echo JText::_($row->prefix); ?><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
				     	   <?php
				     	}else{
				     	   ?>
				     	   <label for="<?php echo $fieldName; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="<?php echo $fieldName; ?><?php echo $z; ?>" name="<?php echo $fieldName; ?>" value="<?php echo $row->$tempvalue; ?>"><?php echo JText::_($row->prefix); ?><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
				     	   <?php
			     		}

				      	if($row->horizontal == "1"){
 				     	   //echo "&nbsp;&nbsp;&nbsp;";
 				     	   echo "&nbsp;";
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
                          <label for="<?php echo $fieldName; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="<?php echo $fieldName; ?><?php echo $z; ?>" name="<?php echo $fieldName; ?>" value="<?php echo $row->$tempvalue; ?>" class="required validate-radio"><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
                          <?php
                       }else{
                          ?>
                          <?php echo JText::_($row->prefix); ?>
                          <label for="<?php echo $fieldName; ?><?php echo $z; ?>" class="<?php echo $myclass; ?>"><input type="radio" id="<?php echo $fieldName; ?><?php echo $z; ?>" name="<?php echo $fieldName; ?>" value="<?php echo $row->$tempvalue; ?>"><?php echo JText::_($row->$tempvalue); ?></label> <?php echo JText::_($row->suffix); ?>
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
	       	                echo ''.$row->prefix.'<label for="'.$fieldName.''.$z.'" class='.$myclass.'><input type="checkbox" name="'.$fieldName.'[]" value="'.$row->$tempvalue.'" id="'.$fieldName.''.$z.'" value="'.$row->$tempvalue.'" class="required validate-checkbox" onchange="MakeOtherMandatory('.$fieldName.','.$z.')">'.$row->otherprefix.'</label><INPUT id="'.$fieldName.''.$z.'_other" name="'.$fieldName.'_OTHER_" class="">'.$row->othersuffix.' '.$row->suffix.'';
	       	             }else{
	       	                echo ''.$row->prefix.'<label for="'.$fieldName.''.$z.'" class='.$myclass.'><input type="checkbox" name="'.$fieldName.'[]" value="'.$row->$tempvalue.'" id="'.$fieldName.''.$z.'" value="'.$row->$tempvalue.'">'.$row->otherprefix.'</label><INPUT id="'.$fieldName.''.$z.'" name="'.$fieldName.'_OTHER_">'.$row->othersuffix.' '.$row->suffix.'';
	       	             }
	       	          }else{
			             if($row->mandatory & class_exists('BFBehavior')){
	   	                    echo ''.$row->prefix.'<label for="'.$fieldName.''.$z.'" class='.$myclass.'><input type="checkbox" name="'.$fieldName.'[]" value="'.$row->$tempvalue.'" id="'.$fieldName.''.$z.'" class="required validate-checkbox">'.$row->$tempvalue.'</label> '.$row->suffix.'';
				         }else{
	   	                    echo ''.$row->prefix.'<label for="'.$fieldName.''.$z.'" class='.$myclass.'><input type="checkbox" name="'.$fieldName.'[]" value="'.$row->$tempvalue.'" id="'.$fieldName.''.$z.'" >'.$row->$tempvalue.'</label> '.$row->suffix.'';
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
	       ?>
	       </div>
	       <div class="bfquiztrialQuestionFooter">

	       </div>
	    </th>
	</tr>
	<?php


	} // end field is not NULL

	$k = 1 - $k;
}
?>
</table>

<?php
$num=count( $this->items );
?>

<input type="hidden" name="num" value="<?php echo $num ?>" />
<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="catid" value="<?php echo $catid ?>" />
<input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
<input type="hidden" name="task" value="updateOnePage" />
<input type="submit" name="task_button" class="button" value="<?php echo JText::_( $submitText ); ?>" />
</form>

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
