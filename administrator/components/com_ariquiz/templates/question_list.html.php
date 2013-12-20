<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $option = $processPage->getVar('option'); $mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$dataTable = $processPage->getVar('dataTable'); $bankDataTable = $processPage->getVar('bankDataTable'); $jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; $quizId = $processPage->getVar('quizId'); ?>
 <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" /> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/container/assets/container.css" /> 
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/container/container-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datatable/datatable-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/paginator/paginator-min.js"></script> <?php $dataTable->render(); ?>
<div id="panelCSVImport" style="visibility: hidden;"> <div class="hd"><?php AriWebHelper::displayResValue('Toolbar.CSVImport'); ?></div> <div class="bd" style="text-align: center; overflow: auto;"> <fieldset>
<legend><?php AriWebHelper::displayResValue('Label.UploadExportFile'); ?></legend> <div> <input type="file" id="importDataCSVFile" name="importDataCSVFile" class="text_area" size="70" />
<input type="button" class="button" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('uploadImport'); return false;" value="<?php AriWebHelper::displayResValue('Toolbar.Import'); ?>" /> </div> </fieldset> <fieldset>
<legend><?php AriWebHelper::displayResValue('Label.ImportExportFileFromDir'); ?></legend> <div> <?php $processPage->renderControl('tbxImportDir', array('class' => 'text_area', 'size' => '70')); ?>
<input type="button" class="button" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('importFromDir'); return false;" value="<?php AriWebHelper::displayResValue('Toolbar.Import'); ?>" /> </div> </fieldset> </div>
<div class="ft" style="text-align: center;"> <div> <input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelCSVImport.hide(); return false;" /> </div> </div>
</div> <div id="panelCopy" style="visibility: hidden;"> <div class="hd"><?php AriWebHelper::displayResValue('Label.CopySettings'); ?></div> <div class="bd" style="text-align: center;">
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="questionContainer"> <tr> <td class="right" style="width: 15%;"> <label for="lbCopyQuiz"><?php AriWebHelper::displayResValue('Label.Quiz'); ?></label> : </td>
<td class="left"> <?php $processPage->renderControl('lbCopyQuiz', array('class' => 'text_area', 'onchange' => 'YAHOO.ARISoft.page.pageManager.triggerAction(\'getCopyQuestionCategories\');')); ?> </td> </tr> <tr>
<td class="right"> <label for="lbCopyQueCategory"><?php AriWebHelper::displayResValue('Label.QuestionCategory'); ?></label> : </td> <td class="left"> <select id="lbCopyQueCategory" name="lbCopyQueCategory">
<option value="0"><?php AriWebHelper::displayResValue('Label.NotSelectedItem'); ?></option> </select> </td> </tr> </table> </div> <div class="ft" style="text-align: center;">
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyCopy');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelCopy.hide(); return false;" /> </div> </div> <div id="panelMove" style="visibility: hidden;">
<div class="hd"><?php AriWebHelper::displayResValue('Label.MoveSettings'); ?></div> <div class="bd" style="text-align: center;"> <table width="100%" border="0" cellpadding="3" cellspacing="0" class="questionContainer" id="tblMassSettings">
<tr> <td class="right" style="width: 15%;"> <label for="lbMoveQuiz"><?php AriWebHelper::displayResValue('Label.Quiz'); ?></label> : </td> <td class="left">
<?php $processPage->renderControl('lbMoveQuiz', array('class' => 'text_area', 'onchange' => 'YAHOO.ARISoft.page.pageManager.triggerAction(\'getMoveQuestionCategories\');')); ?> </td> </tr> <tr> <td class="right">
<label for="lbMoveQueCategory"><?php AriWebHelper::displayResValue('Label.QuestionCategory'); ?></label> : </td> <td class="left"> <select id="lbMoveQueCategory" name="lbMoveQueCategory">
<option value="0"><?php AriWebHelper::displayResValue('Label.NotSelectedItem'); ?></option> </select> </td> </tr> </table> </div> <div class="ft" style="text-align: center;">
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMove');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelMove.hide(); return false;" /> </div> </div> <div id="panelMassEdit" style="visibility: hidden;">
<div class="hd"><?php AriWebHelper::displayResValue('Label.MassEdit'); ?></div> <div class="bd ari-settings-group" style="text-align: center; overflow: auto;">
<input type="checkbox" id="chkMassMainSettingsSwitcher" class="text_area ari-settings-switcher" checked="checked" style="display: none;" /> <table width="100%" border="0" cellpadding="3" cellspacing="0"> <tr>
<td style="width: 25%; text-align: right;"> <label for="lbMassQuestionCategories" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QuestionCategory'); ?></label> : </td>
<td style="text-align: left;"> <?php $processPage->renderControl('lbMassQuestionCategories', array('class' => 'text_area')); ?> </td> </tr> <tr> <td style="text-align: right;">
<label for="tbxMassScore" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.Score'); ?></label> : </td> <td style="text-align: left;">
<input type="text" id="tbxMassScore" name="tbxMassScore" size="4" /> </td> </tr> </table> </div> <div class="ft" style="text-align: center;"> <div>
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMassEdit');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Label.Clear'); ?>" onclick="this.form.reset();YAHOO.ARISoft.page.pageManager.massEditSettings.resetSettings();" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelMassEdit.hide(); return false;" /> </div> <div style="text-align: left;"> <br/>
<b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b> <?php AriWebHelper::displayResValue('Text.MassEditNote'); ?> </div> </div> </div> <div id="panelBankCopy" style="visibility: hidden;">
<div class="hd"><?php AriWebHelper::displayResValue('Label.CopyToBank'); ?></div> <div class="bd" style="text-align: center;"> <table width="100%" border="0" cellpadding="3" cellspacing="0"> <tr>
<td style="width: 35%; text-align: right;"> <label for="lbCopyBankQuestionCategory"><?php AriWebHelper::displayResValue('Label.Category'); ?></label> : </td> <td style="text-align: left;">
<?php $processPage->renderControl('lbCopyBankQuestionCategory', array('class' => 'text_area')); ?> </td> </tr> <tr> <td style="text-align: right;">
<label for="chkBasedOnBank"><?php AriWebHelper::displayResValue('Label.BasedOnBank'); ?></label> : </td> <td style="text-align: left;"> <input type="checkbox" id="chkBasedOnBank" name="chkBasedOnBank" /> </td> </tr>
</table> </div> <div class="ft" style="text-align: center;"> <input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyBankCopy');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelBankCopy.hide(); return false;" /> </div> </div> <div id="panelBankImport" style="visibility: hidden;">
<div class="hd"><?php AriWebHelper::displayResValue('Label.ImportFromBank'); ?></div> <div class="bd" style="text-align: center;"> <fieldset> <legend><?php AriWebHelper::displayResValue('Label.Filter'); ?></legend>
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="tblBankFilter"> <tr> <td style="width: 50%; text-align: left;"> <label for="lbBankCategories"><?php AriWebHelper::displayResValue('Label.Category'); ?></label> :
<?php $processPage->renderControl('lbBankCategories', array('class' => 'text_area dfm-filter', 'onchange' => 'submitbutton(\'apply_bankfilter\');')); ?> </td> <td style="text-align: left;">
<label for="chkLoadUsedQuestions"><?php AriWebHelper::displayResValue('Label.LoadUsedQuestions'); ?></label> :
<input type="checkbox" id="chkLoadUsedQuestions" onclick="submitbutton('apply_bankfilter');" name="chkLoadUsedQuestions" class="dfm-filter" value="1" checked="checked" /> </td> </tr> </table> </fieldset> <fieldset>
<legend><?php AriWebHelper::displayResValue('Label.Settings'); ?></legend> <table width="100%" border="0" cellpadding="3" cellspacing="0" id="tblBankSettings"> <tr> <td style="width: 50%; text-align: left;">
<label for="lbQuestionCategories"><?php AriWebHelper::displayResValue('Label.QCategories'); ?> :</label> <?php $processPage->renderControl('lbQuestionCategories', array('class' => 'text_area')); ?> </td>
<td style="text-align: left;"> <label for="tbxBankScore"><?php AriWebHelper::displayResValue('Label.Score'); ?> :</label> <input type="text" id="tbxBankScore" name="tbxBankScore" size="4" /> </td> </tr> <tr>
<td colspan="2" class="left"> <input type="reset" value="<?php AriWebHelper::displayResValue('Label.Clear'); ?>" /> </td> </tr> </table> </fieldset> <?php
$bankDataTable->render(array('style' => array('width' => '945px', 'margin' => '0px auto'))); ?> </div> <div class="ft" style="text-align: center;">
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Import'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('importFromBank');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelBankImport.hide(); return false;" /> </div> </div> <script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() { var page = YAHOO.ARISoft.page, pageManager = page.pageManager, aDom = YAHOO.ARISoft.DOM, validators = YAHOO.ARISoft.validators; aDom.moveTo('panelBankImport');
aDom.wrapWithElement('form', 'tblBankSettings', {id: 'frmBankSettings', name: 'frmBankSettings'}); aDom.wrapWithElement('form', 'dtQuestionsBankQuestions', {id: 'frmBankData', name: 'frmBankData'}); 
aDom.moveTo(aDom.wrapWithElement('form', 'panelMassEdit', {id: 'frmMassEdit', name: 'frmMassEdit'})); aDom.moveTo(aDom.wrapWithElement('form', 'panelCopy', {id: 'frmCopy', name: 'frmCopy'}));
aDom.moveTo(aDom.wrapWithElement('form', 'panelMove', {id: 'frmMove', name: 'frmMove'})); /* Mass edit settings panel */ pageManager.massEditSettings = new YAHOO.ARISoft.widgets.settingsPanel('panelMassEdit', {}); /* CSV import panel */
page.panelCSVImport  = new YAHOO.widget.Panel("panelCSVImport", { width:"510px", height:"170px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); page.panelCSVImport.render(); (function() {
var dt = <?php echo $bankDataTable->id; ?>; var ds = dt.getDataSource(); var oldHandler = ds.sendRequest; ds.sendRequest = function(oRequest, oCallback, oCaller) {
var filterValues = YAHOO.ARISoft.page.bankFilterManager.getFilterValues(); for (var filterKey in filterValues) { var filterValue = filterValues[filterKey]; oRequest += '&' + filterKey + '=' + encodeURIComponent(filterValue); }
dt.showTableMessage(dt.get("MSG_LOADING"), dt.CLASS_LOADING); oldHandler.call(this, oRequest, oCallback, oCaller); }; })(); page.bankFilterManager = new page.dataFilterManager({container: 'tblBankFilter'}); 
page.panelBankImport = new YAHOO.widget.Panel("panelBankImport", { width:"950px", height:"620px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); page.panelBankImport.render();
page.panelBankNeedReload = false; page.panelBankCopy = new YAHOO.widget.Panel("panelBankCopy", { width:"450px", height:"120px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200});
page.panelBankCopy.render(); page.panelMassEdit = new YAHOO.widget.Panel("panelMassEdit", { width:"510px", height:"140px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200});
page.panelMassEdit.render(); page.panelCopy = new YAHOO.widget.Panel("panelCopy", { width:"450px", height:"120px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); page.panelCopy.render(); 
page.panelMove = new YAHOO.widget.Panel("panelMove", { width:"450px", height:"105px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); page.panelMove.render(); page.showCopyPanel = function() {
page.panelCopy.show(); }; pageManager.registerAction('getCopyQuestionCategories', { onAction: function(action, config) { var pm = this; var ddlQuiz = YAHOO.util.Dom.get('lbCopyQuiz'); ddlQuiz.disabled = true;
YAHOO.ARISoft.ajax.ajaxManager.asyncRequest( 'GET', 'index2.php?option=' + this.option + '&task=question_list$ajax|getQuestionCategories&quizId=' + ddlQuiz.value, { cache: false, success: function(oResponse) {
var categories = null; var args = oResponse.argument; var config = args.config || {}; try { var responseText = oResponse.responseText; eval('categories = ' + responseText); } catch (e) {};
var lbCopyQueCategory = YAHOO.util.Dom.get('lbCopyQueCategory'); lbCopyQueCategory.options.length = 0; lbCopyQueCategory.options[0] = new Option('<?php AriWebHelper::displayResValue('Label.NotSelectedItem'); ?>', 0);
if (categories) { for (var i = 0, cnt = categories.length; i < cnt; i++) { var category = categories[i]; var opt = new Option(category.CategoryName, category.QuestionCategoryId);
lbCopyQueCategory.options[lbCopyQueCategory.options.length] = opt; } }; ddlQuiz.disabled = false; if (config.onComplete) config.onComplete.call(this); }, failure: function(oResponse) {
var args = oResponse.argument; var config = args.config || {}; if (config.errorMessage) alert(config.errorMessage); ddlQuiz.disabled = false; if (config.onFailure) config.onFailure.call(this); },
argument: { config: config }, scope: pm }, null, null, { containerId: 'panelCopy', loadingMessage: config.loadingMessage, overlayCfg: { visible:false,
constraintoviewport:true, close: false, draggable: false, autofillheight: 'body', zIndex: 10000 } }); }, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>',
completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('getMoveQuestionCategories', { onAction: function(action, config) { var pm = this; var ddlQuiz = YAHOO.util.Dom.get('lbMoveQuiz'); ddlQuiz.disabled = true;
YAHOO.ARISoft.ajax.ajaxManager.asyncRequest( 'GET', 'index2.php?option=' + this.option + '&task=question_list$ajax|getQuestionCategories&quizId=' + ddlQuiz.value, { cache: false, success: function(oResponse) {
var categories = null; var args = oResponse.argument; var config = args.config || {}; try { var responseText = oResponse.responseText; eval('categories = ' + responseText); } catch (e) {};
var lbMoveQueCategory = YAHOO.util.Dom.get('lbMoveQueCategory'); lbMoveQueCategory.options.length = 0; lbMoveQueCategory.options[0] = new Option('<?php AriWebHelper::displayResValue('Label.NotSelectedItem'); ?>', 0);
if (categories) { for (var i = 0, cnt = categories.length; i < cnt; i++) { var category = categories[i]; var opt = new Option(category.CategoryName, category.QuestionCategoryId);
lbMoveQueCategory.options[lbMoveQueCategory.options.length] = opt; } }; ddlQuiz.disabled = false; if (config.onComplete) config.onComplete.call(this); }, failure: function(oResponse) {
var args = oResponse.argument; var config = args.config || {}; if (config.errorMessage) alert(config.errorMessage); ddlQuiz.disabled = false; if (config.onFailure) config.onFailure.call(this); },
argument: { config: config }, scope: pm }, null, null, { containerId: 'panelMove', loadingMessage: config.loadingMessage, overlayCfg: { visible:false,
constraintoviewport:true, close: false, draggable: false, autofillheight: 'body', zIndex: 10000 } }); }, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>',
completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); pageManager.registerActionGroup('bankAction', { onAction: page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $bankDataTable->id; ?>, enableValidation: true,
errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); pageManager.registerAction('apply_bankfilter', { onAction: function() { page.bankFilterManager.saveFilterValues(); var dt = <?php echo $bankDataTable->id; ?>;
YAHOO.ARISoft.widgets.dataTable.refresh(dt, YAHOO.ARISoft.widgets.dataTable.generateRequest(dt.getState(), dt)); } }); pageManager.registerActionGroup('questionAction', { onAction: page.actionHandlers.simpleDatatableAction,
dataTable: <?php echo $dataTable->id; ?>, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); pageManager.registerAction('importFromBank', { onAction: function() { if (!validators.alertSummaryValidators.validate(['bankImport'])) return ; page.panelBankNeedReload = true; page.panelBankImport.hide();
pageManager.triggerAction('question_list$ajax|importFromBank'); } }); pageManager.registerAction('applyMassEdit', { onAction: function() { if (!validators.alertSummaryValidators.validate(['massEdit'])) return ; 
page.panelMassEdit.hide(); pageManager.triggerAction('question_list$ajax|massEdit'); } }); pageManager.registerAction('applyCopy', { onAction: function() { if (!validators.alertSummaryValidators.validate(['copy'])) return ; 
page.panelCopy.hide(); pageManager.triggerAction('question_list$ajax|copy'); } }); pageManager.registerAction('applyMove', { onAction: function() { if (!validators.alertSummaryValidators.validate(['move'])) return ; 
page.panelMove.hide(); pageManager.triggerAction('question_list$ajax|move'); } }); pageManager.registerAction('applyBankCopy', { onAction: function() { if (!validators.alertSummaryValidators.validate(['bankCopy'])) return ; 
page.panelBankCopy.hide(); pageManager.triggerAction('question_list$ajax|bankCopy'); } }); pageManager.registerAction('question_list$ajax|massEdit', { onAction: function(action, config) {
config.postData = YAHOO.util.Connect.setForm('frmMassEdit'); page.actionHandlers.simpleDatatableAction.call(this, action, config); }, group: 'questionAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.MassEdit'); ?>' }); pageManager.registerAction('question_list$ajax|importFromBank', { onAction: function(action, config) {
config.postData = YAHOO.util.Connect.setForm('frmBankSettings'); var query = YAHOO.util.Connect.setForm('frmBankData'); if (query) { if (config.postData) config.postData += '&' + query; else config.postData = query; } 
page.actionHandlers.simpleDatatableAction.call(this, action, config); }, group: 'questionAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuestionImported'); ?>' });
pageManager.registerAction('question_list$ajax|move', { onAction: function(action, config) { config.postData = YAHOO.util.Connect.setForm('frmMove'); page.actionHandlers.simpleDatatableAction.call(this, action, config); },
group: 'questionAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuestionMove'); ?>' }); pageManager.registerAction('question_list$ajax|copy', { onAction: function(action, config) {
config.postData = YAHOO.util.Connect.setForm('frmCopy'); page.actionHandlers.simpleDatatableAction.call(this, action, config); }, group: 'questionAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuestionCopy'); ?>' }); pageManager.registerAction('question_list$ajax|bankCopy', { group: 'questionAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuestionBankCopy'); ?>' }); pageManager.registerAction('question_list$ajax|delete', { group: 'questionAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuestionDelete'); ?>' }); pageManager.registerAction('question_list$ajax|orderup', { group: 'questionAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.ChangeQuestionOrder'); ?>' }); pageManager.registerAction('question_list$ajax|orderdown', { group: 'questionAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.ChangeQuestionOrder'); ?>' }); pageManager.registerAction('from_bank', { onAction: function() { if (page.panelBankNeedReload) { page.panelBankNeedReload = false;
var dt = <?php echo $bankDataTable->id; ?>; YAHOO.ARISoft.widgets.dataTable.refresh(dt, YAHOO.ARISoft.widgets.dataTable.generateRequest(dt.getState(), dt)); } page.panelBankImport.show(); } });
pageManager.registerAction('to_bank', { onAction: function() { page.panelBankCopy.show(); } }); pageManager.registerAction('mass_edit', { onAction: function() { page.panelMassEdit.show(); } });
pageManager.registerAction('copy', { onAction: function() { page.showCopyPanel(); } }); pageManager.registerAction('move', { onAction: function() { page.panelMove.show(); } });
pageManager.subscribe('beforeAction', function(o) { if (o.action == 'question_list$ajax|delete') page.panelBankNeedReload = true; }); pageManager.registerAction('csv_import', { onAction: function() { page.panelCSVImport.show();
} }); pageManager.registerAction('uploadImport', { onAction: function() { if (validators.alertSummaryValidators.validate(['<?php echo $processPage->VG_IMPORT_UPLOAD; ?>'])) {
pageManager.triggerAction('<?php echo $processPage->executionTask . '$uploadCSVImport'; ?>'); } } }); pageManager.registerAction('importFromDir', { onAction: function() {
if (validators.alertSummaryValidators.validate(['<?php echo $processPage->VG_IMPORT_DIR; ?>'])) { pageManager.triggerAction('<?php echo $processPage->executionTask . '$csvImportFromDir'; ?>'); } } }); 
validators.validatorManager.addValidator( new validators.requiredValidator('importDataCSVFile',
{validationGroups: ['<?php echo $processPage->VG_IMPORT_UPLOAD; ?>'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.ImportFileRequired'); ?>'})); }); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvBankImport', function(val) { var isValid = false; var dt = <?php echo $bankDataTable->id; ?>; var Dom = YAHOO.util.Dom;
Dom.getElementsByClassName('yui-dt-col-BankQuestionId', null, dt.getTbodyEl(), function(el) { var chk = Dom.getElementBy(function(sEl) { return (sEl.type == 'checkbox'); }, 'input', el); 
if (chk.checked) isValid = true; }); return isValid; }, {validationGroups: ['bankImport'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.BankImport'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.rangeValidator('tbxBankScore', 0, null, YAHOO.ARISoft.validators.rangeValidatorType.int, { validationGroups: ['bankImport'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.BankImport'); ?>'
})); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('tbxMassScore', function(val) { if (YAHOO.ARISoft.page.pageManager.massEditSettings.isDisabledSettingEl(val.ctrlId)) return true;
var validators = YAHOO.ARISoft.validators.validatorManager.getFailedValidator(['massEditScore']); if (validators.length == 0) return true; val.errorMessage = validators[0].errorMessage; return false; }, {
validationGroups: ['massEdit'] })); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.requiredValidator('tbxMassScore', { validationGroups: ['massEditScore'],
errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionScoreRequired'); ?>' })); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.rangeValidator('tbxMassScore', 0, null, YAHOO.ARISoft.validators.rangeValidatorType.int, { validationGroups: ['massEditScore'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionScore'); ?>'
})); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('cvMassEditSettings', function(val) { return YAHOO.ARISoft.page.pageManager.massEditSettings.getActiveElementsCount() > 0; },
{validationGroups: ['massEdit'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.MassEditSettingsRequired'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvMassEditCount', function(val) { return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $dataTable->id; ?>, 'yui-dt-col-QuestionId'); },
{validationGroups: ['massEdit', 'copy', 'move', 'bankCopy'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.SelectAtLeastOneItem'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.rangeValidator('lbCopyQuiz', 1, null, YAHOO.ARISoft.validators.rangeValidatorType.int, { validationGroups: ['copy'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.SelectQuiz'); ?>' }));
YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.rangeValidator('lbMoveQuiz', 1, null, YAHOO.ARISoft.validators.rangeValidatorType.int, { validationGroups: ['move'],
errorMessage : '<?php AriWebHelper::displayResValue('Validator.SelectQuiz'); ?>' })); </script> <input type="hidden" name="quizId" value="<?php echo $quizId; ?>" />