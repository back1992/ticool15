<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');
$option = $processPage->getVar('option');
$mosConfig_live_site = $processPage->getVar('mosConfig_live_site');
$jsPath = $mosConfig_live_site . '/components/' . $option . '/js/';
$jsYuiPath = $jsPath . 'yui/';
$mosConfig_absolute_path = $processPage->getVar('mosConfig_absolute_path');
$quiz = $processPage->getVar('quiz');
$quizId = $processPage->getVar('quizId');
$question = $processPage->getVar('question');
$questionId = $processPage->getVar('questionId');
$questionData = $processPage->getVar('questionData');
$questionOverrideData = $processPage->getVar('questionOverrideData');
$className = $processPage->getVar('className');
$specificQuestion = $processPage->getVar('specificQuestion');
$reload = $processPage->getVar('reload');
$bankQuestionId = $processPage->getVar('bankQuestionId');
$mode = $processPage->getVar('mode');
$clearTask = $processPage->getVar('clearTask');
$uiMode = $processPage->getVar('uiMode');
$modeList = AriConstantsManager::getVar('Mode', AriQuestionUiConstants::getClassName());
$uiModeList = AriConstantsManager::getVar('UiMode', AriQuestionUiConstants::getClassName());
$edQuestion = & $processPage->getControl('edQuestion');
$baseOnBank = !!($question->BankQuestionId);
$imgAdminPath = $mosConfig_live_site . '/administrator/components/' . $option . '/images/';
$isScoreSpecific = $specificQuestion->isScoreSpecific();
?> 
<link rel="stylesheet" type="text/css" href="<?php echo $jsYuiPath; ?>build/container/assets/container.css" /> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/utilities/utilities.js"></script>
<script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/container/container-min.js"></script> <script type="text/javascript" src="<?php echo $jsYuiPath; ?>build/json/json-min.js"></script> <?php AriJoomlaBridge::loadOverlib(); ?>
<table class="adminform" style="width: 100%;"> <tbody> <tr> <th colspan="2"><?php AriWebHelper::displayResValue('Label.MainSettings'); ?></th> </tr> </tbody> <tbody id="tbQuestionSettings"> <?php if ($mode != $modeList['Bank']) {
    ?> <tr> <td align="left" nowrap style="width: 1%;"><?php AriWebHelper::displayResValue('Label.Quiz'); ?> :</td> <td align="left"><?php AriWebHelper::displayDbValue($quiz->QuizName); ?></td> </tr> <tr>
                <td align="left" nowrap><?php AriWebHelper::displayResValue('Label.Category'); ?> :</td> <td align="left"><?php $processPage->renderControl('lbQuestionCategories', array('class' => 'text_area')); ?></td> </tr> <tr valign="top">
                <td nowrap align="left"><?php AriWebHelper::displayResValue('Label.QuestionReference'); ?> :</td> <td align="left"> <span id="lblBankQuestion"> <?php echo $baseOnBank ? AriWebHelper::translateDbValue($question->QuestionVersion->Question, false) : AriWebHelper::translateResValue('Label.NotSelectedItem');
    ?> </span>
                    [<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.bankHelper.showPanel();
            return false;"><?php AriWebHelper::displayResValue('Label.Change'); ?></a>] <?php if ($baseOnBank) { ?>
                        [<a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.bankHelper.resetBankQuestion();
                return false;"><?php AriWebHelper::displayResValue('Label.Clear'); ?></a>] <?php } ?> </td> </tr> <?php } else { ?> <tr>
                <td align="left" nowrap><?php AriWebHelper::displayResValue('Label.Category'); ?> :</td> <td align="left"><?php $processPage->renderControl('lbBankCategories', array('class' => 'text_area')); ?></td> </tr> <?php } ?> <?php if ($uiMode != $uiModeList['Read']) { ?> <tr> <td nowrap align="left"><?php AriWebHelper::displayResValue('Title.QuestionTemplate'); ?> :</td>
                <td align="left"><?php $processPage->renderControl('lbTemplateList', array('class' => 'text_area', 'onchange' => 'submitbutton(\'' . $clearTask . '\')')); ?></td> </tr> <?php } ?> <tr>
            <td nowrap align="left"><?php AriWebHelper::displayResValue('Label.QuestionType'); ?> :</td> <td align="left">
                    <?php $processPage->renderControl('lbQuestionType', array('class' => 'text_area', 'onchange' => 'submitbutton(\'' . $clearTask . '$apply_qtype\')')); ?> </td> </tr> <?php if (!$isScoreSpecific): ?> <tr valign="top">
                <td nowrap align="left"><?php AriWebHelper::displayResValue('Label.Score'); ?> :</td> <td align="left"><?php $processPage->renderControl('tbxScore', array('class' => 'text_area', 'size' => '10')); ?> <?php if ($uiMode == $uiModeList['Read']) { ?> <br/>
                    <?php $processPage->renderControl('chkOverrideScore', array('value' => '1', 'onclick' => 'YAHOO.ARISoft.page.changeScoreOverride(this.checked);', 'style' => 'margin: 0px; padding: 0px;')); ?>&nbsp;&nbsp;<label for="chkOverrideScore"><?php AriWebHelper::displayResValue('Label.Override'); ?></label>
                <?php } ?> </td> </tr> <?php endif; ?> <tr valign="top"> <td nowrap align="left"><?php AriWebHelper::displayResValue('Label.Question'); ?> :</td> <td align="left"> <?php if ($uiMode == $uiModeList['Read'])
                AriWebHelper::displayDbValue($question->QuestionVersion->Question, false);
            else
                $processPage->renderControl('edQuestion', array('width' => '100%;', 'height' => 250, 'cols' => 60, 'rows' => 20));
            ?> </td> </tr> <tr valign="top">
            <td nowrap align="left"><?php AriWebHelper::displayResValue('Label.QuestionNote'); ?> : <?php if ($uiMode != $uiModeList['Read'])
            echo AriJoomlaBridge::toolTip(AriWebHelper::translateResValue('Tooltip.QuestionNote'), AriWebHelper::translateResValue('Label.Tooltip'));
            ?> </td> <td align="left" nowrap> <?php if ($uiMode == $uiModeList['Read'])
            AriWebHelper::displayDbValue($question->QuestionVersion->Note);
        else
            $processPage->renderControl('edQuestionNote', array('width' => '100%;', 'height' => 250, 'cols' => 60, 'rows' => 20));
            ?> </td> </tr> </tbody> <tbody> <tr>
            <th colspan="2"><?php AriWebHelper::displayResValue('Label.AdditionalSettings'); ?></th> </tr> </tbody> <tbody id="tbQueDetailsSettings"> <tr> <td colspan="2" align="left"> <?php $path = $processPage->getQuestionTemplatePath($className);
        if (!empty($path)) require_once($path); ?> </td> </tr> </tbody> </table> <?php if ($mode != $modeList['Bank']) { ?> <div id="panelBankQuestion">
        <div class="hd"><?php AriWebHelper::displayResValue('Label.QuestionReference'); ?></div> <div class="bd"> <table border="0" cellspacing="1" cellpadding="1" style="width: 100%;"> <tr>
                    <td style="white-space: nowrap; width: 1%;"><?php AriWebHelper::displayResValue('Label.Category'); ?> :</td> <td>
            <?php $processPage->renderControl('lbBankCategories', array('class' => 'text_area', 'onchange' => 'YAHOO.ARISoft.page.bankHelper.changeBankCategory(this.value);')); ?>&nbsp; </td> </tr> <tr>
                    <td style="white-space: nowrap; width: 1%;"><?php AriWebHelper::displayResValue('Label.Question'); ?> :</td> <td> <?php $processPage->renderControl('lbBank', array('class' => 'text_area ariHideLoading')); ?>
                        <div class="ariLoadingMessage"> <img src="<?php echo $imgAdminPath; ?>loading.gif" width="16" height="16" border="0" align="absmiddle" /> &nbsp;&nbsp;&nbsp;<?php AriWebHelper::displayResValue('Ajax.Loading'); ?> </div>
                    </td> </tr> <tr> <td colspan="2" align="right"> <br/>
                        <input type="button" class="button" onclick="submitbutton('<?php echo $clearTask . '$applyRefQuestion'; ?>');
            return false;" value="<?php AriWebHelper::displayResValue('Toolbar.Apply'); ?>" /> <?php if ($baseOnBank) { ?>
                            <input type="button" class="button" onclick="YAHOO.ARISoft.page.bankHelper.resetBankQuestion();
                return false;" value="<?php AriWebHelper::displayResValue('Label.Clear'); ?>" /> <?php } ?>
                        <input type="button" class="button" onclick="YAHOO.ARISoft.page.bankHelper.cancelBankQuestion();
            YAHOO.ARISoft.page.bankHelper.hidePanel();
            return false;" value="<?php AriWebHelper::displayResValue('Toolbar.Cancel'); ?>" /> </td> </tr>
            </table> </div> <div class="ft"></div> </div> <script type="text/javascript" language="javascript"> YAHOO.namespace("ari.container");
        YAHOO.ari.container.panelBankQuestion = new YAHOO.widget.Panel("panelBankQuestion",
                {width: "360px", visible: false, constraintoviewport: true, modal: true, fixedcenter: true});
        YAHOO.util.Event.onDOMReady(function() {
            YAHOO.ari.container.panelBankQuestion.render();
        });
        YAHOO.ARISoft.page.bankHelper = {
            QUESTION_LENGTH: 50, _loadedAttrName: '_bankLoaded', _initBankQuestionId: '<?php echo $bankQuestionId; ?>', _initCategoryId: '<?php echo $question->QuestionVersion->_BankCategoryId; ?>', showPanel: function() {
                var panelBankQuestion = YAHOO.util.Dom.get('panelBankQuestion');
                var isLoaded = panelBankQuestion.getAttribute(this._loadedAttrName);
                if (!isLoaded) {
                    panelBankQuestion.setAttribute(this._loadedAttrName, 'true');
                    var ddlBankCategory = YAHOO.util.Dom.get('lbBankCategories');
                    this.changeBankCategory(ddlBankCategory.value, '<?php echo $bankQuestionId; ?>');
                }
                ;
                YAHOO.ari.container.panelBankQuestion.show();
            }, hidePanel: function() {
                YAHOO.ari.container.panelBankQuestion.hide();
            }, cancelBankQuestion: function() {
                if (!this._initBankQuestionId)
                    return;
                var ddlBankCategory = YAHOO.util.Dom.get('lbBankCategories');
                ddlBankCategory.value = this._initCategoryId;
                this.changeBankCategory(this._initCategoryId, this._initBankQuestionId);
            }, resetBankQuestion: function() {
                submitbutton('<?php echo $clearTask ?>$clearRefQuestion');
            },
            changeBankCategory: function(categoryId, selectedValue) {
                this.initAjaxLoadingHandlers();
                selectedValue = typeof(selectedValue) != 'undefined' ? selectedValue : null;
                YAHOO.util.Connect.asyncRequest('GET',
                        'index2.php?option=' + YAHOO.ARISoft.page.option + '&task=<?php echo $processPage->executionTask; ?>$ajax|getBankByCategory&categoryId=' + categoryId, {success: function(response, args) {
                        var responseText = response.responseText || null;
                        var options = [];
                        if (responseText) {
                            try {
                                options = YAHOO.lang.JSON.parse(responseText);
                            } catch (e) {
                                options = [];
                            }
                        }
                        ;
                        var ddlBank = YAHOO.ARISoft.DOM.$('lbBank');
                        ddlBank.options.length = 0;
                        ddlBank.options[0] = new Option(this.truncateQuestion('<?php AriWebHelper::displayResValue('Label.NotSelectedItem'); ?>'), 0);
                        if (options) {
                            for (var i = 0, cnt = options.length; i < cnt; i++) {
                                var option = options[i];
                                var opt = new Option(this.truncateQuestion(option.Question), option.QuestionId);
                                ddlBank.options[ddlBank.options.length] = opt;
                            }
                        }
                        ;
                        if (typeof(response.argument) != 'undefined' && response.argument.length > 0 && response.argument[0])
                            ddlBank.value = response.argument[0];
                        else
                            ddlBank.value = 0;
                        this.hideLoading();
                    }, scope: this,
                    argument: [selectedValue]});
            }, truncateQuestion: function(question) {
                var question = YAHOO.ARISoft.util.stripTags(question);
                if (question.length > this.QUESTION_LENGTH)
                    question = question.substr(0, this.QUESTION_LENGTH) + '...';
                return question;
            }, initAjaxLoadingHandlers: function() {
                var YCM = YAHOO.util.Connect;
                YCM.startEvent.subscribe(this.showLoading);
                YCM.completeEvent.subscribe(this.hideLoading);
            }, showLoading: function() {
                YAHOO.util.Dom.addClass('panelBankQuestion', 'ariLoadingProcess');
            }, hideLoading: function() {
                YAHOO.util.Connect.startEvent.unsubscribe(YAHOO.ARISoft.page.bankHelper.showLoading);
                YAHOO.util.Connect.completeEvent.unsubscribe(YAHOO.ARISoft.page.bankHelper.hideLoading);
                YAHOO.util.Dom.removeClass('panelBankQuestion', 'ariLoadingProcess');
                        }};</script> <?php } ?> <script type="text/javascript"> YAHOO.ARISoft.page.changeScoreOverride = function(isOverride) {
                            var tbxScore = YAHOO.ARISoft.DOM.$('tbxScore');
                            tbxScore.disabled = !isOverride;
                            if (!isOverride)
                                tbxScore.value = tbxScore.getAttribute('_initValue');
                        };
<?php if ($uiMode != $uiModeList['Read']) { ?>
                            YAHOO.ARISoft.page.questionValidate = function(val) {
                                var value = <?php echo $edQuestion->getContent(); ?>;
                                var isValid = (value && value.replace(/^\s+|\s+$/g, '').replace(/<\/?[^>]+>/gi, '').length > 0);
                                return isValid;
                            };
<?php }
?> submitbutton = function(pressbutton) {
                            if (pressbutton == '<?php echo $clearTask; ?>$save' || pressbutton == '<?php echo $clearTask; ?>$apply') {
                                if (!aris.validators.alertSummaryValidators.validate()) {
                                    return;
                                }
                            }
                            submitform(pressbutton);
            }</script> <?php if ($mode != $modeList['Bank']) { ?> <input type="hidden" name="bankQuestionId" value="<?php echo $bankQuestionId; ?>" /> <?php } ?>
<input type="hidden" name="quizId" value="<?php echo $quizId; ?>" /> <input type="hidden" name="questionId" value="<?php echo $questionId; ?>" />