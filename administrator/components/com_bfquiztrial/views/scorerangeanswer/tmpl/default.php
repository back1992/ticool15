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

</div>
<div class="col width-45">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Conditions' ); ?></legend>
	<table class="admintable" align="center">
	   <tr>
	      <td><?php echo JText::_( 'Score Range' ); ?> <img src="./components/com_bfquiztrial/images/con_info.png" class="hasTip" title='Score Range::Numeric values, eg 10 to 20'>
	      </td>
	      <td>
	      <input class="text_area" type="text" name="scoreStart" id="scoreStart" size="3" maxlength="10" value="<?php echo $this->bfquiztrial->scoreStart;?>" />
	      </td>
	      <td><?php echo JText::_( 'to' ); ?>
	      </td>
	      <td>
	      <input class="text_area" type="text" name="scoreEnd" id="scoreEnd" size="3" maxlength="10" value="<?php echo $this->bfquiztrial->scoreEnd;?>" />
	      </td>
	   </tr>
	</table>

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
<input type="hidden" name="task" value="savescorerange" />
<input type="hidden" name="controller" value="scorerangeanswer" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>