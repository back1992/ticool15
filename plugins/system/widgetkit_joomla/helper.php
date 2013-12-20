<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');

class WidgetkitJoomlaWidgetkitHelper extends WidgetkitHelper {

	public function renderItem($item, $params) {

		$result = $item->introtext;

		if ($params->get('readmore') && (trim($item->fulltext) != '')) {

			if ($item->access <= JFactory::getUser()->get('aid', 0)) {
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
			} else {
				$link = JRoute::_('index.php?option=com_user&task=register');
			}

			$result .= '<a class="readmore" href="'.$link.'">' . JText::_('Read more...') . '</a>';
		}

		return $result;

	}

	public function getList($params) {

		$db 	= JFactory::getDBO();
		$user 	= JFactory::getUser();
		$aid	= $user->get('aid', 0);

		$items 	= (int) $params->get('items', 0);
		$order 	= $params->get('order', 'o_asc');

		$noauth = !JComponentHelper::getParams('com_content')->get('shownoauth');

		if (!$catid = $params->get('catid', 0)) {
			return array();
		}

		$nullDate = $db->getNullDate();
		jimport('joomla.utilities.date');
		$date = new JDate();
		$now = $date->toMySQL();

		// Ordering
		$direction = null;
		switch ($params->get('order')) {
			case 'random':
				$ordering = 'RAND()';
				break;
			case 'date':
				$ordering = 'a.created';
				break;
			case 'rdate':
				$ordering = 'a.created DESC';
				break;
			case 'alpha':
				$ordering = 'a.title';
				break;
			case 'ralpha':
				$ordering = 'a.title DESC';
				break;
			case 'hits':
				$ordering = 'a.hits';
				break;
			case 'rhits':
				$ordering = 'a.hits DESC';
				break;
			case 'ordering':
			default:
				$ordering = 'a.ordering';
				break;
		}

		$parts = explode(':', $catid);
		$section = array_shift($parts);
		$category = array_shift($parts);

		// Query to determine article count
		$query = 'SELECT a.*,'
			.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
			.' CASE WHEN CHAR_LENGTH(cc.name) THEN CONCAT_WS(":", cc.id, cc.name) ELSE cc.id END as catslug'
			.' FROM #__content AS a'
			.' INNER JOIN #__categories AS cc ON cc.id = a.catid'
			.' INNER JOIN #__sections AS s ON s.id = a.sectionid'
			.' WHERE a.state = 1 '
			.($noauth ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '')
			.' AND (a.publish_up = "'.$nullDate.'" OR a.publish_up <= "'.$now.'" ) '
			.' AND (a.publish_down = "'.$nullDate.'" OR a.publish_down >= "'.$now.'" )'
			.($category ? ' AND cc.id = '. (int) $category : '')
			.' AND cc.section = '.(int) $section
			.' AND cc.published = 1'
			.' AND s.published = 1'
			.' ORDER BY ' . $ordering
			.' LIMIT 0,' . (int) $items;
		$db->setQuery($query);
		return $db->loadObjectList();

	}

}
