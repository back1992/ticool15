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
    AriKernel::import('Controllers.AriQuiz.QuizController');
    AriKernel::import('Controllers.AriQuiz.UserQuizController');
    class AriQuizStorageEntity extends AriObject {
        var $TicketId;
        var $_db;
        var $_user;
        var $_storage;
        function __construct(&$database, $ticketId, $user, $init = true) {
            $this->_db = &$database;
            $this->_user = $user;
            $this->TicketId = $ticketId;
            if ($init) $this->init();
        }
        function init($reload = false) {
            static $isLoaded;
            if (!$reload && $isLoaded) return;
            $key = $this->getKey();
            if ($reload || empty($_SESSION[$key])) {
                $storage = $this->createStorage();
                $_SESSION[$key] = &$storage;
            }
            $this->_storage = &$_SESSION[$key];
            $this->_storage->LoadedTime = time();
            $isLoaded = true;
        }
        function createStorage() {
            $userQuizController = new AriUserQuizController();
            $quiz = $userQuizController->call('getQuizByTicketId', $this->TicketId);
            $sid = @intval($userQuizController->call('getStatisticsInfoIdByTicketId', $this->TicketId, $this->_user->get('id')), 10);
            $storage = new stdClass();
            $storage->CreatedTime = time();
            $storage->LoadedTime = null;
            $storage->QuizId = $quiz->QuizId;
            $storage->CanSkip = $quiz->CanSkip;
            $storage->CanStop = $quiz->CanStop;
            $storage->UseCalculator = $quiz->UseCalculator;
            $storage->ShowCorrectAnswer = $quiz->ShowCorrectAnswer;
            $storage->ShowExplanation = $quiz->ShowExplanation;
            $storage->ParsePluginTag = $quiz->ParsePluginTag;
            $storage->IsAvailable = null;
            $storage->IsStartDateSet = false;
            $storage->StatisticsInfoId = $sid;
            return $storage;
        }
        function clear() {
            $key = $this->getKey();
            if (isset($_SESSION[$key])) {
                $this->_storage = null;
                $_SESSION[$key] = null;
                unset($_SESSION[$key]);
            }
        }
        function isQuizAvailable($checkPaused = true) {
            if (is_null($this->_storage->IsAvailable)) {
                $user = $this->_user;
                $quizController = new AriUserQuizController();
                $this->_storage->IsAvailable = $quizController->canTakeQuizByTicketId($this->TicketId, $user->get('id'), $user->get('usertype'), $checkPaused);
            }
            return $this->_storage->IsAvailable;
        }
        function get($propName) {
            return (isset($this->_storage->$propName)) ? $this->_storage->$propName : null;
        }
        function set($propName, $value) {
            $this->_storage->$propName = $value;
        }
        function getKey() {
            return 'aq_'.$this->TicketId;
        }
    };
?>