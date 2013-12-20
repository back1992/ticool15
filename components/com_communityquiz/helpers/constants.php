<?php
/**
 * @version		$Id: constants.php 01 2011-01-11 11:37:09Z maverick $
 * @package		CoreJoomla16.Quiz
 * @subpackage	Components
 * @copyright	Copyright (C) 2009 - 2010 corejoomla.com. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
defined('Q_APP_NAME') or define('Q_APP_NAME', 'com_communityquiz');

// Please do not touch these until and unless you know what you are doing.
define("CQ_CURR_VERSION",               "1.7.2");
define("CQ_ASSET_ID",					4);

define("T_QUIZ_CONFIG",					"#__quiz_config");
define("T_QUIZ_CATEGORIES",				"#__quiz_categories");
define("T_QUIZ_QUIZZES",				"#__quiz_quizzes");
define("T_QUIZ_PAGES",					"#__quiz_pages");
define("T_QUIZ_QUESTIONS",				"#__quiz_questions");
define("T_QUIZ_ANSWERS",				"#__quiz_answers");
define("T_QUIZ_RESPONSES",				"#__quiz_responses");
define("T_QUIZ_RESPONSE_DETAILS",		"#__quiz_response_details");
define("T_QUIZ_COUNTRIES",				"#__quiz_countries");
define("T_QUIZ_USERS",					"#__quiz_users");
define("T_CJ_RATING",					"#__corejoomla_rating");
define("T_CJ_RATING_DETAILS",			"#__corejoomla_rating_details");

define("CQ_DEFAULT_TEMPLATE",			"quiz_default_template");
define("CQ_COOKIE_TIME_TO_LIVE",		"cookie_time_to_live");
define("CQ_USER_AVTAR",					"user_avatar");
define("CQ_AVATAR_SIZE",				"user_avatar_size");
define("CQ_SHOW_AVATAR_IN_LISTING",		"show_avatar_in_listing");
define("CQ_TOOLBAR_BUTTONS",			"toolbar_buttons");
define("CQ_USER_NAME",					"user_name");
define("CQ_DEFAULT_EDITOR",				"default_editor");
define("CQ_LIST_LIMIT",					"list_limit");
define("CQ_HIDE_TEMPLATE",				"hide_template");
define("CQ_FILTERED_KEYWORDS",			"filtered_words");
define("CQ_ENABLE_CATEGORY_BOX",		"enable_category_box");
define("CQ_CLEAN_HOME_PAGE",			"clean_home_page");
define("CQ_ENABLE_POWERED_BY",			"powered_by_enabled");
define("CQ_ENABLE_MODERATION",			"enable_moderation");
define("CQ_NOTIF_ADMIN_EMAIL",			"notif_admin_email");
define("CQ_NOTIF_SENDER_NAME",			"notif_sender_name");
define("CQ_NOTIF_SENDER_EMAIL",			"notif_sender_email");
define("CQ_NOTIF_NEW_QUIZ",				"notif_new_quiz");
define("CQ_NOTIF_NEW_RESPONSE",			"notif_new_response");
define("CQ_ACTIVITY_STREAM_TYPE",		"activity_stream_type");
define("CQ_STREAM_NEW_QUIZ",			"stream_new_quiz");
define("CQ_STREAM_NEW_RESPONSE",		"stream_new_response");
define("CQ_POINTS_SYSTEM",				"points_system");
define("CQ_TOUCH_POINTS_NEW_QUIZ",		"touch_points_new_quiz");
define("CQ_TOUCH_POINTS_NEW_RESPONSE",	"touch_points_new_response");
define("CQ_ENABLE_RATINGS",				"enable_ratings");
define("CQ_PERM_COMPONENT_ACCESS",		"permission_component_access");
define("CQ_PERM_GUEST_BROWSE",			"permission_guest_browse");
define("CQ_PERM_CREATE_QUIZ",			"permission_create_quiz");
define("CQ_PERM_EDIT_QUIZ",				"permission_edit_quiz");
define("CQ_PERM_SUBMIT_ANSWER",			"permission_submit_answer");
define("CQ_PERM_GUEST_RESPONSE",		"permission_guest_response");
define("CQ_PERM_WYSIWYG",				"permission_wysiwyg");
define("CQ_PERM_MANAGE",				"permission_manage");

define("CQ_SESSION_CONFIG",				"quiz_session_config");
define("CQ_POINTS_SYSTEM_JOMSOCIAL",	"jomsocial");
define("CQ_POINTS_SYSTEM_AUP",			"aup");
define("CQ_POINTS_SYSTEM_TOUCH",		"touch");
define("CQ_AUP_NEW_QUIZ",				"sysplgaup_new_quiz");
define("CQ_AUP_QUIZ_RESPONSE",			"sysplgaup_quiz_response");
define("CQ_JSP_NEW_QUIZ",				Q_APP_NAME.".new_quiz");
define("CQ_JSP_QUIZ_RESPONSE",			Q_APP_NAME.".quiz_response");
define("CQ_DEFAULT_TEMPLATE_PATH",		JPATH_COMPONENT . DS . "templates" );
define("CQ_DEFAULT_TEMPLATE_URL",		JURI::base(true). "/components/".Q_APP_NAME."/templates/" );
define("CQ_TEMPLATE_OVERRIDES_PATH",	JPATH_ROOT . DS . "templates" . DS . "communityquiz" );
define("CQ_TEMPLATE_OVERRIDES_URL",		JURI::base(true).'/templates/communityquiz/' );
define("QT_PAGE_HEADER",				1);
define("QT_CHOICE_RADIO", 				2);
define("QT_CHOICE_CHECKBOX", 			3);
define("QT_CHOICE_SELECT", 				4);
define("QT_GRID_RADIO", 				5);
define("QT_GRID_CHECKBOX", 				6);
define("QT_FREE_TEXT_SINGLE_LINE",		7);
define("QT_FREE_TEXT_MULTILINE",		8);
define("QT_FREE_TEXT_PASSWORD",			9);
define("QT_FREE_TEXT_RICH_TEXT",		10);
?>