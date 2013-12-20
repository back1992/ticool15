<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $profile = $processPage->getVar('profile'); $quizDataTable = $processPage->getVar('quizDataTable');
$queDataTable = $processPage->getVar('queDataTable'); $availQuizDataTable = $processPage->getVar('availQuizDataTable'); $availQueDataTable = $processPage->getVar('availQueDataTable'); $option = $processPage->getVar('option');
$mosConfig_live_site = $processPage->getVar('mosConfig_live_site'); $jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; $exportAllQuizzes = AriUtils::getParam($profile, 'ExportAllQuizzes', true);
$exportAllBankQuestions = AriUtils::getParam($profile, 'ExportAllBankQuestions', true); ?> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" /> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/container/assets/container.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/tabview/assets/tabview-core.css"/> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/tabview/assets/tabview.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/tabview/assets/border_tabs.css"/> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/menu/assets/skins/sam/menu.css"/> 
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/container/container-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datasource/datasource-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datatable/datatable-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/paginator/paginator-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/tabview/tabview-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/menu/menu-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/selector/selector-min.js"></script> <div style="text-align: right; padding: 3px;">
<input type="button" value="<?php AriWebHelper::displayResValue('Label.SaveSettings'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('export$ajax|saveOptions'); return false;" /> </div> <fieldset>
<legend><label for="chkExportQuiz">Export Quizzes</label><input type="checkbox" id="chkExportQuiz" name="zProfile[ExportQuizzes]"<?php if (AriUtils::getParam($profile, 'ExportQuizzes', true)): ?> checked="checked"<?php endif; ?> value="1" /></legend>
<div id="quizTabContainer" class="yui-navset"> <ul class="yui-nav"> <li class="selected"><a href="#quizBasicTab"><em><?php AriWebHelper::displayResValue('Label.ExportAllQuiz'); ?></em></a></li>
<li><a href="#quizAdvancedTab"><em><?php AriWebHelper::displayResValue('Label.ExportSpecQuiz'); ?></em></a></li> </ul> <div class="yui-content"> <div class="yui-hidden" id="quizBasicTab" style="padding: 4px">
<?php AriWebHelper::displayResValue('Label.ExportResults'); ?> : <?php $processPage->renderControl('lbQuizExportResults', array('class' => 'text_area')); ?> </div> <div class="yui-hidden" id="quizAdvancedTab" style="padding: 4px"> [
<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.panelQuizzes.show(); return false;"><?php AriWebHelper::displayResValue('Label.AddItem'); ?></a> |
<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('removeExportQuiz'); return false;"><?php AriWebHelper::displayResValue('Button.Remove'); ?></a> ] <?php $quizDataTable->render(); ?> </div>
</div> </div> </fieldset> <fieldset>
<legend><label for="chkExportQue">Export Bank Questions</label><input type="checkbox" id="chkExportQue" name="zProfile[ExportBankQuestions]"<?php if (AriUtils::getParam($profile, 'ExportBankQuestions', true)): ?> checked="checked"<?php endif; ?> value="1" /></legend>
<div id="queTabContainer" class="yui-navset"> <ul class="yui-nav"> <li class="selected"><a href="#queBasicTab"><em><?php AriWebHelper::displayResValue('Label.ExportAllQue'); ?></em></a></li>
<li><a href="#queAdvancedTab"><em><?php AriWebHelper::displayResValue('Label.ExportSpecQue'); ?></em></a></li> </ul> <div class="yui-content"> <div class="yui-hidden" id="queBasicTab" style="padding: 4px">
<?php AriWebHelper::displayResValue('Label.ExportQuestionsNote'); ?> </div> <div class="yui-hidden" id="quizAdvancedTab" style="padding: 4px"> [
<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.panelQuestions.show(); return false;"><?php AriWebHelper::displayResValue('Label.AddItem'); ?></a> |
<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('removeExportQue'); return false;"><?php AriWebHelper::displayResValue('Button.Remove'); ?></a> ] <?php $queDataTable->render(); ?> </div>
</div> </div> </fieldset> <div id="panelQuizzes"> <div class="hd"><?php AriWebHelper::displayResValue('Title.QuizList'); ?></div> <div class="bd" style="text-align: center;"> <?php
$availQuizDataTable->render(array('style' => array('width' => '695px', 'margin' => '0px auto'))); ?> </div> <div class="ft" style="text-align: center;">
<input type="button" class="button" value="Add" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('addToQuizQueue'); return false;" />
<input type="button" class="button" value="Cancel" onclick="YAHOO.ARISoft.page.panelQuizzes.hide(); return false;" /> </div> </div> <div id="panelQuestions"> <div class="hd"><?php AriWebHelper::displayResValue('Title.QuestionList'); ?></div>
<div class="bd" style="text-align: center;"> <fieldset> <legend><?php AriWebHelper::displayResValue('Label.Filter'); ?></legend> <table width="100%" border="0" cellpadding="0" cellspacing="0" id="tblBankFilter"> <tr>
<td style="width: 50%; text-align: left;"> <label for="lbBankCategories"><?php AriWebHelper::displayResValue('Label.Category'); ?></label> :
<?php $processPage->renderControl('lbBankCategories', array('class' => 'text_area dfm-filter', 'onchange' => 'submitbutton(\'apply_bankfilter\');')); ?> </td> </tr> </table> </fieldset> <?php
$availQueDataTable->render(array('style' => array('width' => '945px', 'margin' => '0px auto'))); ?> </div> <div class="ft" style="text-align: center;">
<input type="button" class="button" value="Add" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('addToQueQueue'); return false;" />
<input type="button" class="button" value="Cancel" onclick="YAHOO.ARISoft.page.panelQuestions.hide(); return false;" /> </div> </div> <input type="hidden" id="hidProfileId" name="profileId" value="<?php echo $profile->ProfileId; ?>" />
<input type="hidden" id="hidQuizType" name="zProfile[ExportAllQuizzes]" value="<?php echo $exportAllQuizzes ? '1' : '0'; ?>" />
<input type="hidden" id="hidQueType" name="zProfile[ExportAllBankQuestions]" value="<?php echo $exportAllBankQuestions ? '1' : '0'; ?>" /> <script type="text/javascript" language="javascript"> YAHOO.util.Event.onDOMReady(function() {
YAHOO.util.Event.onAvailable("phQuizMenu", function () { var quizMenu = new YAHOO.widget.Menu("quizMenu", { position: "dynamic", zindex: 1000}); quizMenu.addItems([
{ text: "<?php AriWebHelper::displayResValue('Label.No'); ?>", onclick: {fn: function() { YAHOO.ARISoft.page.pageManager.triggerAction('offQuizResults'); } } },
{ text: "<?php AriWebHelper::displayResValue('Label.Yes'); ?>", onclick: {fn: function() { YAHOO.ARISoft.page.pageManager.triggerAction('onQuizResults'); } } } ]); quizMenu.render("phQuizMenu"); 
YAHOO.util.Event.addListener("quizMenuToggler", "click", function(event) { var target = YAHOO.util.Event.getTarget(event); var region = YAHOO.util.Dom.getRegion(target);
quizMenu.cfg.setProperty("xy", [region.left - 5, region.bottom + 3]); quizMenu.show(); }, null, quizMenu); }); (function() { var dt = <?php echo $availQueDataTable->id; ?>; var ds = dt.getDataSource();
var oldHandler = ds.sendRequest; ds.sendRequest = function(oRequest, oCallback, oCaller) { var filterValues = YAHOO.ARISoft.page.bankFilterManager.getFilterValues(); for (var filterKey in filterValues) {
var filterValue = filterValues[filterKey]; oRequest += '&' + filterKey + '=' + encodeURIComponent(filterValue); } dt.showTableMessage(dt.get("MSG_LOADING"), dt.CLASS_LOADING); 
oldHandler.call(this, oRequest, oCallback, oCaller); }; })(); YAHOO.ARISoft.page.bankFilterManager = new YAHOO.ARISoft.page.dataFilterManager({container: 'tblBankFilter'}); function formWrapsElement(formId, el) {
var form = document.createElement('form'); form.name = formId; form.id = formId; var el = YAHOO.util.Dom.get(el); el.parentNode.removeChild(el); form.appendChild(el); document.body.appendChild(form); } 
formWrapsElement('frmExportQuizzes', 'panelQuizzes'); formWrapsElement('frmExportQuestions', 'panelQuestions'); YAHOO.namespace("ari.container"); YAHOO.ARISoft.page.panelQuizzes = new YAHOO.widget.Panel("panelQuizzes",
{ width:"700px", height:"500px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); YAHOO.ARISoft.page.panelQuizzes.render(); 
YAHOO.ARISoft.page.panelQuestions = new YAHOO.widget.Panel("panelQuestions", { width:"950px", height:"550px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200});
YAHOO.ARISoft.page.panelQuestions.render(); var quizTabView = new YAHOO.widget.TabView('quizTabContainer', {activeIndex:<?php echo $exportAllQuizzes ? '0' : '1'; ?>}); quizTabView.on('activeIndexChange', function(e) {
YAHOO.util.Dom.get('hidQuizType').value = e.newValue == 1 ? 0 : 1; }); var queTabContainer = new YAHOO.widget.TabView('queTabContainer', {activeIndex:<?php echo $exportAllBankQuestions ? '0' : '1'; ?>});
queTabContainer.on('activeIndexChange', function(e) { YAHOO.util.Dom.get('hidQueType').value = e.newValue == 1 ? 0 : 1; }); function switchExportResults(enabled) {
var selList = YAHOO.util.Selector.query('.yui-dt-col-ExportResults select', '<?php echo $quizDataTable->id; ?>'); if (!selList || selList.length == 0) return ; for (var i = 0; i < selList.length; i++) { var sel = selList[i];
sel.value = enabled ? 1 : 0; } }; YAHOO.ARISoft.page.pageManager.registerAction('offQuizResults', { onAction: function() { switchExportResults(false); } }); YAHOO.ARISoft.page.pageManager.registerAction('onQuizResults',
{ onAction: function() { switchExportResults(true); } }); YAHOO.ARISoft.page.pageManager.registerAction('apply_bankfilter', { onAction: function() { YAHOO.ARISoft.page.bankFilterManager.saveFilterValues();
var dt = <?php echo $availQueDataTable->id; ?>; YAHOO.ARISoft.widgets.dataTable.refresh(dt, YAHOO.ARISoft.widgets.dataTable.generateRequest(dt.getState(), dt)); } });
YAHOO.ARISoft.page.pageManager.registerActionGroup('exportGlobalAction', { onAction: YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $quizDataTable->id; ?>, enableValidation: true,
errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('export$ajax|saveOptions', { group: 'exportGlobalAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.ExportSettings'); ?>', onComplete: function() { },
onFailure: function() { } }); YAHOO.ARISoft.page.pageManager.registerActionGroup('exportQuizAction', { onAction: YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $quizDataTable->id; ?>,
enableValidation: true, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('export$ajax|removeExportQuiz', { group: 'exportQuizAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizRemoveFromQueue'); ?>', onComplete: function() {
YAHOO.ARISoft.widgets.dataTable.refresh(<?php echo $availQuizDataTable->id; ?>); }, onFailure: function() { YAHOO.ARISoft.widgets.dataTable.refresh(<?php echo $availQuizDataTable->id; ?>); } });
YAHOO.ARISoft.page.pageManager.registerAction('removeExportQuiz', { onAction: function() { if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['quizRemoveQueue'])) {
YAHOO.ARISoft.page.pageManager.triggerAction('export$ajax|removeExportQuiz'); } } }); YAHOO.ARISoft.page.pageManager.registerActionGroup('exportAvailQuizAction', { onAction: YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction,
dataTable: <?php echo $availQuizDataTable->id; ?>, enableValidation: true, formId: 'frmExportQuizzes', errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('export$ajax|addExportQuiz', { group: 'exportAvailQuizAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizAddToQueue'); ?>', onComplete: function() {
YAHOO.ARISoft.page.panelQuizzes.hide(); YAHOO.ARISoft.widgets.dataTable.refresh(<?php echo $quizDataTable->id; ?>); }, onFailure: function() { YAHOO.ARISoft.page.panelQuizzes.hide();
YAHOO.ARISoft.widgets.dataTable.refresh(<?php echo $quizDataTable->id; ?>); } }); YAHOO.ARISoft.page.pageManager.registerActionGroup('exportQueAction', { onAction: YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction,
dataTable: <?php echo $queDataTable->id; ?>, enableValidation: true, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('export$ajax|removeExportQue', { group: 'exportQueAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QueRemoveFromQueue'); ?>', onComplete: function() {
var dt = <?php echo $availQueDataTable->id; ?>; YAHOO.ARISoft.widgets.dataTable.refresh(dt, YAHOO.ARISoft.widgets.dataTable.generateRequest(dt.getState(), dt)); }, onFailure: function() {
var dt = <?php echo $availQueDataTable->id; ?>; YAHOO.ARISoft.widgets.dataTable.refresh(dt, YAHOO.ARISoft.widgets.dataTable.generateRequest(dt.getState(), dt)); } }); YAHOO.ARISoft.page.pageManager.registerAction('removeExportQue', {
onAction: function() { if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['queRemoveQueue'])) { YAHOO.ARISoft.page.pageManager.triggerAction('export$ajax|removeExportQue'); } } }); 
YAHOO.ARISoft.page.pageManager.registerActionGroup('exportAvailQueAction', { onAction: YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $availQueDataTable->id; ?>, enableValidation: true,
formId: 'frmExportQuestions', errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('export$ajax|addExportQue', { group: 'exportAvailQueAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QueAddToQueue'); ?>', onComplete: function() {
YAHOO.ARISoft.page.panelQuestions.hide(); YAHOO.ARISoft.widgets.dataTable.refresh(<?php echo $queDataTable->id; ?>); }, onFailure: function() { YAHOO.ARISoft.page.panelQuestions.hide();
YAHOO.ARISoft.widgets.dataTable.refresh(<?php echo $queDataTable->id; ?>); } }); YAHOO.ARISoft.page.pageManager.registerAction('addToQuizQueue', { onAction: function() {
if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['quizExportQueue'])) { YAHOO.ARISoft.page.pageManager.triggerAction('export$ajax|addExportQuiz'); } } });
YAHOO.ARISoft.page.pageManager.registerAction('addToQueQueue', { onAction: function() { if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['queExportQueue'])) {
YAHOO.ARISoft.page.pageManager.triggerAction('export$ajax|addExportQue'); } } }); YAHOO.ARISoft.page.pageManager.registerAction('export', { onAction: function() {
if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['export'])) { submitbutton('export$export'); } } }); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvExport', function(val) { var isValid = false; var Dom = YAHOO.util.Dom; var chkExportQuiz = Dom.get('chkExportQuiz'); var chkExportQue = Dom.get('chkExportQue'); 
if (chkExportQuiz.checked) { var hidQuizType = Dom.get('hidQuizType'); if (hidQuizType.value == "1") isValid = true; else { var rs = <?php echo $quizDataTable->id; ?>.getRecordSet();
isValid = (rs.getLength() > 0); } } if (!isValid && chkExportQue.checked) { var hidQueType = Dom.get('hidQueType'); if (hidQueType.value == "1") isValid = true; else {
var rs = <?php echo $queDataTable->id; ?>.getRecordSet(); isValid = (rs.getLength() > 0); } } return isValid; },
{validationGroups: ['export'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.Export'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('cvQuizAddToQueue',
function(val) { return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $availQuizDataTable->id; ?>, 'yui-dt-col-QuizId'); },
{validationGroups: ['quizExportQueue'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.Export'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('cvQueAddToQueue',
function(val) { return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $availQueDataTable->id; ?>, 'yui-dt-col-BankQuestionId'); },
{validationGroups: ['queExportQueue'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.Export'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvQuizRemoveFromQueue', function(val) { return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $quizDataTable->id; ?>, 'yui-dt-col-QuizId'); },
{validationGroups: ['quizRemoveQueue'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.ExportRemove'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvQueRemoveFromQueue', function(val) { return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $queDataTable->id; ?>, 'yui-dt-col-QuestionId'); },
{validationGroups: ['queRemoveQueue'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.ExportRemove'); ?>'})); }); </script>