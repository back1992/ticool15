<?php defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.'); AriKernel::import('Web.Controls.ListBox'); $dataItem = $specificQuestion->getDataFromXml($questionData, false);
$path = $mosConfig_absolute_path . '/components/' . $option . '/images/hotspot'; $cacheImagePath = $processPage->getVar('cacheImagePath'); $imageList = $processPage->getVar('imageList');
$files = $processPage->_questionController->call('getLatestQuestionFiles', $question->BankQuestionId ? $question->BankQuestionId : $question->QuestionId);
$imgId = !empty($files['hotspot_image']) ? @intval(AriUtils::getParam($files['hotspot_image'], 'FileId', 0), 10) : 0; $ddlHotSpotImg =& new AriListBoxWebControl('ddlHotSpotImg', array('name' => 'zQuizFiles[hotspot_image]'));
$ddlHotSpotImg->dataBind($imageList, 'FileName', 'FileId'); $ddlHotSpotImg->setSelectedValue($imgId); ?> <?php if ($uiMode == $uiModeList['Read']) { $x = $dataItem[ARI_QUIZ_HOTSPOT_X1]; $y = $dataItem[ARI_QUIZ_HOTSPOT_Y1];
$width = $dataItem[ARI_QUIZ_HOTSPOT_X2] - $dataItem[ARI_QUIZ_HOTSPOT_X1]; $height = $dataItem[ARI_QUIZ_HOTSPOT_Y2] - $dataItem[ARI_QUIZ_HOTSPOT_Y1]; $hotSpotImg = ''; $imgWidth = 0; $imgHeight = 0; if (!empty($imageList)) {
$cnt = count($imageList); for ($i = 1; $i < $cnt; $i++) { $imageInfo = $imageList[$i]; if ($imageInfo->FileId == $imgId) { $hotSpotImg = $cacheImagePath . $imageInfo->RealFileName; $imgWidth = $imageInfo->Width; $imgHeight = $imageInfo->Height;
break; } } } ?> <div style="position: relative; text-align: left;" id="divAriHotSpotWrap">
<div id="divAriHotSpotCorrect" style="left: <?php echo $x; ?>px; top: <?php echo $y; ?>px; width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; font-size: 0; position: absolute; z-index: 2; background: white; opacity: 0.5; filter:alpha(opacity=50);">&nbsp;</div>
<img id="imgAriHotSpot" style="position: relative;" src="<?php echo $hotSpotImg;?>" width="<?php echo $imgWidth; ?>" height="<?php echo $imgHeight; ?>" border="0" /> </div> <?php } else { ?> 
<script src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option; ?>/js/mootools.js" type="text/javascript"></script>
<script src="<?php echo $mosConfig_live_site;?>/components/<?php echo $option; ?>/js/MooCrop.js" type="text/javascript"></script> <script type="text/javascript" language="javascript"> var ariHotSpotManager = { canReset : false,
cropperHotSpot : null, cropperOnloadCoords : <?php echo !empty($dataItem) ? sprintf('{x1: %d, y1: %d, x2: %d, y2: %d}', $dataItem[ARI_QUIZ_HOTSPOT_X1], $dataItem[ARI_QUIZ_HOTSPOT_Y1], $dataItem[ARI_QUIZ_HOTSPOT_X2],
$dataItem[ARI_QUIZ_HOTSPOT_Y2]) : 'null'; ?>, init : function() { window.addEvent('load',function() { ariHotSpotManager.changeHotSpotImg(<?php echo $imgId; ?>, ariHotSpotManager.cropperOnloadCoords); }); }, 
onEndHotSpotCrop : function(coords, dimensions) { aris.DOM.$('hidHotSpotX1').value = coords.x1; aris.DOM.$('hidHotSpotY1').value = coords.y1; aris.DOM.$('hidHotSpotX2').value = coords.x2;
aris.DOM.$('hidHotSpotY2').value = coords.y2; }, clearHotSpotCoords : function() { this.onEndHotSpotCrop({x1: -1, y1: -1, x2: -1, y2: -1}); }, changeHotSpotImg : function(fileId, onloadCoords) {
this.clearHotSpotCoords(); var img = aris.DOM.$('imgAriHotSpot'); var tbHotSpotCont = aris.DOM.$('tbHotSpotCont'); var cropperHotSpot = this.cropperHotSpot; if (fileId > 0 && this.hotSpotImageInfo[fileId]) {
if (this.canReset && cropperHotSpot) cropperHotSpot.removeOverlay(); var cropperOnloadCoords = this.cropperOnloadCoords; var imgInfo = this.hotSpotImageInfo[fileId]; img.src = '<?php echo $cacheImagePath; ?>' + imgInfo.name;
img.width = imgInfo.width; img.height = imgInfo.height; YAHOO.util.Dom.setStyle(tbHotSpotCont, 'display', ''); var initCoords = { x : cropperOnloadCoords != null ? cropperOnloadCoords.x1 : 0,
y : cropperOnloadCoords != null ? cropperOnloadCoords.y1 : 0, width : cropperOnloadCoords != null ? cropperOnloadCoords.x2 - cropperOnloadCoords.x1 : imgInfo.width,
height :  cropperOnloadCoords != null ? cropperOnloadCoords.y2 - cropperOnloadCoords.y1 : imgInfo.height }; if (!cropperHotSpot) { this.cropperHotSpot = new MooCrop('imgAriHotSpot',{
min : { 'width' : 0, 'height' : 0 }, showMask: true, width: initCoords.width, height: initCoords.height, top: initCoords.y, left: initCoords.x}); 
this.cropperHotSpot.addEvent('onComplete' , function(src, cropArea, bounds){ ariHotSpotManager.onEndHotSpotCrop({x1: cropArea.left, y1: cropArea.top, x2: cropArea.right - bounds.width, y2: cropArea.bottom - bounds.height}); }); 
this.onEndHotSpotCrop({x1: initCoords.x, y1: initCoords.y, x2: initCoords.x + initCoords.width, y2: initCoords.y + initCoords.height }); } else { this.cropperHotSpot = new MooCrop('imgAriHotSpot',{
min : { 'width' : 0, 'height' : 0 }, showMask: true, width: initCoords.width, height: initCoords.height, top: initCoords.y, left: initCoords.x}); 
this.cropperHotSpot.addEvent('onComplete' , function(src, cropArea, bounds){ ariHotSpotManager.onEndHotSpotCrop({x1: cropArea.left, y1: cropArea.top, x2: cropArea.right - bounds.width, y2: cropArea.bottom - bounds.height}); });
}; this.cropperOnloadCoords = null; this.canReset = true; } else { this.cropperOnloadCoords = null; if (this.canReset && cropperHotSpot) { cropperHotSpot.removeOverlay(); } img.width = 1;
img.height = 1; YAHOO.util.Dom.setStyle(tbHotSpotCont, 'display', 'none'); this.canReset = false; } }, hotSpotImageInfo : [] }; ariHotSpotManager.init(); <?php if (!empty($imageList)) { $cnt = count($imageList);
for ($i = 1; $i < $cnt; $i++) { $imageInfo = $imageList[$i];
echo 'ariHotSpotManager.hotSpotImageInfo[' . $imageInfo->FileId . '] = {name: \'' . str_replace('\'', '\\\'', $imageInfo->RealFileName). '\',width: ' . $imageInfo->Width . ', height: ' . $imageInfo->Height . '};'; } } ?> 
aris.validators.validatorManager.addValidator( new aris.validators.customValidator('ddlHotSpotImg', function(val) { var isValid = true; var value = val.getValue(); if (value == 0) {
val.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.NotSelectImage'); ?>'); isValid = false; } else { var x1 = parseInt(aris.DOM.$('hidHotSpotX1').value, 10);
var y1 = parseInt(aris.DOM.$('hidHotSpotY1').value, 10); var x2 = parseInt(aris.DOM.$('hidHotSpotX2').value, 10); var y2 = parseInt(aris.DOM.$('hidHotSpotY2').value, 10); if (x2 - x1 < 1 || y2 - y1 < 1) {
val.errorMessage = aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.NotSelectArea'); ?>'); isValid = false; } }; return isValid; },
{emptyValidate : true, errorMessage : '<?php AriWebHelper::displayResValue('Validator.QuestionRequired'); ?>'})); </script> <table id="tblQueContainer" style="width: 100%;" cellpadding="0" cellspacing="0"> <tbody> <tr>
<td><?php AriWebHelper::displayResValue('Label.Image'); ?>: </td> <td> <?php $processPage->renderControl('ddlHotSpotImg', array('class' => 'text_area', 'onchange' => 'ariHotSpotManager.changeHotSpotImg(this.value);')); ?>
<input type="file" id="fileHotSpotImage" name="fileHotSpotImage" class="text_area" size="70" />
<input type="button" value="Add Image" class="button" onclick="var fileName = aris.DOM.$('fileHotSpotImage').value.replace(/^\s+|\s+$/g, ''); if (fileName.length == 0 || !aris.util.isImageFile(fileName)) { alert(aris.core.getNormalizeValue('<?php AriWebHelper::displayResValue('Validator.NotSelectImage'); ?>')); return false; }; submitbutton('<?php echo $clearTask; ?>$addHotSpotImg');" />
</td> </tr> </tbody> <tbody id="tbHotSpotCont"> <tr> <td colspan="2" align="left">
<img id="imgAriHotSpot" src="<?php echo $mosConfig_live_site;?>/administrator/components/<?php echo $option; ?>/images/x.gif" width="1" height="1" /> </td> </tr> </tbody> </table> 
<input type="hidden" id="hidHotSpotX1" name="hidHotSpotX1" /> <input type="hidden" id="hidHotSpotY1" name="hidHotSpotY1" /> <input type="hidden" id="hidHotSpotX2" name="hidHotSpotX2" /> <input type="hidden" id="hidHotSpotY2" name="hidHotSpotY2" />
<?php } ?>