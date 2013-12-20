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
    class JElementHeader extends JElement {
        var $_name = 'Header';
        function fetchElement($name, $value, &$node, $control_name) {
            $options = array(JText::_($value));
            foreach($node->children() as $option) {
                $options[] = $option->data();
            }
            return sprintf('<div style="font-weight: bold; font-size: 120%%; color: #FFF; background-color: #7A7A7A; padding: 2px 0; text-align: center;">%s</div>', call_user_func_array('sprintf', $options));
        }
    };
?>