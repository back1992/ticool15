<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');

// Tab General
$general_settings = array(
	'title'=>'GENERAL_SETTINGS',
	'elements'=>array(
		array('name'=>CQ_DEFAULT_TEMPLATE, 'type'=>'select', 'values'=>array('default'), 'labels'=>array('Default')),
		array('name'=>CQ_DEFAULT_EDITOR, 'type'=>'select', 'values'=>array('default','bbcode'), 'labels'=>array('LBL_DEFAULT','LBL_BBCODE')),
		array('name'=>CQ_LIST_LIMIT, 'type'=>'text'),
		array('name'=>CQ_ENABLE_MODERATION, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_ENABLE_RATINGS, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_FILTERED_KEYWORDS, 'type'=>'textarea')
	)
);

$display_settings = array(
	'title'=>'DISPLAY_SETTINGS',
	'elements'=>array(
		array('name'=>CQ_USER_NAME, 'type'=>'select', 'values'=>array('name','username'), 'labels'=>array('LBL_NAME','LBL_USERNAME')),
		array('name'=>CQ_USER_AVTAR, 'type'=>'select', 'values'=>array('none','gravatar','cb','jomsocial','kunena','aup','touch'), 'labels'=>array('OPTION_NONE','OPTION_GRAVATAR','OPTION_CB','OPTION_JOMSOCIAL','OPTION_KUNENA','OPTION_AUP','OPTION_MIGHTY_TOUCH')),
		array('name'=>CQ_AVATAR_SIZE, 'type'=>'text'),
		array('name'=>CQ_SHOW_AVATAR_IN_LISTING, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_TOOLBAR_BUTTONS, 'type'=>'text'),
		array('name'=>CQ_HIDE_TEMPLATE, 'type'=>'select', 'values'=>array('0','1','2'), 'labels'=>array('LBL_USER_SELECTABLE','LBL_FORCE_HIDE','LBL_FORCE_SHOW')),
		array('name'=>CQ_ENABLE_CATEGORY_BOX, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_CLEAN_HOME_PAGE, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_ENABLE_POWERED_BY, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO'))
	)
);

$notification_settings = array(
	'title'=>'EMAIL_NOTIFICATION_SETTINGS',
	'elements'=>array(
		array('name'=>CQ_NOTIF_SENDER_NAME, 'type'=>'text'),
		array('name'=>CQ_NOTIF_SENDER_EMAIL, 'type'=>'text'),
		array('name'=>CQ_NOTIF_ADMIN_EMAIL, 'type'=>'text'),
		array('name'=>CQ_NOTIF_NEW_QUIZ, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_NOTIF_NEW_RESPONSE, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO'))
	)
);

$activity_settings = array(
	'title'=>'ACTIVITY_STREAM_SETTINGS',
	'elements'=>array(
		array('name'=>CQ_ACTIVITY_STREAM_TYPE, 'type'=>'select', 'values'=>array('none','jomsocial','touch'), 'labels'=>array('OPTION_NONE','OPTION_JOMSOCIAL','OPTION_MIGHTY_TOUCH')),
		array('name'=>CQ_STREAM_NEW_QUIZ, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO')),
		array('name'=>CQ_STREAM_NEW_RESPONSE, 'type'=>'select', 'values'=>array('1','0'), 'labels'=>array('LBL_YES','LBL_NO'))
	)
);

$points_system_settings = array(
	'title'=>'POINTS_SYSTEM_SETTINGS',
	'elements'=>array(
		array('name'=>CQ_POINTS_SYSTEM, 'type'=>'select', 'values'=>array('none','jomsocial','touch','aup'), 'labels'=>array('OPTION_NONE','OPTION_JOMSOCIAL','OPTION_MIGHTY_TOUCH','OPTION_AUP')),
		array('name'=>CQ_TOUCH_POINTS_NEW_QUIZ, 'type'=>'text'),
		array('name'=>CQ_TOUCH_POINTS_NEW_RESPONSE, 'type'=>'text')
	)
);

$guest_permission_settings = array(
	'title'=>'LBL_GUEST_PERMISSIONS',
	'elements'=>array(
		array('name'=>CQ_PERM_GUEST_BROWSE, 'type'=>'checkbox'),
		array('name'=>CQ_PERM_GUEST_RESPONSE, 'type'=>'checkbox')
	)
);

$permission_settings = array(
	'title'=>'PERMISSION_SETTINGS', 
	'elements'=>array(
		array('name'=>CQ_PERM_COMPONENT_ACCESS, 'type'=>'permissions'),
		array('name'=>CQ_PERM_CREATE_QUIZ, 'type'=>'permissions'),
		array('name'=>CQ_PERM_EDIT_QUIZ, 'type'=>'permissions'),
		array('name'=>CQ_PERM_SUBMIT_ANSWER, 'type'=>'permissions'),
		array('name'=>CQ_PERM_WYSIWYG, 'type'=>'permissions'),
		array('name'=>CQ_PERM_MANAGE, 'type'=>'permissions')
	)
);

$tab_group_general = array('name'=>'general_settings', 'title'=>'TAB_GENERAL', 'groups'=>array($general_settings, $display_settings));
$tab_notification = array('name'=>'notification_settings', 'title'=>'TAB_NOTIFICATION', 'groups'=>array($notification_settings)); 
$tab_third_party = array('name'=>'thirdparty_settings', 'title'=>'TAB_THIRD_PARTY', 'groups'=>array($activity_settings, $points_system_settings));
$tab_permissions = array('name'=>'permission_settings', 'title'=>'TAB_PERMISSIONS', 'groups'=>array($guest_permission_settings, $permission_settings));

$configuration = array($tab_group_general, $tab_notification, $tab_third_party);
if(APP_VERSION == '1.5'){
	$configuration[] = $tab_permissions;
}

JPluginHelper::importPlugin( 'corejoomla' );
$dispatcher =& JDispatcher::getInstance();
$dispatcher->trigger('onCallIncludeJQuery', array(array("jquery","jqueryui")));

$document = JFactory::getDocument();
$document->addScriptDeclaration('function resetPermissionOptions(select){ selectBox = document.getElementById(select); selectBox.selectedIndex = -1; }');
$document->addScriptDeclaration('jQuery(document).ready(function($){jQuery("#config-document").tabs();});');
$config = CommunityQuizHelper::getConfig();
?>
<form action="index.php?option=com_communityanswers" method="post" name="adminForm">
<div id="config-document">
	<ul>
		<?php foreach ($configuration as $tab):?>
		<li><a href="#<?php echo $tab['name'];?>"><?php echo JText::_($tab['title']);?></a></li>
		<?php endforeach;;?>
	</ul>
	<?php 
	foreach ($configuration as $tab){
		echo '<div id="'.$tab['name'].'">';
		foreach ($tab['groups'] as $group){
			echo '<fieldset><legend>'.JText::_($group['title']).'</legend><table class="admintable">';
			foreach ($group['elements'] as $element){
				switch ($element['type']){
					case 'text':
						echo '<tr><td class="formelement">';
						echo '<label for="'.$element['name'].'"><span class="editlinktip hasTip" title="'.JText::_('LBL_'.$element['name'].'_DESC').'">'.JText::_('LBL_'.$element['name']).'</span></label>';
						echo '</td><td>';
						echo '<input type="text" id="'.$element['name'].'" name="'.$element['name'].'" size="25" value="'.$config[$element['name']].'">';
						echo '</td></tr>';
						break;
					case 'textarea':
						echo '<tr><td class="formelement">';
						echo '<label for="'.$element['name'].'"><span class="editlinktip hasTip" title="'.JText::_('LBL_'.$element['name'].'_DESC').'">'.JText::_('LBL_'.$element['name']).'</span></label>';
						echo '</td><td>';
						echo '<textarea cols="40" rows="4" id="'.$element['name'].'" name="'.$element['name'].'" size="25">'.$config[$element['name']].'</textarea>';
						echo '</td></tr>';
						break;
					case 'password':
						echo '<tr><td class="formelement">';
						echo '<label for="'.$element['name'].'"><span class="editlinktip hasTip" title="'.JText::_('LBL_'.$element['name'].'_DESC').'">'.JText::_('LBL_'.$element['name']).'</span></label>';
						echo '</td><td>';
						echo '<input type="password" id="'.$element['name'].'" name="'.$element['name'].'" size="25" value="*****">';
						echo '</td></tr>';
						break;
					case 'select':
						echo '<tr><td class="formelement">';
						echo '<label for="'.$element['name'].'"><span class="editlinktip hasTip" title="'.JText::_('LBL_'.$element['name'].'_DESC').'">'.JText::_('LBL_'.$element['name']).'</span></label>';
						echo '</td><td>';
						echo '<select id="'.$element['name'].'" name="'.$element['name'].'" size="1">';
						foreach ($element['values'] as $i=>$value){
							echo '<option value="'.$value.'"'.($config[$element['name']] == $value ? ' selected="selected"':'').'>'.JText::_($element['labels'][$i]).'</option>';
						}
						echo '</select></td></tr>';
						break;
					case 'checkbox':
						echo '<tr><td>';
						echo '<input type="checkbox" id="'.$element['name'].'" name="'.$element['name'].'" size="25" value="1"'.($config[$element['name']] == '1' ? ' checked="checked"':'').'>';
						echo '</td><td class="formelement">';
						echo '<label for="'.$element['name'].'"><span class="editlinktip hasTip" title="'.JText::_('LBL_'.$element['name'].'_DESC').'">'.JText::_('LBL_'.$element['name']).'</span></label>';
						echo '</td></tr>';
						break;
					case 'checkboxes':
						break;
					case 'permissions':
						echo '<tr><td class="formelement">';
						echo '<label for="'.$element['name'].'"><span class="editlinktip hasTip" title="'.JText::_('LBL_'.$element['name'].'_DESC').'">'.JText::_('LBL_'.$element['name']).'</span></label>';
						echo '</td><td>';
						echo CommunityQuizHelper::usersGroups($element['name'],$element['name'].'[]',explode(',', $config[$element['name']]));
						echo '</td></tr>';
						break;
				}
			}
			echo '</table></fieldset>';
		}
		echo '</div>';
	}
	?>
</div>
<input type="hidden" name="option" value="<?php echo Q_APP_NAME;?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="config" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>