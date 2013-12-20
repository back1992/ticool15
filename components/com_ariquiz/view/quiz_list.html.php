<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

$quizList = $processPage->getVar('quizList');
$option = $processPage->getVar('option');
$Itemid = $processPage->getVar('Itemid');
?>

<?php
if (!empty($quizList))
{
?>
<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
<?php
	$prevCategoryId = -1;
	foreach ($quizList as $quiz)
	{
		if ($quiz->CategoryId != $prevCategoryId)
		{
?>
	<tr>
		<th class="sectiontableheader"><?php echo empty($quiz->CategoryId) ? AriWebHelper::translateResValue('Category.Uncategory') : AriWebHelper::translateDbValue($quiz->CategoryName); ?></th>
	</tr>
<?php
		}

		$link = AriJoomlaBridge::getLink('index.php?option=' . $option . '&task=quiz&quizId=' . $quiz->QuizId . '&Itemid=' . $Itemid, false, false);
?>
	<tr>
		<td>
			<a href="<?php echo $link; ?>"><?php AriWebHelper::displayDbValue($quiz->QuizName); ?></a>
		</td>
	</tr>
<?php
		$prevCategoryId = $quiz->CategoryId;
	}
?>
</table>
<?php
}
else
{
	AriWebHelper::displayResValue('Label.NotItemsFound');
}
?>