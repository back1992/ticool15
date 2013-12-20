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
    class JElementGroup extends JElement {
        var $_name = 'Group';
        function fetchElement($name, $value, &$node, $control_name) {
            $parent = &$this->_parent;
            $childParameter = new JParameter($parent->_raw);
            $paths = $parent->_elementPath;
            if (is_array($paths)) foreach($paths as $path) $childParameter->addElementPath($path);
            $childParameter->setXML($node);
            $visible = $node->attributes('visible');
            $prefix = $node->attributes('prefix');
            $id = 'group_'.$prefix.'_'.$node->attributes('group_id');
            return sprintf('<div id="%s" class="el-group" style="display: %s;"><div class="el-group-header"><h4>%s</h4></div><div>%s</div></div>', $id, $visible ? 'block' : 'none', JText::_($node->attributes('label')), $childParameter->render('params'));
        }
        function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '') {
            return '';
        }
    };
?>