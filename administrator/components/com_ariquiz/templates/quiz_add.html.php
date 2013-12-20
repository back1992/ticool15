<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); $option = $processPage->getVar('option'); $mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$jsPath = $mosConfig_live_site . '/components/' . $option . '/js/'; $jsYuiPath = $jsPath . 'yui/'; $quizId = $processPage->getVar('quizId'); $quiz = $processPage->getVar('quiz'); $props = $processPage->getVar('props');
$lbScale = $processPage->getControl('lbScale'); $scaleId = $lbScale->getSelectedValue(); ?> <script type="text/javascript" src="<?php echo $jsPath; ?>mootools.js"></script> 
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/yahoo-dom-event/yahoo-dom-event.js"></script> <?php AriJoomlaBridge::loadOverlib(); ?> <table class="adminform" style="width: 100%;"> <tbody> <tr>
<th colspan="2"><?php AriWebHelper::displayResValue('Label.MainSettings'); ?></th> </tr> </tbody> <tbody id="tbQuizSettings"> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.Name'); ?> :</td>
<td align="left"><?php $processPage->renderControl('tbxQuizName', array('class' => 'text_area', 'size' => '70')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.Category'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbCategories', array('class' => 'text_area')); ?></td> </tr> <tr valign="top"> <td align="left"><?php AriWebHelper::displayResValue('Label.Access'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbAccess', array('class' => 'text_area', 'size' => '5')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.Active'); ?> :</td>
<td align="left"><?php $processPage->renderControl('chkStatus', array('value' => '1')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.TotalTime'); ?> :</td>
<td align="left"><?php $processPage->renderControl('tbxTotalTime', array('class' => 'text_area')); ?></td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.PassedScore'); ?> <?php AriWebHelper::displayResValue('Label.Percent'); ?> :</td>
<td align="left"><?php $processPage->renderControl('tbxPassedScore', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.QuestionCount'); ?> :</td>
<td align="left"><?php $processPage->renderControl('tbxQuestionCount', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.QuestionTime'); ?> :</td>
<td align="left"><?php $processPage->renderControl('tbxQuestionTime', array('class' => 'text_area')); ?></td> </tr> <tr valign="top"> <td align="left"><?php AriWebHelper::displayResValue('Label.Description'); ?> :</td>
<td align="left"> <?php $processPage->renderControl('edDescription', array('width' => '100%;', 'height' => 250, 'cols' => 60, 'rows' => 20)); ?> </td> </tr> </tbody> <tbody> <tr>
<th colspan="2"><?php AriWebHelper::displayResValue('Label.TextTemplates'); ?>&nbsp;&nbsp;<?php $processPage->renderControl('lbScale', array('class' => 'text_area', 'onchange' => 'YAHOO.util.Dom.setStyle(\'tbQuizTemplate\', \'display\', this.value != \'0\' ? \'none\' : \'\')')); ?></th>
</tr> </tbody> <tbody id="tbQuizTemplate"<?php echo $scaleId ? ' style="display: none;"' : ''; ?>> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.SucEmailTemplate'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbSucEmail', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.FailedEmailTemplate'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbFailEmail', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.SucPrintTemplate'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbSucPrint', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.FailedPrintTemplate'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbFailPrint', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.SucTemplate'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbSuc', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.FailedTemplate'); ?> :</td>
<td align="left"><?php $processPage->renderControl('lbFail', array('class' => 'text_area')); ?></td> </tr> </tbody> <tbody> <tr> <th colspan="2"><?php AriWebHelper::displayResValue('Label.AdditionalSettings'); ?></th> </tr>
</tbody> <tbody id="tbExtraQuizSettings"> <tr valign="top"> <td align="left"><?php AriWebHelper::displayResValue('Label.SendResultTo'); ?> :</td> <td> <table width="100%" cellpadding="1" cellspacing="1"> <tr>
<td style="width: 1%; white-space: nowrap;"> <?php AriWebHelper::displayResValue('Label.Email'); ?> : </td> <td> <?php $processPage->renderControl('tbxAdminEmail', array('class' => 'text_area', 'size' => '100')); ?>
</td> </tr> <tr> <td style="width: 1%; white-space: nowrap;"> <?php AriWebHelper::displayResValue('Label.Template'); ?> : </td>
<td><?php $processPage->renderControl('lbAdminEmail', array('class' => 'text_area')); ?></td> </tr> </table> </td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.Template'); ?> :</td>
<td><?php $processPage->renderControl('lbCss', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.QOT'); ?> :</td>
<td><?php $processPage->renderControl('lbQueOrderType', array('class' => 'text_area')); ?></td> </tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Label.AnonStatus'); ?> :</td>
<td><?php $processPage->renderControl('lbAnonymous', array('class' => 'text_area')); ?> <?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.Anomymous'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td>
</tr> <tr> <td align="left"><?php AriWebHelper::displayResValue('Quiz.ShowFullStatistics'); ?> :</td> <td><?php $processPage->renderControl('lbFullStatisticsType', array('class' => 'text_area')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.FullStatistics'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.ParsePluginTag'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkParsePluginTag', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.ParsePluginTag'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.Skip'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkCanSkip', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.QuizCanSkip'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.QuizCanStop'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkCanStop', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.QuizCanStop'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.RandomQuestion'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkRandom', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.QuizRandomQuestion'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.UseCalculator'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkUseCalc', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.QuizCalculator'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.ShowCorrectAnswer'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkShowCorrectAnswer', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.ShowCorrectAnswer'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.ShowExplanation'); ?> :</td> <td align="left"><?php $processPage->renderControl('chkShowExplanation', array('value' => '1')); ?>
<?php echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.ShowExplanation'), AriWebHelper::translateResValue('Label.Tooltip')); ?> </td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.LagTime'); ?> :</td> <td align="left"><?php $processPage->renderControl('tbxLagTime', array('class' => 'text_area')); ?></td> </tr> <tr>
<td align="left"><?php AriWebHelper::displayResValue('Label.AttemptCount'); ?> :</td> <td align="left"><?php $processPage->renderControl('tbxAttemptCount', array('class' => 'text_area')); ?></td> </tr> </tbody> <?php if (!empty($props))
{ ?> <tbody> <tr> <th colspan="2"><?php AriWebHelper::displayResValue('Label.AdditionalProperties'); ?></th> </tr> </tbody> <tbody> <?php foreach ($props as $propItem) { ?> <tr valign="top">
<td align="left"><?php AriWebHelper::displayResValue($propItem->ResourceKey); ?> :</td> <td align="left"><?php $processPage->renderControl('QuizProp[' . $propItem->PropertyName . ']', array('class' => 'text_area')); ?></td> </tr> <?php }
?> </tbody> <?php } ?> </table> <input type="hidden" name="quizId" value="<?php echo $quizId; ?>" /> <script type="text/javascript" language="javascript"> YAHOO.ARISoft.page.quizNameValidate = function(val) { var isValid = true;
new Ajax('index2.php?option=<?php echo $option; ?>&task=<?php echo $processPage->executionTask; ?>$ajax|checkQuizName&quizId=<?php echo $quizId; ?>&name=' + encodeURIComponent(val.getValue()), { async : false,
onSuccess: function(response) { isValid = Json.evaluate(response); if (!isValid) isValid = confirm('<?php AriWebHelper::displayResValue('Validator.ConfirmNameNotUnique'); ?>'); } }).request(); return isValid; }; 
submitbutton = function(pressbutton) { if (pressbutton == 'quiz_add$save' || pressbutton == 'quiz_add$apply') { if (!aris.validators.alertSummaryValidators.validate()) { return; } } submitform(pressbutton); };
</script>