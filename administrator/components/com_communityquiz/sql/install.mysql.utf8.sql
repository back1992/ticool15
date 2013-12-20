CREATE TABLE IF NOT EXISTS `#__quiz_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `quiz_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `answer_type` varchar(10) NOT NULL,
  `responses` int(10) unsigned NOT NULL DEFAULT '0',
  `correct_answer` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_QUIZ_OPTIONS_QUIZ_ID` (`quiz_id`),
  KEY `FK_QUIZ_QUESTIONS_QUESTION_ID` (`question_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `nleft` int(10) unsigned NOT NULL,
  `nright` int(10) unsigned NOT NULL,
  `nlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `norder` int(10) unsigned NOT NULL DEFAULT '0',
  `quizzes` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `jos_quiz_categories_nleft` (`nleft`),
  KEY `jos_quiz_categories_parent_id` (`parent_id`),
  KEY `jos_quiz_categories_nright` (`nright`),
  KEY `jos_quiz_categories_nlevel` (`nlevel`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_config` (
  `config_name` varchar(255) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  PRIMARY KEY (`config_name`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_countries` (
  `country_code` char(3) NOT NULL,
  `country_name` varchar(128) NOT NULL,
  PRIMARY KEY (`country_code`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(10) unsigned NOT NULL,
  `question_type` int(10) unsigned NOT NULL,
  `page_number` int(10) unsigned NOT NULL,
  `responses` decimal(10,0) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `mandatory` tinyint(1) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `include_custom` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(999) NOT NULL,
  `description` mediumtext,
  `answer_explanation` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_quizzes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `responses` int(10) unsigned NOT NULL DEFAULT '0',
  `show_answers` tinyint(1) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `ip_address` varchar(39) NOT NULL,
  `show_template` tinyint(1) NOT NULL DEFAULT '1',
  `published` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `duration` int(10) unsigned NOT NULL DEFAULT '0',
  `multiple_responses` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_response_details` (
  `response_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `answer_id` int(10) unsigned NOT NULL,
  `column_id` int(10) unsigned NOT NULL,
  `free_text` mediumtext,
  KEY `FK_QUIZ_RESPONSE_DETAILS_RESPONSE_ID` (`response_id`),
  KEY `FK_QUIZ_RESPONSE_DETAILS_QUESTION_ID` (`question_id`),
  KEY `FK_QUIZ_RESPONSE_DETAILS_OPTION_ID` (`answer_id`) 
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__quiz_responses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `finished` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `score` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned NOT NULL,
  `ip_address` varchar(39) NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `country` varchar(3) NOT NULL DEFAULT '',
  `browser_info` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_QUIZ_RESPONSES_QUIZ_ID` (`quiz_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__corejoomla_rating_details` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `asset_id` INTEGER UNSIGNED NOT NULL,
  `item_id` INTEGER UNSIGNED NOT NULL,
  `action_id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `rating` INTEGER UNSIGNED NOT NULL,
  `created_by` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__corejoomla_rating`(
  `item_id` int(10) unsigned NOT NULL,
  `asset_id` int(10) unsigned NOT NULL,
  `total_ratings` int(10) unsigned NOT NULL DEFAULT '0',
  `sum_rating` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` decimal(4,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`item_id`,`asset_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__corejoomla_assets` (
  `id` INTEGER UNSIGNED NOT NULL,
  `name` VARCHAR(64) NOT NULL,
  `version` VARCHAR(32) NOT NULL,
  `released` DATE NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;