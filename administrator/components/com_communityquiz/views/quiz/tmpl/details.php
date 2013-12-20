<?php 
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT.DS.'helpers'.DS.'answers.php';
?>
<script type="text/javascript">
    function submitbutton(pressbutton) {
        document.adminForm.task.value=pressbutton;
        submitform(pressbutton);
    }
</script>
<form id="adminForm" name="adminForm" action="index.php?option=<?php echo Q_APP_NAME;?>&view=quiz" method="post">
	<div id="quiz-results">
		<?php if($this->quiz && $this->quiz->questions):?>
		<div class="page-header"><?php echo $this->escape($this->quiz->title);?></div>
		<div class="quiz-details">
			<?php echo JText::_('LBL_USERNAME');?>:&nbsp;<?php echo $this->escape($this->quiz->username);?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo JText::_('LBL_CATEGORY');?>:&nbsp;<?php echo $this->escape($this->quiz->category);?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo JText::_('LBL_CREATED');?>:&nbsp;<?php echo $this->escape($this->quiz->created);?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo JText::_('LBL_RESPONSES');?>:&nbsp;<?php echo $this->escape($this->quiz->responses);?>
		</div>
		<div class="quiz-description"><?php echo CommunityQuizHelper::process_html($this->quiz->description);?></div>
		<?php
		$i = 1;
		foreach ($this->quiz->questions as $question){
			echo '<div class="quiz-question'.($i?'':' alt').'">';
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
				case 10: // Free Text - Rich text
					echo QuizAnswerManager::rich_text($question,"qn-richtext");
					break;
			}
			echo '</div>';
			$i = 1 - $i;
		}
		?>
		<?php endif;?>
	</div>
	<input type="hidden" name="cid" value="<?php echo $this->quiz->id;?>"/>
	<input type="hidden" name="option" value="<?php echo Q_APP_NAME;?>"/>
	<input type="hidden" name="view" value="quiz"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0" />
</form>