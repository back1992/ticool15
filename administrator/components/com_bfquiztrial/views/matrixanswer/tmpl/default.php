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
			if (form.description.value == "") {
				alert( "<?php echo JText::_( 'Please enter a description.', true ); ?>" );
			} else if ( getSelectedValue('adminForm','catid') == 0 ) {
				alert( "<?php echo JText::_( 'Please select a category.', true ); ?>" );
			} else {
				submitform( pressbutton );
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
<div class="col width-45">
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
				&nbsp;<img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Category::This is the quiz category that your answer matrix applies to.">
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Description' ); ?>: *
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="description" id="description" size="60" maxlength="250" value="<?php echo $this->bfquiztrial->description;?>"/>
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
	</table>

	<input class="inputbox" type="hidden" name="ordering" id="ordering" size="6" value="<?php echo $this->bfquiztrial->ordering;?>" />

	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Result' ); ?></legend>

		<table class="admintable">
		<tr>
			<td valign="top" align="right" class="key">
			    <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title="Redirect URL::This is the page that you will be redirected to if criteria match. Should be left blank if using Results Text below.">
			    &nbsp;
				<label for="title">
					<?php echo JText::_( 'Redirect URL' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="redirectURL" id="redirectURL" size="60" maxlength="250" value="<?php echo $this->bfquiztrial->redirectURL;?>"/>
			</td>
		</tr>
		</table>

	</fieldset>

</div>
<div class="col width-45">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Conditions' ); ?></legend>
	<table class="admintable" align="center">
	   <tr>
	      <td align="center"><img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Score::A, B, C or D etc.'>
	      <br>Score
	      </td>
	      <td align="center"><img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Condition::Select from list.'>
	      <br>Condition</td>
	      <td align="center"><img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Qty::Numeric value. How many instances of this score.'>
	      <br>
	      Qty</td>
	   </tr>

	   <?php for ($i=0, $n=5; $i < $n; $i++ ) { ?>
	   <tr>
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
		        <?php $tempnext="condition".($i+1); ?>
				<?php echo bfquiztrialHelper::ConditionType( $this->bfquiztrial->$tempnext, ($i+1) ); ?>
		  </td>

	      <td>
	         <?php $tempnext="qty".($i+1); ?>
		  	 <?php
		  	      if(!isset($this->bfquiztrial->$tempnext)){
		  	         $this->bfquiztrial->$tempnext = "";
		  	      }
		  	 ?>
		  	 <input class="text_area" type="text" name="qty<?php echo ($i+1); ?>" id="qty<?php echo ($i+1); ?>" size="3" maxlength="10" value="<?php echo $this->bfquiztrial->$tempnext;?>" />
		  </td>

		  <td>
		        <?php $tempnext="operator".($i+1); ?>
				<?php echo bfquiztrialHelper::OperatorType( $this->bfquiztrial->$tempnext, ($i+1) ); ?>
		  </td>

	   </tr>
	   <?php } ?>

	</table>

		<table class="admintable">
			<tr>
				<td width="100" align="right" class="key">
					<label for="title">
						<?php echo JText::_( 'Exact Match' ); ?>:
						&nbsp;<td align="center"><img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Exact match::Used when you want to match a specific answer combination. Should be left blank if using selection criteria above.'>
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="exactMatch" id="exactMatch" size="60" maxlength="250" value="<?php echo $this->bfquiztrial->exactMatch;?>"/>
				</td>
			</tr>
		</table>
		</fieldset>


</div>
<div class="clr"></div>

<?php $editor = JFactory::getEditor(); ?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'Result text' ); ?></legend>
<center>
	<?php echo $editor->display( 'resultText',  $this->bfquiztrial->resultText, '800', '400', '60', '40', array()) ; ?>
</center>
</fieldset>

<input type="hidden" name="default" value="<?php echo $this->bfquiztrial->default; ?>">

<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="id" value="<?php echo $this->bfquiztrial->id; ?>" />
<input type="hidden" name="task" value="savematrix" />
<input type="hidden" name="controller" value="matrixanswer" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>