<?php 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT.DS.'helpers'.DS.'questions.php';
$itemid = CommunityQuizHelper::getItemId();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	QuizFactory.init_quiz_response();
});
//-->
</script>
<?php if(!empty($this->quiz)):?>
<div id="quiz-response">
	<?php if($this->quiz->duration > 0):?>
	<div class="navigation quiz-clock ui-widget-content ui-corner-all"><?php echo JText::_('LBL_TIME_LEFT');?>:&nbsp;<span class="quiztimer"></span></div>
	<?php endif;?>
	<form id="response-form" action="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=save_response');?>" method="post">
		<div class="page-header"><?php echo $this->escape($this->quiz->title);?></div>
		<div id="dummyelement"></div>
		<?php
		$i = 1;
		$count = 1;
		if($this->quiz->questions){
			foreach ($this->quiz->questions as $question){
				echo '<div class="quiz-question'.($i?' alt':'').' ui-widget-content ui-corner-all">';
				switch ($question->question_type){
					case 1: //Page Header
						echo QuizQuestionManager::page_header($question,"qn-page-header");
						break;
					case 2: // Choice - Radio
						echo QuizQuestionManager::choice_radio($question,"qn-choice_radio");
						break;
					case 3: // Choice - Checkbox
						echo QuizQuestionManager::choice_checkbox($question,"qn-choice_checkbox");
						break;
					case 4: // Choice - Select
						echo QuizQuestionManager::choice_select($question,"qn-choice_select");
						break;
					case 5: // Grid - Radio
						echo QuizQuestionManager::grid_radio($question,"qn-grid_radio");
						break;
					case 6: // Grid - Checkbox
						echo QuizQuestionManager::grid_checkbox($question,"qn-grid_checkbox");
						break;
					case 7: // Free Text - Single line
						echo QuizQuestionManager::free_text_singleline($question,"qn-freetext-singleline");
						break;
					case 8: // Free Text - Multiline
						echo QuizQuestionManager::free_text_multiline($question,"qn-freetext-multiline");
						break;
					case 9: // Free Text - Password
						echo QuizQuestionManager::free_text_password($question,"qn-freetext-password");
						break;
					case 10: // Free Text - Rich Text
						echo QuizQuestionManager::free_text_rich_editor($question,"qn-freetext-richeditor");
						break;
				}
				echo '</div>';
				echo '<div>'.CommunityQuizHelper::loadModulePosition('quiz_response_after_question'.$count).'</div>';
				$count++;
				$i = 1 - $i;
			}
		} 
		?>
		<?php if($this->quiz->final_page):?>
		<div class="navigation ui-widget-content ui-corner-all">
			<div class="rating-help"><?php echo JText::_('TXT_RATING_HELP');?></div>
        	<div class="star-rating">
        		<span class="rating-float">&nbsp;</span>
        		<div class="rating-outer-wrapper">
	        		<span class="rating-wrapper">
		        		<select name="quiz-rating">
							<option value="1">Poor</option>
							<option value="2">Average</option>
							<option value="3">Good</option>
							<option value="4">Very good</option>
							<option value="5">Excellent</option>        			
		        		</select>
		        	</span>
        		</div>
	        	<span class="clear"></span>
        	</div>
		</div>
		<?php endif;?>
		<div class="navigation ui-widget-content ui-corner-all">
			<a id="btn_cancel" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&'.$itemid);?>"><?php echo JText::_('LBL_CANCEL');?></a>
			<?php if($this->quiz->final_page):?>
			<a id="btn_submit" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=save_response'.$itemid);?>" onclick="return false;"><?php echo JText::_('LBL_FINISH');?></a>
			<?php else:?>
			<a id="btn_submit" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=save_response'.$itemid);?>" onclick="return false;"><?php echo JText::_('LBL_CONTINUE');?></a>
			<?php endif;?>
			<input type="hidden" name="id" value="<?php echo $this->quiz->id;?>">
			<input type="hidden" name="pid" value="<?php echo $this->quiz->pid;?>">
			<input type="hidden" name="rid" value="<?php echo $this->quiz->response_id;?>">
			<input type="hidden" name="finalize" value="0">
			<input type="hidden" name="page_number" value="<?php echo $this->quiz->page_number;?>">
		</div>
	</form>
	<?php if($this->quiz->duration > 0):?>
	<div class="navigation quiz-clock ui-widget-content ui-corner-all"><?php echo JText::_('LBL_TIME_LEFT');?>:&nbsp;<span class="quiztimer"></span></div>
	<?php endif;?>
</div>
<div id="default_error_required" style="display: none"><div><?php echo JText::_("MSG_QUESTION_MANDATORY");?></div></div>
<?php 
jimport('joomla.utilities.date');
$created = new JDate($this->quiz->response_created);
$now = new JDate();
$timeleft = $this->quiz->duration*60 - ($now->toUnix() - $created->toUnix());
$timeleft = ($this->quiz->duration > 0) ? (($timeleft < 0) ? 0 : $timeleft) : -1;
?>
<div id="quiz_duration" style="display: none"><?php echo $timeleft;?></div>
<?php endif;?>