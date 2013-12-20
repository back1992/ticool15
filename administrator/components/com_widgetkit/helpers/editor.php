<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

jimport('joomla.html.editor');

/*
	Class: EditorWidgetkitHelper
		Editor helper class, to integrate the Joomla Editor Plugins.
*/
class EditorWidgetkitHelper extends WidgetkitHelper {

	/*
		Function: init
			Init System Editor
	*/
	public function init() {
		
		if (is_a($this['system']->document, 'JDocumentRAW')) {
			return;
		}
		
		$editor = JFactory::getConfig()->getValue('config.editor');
		
		if (in_array(strtolower($editor), array('tinymce', 'jce'))) {
			JEditor::getInstance($editor)->_loadEditor();
		}
	}
	
}