<?php
/**
 * Joomla! 1.5 component Community Polls
 *
 * @version $Id: CPAuthorization.php 2009-08-10 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Polls
 * @license GNU/GPL
 *
 * The Community Polls allows the members of the Joomla website to create and manage polls from the front-end.
 * The administrator has the powerful tools provided in the back-end to manage the polls published by all users.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CAuthorization {

    function CAuthorization($config) {
    	if(APP_VERSION == '1.5'){
	        $auth =& JFactory::getACL();
	        $app = &JFactory::getApplication();
	        $db = &JFactory::getDBO();
	
	        // View quiz
	        if(!empty($config[CQ_PERM_COMPONENT_ACCESS])){
		        $query = "select name from #__core_acl_aro_groups where id in(". $config[CQ_PERM_COMPONENT_ACCESS] . ")";
		        $db->setQuery($query);
		        $groups = $db->loadObjectList();
		        if($groups){
		            foreach($groups as $group){
		                $auth->addACL( Q_APP_NAME, 'quiz.access', 'users', $group->name, 'quiz', 'all' );
		            }
		        }
	        }
	
	        // Create questions
	        if(!empty($config[CQ_PERM_CREATE_QUIZ])){
		        $query = "select name from #__core_acl_aro_groups where id in(". $config[CQ_PERM_CREATE_QUIZ] . ")";
		        $db->setQuery($query);
		        $groups = $db->loadObjectList();
		        if($groups){
		            foreach($groups as $group){
		                $auth->addACL( Q_APP_NAME, 'quiz.create', 'users', $group->name, 'quiz', 'all' );
		            }
		        }
	        }
	
	        // Create questions
	        if(!empty($config[CQ_PERM_EDIT_QUIZ])){
		        $query = "select name from #__core_acl_aro_groups where id in(". $config[CQ_PERM_EDIT_QUIZ] . ")";
		        $db->setQuery($query);
		        $groups = $db->loadObjectList();
		        if($groups){
		            foreach($groups as $group){
		                $auth->addACL( Q_APP_NAME, 'quiz.edit', 'users', $group->name, 'quiz', 'all' );
		            }
		        }
	        }
	        
	        // Answer questions
	        if(!empty($config[CQ_PERM_SUBMIT_ANSWER])){
		        $query = "select name from #__core_acl_aro_groups where id in(". $config[CQ_PERM_SUBMIT_ANSWER] . ")";
		        $db->setQuery($query);
		        $groups = $db->loadObjectList();
		        if($groups){
		            foreach($groups as $group){
		                $auth->addACL( Q_APP_NAME, 'quiz.respond', 'users', $group->name, 'quiz', 'all' );
		            }
		        }
	        }
		        
	        // wysiwyg
	        if(!empty($config[CQ_PERM_WYSIWYG])){
		        $query = "select name from #__core_acl_aro_groups where id in(". $config[CQ_PERM_WYSIWYG] . ")";
		        $db->setQuery($query);
		        $groups = $db->loadObjectList();
		        if($groups){
		            foreach($groups as $group){
		                $auth->addACL( Q_APP_NAME, 'quiz.wysiwyg', 'users', $group->name, 'quiz', 'all' );
		            }
		        }
	        }
		        
	        // wysiwyg
	        if(!empty($config[CQ_PERM_MANAGE])){
		        $query = "select name from #__core_acl_aro_groups where id in(". $config[CQ_PERM_MANAGE] . ")";
		        $db->setQuery($query);
		        $groups = $db->loadObjectList();
		        if($groups){
		            foreach($groups as $group){
		                $auth->addACL( Q_APP_NAME, 'quiz.manage', 'users', $group->name, 'quiz', 'all' );
		            }
		        }
	        }
	
	        // Moderate questions
	        $auth->addACL( Q_APP_NAME, 'quiz.access', 'users', 'super administrator', 'quiz', 'all' );
	        $auth->addACL( Q_APP_NAME, 'quiz.create', 'users', 'super administrator', 'quiz', 'all' );
	        $auth->addACL( Q_APP_NAME, 'quiz.edit', 'users', 'super administrator', 'quiz', 'all' );
	        $auth->addACL( Q_APP_NAME, 'quiz.respond', 'users', 'super administrator', 'quiz', 'all' );
	        $auth->addACL( Q_APP_NAME, 'quiz.wysiwyg', 'users', 'super administrator', 'quiz', 'all' );
	        $auth->addACL( Q_APP_NAME, 'quiz.manage', 'users', 'super administrator', 'quiz', 'all' );
    	}
    }

    public static function authorise($action){
    	$user = &JFactory::getUser();
    	if(APP_VERSION == '1.5'){
	        $config = CommunityQuizHelper::getConfig();
	        if($user->guest){
	        	switch ($action){
	        		case "quiz.access":
	            		return $config[CQ_PERM_GUEST_BROWSE] == '1';
	        		case "quiz.respond":
	        			return $config[CQ_PERM_GUEST_RESPONSE] == '1';
	        	}
	        } else {
	        	return $user->authorize(Q_APP_NAME,$action,'quiz','all');
	        }       
    	}else{
            return $user->authorise($action, Q_APP_NAME);
        }
    }
}