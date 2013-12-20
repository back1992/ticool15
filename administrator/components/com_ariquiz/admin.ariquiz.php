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
    if (!defined('_ARI_QUIZ_INSTALL_DATE')) define('_ARI_QUIZ_INSTALL_DATE', '10 March 2010');
    $basePath = dirname(__FILE__) .'/';
    require_once ($basePath.'kernel/class.AriKernel.php');
    AriKernel::import('Joomla.JoomlaBridge');
    global $mosConfig_absolute_path, $option, $my;
    AriKernel::import('PHPCompat.CompatPHP50x');
    AriKernel::import('Constants.ClassConstants');
    AriKernel::import('Constants.ConstantsManager');
    AriKernel::import('GlobalPrefs.GlobalPrefs');
    AriKernel::import('Components.AriQuiz.AriQuiz');
    AriKernel::import('Web.Utils.WebHelper');
    AriKernel::import('Web.TaskManager');
    AriKernel::import('Web.Response');
    $managerComp = &AriQuizComponent::instance();
    $managerComp->init();
    AriWebHelper::prepareRequestValues();
    AriTaskManager::registerTaskGroup('ajax', $basePath.'ajax/');
    AriTaskManager::registerTaskGroup('', $basePath.'pages/', array(ARI_TM_KEY_TEMPLATEDIR=>$basePath.'templates/', ARI_TM_KEY_TEMPLATEEXT=>'html.php'));
    if (!AriTaskManager::doTask($task)) AriResponse::redirect('index2.php?option='.$option.'&task=quiz_list');
    AriWebHelper::restoreRequestValues();
?>