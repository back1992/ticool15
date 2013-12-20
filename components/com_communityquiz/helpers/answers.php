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
	private static $_score = 0;
	
	public static function get_score(){
		return QuizAnswerManager::$_score;
	}
	
	function increment_score(){
		QuizAnswerManager::$_score += 1;
	}
	
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
	
	// Choice radio, checkbox, select - type 2, 3, 4
	function multiple_choice($question, $css, $explanation=true){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$correct_score = true;
		$free_text = '';
		
		foreach ($question->answers as $answer){
			$selected_answer = false;
			$correct_answer = true;
			$class = 'noanswer';
			
			if(!empty($question->responses)){
				foreach ($question->responses as $response){
					if(($response->question_id == $question->id) && ($response->answer_id == $answer->id)){
						$selected_answer = true;
						break;
					}
				}
			}
			
			if( $answer->correct_answer == '1' ){
				if($selected_answer){
					$class = 'correctanswer';
				}else{
					$correct_answer = false;
					$correct_score = false;
				}
			}else{
				if($selected_answer){
					$correct_answer = false;
					$correct_score = false;
					$class = 'wronganswer';
				}
			}
			
			$html = $html.'<div class="answer"><span class="'.$class.'">'.CommunityQuizHelper::escape($answer->title).'</span>-'.$answer->correct_answer.'-'.((int)$selected_answer);
			if( $answer->correct_answer == '1' ){
				if($correct_answer == 1){
					$html = $html . ' ( <span class="correctanswer">'.JText::_('LBL_ANSWER').'</span> )';
				}else{
					$html = $html . ' ( <span class="wronganswer">'.JText::_('LBL_ANSWER').'</span> )';
				}
			}
			$html = $html . '</div>';
					
			if(!empty($answer->free_text)){
				$free_text = $answer->free_text;
			}
		}
		if($question->include_custom == '1'){
			$html = $html . '<div class="free_text">'.CommunityQuizHelper::escape($free_text).'</div>';
		}
		if($explanation && !empty($question->answer_explanation)){
			$html = $html . '<div class="answer_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').': </div><div class="explanation">'.$question->answer_explanation.'</div></div>';
		}
		$html = $html . '</div>';

		if($correct_score){
			QuizAnswerManager::increment_score();
		}
		return $html;
	}
	
	// Grid radio, checkbox - type 5, 6
	function multiple_choice_grid($question, $css, $explanation=true){
		$rows = array();
		$columns = array();
		$correct_score = true;
		$free_text = '';
		
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
		$html = $html.'<th></th></tr></thead><tbody>';
		foreach ($rows as $row){
			$correct_answer = '';
			$is_correct = true;;
			$html = $html.'<tr><th>'.CommunityQuizHelper::escape($row->title).'</th>';
			$correct = explode(',',$row->correct_answer);
			foreach ($columns as $column){
				$class = 'noanswer';
				if(in_array($column->id, $correct)){
					$flag = false;
					if(!empty($question->responses)){
						foreach ($question->responses as $response){
							if(($response->question_id == $question->id) && ($response->answer_id == $row->id) && ($response->column_id == $column->id)){
								$flag = true;
								break;
							}
						}
					}
					if($flag){
						$class = 'correctanswer';
					}else{
						$correct_score = false;
						$is_correct = false;
					}
					$correct_answer = $column->title;
				}else{
					if(!empty($question->responses)){
						foreach ($question->responses as $response){
							if(($response->question_id == $question->id) && ($response->answer_id == $row->id) && ($response->column_id == $column->id)){
								$class = 'wronganswer';
								$correct_score = false;
								$is_correct = false;
							}
						}
					}
				}
				$html=$html.'<td><span class="'.$class.'"></span></td>';
			}
			
			if(!empty($correct_answer)){
				if($is_correct){
					$html=$html.'<td><span> ( <span class="correctanswer">'.JText::_('LBL_ANSWER').': '.CommunityQuizHelper::escape($correct_answer).'</span> )</td>';
				}else{
					$html=$html.'<td><span> ( <span class="wronganswer">'.JText::_('LBL_ANSWER').': '.CommunityQuizHelper::escape($correct_answer).'</span> )</td>';
				}
			}
			$html=$html.'</tr>';
							
			if(!empty($answer->free_text)){
				$free_text = $answer->free_text;
			}
		}
		$html = $html . '</tbody></table>';
		if($question->include_custom == '1'){
			$html = $html . '<div class="free_text">'.CommunityQuizHelper::escape($free_text).'</div>';
		}
		if($explanation && !empty($question->answer_explanation)){
			$html = $html . '<div class="answer_explanation"><div class="explanation_title">'.JText::_('LBL_ANSWER_EXPLANATION').': </div><div class="explanation">'.$question->answer_explanation.'</div></div>';
		}
		$html = $html . '</div>';
		
		if($correct_score){
			QuizAnswerManager::increment_score();
		}
		return $html;
	}
	
	// Free text single/multi line, password - type 7, 8, 9
	function free_text($question, $css){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html . '<span class="answer">'.(isset($question->responses[0]) ? CommunityQuizHelper::escape($question->responses[0]->free_text) : '' ).'</span>';
		$html = $html . '</div>';
		return $html;
	}
	
	// Free text single/multi line, password - type 7, 8, 9
	function rich_text($question, $css){
		$html = '<div class="'.$css.'">';
		$html = $html.'<div class="question_title">'.CommunityQuizHelper::escape($question->title).'</div>';
		$html = $html.'<div class="question_description">'.$question->description.'</div>';
		$html = $html . '<span class="answer">'.(isset($question->responses[0]) ? $question->responses[0]->free_text : '' ).'</span>';
		$html = $html . '</div>';
		return $html;
	}
}