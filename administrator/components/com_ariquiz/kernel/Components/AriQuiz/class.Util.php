<?php
    /**
    ARI Soft copyright
    * Copyright (C) 2008 ARI Soft.
    * All Rights Reserved.  No use, copying or distribution of this
    * work may be made except in accordance with a valid license
    * agreement from ARI Soft. This notice must be included on
    * all copies, modifications and derivatives of this work.
    *
    * ARI Soft products are provided "as is" without warranty of
    * any kind, either expressed or implied. In no event shall our
    * juridical person be liable for any damages including, but
    * not limited to, direct, indirect, special, incidental or
    * consequential damages or other losses arising out of the use
    * of or inability to use our products.
    *
    *
    */
    defined('ARI_FRAMEWORK_LOADED') or die ('Direct Access to this location is not allowed.');
    AriKernel::import('Controllers.AriQuiz.UserQuizController');
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Cache.FileCache');
    class AriQuizUtils extends AriObject {
        function getShortPeriods() {
            return array(AriWebHelper::translateResValue('Date.DayShort') =>ARIS_DATE_DSC, AriWebHelper::translateResValue('Date.HourShort') =>ARIS_DATE_HSC, AriWebHelper::translateResValue('Date.MinuteShort') =>ARIS_DATE_MINSC, AriWebHelper::translateResValue('Date.SecondShort') =>ARIS_DATE_SSC);
        }
        function checkQuizAvailability($userId, $ticketId, $onlyCheck = false, $checkPaused = true) {
            global $option, $my;
            $quizController = new AriUserQuizController();
            $canTake = $quizController->canTakeQuizByTicketId($ticketId, $userId, $my->get('usertype'), $checkPaused);
            if (!$canTake) {
                if (!$onlyCheck) {
                    AriQuizUtils::redirectToInfo('FrontEnd.QuizNotAvailable');
                }
                return false;
            }
            return true;
        }
        function redirectToInfo($mid, $rurl = '', $params = array()) {
            global $option, $Itemid;
            $params['option'] = $option;
            $params['task'] = 'quiz_info';
            $params['mid'] = $mid;
            $params['Itemid'] = $Itemid;
            if (!empty($rurl)) $params['rurl'] = $rurl;
            $url = 'index.php?';
            $urlParams = '';
            foreach($params as $key=>$value) {
                if (!empty($urlParams)) $urlParams.= '&';
                $urlParams.= $key.'='.urlencode($value);
            }
            AriResponse::redirect(AriJoomlaBridge::getLink($url.$urlParams, false, false));
        }
        function getCssFile($cssId) {
            global $mosConfig_live_site, $mosConfig_absolute_path;
            $option = AriQuizComponent::getCodeName();
            $templateGroup = AriConstantsManager::getVar('FileGroup.CssTemplate', $option);
            $cssFile = $mosConfig_live_site.'/components/'.$option.'/css/default.css';
            $cacheDir = $mosConfig_absolute_path.'/components/'.$option.'/cache/files/'.$templateGroup.'/';
            $webCacheDir = $mosConfig_live_site.'/components/'.$option.'/cache/files/'.$templateGroup.'/';
            if (!empty($cssId)) {
                $fileName = $cacheDir.$cssId.'.css';
                $isExists = file_exists($fileName);
                if (!$isExists) {
                    $fileController = new AriFileController(AriConstantsManager::getVar('FileTable', $option));
                    $file = $fileController->getFile($cssId, $templateGroup);
                    if (!empty($file)) {
                        AriFileCache::saveTextFile($file->Content, $fileName);
                        $isExists = file_exists($fileName);
                    }
                }
                if ($isExists) {
                    $cssFile = $webCacheDir.$cssId.'.css';
                }
            }
            return $cssFile;
        }
        function getStatByCategoriesHtml($result) {
            $html = '';
            if (!is_array($result) || count($result) == 0) return $html;
            $html = '<table cellpadding="0" cellspacing="0" border="0" class="ariQuizStatByCategories">';
            foreach($result as $resultItem) {
                $html.= sprintf('<tr><td class="catName">%s</td><td class="pointInfo">%s</td></tr>', $resultItem['CategoryName'], sprintf('%d / %d (%d%%)', $resultItem['UserScore'], $resultItem['MaxScore'], $resultItem['PercentScore']));
            }
            $html.= '</table>';
            return $html;
        }
    };
?>