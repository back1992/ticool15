<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

$mid = $processPage->getVar('mid');
$rurl = $processPage->getVar('rurl');
$Itemid = $processPage->getVar('Itemid');
?>

<form action="<?php echo AriJoomlaBridge::getLink($rurl); ?>" method="post">
<?php AriWebHelper::displayResValue($mid); ?>
<br /><br />
<input type="submit" value="<?php AriWebHelper::displayResValue('Label.Continue'); ?>" class="button" />
<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
</form>