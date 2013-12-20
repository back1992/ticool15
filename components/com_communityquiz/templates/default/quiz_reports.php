<?php 
defined('_JEXEC') or die('Restricted access');
$itemid = CommunityQuizHelper::getItemId();
?>
<script type="text/javascript">jQuery(document).ready(function($){QuizFactory.init_quiz_reports();});
</script>
<div id="quiz-reports">
	<div class="subtitle"><?php echo JText::_('LBL_REPORTS');?>:&nbsp;<?php echo $this->escape($this->quiz->title);?></div>
	<?php if(!empty($this->quiz->responselist)):?>
	<div class="navigation ui-widget-content ui-corner-all">
		<a id="btn_back" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz');?>"><?php echo JText::_('LBL_HOME');?></a>
		<a id="btn_csv_download" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=csvdownload&id='.$this->quiz->id.$itemid.'&format=raw&tmpl=component');?>" target="_blank"><?php echo JText::_('LBL_CSV_DOWNLOAD');?></a>
	</div>
	<ul class="quizlisting">
		<?php 
		foreach ($this->quiz->responselist as $i=>$response){
			$user_profile = $response->created_by ? CommunityQuizHelper::getUserProfileUrl($response->created_by, $response->username) : JText::_('LBL_GUEST');
			$created = CommunityQuizHelper::getFormattedDate($response->created);
			?>
			<li>
				<div class="quiz-response-row">
					<span class="quiz-response-row-detail"><?php echo sprintf(JText::_('TXT_QUIZ_RESPONSE_DETAIL'), $i+1, $user_profile, $created, $response->score);?></span>
					<span class="quiz-response-row-link">
						<?php echo JHtml::link(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=report_details&id='.$this->quiz->id.'&response_id='.$response->id.$itemid), JText::_('LBL_VIEW_DETAILS'));?>
					</span>
				</div>
			</li>
			<?php 
		} //end response listing
		?>
	</ul>
	<?php endif; //end check?>
	<div class="navigation ui-widget-content ui-corner-all">
		<a id="btn_back" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz');?>"><?php echo JText::_('LBL_HOME');?></a>
		<?php if(!empty($this->quiz->responselist)):?>
		<a id="btn_csv_download" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=csvdownload&id='.$this->quiz->id.$itemid.'&format=raw&tmpl=component');?>" target="_blank"><?php echo JText::_('LBL_CSV_DOWNLOAD');?></a>
		<?php endif;?>
	</div>
</div>