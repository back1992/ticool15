<?php 
defined('_JEXEC') or die('Restricted access');
$itemid = CommunityQuizHelper::getItemId();
require_once JPATH_COMPONENT.DS.'helpers'.DS.'answers.php';
?>
<script type="text/javascript">jQuery(document).ready(function($){QuizFactory.init_quiz_reports();});
</script>
<div id="quiz-reports">
	<?php if(!empty($this->quiz)):?>
	<div class="subtitle"><?php echo $this->escape($this->quiz->title);?></div>
	<?php 
	$i = 0;
	foreach($this->quiz->questions as $question){
		echo '<div class="quiz-question'.($i?'':' alt').' ui-widget-content ui-corner-all">';
		switch ( $question->question_type) {
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
		$i = 1 - $i;
	}
	?>
	<?php endif;?>
	<div class="navigation ui-widget-content ui-corner-all">
		<strong><?php echo JText::_('TXT_YOUR_SCORE').': '.QuizAnswerManager::get_score();?></strong>
	</div>
	<div class="navigation ui-widget-content ui-corner-all">
		<a id="btn_back" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=reports&id='.$this->quiz->id.':'.$this->quiz->alias.$itemid);?>"><?php echo JText::_('LBL_BACK');?></a>
		<a id="btn_home" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid);?>"><?php echo JText::_('LBL_HOME');?></a>
	</div>
</div>