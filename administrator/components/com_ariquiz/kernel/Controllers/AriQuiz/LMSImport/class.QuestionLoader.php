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
    AriKernel::import('Controllers.AriQuiz.LMSImport.Questions.QuestionBase');
    class AriQuizLMSImportQuestionLoader {
        function getQuestion($type) {
            static $questions = array();
            $instance = null;
            if (array_key_exists($type, $questions)) {
                $className = $questions[$type];
                return new $className();
            }
            if (!preg_match('/^[A-z]+$/', $type)) return $instance;
            $questionPath = dirname(__FILE__) .'/Questions/class.'.$type.'.php';
            if (file_exists($questionPath) && is_file($questionPath)) {
                require_once $questionPath;
                $className = 'AriQuizLMSImport'.$type;
                if (class_exists($className)) {
                    $questions[$type] = $className;
                    $instance = new $className();
                }
            }
            return $instance;
        }
    };
?>