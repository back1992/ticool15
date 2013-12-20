<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $extraData = $specificQuestion->getExtraDataFromXml($questionData); ?> 
<script src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option; ?>/js/ari.multiplierControls.js" type="text/javascript"></script> <table class="questionContainer" cellpadding="0" cellspacing="0"> <tr>
<td class="colMin right"><label for="chkMQRandomizeOrder"><?php AriWebHelper::displayResValue('Label.RandomOrder'); ?></label> :</td>
<td class="left"><input type="checkbox" id="chkMQRandomizeOrder" name="chkMQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?>/></td> </tr> </table>
<table id="tblQueContainer" class="multipleQuestionContainer questionContainer" cellpadding="0" cellspacing="0"> <thead> <tr id="trMQHeader"> <?php if ($uiMode != $uiModeList['Read']) { ?>
<th class="colMin"><div class="addItemIcon" title="+" onclick="aris.widgets.multiplierControls.addItem('tblQueContainer'); YAHOO.ARISoft.page.multipleQuestion.fixScoreSections(); return false;">&nbsp;</div></th> <?php } ?>
<th class="colMin"><?php AriWebHelper::displayResValue('Label.Correct'); ?><?php if ($uiMode != $uiModeList['Read']) { ?>&nbsp;[<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.multipleQuestion.addScoreSection(); return false;">+</a>]<?php } ?></th>
<th class="colScoreTemplate colMin noWrap">
<input type="text" id="tbxScore" title="<?php AriWebHelper::displayResValue('Label.PercentScore'); ?>" size="3" maxlength="3" class="mqScoreControl text_area" /><input type="hidden" class="mqScoreIdControl" /><?php AriWebHelper::displayResValue('Label.Percent'); ?>
<?php if (!empty($baseOnBank)) { ?> <input type="checkbox" value="1" alt="Override" title="Override" onclick="YAHOO.ARISoft.page.multipleQuestion.overrideScore(this);" /><input type="hidden" class="mqHidBankScore" /> <?php } else { ?>
<span class="deleteItemIcon" onclick="if (confirm('<?php AriWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) YAHOO.ARISoft.page.multipleQuestion.removeScoreSection(this); return false;" title="Remove"><img src="<?php echo $mosConfig_live_site;?>/administrator/components/<?php echo $option; ?>/images/x.gif" width="14" height="12" border="0" alt="" /></span>
<?php }; ?> </th> <th><?php AriWebHelper::displayResValue('Label.Answer'); ?></th> <?php if ($uiMode != $uiModeList['Read']) { ?> <th class="colMin"><?php AriWebHelper::displayResValue('Label.Actions'); ?></th> <?php }
?> </tr> </thead> <tbody> <tr id="trQueTemplate" class="mqTemplate"> <?php if ($uiMode != $uiModeList['Read']) { ?> <td>&nbsp;</td> <?php } ?>
<td><input type="checkbox" name="cbCorrect" id="cbCorrect" value="true" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /></td>
<td class="colScoreTemplate"><input type="checkbox" id="cbScoreCorrect" value="1" class="mqChkScoreControl" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /></td> <td>
<input type="text" id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 99%;" <?php if ($uiMode == $uiModeList['Read']) echo 'disabled="true"'; ?> /> <input type="hidden" class="mqIdControl" id="hidQueId" name="hidQueId" />
</td> <?php if ($uiMode != $uiModeList['Read']) { ?> <td> <table cellpadding="0" cellspacing="0" border="0"> <tr> <td>
<div class="deleteItemIcon" onclick="if (confirm('<?php AriWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) aris.widgets.multiplierControls.removeItem(aris.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trQueTemplate')); return false;" title="Remove">&nbsp;</div>
</td> <td> <div class="upItemIcon" onclick="aris.widgets.multiplierControls.moveUpItem(this, 'trQueTemplate'); return false;" title="Up">&nbsp;</div> </td> <td>
<div class="downItemIcon" onclick="aris.widgets.multiplierControls.moveDownItem(this, 'trQueTemplate', 'tblQueContainer'); return false;" title="Down">&nbsp;</div> </td> </tr> </table> </td> <?php } ?> </tr>
</tbody> </table> <input type="hidden" id="hidPercentScore" name="hidPercentScore" value="" /> <table class="questionNote" cellpadding="0" cellspacing="0"> <tr>
<td class="colMin noWrap"><b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b></td> <td><?php AriWebHelper::displayResValue('Text.MQNote'); ?></td> </tr> </table> <script type="text/javascript" language="javascript">
YAHOO.ARISoft.page.multipleQuestion = { SCORE_TEMPLATE_CLASS: 'colScoreTemplate', SCORE_ITEM_CLASS: 'colScoreItem', SCORE_ITEM_INDEX_CLASS: 'scoreItemIndex_', SCORE_CONTROL_CLASS: 'mqScoreControl', 
SCORE_CORRECT_CONTROL_CLASS: 'mqChkScoreControl', SCORE_OVERRIDE_CONTROL_CLASS: 'mqChkOverride', SCORE_BANK_SCORE_CONTROL_CLASS: 'mqHidBankScore', SCORE_ID_CONTROL_CLASS: 'mqScoreIdControl', TEMPLATE_CLASS: 'mqTemplate', 
ID_CONTROL_CLASS: 'mqIdControl', OLD_SUBMIT_HANDLER: null, counter: 0, isBankBased: false, collectScoreSection: function() { var MQ = this; var data = []; 
var scoreSectionList = YAHOO.util.Dom.getElementsByClassName(this.SCORE_ITEM_CLASS, 'th', 'trMQHeader'); var scoreSectionCount = scoreSectionList ? scoreSectionList.length : 0; if (scoreSectionCount > 0) {
for (var i = 0; i < scoreSectionCount; i++) { var scoreSection = scoreSectionList[i]; var score = ''; YAHOO.util.Dom.getElementsByClassName(this.SCORE_CONTROL_CLASS, 'input', scoreSection, function(el) {
score = YAHOO.lang.trim(el.value); var iScore = parseInt(score, 10); score = (score == iScore && iScore > 0 && iScore < 101) ? iScore : ''; }); if (!score) continue; var id = '';
YAHOO.util.Dom.getElementsByClassName(this.SCORE_ID_CONTROL_CLASS, 'input', scoreSection, function(el) { id = el.value; }); var scoreItem = {id: id, score: score, correct: [], override: false};
if (this.isBankBased) { YAHOO.util.Dom.getElementsByClassName(this.SCORE_OVERRIDE_CONTROL_CLASS, 'input', scoreSection, function(chk) { scoreItem.override = chk.checked; }); } else {
var indexClass = this.getIndexClassByEl(scoreSection); YAHOO.util.Dom.getElementsByClassName(indexClass, 'td', 'tblQueContainer', function(el) { var correct = false;
YAHOO.util.Dom.getElementsByClassName(MQ.SCORE_CORRECT_CONTROL_CLASS, 'input', el, function(chk) { correct = chk.checked; }); scoreItem.correct.push(correct); }); } data.push(scoreItem);
} } var hidPercentScore = YAHOO.util.Dom.get('hidPercentScore'); hidPercentScore.value = YAHOO.lang.JSON.stringify(data); }, addScoreSection: function(scoreData) { var indexClass = this.generateIndexClass(); 
var elements = YAHOO.util.Dom.getElementsByClassName(this.SCORE_TEMPLATE_CLASS, null, 'tblQueContainer'); if (elements) { for (var i = 0; i < elements.length; i++) { var el = elements[i];
var cloneEl = el.cloneNode(true); YAHOO.util.Dom.replaceClass(cloneEl, this.SCORE_TEMPLATE_CLASS, this.SCORE_ITEM_CLASS); YAHOO.util.Dom.addClass(cloneEl, indexClass); el.parentNode.insertBefore(cloneEl, el); } 
if (scoreData) { this.initScoreSection(indexClass, scoreData); } } }, initScoreSection: function(indexClass, scoreData) { if (!indexClass || !scoreData) return ; var MQ = this; var Dom = YAHOO.util.Dom;
var score = scoreData.score; var id = !YAHOO.lang.isUndefined(scoreData.id) ? scoreData.id : ''; var override = (!YAHOO.lang.isUndefined(scoreData.override) && scoreData.override);
var bankScore = !YAHOO.lang.isUndefined(scoreData.bankScore) ? scoreData.bankScore : ''; Dom.getElementsByClassName(indexClass, 'th', 'trMQHeader', function(el) {
Dom.getElementsByClassName(MQ.SCORE_CONTROL_CLASS, 'input', el, function(tbx) { tbx.value = score; tbx.disabled = (MQ.isBankBased && !override); }); if (id) {
Dom.getElementsByClassName(MQ.SCORE_ID_CONTROL_CLASS, 'input', el, function(hid) { hid.value = id; }); } if (MQ.isBankBased) { if (override) {
Dom.getElementsByClassName(MQ.SCORE_OVERRIDE_CONTROL_CLASS, 'input', el, function(chk) { chk.checked = true; }); } if (bankScore) {
Dom.getElementsByClassName(MQ.SCORE_BANK_SCORE_CONTROL_CLASS, 'input', el, function(hid) { hid.value = bankScore; }); } } }); var correct = scoreData.correct; if (correct.length == 0) return ; 
Dom.getElementsByClassName(indexClass, 'td', 'tblQueContainer', function(el) { var template = Dom.getAncestorByClassName(el, MQ.TEMPLATE_CLASS); var id = '';
Dom.getElementsByClassName(MQ.ID_CONTROL_CLASS, 'input', template, function(hid) { id = hid.value; }); if (id && correct.indexOf(id) > -1) {
Dom.getElementsByClassName(MQ.SCORE_CORRECT_CONTROL_CLASS, 'input', el, function(chk) { chk.checked = true; }); } }); }, fixScoreSections: function() {
var scoreSectionList = YAHOO.util.Dom.getElementsByClassName(this.SCORE_ITEM_CLASS, 'th', 'trMQHeader'); var scoreSectionCount = scoreSectionList ? scoreSectionList.length : 0; if (scoreSectionCount == 0) return ; 
var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.widgets.multiplierControls.originalIdAttr, 'trQueTemplate'); var templateCnt = templates ? templates.length : 0; if (templateCnt == 0) return ; 
var newTemplate = templates[templateCnt - 1]; var elements = YAHOO.util.Dom.getElementsByClassName(this.SCORE_TEMPLATE_CLASS, 'td', newTemplate); if (elements) { for (var i = 0; i < elements.length; i++) {
for (var j = 0; j < scoreSectionCount; j++) { var el = elements[i]; var scoreSection = scoreSectionList[j]; var indexClass = this.getIndexClassByEl(scoreSection); var cloneEl = el.cloneNode(true);
YAHOO.util.Dom.replaceClass(cloneEl, this.SCORE_TEMPLATE_CLASS, this.SCORE_ITEM_CLASS); YAHOO.util.Dom.addClass(cloneEl, indexClass); el.parentNode.insertBefore(cloneEl, el); } } }; }, 
removeScoreSection: function(target) { var cont = YAHOO.util.Dom.getAncestorByClassName(target, this.SCORE_ITEM_CLASS); var indexClass = this.getIndexClassByEl(cont); 
var elements = YAHOO.util.Dom.getElementsByClassName(indexClass, null, 'tblQueContainer'); if (elements) { for (var i = 0; i < elements.length; i++) { var el = elements[i]; el.parentNode.removeChild(el); } }
}, getIndexClassByEl: function(el) { var classList = YAHOO.lang.trim(el.className).split(/\s+/); if (classList) { for (var i = 0; i < classList.length; i++) { var className = classList[i];
if (className.indexOf(this.SCORE_ITEM_INDEX_CLASS) == 0) return className; } } return null; }, getIndexClass: function(index) { return this.SCORE_ITEM_INDEX_CLASS + index; }, generateIndexClass: function()
{ ++this.counter; return this.getIndexClass(this.counter - 1); }, overrideScore: function(chkOverride) { var Dom = YAHOO.util.Dom; var template = Dom.getAncestorByClassName(chkOverride, this.SCORE_ITEM_CLASS); 
var bankScore = ''; Dom.getElementsByClassName(this.SCORE_BANK_SCORE_CONTROL_CLASS, 'input', template, function(hid) { bankScore = hid.value; }); 
Dom.getElementsByClassName(this.SCORE_CONTROL_CLASS, 'input', template, function(tbx) { tbx.disabled = !chkOverride.checked; tbx.value = bankScore; }); }, init: function(isBankBased) { this.isBankBased = isBankBased;
YAHOO.util.Event.on(window, 'load', function() { var frm = YAHOO.util.Dom.get('adminForm'); if (frm) { if (frm.onsubmit) this.OLD_SUBMIT_HANDLER = frm.onsubmit;
frm.onsubmit = YAHOO.ARISoft.page.multipleQuestion.submitHandler; } }, this, true); }, submitHandler: function() { YAHOO.ARISoft.page.multipleQuestion.collectScoreSection(); return false;
if (this.OLD_SUBMIT_HANDLER) this.OLD_SUBMIT_HANDLER(); } }; YAHOO.ARISoft.page.multipleQuestion.init(<?php echo !empty($baseOnBank) ? 'true' : 'false'; ?>); 
aris.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false)); ?>, function() {
var scoreData = <?php echo AriJSONHelper::encode($specificQuestion->getScoreDataFromXml($questionData, isset($questionOverrideData) ? $questionOverrideData : null)); ?>; if (!scoreData) return ; var MQ = YAHOO.ARISoft.page.multipleQuestion;
for (var i = 0; i < scoreData.length; i++) { MQ.addScoreSection(scoreData[i]); } }); aris.validators.validatorManager.addValidator( new aris.validators.customValidator(null, function(val) { var isValid = true;
var tbxScore = YAHOO.util.Dom.getElementsByClassName(YAHOO.ARISoft.page.multipleQuestion.SCORE_CONTROL_CLASS, 'input', 'tblQueContainer', function(tbxScore) { if (isValid) { var sScore = YAHOO.lang.trim(tbxScore.value);
if (sScore.length > 0) { var score = parseInt(sScore, 10); if (sScore != score || score < 0 || score > 100) { isValid = false; } } } }); return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionNoSetPercentScore'); ?>'})); <?php if ($uiMode != $uiModeList['Read']) { ?> aris.validators.validatorManager.addValidator(
new aris.validators.customValidator(null, function(val) { var isValid = true; var isSetCorrect = false; var isNotEmpty = false; 
var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.widgets.multiplierControls.originalIdAttr, 'trQueTemplate'); var templateCnt = templates ? templates.length : 0; if (templateCnt > 0) {
for (var i = 0; i < templateCnt; i++) { var template = templates[i]; var cbCorrect = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'cbCorrect');
if (cbCorrect && cbCorrect.checked) { isSetCorrect = true; var tbxAnswer = aris.DOM.getChildElementByAttribute(template, aris.widgets.multiplierControls.originalIdAttr, 'tbxAnswer'); var value = tbxAnswer.value;
if (value && value.replace(/^\s+|\s+$/g, '').length > 0) { isNotEmpty = true; break; } } } if (!isSetCorrect) {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotCorrect'); ?>'); isValid = false; } else if (!isNotEmpty) {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } } else {
this.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'); isValid = false; } return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'})); <?php } ?> </script>