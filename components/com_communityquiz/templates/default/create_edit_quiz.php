<?php 
defined('_JEXEC') or die('Restricted access');
$itemid = CommunityQuizHelper::getItemId();
$user = JFactory::getUser();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function($){
	QuizFactory.init_create_edit_quiz();
});
//-->
</script>
<div id="quiz-wrapper-create">
	<div class="page-header"><?php echo $this->list_header;?></div>
	<form id="quiz-form" action="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=save'.(isset($this->quiz)?'&id='.$this->quiz->id:'').$itemid);?>" method="post">
		<table class="form-fields ui-widget-content ui-corner-all">
			<tr>
				<td><label for="title" class="field-label"><?php echo JText::_('LBL_TITLE');?>: </label></td>
				<td><input class="required" type="text" name="title" id="title" value="<?php echo isset($this->quiz) ? $this->escape($this->quiz->title) : '';?>"/></td>
			</tr>
			<tr>
				<td><label for="description"><?php echo JText::_('LBL_DESCRIPTION');?>: </label></td>
				<td><?php echo CommunityQuizHelper::load_editor('description', 'description', isset($this->quiz) ? $this->quiz->description : '', '10', '40', '100%', '200px', null, 'width: 99%;'); ?></td>
			</tr>
			<tr>
				<td><label for="duration"><?php echo JText::_('LBL_TIME_DURATION');?></label></td>
				<td><input class="required" type="text" size="6" maxlength="6" name="duration" id="duration" value="<?php echo isset($this->quiz) ? $this->quiz->duration : '0';?>"/></td>
			</tr>
			<tr>
				<td><label for="category"><?php echo JText::_('LBL_CATEGORY');?>:</label></td>
				<td>
					<select name="category" id="category" class="required">
						<option value=""><?php echo JText::_('TXT_SELECT_CATEGORY');?></option>
						<?php if($this->categories):?>
						<?php foreach ($this->categories as $category):?>
						<?php if($category->parent_id):?>
						<option value="<?php echo $category->id;?>" <?php echo (isset($this->quiz) && ($category->id == $this->quiz->catid))?'selected="selected"':''?>><?php echo str_repeat('.', ($category->nlevel ? $category->nlevel-1 : 0) * 4) . $this->escape($category->title)?></option>
						<?php endif;?>
						<?php endforeach;?>
						<?php endif;?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="show-result-show"><?php echo JText::_('LBL_SHOW_RESULT');?></label></td>
				<td>
					<input type="radio" name="show-result" id="show-result-show" value="1" <?php echo (isset($this->quiz) && $this->quiz->show_answers) == '1' ? 'checked="checked"':'';?>>
						<label for="show-result-show"><?php echo JText::_('LBL_SHOW');?></label>
					<input type="radio" name="show-result" id="show_result-hide" value="0" <?php echo (isset($this->quiz) && $this->quiz->show_answers) == '0' ? 'checked="checked"':'';?>>
						<label for="show-result-hide"><?php echo JText::_('LBL_HIDE');?></label>
				</td>
			</tr>
			<tr>
				<td><label for="show-template-show"><?php echo JText::_('LBL_SHOW_TEMPLATE');?></label></td>
				<td>
					<input type="radio" name="show-template" id="show-template-show" value="1" <?php echo (isset($this->quiz) && $this->quiz->show_template) == '1' ? 'checked="checked"':'';?>>
						<label for="show-template-show"><?php echo JText::_('LBL_SHOW');?></label>
					<input type="radio" name="show-template" id="show_template-hide" value="0" <?php echo (isset($this->quiz) && $this->quiz->show_template) == '0' ? 'checked="checked"':'';?>>
						<label for="show-template-hide"><?php echo JText::_('LBL_HIDE');?></label>
				</td>
			</tr>
			<tr>
				<td><label for="multiple-responses_allow"><?php echo JText::_('LBL_MULTIPLE_RESPONSES');?></label></td>
				<td>
					<input type="radio" name="multiple_responses" id="multiple-responses_allow" value="1" <?php echo (isset($this->quiz) && $this->quiz->multiple_responses) == '1' ? 'checked="checked"':'';?>>
						<label for="multiple_responses_allow"><?php echo JText::_('LBL_ALLOW');?></label>
					<input type="radio" name="multiple_responses" id="multiple_responses_disallow" value="0" <?php echo (isset($this->quiz) && $this->quiz->multiple_responses) == '0' ? 'checked="checked"':'';?>>
						<label for="multiple_responses_disallow"><?php echo JText::_('LBL_DISALLOW');?></label>
				</td>
			</tr>
		</table>
		<div class="navigation ui-widget-content ui-corner-all">
			<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=home'.$itemid);?>" id="btn-cancel"><?php echo JText::_('LBL_CANCEL');?></a>
			<a href="#" id="btn-continue" onclick="return false;"><?php echo JText::_('LBL_CONTINUE');?></a>
		</div>
		<input type="hidden" name="id" value="<?php echo isset($this->quiz) ? $this->quiz->id : '';?>">
	</form>
</div>