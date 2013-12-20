<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $extraData = $specificQuestion->getExtraDataFromXml($questionData); ?> 
<script src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option; ?>/js/ari.multiplierControls.js" type="text/javascript"></script> <table class="questionContainer" cellpadding="0" cellspacing="0"> <tr>
<td class="colMin right"><label for="chkCQRandomizeOrder"><?php AriWebHelper::displayResValue('Label.RandomOrder'); ?></label> :</td>
<td class="left"><input type="checkbox" id="chkCQRandomizeOrder" name="chkCQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?>/></td> </tr> </table>
<table id="tblQueContainer" style="width: 100%;" cellpadding="0" cellspacing="0"> <tr> <?php if ($uiMode != $uiModeList['Read']) { ?>
<th style="width: 1%; text-align: center;"><div class="addItemIcon" title="+" onclick="aris.widgets.multiplierControls.addItem('tblQueContainer'); return false;">&nbsp;</div></th> <?php } ?>
<th style="text-align: center;"><?php AriWebHelper::displayResValue('Label.Answer'); ?></th> <th style="text-align: center;"><?php AriWebHelper::displayResValue('Label.Answer'); ?></th> <?php if ($uiMode != $uiModeList['Read']) { ?>
<th style="width: 5%; text-align: center;"><?php AriWebHelper::displayResValue('Label.Actions'); ?></th> <?php } ?> </tr> <tr id="trQueTemplate"> <?php if ($uiMode != $uiModeList['Read']) { ?> <td>&nbsp;</td> <?php } ?>
<td> <input type="text" id="tbxLabel" name="tbxLabel" class="text_area" style="width: 99%;" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /> <input type="hidden" id="hidLabelId" name="hidLabelId" /> </td> <td>
<input type="text" id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 99%;" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /> <input type="hidden" id="hidAnswerId" name="hidAnswerId" /> </td> <?php
if ($uiMode != $uiModeList['Read']) { ?> <td> <table cellpadding="0" cellspacing="0" border="0"> <tr> <td>
<div class="deleteItemIcon" onclick="if (confirm('<?php AriWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) aris.widgets.multiplierControls.removeItem(aris.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trQueTemplate')); return false;" title="Remove">&nbsp;</div>
</td> <td> <div class="upItemIcon" onclick="aris.widgets.multiplierControls.moveUpItem(this, 'trQueTemplate'); return false;" title="Up">&nbsp;</div> </td> <td>
<div class="downItemIcon" onclick="aris.widgets.multiplierControls.moveDownItem(this, 'trQueTemplate', 'tblQueContainer'); return false;" title="Down">&nbsp;</div> </td> </tr> </table> </td> <?php } ?> </tr> </table>
<table class="questionNote" cellpadding="0" cellspacing="0"> <tr> <td class="colMin noWrap"><b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b></td> <td><?php AriWebHelper::displayResValue('Text.EmptyAnswerIgnored'); ?></td> </tr>
</table> <script type="text/javascript" language="javascript">
aris.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false)); ?>); <?php if ($uiMode != $uiModeList['Read']) { ?>
aris.validators.validatorManager.addValidator( new aris.validators.customValidator(null, function(val) { var isValid = true; var isNotEmpty = false; 
var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.widgets.multiplierControls.originalIdAttr, 'trQueTemplate'); var templateCnt = templates ? templates.length : 0; if (templateCnt > 0) {
for (var i = 0; i < templateCnt; i++) { var template = templates[i]; var tbxAnswer = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxAnswer');
var tbxLabel = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxLabel'); var ans = tbxAnswer.value; var lbl = tbxLabel.value;
if (ans && ans.replace(/^\s+|\s+$/g, '').length > 0 && lbl && lbl.replace(/^\s+|\s+$/g, '').length > 0) { isNotEmpty = true; break; } } } if (!isNotEmpty) {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'})); <?php } ?> </script>