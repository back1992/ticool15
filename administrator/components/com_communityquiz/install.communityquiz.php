<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: install.communityQuiz.php 2009-08-10 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Quiz
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT.DS.'components'.DS.'com_communityquiz'.DS.'helpers'.DS.'constants.php';

// Initialize the database
$db =& JFactory::getDBO();
$update_queries = array ();
$createdate = JFactory::getDate();
$createdate = $createdate->toMySQL();

/*Default Configuration Properties */
//$update_queries[] = 'ALTER IGNORE TABLE '.T_QUIZ_RESPONSES.' ADD COLUMN `score` INTEGER UNSIGNED NOT NULL DEFAULT \'0\'';
//$update_queries[] = 'ALTER IGNORE TABLE '.T_QUIZ_RESPONSES.' ADD COLUMN `finished` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
//$update_queries[] = 'ALTER IGNORE TABLE '.T_QUIZ_QUESTIONS.' ADD COLUMN `description` MEDIUMTEXT';
//$update_queries[] = 'ALTER IGNORE TABLE '.T_QUIZ_QUESTIONS.' ADD COLUMN `answer_explanation` MEDIUMTEXT';
//$update_queries[] = 'ALTER IGNORE TABLE '.T_QUIZ_CONFIG.' MODIFY COLUMN `config_value` MEDIUMTEXT NOT NULL';

$update_queries[] = 'insert ignore into `#__quiz_config` (`config_name`, `config_value`) VALUES' .
			'("' . CQ_DEFAULT_TEMPLATE . '",' . $db->quote('default') . '),' .
			'("' . CQ_DEFAULT_EDITOR. '",' . $db->quote('default') . '),' .
            '("' . CQ_LIST_LIMIT . '",' . $db->quote('20') . '),' .
			'("' . CQ_ENABLE_MODERATION . '",' . $db->quote('0') . '),' .
			'("' . CQ_USER_NAME . '",' . $db->quote('username') . '),' .
            '("' . CQ_USER_AVTAR . '",' . $db->quote('none') . '),' .
			'("' . CQ_SHOW_AVATAR_IN_LISTING . '",' . $db->quote('1') . '),' .
			'("' . CQ_AVATAR_SIZE . '",' . $db->quote('42') . '),' .
			'("' . CQ_TOOLBAR_BUTTONS . '",' . $db->quote('L,P') . '),' .
			'("' . CQ_HIDE_TEMPLATE . '",' . $db->quote('0') . '),' .
			'("' . CQ_ENABLE_CATEGORY_BOX . '",' . $db->quote('1') . '),' .
			'("' . CQ_CLEAN_HOME_PAGE . '",' . $db->quote('0') . '),' .
			'("' . CQ_ENABLE_POWERED_BY . '",' . $db->quote('1') . '),' .
			'("' . CQ_NOTIF_SENDER_NAME . '",' . $db->quote('') . '),' .
            '("' . CQ_NOTIF_SENDER_EMAIL . '",' . $db->quote('') . '),' .
			'("' . CQ_NOTIF_ADMIN_EMAIL . '",' . $db->quote('') . '),' .
			'("' . CQ_NOTIF_NEW_QUIZ . '",' . $db->quote('0') . '),' .
            '("' . CQ_NOTIF_NEW_RESPONSE . '",' . $db->quote('0') . '),' .
            '("' . CQ_ACTIVITY_STREAM_TYPE . '",' . $db->quote('none') . '),' .
            '("' . CQ_STREAM_NEW_QUIZ . '",' . $db->quote('0') . '),' .
            '("' . CQ_STREAM_NEW_RESPONSE . '",' . $db->quote('0') . '),' .
            '("' . CQ_POINTS_SYSTEM . '",' . $db->quote('none') . '),' .
            '("' . CQ_TOUCH_POINTS_NEW_QUIZ . '",' . $db->quote('') . '),' .
            '("' . CQ_TOUCH_POINTS_NEW_RESPONSE . '",' . $db->quote('') . '),'.
			'("' . CQ_PERM_GUEST_BROWSE . '",' . $db->quote('0') . '),' .
			'("' . CQ_PERM_GUEST_RESPONSE . '",' . $db->quote('0') . '),' .
			'("' . CQ_PERM_COMPONENT_ACCESS . '",' . $db->quote('30,23,24,25') . '),' .
            '("' . CQ_PERM_CREATE_QUIZ . '",' . $db->quote('30,23,24,25') . '),' .
			'("' . CQ_PERM_EDIT_QUIZ . '",' . $db->quote('30,23,24,25') . '),' .
            '("' . CQ_PERM_SUBMIT_ANSWER . '",' . $db->quote('30,23,24,25') . '),' .
            '("' . CQ_PERM_WYSIWYG . '",' . $db->quote('30,23,24,25') . ')';

$update_queries[] = "insert ignore into #__quiz_countries(country_name, country_code) values
	('Afghanistan','AF'), ('Åland Islands','AX'), ('Albania','AL'), ('Algeria','DZ'), ('American Samoa','AS'), ('Andorra','AD'), 
	('Angola','AO'),('Anguilla','AI'), ('Antarctica','AQ'), ('Antigua And Barbuda','AG'), ('Argentina','AR'), ('Armenia','AM'), 
	('Aruba','AW'), ('Australia','AU'), ('Austria','AT'), ('Azerbaijan','AZ'), ('Bahamas','BS'), ('Bahrain','BH'), ('Bangladesh','BD'), 
	('Barbados','BB'), ('Belarus','BY'), ('Belgium','BE'), ('Belize','BZ'), ('Benin','BJ'), ('Bermuda','BM'), ('Bhutan','BT'), 
	('Bolivia','BO'), ('Bosnia And Herzegovina','BA'), ('Botswana','BW'), ('Bouvet Island','BV'), ('Brazil','BR'), 
	('British Indian Ocean Territory','IO'),('Brunei Darussalam','BN'), ('Bulgaria','BG'), ('Burkina Faso','BF'), ('Burundi','BI'), 
	('Cambodia','KH'), ('Cameroon','CM'), ('Canada','CA'), ('Cape Verde','CV'), ('Cayman Islands','KY'), ('Central African Republic','CF'), 
	('Chad','TD'), ('Chile','CL'), ('China','CN'), ('Christmas Island','CX'), ('Cocos Keeling Islands','CC'), ('Colombia','CO'), 
	('Comoros','KM'), ('Congo','CG'), ('Congo','CD'), ('Cook Islands','CK'), ('Costa Rica','CR'), ('Côte D\'ivoire','CI'),
	('Croatia','HR'), ('Cuba','CU'), ('Cyprus','CY'), ('Czech Republic','CZ'), ('Denmark','DK'), ('Djibouti','DJ'), ('Dominica','DM'), 
	('Dominican Republic','DO'), ('Ecuador','EC'), ('Egypt','EG'), ('El Salvador','SV'), ('Equatorial Guinea','GQ'), ('Eritrea','ER'), ('Estonia','EE'),
	('Ethiopia','ET'), ('Falkland Islands Malvinas','FK'),('Faroe Islands','FO'), ('Fiji','FJ'), ('Finland','FI'), ('France','FR'), ('French Guiana','GF'),
	('French Polynesia','PF'), ('French Southern Territories','TF'), ('Gabon','GA'), ('Gambia','GM'), ('Georgia','GE'), ('Germany','DE'), ('Ghana','GH'),
	('Gibraltar','GI'), ('Greece','GR'), ('Greenland','GL'), ('Grenada','GD'), ('Guadeloupe','GP'), ('Guam','GU'), ('Guatemala','GT'), ('Guernsey','GG'),
	('Guinea','GN'), ('Guinea-Bissau','GW'), ('Guyana','GY'), ('Haiti','HT'), ('Heard Island And Mcdonald Islands','HM'), ('Honduras','HN'), 
	('Hong Kong','HK'), ('Hungary','HU'), ('Iceland','IS'), ('India','IN'), ('Indonesia','ID'), ('Iran','IR'), ('Iraq','IQ'), ('Ireland','IE'),
	('Isle Of Man','IM'), ('Israel','IL'), ('Italy','IT'), ('Jamaica','JM'), ('Japan','JP'), ('Jersey','JE'), ('Jordan','JO'), ('Kazakhstan','KZ'),
	('Kenya','KE'), ('Kiribati','KI'), ('Korea','KP'), ('Korea','KR'), ('Kuwait','KW'), ('Kyrgyzstan','KG'), ('Lao People\'s Democratic Republic','LA'),
	('Latvia','LV'), ('Lebanon','LB'), ('Lesotho','LS'), ('Liberia','LR'), ('Libyan Arab Jamahiriya','LY'), ('Liechtenstein','LI'),
	('Lithuania','LT'), ('Luxembourg','LU'), ('Macao','MO'), ('Macedonia','MK'), ('Madagascar','MG'), ('Malawi','MW'), ('Malaysia','MY'),
	('Maldives','MV'), ('Mali','ML'), ('Malta','MT'), ('Marshall Islands','MH'), ('Martinique','MQ'), ('Mauritania','MR'), ('Mauritius','MU'),
	('Mayotte','YT'), ('Mexico','MX'), ('Micronesia','FM'), ('Moldova','MD'), ('Monaco','MC'), ('Mongolia','MN'), ('Montenegro','ME'),
	('Montserrat','MS'), ('Morocco','MA'), ('Mozambique','MZ'), ('Myanmar','MM'), ('Namibia','NA'),('Nauru','NR'), ('Nepal','NP'), ('Netherlands','NL'),
	('Netherlands Antilles','AN'), ('New Caledonia','NC'), ('New Zealand','NZ'), ('Nicaragua','NI'), ('Niger','NE'), ('Nigeria','NG'), ('Niue','NU'),
	('Norfolk Island','NF'), ('Northern Mariana Islands','MP'), ('Norway','NO'), ('Oman','OM'), ('Pakistan','PK'), ('Palau','PW'), 
	('Palestinian Territory','PS'), ('Panama','PA'), ('Papua New Guinea','PG'), ('Paraguay','PY'), ('Peru','PE'), ('Philippines','PH'), ('Pitcairn','PN'),
	('Poland','PL'), ('Portugal','PT'), ('Puerto Rico','PR'), ('Qatar','QA'), ('Réunion','RE'), ('Romania','RO'), ('Russian Federation','RU'), 
	('Rwanda','RW'), ('Saint Barthélemy','BL'), ('Saint Helena','SH'), ('Saint Kitts And Nevis','KN'), ('Saint Lucia','LC'), ('Saint Martin','MF'),
 	('Saint Pierre And Miquelon','PM'), ('Saint Vincent And The Grenadines','VC'), ('Samoa','WS'), ('San Marino','SM'), ('Sao Tome And Principe','ST'),
	('Saudi Arabia','SA'), ('Senegal','SN'),  ('Serbia','RS'), ('Seychelles','SC'), ('Sierra Leone','SL'), ('Singapore','SG'), ('Slovakia','SK'),
	('Slovenia','SI'), ('Solomon Islands','SB'),  ('Somalia','SO'), ('South Africa','ZA'),  ('South Georgia And The South Sandwich Islands','GS'),
	('Spain','ES'), ('Sri Lanka','LK'), ('Sudan','SD'), ('Suriname','SR'), ('Svalbard And Jan Mayen','SJ'), ('Swaziland','SZ'), ('Sweden','SE'),
	('Switzerland','CH'), ('Syrian Arab Republic','SY'), ('Taiwan','TW'), ('Tajikistan','TJ'), ('Tanzania','TZ'), ('Thailand','TH'), ('Timor-Leste','TL'),
	('Togo','TG'), ('Tokelau','TK'), ('Tonga','TO'), ('Trinidad And Tobago','TT'), ('Tunisia','TN'), ('Turkey','TR'), ('Turkmenistan','TM'),
	('Turks And Caicos Islands','TC'), ('Tuvalu','TV'), ('Uganda','UG'), ('Ukraine','UA'), ('United Arab Emirates','AE'), ('United Kingdom','GB'),
	('United States','US'), ('United States Minor Outlying Islands','UM'), ('Uruguay','UY'), ('Uzbekistan','UZ'), ('Vanuatu','VU'), 
	('Vatican City State','VA'), ('Venezuela','VE'), ('Viet Nam','VN'), ('Virgin Islands British','VG'), ('Virgin Islands U.S.','VI'), 
	('Wallis And Futuna','WF'), ('Western Sahara','EH'), ('Yemen','YE'), ('Zambia','ZM'), ('Zimbabwe','ZW'), ('European Union','EU'), 
	('United Kingdom', 'UK'), ('Ascension Island', 'AC'), ('Clipperton Island', 'CP'), ('Diego Garcia','DG'), ('Ceuta, Melilla','EA'), 
	('France, Metropolitan','FX'), ('Canary Islands','IC'), ('USSR','SU'), ('Tristan da Cunha','TA'),('Unknown','XX')";
// Perform all queries - we don't care if it fails
foreach( $update_queries as $query ) {
    $db->setQuery( $query );
    $db->query();
}

$query = 'select count(*) from #__quiz_categories where parent_id=0 and title='.$db->quote('Root');
$db->setQuery($query);
$count = $db->loadResult();
if(!$count){
	$query = 'insert into #__quiz_categories(title, alias, parent_id, nleft, nright, norder) values ('.$db->quote('Root').','.$db->quote('root').', 0, 1, 2, 1)';
	$db->setQuery($query);
	if($db->query()){
		$root = $db->insertid();
		$query = 'update #__quiz_categories set parent_id='.$root.' where parent_id=0 and id!='.$root;
		$db->setQuery($query);
		if($db->query()){
			require_once JPATH_ROOT.DS.'components'.DS.'com_communityquiz'.DS.'helpers'.DS.'nestedtree.php';
			$tree = new QuizCategories($db, '#__quiz_categories');
			if(!$tree->rebuild()){
				echo 'An error occured while upgrading categories table. Please contact corejoomla support at support@corejoomla.com for assistance.';
			}
		}
	}else{
		echo 'An error occured while upgrading categories table. Please contact corejoomla support at support@corejoomla.com for assistance.';
	}
}
echo "<b><font color=\"red\">Database tables successfully migrated to the latest version. Please check the configuration options once again.</font></b>";
?>