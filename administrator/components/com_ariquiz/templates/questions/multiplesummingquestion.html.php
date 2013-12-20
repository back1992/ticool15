<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $extraData = $specificQuestion->getExtraDataFromXml($questionData); ?> 
<script src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option; ?>/js/ari.multiplierControls.js" type="text/javascript"></script> <table class="questionContainer" cellpadding="0" cellspacing="0"> <tr>
<td class="colMin right"><label for="chkMSQRandomizeOrder"><?php AriWebHelper::displayResValue('Label.RandomOrder'); ?></label> :</td>
<td class="left"><input type="checkbox" id="chkMSQRandomizeOrder" name="chkMSQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?>/></td> </tr> </table>
<table id="tblQueContainer" class="questionContainer" cellpadding="0" cellspacing="0"> <thead> <tr id="trMQHeader"> <?php if ($uiMode != $uiModeList['Read']) { ?>
<th class="colMin"><div class="addItemIcon" title="+" onclick="aris.widgets.multiplierControls.addItem('tblQueContainer'); return false;">&nbsp;</div></th> <?php } ?> <th><?php AriWebHelper::displayResValue('Label.Answer'); ?></th>
<th class="colMin"><?php AriWebHelper::displayResValue('Label.Score'); ?></th> <?php if ($uiMode != $uiModeList['Read']) { ?> <th class="colMin"><?php AriWebHelper::displayResValue('Label.Actions'); ?></th> <?php } ?> </tr>
</thead> <tbody> <tr id="trQueTemplate" class="mqTemplate"> <?php if ($uiMode != $uiModeList['Read']) { ?> <td>&nbsp;</td> <?php } ?> <td>
<input type="text" id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 99%;" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /> <input type="hidden" id="hidQueId" name="hidQueId" /> </td> <td>
<input type="text" name="tbxMSQScore" id="tbxMSQScore" class="text_area" size="4" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /> <?php if (!empty($baseOnBank) && false): ?>
<input type="checkbox" id="chkOverride" name="chkOverride" value="1" alt="Override" title="Override" onclick="YAHOO.ARISoft.page.multipleSummingQuestion.overrideScore(this);" /><input type="hidden" id="hidScore" name="hidScore" />
<?php endif; ?> </td> <?php if ($uiMode != $uiModeList['Read']) { ?> <td> <table cellpadding="0" cellspacing="0" border="0"> <tr> <td>
<div class="deleteItemIcon" onclick="if (confirm('<?php AriWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) aris.widgets.multiplierControls.removeItem(aris.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trQueTemplate')); return false;" title="Remove">&nbsp;</div>
</td> <td> <div class="upItemIcon" onclick="aris.widgets.multiplierControls.moveUpItem(this, 'trQueTemplate'); return false;" title="Up">&nbsp;</div> </td> <td>
<div class="downItemIcon" onclick="aris.widgets.multiplierControls.moveDownItem(this, 'trQueTemplate', 'tblQueContainer'); return false;" title="Down">&nbsp;</div> </td> </tr> </table> </td> <?php } ?> </tr>
</tbody> </table> <table class="questionNote" cellpadding="0" cellspacing="0"> <tr> <td class="colMin noWrap"><b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b></td>
<td><?php AriWebHelper::displayResValue('Text.MSQNote'); ?></td> </tr> </table> <script type="text/javascript" language="javascript"> YAHOO.ARISoft.page.multipleSummingQuestion = { overrideScore: function(chkOverride) {
var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'tbxMSQScore'); var hidScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'hidScore'); 
tbxScore.disabled = !chkOverride.checked; tbxScore.value = hidScore.value; } }; 
aris.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false)); ?>); <?php if ($uiMode != $uiModeList['Read']) { ?>
aris.validators.validatorManager.addValidator( new aris.validators.customValidator(null, function(val) { var isValid = true; var isNotCorrectScore = false; var isNotEmpty = false; 
var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.widgets.multiplierControls.originalIdAttr, 'trQueTemplate'); var templateCnt = templates ? templates.length : 0; if (templateCnt > 0) {
for (var i = 0; i < templateCnt; i++) { var template = templates[i]; var tbxScore = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxMSQScore');
var tbxAnswer = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxAnswer'); var answer = YAHOO.lang.trim(tbxAnswer.value); var sScore = YAHOO.lang.trim(tbxScore.value); 
if (answer.length) isNotEmpty = true; if (sScore.length > 0) { var score = parseInt(sScore, 10); if (sScore != score) { isNotCorrectScore = true; break; } } } 
if (isNotCorrectScore) { this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionScore'); ?>'); isValid = false; } else if (!isNotEmpty) {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } } else {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'})); <?php } ?> </script>