<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

$category = $processPage->getVar('category');
$quizList = $processPage->getVar('quizList');
$option = $processPage->getVar('option');
$Itemid = $processPage->getVar('Itemid');
?>

<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
	<tr>
		<th class="sectiontableheader"><?php echo empty($category) || empty($category->CategoryId) ? AriWebHelper::translateResValue('Category.Uncategory') : AriWebHelper::translateDbValue($category->CategoryName); ?></th>
	</tr>
<?php
	if (!empty($category->Description)):
?>
	<tr>
		<td>
			<div class="aq-cat-descr">
				<?php echo AriWebHelper::translateDbValue($category->Description, false); ?>
			</div>
		</td>
	</tr>
<?php
	endif;
	if (!empty($quizList)):
		foreach ($quizList as $quiz):
			$link = AriJoomlaBridge::getLink('index.php?option=' . $option . '&task=quiz&quizId=' . $quiz->QuizId . '&Itemid=' . $Itemid);
?>
	<tr>
		<td>
			<a href="<?php echo $link; ?>"><?php AriWebHelper::displayDbValue($quiz->QuizName); ?></a>
		</td>
	</tr>
<?php
		endforeach;
	endif;
?>
</table>
<?php
if (empty($quizList))
	AriWebHelper::displayResValue('Label.NotItemsFound');
?>