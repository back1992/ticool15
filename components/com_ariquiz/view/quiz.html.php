<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

$version = $processPage->getVar('version');
$quiz = $processPage->getVar('quiz');
$ticketId = $processPage->getVar('ticketId');
$option = $processPage->getVar('option');
$task = $processPage->getVar('task');
$cssFile = $processPage->getVar('cssFile');
$canTakeQuiz = $processPage->getVar('canTakeQuiz');
$errorMessage = $processPage->getVar('errorMessage');
$Itemid = $processPage->getVar('Itemid');
$mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$jsAdminPath = $mosConfig_live_site . '/components/' . $option . '/js/';
$jsYuiPath = $jsAdminPath . 'yui/';
$messagesLink = AriJoomlaBridge::getLink('index.php?option=' . $option . '&task=script.messages&t=' . time());
$takeQuizTask = $processPage->getVar('takeQuizTask');
$tmpl = AriRequest::getParam('tmpl');
$my = $processPage->getVar('my');
$showInfoArea = ($quiz->Anonymous != 'Yes' && !$my->get('id'));
?>

<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script>
<script charset="utf-8" src="<?php echo $jsAdminPath; ?>ari.all.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<script charset="utf-8" src="<?php echo $jsAdminPath; ?>ari.quiz.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<script type="text/javascript">
	YAHOO.ARISoft.page.option = '<?php echo $option; ?>';
	YAHOO.util.Get.css('<?php echo $cssFile; ?>');
</script>
<script charset="utf-8" src="<?php echo $messagesLink; ?>" type="text/javascript"></script>

<div style="margin: 4px 4px 4px 4px;">
	<form action="index.php" method="post">
	<?php
		if ($showInfoArea):
	?>
	<fieldset style="width: 400px;">
		<legend><?php AriWebHelper::displayResValue('Label.Information'); ?></legend>
		<table cellpadding="3" cellspacing="3" border="0">
			<tr>
				<td nowrap="nowrap"><?php AriWebHelper::displayResValue('Label.Name'); ?> : </td>
				<td><?php $processPage->renderControl('tbxGuestName', array('class' => 'inputbox', 'size' => 50)); ?></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><?php AriWebHelper::displayResValue('Label.Email'); ?> : </td>
				<td><?php $processPage->renderControl('tbxGuestMail', array('class' => 'inputbox', 'size' => 50)); ?></td>
			</tr>
		</table>
	</fieldset>
	<?php
		endif;
	?>
	<h1 align="center"><?php AriWebHelper::displayDbValue($quiz->QuizName); ?></h1><br />
	<?php AriWebHelper::displayDbValue($quiz->Description, false); ?>
	<br /><br />
<?php
	if ($canTakeQuiz)
	{
?>
	<input type="submit" onclick="return aris.validators.alertSummaryValidators.validate();" class="button" value="<?php AriWebHelper::displayResValue('Label.Continue'); ?>" />
<?php
	}
	else if ($errorMessage)
	{
?>
	<div class="ariQuizErrorMessage">
		<?php AriWebHelper::displayResValue($errorMessage); ?>
	</div>
<?php
	}

	if ($tmpl)
	{
?>
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
<?php
	}
?>
	<input type="hidden" name="task" value="<?php echo $takeQuizTask; ?>" />
	<input type="hidden" name="quizId" value="<?php echo $quiz->QuizId; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
	</form>
</div>