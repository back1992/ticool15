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

$managerComp =& AriQuizComponent::instance();
$managerComp->init();

global $my;

$count = intval($params->get('count', 5));
if ($count < 0) $count = 5;
$measureUnit = $params->get('pointUnit', 'percent');
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$label = $params->get('label', 'My Top Results');
$userId = $my->get('id');
$emptyMessage = trim($params->get('emptyMessage', ''));

$resultController = new AriQuizResultController();
$results = $resultController->call('getTopUserResults', $userId, $count);
?>
<?php
	if (!empty($results) || !empty($emptyMessage))
	{
?>
	<table style="width: 100%; font-size: 100%;" class="aqmodtable<?php echo $moduleclass_sfx; ?>">
		<tr>
			<th colspan="2"><?php echo $label; ?></th>
		</tr>
<?php
	if (!empty($results)):
		foreach ($results as $result)
		{
?>
		<tr>
			<td class="aqmodquiz<?php echo $moduleclass_sfx; ?>"><?php AriWebHelper::displayDbValue($result->QuizName); ?></td>
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