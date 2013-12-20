<?php
global $mosConfig_absolute_path;

if (empty($mosConfig_absolute_path))
{
	if (defined('JPATH_SITE')) $mosConfig_absolute_path = JPATH_SITE;
}

$adminBasePath = $mosConfig_absolute_path . '/administrator/components/com_ariquiz/';
$isAriKernelLoaded = @file_exists($adminBasePath . 'kernel/class.AriKernel.php');

if ($isAriKernelLoaded)
{
require_once ($adminBasePath . 'kernel/class.AriKernel.php');

AriKernel::import('Joomla.JoomlaBridge');
AriKernel::import('Constants.ClassConstants');
AriKernel::import('Constants.ConstantsManager');
AriKernel::import('GlobalPrefs.GlobalPrefs');
AriKernel::import('Components.AriQuiz.AriQuiz');
AriKernel::import('Web.Utils.WebHelper');
AriKernel::import('Controllers.AriQuiz.ResultController');
AriKernel::import('String.String');
AriKernel::import('I18N.I18N');
AriKernel::import('Utils.Utils');

$managerComp =& AriQuizComponent::instance();
$managerComp->init();

$count = intval($params->get('count', 5));
if ($count < 0) $count = 5;
$measureUnit = $params->get('pointUnit', 'percent');
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$label = $params->get('label', 'Last Results');
$nameField = $params->get('nameField', 'username');
$nameField = ($nameField == 'login' ? 'LoginName' : 'UserName');
$ignoreGuest = AriUtils::parseValueBySample($params->get('ignoreGuest', 'true'), true);
$emptyMessage = trim($params->get('emptyMessage', ''));
$categoryId = $params->get('categoryId', null);
if ($categoryId || $categoryId === '0') $categoryId = explode(',', $categoryId);

$resultController = new AriQuizResultController();
$results = $resultController->call('getLastResults', $count, $ignoreGuest, $categoryId);
?>
<?php
	if (!empty($results) || !empty($emptyMessage))
	{
?>
	<table style="width: 100%; font-size: 100%;" cellpadding="2" class="aqmodtable<?php echo $moduleclass_sfx; ?>">
		<tr>
			<th colspan="3"><?php echo $label; ?></th>
		</tr>
<?php
	if (!empty($results)):
		$guest = AriWebHelper::translateResValue('Label.Guest');
		foreach ($results as $result)
		{
			$name = AriWebHelper::translateDbValue(AriUtils::getParam($result, $nameField, ''));
			if (empty($name)) $name = $guest;
?>
		<tr>
			<td class="aqmodquiz<?php echo $moduleclass_sfx; ?>"><?php AriWebHelper::displayDbValue($result->QuizName); ?></td>
			<td class="aqmoduser<?php echo $moduleclass_sfx; ?>"><?php echo $name; ?></td>
			<td class="aqmodpoint<?php echo $moduleclass_sfx; ?>" style="width: 1%; white-space: nowrap;"><?php echo $measureUnit == 'point' ? $result->UserScore : sprintf('%.2f %%', $result->PercentScore); ?></td>
		</tr>	
<?php
		}
	else:
?>
		<tr>
			<td colspan="3"><?php echo $emptyMessage; ?></td>
		</tr>
<?php
	endif;
?>
	</table>
<?php
	}
}
?>