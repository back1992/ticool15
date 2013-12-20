<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: helper.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityQuizHelper {
    function &getConfig($rebuild=false) {
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
    
	public static function addSubmenu($vName, $status){
		JSubMenuHelper::addEntry(
			JText::_('COM_COMMUNITYQUIZ_CONTROL_PANEL'),
			'index.php?option=com_communityquiz&amp;view=cpanel',
			$vName == 'cpanel'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_COMMUNITYQUIZ_QUIZZES'),
			'index.php?option=com_communityquiz&amp;view=quiz',
			(($vName == 'quiz') && ($status == 0))
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_COMMUNITYQUIZ_APPROVAL'),
			'index.php?option=com_communityquiz&amp;view=quiz&status=3',
			(($vName == 'quiz') && ($status == 3))
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_COMMUNITYQUIZ_CATEGORIES'),
			'index.php?option=com_communityquiz&amp;view=categories',
			$vName == 'categories'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_COMMUNITYQUIZ_CONFIGURATION'),
			'index.php?option=com_communityquiz&amp;view=config',
			$vName == 'config'
		);
	}
	
	function load_editor($id, $name, $html, $rows, $cols, $width=null, $height=null, $class=null, $style=null){
		$config = CommunityQuizHelper::getConfig();

		$style = $style ? ' style="'.$style.'"' : '';
		$class = $class ? ' class="'.$class.'"' : '';
		$width = $width ? $width : '450px';
		$height = $height ? $height : '200px';

		$content = '';
		if( empty($html) ) $html = '';
	  
		if($config[CQ_DEFAULT_EDITOR] == 'bbcode'){
			$content = '<style type="text/css"><!-- .markItUpHeader ul { margin: 0; } .markItUpHeader ul li	{ list-style:none; float:left; position:relative; background: none;	line-height: 100%; margin: 0; padding: 0; } --></style>';
			$content .= '<div style="width: '.$width.';"><textarea name="'.$name.'" id="'.$id.'" rows="5" cols="50"'.$style.$class.'>'.$html.'</textarea></div>';
			$document = JFactory::getDocument();
			$document->addScript(JURI::root(true). '/components/'.Q_APP_NAME.'/assets/markitup/jquery.markitup.js');
			$document->addScript(JURI::root(true). '/components/'.Q_APP_NAME.'/assets/markitup/sets/bbcode/set.js');
			$document->addStyleSheet(JURI::root(true). '/components/'.Q_APP_NAME.'/assets/markitup/skins/markitup/style.css');
			$document->addStyleSheet(JURI::root(true). '/components/'.Q_APP_NAME.'/assets/markitup/sets/bbcode/style.css');
			$document->addScriptDeclaration('jQuery(document).ready(function($){$("#'.$id.'").markItUp(myBBCodeSettings)});;');
		}else{
			$editor =& JFactory::getEditor();
			$content = '<div style="overflow: hidden; clear: both;">'.$editor->display( $id, $html, $width, $height, $cols, $rows ).'</div>';
		}

		return $content;
	}

    function escape($var) {
        return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
    }

    function usersGroups($id, $name, $value) {
    	$groups = array();
    	if(APP_VERSION != '1.5'){
	    	$db = JFactory::getDbo();
	    	$query = 'SELECT CONCAT( REPEAT(\'..\', COUNT(parent.id) - 1), node.title) as text, node.id as value'
	    		. ' FROM #__usergroups AS node, #__usergroups AS parent'
	    		. ' WHERE node.lft BETWEEN parent.lft AND parent.rgt'
	    		. ' GROUP BY node.id'
	    		. ' ORDER BY node.lft';
	    	
	    	$db->setQuery($query);
	    	$groups = $db->loadObjectList();
    	}else{
    		$acl	=& JFactory::getACL();
    		$groups	= $acl->get_group_children_tree( null, 'USERS', false );
    	}
    	
        $attribs	= ' ';
        $attribs	.= 'size="'.count($groups).'"';
        $attribs	.= 'class="inputbox"';
        $attribs	.= 'multiple="multiple"';
        return JHTML::_('select.genericlist', $groups, $name, $attribs, 'value', 'text', $value, $id );
    }

    function filterData($data) {
        // Filter settings
        jimport('joomla.application.component.helper');
        $config = JComponentHelper::getParams('com_content');
        $user = &JFactory::getUser();
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
    }

    function checkUpdate() {
        $url = 'http://www.corejoomla.com/extensions.xml';
        $data = '';
        $check = array();
        $check['connect'] = 0;
        $check['current_version'] = CQ_CURR_VERSION;

        //try to connect via cURL
        if(function_exists('curl_init') && function_exists('curl_exec')) {
            $ch = @curl_init();

            @curl_setopt($ch, CURLOPT_URL, $url);
            @curl_setopt($ch, CURLOPT_HEADER, 0);
            //http code is greater than or equal to 300 ->fail
            @curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //timeout of 5s just in case
            @curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $data = @curl_exec($ch);

            @curl_close($ch);
        }

        //try to connect via fsockopen
        if(function_exists('fsockopen') && $data == '') {

            $errno = 0;
            $errstr = '';

            //timeout handling: 5s for the socket and 5s for the stream = 10s
            $fsock = @fsockopen("www.corejoomla.com", 80, $errno, $errstr, 5);

            if ($fsock) {
                @fputs($fsock, "GET /extensions.xml HTTP/1.1\r\n");
                @fputs($fsock, "HOST: www.corejoomla.com\r\n");
                @fputs($fsock, "Connection: close\r\n\r\n");

                //force stream timeout...
                @stream_set_blocking($fsock, 1);
                @stream_set_timeout($fsock, 5);

                $get_info = false;
                while (!@feof($fsock)) {
                    if ($get_info) {
                        $data .= @fread($fsock, 10240);
                    }
                    else {
                        if (@fgets($fsock, 10240) == "\r\n") {
                            $get_info = true;
                        }
                    }
                }
                @fclose($fsock);

                //need to check data cause http error codes aren't supported here
                if(!strstr($data, '<?xml version="1.0" encoding="utf-8" ?><update>')) {
                    $data = '';
                }
            }
        }

        //try to connect via fopen
        if (function_exists('fopen') && ini_get('allow_url_fopen') && $data == '') {

            //set socket timeout
            ini_set('default_socket_timeout', 5);

            $handle = @fopen ($url, 'r');

            //set stream timeout
            @stream_set_blocking($handle, 1);
            @stream_set_timeout($handle, 5);

            $data	= @fread($handle, 10240);

            @fclose($handle);
        }

        if( !empty($data) && strstr($data, '<?xml version="1.0" encoding="utf-8" ?>') ) {
			$xml = new SimpleXMLElement($data);
            foreach($xml->extension as $extension){
            	if($extension['name'] == Q_APP_NAME && $extension['jversion'] == '1.7'){
		            $check['version']		= $extension->version;
		            $check['released']		= $extension->released;
		            $check['changelog']		= $extension->changelog;
		            $check['status']		= version_compare( $check['current_version'], $check['version'] );
		            $check['connect']		= 1;
		            break;
            	}
            }
        }

        return $check;
    }
    
    function process_html($content){
    	$config = CommunityQuizHelper::getConfig();
    	if($config[CQ_DEFAULT_EDITOR] == 'bbcode'){
			require_once JPATH_ROOT.DS.'components'.DS.Q_APP_NAME.DS.'helpers'.DS.'markitup.bbcode-parser.php';
			$content = BBCode2Html($content);
    	}
    	return $content;
    }
}
?>