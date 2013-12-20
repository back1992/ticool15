<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

$result = $processPage->getVar('result');
$resultText = $processPage->getVar('resultText');
$option = $processPage->getVar('option');
$ticketId = $processPage->getVar('ticketId');
$infoMsg = $processPage->getVar('infoMsg');
//$printVisible = $processPage->getVar('printVisible');
$printVisible = FALSE;
$emailVisible = $processPage->getVar('emailVisible');
$version = $processPage->getVar('version');
$cssFile = $processPage->getVar('cssFile');
$mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$jsAdminPath = $mosConfig_live_site . '/components/' . $option . '/js/';
$jsYuiPath = $jsAdminPath . 'yui/';
$mosConfig_absolute_path = $processPage->getVar('mosConfig_absolute_path');
$messagesLink = AriJoomlaBridge::getLink('index.php?option=' . $option . '&task=script.messages&t=' . time());
$dataTable = $processPage->getVar('dataTable');
$isStatisticsShow = !empty($dataTable);
?>

<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" />

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

    YAHOO.util.Get.css('<?php echo $cssFile; ?>');
</script>
<script charset="utf-8" src="<?php echo $messagesLink; ?>" type="text/javascript"></script>
<form method="post" action="" style="margin: 4px 4px 4px 4px;">
    <?php
    if (!empty($infoMsg)) {
        ?>
        <h3 align="center"><?php echo $infoMsg; ?></h3>
        <?php
    }
    ?>
    <?php
    if ($emailVisible) {
        ?>
        <input type="submit" name="ariEvent[email]" class="button" value="<?php AriWebHelper::displayResValue('Label.Email'); ?>"  />
        <?php
    }
    ?>
    <?php
    if ($printVisible) {
        ?>
        <input type="button" class="button" value="<?php AriWebHelper::displayResValue('Label.Print'); ?>" onclick="window.open('index2.php?option=<?php echo $option; ?>&task=quiz_finished$print&ticketId=<?php echo $ticketId; ?>&tmpl=component', 'blank');" />
        <?php
    }
    ?>
    <br/><br/>
    <?php AriWebHelper::displayDbValue($resultText, false); ?>
    <br/><br/>
    <?php
    if ($emailVisible) {
        ?>
        <input type="submit" name="ariEvent[email]" class="button" value="<?php AriWebHelper::displayResValue('Label.Email'); ?>"  />
        <?php
    }
    ?>
    <?php
    if ($printVisible) {
        ?>
        <input type="button" class="button" value="<?php AriWebHelper::displayResValue('Label.Print'); ?>" onclick="window.open('index2.php?option=<?php echo $option; ?>&task=quiz_finished$print&ticketId=<?php echo $ticketId; ?>&tmpl=component', 'blank');" />
        <?php
    }
    ?>
    <input type="hidden" name="task" value="quiz_finished" />
    <input type="hidden" name="ticketId" value="<?php echo $ticketId; ?>" />
    <input type="hidden" name="option" value="<?php echo $option; ?>" />
</form>

<?php
if ($isStatisticsShow)
    $dataTable->render();
?>