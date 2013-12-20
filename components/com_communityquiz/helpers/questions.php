<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: helper.php 2010-01-02 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Polls
 * @license GNU/GPL
 *
 * The Community quiz allows the members of the Joomla website to create and manage quiz from the front-end.
 * The administrator has the powerful tools provided in the back-end to manage the quiz published by all users.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class QuizQuestionManager {
	// Page header - type 1
	function page_header($question,$css){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		if(!empty($question->answers)){
			foreach ($question->answers as $answer){
				if(strcmp($answer->answer_type, "note") == 0){
					$html = $html.'<div class="answer">'.CommunityQuizHelper::escape($answer->title).'</div>';
				}
			}
		}
		$html = $html . '</div>';
		return $html;
	}
	
	// Choice radio - type 2
	function choice_radio($question, $css, $mandatory=false, $explanation=false, $include_custom=true){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		foreach ($question->answers as $answer){
			$html = $html.'<div class="answer">'
				. '<input id="answer'.$answer->id.'" type="radio" name="answer'.$question->id.'" value="'.$answer->id.'" '
				. (($mandatory || $question->mandatory)?'class="required"':'').'/>&nbsp;&nbsp;<label for="answer'.$answer->id.'">'
				. CommunityQuizHelper::escape($answer->title).'<label></div>';
		}
		if($question->include_custom == '1' && $include_custom){
			$html = $html . '<div><label for="free_text'.$question->id.'">'.JText::_('LBL_ENTER_YOUR_ANSWER').'</label></div><div>';
			$html = $html . '<input type="text" size="50" id="free_text'.$question->id.'" name="free_text'.$question->id.'" value=" "/>';
			$html = $html . '</div>';
		}
		if($explanation){
			$html = $html . '<div class="opt_answer_explanation"><div class="explanation_text">'.JText::_('LBL_ENTER_ANSWER_EXPLANATION').'</div>';
			$html = $html . CommunityQuizHelper::load_editor('explanation'.$question->id, 'explanation'.$question->id, $question->answer_explanation, '5', '23', '100%', '200px', null, 'width: 99%;');
			$html = $html .'</div>';
		}
		$html = $html . '</div>';
		return $html;
	}
	
	// Choice checkbox - type 3 
	function choice_checkbox($question, $css, $mandatory=false, $explanation=false, $include_custom=true){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		foreach ($question->answers as $answer){
			$html = $html.'<div class="answer"><input id="answer'.$answer->id.'" type="checkbox" name="answer'.$question->id.'[]" value="'.$answer->id.'" '
				. (($mandatory || $question->mandatory)?'class="required"':'').'/>&nbsp;&nbsp;<label for="answer'.$answer->id.'">'
				. CommunityQuizHelper::escape($answer->title).'<label></div>';
		}
		if($question->include_custom == '1' && $include_custom){
			$html = $html . '<div><label for="free_text'.$question->id.'">'.JText::_('LBL_ENTER_YOUR_ANSWER').'</label></div><div>';
			$html = $html . '<input type="text" size="50" id="free_text'.$question->id.'" name="free_text'.$question->id.'" value=" "/>';
			$html = $html . '</div>';
		}
		if($explanation){
			$html = $html . '<div class="opt_answer_explanation"><div class="explanation_text">'.JText::_('LBL_ENTER_ANSWER_EXPLANATION').'</div>';
			$html = $html . CommunityQuizHelper::load_editor('explanation'.$question->id, 'explanation'.$question->id, $question->answer_explanation, '5', '23', '100%', '200px', null, 'width: 99%;');
			$html = $html .'</div>';
		}
		$html = $html . '</div>';
		return $html;
	}
	
	// Choice select - type 4
	function choice_select($question, $css, $mandatory=false, $explanation=false, $include_custom=true){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html . '<div class="answer"><select id="answer'.$answer->id.'" name="answer'.$question->id.'" '
			.(($mandatory || $question->mandatory)?'class="required"':'').'><option value="">'.JText::_('LBL_SELECT_OPTION').'</option>';
		foreach ($question->answers as $answer){
			$html = $html.'<option value="'.$answer->id.'">'.CommunityQuizHelper::escape($answer->title).'</option>';
		}
		$html = $html . '</select></div>';
		if($question->include_custom == '1' && $include_custom){
			$html = $html . '<div><label for="free_text'.$question->id.'">'.JText::_('LBL_ENTER_YOUR_ANSWER').'</label></div><div>';
			$html = $html . '<input type="text" size="50" id="free_text'.$question->id.'" name="free_text'.$question->id.'" value=" "/>';
			$html = $html . '</div>';
		}
		if($explanation){
			$html = $html . '<div class="opt_answer_explanation"><div class="explanation_text">'.JText::_('LBL_ENTER_ANSWER_EXPLANATION').'</div>';
			$html = $html . CommunityQuizHelper::load_editor('explanation'.$question->id, 'explanation'.$question->id, $question->answer_explanation, '5', '23', '100%', '200px', null, 'width: 99%;');
			$html = $html .'</div>';
		}
		$html = $html . '</div>';
		return $html;
	}
	
	// Grid radio - type 5
	function grid_radio($question, $css, $mandatory=false, $explanation=false, $include_custom=true){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$rows = array();
		$columns = array();
		foreach ($question->answers as $answer){
			if($answer->answer_type == 'x'){
				$rows[] = $answer;
			}else if($answer->answer_type == 'y'){
				$columns[] = $answer;
			}
		}

		$html = $html.'<table class="grid"><thead><tr><th></th>';
		foreach ($columns as $column){
			$html = $html.'<th>'.CommunityQuizHelper::escape($column->title).'</th>';
		}
		$html = $html.'</tr></thead><tbody>';
		foreach ($rows as $row){
			$html = $html.'<tr><th>'.CommunityQuizHelper::escape($row->title).'</th>';
			foreach ($columns as $column){
				$html=$html.'<td><input type="radio" name="answer'.$question->id.$row->id.'" value="'.$column->id.'" '.(($mandatory || $question->mandatory)?'class="required"':'').'/></td>';
			}
			$html=$html.'</tr>';
		}
		$html = $html . '</tbody></table>';
		if($question->include_custom == '1' && $include_custom){
			$html = $html.'<div><label for="free_text'.$question->id.'">'.JText::_('LBL_ENTER_YOUR_ANSWER').'</label></div><div>';
			$html = $html.'<input type="text" size="50" id="free_text'.$question->id.'" name="free_text'.$question->id.'" value=" "/>';
			$html = $html.'</div>';
		}
		if($explanation){
			$html = $html.'<div class="opt_answer_explanation"><div class="explanation_text">'.JText::_('LBL_ENTER_ANSWER_EXPLANATION').'</div>';
			$html = $html.CommunityQuizHelper::load_editor('explanation'.$question->id, 'explanation'.$question->id, $question->answer_explanation, '5', '23', '100%', '200px', null, 'width: 99%;');
			$html = $html.'</div>';
		}
		$html = $html . '</div>';
		return $html;
	}
	
	// Grid checkbox - type 6
	function grid_checkbox($question, $css, $mandatory=false, $explanation=false, $include_custom=true){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$rows = array();
		$columns = array();
		foreach ($question->answers as $answer){
			if($answer->answer_type == 'x'){
				$rows[] = $answer;
			}else if($answer->answer_type == 'y'){
				$columns[] = $answer;
			}
		}

		$html = $html.'<table class="grid"><thead><tr><th></th>';
		foreach ($columns as $column){
			$html = $html.'<th>'.CommunityQuizHelper::escape($column->title).'</th>';
		}
		$html = $html.'</tr></thead><tbody>';
		foreach ($rows as $row){
			$html = $html.'<tr><th>'.CommunityQuizHelper::escape($row->title).'</th>';
			foreach ($columns as $column){
				$html=$html.'<td><input type="checkbox" name="answer'.$question->id.$row->id.'" value="'.$column->id.'" '. (($mandatory || $question->mandatory)?'class="required"':'').'/></td>';
			}
			$html=$html.'</tr>';
		}
		$html = $html . '</tbody></table>';
		if($question->include_custom == '1' && $include_custom){
			$html = $html.'<div><label for="free_text'.$question->id.'">'.JText::_('LBL_ENTER_YOUR_ANSWER').'</label></div><div>';
			$html = $html.'<input type="text" size="50" id="free_text'.$question->id.'" name="free_text'.$question->id.'" value=" "/>';
			$html = $html.'</div>';
		}
		if($explanation){
			$html = $html.'<div class="opt_answer_explanation"><div class="explanation_text">'.JText::_('LBL_ENTER_ANSWER_EXPLANATION').'</div>';
			$html = $html.CommunityQuizHelper::load_editor('explanation'.$question->id, 'explanation'.$question->id, $question->answer_explanation, '5', '23', '100%', '200px', null, 'width: 99%;');
			$html = $html.'</div>';
		}
		$html = $html . '</div>';
		return $html;
	}
	
	// Free text single line - type 7
	function free_text_singleline($question, $css, $mandatory=false){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html.'<input id="free_text'.$question->id.'" type="text" size="50" name="free_text'.$question->id.'" value=" " '. (($mandatory || $question->mandatory)?'class="required"':'').'/>';
		$html = $html.'</div>';
		return $html;
	}
	
	// Free text multi line - type 8
	function free_text_multiline($question, $css, $mandatory=false){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html.'<textarea id="free_text'.$question->id.'" name="free_text'.$question->id.'" rows="3" cols="50" '.(($mandatory || $question->mandatory)?'class="required"':'').'></textarea>';
		$html = $html.'</div>';
		return $html;
	}
	
	// Free text password - type 9
	function free_text_password($question, $css, $mandatory=false){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html.'<input id="free_text'.$question->id.'" type="password" name="free_text'.$question->id.'" size="50" value="" '
			. (($mandatory || $question->mandatory)?'class="required"':'').'/>';
		$html = $html.'</div>';
		return $html;
	}
	
	// Free text rich editor - type 10
	function free_text_rich_editor($question, $css, $mandatory=false){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html.CommunityQuizHelper::load_editor('free_text'.$question->id, 'free_text'.$question->id, '', '5', '23', '100%', '200px', (($mandatory || $question->mandatory)?'required':null), 'width: 99%;');
		$html = $html.'</div>';
		return $html;
	}
}