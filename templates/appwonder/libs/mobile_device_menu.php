<?php

class s5_mobile_menu_helper
{
  function get_s5_mobile_menu($s5_mobile_device_menu_title,$menu_name,$s5_mobile_device_menu_subs)
    {
    global $mainframe;
	
	$user =& JFactory::getUser();
	$database 	=& JFactory::getDBO();
    $menu_name =  $menu_name;
	$startline = $s5_mobile_device_menu_title;
	if (true ) {
	
	if ($s5_mobile_device_menu_subs == "first") {
 	$query = "SELECT * FROM #__menu "
	. " WHERE menutype = '". $menu_name ."' "
	. " AND published = '1' AND parent = '0' "
	. " ORDER BY name ASC";
	}
	
	if ($s5_mobile_device_menu_subs == "all") {
 	$query = "SELECT * FROM #__menu "
	. " WHERE menutype = '". $menu_name ."' "
	. " AND published = '1' AND parent != '100000' "
	. " ORDER BY name ASC";
	}

 $database->setQuery( $query );
 
 $mymenu_content ="<form name='Lnk'><select id='s5_mobile_menu' name='s5_mobile_menu' class='inputbox' 
		onchange='javascript:location.href=document.Lnk.s5_mobile_menu.options[document.Lnk.s5_mobile_menu.selectedIndex].value;' >
        <option id='s5_md_menu_active'>-- ".$startline."  --</option>";

 if ($mymenu_rows = $database->loadObjectList() ) {
 
	foreach($mymenu_rows as $mymenu_row) {
		
		if ($mymenu_row->type != "separator" && $mymenu_row->link != "javascript:;") {
			if ($mymenu_row->access <= 0) {
				$mymenu_content .= " <option value=\"".$mymenu_row->link."\" >$mymenu_row->name</option>\r";
			}
			if ($mymenu_row->access > 0 && $user->gid > 0) {
				$mymenu_content .= " <option value=\"".$mymenu_row->link."\" >$mymenu_row->name</option>\r";
			}
		}
    }

    $mymenu_content .= "</select></form>";
        return $mymenu_content ;
    }

}
}
}

?>


