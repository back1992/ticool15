<?php $fullResults = $processPage->getVar('fullResults');
$mosConfig_absolute_path = $processPage->getVar('mosConfig_absolute_path');
$mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$cssFile = $processPage->getVar('cssFile');
$option = $processPage->getVar('option');
if (is_array($fullResults) && count($fullResults) > 0) {
    ?> <link rel="stylesheet" type="text/css" href="<?php echo $cssFile; ?>" />
    <table cellpadding="3" cellspacing="3" class="ariQuizResults" align="center"> <?php foreach ($fullResults as $item) {
                $isCorrect = ($item->MaxScore == $item->Score); ?> <tr class="ariQuizQueInfo">
                <td rowspan="2" class="ariQuizIndex"><?php echo ($item->QuestionIndex + 1); ?>.</td> <td class="ariQuizQuestionCnt"><div class="ariQuizQuestionTitle"><?php AriWebHelper::displayResValue('Label.Question'); ?></div></td>
                <td class="ariQuizQuestion"><?php AriWebHelper::displayDbValue($item->Question, false); ?></td> </tr> <tr class="ariQuizAnsInfo">
                <td><div class="<?php echo $isCorrect ? 'ariQuizAnsCorrect' : 'ariQuizAnsIncorrect'; ?>"><?php AriWebHelper::displayResValue($isCorrect ? 'Label.Correct' : 'Label.Incorrect'); ?></div></td> <td> <?php $path = $mosConfig_absolute_path . '/components/' . $option . '/view/stats/stats1/' . strtolower($item->ClassName) . '.html.php';
                $queItem = $item;
                if (file_exists($path)) include($path); ?> </td> </tr> <tr class="ariQuizSplitter">
                <td colspan="3"> <hr /> </td> </tr> <?php } ?> </table> <?php } ?>