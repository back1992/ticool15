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
AriKernel::import('Xml.SimpleXml');
AriKernel::import('I18N.I18N');
AriKernel::import('Utils.Utils');

$managerComp =& AriQuizComponent::instance();
$managerComp->init();

$count = intval($params->get('count', 5));
if ($count < 0) $count = 5;
$measureUnit = $params->get('pointUnit', 'percent');
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$label = $params->get('label', 'Top Results');
$nameField = $params->get('nameField', 'username');
$nameField = ($nameField == 'login' ? 'LoginName' : 'UserName');
$ignoreGuest = AriUtils::parseValueBySample($params->get('ignoreGuest', 'true'), true);
$hideQuizName = AriUtils::parseValueBySample($params->get('hideQuizName'), true);
$aggregateResults = AriUtils::parseValueBySample($params->get('aggregateResults', 'true'), true);
$aggregateUserResults = AriUtils::parseValueBySample($params->get('aggregateUserResults'), true);
$emptyMessage = trim($params->get('emptyMessage', ''));
$categoryId = $params->get('categoryId', null);
$colspan = $hideQuizName ? 2 : 3;
if ($categoryId || $categoryId === '0') $categoryId = explode(',', $categoryId);

// calculate date filters
$tzOffset = floatval($params->get('time_zone')) * 60 * 60; 
$dateFilterType = $params->get('dateFilterType');
$startDate = null;
$endDate = null;
if ($dateFilterType == 'range')
{
	$startDate = $params->get('daterange_start_date', null);
	$endDate = $params->get('daterange_end_date', null);
	
	if ($startDate)
	{
		$dateInfo = getdate(strtotime($startDate));
		$startDate = mktime(0, 0, 1, $dateInfo['mon'], $dateInfo['mday'], $dateInfo['year']);
	}
	
	if ($endDate)
	{
		$dateInfo = getdate(strtotime($endDate));
		$endDate = mktime(23, 59, 59, $dateInfo['mon'], $dateInfo['mday'], $dateInfo['year']);
	}
}
else if ($dateFilterType == 'recurrence')
{
	$recurrence_type = $params->get('recurrence_type', 'month');
	switch ($recurrence_type)
	{
		case 'day':
			$startDate = gmmktime(0, 0, 1);
			$endDate = gmmktime(23, 59, 59);
			break;
			
		case 'week':
			$dateInfo = getdate(strtotime('Monday'));
			$startDate = gmmktime(0, 0, 1, $dateInfo['mon'], $dateInfo['mday'], $dateInfo['year']);
			
			$dateInfo = getdate(strtotime('Sunday'));
			$endDate = gmmktime(23, 59, 59, $dateInfo['mon'], $dateInfo['mday'], $dateInfo['year']);
			break;

		case 'month':
			$startDate = gmmktime(0, 0, 1, gmdate('n'), 1);
			$endDate = gmmktime(23, 59, 59, gmdate('n') + 1, 0);
			break;

		case 'year':
			$startDate = gmmktime(0, 0, 1, 1, 1);
			$endDate = gmmktime(23, 59, 59, 1, 0, gmdate('Y') + 1);
			break;
	}
}

if ($startDate)
	$startDate -= $tzOffset;
	
if ($endDate)
	$endDate -= $tzOffset;

$resultController = new AriQuizResultController();
$results = $aggregateResults 
	? $resultController->call('getAggregateTopResults', $count, $ignoreGuest, $categoryId, $startDate, $endDate)
	: $resultController->call('getTopResults', $count, $ignoreGuest, $categoryId, $aggregateUserResults, $startDate, $endDate);
?>
<?php
	if (!empty($results) || !empty($emptyMessage))
	{
?>
	<table style="width: 100%; font-size: 100%;" class="aqmodtable<?php echo $moduleclass_sfx; ?>">
		<tr>
			<th colspan="<?php echo $colspan; ?>"><?php echo $label; ?></th>
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
			<?php
				if (!$hideQuizName):
			?>
			<td class="aqmodquiz<?php echo $moduleclass_sfx; ?>"><?php AriWebHelper::displayDbValue($result->QuizName); ?></td>
			<?php
				endif;
			?>
			<td class="aqmoduser<?php echo $moduleclass_sfx; ?>"><?php echo $name; ?></td>
			<td class="aqmodpoint<?php echo $moduleclass_sfx; ?>" style="width: 1%; white-space: nowrap;"><?php echo $measureUnit == 'point' ? $result->UserScore : sprintf('%.2f %%', $result->PercentScore); ?></td>
		</tr>	
<?php
		}
	else:
?>
		<tr>
			<td colspan="<?php echo $colspan; ?>"><?php echo $emptyMessage; ?></td>
		</tr>
<?php
	endif;
?>
	</table>
<?php
	}
}
?>