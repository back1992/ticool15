<?php 
defined('_JEXEC') or die('Restricted access');
$menu = &JSite::getMenu();
$mnuitem = $menu->getItems('link', 'index.php?option='.Q_APP_NAME.'&view=quiz', true);
$itemid = isset($mnuitem) ? '&Itemid='.$mnuitem->id : '';
?>
<script type="text/javascript">

jQuery(document).ready(function(){
	QuizFactory.init_quiz_form();
    //jQuery("#quiz-content > ul:first").sortable();
});
</script>
<div class="main-wrapper">
	<div class="quiz-wrapper" id="quiz-wrapper">
		<div id="toolbox-wrapper">
			<div class="quiz-toolbox ui-widget-content ui-corner-all" id="quiz-toolbox">
				<ul>
					<li id="pageheader"><?php echo JText::_('LBL_PAGE_HEADER');?></li>
					<li id="choice"><?php echo JText::_('LBL_MULTIPLE_CHOICE');?></li>
					<li id="grid"><?php echo JText::_('LBL_GRID');?></li>
					<li id="textbox" class="last"><?php echo JText::_('LBL_FREE_TEXT');?></li>
				</ul>
				<?php if(JDocumentHTML::countModules('quiz_create_below_toolbox')) :?>
				<div><?php echo SurveyHelper::loadModulePosition('quiz_create_below_toolbox');?></div>
				<?php endif; ?>
			</div>
		</div>
		<div id="quiz-content-wrapper">
			<div class="quiz-content" id="quiz-content">
				<div class="cs-pagination">
					<?php
					$i=1;
					foreach ($this->quiz->pages as $page){
					?>
					<input type="radio" id="page_<?php echo $page;?>" name="current_page" <?php echo ($i == 1)?'checked="checked"':'';?> value="<?php echo $page?>"><label for="page_<?php echo $page;?>"><?php echo (($i == 1)?JText::_('LBL_PAGE').' ':'').$i;?></label>
					<?php
					$i++;
					}
					?>
					<input type="radio" id="new_page" name="new_page"><label for="new_page"><?php echo JText::_("LBL_NEW")?></label>
					<input type="radio" id="remove_page" name="remove_page"><label for="remove_page"><?php echo JText::_("LBL_REMOVE")?></label>
					<input type="radio" id="finish_quiz" name="finish_quiz"><label for="finish_quiz"><?php echo JText::_("LBL_FINISH")?></label>
				</div>
		    	<div class="ui-widget" id="msg_drag_drop" style="display: none;">
	        		<div class="ui-state-highlight ui-corner-all" style="margin-bottom: 10px; padding: 0 .7em;">
		    	        <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><strong><?php echo JText::_('TXT_HELP_DRAG_QN');?></strong></p>
	    		    </div>
		    	</div>
				<ul class="content"></ul>
			</div>
		</div>
	</div>
</div>
<span id="txt_option" style="display: none;"><?php echo Q_APP_NAME;?></span>
<span id="id" style="display: none;"><?php echo $this->quiz->id;?></span>
<span id="pid" style="display: none;"><?php echo $this->quiz->pages[0];?></span>
<span id="sort_icons" style="display: none"><a class="down" href="#" onclick="return false;"></a><a class="up" href="#"	onclick="return false;"></a></span>
<span id="drag_handle" style="display: none"><img alt="<?php echo JText::_('LBL_DRAG_SORT');?>" title="<?php echo JText::_('LBL_DRAG_SORT');?>"	src="<?php echo $templateUrlPath;?>/images/arrow_out.png" style="cursor: move;"></span>
<span id="url_home" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=home".$itemid);?></span>
<span id="url_saveqn" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=saveqn");?></span>
<span id="url_loadqn" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=loadqn");?></span>
<span id="url_newpage" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=newpage");?></span>
<span id="url_removepage" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=removepage");?></span>
<span id="url_finish" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=finish&id=".$this->quiz->id.$itemid);?></span>
<span id="url_delete_qn" style="display: none"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=delete_qn".$itemid);?></span>
<span id="url_move_up" style="display: none"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=move_up".$itemid);?></span>
<span id="url_move_down" style="display: none"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=move_down".$itemid);?></span>
<span id="img_delete_qn" style="display: none"><img src="<?php echo $templateUrlPath;?>/images/bullet_red.png"></span>
<span id="img_move_up" style="display: none"><img src="<?php echo $templateUrlPath;?>/images/bullet_arrow_up.png"></span>
<span id="img_move_down" style="display: none"><img src="<?php echo $templateUrlPath;?>/images/bullet_arrow_down.png"></span>
<span id="txt_page_header_title" style="display: none"><?php echo JText::_('TXT_PAGE_HEADER_TITLE');?>:</span>
<span id="txt_enter_question_title" style="display: none"><?php echo JText::_('TXT_ENTER_QUESTION_TITLE');?>:</span>
<span id="txt_enter_options" style="display: none">* <?php echo JText::_('TXT_ENTER_OPTIONS');?></span>
<span id="txt_include_note" style="display: none"><?php echo JText::_('LBL_ATTACH_NOTE');?></span>
<span id="txt_include_note_desc" style="display: none"><?php echo JText::_('LBL_ATTACH_NOTE_DESC');?></span>
<span id="txt_questions" style="display: none"><?php echo JText::_('LBL_QUESTIONS');?></span>
<span id="txt_columns" style="display: none"><?php echo JText::_('LBL_COLUMNS');?></span>
<span id="txt_mandatory" style="display: none"><?php echo JText::_('LBL_MANDATORY');?></span>
<span id="txt_radio" style="display: none"><?php echo JText::_('LBL_RADIO');?></span>
<span id="txt_checkbox" style="display: none"><?php echo JText::_('LBL_CHECKBOX');?></span>
<span id="txt_select" style="display: none"><?php echo JText::_('LBL_SELECT');?></span>
<span id="txt_single_line" style="display: none"><?php echo JText::_('LBL_SINGLE_LINE');?></span>
<span id="txt_multi_line" style="display: none"><?php echo JText::_('LBL_MULTILINE');?></span>
<span id="txt_rich_text" style="display: none"><?php echo JText::_('LBL_RICHTEXT');?></span>
<span id="txt_password" style="display: none"><?php echo JText::_('LBL_PASSWORD');?></span>
<span id="txt_error" style="display: none"><?php echo JText::_('TXT_ERROR');?></span>
<span id="txt_custom_choice" style="display: none"><?php echo JText::_('TXT_CUSTOM_CHOICE');?></span>
<span id="txt_loading_wait" style="display: none">&nbsp;<?php echo JText::_('TXT_LOADING_WAIT');?></span>
<span id="txt_enter_description" style="display: none"><?php echo JText::_('TXT_ENTER_DESCRIPTION');?>:</span>
<span id="lbl_select_option" style="display: none;"><?php echo JText::_("LBL_SELECT_OPTION");?></span>
<span id="lbl_ok" style="display: none"><?php echo JText::_('LBL_OK');?></span>
<span id="lbl_add_option" style="display: none;"><?php echo JText::_("LBL_ADD_OPTION");?></span>
<span id="lbl_add_row" style="display: none;"><?php echo JText::_("LBL_ADD_ROW");?></span>
<span id="lbl_add_column" style="display: none;"><?php echo JText::_("LBL_ADD_COLUMN");?></span>
<span id="lbl_include_note" style="display: none;"><?php echo JText::_('LBL_INCLUDE_NOTE');?></span>
<span id="lbl_save" style="display: none"><?php echo JText::_('LBL_SAVE');?></span>
<span id="lbl_cancel" style="display: none"><?php echo JText::_('LBL_CANCEL');?></span>
<span id="lbl_element_title_choice" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_CHOICE');?></span>
<span id="lbl_element_title_grid" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_GRID');?></span>
<span id="lbl_element_title_free_text" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_FREE_TEXT');?></span>
<span id="lbl_element_title_datetime" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_DATE_TIME');?></span>
<span id="lbl_element_title_email" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_EMAIL');?></span>
<span id="lbl_element_title_page_header" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_PAGE_HEADER');?></span>
<span id="lbl_element_title_multiple_choice" style="display: none"><?php echo JText::_('LBL_ELEMENT_TITLE_MULTIPLE_CHOICE');?></span>
<span id="lbl_delete_qn" style="display: none"><?php echo JText::_('LBL_DELETE_QUESTION');?></span>
<span id="lbl_move_up" style="display: none"><?php echo JText::_('LBL_MOVE_UP');?></span>
<span id="lbl_move_down" style="display: none"><?php echo JText::_('LBL_MOVE_DOWN');?></span>
<span id="lbl_processing" style="display: none"><?php echo JText::_('LBL_PROCESSING');?></span>
<span id="info_empty_choice_ignore" style="display: none">* <?php echo JText::_('INFO_EMPTY_CHOICE');?></span>
<span id="msg_error_processing" style="display: none" title="<?php echo JText::_('TXT_ERROR');?>"><?php echo JText::_('MSG_ERROR_PROCESSING');?></span>
<span id="msg_page_header_exists" style="display: none" title="<?php echo JText::_('TXT_ERROR');?>"><?php echo JText::_('MSG_PAGE_HEADER_EXISTS');?></span>
<span id="msg_unsaved_questions" style="display: none" title="<?php echo JText::_('MSG_ALERT');?>"><?php echo JText::_('MSG_PAGE_UNSAVED');?></span>
<span id="msg_remove_current_page" style="display: none" title="<?php echo JText::_('MSG_ALERT');?>"><?php echo JText::_('MSG_REMOVE_CURRENT_PAGE');?></span>
<span id="msg_remove_first_page" style="display: none" title="<?php echo JText::_('MSG_ALERT');?>"><?php echo JText::_('MSG_REMOVE_FIRST_PAGE');?></span>
<span id="msg_please_wait" style="display: none"><?php echo JText::_('MSG_PROCESSING_WAIT');?></span>
<span id="msg_save_questions" style="display: none" title="<?php echo JText::_('MSG_ALERT');?>"><?php echo JText::_('MSG_SAVE_QUESTIONS');?></span>
<span id="msg_confirm" style="display: none" title="<?php echo JText::_('MSG_ALERT');?>"><?php echo JText::_('MSG_CONFIRM');?></span>
<img id="progress-confirm" alt="..." src="<?php echo $templateUrlPath;?>/images/ui-anim_basic_16x16.gif" style="display: none;"/>
<span id="rich_editor" style="display: none"><?php echo CommunityQuizHelper::load_editor('question_description', 'question_description', '', '5', '23', '100%', '200px', null, 'width: 99%;'); ?></span>