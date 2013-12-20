<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: helper.php 2010-11-02 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Quiz
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityQuizHelper {

    public static function &getConfig($rebuild=false) {
        $app = &JFactory::getApplication();
        $cqConfig = $app->getUserState( CQ_SESSION_CONFIG );

        if(!isset($cqConfig) || $rebuild) {
            $db     =& JFactory::getDBO();
            $query = 'SELECT config_name, config_value FROM '. T_QUIZ_CONFIG;
            $db->setQuery($query);
            $configt = $db->loadObjectList();

            if($configt) {
                foreach($configt as $ct) {
                    $cqConfig[$ct->config_name] = $ct->config_value;
                }
            }else {
                JError::raiseError( 403, JText::_('Access Forbidden. Error Code: 10001.') );
                return;
            }
            $app->setUserState( CQ_SESSION_CONFIG, $cqConfig );
        }

        return $cqConfig;
    }

	public static function escape($var) {
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	public static function filterData($data) {
		jimport('joomla.application.component.helper');
		$config		= JComponentHelper::getParams('com_content');
		$user		= JFactory::getUser();
		
		if(APP_VERSION == '1.5'){
			// Filter settings
			$gid = $user->get('gid');
			$filterGroups = $config->get('filter_groups');
	
			// convert to array if one group selected
			if ((!is_array($filterGroups) && (int) $filterGroups > 0)) {
				$filterGroups = array($filterGroups);
			}
	
			if (is_array($filterGroups) && in_array($gid, $filterGroups)) {
				$filterType = $config->get('filter_type');
				$filterTags = preg_split('#[,\s]+#', trim($config->get('filter_tags')));
				$filterAttrs = preg_split('#[,\s]+#', trim($config->get('filter_attritbutes')));
				switch ($filterType) {
					case 'NH':
						$filter = new JFilterInput();
						break;
					case 'WL':
						$filter = new JFilterInput($filterTags, $filterAttrs, 0, 0);
						break;
					case 'BL':
					default:
						$filter = new JFilterInput($filterTags, $filterAttrs, 1, 1);
						break;
				}
				$data = $filter->clean($data);
			} elseif (empty($filterGroups)) {
				$filter = new JFilterInput(array(), array(), 1, 1);
				$data = $filter->clean($data);
			}
			return $data;
		}else{
			$userGroups	= JAccess::getGroupsByUser($user->get('id'));
			$filters = $config->get('filters');
	
			$blackListTags			= array();
			$blackListAttributes	= array();
	
			$whiteListTags			= array();
			$whiteListAttributes	= array();
	
			$noHtml		= false;
			$whiteList	= false;
			$blackList	= false;
			$unfiltered	= false;
	
			// Cycle through each of the user groups the user is in.
			// Remember they are include in the Public group as well.
			foreach ($userGroups AS $groupId){
				// May have added a group by not saved the filters.
				if (!isset($filters->$groupId)) {
					continue;
				}
	
				// Each group the user is in could have different filtering properties.
				$filterData = $filters->$groupId;
				$filterType	= strtoupper($filterData->filter_type);
	
				if ($filterType == 'NH') {
					// Maximum HTML filtering.
					$noHtml = true;
				}
				else if ($filterType == 'NONE') {
					// No HTML filtering.
					$unfiltered = true;
				}
				else {
					// Black or white list.
					// Preprocess the tags and attributes.
					$tags			= explode(',', $filterData->filter_tags);
					$attributes		= explode(',', $filterData->filter_attributes);
					$tempTags		= array();
					$tempAttributes	= array();
	
					foreach ($tags AS $tag)
					{
						$tag = trim($tag);
	
						if ($tag) {
							$tempTags[] = $tag;
						}
					}
	
					foreach ($attributes AS $attribute)
					{
						$attribute = trim($attribute);
	
						if ($attribute) {
							$tempAttributes[] = $attribute;
						}
					}
	
					// Collect the black or white list tags and attributes.
					// Each list is cummulative.
					if ($filterType == 'BL') {
						$blackList				= true;
						$blackListTags			= array_merge($blackListTags, $tempTags);
						$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
					}
					else if ($filterType == 'WL') {
						$whiteList				= true;
						$whiteListTags			= array_merge($whiteListTags, $tempTags);
						$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
					}
				}
			}
	
			// Remove duplicates before processing (because the black list uses both sets of arrays).
			$blackListTags			= array_unique($blackListTags);
			$blackListAttributes	= array_unique($blackListAttributes);
			$whiteListTags			= array_unique($whiteListTags);
			$whiteListAttributes	= array_unique($whiteListAttributes);
	
			// Unfiltered assumes first priority.
			if ($unfiltered) {
				// Dont apply filtering.
			} else {
				// Black lists take second precedence.
				if ($blackList) {
					// Remove the white-listed attributes from the black-list.
					$filter = JFilterInput::getInstance(
						array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
						array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
						1,														// blacklist tags
						1														// blacklist attributes
					);
				}
				// White lists take third precedence.
				else if ($whiteList) {
					$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
				}
				// No HTML takes last place.
				else {
					$filter = JFilterInput::getInstance();
				}
				
				$data = $filter->clean($data, 'html');
			}
	
			return $data;
		}
	}
    
    function getMessage($message, $args) {
        $msg = $message;
        if($args && count($args) > 0) {
            for($i=0;$i<count($args);$i++) {
                $msg = str_replace($message, "{".$i."}", $args[$i]);
            }
        }
        return $msg;
    }

    /**
     * Gets the IP address of the currently visiting user.
     *
     * @return <type>
     */
    function get_user_ip() {
		$ip = '';
		if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR'])>6 ){
	        $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
	    }elseif( !empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP'])>6 ){
			 $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
		}elseif(!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR'])>6){
			 $ip = strip_tags($_SERVER['REMOTE_ADDR']);
	    }
		return trim($ip);
    }

	function get_user_location($ipAddr){
		//function to find country and city from IP address
		//Developed by Roshan Bhattarai [url]http://roshanbh.com.np[/url]

		//verify the IP address for the
		$ipDetail=array(); //initialize a blank array
		if(ip2long($ipAddr)== -1 || ip2long($ipAddr) === false){
			$ipDetail['city'] = "Unknown";
			$ipDetail['country'] = "Unknown";
			$ipDetail['country_code'] = "00";
			return $ipDetail;
		}

		//get the XML result from hostip.info
		$xml = file_get_contents("http://api.hostip.info/?ip=".$ipAddr);

		//get the city name inside the node <gml:name> and </gml:name>
		preg_match("@<Hostip>(\s)*<gml:name>(.*?)</gml:name>@si",$xml,$match);

		//assing the city name to the array
		if(!empty($match[2])){
			$ipDetail['city']=$match[2];
		} else {
			$ipDetail['city']='';
		}

		//get the country name inside the node <countryName> and </countryName>
		preg_match("@<countryName>(.*?)</countryName>@si",$xml,$matches);

		//assign the country name to the $ipDetail array
		$ipDetail['country']=$matches[1];

		//get the country name inside the node <countryName> and </countryName>
		preg_match("@<countryAbbrev>(.*?)</countryAbbrev>@si",$xml,$cc_match);
		$ipDetail['country_code']=$cc_match[1]; //assing the country code to array

		//return the array containing city, country and country code
		return $ipDetail;
	}
    
	function getItemId($nocat=false){
		$menu = &JSite::getMenu();
		$mnuitem = $menu->getItems('link', 'index.php?option='.Q_APP_NAME.'&view=quiz', true);
		$ritemid = JRequest::getInt('Itemid');
		return isset($mnuitem) ? '&Itemid='.$mnuitem->id : (isset($ritemid)?'&Itemid='.$ritemid:'');
	}
    
    /**
     * Gets the avatar of the user. Currently supporting JomSocial, Kunena, AUP and CB.
     *
     * @global <type> $cqConfig
     * @param <type> $userid
     * @param <type> $height
     * @return <type>
     */
    function getUserAvatar($userid=0, $height=48) {
        $cqConfig = CommunityQuizHelper::getConfig();
        $db = JFactory::getDBO();
        $avatar = '';
        $username = $cqConfig[CQ_USER_NAME];
        switch ( $cqConfig[CQ_USER_AVTAR] ) {
            case 'jomsocial':
				include_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'defines.community.php' );
				require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
				require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'helpers' . DS . 'string.php' );

				$user 		= CFactory::getUser($userid);				
				$userName 	= CStringHelper::escape( $user->getDisplayName() );
				$userLink 	= CRoute::_('index.php?option=com_community&view=profile&userid='.$userid);
				
				$avatar = '<a href="'.$userLink.'"><img width="'.$height.'" src="'.$user->getThumbAvatar().'" alt="'. $userName.'" title="'.$userName.'"/></a>';
                break;
            case 'cb':
                $strSql = "SELECT avatar, firstname FROM #__comprofiler WHERE user_id={$userid} AND avatarapproved=1";
                $db->setQuery($strSql);
                $result = $db->loadObject();
                $link = JRoute::_( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='.$userid);
                if($result && !empty($result->avatar)) {
                    $avatarLoc = JURI::base(true)."/images/comprofiler/".$result->avatar;
                } else {
                    $avatarLoc = JURI::base(true)."/components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png";
                }
                $avatar = '<a href="'.$link.'"><img src="'.$avatarLoc.'" class="hasTip" style="border: 1px solid #cccccc; height: '.$height.'px;"/></a>';
                break;
            case 'touch':
                $avatarLoc = JURI::base(true) . '/index2.php?option=com_community&amp;controller=profile&amp;task=avatar&amp;width=' . $height . '&amp;height=' . $height . '&amp;user_id=' . $userid . '&amp;no_ajax=1';
                $avatar = '<img src="'.$avatarLoc.'" style="border: 1px solid #cccccc; height: "'.$height.'"px;" alt=""/>';
                $link = JRoute::_("index.php?option=com_community&view=profile&user_id={$userid}&Itemid=".JRequest::getInt('Itemid'));
                $avatar = '<a href="' . $link . '">' . $avatar . '</a>';
                break;
            case 'gravatar':
				$strSql = 'SELECT email FROM #__users WHERE id=' . $userid;
				$db->setQuery($strSql);
				$email = $db->loadResult();
				$url = 'http://www.gravatar.com/avatar/'.md5( strtolower( trim( $email ) ) );
				$url .= "?s=$height&d=mm&r=g";
				$avatar = '<img src="' . $url . '"/>';				
				break;
            case 'kunena':
				require_once (JPATH_ROOT.DS.'components'.DS.'com_kunena'.DS.'class.kunena.php');
            	require_once (JPATH_ROOT.DS.'components'.DS.'com_kunena'.DS.'lib'.DS.'kunena.link.class.php');
				$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
				$username = $kunena_user->getName(); // Takes care of realname vs username setting
				$avatarlink = $kunena_user->getAvatarLink ( '', $height, $height );
				$avatar = CKunenaLink::GetProfileLink ( $userid, $avatarlink, $username );
                break;
            case 'aup':
                $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                if ( file_exists($api_AUP)) {
                    require_once ($api_AUP);
                    $avatar = AlphaUserPointsHelper::getAupAvatar($userid, 1, $height, $height);
                }
                break;
        }
        return $avatar;
    }

    function getUserProfileUrl($userid=0, $username='Guest') {
        $cqConfig = CommunityQuizHelper::getConfig();
        $link = null;
        
        switch ( $cqConfig[CQ_USER_AVTAR] ) {
            case 'jomsocial':
                $jspath = JPATH_BASE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php';
                if(file_exists($jspath)) {
                    include_once($jspath);
                    $link = '<a href="' . CRoute::_('index.php? option=com_community&view=profile&userid='.$userid) . '">' . $username . '</a>';
                }
                break;
            case 'cb':
                $link = '<a href="' . JRoute::_( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user=' . $userid) . '">' . $username . '</a>';
                break;
            case 'touch':
                $link = CommunityQuizHelper::getTouchPopup($userid, $username);
                break;
            case 'kunena':
                $link = '<a href="' . JRoute::_( 'index.php?option=com_kunena&amp;func=fbprofile&amp;userid=' . $userid) . '">' . $username . '</a>';
                break;
            case 'aup':
                $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                if ( file_exists($api_AUP)) {
                    require_once ($api_AUP);
                    $link = '<a href="' . AlphaUserPointsHelper::getAupLinkToProfil($userid) . '">' . $username . '</a>';
                }
                break;
        }
        return (!$link)?$username:$link;
    }

    function getTouchPopup($user_id, $user_name) {
        Static $capi = false;
        Static $api_enabled = true;
		
        if($api_enabled == false) return $user_name;

        if(!$capi) {
            $API = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'api.php';
            if(file_exists($API)) {
                require_once $API;
                $capi = new JSCommunityApi();
            }
            else {
                $api_enabled = false;
                return $user_name;
            }
        }

        $params['width'] = 400;

        $links[0]->link = JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=answers&task=myquestions&userid='.$user_id.'&Itemid='.CommunityQuizHelper::getItemId());
        $links[0]->alt = JText::_('LBL_ALL_QUIZZES_BY');
        $links[0]->icon = 'components/'.Q_APP_NAME.'/assets/images/logo.png';

        $user_name = $capi->getUserSlideMenu($user_id, $user_name, $links, $params);

        return $user_name;
    }

    function getFormattedDate($strdate, $format='d m Y') {
		if(empty($strdate) || $strdate == '0000-00-00 00:00:00'){
			return JText::_('LBL_NA');
		}
        jimport('joomla.utilities.date');
        $user =& JFactory::getUser();
        $tz = '';
        if($user->get('id')) {
            $tz = $user->getParam('timezone');
        } else {
            $conf =& JFactory::getConfig();
            $tz = $conf->getValue('config.offset');
        }
		
        // Given time
        $date = new JDate($strdate, $tz);
        $compareTo = new JDate('now', $tz);
		
		$diff = $compareTo->toUnix() - $date->toUnix();
		$dayDiff = floor($diff/86400);
		
		if($dayDiff == 0) {
			if($diff < 60) {
				return JText::_('TXT_JUST_NOW');
			} elseif($diff < 120) {
				return JText::_('TXT_ONE_MINUTE_AGO');
			} elseif($diff < 3600) {
				return sprintf(JText::_('TXT_N_MINUTES_AGO'), floor($diff/60));
			} elseif($diff < 7200) {
				return JText::_('TXT_ONE_HOUR_AGO');
			} elseif($diff < 86400) {
				return sprintf(JText::_('TXT_N_HOURS_AGO'), floor($diff/3600));
			}
		} elseif($dayDiff == 1) {
			return JText::_('TXT_YESTERDAY');
		} elseif($dayDiff < 7) {
			return sprintf(JText::_('TXT_N_DAYS_AGO'), $dayDiff);
		} elseif($dayDiff == 7) {
			return JText::_('TXT_ONE_WEEK_AGO');
		} elseif($dayDiff < (7*6)) {
			return sprintf(JText::_('TXT_N_WEEKS_AGO'), ceil($dayDiff/7));
		} elseif($dayDiff > 30 && $dayDiff <= 60) {
			return JText::_('TXT_ONE_MONTH_AGO');
		} elseif($dayDiff < 365) {
			return sprintf(JText::_('TXT_N_MONTHS_AGO'), ceil($dayDiff/(365/12)));
		} else {
			$years = round($dayDiff/365);
			if($years == 1){
				return sprintf(JText::_('TXT_ONE_YEAR_AGO'), round($dayDiff/365));
			}else{
				return sprintf(JText::_('TXT_N_YEARS_AGO'), round($dayDiff/365));
			}
		}
	}

    function awardPoints($userid, $function, $referrence, $info){
        $cqConfig = CommunityQuizHelper::getConfig();
        if(strcasecmp($cqConfig[CQ_POINTS_SYSTEM], CQ_POINTS_SYSTEM_AUP) == 0) {
            $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
            if ( file_exists($api_AUP)){
                require_once ($api_AUP);
                $aupid = AlphaUserPointsHelper::getAnyUserReferreID( $userid );
                if ( $aupid ){
                    switch ($function){
                        case 1: //New Question
                            AlphaUserPointsHelper::newpoints( CQ_AUP_NEW_QUIZ, $aupid, $referrence, $info );
                            break;
                        case 2: // New Answer
                            AlphaUserPointsHelper::newpoints( CQ_AUP_QUIZ_RESPONSE, $aupid, $referrence, $info );
                            break;
                    }
                }
            }
        }else if(strcasecmp($cqConfig[CQ_POINTS_SYSTEM], CQ_POINTS_SYSTEM_JOMSOCIAL) == 0) {
            include_once( JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php');
            switch ($function){
                case 1: //New Question
                     CuserPoints::assignPoint(CQ_JSP_NEW_QUIZ, $userid);
                    break;
                case 2: // New Answer
                    CuserPoints::assignPoint(CQ_JSP_QUIZ_RESPONSE, $userid);
                    break;
            }
        }else if(strcasecmp($cqConfig[CQ_POINTS_SYSTEM], CQ_POINTS_SYSTEM_TOUCH) == 0) {
            $API = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'api.php';
            if(file_exists($API)){
                require_once $API;
                switch ($function){
                    case 1: //New Question
                        JSCommunityApi::increaseKarma($userid, $cqConfig[CQ_TOUCH_POINTS_NEW_QUIZ]);
                        break;
                    case 2: // New Answer
                        JSCommunityApi::increaseKarma($userid, $cqConfig[CQ_TOUCH_POINTS_NEW_RESPONSE]);
                        break;
                }
            }
        }

    }

    function streamActivity($action, $quiz) {
        $cqConfig = CommunityQuizHelper::getConfig();
        $user =& JFactory::getUser();
        $itemid = CommunityQuizHelper::getItemId(true);

        if(strcasecmp($cqConfig[CQ_ACTIVITY_STREAM_TYPE], 'jomsocial') == 0) {
            $API = JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
            if(file_exists($API)) {
                include_once( $API );
                $link = JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz->id . ":" . $quiz->alias. $itemid);
                $act = new stdClass();
                $act->cmd       = 'wall.write';
                $act->actor 	= $user->id;
                $act->target 	= 0; // no target
                $act->content 	= '';
                $act->app       = 'wall';
                $act->cid       = 0;
                
                switch ($action){
                    case 1: // New question
                        $text = JText::_('TXT_CREATED_QUIZ');
                        $act->title 	= JText::_('{actor} ' . $text . ' <a href="'.$link.'">'.CommunityQuizHelper::escape($quiz->title).'</a>');
                        $act->content 	= $quiz->description;
                        break;
                    case 2: // New answer
                        $text = JText::_('TXT_RESPONDED_QUIZ');
                        $act->title 	= JText::_('{actor} ' . $text . ' <a href="'.$link.'">'.CommunityQuizHelper::escape($quiz->title).'</a>');
                        $act->content 	= $quiz->description;
                        break;
                }

                CFactory::load('libraries', 'activities');
                CActivityStream::add($act);
            }
            
        } else if(strcasecmp($cqConfig[CQ_ACTIVITY_STREAM_TYPE], 'touch') == 0){
            $API = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'api.php';
            if(file_exists($API)) {
                require_once $API;
                $capi = new JSCommunityApi();
                if($user->id) {
                    $link = JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz->id.":".$quiz->alias.$itemid);
                    $icon = 'components/'.Q_APP_NAME.'/assets/images/icon-16-quiz.png';
                    $text = '';
                    switch ($action){
                        case 1: // New question
                            $text = JText::_('TXT_CREATED_QUIZ') . ' <a href="'.$link.'">' . CommunityQuizHelper::escape($quiz->title) . '</a>';
                            break;
                        case 2: // New answer
                            $text = JText::_('TXT_RESPONDED_QUIZ') . ' <a href="'.$link.'">' . CommunityQuizHelper::escape($quiz->title) . '</a>';
                            break;
                    }
                    $capi->registerActivity(0, $text, $user->get('id'), $icon, 'user', null, ''.Q_APP_NAME.'', '', JText::_('LBL_QUIZ'));
                }
            }
        }
    }
    
    function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null){
        // Get a JMail instance
        $mail = &JFactory::getMailer();

        $mail->setSender(array($from, $fromname));
        $mail->setSubject($subject);
        $mail->setBody($body);

        // Are we sending the email as HTML?
        if ($mode) {
            $mail->IsHTML(true);
        }

        $mail->addRecipient($recipient);
        $mail->addCC($cc);
        $mail->addBCC($bcc);
        $mail->addAttachment($attachment);

        // Take care of reply email addresses
        if (is_array($replyto)) {
            $numReplyTo = count($replyto);
            for ($i=0; $i < $numReplyTo; $i++){
                    $mail->addReplyTo(array($replyto[$i], $replytoname[$i]));
            }
        } elseif (isset($replyto)) {
            $mail->addReplyTo(array($replyto, $replytoname));
        }

        return  $mail->Send();
    }
	
	// Generate a random character string
	function generate_key($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
		// Length of character list
		$chars_length = (strlen($chars) - 1);

		// Start our string
		$string = $chars{rand(0, $chars_length)};
		 
		// Generate random string
		for ($i = 1; $i < $length; $i = strlen($string))
		{
			// Grab a random character from our list
			$r = $chars{rand(0, $chars_length)};
			 
			// Make sure the same two characters don't appear next to each other
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		 
		// Return the string
		return $string;
	}
    
    function nl2a($string) {
        $class_attr = ($class!='') ? ' class="'.$class.'"' : '';
        $strings = explode("\n", $string);
        $html = '';
        foreach($strings as $str){
            $str = trim($str);
            if((strncmp($str, 'http://', 7) == 0) || (strncmp($str, 'www.', 4) == 0)){
                $html .= '<a href="' . $str . '">' . $str . '</a>' . '<br>';
            }
        }
        return $html;
    }

    /**
     * Loads the modules published in the position name passed.
     *
     * @param <type> $position
     * @return <type>
     */
    function loadModulePosition($position) {
        if(JDocumentHTML::countModules($position)) {
            $document	= &JFactory::getDocument();
            $renderer	= $document->loadRenderer('modules');
            $options	= array('style' => 'xhtml');
            return $renderer->render($position, $options, null);
        }else {
            return '';
        }
    }
    
    function process_html($content){
    	$config = CommunityQuizHelper::getConfig();
    	if($config[CQ_DEFAULT_EDITOR] == 'bbcode'){
			require_once JPATH_COMPONENT.DS.'helpers'.DS.'markitup.bbcode-parser.php';
			$content = BBCode2Html($content);
    	}
    	return $content;
    }
    
    function load_editor($id, $name, $html, $rows, $cols, $width=null, $height=null, $class=null, $style=null){
    	$config = CommunityQuizHelper::getConfig();
    	
    	$style = $style ? ' style="'.$style.'"' : '';
    	$class = $class ? ' class="'.$class.'"' : '';
    	$width = $width ? $width : '450px';
    	$height = $height ? $height : '200px';
    	
    	$content = '';
    	if(CAuthorization::authorise('quiz.wysiwyg')){
	    	if( empty($html) ) $html = '';
	    	
    		if($config[CQ_DEFAULT_EDITOR] == 'bbcode'){
    			$content = '<style type="text/css"><!-- .markItUpHeader ul { margin: 0; } .markItUpHeader ul li	{ list-style:none; float:left; position:relative; background: none;	line-height: 100%; margin: 0; padding: 0; } --></style>';
    			$content .= '<div style="width: '.$width.';"><textarea name="'.$name.'" id="'.$id.'" rows="5" cols="50"'.$style.$class.'>'.$html.'</textarea></div>';
    			$document = JFactory::getDocument();
    			$document->addScript(JURI::base(true). '/components/'.Q_APP_NAME.'/assets/markitup/jquery.markitup.js');
    			$document->addScript(JURI::base(true). '/components/'.Q_APP_NAME.'/assets/markitup/sets/bbcode/set.js');
    			$document->addStyleSheet(JURI::base(true). '/components/'.Q_APP_NAME.'/assets/markitup/skins/markitup/style.css');
    			$document->addStyleSheet(JURI::base(true). '/components/'.Q_APP_NAME.'/assets/markitup/sets/bbcode/style.css');
    			$document->addScriptDeclaration('jQuery(document).ready(function($){$("#'.$id.'").markItUp(myBBCodeSettings)});;');
    		}else{
                $editor =& JFactory::getEditor();
                $content = '<div style="overflow: hidden; clear: both;">'.$editor->display( $id, $html, $width, $height, $cols, $rows ).'</div>';
    		}
    	}else{
    		$content = '<textarea name="'.$name.'" id="'.$id.'" rows="5" cols="50"'.$style.$class.'>'.$html.'</textarea>';
    	}
    	
    	return $content;
    }
    
    function getPoweredByLink() {
        $poweredby = '<div style="text-align: center; width: 100%; font-family: arial; font-size:9px; font-color: #ccc;">' . JText::_('POWERED_BY') . ' <a href="http://www.corejoomla.com" alt="http://www.corejoomla.com">Community Quiz</a></div>';
        return $poweredby;
    }
}
?>