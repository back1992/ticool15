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
    $basePath = dirname(__FILE__) .'/';
    require_once ($basePath.'kernel/class.AriKernel.php');
    AriKernel::import('PHPCompat.CompatPHP50x');
    AriKernel::import('Joomla.JoomlaBridge');
    AriKernel::import('Web.TaskManager');
    AriKernel::import('Constants.ClassConstants');
    AriKernel::import('Constants.ConstantsManager');
    AriKernel::import('GlobalPrefs.GlobalPrefs');
    AriKernel::import('Components.AriQuiz.AriQuiz');
    AriKernel::import('Components.AriQuiz.Toolbar');
    AriKernel::import('Web.Utils.WebHelper');
    $quizComp = &AriQuizComponent::instance();
    $quizComp->init();
    $clearTask = AriTaskManager::getTask($task);
    $toolbar = new AriQuizToolbar();
    $toolbar->showToolbar($clearTask);
?>