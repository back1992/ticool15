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
    AriKernel::import('Web.Page.PageBase');
    AriKernel::import('Components.AriQuiz.Util');
    AriKernel::import('Controllers.AriQuiz.UserQuizController');
    AriKernel::import('Event.EventController');
    class resume_quizAriPage extends AriPageBase {
        var $_userQuizController;
        function _init() {
            $this->_userQuizController = new AriUserQuizController();
            parent::_init();
        }
        function execute() {
            global $option, $my, $Itemid;
            $userId = $my->get('id');
            $quizId = AriRequest::getParam('quizId', 0);
            $errorCodeList = AriConstantsManager::getVar('ErrorCode.TakeQuiz', AriUserQuizControllerConstants::getClassName());
            $errorCode = $this->_userQuizController->canTakeQuiz2($quizId, $userId, $my->get('usertype'), true);
            if ($errorCode == $errorCodeList['None']) {
                AriResponse::redirect(AriJoomlaBridge::getLink('index.php?option='.$option.'&task=quiz&quizId='.$quizId.'&Itemid='.$Itemid, false, false));
            } else if ($errorCode == $errorCodeList['HasPausedQuiz']) {
                AriEventController::raiseEvent('onBeforeResumeQuiz', array('QuizId'=>$quizId, 'UserId'=>$userId));
                $ticketId = $this->_userQuizController->resumeQuiz($quizId, $userId);
                if (!empty($ticketId)) {
                    AriEventController::raiseEvent('onResumeQuiz', array('QuizId'=>$quizId, 'UserId'=>$userId, 'TicketId'=>$ticketId));
                    AriResponse::redirect(AriJoomlaBridge::getLink('index.php?option='.$option.'&task=question&ticketId='.$ticketId.'&Itemid='.$Itemid, false, false));
                }
            }
            AriQuizUtils::redirectToInfo('FrontEnd.QuizNotAvailable');
        }
    };
?>