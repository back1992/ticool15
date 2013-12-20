<?php 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT.DS.'helpers'.DS.'answers.php';
$itemid = CommunityQuizHelper::getItemId(true);
$user = JFactory::getUser();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	QuizFactory.init_quiz_results();
});
//-->
</script>
<div id="quiz-results">
	<?php if($this->quiz && $this->quiz->questions):?>
	<div class="page-header"><?php echo $this->escape($this->quiz->title);?></div>
	<?php
	$i = 1;
	$count = 1;
	if($this->quiz->questions){
		foreach ($this->quiz->questions as $question){
			echo '<div class="quiz-question'.($i?' alt':'').' ui-widget-content ui-corner-all">';
			switch ($question->question_type){
				case 1: //Page Header
					echo QuizAnswerManager::page_header($question,"qn-page-header");
					break;
				case 2: // Choice - Radio
				case 3: // Choice - Checkbox
				case 4: // Choice - Select
					echo QuizAnswerManager::multiple_choice($question,"qn-multiple-choice");
					break;
				case 5: // Grid - Radio
				case 6: // Grid - Checkbox
					echo QuizAnswerManager::multiple_choice_grid($question,"qn-grid");
					break;
				case 7: // Free Text - Single line
				case 8: // Free Text - Multiline
				case 9: // Free Text - Password
					echo QuizAnswerManager::free_text($question,"qn-freetext");
					break;
				case 10: // Free Text - Rich Text
					echo QuizAnswerManager::rich_text($question,"qn-richtext");
					break;
			}
			echo '</div>';
			echo '<div>'.CommunityQuizHelper::loadModulePosition('quiz_results_after_question'.$count).'</div>';
			$count++;
			$i = 1 - $i;
		}
	}
	?>
	<div class="score-wrapper ui-widget-content ui-corner-all">
		<div class="score"><?php echo JText::_('TXT_YOUR_SCORE');?>:&nbsp;<?php echo QuizAnswerManager::get_score();?></div>
	</div>
	<div class="navigation">
		<a id="btn_home" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&'.$itemid);?>"><?php echo JText::_('LBL_HOME');?></a>
		<?php if(!$user->guest):?>
		<a id="btn_my_responses" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=my_responses'.$itemid);?>"><?php echo JText::_('LBL_MY_RESPONSES');?></a>
		<?php endif;?>
		<?php if($this->quiz->multiple_responses == '1'):?>
		<a id="btn_respond" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$this->quiz->id.':'.$this->quiz->alias.$itemid);?>"><?php echo JText::_('LBL_TAKE_AGAIN');?></a>
		<?php endif;?>
	</div>
	<?php endif;?>
</div>
