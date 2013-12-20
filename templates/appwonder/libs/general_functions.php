<?php
/* determines menu output per admin selection */
if (($s5_menu  == "1") || ($s5_menu  == "3") || ($s5_menu  == "4")){ 
	if ($s5_jsmenu == "s5") { 
		require("libs/s5_no_moo_menu.php");
		$menu_name = $this->params->get ("xml_menuname");
	}
	else if ($s5_jsmenu == "jq")  {
		require("s5_suckerfish.php");
		$menu_name = $this->params->get ("xml_menuname");
	}
}

else if ($s5_menu  == "2")  {
	require("s5_suckerfish.php");
	$menu_name = $this->params->get ("xml_menuname");
}


/* pulls from admin if URL entered in admin area */
if ($s5_urlforSEO  == ""){ 
$LiveSiteUrl = JURI::root();}
if ($s5_urlforSEO  != ""){ 
$LiveSiteUrl = "$s5_urlforSEO/";}

/* template directory URL used index.php */
$s5_template_name = $this->template;
$s5_directory_path = $LiveSiteUrl."templates/".$s5_template_name;
$s5_multibox_path = "templates/".$s5_template_name."/js/multibox/";
?>
<script type="text/javascript">
var s5_multibox_path = "<?php echo $s5_multibox_path ?>";
</script>
<?php

/* If browser calls */
$br = strtolower($_SERVER['HTTP_USER_AGENT']);
$browser = "other";

if(strrpos($br,"msie 6") > 1) {
$browser = "ie6";} 

if(strrpos($br,"msie 7") > 1) {
$browser = "ie7";} 

if(strrpos($br,"msie 8") > 1) {
$browser = "ie8";} 

if(strrpos($br,"msie 9") > 1) {
$browser = "ie9";} 

if(strrpos($br,"opera") > 1) {
$browser = "opera";} 


/* Hides frontpage component area when enabled in admin */
$s5_domain = $_SERVER['HTTP_HOST'];
$s5_url = "http://" . $s5_domain . $_SERVER['REQUEST_URI'];

$s5_frontpage = "yes";
$s5_current_page = "";
if (JRequest::getVar('view') == "frontpage") {
	$s5_current_page = "frontpage";
}
if (JRequest::getVar('view') != "frontpage") {
	$s5_current_page = "not_frontpage";
}
$s5_check_frontpage = strrpos($s5_url,"index.php");
if ($s5_check_frontpage > 1) {
	$s5_current_page = "not_frontpage";
}
$s5_check_frontpage2 = strrpos($s5_url,"view=frontpage&Itemid=1");
if ($s5_check_frontpage2 > 1) {
	$s5_current_page = "frontpage";
}
if ($s5_show_frontpage == "no" && $s5_current_page == "frontpage") {
	$s5_frontpage = "no";
}

?>