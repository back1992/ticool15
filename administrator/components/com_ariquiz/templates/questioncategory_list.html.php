<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $option = $processPage->getVar('option'); $mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$dataTable = $processPage->getVar('dataTable'); $jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; $quizId = $processPage->getVar('quizId'); ?> 
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" /> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/container/assets/container.css" /> 
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/container/container-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datatable/datatable-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/paginator/paginator-min.js"></script> <?php $dataTable->render(); ?>
<div id="panelMassEdit" style="visibility: hidden;"> <div class="hd"><?php AriWebHelper::displayResValue('Label.MassEdit'); ?></div> <div class="bd ari-settings-group" style="text-align: center; overflow: auto;">
<input type="checkbox" id="chkMassMainSettingsSwitcher" class="ari-settings-switcher" checked="checked" style="display: none;" /> <table width="100%" border="0" cellpadding="3" cellspacing="0" id="tblMassSettings"> <tr>
<td style="width: 25%; text-align: right;"> <label for="tbxMassQuestionCount" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QuestionCount'); ?></label> : </td> <td style="text-align: left;">
<input type="text" id="tbxMassQuestionCount" name="MassEdit[QuestionCount]" size="4" value="0" /> </td> </tr> <tr> <td style="text-align: right;">
<label for="tbxMassQuestionTime" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QuestionTime'); ?></label> : </td> <td style="text-align: left;">
<input type="text" id="tbxMassQuestionTime" name="MassEdit[QuestionTime]" size="4" value="0" /> </td> </tr> </table> </div> <div class="ft" style="text-align: center;"> <div>
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMassEdit');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Label.Clear'); ?>" onclick="this.form.reset();YAHOO.ARISoft.page.pageManager.massEditSettings.resetSettings();" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelMassEdit.hide(); return false;" /> </div> <div style="text-align: left;"> <br/>
<b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b> <?php AriWebHelper::displayResValue('Text.MassEditNote'); ?> </div> </div> </div> <script type="text/javascript" language="javascript"> YAHOO.util.Event.onDOMReady(function()
{ YAHOO.ARISoft.DOM.moveTo(YAHOO.ARISoft.DOM.wrapWithElement('form', 'panelMassEdit', {id: 'frmMassEdit', name: 'frmMassEdit'})); /* Mass edit settings panel */
YAHOO.ARISoft.page.pageManager.massEditSettings = new YAHOO.ARISoft.widgets.settingsPanel('panelMassEdit', {}); YAHOO.ARISoft.page.panelMassEdit = new YAHOO.widget.Panel("panelMassEdit",
{ width:"510px", height:"140px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); YAHOO.ARISoft.page.panelMassEdit.render(); YAHOO.ARISoft.page.pageManager.registerActionGroup('categoryAction', {
onAction: YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $dataTable->id; ?>, enableValidation: true, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); YAHOO.ARISoft.page.pageManager.registerAction('questioncategory_list$ajax|delete', { group: 'categoryAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuestionCategoryDelete'); ?>' });
YAHOO.ARISoft.page.pageManager.registerAction('questioncategory_list$ajax|massEdit', { onAction: function(action, config) { config.postData = YAHOO.util.Connect.setForm('frmMassEdit'); 
YAHOO.ARISoft.page.actionHandlers.simpleDatatableAction.call(this, action, config); }, group: 'categoryAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.MassEdit'); ?>' });
YAHOO.ARISoft.page.pageManager.registerAction('applyMassEdit', { onAction: function() { if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['massEdit'])) return ; YAHOO.ARISoft.page.panelMassEdit.hide();
YAHOO.ARISoft.page.pageManager.triggerAction('questioncategory_list$ajax|massEdit'); } }); YAHOO.ARISoft.page.pageManager.registerAction('mass_edit', { onAction: function() { YAHOO.ARISoft.page.panelMassEdit.show(); } });
}); YAHOO.ARISoft.page.pageManager.subscribe('beforeAction', function(o) { if (o.action != 'questioncategory_list$ajax|delete') return ; 
var isDeleteQuestions = confirm('<?php AriWebHelper::displayResValue('Warning.DeleteQueFromQCategory'); ?>'); document.getElementById('zq_deleteQuestions').value = isDeleteQuestions ? '1' : '0'; }); 
YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('tbxMassQuestionCount', function(val) { if (YAHOO.ARISoft.page.pageManager.massEditSettings.isDisabledSettingEl(val.ctrlId)) return true; 
var validators = YAHOO.ARISoft.validators.validatorManager.getFailedValidator(['massEditCount']); if (validators.length == 0) return true; val.errorMessage = validators[0].errorMessage; return false; }, {
validationGroups: ['massEdit'] })); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('tbxMassQuestionTime', function(val) {
if (YAHOO.ARISoft.page.pageManager.massEditSettings.isDisabledSettingEl(val.ctrlId)) return true; var validators = YAHOO.ARISoft.validators.validatorManager.getFailedValidator(['massEditTime']); if (validators.length == 0) return true; 
val.errorMessage = validators[0].errorMessage; return false; }, { validationGroups: ['massEdit'] })); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.rangeValidator('tbxMassQuestionCount', 0, null, YAHOO.ARISoft.validators.rangeValidatorType.int, { validationGroups: ['massEditCount'],
errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionCount'); ?>' })); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.rangeValidator('tbxMassQuestionTime', 0, null, YAHOO.ARISoft.validators.rangeValidatorType.int, { validationGroups: ['massEditTime'],
errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionTime'); ?>' })); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('cvMassEditSettings', function(val) {
return YAHOO.ARISoft.page.pageManager.massEditSettings.getActiveElementsCount() > 0; }, {validationGroups: ['massEdit'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.MassEditSettingsRequired'); ?>'}));
YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('cvMassEditCount', function(val) {
return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $dataTable->id; ?>, 'yui-dt-col-QuestionCategoryId'); },
{validationGroups: ['massEdit'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.SelectAtLeastOneItem'); ?>'})); </script> <input type="hidden" name="quizId" id="quizId" value="<?php echo $quizId; ?>" />
<input type="hidden" id="zq_deleteQuestions" name="zq_deleteQuestions" value="0" />