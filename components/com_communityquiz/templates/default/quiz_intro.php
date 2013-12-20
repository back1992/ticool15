<?php 
defined('_JEXEC') or die('Restricted access');
$itemid = CommunityQuizHelper::getItemId();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	QuizFactory.init_quiz_intro();
});
//-->
</script>
<div id="quiz-intro">
	<div class="page-header"><?php echo $this->escape($this->quiz->title);?></div>
	<?php if(!empty($this->quiz->description)):?>
	<div class="description ui-widget-content ui-corner-all"><?php echo CommunityQuizHelper::process_html($this->quiz->description);?></div>
	<?php endif;?>
	<div class="navigation ui-widget-content ui-corner-all"><?php echo JText::_('NOTICE_QUIZ');?></div>
	<div class="navigation ui-widget-content ui-corner-all">
		<form id="intro_form" action="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=response_form'.$itemid);?>" method="post">
			<a id="btn_back" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz');?>">
				<?php echo JText::_('LBL_HOME');?>
			</a>
			<a id="btn_continue" href="#" onclick="return false;"><?php echo JText::_('LBL_CONTINUE');?></a>
			<input type="hidden" name="id" value="<?php echo $this->quiz->id;?>">
		</form>
	</div>
</div>
