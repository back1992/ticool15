<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $option = $processPage->getVar('option'); $mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$dataTable = $processPage->getVar('dataTable'); $jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; $quizId = $processPage->getVar('quizId'); $startDate = $processPage->getVar('startDate');
$endDate = $processPage->getVar('endDate'); ?> <script type="text/javascript" src="<?php echo $jsPath; ?>date.js"></script> 
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/datatable/assets/skins/joomla/datatable-skin.css" /> <link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath;?>build/container/assets/container.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/calendar/assets/skins/sam/calendar.css" /> <script type="text/javascript" src="<?php echo $jsPath; ?>date.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/container/container-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/datatable/datatable-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/calendar/calendar-min.js"></script> <script type="text/javascript" src="<?php echo $jsPath; ?>widgets/ari.calendar.js"></script> <div class="textRight">
<fieldset style="width: 290px;" class="rightPos"> <legend><?php AriWebHelper::displayResValue('Label.Filter'); ?></legend> <div> <div>
<div class="leftPos"><?php $processPage->renderControl('lbFilterQuiz', array('class' => 'text_area')); ?></div> <div class="textRight"><?php $processPage->renderControl('lbFilterUser', array('class' => 'text_area')); ?></div> </div>
<br class="clearBoth" /> <div> <div class="leftPos"> <div> <?php AriWebHelper::displayResValue('Label.StartDate'); ?> :
<input type="text" id="tbxStartDate" name="tbxStartDate" class="text_area" size="10" readonly="readonly" /> <input type="hidden" id="hidStartDate" name="hidStartDate" /> </div>
<div id="calStartDateContainer" style="visibility: hidden;" class="ari-calendar"> <div class="hd"><?php AriWebHelper::displayResValue('Label.StartDate'); ?> :</div> </div> </div> <div> <div class="textRight">
<?php AriWebHelper::displayResValue('Label.EndDate'); ?> : <input type="text" id="tbxEndDate" name="tbxEndDate" class="text_area" size="10" readonly="readonly" /> <input type="hidden" id="hidEndDate" name="hidEndDate" />
</div> <div id="calEndDateContainer" style="visibility: hidden;" class="ari-calendar"> <div class="hd"><?php AriWebHelper::displayResValue('Label.EndDate'); ?> :</div> </div> </div> </div> <br class="clearBoth" />
<div class="textRight"> <input type="button" class="button" onclick="submitbutton('results$ajax|filters'); return false;" value="<?php echo AriWebHelper::displayResValue('Label.Apply'); ?>" /> </div> </div> </fieldset> <div></div>
<br class="clearBoth" /> </div> <br/> <?php $dataTable->render(); ?> <input type="hidden" name="quizId" value="<?php echo $quizId;?>" /> <script type="text/javascript"> YAHOO.util.Event.onDOMReady(function() {
var page = YAHOO.ARISoft.page, pageManager = page.pageManager; YAHOO.util.Dom.addClass(document.body, "yui-skin-sam"); page.calStartDate = new YAHOO.ARISoft.widgets.Calendar( "calStartDate", "calStartDateContainer", { close:false,
iframe:false, dateElement: "tbxStartDate", hiddenDateElement: "hidStartDate", pagedate: "<?php echo $startDate ? date('m/Y', $startDate) : ''; ?>", selected: "<?php echo $startDate ? date('m/d/Y', $startDate) : ''; ?>" }, {
context: ["tbxStartDate", "tr", "bl"] }); page.calEndDate = new YAHOO.ARISoft.widgets.Calendar( "calEndDate", "calEndDateContainer", { close:false, iframe:false, dateElement: "tbxEndDate",
hiddenDateElement: "hidEndDate", pagedate: "<?php echo $endDate ? date('m/Y', $endDate) : ''; ?>", selected: "<?php echo $endDate ? date('m/d/Y', $endDate) : ''; ?>" }, { context: ["tbxStartDate", "tr", "bl"] }); 
pageManager.registerActionGroup('resultAction', { onAction: page.actionHandlers.simpleDatatableAction, dataTable: <?php echo $dataTable->id; ?>, enableValidation: true,
errorMessage: '<?php AriWebHelper::displayResValue('Label.ActionFailure'); ?>', completeMessage: '',
loadingMessage: '<img src="<?php echo $mosConfig_live_site; ?>/administrator/components/<?php echo $option; ?>/images/loading.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Label.Loading'); ?>'
}); pageManager.registerAction('results$ajax|delete', { group: 'resultAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.ResultDelete'); ?>' }); pageManager.registerAction('results$ajax|deleteAll', {
group: 'resultAction', completeMessage: '<?php AriWebHelper::displayResValue('Complete.ResultDelete'); ?>' }); pageManager.registerAction('results$ajax|filters', { group: 'resultAction' }); pageManager.registerAction('deleteAll', {
onAction: function() { if (confirm('<?php AriWebHelper::displayResValue('Warning.RemoveAllQuizResults'); ?>')) { pageManager.triggerAction('results$ajax|deleteAll'); } } }); 
pageManager.subscribe('afterAction', function(o) { if (o.action == 'results$tocsv' || o.action == 'results$toexcel' || o.action == 'results$tohtml' || o.action == 'results$toword') { document.getElementById('task').value = 'results'; }
}); }); </script>