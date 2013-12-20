<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

$results = $processPage->getVar('results');
$option =  $processPage->getVar('option');
$Itemid = $processPage->getVar('Itemid');
$option = $processPage->getVar('option');
$mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$jsAdminPath = $mosConfig_live_site . '/components/' . $option . '/js/';
$jsYuiPath = $jsAdminPath . 'yui/';
$dataTable = $processPage->getVar('dataTable');
$messagesLink = AriJoomlaBridge::getLink('index.php?option=' . $option . '&task=script.messages&t=' . time());
?>
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" />

<script type="text/javascript" src="<?php echo $jsAdminPath; ?>date.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datatable/datatable-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/paginator/paginator-min.js"></script>

<script charset="utf-8" src="<?php echo $jsAdminPath; ?>ari.all.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<script charset="utf-8" src="<?php echo $jsAdminPath; ?>ari.quiz.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<script type="text/javascript">
	YAHOO.ARISoft.page.baseUrl = '<?php echo $mosConfig_live_site . '/'; ?>';
	YAHOO.ARISoft.page.adminBaseUrl = '<?php echo $mosConfig_live_site . '/administrator/'; ?>';
	YAHOO.ARISoft.page.option = '<?php echo $option; ?>';
</script>
<script charset="utf-8" src="<?php echo $messagesLink; ?>" type="text/javascript"></script>

<div class="componentheading"><?php AriWebHelper::displayResValue('Title.QuizResultList'); ?></div>
<?php
	$dataTable->render();
?>