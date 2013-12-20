<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$db = JFactory::getDBO();

$query = '(SELECT CONCAT_WS( ":", s.id, c.id ) as value, CONCAT("- ", c.title) AS text, CONCAT_WS("", s.title, c.title) AS ordering'
	.' FROM #__sections AS s'
	.' JOIN #__categories AS c ON s.id=c.section'
	.' WHERE c.published = 1'
	.' AND s.scope = '.$db->Quote('content')
	.') UNION (SELECT s.id AS value, s.title AS text, s.title AS ordering'
	.' FROM #__sections AS s'
	.' WHERE s.scope = '.$db->Quote('content')
	.') ORDER BY ordering';

$db->setQuery($query);
$options = $db->loadObjectList();

printf('<select %s>', $this['field']->attributes(compact('name')));

foreach ($options as $option) {

	// set attributes
	$attributes = array('value' => $option->value);

	// is checked ?
	if ($option->value == $value) {
		$attributes = array_merge($attributes, array('selected' => 'selected'));
	}

	printf('<option %s>%s</option>', $this['field']->attributes($attributes), $option->text);
}

printf('</select>');