<?php
    /**
    ARI Soft copyright
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
    *
    */
    defined('ARI_FRAMEWORK_LOADED') or die ('Direct Access to this location is not allowed.');
    AriKernel::import('Web.Page.Specific.AdminSecurePageBase');
    AriKernel::import('Web.Controls.Editor');
    AriKernel::import('Web.Controls.TextBox');
    AriKernel::import('Web.Controls.CheckBox');
    AriKernel::import('Web.Controls.ListBox');
    AriKernel::import('Web.Controls.Advanced.MultiplierControls');
    AriKernel::import('Web.Controls.Validators.RequiredValidator');
    AriKernel::import('Web.Controls.Validators.RangeValidator');
    AriKernel::import('Web.Controls.Validators.CustomValidator');
    AriKernel::import('Controllers.AriQuiz.QuizController');
    AriKernel::import('Controllers.AriQuiz.QuestionBankController');
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Controllers.AriQuiz.QuestionCategoryController');
    AriKernel::import('Controllers.AriQuiz.QuestionBankCategoryController');
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Cache.FileCache');
    AriKernel::import('Text.Text');
    class AriQuestionUiConstants extends AriClassConstants {
        var $Mode = array('None'=>0, 'Bank'=>1);
        var $UiMode = array('None'=>0, 'Read'=>1);
        function getClassName() {
            return strtolower('AriQuestionUiConstants');
        }
    }
    new AriQuestionUiConstants();
    class AriQuestionAddPageBase extends AriAdminSecurePageBase {
        var $_questionTypeId;
        var $_fileController;
        var $_task = null;
        var $_taskList = null;
        var $_bankQuestionId = null;
        var $_mode;
        var $_bankController;
        var $_questionController;
        var $_questionCategoryController;
        var $_bankCategoryContoller;
        var $_applyRefQuestion = false;
        var $_quizController;
        var $_uiMode;
        var $_questionTypeList;
        var $_lbBankCategory;
        var $_lbQuestionCategory;
        var $_lbBank;
        var $_lbTemplateList;
        var $_lbQuestionType;
        var $_tbxScore;
        var $_chkOverrideScore;
        var $_edQuestion;
        var $_edQuestionNote;
        var $_acvQuestion;
        var $_arqvScore;
        var $_aravScore;
        function _init() {
            $this->_mode = AriConstantsManager::getVar('Mode.None', AriQuestionUiConstants::getClassName());
            $this->_uiMode = AriConstantsManager::getVar('UiMode.None', AriQuestionUiConstants::getClassName());
            $this->_bankCategoryContoller = new AriQuizQuestionBankCategoryController();
            $this->_fileController = new AriFileController();
            $this->_questionController = new AriQuizQuestionController();
            $this->_bankController = new AriQuizQuestionBankController();
            $this->_quizController = new AriQuizController();
            $this->_questionCategoryController = new AriQuizQuestionCategoryController();
            $this->_questionTypeList = $this->_questionController->call('getQuestionTypeList');
            $this->_bankQuestionId = AriRequest::getParam('bankQuestionId', null);
            $this->task = $this->_task;
            parent::_init();
        }
        function execute() {
            global $mosConfig_live_site, $act, $option, $task;
            $questionTypeId = AriRequest::getParam('questionTypeId', 0);
            $templateId = AriRequest::getParam('templateId', 0);
            $questionId = AriRequest::getParam('questionId', 0);
            $quizId = null;
            $question = null;
            $className = null;
            $questionData = null;
            $questionTypeList = $this->_questionTypeList;
            if ($questionId == 0) {
                $quizId = AriRequest::getParam('quizId', 0);
                $question = AriEntityFactory::createInstance('AriQuizQuestionEntity', AriGlobalPrefs::getEntityGroup());
            } else {
                $question = $this->_questionController->call('getQuestion', $questionId);
                $quizId = $question->QuizId;
                if (empty($questionTypeId)) {
                    $questionTypeId = $question->QuestionVersion->QuestionTypeId;
                }
                if ($question->Status == AriConstantsManager::getVar('Status.Delete', AriQuizQuestionControllerConstants::getClassName())) {
                    AriResponse::redirect('index2.php?option='.$option.'&task='.$this->_taskList);
                }
            }
            if (is_null($this->_bankQuestionId) && $question->BankQuestionId) {
                $this->_bankQuestionId = $question->BankQuestionId;
            }
            $question = $this->_bindQuestionFromRequest($question);
            $question = $this->_bindQuestionFromBank($question);
            $this->_uiMode = $question->BankQuestionId ? AriConstantsManager::getVar('UiMode.Read', AriQuestionUiConstants::getClassName()) : AriConstantsManager::getVar('UiMode.None', AriQuestionUiConstants::getClassName());
            if ($question->BankQuestionId) {
                $questionTypeId = $question->QuestionVersion->QuestionTypeId;
            } else if (empty($questionTypeId)) {
                $qt = AriRequest::getParam('questionTypeId', null);
                if (!is_null($qt)) $questionTypeId = $qt;
            }
            if ($templateId>0) {
                $template = $this->_questionController->call('getQuestionTemplate', $templateId);
                if (!empty($template) && $template->QuestionTypeId>0) {
                    $questionTypeId = $template->QuestionTypeId;
                    $questionData = $template->Data;
                }
            }
            if (empty($questionTypeId)) {
                $questionTypeId = $questionTypeList[0]->QuestionTypeId;
            }
            foreach($questionTypeList as $qt) {
                if ($qt->QuestionTypeId == $questionTypeId) {
                    $className = $qt->ClassName;
                    break;
                }
            }
            $quiz = $this->_quizController->call('getQuiz', $quizId, false);
            $specificQuestion = AriEntityFactory::createInstance($className, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
            if (empty($questionData)) {
                $questionData = $question->QuestionVersion->Data;
            }
            if ($className == 'HotSpotQuestion') {
                $hotspotGroup = AriConstantsManager::getVar('FileGroup.HotSpot', AriQuizComponent::getCodeName());
                $cacheImagePath = $mosConfig_live_site.'/administrator/components/'.$option.'/cache/files/'.$hotspotGroup.'/';
                $imageList = $this->_getImageList();
                $this->addVar('imageList', $imageList);
                $this->addVar('cacheImagePath', $cacheImagePath);
            }
            $this->_bindControls($question, $quizId, $questionTypeId);
            $this->addVar('quiz', $quiz);
            $this->addVar('quizId', $quizId);
            $this->addVar('question', $question);
            $this->addVar('questionId', $questionId);
            $this->addVar('className', $className);
            $this->addVar('questionData', $questionData);
            $this->addVar('questionOverrideData', $question->QuestionVersion->_OverrideData);
            $this->addVar('specificQuestion', $specificQuestion);
            $this->addVar('mode', $this->_mode);
            $this->addVar('clearTask', $this->_task);
            $this->addVar('uiMode', $this->_uiMode);
            $this->addVar('bankQuestionId', $this->_bankQuestionId);
            $this->_enableValidators($specificQuestion);
            $this->setTitle(AriWebHelper::translateResValue('Label.Question') .' : '.AriWebHelper::translateResValue($questionId ? 'Label.UpdateItem' : 'Label.AddItem'));
            parent::execute();
        }
        function getQuestionTemplatePath($questionType, $dir = 'questions') {
            $path = '';
            if (!empty($questionType) && preg_match('/^[A-z]+$/', $questionType)) {
                global $mosConfig_absolute_path;
                $path = $mosConfig_absolute_path.'/administrator/components/'.AriQuizComponent::getCodeName() .'/templates/'.$dir.'/'.strtolower($questionType) .'.html.php';
                if (!file_exists($path)) {
                    $path = '';
                }
            }
            return $path;
        }
        function _bindQuestionFromBank($question) {
            if (empty($question) || $this->_mode == AriConstantsManager::getVar('Mode.Bank', AriQuestionUiConstants::getClassName()) || !$this->_applyRefQuestion || empty($this->_bankQuestionId)) {
                if (empty($this->_bankQuestionId) && !is_null($this->_bankQuestionId)) $question->BankQuestionId = 0;
                return $question;
            }
            $refQuestion = null;
            if ($this->_bankQuestionId) $refQuestion = $this->_getRefQuestion($this->_bankQuestionId);
            if (!$refQuestion) return $question;
            $questionVersion = &$question->QuestionVersion;
            $refQuestionVersion = &$refQuestion->QuestionVersion;
            $question->BankQuestionId = $refQuestion->QuestionId;
            $questionVersion->Question = $refQuestionVersion->Question;
            $questionVersion->QuestionTypeId = $refQuestionVersion->QuestionTypeId;
            $questionVersion->QuestionType = $refQuestionVersion->QuestionType;
            $questionVersion->Data = $refQuestionVersion->Data;
            $questionVersion->OverrideScore = false;
            $questionVersion->Score = $refQuestionVersion->Score;
            $questionVersion->BankScore = $refQuestionVersion->Score;
            return $question;
        }
        function _bindQuestionFromRequest($question) {
            if (empty($question)) return $question;
            $zQuiz = AriWebHelper::translateRequestValues('zQuiz');
            if (empty($zQuiz)) return $question;
            $val = AriUtils::getParam($zQuiz, 'QuestionCategoryId', null);
            if (!is_null($val)) $question->QuestionVersion->QuestionCategoryId = $val;
            $val = AriUtils::getParam($zQuiz, 'Score', null);
            if (!is_null($val)) $question->QuestionVersion->Score = $val;
            $val = isset($zQuiz['Question']) ? $zQuiz['Question'] : null;
            if (!is_null($val)) $question->QuestionVersion->Question = $val;
            $question->BankQuestionId = $this->_bankQuestionId;
            return $question;
        }
        function _getBankList() {
            $bankList = $this->_bankController->call('getQuestionList');
            if (!empty($bankList)) {
                foreach($bankList as $key=>$question) {
                    $text = strip_tags(AriWebHelper::translateDbValue($question->Question, false));
                    if (ArisText::html_strlen($text) >50) {
                        $text = ArisText::html_substr($text, 0, 50) .'...';
                    }
                    $bankList[$key]->Question = $text;
                }
            }
            return $bankList;
        }
        function _getImageList() {
            global $mosConfig_absolute_path, $option;
            $hotspotGroup = AriConstantsManager::getVar('FileGroup.HotSpot', AriQuizComponent::getCodeName());
            $tempImageList = $this->_fileController->getFileList($hotspotGroup);
            $imageList = array();
            $this->_checkImageCache($tempImageList);
            if (!empty($tempImageList)) {
                $hotspotGroup = AriConstantsManager::getVar('FileGroup.HotSpot', AriQuizComponent::getCodeName());
                $cacheDir = $mosConfig_absolute_path.'/administrator/components/'.$option.'/cache/files/'.$hotspotGroup.'/';
                foreach($tempImageList as $key=>$image) {
                    $realFileName = $image->FileId.'.'.$image->Extension;
                    $imageInfo = @getimagesize($cacheDir.$realFileName);
                    if (!empty($imageInfo) && $imageInfo[0]>0) {
                        $imageList[$key] = $tempImageList[$key];
                        $imageList[$key]->RealFileName = $realFileName;
                        $imageList[$key]->FileName = AriWebHelper::translateDbValue($image->FileName);
                        $imageList[$key]->Width = $imageInfo[0];
                        $imageList[$key]->Height = $imageInfo[1];
                    }
                }
            }
            $emptyImage = new stdClass();
            $emptyImage->FileId = 0;
            $emptyImage->FileName = AriWebHelper::translateResValue('Label.NotSelectedItem');
            array_unshift($imageList, $emptyImage);
            return $imageList;
        }
        function _checkImageCache($imageList) {
            global $mosConfig_absolute_path, $option;
            if (!empty($imageList)) {
                $cacheId = array();
                $hotspotGroup = AriConstantsManager::getVar('FileGroup.HotSpot', AriQuizComponent::getCodeName());
                foreach($imageList as $image) {
                    $fileName = $mosConfig_absolute_path.'/administrator/components/'.$option.'/cache/files/'.$hotspotGroup.'/'.$image->FileId.'.'.$image->Extension;
                    if (!file_exists($fileName)) {
                        $cacheId[$image->FileId] = $fileName;
                    }
                }
                if (count($cacheId) >0) {
                    $cacheImageList = $this->_fileController->call('getFileList', $hotspotGroup, array_keys($cacheId), true);
                    if (!empty($cacheImageList)) {
                        foreach($cacheImageList as $cacheImage) {
                            $fileName = $cacheId[$cacheImage->FileId];
                            AriFileCache::saveBinaryFile($cacheImage->Content, $fileName);
                        }
                    }
                }
            }
        }
        function _getCategoryList($quizId) {
            $categoryList = $this->_questionCategoryController->call('getQuestionCategoryList', $quizId);
            return $categoryList;
        }
        function _getBankCategoryList($addUncategory = false) {
            $categoryList = $this->_bankCategoryContoller->call('getCategoryList');
            if ($addUncategory) {
                if (empty($categoryList)) $categoryList = array();
                $uncategory = new stdClass();
                $uncategory->CategoryId = 0;
                $uncategory->CategoryName = AriWebHelper::translateResValue('Category.Uncategory');
                array_unshift($categoryList, $uncategory);
            }
            return $categoryList;
        }
        function _getTemplateList() {
            $templateList = $this->_questionController->call('getQuestionTemplateList', null);
            return $templateList;
        }
        function _registerEventHandlers() {
            $this->_registerEventHandler('save', 'clickSave');
            $this->_registerEventHandler('apply', 'clickApply');
            $this->_registerEventHandler('cancel', 'clickCancel');
            $this->_registerEventHandler('apply_qtype', 'clickApplyType');
            $this->_registerEventHandler('applyRefQuestion', 'clickApplyRefQuestion');
            $this->_registerEventHandler('clearRefQuestion', 'clickClearRefQuestion');
            $this->_registerEventHandler('addHotSpotImg', 'clickAddHotSpotImage');
        }
        function clickAddHotSpotImage($eventArgs) {
            global $my;
            $file = AriUtils::getParam($_FILES, 'fileHotSpotImage', null);
            if (!empty($file) && $file['size']>0) {
                $userId = $my->get('id');
                $fileName = basename($file['name']);
                if (!get_magic_quotes_gpc()) {
                    $fileName = addslashes($fileName);
                }
                $hotspotGroup = AriConstantsManager::getVar('FileGroup.HotSpot', AriQuizComponent::getCodeName());
                $fields = array('Group'=>$hotspotGroup, 'FileName'=>AriWebHelper::translateValue($fileName));
                $this->_fileController->call('saveFileFromFile', 0, $fields, $file['tmp_name'], $userId);
            }
            $this->reload = true;
        }
        function clickApplyRefQuestion() {
            $bankQuestionId = AriRequest::getParam('ddlBank', null);
            $this->_bankQuestionId = $bankQuestionId;
            $this->_applyRefQuestion = true;
        }
        function clickClearRefQuestion() {
            $_REQUEST['ddlBank'] = 0;
            $this->_bankQuestionId = 0;
        }
        function clickApplyType() {
            $this->_questionTypeId = AriRequest::getParam('questionTypeId', '');
        }
        function clickCancel($eventArgs) {
            $questionId = AriRequest::getParam('questionId', 0);
            $quizId = 0;
            if ($questionId == 0) {
                $quizId = AriRequest::getParam('quizId', 0);
            } else {
                $question = $this->_questionController->call('getQuestion', $questionId);
                if ($question) $quizId = $question->QuizId;
            }
            AriWebHelper::cancelAction($this->_taskList, array('quizId'=>$quizId));
        }
        function clickSave($eventArgs) {
            $question = $this->_saveQuestion();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.QuestionSave', array('task'=>$this->_taskList, 'quizId'=>$question->QuizId));
            }
        }
        function clickApply($eventArgs) {
            $question = $this->_saveQuestion();
            if (!$this->_isError()) {
                AriWebHelper::preCompleteAction('Complete.QuestionSave', array('task'=>$this->_task, 'questionId'=>$question->QuestionId, 'hidemainmenu'=>1, 'quizId'=>$question->QuizId));
            }
        }
        function _saveQuestion() {
        }
        function _getRefQuestion($id) {
            $question = null;
            $id = intval($id, 10);
            if ($id) {
                $question = $this->_questionController->call('getQuestion', $id, true, false);
                if ($question->QuizId || $question->Status == AriConstantsManager::getVar('Status.Delete', AriQuizQuestionControllerConstants::getClassName())) $question = null;
            }
            return $question;
        }
        function _createControls() {
            $this->_lbQuestionCategory = &new AriListBoxWebControl('lbQuestionCategories', array('name'=>'zQuiz[QuestionCategoryId]'));
            $this->_lbBankCategory = &new AriListBoxWebControl('lbBankCategories', array('name'=>'BankCategoryId'));
            $this->_lbBank = &new AriListBoxWebControl('lbBank', array('name'=>'ddlBank', 'translateText'=>false));
            $this->_lbTemplateList = &new AriListBoxWebControl('lbTemplateList', array('name'=>'templateId'));
            $this->_lbQuestionType = &new AriListBoxWebControl('lbQuestionType', array('name'=>'questionTypeId'));
            $this->_tbxScore = &new AriTextBoxWebControl('tbxScore', array('name'=>'zQuiz[Score]', 'maxLength'=>85));
            $this->_chkOverrideScore = &new AriCheckBoxWebControl('chkOverrideScore', array('name'=>'chkOverrideScore'));
            $this->_edQuestion = &new AriEditorWebControl('edQuestion', array('name'=>'zQuiz[Question]'));
            $this->_edQuestionNote = &new AriEditorWebControl('edQuestionNote', array('name'=>'zQuiz[Note]'));
        }
        function _createValidators() {
            $this->_arqvScore = &new AriRequiredValidatorWebControl('arqvScore', array('controlToValidate'=>'tbxScore', 'errorMessageResourceKey'=>'Validator.QuestionScoreRequired'));
            $this->_aravScore = &new AriRangeValidatorWebControl('aravScore', array('controlToValidate'=>'tbxScore', 'minValue'=>0, 'errorMessageResourceKey'=>'Validator.QuestionScore'));
            $validate = array(&$this, 'cvQuizName');
            $this->_acvQuestion = &new AriCustomValidatorWebControl('acvQuestion', $validate, array('controlToValidate'=>'edQuestion', 'clientValidateFunc'=>'YAHOO.ARISoft.page.questionValidate', 'errorMessageResourceKey'=>'Validator.QuestionRequired'));
        }
        function _enableValidators($specificQuestion) {
            $isScoreSpecific = $specificQuestion->isScoreSpecific();
            $this->_arqvScore->setEnabled(!$isScoreSpecific);
            $this->_aravScore->setEnabled(!$isScoreSpecific);
        }
        function cvQuestion() {
            return true;
        }
        function _bindControls($question, $quizId, $questionTypeId) {
            $isRead = $this->_uiMode == AriConstantsManager::getVar('UiMode.Read', AriQuestionUiConstants::getClassName());
            if ($this->_mode == AriConstantsManager::getVar('Mode.None', AriQuestionUiConstants::getClassName())) {
                $this->_lbQuestionCategory->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
                $this->_lbQuestionCategory->dataBind($this->_getCategoryList($quizId), 'CategoryName', 'QuestionCategoryId');
                $this->_lbQuestionCategory->setSelectedValue($question != null ? $question->QuestionVersion->QuestionCategoryId : null);
                $this->_lbBankCategory->setEmptyRow(AriWebHelper::translateResValue('Label.AllCategory'), -1);
                $this->_lbBankCategory->dataBind($this->_getBankCategoryList(true), 'CategoryName', 'CategoryId');
                $this->_lbBankCategory->setSelectedValue($question != null ? $question->QuestionVersion->_BankCategoryId : 0);
            } else {
                $this->_lbBankCategory->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
                $this->_lbBankCategory->dataBind($this->_getBankCategoryList(), 'CategoryName', 'CategoryId');
                $this->_lbBankCategory->setSelectedValue($question != null ? $question->QuestionVersion->QuestionCategoryId : null);
            }
            $this->_lbTemplateList->setEmptyRow(AriWebHelper::translateResValue('Label.NotSelectedItem'), 0);
            $this->_lbTemplateList->dataBind($this->_getTemplateList(), 'TemplateName', 'TemplateId');
            $this->_lbTemplateList->setSelectedValue(0);
            $this->_lbQuestionType->dataBind($this->_questionTypeList, 'QuestionType', 'QuestionTypeId');
            $this->_lbQuestionType->setSelectedValue($questionTypeId);
            if ($isRead) $this->_lbQuestionType->addAttribute('disabled', 'disabled');
            $this->_tbxScore->setText($question->QuestionVersion->Score);
            $this->_tbxScore->addAttribute('_initValue', $question->QuestionVersion->BankScore);
            if ($isRead && !$question->QuestionVersion->OverrideScore) $this->_tbxScore->addAttribute('disabled', 'disabled');
            $this->_chkOverrideScore->setChecked($question->QuestionVersion->OverrideScore);
            $this->_edQuestion->setText(AriWebHelper::translateDbValue($question->QuestionVersion->Question));
            $this->_edQuestionNote->setText(AriWebHelper::translateDbValue($question->QuestionVersion->Note));
            $this->_acvQuestion->setEnabled(!$isRead);
        }
    };
?>