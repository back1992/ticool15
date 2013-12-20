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
    AriKernel::import('Controllers.AriQuiz.CategoryControllerBase');
    AriKernel::import('Controllers.AriQuiz.QuestionBankController');
    class AriQuizQuestionBankCategoryController extends AriQuizCategoryControllerBase {
        var $_tableName = '#__ariquizbankcategory';
        var $_entityName = 'AriQuizBankCategoryEntity';
        function deleteCategory($idList, $deleteQuestions = false) {
            $idList = $this->_fixIdList($idList);
            if (empty($idList)) return true;
            $result = parent::deleteCategory($idList);
            global $database;
            $catStr = join(',', $this->_quoteValues($idList));
            if ($deleteQuestions) {
                $query = sprintf('SELECT QQ.QuestionId'.' FROM #__ariquizquestion QQ'.' WHERE QQ.QuizId = 0 AND QQ.QuestionCategoryId IN (%s)', $catStr);
                $database->setQuery($query);
                $queIdList = $database->loadResultArray();
                if ($database->getErrorNum()) {
                    return false;
                }
                $bankController = new AriQuizQuestionBankController();
                $bankController->call('deleteQuestion', $queIdList);
                if ($bankController->_isError(true, false)) {
                    return false;
                }
            }
            $query = sprintf('UPDATE #__ariquizquestion QQ, #__ariquizquestionversion QQV'.' SET QQ.QuestionCategoryId = 0, QQV.QuestionCategoryId = 0'.' WHERE QQ.QuestionVersionId = QQV.QuestionVersionId AND QQ.QuizId = 0 AND QQ.QuestionCategoryId IN (%s)', $catStr);
            $database->setQuery($query);
            $database->query();
            if ($database->getErrorNum()) {
                return false;
            }
            return true;
        }
    };
?>