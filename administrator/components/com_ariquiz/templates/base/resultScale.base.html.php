<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $option = $processPage->getVar('option'); $mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; $scaleItemData = $processPage->getVar('scaleItemData'); ?>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/yahoo-dom-event/yahoo-dom-event.js"></script> <script type="text/javascript" src="<?php echo $jsPath; ?>ari.multiplierControls.js"></script> 
<table class="adminform" style="width: 100%;"> <tbody> <tr> <th colspan="3"><?php AriWebHelper::displayResValue('Label.MainSettings'); ?></th> </tr> </tbody> <tbody id="tbQuizSettings"> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.Name'); ?> :</td> <td align="left" colspan="2"><?php $processPage->renderControl('tbxScaleName', array('class' => 'text_area', 'size' => '70')); ?></td> </tr> <tr>
<td align="left"></td> <td align="left"> <table id="tblScaleContainer" border="0" cellpadding="0" cellspacing="0"> <tr>
<th style="width: 1%; text-align: center;"><div class="addItemIcon" title="+" onclick="aris.widgets.multiplierControls.addItem('tblScaleContainer'); return false;">&nbsp;</div></th> <th>&nbsp;</th> <th>&nbsp;</th>
<th style="width: 5%; text-align: center;"><?php AriWebHelper::displayResValue('Label.Actions'); ?></th> </tr> <tbody id="trScaleTemplate"> <tr valign="top"> <td colspan="3">
<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;"> <tr>
<td><?php AriWebHelper::displayResValue('Label.StartPoint'); ?>: <input type="text" size="5" class="text_area" id="tbxStartPoint" name="tbxStartPoint" maxlength="3" />&nbsp;<?php AriWebHelper::displayResValue('Label.Percent'); ?></td>
<td><?php AriWebHelper::displayResValue('Label.EndPoint'); ?>: <input type="text" size="5" class="text_area" id="tbxEndPoint" name="tbxEndPoint" maxlength="3" />&nbsp;<?php AriWebHelper::displayResValue('Label.Percent'); ?></td>
</tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.MailTemplate'); ?> :</td> <td align="left"><?php $processPage->renderControl('lbEmailTemplate', array('class' => 'text_area')); ?></td>
</tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.PrintTemplate'); ?> :</td> <td align="left"><?php $processPage->renderControl('lbPrintTemplate', array('class' => 'text_area')); ?></td>
</tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.TextTemplate'); ?> :</td> <td align="left"><?php $processPage->renderControl('lbTextTemplate', array('class' => 'text_area')); ?></td>
</tr> </table> </td> <td style="text-align: center; white-space: nowrap;"> <div style="text-align: center;">
<div class="deleteItemIcon" title="Remove" onclick="if (confirm('<?php AriWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) aris.widgets.multiplierControls.removeItem(aris.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trScaleTemplate')); return false;">&nbsp;</div>
</div> </td> </tr> <tr> <td colspan="4" style="text-align:center;"><hr /></td> </tr> </tbody> </table> </td> </tr> </tbody> </table>
<script type="text/javascript" language="javascript"> YAHOO.ARISoft.page.isValidSection = function(section) { return (section['startPoint'] != null && section['endPoint'] != null); }; 
YAHOO.ARISoft.page.getSectionData = function(dataItem, parse) { parse = parse || false;
var startPoint = !YAHOO.lang.isUndefined(dataItem['tbxStartPoint']) ? (parse ? parseInt(dataItem['tbxStartPoint'], 10) : YAHOO.lang.trim(dataItem['tbxStartPoint'])) : null;
var endPoint = !YAHOO.lang.isUndefined(dataItem['tbxEndPoint']) ? (parse ? parseInt(dataItem['tbxEndPoint'], 10) : YAHOO.lang.trim(dataItem['tbxEndPoint'])) : null; if (parse) { if (isNaN(startPoint)) startPoint = null;
if (isNaN(endPoint)) endPoint = null; if (startPoint != null && endPoint != null && startPoint > endPoint) { var tPoint = startPoint; startPoint = endPoint; endPoint = tPoint; } }; 
return  {'startPoint': startPoint, 'endPoint': endPoint}; }; YAHOO.ARISoft.page.pointsValidate = function(val) { var isValid = false;
var data = aris.widgets.multiplierControls.getData('tblScaleContainer', 'trScaleTemplate', ['tbxStartPoint', 'tbxEndPoint']); if (!YAHOO.ARISoft.page.formatPointValidate(data)) {
val.errorMessage = '<?php AriWebHelper::displayResValue('Validator.RSRangePoint'); ?>'; } else if (!YAHOO.ARISoft.page.emptyPointValidate(data)) { val.errorMessage = '<?php AriWebHelper::displayResValue('Validator.RSEmptySection'); ?>';
} else if (!YAHOO.ARISoft.page.rangePointValidate(data)) { val.errorMessage = '<?php AriWebHelper::displayResValue('Validator.RSRangePoint'); ?>'; } else if (!YAHOO.ARISoft.page.intersectPointValidate(data)) {
val.errorMessage = '<?php AriWebHelper::displayResValue('Validator.RSOverlapPoint'); ?>'; } else if (!YAHOO.ARISoft.page.emptyAllRangeCoveredPointValidate(data)) {
val.errorMessage = '<?php AriWebHelper::displayResValue('Validator.RSNotCoveredPoint'); ?>'; isValid = confirm('<?php AriWebHelper::displayResValue('Warning.RSNotCoveredPoint'); ?>'); } else { isValid = true; } 
return isValid; }; YAHOO.ARISoft.page.formatPointValidate = function(data) { data = data || null; if (!data || data.length < 1) return true; var isValid = true; for (var i = 0; i < data.length; i++) {
var item = data[i]; var section = YAHOO.ARISoft.page.getSectionData(item); if ((section['startPoint'] != null && section['startPoint'].length > 0 && section['startPoint'] != parseInt(section['startPoint'], 10)) ||
(section['endPoint'] != null && section['endPoint'].length > 0 && section['endPoint'] != parseInt(section['endPoint'], 10))) { isValid = false; break; } }; return isValid; }; 
YAHOO.ARISoft.page.emptyPointValidate = function(data) { data = data || null; var isValid = (data && data.length > 0); if (!isValid) return isValid; isValid = false; for (var i = 0; i < data.length; i++) { var item = data[i];
var section = YAHOO.ARISoft.page.getSectionData(item, true); if (YAHOO.ARISoft.page.isValidSection(section)) { isValid = true; break; } }; return isValid; }; 
YAHOO.ARISoft.page.intersectPointValidate = function(data) { data = data || null; if (!data || data.length < 1) return true; var isValid = true; var range = {}; for (var i = 0; i < data.length; i++) { var item = data[i];
var section = YAHOO.ARISoft.page.getSectionData(item, true); if (YAHOO.ARISoft.page.isValidSection(section)) { var startPoint = section['startPoint']; var endPoint = section['endPoint']; for (var startRange in range) {
var endRange = range[startRange]; if (startRange <= startPoint && startPoint < endRange) { isValid = false; break; } }; range[startPoint] = endPoint; } }; return isValid; }; 
YAHOO.ARISoft.page.rangePointValidate = function(data) { data = data || null; if (!data || data.length < 1) return true; var isValid = true; for (var i = 0; i < data.length; i++) { var item = data[i];
var section = YAHOO.ARISoft.page.getSectionData(item, true); if (YAHOO.ARISoft.page.isValidSection(section) && (section['startPoint'] < 0 || section['startPoint'] > 100 || section['endPoint'] < 0 || section['endPoint'] > 100)) {
isValid = false; break; } }; return isValid; }; YAHOO.ARISoft.page.emptyAllRangeCoveredPointValidate = function(data) { data = data || null; if (!data || data.length < 1) return true; var range = {};
var counter = 101; for (var i = 0; i < data.length; i++) { var item = data[i]; var section = YAHOO.ARISoft.page.getSectionData(item, true); if (YAHOO.ARISoft.page.isValidSection(section)) {
var startPoint = section['startPoint']; var endPoint = section['endPoint']; if (!YAHOO.lang.isUndefined(range[startPoint])) ++counter; counter -= endPoint - startPoint + 1; range[endPoint] = startPoint; } }; 
return (counter < 1); }; aris.widgets.multiplierControls.init('trScaleTemplate', 'tblScaleContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($scaleItemData); ?>); submitbutton = function(pressbutton) {
if (pressbutton == '<?php echo $processPage->executionTask; ?>$save' || pressbutton == '<?php echo $processPage->executionTask; ?>$apply') { if (!aris.validators.alertSummaryValidators.validate()) { return; } } 
submitform(pressbutton); }; </script>