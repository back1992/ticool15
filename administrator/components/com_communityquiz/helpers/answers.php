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

class QuizAnswerManager {
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
		$html = $html.'<div class="question_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').':</div><div>'.$question->answer_explanation.'</div></div>';
		$html = $html . '</div>';
		return $html;
	}
	
	// Choice radio, checkbox, select - type 2, 3, 4
	function multiple_choice($question, $css){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$selected_answer = true;
		foreach ($question->answers as $answer){
			if( $answer->correct_answer == '1' ){
				$class = 'correctanswer';
			}else{
				$class = 'noanswer';
			}
			$html = $html.'<div class="answer"><span class="'.$class.'">'.CommunityQuizHelper::escape($answer->title).'</div>';
		}
		if($question->include_custom == '1'){
			$html = $html . '<div class="free_text">'.CommunityQuizHelper::escape($answer->free_text).'</div>';
		}
		$html = $html.'<div class="question_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').':</div><div>'.$question->answer_explanation.'</div></div>';
		$html = $html . '</div>';
		return $html;
	}
	
	// Grid radio, checkbox - type 5, 6
	function multiple_choice_grid($question, $css){
		$rows = array();
		$columns = array();
		$selected_answer = true;
		
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
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
			$correct_answer = '';
			$html = $html.'<tr><th>'.CommunityQuizHelper::escape($row->title).'</th>';
			$correct = explode(',',$row->correct_answer);
			foreach ($columns as $column){
				if(in_array($column->id, $correct)){
					$class = 'correctanswer';
				}else{
					$class = 'noanswer';
				}
				$html=$html.'<td><span class="'.$class.'"></span></td>';
			}
			$html=$html.'</tr>';
		}
		$html = $html . '</tbody></table></div>';
		$html = $html.'<div class="question_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').':</div><div>'.$question->answer_explanation.'</div></div>';
		return $html;
	}
	
	// Free text single/multi line, password - type 7, 8, 9
	function free_text($question, $css){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html . '<textarea rows=5 cols=20></textarea>';
		$html = $html.'<div class="question_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').':</div><div>'.$question->answer_explanation.'</div></div>';
		$html = $html . '</div>';
		return $html;
	}
	
	// Free text single/multi line, password - type 7, 8, 9
	function rich_text($question, $css){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html.CommunityQuizHelper::load_editor('free_text'.$question->id, 'free_text'.$question->id, '', '5', '23', '100%', '200px', null, 'width: 450px;');
		$html = $html.'<div class="question_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').':</div><div>'.$question->answer_explanation.'</div></div>';
		$html = $html . '</div>';
		return $html;
	}
}