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
    defined('_JEXEC') or die ('Restricted access');
    $basePath = dirname(__FILE__) .DS.'..'.DS;
    require_once ($basePath.'kernel'.DS.'class.AriKernel.php');
    AriKernel::import('Joomla.JoomlaBridge');
    AriKernel::import('PHPCompat.CompatPHP50x');
    AriKernel::import('Constants.ClassConstants');
    AriKernel::import('Constants.ConstantsManager');
    AriKernel::import('GlobalPrefs.GlobalPrefs');
    AriKernel::import('Components.AriQuiz.AriQuiz');
    AriKernel::import('Web.Utils.WebHelper');
    AriKernel::import('Web.TaskManager');
    AriKernel::import('Web.Response');
    AriKernel::import('Controllers.AriQuiz.CategoryController');
    class JElementCategory extends JElement {
        var $_name = 'Category';
        function fetchElement($name, $value, &$node, $control_name) {
            $catController = new AriQuizCategoryController();
            $categories = $catController->getCategoryList(new AriDataFilter(array('sortField'=>'CategoryName'), false, null));
            if (!is_array($categories)) $categories = array();
            $emptyCat = new stdClass();
            $emptyCat->CategoryId = 0;
            $emptyCat->CategoryName = JText::_('UNCATEGORY');
            array_unshift($categories, $emptyCat);
            return JHTML::_('select.genericlist', $categories, $control_name.'['.$name.']', 'class="inputbox"', 'CategoryId', 'CategoryName', $value, $control_name.$name);
        }
    };
?>