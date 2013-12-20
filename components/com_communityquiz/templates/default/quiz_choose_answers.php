<?php 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT.DS.'helpers'.DS.'questions.php';
$itemid = CommunityQuizHelper::getItemId();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	QuizFactory.init_quiz_choose_answers();
});
//-->
</script>
<div id="quiz-response">
	<form id="response-form" action="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=save_response');?>" method="post">
		<div class="page-header"><?php echo $this->escape($this->quiz->title);?></div>
		<div class="navigation ui-widget-content ui-corner-all"><strong><?php echo JText::_('MSG_CHOOSE_CORRECT_ANSWERS');?></strong></div>
		<?php
		$i = 1;
		if($this->quiz->questions){
			foreach ($this->quiz->questions as $question){
				if($question->question_type > 1 && $question->question_type < 6){
					echo '<div class="quiz-question  ui-widget-content ui-corner-all">';
					switch ($question->question_type){
						case 2: // Choice - Radio
							echo QuizQuestionManager::choice_radio($question,"qn-choice_radio", true, true, false);
							break;
						case 3: // Choice - Checkbox
							echo QuizQuestionManager::choice_checkbox($question,"qn-choice_checkbox", true, true, false);
							break;
						case 4: // Choice - Select
							echo QuizQuestionManager::choice_select($question,"qn-choice_select", true, true, false);
							break;
						case 5: // Grid - Radio
							echo QuizQuestionManager::grid_radio($question,"qn-grid_radio", true, true, false);
							break;
						case 6: // Grid - Checkbox
							echo QuizQuestionManager::grid_checkbox($question,"qn-grid_checkbox", true, true, false);
							break;
					}
					echo '</div>';
					$i = 1 - $i;
				}
			}
		} 
		?>
		<div class="navigation ui-widget-content ui-corner-all">
			<a id="btn_cancel" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&'.$itemid);?>"><?php echo JText::_('LBL_CANCEL');?></a>
			<a id="btn_submit" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=save_answers'.$itemid);?>" onclick="return false;"><?php echo JText::_('LBL_SUBMIT');?></a>
			<input type="hidden" name="id" value="<?php echo $this->quiz->id;?>">
		</div>
	</form>
</div>
<div id="default_error_required" style="display: none"><div><?php echo JText::_("MSG_QUESTION_MANDATORY");?></div></div>