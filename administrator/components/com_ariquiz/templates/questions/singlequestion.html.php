<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $extraData = $specificQuestion->getExtraDataFromXml($questionData); ?> 
<script src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option; ?>/js/ari.multiplierControls.js" type="text/javascript"></script> <table class="questionContainer" cellpadding="0" cellspacing="0"> <tr>
<td class="colMin right"><label for="chkSQRandomizeOrder"><?php AriWebHelper::displayResValue('Label.RandomOrder'); ?></label> :</td>
<td class="left"><input type="checkbox" id="chkSQRandomizeOrder" name="chkSQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?>/></td> </tr> <tr>
<td class="right"><label for="ddlSQView"><?php AriWebHelper::displayResValue('Label.ViewType'); ?></label> :</td> <td class="left"> <select class="text_area" id="ddlSQView" name="ddlSQView">
<option value="<?php echo ZQUIZ_SQ_VIEWTYPE_RADIO; ?>"><?php AriWebHelper::displayResValue('Label.ViewType.Radio'); ?></option>
<option value="<?php echo ZQUIZ_SQ_VIEWTYPE_DROPDOWN; ?>"<?php if ($extraData['view'] == ZQUIZ_SQ_VIEWTYPE_DROPDOWN) echo ' selected="selected"'; ?>><?php AriWebHelper::displayResValue('Label.ViewType.DropDown'); ?></option> </select> </td>
</tr> </table> <table id="tblQueContainer" class="singleQuestionContainer questionContainer" cellpadding="0" cellspacing="0"> <thead> <tr> <?php if ($uiMode != $uiModeList['Read']) { ?>
<th class="colMin"><div class="addItemIcon" title="+" onclick="aris.widgets.multiplierControls.addItem('tblQueContainer'); YAHOO.ARISoft.page.singleQuestion.updateHidCorrect();return false;">&nbsp;</div></th> <?php } ?>
<th class="colMin"><?php AriWebHelper::displayResValue('Label.Correct'); ?></th> <th><?php AriWebHelper::displayResValue('Label.Answer'); ?></th> <th class="colMin"><?php AriWebHelper::displayResValue('Label.PercentScore'); ?></th> <?php
if ($uiMode != $uiModeList['Read']) { ?> <th class="colMin"><?php AriWebHelper::displayResValue('Label.Actions'); ?></th> <?php } ?> </tr> </thead> <tbody> <tr id="trQueTemplate"> <?php if ($uiMode != $uiModeList['Read'])
{ ?> <td>&nbsp;</td> <?php } ?>
<td><input type="radio" onclick="YAHOO.ARISoft.page.singleQuestion.updateHidCorrect();" name="rbCorrect" id="rbCorrect" value="true" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /></td> <td>
<input type="text" id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 99%;" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /> <input type="hidden" id="hidQueId" name="hidQueId" />
<input type="hidden" id="hidCorrect" name="hidCorrect" /> </td>
<td><input type="text" size="5" name="tbxScore" id="tbxScore" class="text_area" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /><?php AriWebHelper::displayResValue('Label.Percent'); ?>&nbsp;<?php if (!empty($baseOnBank)) { ?><input type="checkbox" id="chkOverride" name="chkOverride" value="1" alt="Override" title="Override" onclick="YAHOO.ARISoft.page.singleQuestion.overrideScore(this);" /><input type="hidden" id="hidScore" name="hidScore" /><?php } ?></td>
<?php if ($uiMode != $uiModeList['Read']) { ?> <td> <table cellpadding="0" cellspacing="0" border="0"> <tr> <td>
<div class="deleteItemIcon" onclick="if (confirm('<?php AriWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) aris.widgets.multiplierControls.removeItem(aris.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trQueTemplate')); return false;" title="Remove">&nbsp;</div>
</td> <td> <div class="upItemIcon" onclick="aris.widgets.multiplierControls.moveUpItem(this, 'trQueTemplate'); return false;" title="Up">&nbsp;</div> </td> <td>
<div class="downItemIcon" onclick="aris.widgets.multiplierControls.moveDownItem(this, 'trQueTemplate', 'tblQueContainer'); return false;" title="Down">&nbsp;</div> </td> </tr> </table> </td> <?php } ?> </tr>
</tbody> </table> <table class="questionNote" cellpadding="0" cellspacing="0"> <tr> <td class="colMin noWrap"><b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b></td> <td><?php AriWebHelper::displayResValue('Text.SQNote'); ?></td>
</tr> </table> <script type="text/javascript" language="javascript"> YAHOO.ARISoft.page.singleQuestion = { overrideScore: function(chkOverride) {
var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'tbxScore'); var hidScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'hidScore'); 
tbxScore.disabled = !chkOverride.checked; tbxScore.value = hidScore.value; }, updateHidCorrect: function() { var rbCorrectList = aris.widgets.multiplierControls.getContainerElements('tblQueContainer', 'rbCorrect');
if (rbCorrectList) { for (var i = 0; i < rbCorrectList.length; i++) { rbCorrectList[i].onclick = this.setCorrect; rbCorrectList[i].onchange = this.setCorrect; } } }, setCorrect: function(e) {
e = e || event; var ctrl = e.srcElement || e.target; var hidCorrectList = aris.widgets.multiplierControls.getContainerElements('tblQueContainer', 'hidCorrect'); for (var i = 0; i < hidCorrectList.length; i++) {
hidCorrectList[i].value = ''; }; var curHidCorrect = aris.widgets.multiplierControls.getTemplateElement(ctrl, 'trQueTemplate', 'hidCorrect'); if (curHidCorrect) { curHidCorrect.defaultValue = 'true';
curHidCorrect.value = 'true'; } YAHOO.ARISoft.page.singleQuestion.updateCorrectScore(ctrl, true); }, updateCorrectScore: function(corEl, clearPrev) { if (clearPrev) {
var tbxScoreList = aris.widgets.multiplierControls.getContainerElements('tblQueContainer', 'tbxScore'); for (var i = 0; i < tbxScoreList.length; i++) { var curTbxScore = tbxScoreList[i]; if (curTbxScore.disabled) {
curTbxScore.value = ''; curTbxScore.disabled = false; } } } var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(corEl, 'trQueTemplate', 'tbxScore'); tbxScore.value = 100;
tbxScore.disabled = true; var chkOverride = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(corEl, 'trQueTemplate', 'chkOverride'); if (chkOverride) chkOverride.disabled = true; } }; 
aris.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false, isset($questionOverrideData) ? $questionOverrideData : null)); ?>,
function() { var chkOverrideList = aris.widgets.multiplierControls.getContainerElements('tblQueContainer', 'chkOverride'); for (var i = 0; i < chkOverrideList.length; i++) { if (chkOverrideList[i].checked) {
var tbxScore = aris.widgets.multiplierControls.getTemplateElement(chkOverrideList[i], 'trQueTemplate', 'tbxScore'); tbxScore.disabled = false; } }; 
var hidCorrectList = aris.widgets.multiplierControls.getContainerElements('tblQueContainer', 'hidCorrect'); for (var i = 0; i < hidCorrectList.length; i++) { if (hidCorrectList[i].value == 'true') {
var selRbCorrect = aris.widgets.multiplierControls.getTemplateElement(hidCorrectList[i], 'trQueTemplate', 'rbCorrect'); selRbCorrect.defaultChecked = true; selRbCorrect.checked = true; 
YAHOO.ARISoft.page.singleQuestion.updateCorrectScore(selRbCorrect); break; } }; YAHOO.ARISoft.page.singleQuestion.updateHidCorrect(); }); aris.validators.validatorManager.addValidator(
new aris.validators.customValidator(null, function(val) { var isValid = true; var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.widgets.multiplierControls.originalIdAttr, 'trQueTemplate');
var templateCnt = templates ? templates.length : 0; if (templateCnt > 0) { for (var i = 0; i < templateCnt; i++) { var template = templates[i];
var tbxScore = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxScore'); var sScore = YAHOO.lang.trim(tbxScore.value); if (sScore.length == 0) continue; 
var score = parseInt(sScore, 10); if (sScore != score || score < 0 || score > 100) { isValid = false; break; } } } return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionNoSetPercentScore'); ?>'})); <?php if ($uiMode != $uiModeList['Read']) { ?> aris.validators.validatorManager.addValidator(
new aris.validators.customValidator(null, function(val) { var isValid = true; var isSetCorrect = false; var isNotEmpty = false; 
var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.widgets.multiplierControls.originalIdAttr, 'trQueTemplate'); var templateCnt = templates ? templates.length : 0; if (templateCnt > 0) {
for (var i = 0; i < templateCnt; i++) { var template = templates[i]; var rbCorrect = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'rbCorrect');
if (rbCorrect && rbCorrect.checked) { isSetCorrect = true; var tbxAnswer = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxAnswer'); var value = tbxAnswer.value;
if (value && value.replace(/^\s+|\s+$/g, '').length > 0) { isNotEmpty = true; break; } } } if (!isSetCorrect) {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotCorrect'); ?>'); isValid = false; } else if (!isNotEmpty) {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } } else {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'})); <?php } ?> </script>