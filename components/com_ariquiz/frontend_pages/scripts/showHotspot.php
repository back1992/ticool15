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
    AriKernel::import('Controllers.AriQuiz.QuestionController');
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Cache.FileCache');
    class showHotspotAriPage extends AriPageBase {
        var $_userQuizController;
        var $_questionController;
        var $_quizStorage;
        var $_ticketId;
        function _init() {
            $this->_userQuizController = new AriUserQuizController();
            $this->_questionController = new AriQuizQuestionController();
            parent::_init();
        }
        function execute() {
            $this->sendResponse($this->_getImage());
        }
        function getQuizStorage() {
            if (is_null($this->_quizStorage)) {
                global $my;
                $this->_quizStorage = AriEntityFactory::createInstance('AriQuizStorageEntity', AriGlobalPrefs::getEntityGroup(), $this->getTicketId(), $my);
            }
            return $this->_quizStorage;
        }
        function getTicketId() {
            if (is_null($this->_ticketId)) {
                $this->_ticketId = AriRequest::getParam('ticketId', '');
            }
            return $this->_ticketId;
        }
        function _getImage() {
            global $mosConfig_absolute_path, $option, $mosConfig_live_site;
            $quizStorage = $this->getQuizStorage();
            $sid = $quizStorage->get('StatisticsInfoId');
            $statistics = $this->_userQuizController->call('getCurrentQuestion', $sid);
            if (empty($statistics) || empty($statistics->StatisticsId)) {
                return $this->_getErrorImage();
            }
            $questionVersionId = $statistics->Question->QuestionVersionId;
            $questionVersion = $statistics->Question->QuestionVersion;
            if ($questionVersion->QuestionType->ClassName != 'HotSpotQuestion') {
                return $this->_getErrorImage();
            }
            $files = $this->_questionController->call('getQuestionFiles', $statistics->BankVersionId ? $statistics->BankVersionId : $questionVersionId);
            if (empty($files['hotspot_image'])) {
                return $this->_getErrorImage();
            }
            $imageFile = $files['hotspot_image'];
            $questionEntity = AriEntityFactory::createInstance($questionVersion->QuestionType->ClassName, AriConstantsManager::getVar('QuestionEntityGroup', AriQuizComponent::getCodeName()));
            $questionData = $questionEntity->getDataFromXml($questionVersion->Data);
            $hotspotGroup = AriConstantsManager::getVar('FileGroup.HotSpot', $option);
            $cacheFile = $imageFile['FileId'].'.'.$imageFile['Extension'];
            $cacheDir = $mosConfig_absolute_path.'/administrator/components/'.$option.'/cache/files/'.$hotspotGroup.'/';
            $wwwDir = $mosConfig_live_site.'/administrator/components/'.$option.'/cache/files/'.$hotspotGroup.'/';
            $hotSpotImg = '';
            if (empty($cacheFile) || !file_exists($cacheDir.$cacheFile)) {
                $fileId = $imageFile['FileId'];
                $fileController = new AriFileController(AriConstantsManager::getVar('FileTable', AriQuizComponent::getCodeName()));
                $cacheImageList = $fileController->call('getFileList', $hotspotGroup, array($fileId), true);
                if (!empty($cacheImageList) && count($cacheImageList) >0) {
                    $cacheImage = $cacheImageList[0];
                    if (!file_exists($cacheDir.$cacheImage->FileId.'.'.$cacheImage->Extension)) {
                        AriFileCache::saveBinaryFile($cacheImage->Content, $cacheDir.$cacheImage->FileId.'.'.$cacheImage->Extension);
                    }
                    $hotSpotImg = $cacheDir.$cacheImage->FileId.'.'.$cacheImage->Extension;
                }
            } else {
                $hotSpotImg = $cacheDir.$cacheFile;
            }
            $oldMQR = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            if (!file_exists($hotSpotImg)) return $this->_getErrorImage();
            $handle = fopen($hotSpotImg, "rb");
            $content = fread($handle, filesize($hotSpotImg));
            fclose($handle);
            set_magic_quotes_runtime($oldMQR);
            return $content;
        }
        function _getErrorImage() {
            return '';
        }
    };
?>