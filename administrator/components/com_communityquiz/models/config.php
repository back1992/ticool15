<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: communityquiz.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class CommunityQuizModelConfig extends JModel {
    function __construct() {
		parent::__construct();
    }
    
    function getConfiguration(){
		$query = 'SELECT config_name, config_value FROM '. T_QUIZ_CONFIG;
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		return $rows;
    }

	function save() {
		$db =& JFactory::getDBO();
		$default_template			= JRequest::getVar(CQ_DEFAULT_TEMPLATE, 'default', 'post','CMD');
		$default_editor				= JRequest::getVar(CQ_DEFAULT_EDITOR, 'default', 'post','CMD');
		$list_length                = JRequest::getVar(CQ_LIST_LIMIT, 20, 'post','INT');
		$enable_moderation			= JRequest::getVar(CQ_ENABLE_MODERATION, 0, 'post', 'INT');
		$enable_ratings				= JRequest::getVar(CQ_ENABLE_RATINGS, '0', 'post','INT');
		$user_name                  = JRequest::getVar(CQ_USER_NAME, 'username', 'post','STRING');
		$filtered_words				= JRequest::getVar(CQ_FILTERED_KEYWORDS, '', 'post','STRING');
		$user_avtar                 = JRequest::getVar(CQ_USER_AVTAR, 'none', 'post','CMD');
		$show_avatar_in_listing		= JRequest::getVar(CQ_SHOW_AVATAR_IN_LISTING, 'none', 'post','INT');
		$avatar_size				= JRequest::getVar(CQ_AVATAR_SIZE, '42', 'post','INT');
		$toolbar_buttons			= JRequest::getVar(CQ_TOOLBAR_BUTTONS, 'L,P,T', 'post','STRING');
		$hide_template				= JRequest::getVar(CQ_HIDE_TEMPLATE, '0', 'post','INT');
		$enable_category_box		= JRequest::getVar(CQ_ENABLE_CATEGORY_BOX, '1', 'post','INT');
		$clean_home_page			= JRequest::getVar(CQ_CLEAN_HOME_PAGE, '0', 'post','INT');
		$enable_credits             = JRequest::getVar(CQ_ENABLE_POWERED_BY, '1', 'post','INT');
		$notif_sender_name          = JRequest::getVar(CQ_NOTIF_SENDER_NAME, '', 'post','STRING');
		$notif_sender_email         = JRequest::getVar(CQ_NOTIF_SENDER_EMAIL, '', 'post','STRING');
		$notif_admin_email			= JRequest::getVar(CQ_NOTIF_ADMIN_EMAIL, '', 'post','STRING');
		$notif_new_quiz				= JRequest::getVar(CQ_NOTIF_NEW_QUIZ, '1', 'post','INT');
		$notif_new_response			= JRequest::getVar(CQ_NOTIF_NEW_RESPONSE, '1', 'post','INT');
		$activity_stream_type       = JRequest::getVar(CQ_ACTIVITY_STREAM_TYPE, 'none', 'post','CMD');
		$stream_new_quiz			= JRequest::getVar(CQ_STREAM_NEW_QUIZ, '1', 'post','INT');
		$stream_new_response		= JRequest::getVar(CQ_STREAM_NEW_RESPONSE, '1', 'post','INT');
		$points_system              = JRequest::getVar(CQ_POINTS_SYSTEM, 'none', 'post','CMD');
		$points_new_quiz			= JRequest::getVar(CQ_TOUCH_POINTS_NEW_QUIZ, '0', 'post','INT');
		$points_new_response		= JRequest::getVar(CQ_TOUCH_POINTS_NEW_RESPONSE, '0', 'post','INT');
		$perm_guest_browsing        = JRequest::getVar(CQ_PERM_GUEST_BROWSE, '0', 'post','INT');
		$perm_guest_response		= JRequest::getVar(CQ_PERM_GUEST_RESPONSE, '0', 'post','INT');
		$perm_component_access		= JRequest::getVar(CQ_PERM_COMPONENT_ACCESS, array(), 'post','ARRAY');
		$perm_create_quiz			= JRequest::getVar(CQ_PERM_CREATE_QUIZ, array(), 'post','ARRAY');
		$perm_edit_quiz				= JRequest::getVar(CQ_PERM_EDIT_QUIZ, array(), 'post','ARRAY');
		$perm_submit_response		= JRequest::getVar(CQ_PERM_SUBMIT_ANSWER, array(), 'post','ARRAY');
		$perm_wysiwyg               = JRequest::getVar(CQ_PERM_WYSIWYG, array(), 'post','ARRAY');
		$perm_manage				= JRequest::getVar(CQ_PERM_MANAGE, array(), 'post','ARRAY');
		
		JArrayHelper::toInteger( $perm_component_access );
		JArrayHelper::toInteger( $perm_create_quiz );
		JArrayHelper::toInteger( $perm_submit_response );
		JArrayHelper::toInteger( $perm_wysiwyg );
		JArrayHelper::toInteger( $perm_edit_quiz );
		JArrayHelper::toInteger( $perm_manage );
		$perm_component_access		= implode(",", $perm_component_access);
		$perm_create_quiz			= implode(",", $perm_create_quiz);
		$perm_edit_quiz				= implode(",",$perm_edit_quiz);
		$perm_submit_response		= implode(",", $perm_submit_response);
		$perm_wysiwyg				= implode(",",$perm_wysiwyg);
		$perm_manage				= implode(",",$perm_manage);
		
		/*Default Configuration Properties */
		$query = 'INSERT INTO '.T_QUIZ_CONFIG.' (`config_name`, `config_value`) VALUES' .
			'("' . CQ_DEFAULT_TEMPLATE . '",' . $db->quote('default') . '),' .
			'("' . CQ_DEFAULT_EDITOR. '",' . $db->quote($default_editor) . '),' .
            '("' . CQ_LIST_LIMIT . '",' . $db->quote($list_length) . '),' .
			'("' . CQ_ENABLE_MODERATION . '",' . $db->quote($enable_moderation) . '),' .
			'("' . CQ_ENABLE_RATINGS . '",' . $db->quote($enable_ratings) . '),' .
			'("' . CQ_FILTERED_KEYWORDS . '",' . $db->quote($filtered_words) . '),' .
			'("' . CQ_USER_NAME . '",' . $db->quote($user_name) . '),' .
            '("' . CQ_USER_AVTAR . '",' . $db->quote($user_avtar) . '),' .
			'("' . CQ_SHOW_AVATAR_IN_LISTING . '",' . $db->quote($show_avatar_in_listing) . '),' .
			'("' . CQ_AVATAR_SIZE . '",' . $db->quote($avatar_size) . '),' .
			'("' . CQ_TOOLBAR_BUTTONS . '",' . $db->quote($toolbar_buttons) . '),' .
			'("' . CQ_HIDE_TEMPLATE . '",' . $db->quote($hide_template) . '),' .
			'("' . CQ_ENABLE_CATEGORY_BOX . '",' . $db->quote($enable_category_box) . '),' .
			'("' . CQ_CLEAN_HOME_PAGE . '",' . $db->quote($clean_home_page) . '),' .
			'("' . CQ_ENABLE_POWERED_BY . '",' . $db->quote($enable_credits) . '),' .
			'("' . CQ_NOTIF_SENDER_NAME . '",' . $db->quote($notif_sender_name) . '),' .
            '("' . CQ_NOTIF_SENDER_EMAIL . '",' . $db->quote($notif_sender_email) . '),' .
			'("' . CQ_NOTIF_ADMIN_EMAIL . '",' . $db->quote($notif_admin_email) . '),' .
			'("' . CQ_NOTIF_NEW_QUIZ . '",' . $db->quote($notif_new_quiz) . '),' .
            '("' . CQ_NOTIF_NEW_RESPONSE . '",' . $db->quote($notif_new_response) . '),' .
            '("' . CQ_ACTIVITY_STREAM_TYPE . '",' . $db->quote($activity_stream_type) . '),' .
            '("' . CQ_STREAM_NEW_QUIZ . '",' . $db->quote($stream_new_quiz) . '),' .
            '("' . CQ_STREAM_NEW_RESPONSE . '",' . $db->quote($stream_new_response) . '),' .
            '("' . CQ_POINTS_SYSTEM . '",' . $db->quote($points_system) . '),' .
            '("' . CQ_TOUCH_POINTS_NEW_QUIZ . '",' . $db->quote($points_new_quiz) . '),' .
            '("' . CQ_TOUCH_POINTS_NEW_RESPONSE . '",' . $db->quote($points_new_response) . '),' .
			'("' . CQ_PERM_GUEST_BROWSE . '",' . $db->quote($perm_guest_browsing) . '),' .
			'("' . CQ_PERM_GUEST_RESPONSE . '",' . $db->quote($perm_guest_response) . '),' .
			'("' . CQ_PERM_COMPONENT_ACCESS . '",' . $db->quote($perm_component_access) . '),' .
            '("' . CQ_PERM_CREATE_QUIZ . '",' . $db->quote($perm_create_quiz) . '),' .
			'("' . CQ_PERM_EDIT_QUIZ . '",' . $db->quote($perm_edit_quiz) . '),' .
            '("' . CQ_PERM_SUBMIT_ANSWER . '",' . $db->quote($perm_submit_response) . '),' .
            '("' . CQ_PERM_WYSIWYG . '",' . $db->quote($perm_wysiwyg) . '),' .
			'("' . CQ_PERM_MANAGE . '",' . $db->quote($perm_manage) . ')' .
            ' ON DUPLICATE KEY UPDATE config_value=VALUES(config_value)';

		$db->setQuery( $query );

		if(!$db->query()) {
			return false;
			$this->setError($query);
		}

		return true;
	}
}
?>