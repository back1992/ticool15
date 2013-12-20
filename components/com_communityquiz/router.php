<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: router.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Function to convert a system URL to a SEF URL
 */
function CommunityQuizBuildRoute(&$query) {
    static $items;

    $segments	= array();
    if(isset($query['task'])) {
        $segments[] = $query['task'];
        unset($query['task']);
    }
    if(isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
    if(isset($query['catid'])) {
        $segments[] = $query['catid'];
        unset($query['catid']);
    }
	unset($query['view']);
    return $segments;
}
/*
 * Function to convert a SEF URL back to a system URL
 */
function CommunityQuizParseRoute($segments) {
    $vars = array();
    if(count($segments) > 0){
        $vars['task']	= $segments[0];
    }
    if(count($segments) > 1) {
        $vars['id']     = $segments[1];
        $vars['catid']  = $segments[1];
    }

    return $vars;
}
?>