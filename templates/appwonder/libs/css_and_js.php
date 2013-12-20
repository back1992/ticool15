<link rel="stylesheet" href="<?php echo $LiveSiteUrl ?>templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $LiveSiteUrl ?>templates/system/css/general.css" type="text/css" />

<link href="<?php echo $s5_directory_path ?>/css/template_default.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $s5_directory_path ?>/css/template.css" rel="stylesheet" type="text/css" media="screen" />

<?php if($mobile==true){ ?>
<link href="<?php echo $s5_directory_path ?>/css/mobile_device.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>

<link href="<?php echo $s5_directory_path ?>/css/com_content.css" rel="stylesheet" type="text/css" media="screen" />

<?php if($mobile==false){ ?>
<link href="<?php echo $s5_directory_path ?>/css/s5_suckerfish.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>

<link href="<?php echo $s5_directory_path ?>/css/editor.css" rel="stylesheet" type="text/css" media="screen" />

<?php if($s5_thirdparty == "enabled") { ?>
<link href="<?php echo $s5_directory_path ?>/css/thirdparty.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>

<?php if ($browser == "ie7" || $browser == "ie8") { ?>
<link href="<?php echo $s5_directory_path ?>/css/IECSS3.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>

<?php if($s5_language_direction == "1") { ?>
<link href="<?php echo $s5_directory_path ?>/css/template_rtl.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $s5_directory_path ?>/css/editor_rtl.css" rel="stylesheet" type="text/css" media="screen" />
<?php if($mobile==true){ ?>
<link href="<?php echo $s5_directory_path ?>/css/mobile_device_rtl.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>
<?php } ?>

<?php if(($s5_fonts != "Arial") || ($s5_fonts != "Helvetica")|| ($s5_fonts != "Sans-Serif")) { ?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo $s5_fonts;?>" />
<?php } ?>



<?php if ($s5_multibox  == "yes" || $s5_scrolltotop  == "yes") { 
s5_mootools_call();
} ?>

<?php if ($s5_multibox  == "yes") { ?>
<link href="<?php echo $s5_directory_path ?>/css/multibox/multibox.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $s5_directory_path ?>/css/multibox/ajax.css" rel="stylesheet" type="text/css" media="screen" />
<?php if ($s5_moover  == "moo112") { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/overlay.js"></script>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/multibox.js"></script>
<?php } ?>
<?php if ($s5_moover  == "moo124") { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/mootools124/overlay.js"></script>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/mootools124/multibox.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/multibox/AC_RunActiveContent.js"></script>
<?php } ?>


<?php if ($s5_jsmenu == "jq" && $mobile==false) { ?>
<?php if (($s5_menu  == "1") || ($s5_menu  == "3") || ($s5_menu  == "4")) { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/jquery13.js"></script>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/jquery_no_conflict.js"></script>
<script type="text/javascript">
<?php if ($s5_menu  == "3") { ?>
var s5_fading_menu = "yes";
<?php } ?>
<?php if ($s5_menu  != "3") { ?>
var s5_fading_menu = "no";
<?php } ?>
function s5_jqmainmenu(){
jQuery(" #navlist ul ").css({display: "none"}); // Opera Fix
jQuery(" #s5_navv li").hover(function(){
		jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).<?php if ($s5_menu  == "1") { ?>show(400)<?php } ?><?php if ($s5_menu  == "3") { ?>fadeIn(400)<?php } ?><?php if ($s5_menu  == "4") { ?>slideDown(400)<?php } ?>;
		},function(){jQuery(this).find('ul:first').css({visibility: "hidden"});	});}
  jQuery(document).ready(function(){ s5_jqmainmenu();});
</script>
<?php } ?>
<?php } ?>

<link href="<?php echo $s5_directory_path ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />

<?php if($browser == "ie7" && $s5_menu != "5" && $mobile==false) { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/IEsuckerfish.js"></script>
<?php } ?>

<?php if($s5_font_resizer == "yes" && $mobile==false) { ?>
<script type="text/javascript" src="<?php echo $s5_directory_path ?>/js/s5_font_adjuster.js"></script>
<?php } ?>

