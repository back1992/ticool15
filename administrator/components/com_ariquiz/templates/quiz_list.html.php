<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $option = $processPage->getVar('option'); $mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$dataTable = $processPage->getVar('dataTable'); $jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; ?> 
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" /> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/container/assets/container.css" /> 
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/container/container-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datatable/datatable-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/paginator/paginator-min.js"></script> <div style="text-align: right;">
<?php AriWebHelper::displayResValue('Label.Filter'); ?> : <?php $processPage->renderControl('lbFilterCategory', array('class' => 'text_area')); ?> <?php $processPage->renderControl('lbFilterStatus', array('class' => 'text_area')); ?>
<input type="button" class="button" onclick="submitbutton('quiz_list$ajax|filters'); return false;" value="<?php echo AriWebHelper::displayResValue('Label.Apply'); ?>" /> </div> <br/> <?php $dataTable->render(); ?> 
<div id="panelCopy" style="visibility: hidden;"> <div class="hd"><?php AriWebHelper::displayResValue('Label.CopySettings'); ?></div> <div class="bd" style="text-align: center;"> <table width="100%" border="0" cellpadding="3" cellspacing="0">
<tr> <td style="width: 10%; text-align: right;"> <label><?php AriWebHelper::displayResValue('Label.Name'); ?></label> : </td> <td style="text-align: left;">
<input type="text" id="tbxCopyQuizName" name="tbxCopyQuizName" value="Copy of {$QuizName}" size="100" /> </td> </tr> </table> </div> <div class="ft" style="text-align: center;"> <div>
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyCopy');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelCopy.hide(); return false;" /> </div> <div style="text-align: left;"> <br/>
<b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b> <?php AriWebHelper::displayResValue('Text.CopyQuiz'); ?> </div> </div> </div> <div id="panelMassEdit" style="visibility: hidden;">
<div class="hd"><?php AriWebHelper::displayResValue('Label.MassEdit'); ?></div> <div class="bd" style="text-align: center; overflow: auto;" id="tblMassSettings"> <fieldset class="ari-settings-group">
<legend><label for="chkMassMainSettingsSwitcher"><?php AriWebHelper::displayResValue('Label.MainSettings'); ?></label> <input type="checkbox" id="chkMassMainSettingsSwitcher" class="text_area ari-settings-switcher" checked="checked" /></legend>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="questionContainer"> <tr> <td class="right" style="width: 25%;">
<label for="lbMassCategory" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.Category'); ?></label> : </td> <td class="left" colspan="3">
<?php $processPage->renderControl('lbMassCategory', array('class' => 'text_area')); ?> </td> </tr> <tr> <td class="right">
<label for="tbxMassTotalTime" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.TotalTime'); ?></label> : </td> <td class="left" style="width: 25%;">
<input type="text" id="tbxMassTotalTime" name="MassEdit[TotalTime]" size="4" /> </td> <td class="right" style="width: 25%;">
<label for="tbxMassPassedScore" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.PassedScore'); ?></label> : </td> <td class="left" style="width: 25%;">
<input type="text" id="tbxMassPassedScore" name="MassEdit[PassedScore]" size="4" /> </td> </tr> <tr> <td class="right">
<label for="tbxMassQuestionCount" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QuestionCount'); ?></label> : </td> <td class="left">
<input type="text" id="tbxMassQuestionCount" name="MassEdit[QuestionCount]" size="4" /> </td> <td class="right">
<label for="tbxMassQuestionTime" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QuestionTime'); ?></label> : </td> <td class="left">
<input type="text" id="tbxMassQuestionTime" name="MassEdit[QuestionTime]" size="4" /> </td> </tr> </table> </fieldset> <fieldset class="ari-settings-group">
<legend><label for="chkMassTextSwitcher"><?php AriWebHelper::displayResValue('Label.TextTemplates'); ?></label> <input type="checkbox" id="chkMassTextSwitcher" class="text_area ari-settings-switcher" checked="checked" /><label for="lbMassScale" class="ari-setting-label" style="display: none;" >&nbsp;</label><?php $processPage->renderControl('lbMassScale', array('class' => 'text_area', 'onchange' => 'YAHOO.util.Dom.setStyle(\'tblMassTextTemplates\', \'display\', this.value != \'0\' ? \'none\' : \'\')')); ?></legend>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="questionContainer" id="tblMassTextTemplates"> <tr> <td class="right noWrap colMin">
<label for="lbMassSucEmail" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.SucEmailTemplate'); ?></label> : </td>
<td class="left"><?php $processPage->renderControl('lbMassSucEmail', array('class' => 'text_area')); ?></td> <td class="right noWrap colMin">
<label for="lbMassFailEmail" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.FailedEmailTemplate'); ?></label> : </td>
<td class="left"><?php $processPage->renderControl('lbMassFailEmail', array('class' => 'text_area')); ?></td> </tr> <tr> <td class="right colMin">
<label for="lbMassSucPrint" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.SucPrintTemplate'); ?></label> : </td>
<td class="left"><?php $processPage->renderControl('lbMassSucPrint', array('class' => 'text_area')); ?></td> <td class="right colMin">
<label for="lbMassFailPrint" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.FailedPrintTemplate'); ?></label> : </td>
<td class="left"><?php $processPage->renderControl('lbMassFailPrint', array('class' => 'text_area')); ?></td> </tr> <tr> <td class="right colMin">
<label for="lbMassSuc" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.SucTemplate'); ?></label> : </td>
<td class="left"><?php $processPage->renderControl('lbMassSuc', array('class' => 'text_area')); ?></td> <td class="right colMin">
<label for="lbMassFail" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.FailedTemplate'); ?></label> : </td>
<td class="left"><?php $processPage->renderControl('lbMassFail', array('class' => 'text_area')); ?></td> </tr> </table> </fieldset> <fieldset class="ari-settings-group">
<legend><label for="chkMassExtraSwitcher"><?php AriWebHelper::displayResValue('Label.AdditionalSettings'); ?></label> <input type="checkbox" id="chkMassExtraSwitcher" class="text_area ari-settings-switcher" checked="checked" /></legend>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="questionContainer"> <tr> <td colspan="4"> <fieldset> <legend><?php AriWebHelper::displayResValue('Label.SendResultTo'); ?></legend>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="questionContainer"> <tr> <td class="right noWrap" style="width: 1%;">
<label for="tbxMassAdminEmail" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.Email'); ?></label> : </td> <td class="left">
<input type="text" id="tbxMassAdminEmail" name="MassEdit[AdminEmail]" size="50" /> </td> <td class="right noWrap" style="width: 1%;">
<label for="lbMassAdminEmail" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.Template'); ?></label> : </td> <td class="left">
<?php $processPage->renderControl('lbMassAdminEmail', array('class' => 'text_area')); ?> </td> </tr> </table> </fieldset> </td> </tr> <tr> <td class="right">
<label for="lbMassTemplate" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.Template'); ?></label> : </td> <td class="left" style="width: 25%;">
<?php $processPage->renderControl('lbMassTemplate', array('class' => 'text_area')); ?> </td> <td class="right" style="width: 25%;">
<label for="lbMassAnsOrderType" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QOT'); ?></label> : </td> <td class="left" style="width: 25%;">
<?php $processPage->renderControl('lbMassAnsOrderType', array('class' => 'text_area')); ?> </td> </tr> <tr> <td class="right">
<label for="lbMassFullStatisticsType" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Quiz.ShowFullStatistics'); ?></label> : </td> <td class="left">
<?php $processPage->renderControl('lbMassFullStatisticsType', array('class' => 'text_area')); ?> </td> <td class="right" style="width: 25%;">
<label for="lbMassAnonStatus" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.AnonStatus'); ?></label> : </td> <td class="left" style="width: 25%;">
<?php $processPage->renderControl('lbMassAnonStatus', array('class' => 'text_area')); ?> </td> </tr> <tr> <td class="right">
<label for="chkMassParsePlugin" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.ParsePluginTag'); ?></label> : </td> <td class="left">
<input type="checkbox" id="chkMassParsePlugin" name="MassEdit[ParsePluginTag]" size="4" class="ari-advanced-checkbox" /> </td> <td class="right">
<label for="chkMassCanSkip" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.Skip'); ?></label> : </td> <td class="left">
<input type="checkbox" id="chkMassCanSkip" name="MassEdit[CanSkip]" size="4" class="ari-advanced-checkbox" /> </td> </tr> <tr> <td class="right">
<label for="chkMassCanStop" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.QuizCanStop'); ?></label> : </td> <td class="left">
<input type="checkbox" id="chkMassCanStop" name="MassEdit[CanStop]" size="4" class="ari-advanced-checkbox" /> </td> <td class="right">
<label for="chkMassRandomQuestion" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.RandomQuestion'); ?></label> : </td> <td class="left">
<input type="checkbox" id="chkMassRandomQuestion" name="MassEdit[RandomQuestion]" size="4" class="ari-advanced-checkbox" /> </td> </tr> <tr> <td class="right">
<label for="chkMassCorrectAnswer" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.ShowCorrectAnswer'); ?></label> : </td> <td class="left">
<input type="checkbox" id="chkMassCorrectAnswer" name="MassEdit[ShowCorrectAnswer]" size="4" class="ari-advanced-checkbox" /> </td> <td class="right">
<label for="chkMassExplanation" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.ShowExplanation'); ?></label> : </td> <td class="left">
<input type="checkbox" id="chkMassExplanation" name="MassEdit[ShowExplanation]" size="4" class="ari-advanced-checkbox" /> </td> </tr> <tr> <td class="right">
<label for="chkMassUseCalc" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.UseCalculator'); ?></label> : </td> <td class="left" colspan="3">
<input type="checkbox" id="chkMassUseCalc" name="MassEdit[UseCalculator]" size="4" class="ari-advanced-checkbox" /> </td> </tr> <tr> <td class="right">
<label for="tbxMassLagTime" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.LagTime'); ?></label> : </td> <td class="left">
<input type="text" id="tbxMassLagTime" name="MassEdit[LagTime]" size="8" /> </td> <td class="right">
<label for="tbxMassAttemptCount" class="ari-dashed-line ari-setting-label"><?php AriWebHelper::displayResValue('Label.AttemptCount'); ?></label> : </td> <td class="left">
<input type="text" id="tbxMassAttemptCount" name="MassEdit[AttemptCount]" size="8" /> </td> </tr> </table> </fieldset> <!--fieldset class="ari-settings-group">
<legend><label for="chkMassAdditionalSwitcher"><?php AriWebHelper::displayResValue('Label.AdditionalProperties'); ?></label> <input type="checkbox" id="chkMassAdditionalSwitcher" class="text_area ari-settings-switcher" checked="checked" /></legend>
</fieldset--> </div> <div class="ft" style="text-align: center;"> <div>
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMassEdit');" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Label.Clear'); ?>" onclick="this.form.reset();YAHOO.ARISoft.page.pageManager.massEditSettings.resetSettings();YAHOO.ARISoft.page.pageManager.settingAdvCheckboxes.renew();" />
<input type="button" class="button" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" onclick="YAHOO.ARISoft.page.panelMassEdit.hide(); return false;" /> </div> <div style="text-align: left;"> <br/>
<b><?php AriWebHelper::displayResValue('Label.Note'); ?> :</b> <?php AriWebHelper::displayResValue('Text.MassEditNote'); ?> </div> </div> </div> <script type="text/javascript"> YAHOO.util.Event.onDOMReady(function() {
var page = YAHOO.ARISoft.page, pageManager = page.pageManager, aDom = YAHOO.ARISoft.DOM, validators = YAHOO.ARISoft.validators; pageManager.settingAdvCheckboxes = new YAHOO.ARISoft.widgets.advancedCheckbox("panelMassEdit", {}); 
aDom.moveTo(aDom.wrapWithElement('form', 'panelMassEdit', {id: 'frmMassEdit', name: 'frmMassEdit'})); page.panelMassEdit = new YAHOO.widget.Panel("panelMassEdit",
{ width:"600px", height:"610px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); page.panelMassEdit.render(); page.panelCopy = new YAHOO.widget.Panel("panelCopy",
{ width:"500px", height:"130px", visible:false, constraintoviewport:true, modal:true, fixedcenter: "contained", zIndex: 200}); page.panelCopy.render(); /* Mass edit settings panel */
pageManager.massEditSettings = new YAHOO.ARISoft.widgets.settingsPanel('panelMassEdit', {}); pageManager.registerActionGroup('quizAction', { onAction: page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $dataTable->id; ?>,
enableValidation: true, errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); pageManager.registerAction('quiz_list$ajax|activate', { group: 'quizAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizActivate'); ?>' }); pageManager.registerAction('quiz_list$ajax|deactivate', {
group: 'quizAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizDeactivate'); ?>' }); pageManager.registerAction('quiz_list$ajax|singleActivate', { group: 'quizAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizActivate'); ?>' }); pageManager.registerAction('quiz_list$ajax|singleDeactivate', { group: 'quizAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizDeactivate'); ?>' }); pageManager.registerAction('quiz_list$ajax|delete', { group: 'quizAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizDelete'); ?>' }); pageManager.registerAction('quiz_list$ajax|filters', { group: 'quizAction' }); pageManager.registerAction('quiz_list$ajax|massEdit', {
onAction: function(action, config) { pageManager.settingAdvCheckboxes.renew(); config.postData = YAHOO.util.Connect.setForm('frmMassEdit'); page.actionHandlers.simpleDatatableAction.call(this, action, config); },
group: 'quizAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.MassEdit'); ?>' }); pageManager.registerAction('quiz_list$ajax|copy', { group: 'quizAction',
completeMessage: '<?php AriWebHelper::displayResValue('Complete.QuizCopy'); ?>' }); pageManager.registerAction('applyMassEdit', { onAction: function() { if (!validators.alertSummaryValidators.validate(['massEdit'])) return ; 
page.panelMassEdit.hide(); pageManager.triggerAction('quiz_list$ajax|massEdit'); } }); pageManager.registerAction('applyCopy', { onAction: function() { if (!validators.alertSummaryValidators.validate(['copy'])) return ; 
page.panelCopy.hide(); pageManager.triggerAction('quiz_list$ajax|copy'); } }); pageManager.registerAction('mass_edit', { onAction: function() { page.panelMassEdit.show(); } }); pageManager.registerAction('copy', {
onAction: function() { page.panelCopy.show(); } }); }); YAHOO.ARISoft.validators.validatorManager.addValidator( new YAHOO.ARISoft.validators.customValidator('cvMassEditActive', function(val) {
var validators = YAHOO.ARISoft.validators.validatorManager.validators; for (var i = 0; i < validators.length; i++) { var validator = validators[i]; if (!validator.inValidationGroup(['massEditActive'])) continue; 
validator.enabled = !YAHOO.ARISoft.page.pageManager.massEditSettings.isDisabledSettingEl(validator.ctrlId); } var failedValidators = YAHOO.ARISoft.validators.validatorManager.getFailedValidator(['massEditActive']);
if (failedValidators.length == 0) return true; val.errorMessage = failedValidators[0].errorMessage; return false; }, {validationGroups: ['massEdit'], errorMessage : ''})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvMassEditSettings', function(val) { return YAHOO.ARISoft.page.pageManager.massEditSettings.getActiveElementsCount() > 0; },
{validationGroups: ['massEdit'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.MassEditSettingsRequired'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.customValidator('cvMassEditCount', function(val) { return YAHOO.ARISoft.widgets.dataTable.utils.isCheckedCheckboxField(<?php echo $dataTable->id; ?>, 'yui-dt-col-QuizId'); },
{validationGroups: ['massEdit', 'copy'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.SelectAtLeastOneItem'); ?>'})); YAHOO.ARISoft.validators.validatorManager.addValidator(
new YAHOO.ARISoft.validators.requiredValidator('tbxCopyQuizName', { validationGroups: ['copy'], errorMessage : '<?php AriWebHelper::displayResValue('Validator.NameRequired'); ?>' })); </script>