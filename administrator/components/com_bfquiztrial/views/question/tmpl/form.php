<?php defined('_JEXEC') or die('Restricted access'); ?>

		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.question.value == "") {
				alert( "<?php echo JText::_( 'Please enter a question.', true ); ?>" );
			} else if ( getSelectedValue('adminForm','question_type') == 1 & form.option1.value == "") {
				alert( "<?php echo JText::_( 'For radio type you must enter options.', true ); ?>" );
			} else if (form.field_name.value == "") {
				alert( "<?php echo JText::_( 'Please enter a field name.', true ); ?>" );
			} else if ( getSelectedValue('adminForm','catid') == 0 ) {
				alert( "<?php echo JText::_( 'Please select a category.', true ); ?>" );
			} else if(document.getElementById("field_name").value.match(" ") != null){  //can't have spaces in field names
		       alert("Field name must not contain spaces");
			} else if(document.getElementById("field_name").value.match("-") != null){  //can't have minus in field names
		       alert("Field name must not contain minus(-)");
			} else {
				submitform( pressbutton );
			}
		}


		function showOptions(){
			  var i=0;
			  for (i=1;i<21;i++){
		         document.getElementById("option"+i).style.display = '';
		         document.getElementById("optionText"+i).style.display = '';
		         document.getElementById("next_question"+i).style.display = '';
		         document.getElementById("score"+i).style.display = '';
		         document.getElementById("answer"+i).style.display = '';
		      }
		}

		function hideAllOptions(){
		   var i=0;
		   for (i=1;i<21;i++){
	          document.getElementById("option"+i).style.display = 'none';
	          document.getElementById("optionText"+i).style.display = 'none';
	          if(i>1){
	             document.getElementById("next_question"+i).style.display = 'none';
	             document.getElementById("score"+i).style.display = 'none';
		         document.getElementById("answer"+i).style.display = 'none';
	          }
	       }
		}


		function hideBranching(){
   		var i=0;
		   for (i=1;i<21;i++){
	          if(i>1){
	             document.getElementById("next_question"+i).style.display = 'none';
	          }
	       }
		}

		function hideType(){
		    document.getElementById("next_question1").style.display = '';
		    document.getElementById("horizontal_values").style.display = 'none';
 		    document.getElementById("horizontalText").style.display = 'none';
 		    document.getElementById("field_size").style.display = 'none';
 		    document.getElementById("fieldSize").style.display = 'none';

		    if(document.getElementById("question_type").value == 0){  //text
			   hideAllOptions();
			   document.getElementById("option1").style.display = '';
	           document.getElementById("optionText1").style.display = '';
		       document.getElementById("mandatory_1").style.display = '';
 		       document.getElementById("mandatoryText").style.display = '';
		       document.getElementById("field_size").style.display = '';
 		       document.getElementById("fieldSize").style.display = '';
		    }else if(document.getElementById("question_type").value == 1){  //radio
			   document.getElementById("mandatory_1").style.display = '';
		       document.getElementById("mandatoryText").style.display = '';
		       document.getElementById("horizontal_values").style.display = '';
 		       document.getElementById("horizontalText").style.display = '';
		       showOptions();
		    }
		}

		//-->
		</script>

<script language="javascript">
	function imposeMaxLength(Object, MaxLen)
	{
	  return (Object.value.length < MaxLen);
	}
</script>

<?php
// For popup help
JHTML::_('behavior.tooltip');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col width-65">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'Category' ); ?>: *
				</label>
			</td>
			<td>
				<?php echo $this->lists['catid']; ?>
				&nbsp;<img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Category::This is the quiz that your question will appear in">
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Question' ); ?>: *
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="question" id="question" size="60" maxlength="250" value="<?php echo $this->bfquiztrial->question;?>"/>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label name="suppressText" id="suppressText"><?php echo JText::_( 'Suppress Question' ); ?>:</label>
			</td>
			<td>
			    <?php
			       if(!isset($this->bfquiztrial->suppressQuestion)){
			          $this->bfquiztrial->suppressQuestion = 0;
			       }
			    ?>
				<label name="suppressQuestion_1" id="suppressQuestion_1"><?php echo JHTML::_( 'select.booleanlist',  'suppressQuestion', 'class="inputbox"', $this->bfquiztrial->suppressQuestion ); ?></label>
				&nbsp;<img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Suppress Question::This will hide the question, so you only see the options.">
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
			    <?php
			       if(!isset($this->bfquiztrial->published)){
			          $this->bfquiztrial->published=0;
			       }
			    ?>
				<?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->bfquiztrial->published ); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="helpText">
					<?php echo JText::_( 'Help Text' ); ?>:
				</label>
			</td>
			<td>
			   <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Help text::This appears between the question and the options. It can contain HTML, and supports Allvideos tags eg. {youtube}Xr2mhMHhUMY{/youtube}.'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			    <?php
			       if(!isset($this->bfquiztrial->helpText)){
			          $this->bfquiztrial->helpText = "";
			       }
			    ?>
			    <?php $editor = JFactory::getEditor(); ?>
				<?php echo $editor->display( 'helpText',  $this->bfquiztrial->helpText, '500', '300', '60', '40', array()) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="field_name">
					<?php echo JText::_( 'DB field Name' ); ?>: *
				</label>
			</td>
			<td>
			    <?php
			       if(!isset($this->bfquiztrial->field_name)){
			          $this->bfquiztrial->field_name = "";
			       }
			    ?>
			    <input class="inputbox" type="text" name="field_name" id="field_name" size="30" value="<?php echo $this->bfquiztrial->field_name;?>"/>
			    &nbsp;<img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Field Name::This is the name of the field in the mySQL table used to store reponses to this quiz. Field name cannot use reserved words such as 'like' or 'where', must be unique, and must not contain spaces, dashes, slashes or any other special characters.">
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="field_size" id="field_size">
					<?php echo JText::_( 'Max answer length' ); ?>:
				</label>
			</td>
			<td>
			    <?php
			       if(!isset($this->bfquiztrial->fieldSize)){
			          $this->bfquiztrial->fieldSize = 255;
			       }
			    ?>
			    <input class="inputbox" type="text" name="fieldSize" id="fieldSize" maxlength="5" size="30" value="<?php echo $this->bfquiztrial->fieldSize;?>"/>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="question_type">
					<?php echo JText::_( 'Type' ); ?>:
				</label>
			</td>
			<td>
				<?php echo bfquiztrialHelper::QuestionType( $this->bfquiztrial->question_type ); ?>
			</td>
		</tr>
		<tr>
		   <td width="100" class="key">
		      <label name="horizontalText" id="horizontalText"><?php echo JText::_('Horizontal'); ?></label>
		   </td>
		   <td>
		      <?php
		         if(!isset($this->bfquiztrial->horizontal)){
		            $this->bfquiztrial->horizontal = 0;
		         }
		      ?>
		      <div id="horizontal_values">
		      <label name="horizontal_1" id="horizontal_1"><?php echo JHTML::_( 'select.booleanlist',  'horizontal', 'class="inputbox"', $this->bfquiztrial->horizontal ); ?></label>
		      &nbsp;<img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Horizontal::To display radio or checkbox options all on one line.">
		      </div>
		   </td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label name="mandatoryText" id="mandatoryText"><?php echo JText::_( 'Mandatory' ); ?>: *</label>
			</td>
			<td>
			    <?php
			       if(!isset($this->bfquiztrial->mandatory)){
			          $this->bfquiztrial->mandatory = 0;
			       }
			    ?>
				<label name="mandatory_1" id="mandatory_1"><?php echo JHTML::_( 'select.booleanlist',  'mandatory', 'class="inputbox"', $this->bfquiztrial->mandatory ); ?></label>
				&nbsp;<img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Mandatory::For radio & checkbox, you must install and publish BF Validate plugin.">
			</td>
		</tr>
		<tr>
			<td class="key" align="right" valign="top">
				<?php echo JText::_( 'Parent Item' ); ?>:
			</td>
			<td>
			    <?php
			       if(!isset($this->bfquiztrial->parent)){
			          $this->bfquiztrial->parent = 0;
			       }
			    ?>
				<?php echo bfquiztrialHelper::Parent( $this->bfquiztrial ); ?>
			</td>
		</tr>
		<tr>
		   <td class="key" colspan=2>
		   &nbsp;
		   </td>
		</tr>
		<tr>
			<td class="key">
				<label for="solutionText">
					<?php echo JText::_( 'Solution/Answer' ); ?>:
				</label>
			</td>
			<td>
			   <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Solution/Answer::Explaination shown after quiz to explain correct answer.'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			    <?php
			       if(!isset($this->bfquiztrial->solution)){
			          $this->bfquiztrial->solution = "";
			       }
			    ?>
			    <?php $editor = JFactory::getEditor(); ?>
				<?php echo $editor->display( 'solution',  $this->bfquiztrial->solution, '500', '300', '60', '40', array()) ; ?>
			</td>
		</tr>
	</table>

	<input class="inputbox" type="hidden" name="ordering" id="ordering" size="6" value="<?php echo $this->bfquiztrial->ordering;?>" />

	</fieldset>
</div>
<div class="col width-35">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Options' ); ?></legend>
	<table class="admintable">
		<tr>
		    <td>
		    </td>
		    <td>
		    </td>
		    <td>
		       <strong><?php echo JText::_('Answer?'); ?></strong>
		    </td>
		    <td>
			   <strong><?php echo JText::_('Score'); ?></strong>
		    </td>
		    <td><strong><?php echo JText::_('Next Qn ID'); ?></strong>
		    </td>
		</tr>

		<tr>
		    <td>
		    </td>
		    <td>
		    </td>
		    <td>
		       <center>
		       <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Answer?::Tick correct answer(s) to this question.">
		       </center>
		    </td>
		    <td>
		       <center>
			   <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Score::Indicate score for selecting this option.">
			   </center>
		    </td>
		    <td>
		       <center>
		       <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Next Qn ID::Conditional branching that allows you to go to specific question if that option is selected as answer. Enter question's id number.">
		       </center>
		    </td>
		</tr>


		<?php for ($i=0, $n=20; $i < $n; $i++ ) { ?>
		<tr>
			<td width="100" class="key">
				<label name="optionText<?php echo ($i+1); ?>" id="optionText<?php echo ($i+1); ?>"><?php echo JText::_( 'Option' ); ?> <?php echo ($i+1); ?></label>
			</td>
			<td>
			    <?php $tempvalue="option".($i+1); ?>
			    <?php
			       if(!isset($this->bfquiztrial->$tempvalue)){
			          $this->bfquiztrial->$tempvalue = "";
			       }
			    ?>
				<input class="text_area" type="text" name="option<?php echo ($i+1); ?>" id="option<?php echo ($i+1); ?>" size="32" maxlength="150" value="<?php echo $this->bfquiztrial->$tempvalue;?>" />
			</td>

			<td>
			   <center>
			   <?php $tempnext="answer".($i+1); ?>
			   <?php
			      if(!isset($this->bfquiztrial->$tempnext)){
			         $this->bfquiztrial->$tempnext = 0;
			      }

			      if ($this->bfquiztrial->$tempnext==1) {
			   ?>
 	      	      <input type="checkbox" name="answer<?php echo ($i+1); ?>" value=1 id="answer<?php echo ($i+1); ?>" checked="checked" />
 	      	   <?php
	       	   }else {
	       	   ?>
	      		  <input type="checkbox" name="answer<?php echo ($i+1); ?>" id="answer<?php echo ($i+1); ?>" value=1 />
	      	   <?php
		       }
			   ?>

			   </center>
			</td>

			<td>
			   <?php $tempnext="score".($i+1); ?>
			   <?php
			      if(!isset($this->bfquiztrial->$tempnext)){
			         $this->bfquiztrial->$tempnext = "";
			      }
			   ?>
				<input class="text_area" type="text" name="score<?php echo ($i+1); ?>" id="score<?php echo ($i+1); ?>" size="3" maxlength="10" value="<?php echo $this->bfquiztrial->$tempnext;?>" />
			</td>

			<td>
			   <center>
			   <?php $tempnext="next_question".($i+1); ?>
			   <?php
			      if(!isset($this->bfquiztrial->$tempnext)){
			         $this->bfquiztrial->$tempnext = "";
			      }
			   ?>
				<input class="text_area" type="text" name="next_question<?php echo ($i+1); ?>" id="next_question<?php echo ($i+1); ?>" size="3" maxlength="150" value="<?php echo $this->bfquiztrial->$tempnext;?>" />
				</center>
			</td>
		</tr>
		<?php } ?>

	</table>
	<table class="admintable">
	<tr>
	   <td width="100" class="key">
	      <label name="prefixText" id="prefixText"><?php echo JText::_('Option Prefix'); ?></label>
	   </td>
	   <td>
	      <?php
	         if(!isset($this->bfquiztrial->prefix)){
	            $this->bfquiztrial->prefix = "";
	         }
	      ?>
	      <input class="inputbox" type="text" name="prefix" id="prefix" size="20" value="<?php echo $this->bfquiztrial->prefix;?>" />
	   </td>
	</tr>
	<tr>
	   <td width="100" class="key">
	      <label name="suffixText" id="suffixText"><?php echo JText::_('Option Suffix'); ?></label>
	   </td>
	   <td>
	      <?php
	         if(!isset($this->bfquiztrial->suffix)){
	            $this->bfquiztrial->suffix = "";
	         }
	      ?>
	      <input class="inputbox" type="text" name="suffix" id="suffix" size="20" value="<?php echo $this->bfquiztrial->suffix;?>" />
	   </td>
	</tr>
	</table>
	</fieldset>


</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="id" value="<?php echo $this->bfquiztrial->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="question" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>


<script language="javascript" type="text/javascript">
<!--
hideType();
//-->
</script>