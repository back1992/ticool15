<?php
/** ARI Soft copyright
 * Copyright (C) 2008 ARI Soft.
 * All Rights Reserved.  No use, copying or distribution of this
 * work may be made except in accordance with a valid license
 * agreement from ARI Soft. This notice must be included on 
 * all copies, modifications and derivatives of this work.
 *
 * ARI Soft products are provided "as is" without warranty of 
 * any kind, either expressed or implied. In no event shall our 
 * juridical person be liable for any damages including, but 
 * not limited to, direct, indirect, special, incidental or 
 * consequential damages or other losses arising out of the use 
 * of or inability to use our products.
 *
**/

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); require_once dirname(__FILE__) . '/base/questionAddPageBase.php'; class bank_addAriPage extends AriQuestionAddPageBase { function _init() {parent::_init(); $this->_task = 'bank_add'; $this->_taskList = 'bank'; $this->_mode = AriConstantsManager::getVar('Mode.Bank', AriQuestionUiConstants::getClassName());; $this->task = $this->_task; } function _saveQuestion() { global $my;$ownerId = $my->get('id'); $quizId = 0; $questionTypeId = AriRequest::getParam('questionTypeId', ''); $fields = AriWebHelper::translateRequestValues('zQuiz'); $fields['QuestionCategoryId'] = AriRequest::getParam('BankCategoryId', null);$questionType = $this->_questionController->call('getQuestionType', $questionTypeId); $questionObj = AriEntityFactory::createInstance($questionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));$data = $questionObj->getXml(); $files = AriWebHelper::translateRequestValues('zQuizFiles'); $score = @intval(AriUtils::getParam($fields, 'Score', 0), 10); $fields['Score'] = $questionObj->getMaximumQuestionScore($score, $data);return $this->_questionController->call('saveQuestion', AriRequest::getParam('questionId', 0), $quizId, $questionTypeId, $ownerId, $fields, $data, $files); } } 

 ;

;
?>