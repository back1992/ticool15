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
    AriKernel::import('I18N.I18N');
    AriKernel::import('Components.Constants');
    class AriQuizConstants extends AriComponentConstants {
        var $Option = null;
        var $TextTemplateTable = '#__arigenerictemplate';
        var $PersistanceTable = '#__ariquiz_persistance';
        var $FileTable = '#__ariquizfile';
        var $ConfigTable = '#__ariquizconfig';
        var $MailTemplateTable = '#__ariquizmailtemplate';
        var $PropertyTable = array('Property'=>'#__ariquiz_property', 'PropertyValue'=>'#__ariquiz_property_value');
        var $DbCharset = 'UTF-8';
        var $EntityGroup = '_AriQuizEntity';
        var $QuestionEntityGroup = '_AriQuizQuestionEntity';
        var $EntityKey = 'AriQuiz';
        var $Config = array('Version'=>'Version', 'BackendLang'=>'BLang', 'FrontendLang'=>'FLang');
        var $FileGroup = array('BackendLang'=>'lbackend', 'FrontendLang'=>'lfrontend', 'CssTemplate'=>'css', 'HotSpot'=>'hotspot');
        var $TemplateGroup = array('Results'=>'QuizResult', 'MailResults'=>'QuizMailResult');
        var $TextTemplates = array('Successful'=>'QuizSuccessful', 'Failed'=>'QuizFailed', 'SuccessfulEmail'=>'QuizSuccessfulEmail', 'FailedEmail'=>'QuizFailedEmail', 'SuccessfulPrint'=>'QuizSuccessfulPrint', 'FailedPrint'=>'QuizFailedPrint', 'AdminEmail'=>'QuizAdminEmail');
        function __construct() {
            global $mosConfig_absolute_path;
            $this->Option = AriQuizComponent::getCodeName();
            $this->CacheDir = $mosConfig_absolute_path.'/administrator/components/'.$this->Option.'/cache/files/';
            parent::__construct();
        }
    }
    new AriQuizConstants();
    class AriQuizComponent extends AriObject {
        function getCodeName() {
            return 'com_ariquiz';
        }
        function &instance() {
            static $instance;
            if (!isset($instance)) {
                $c = __CLASS__;
                $instance = new $c();
            }
            return $instance;
        }
        function init($loadI18N = true) {
            $codeName = $this->getCodeName();
            AriGlobalPrefs::setOption($codeName);
            AriGlobalPrefs::setConfigGroup($codeName);
            AriGlobalPrefs::setCacheDir(AriConstantsManager::getVar('CacheDir', $codeName));
            AriGlobalPrefs::setConfigTable(AriConstantsManager::getVar('ConfigTable', $codeName));
            AriGlobalPrefs::setFileTable(AriConstantsManager::getVar('FileTable', $codeName));
            AriGlobalPrefs::setPersistanceTable(AriConstantsManager::getVar('PersistanceTable', $codeName));
            if ($loadI18N) {
                $i18n = &$this->_getI18N();
                AriGlobalPrefs::setI18N($i18n);
                AriGlobalPrefs::setDbCharset(AriConstantsManager::getVar('DbCharset', $codeName));
                AriGlobalPrefs::setEntityGroup(AriConstantsManager::getVar('EntityGroup', $codeName));
            }
        }
        function &_getI18N() {
            $isAdmin = AriJoomlaBridge::isAdmin();
            $codeName = $this->getCodeName();
            $configKey = $isAdmin ? 'Config.BackendLang' : 'Config.FrontendLang';
            $configKey = AriConstantsManager::getVar($configKey, $codeName);
            $fileGroup = $isAdmin ? 'FileGroup.BackendLang' : 'FileGroup.FrontendLang';
            $fileGroup = AriConstantsManager::getVar($fileGroup, $codeName);
            $i18n = $this->_createI18N($configKey, $fileGroup);
            return $i18n;
        }
        function _createI18N($configKey, $fileGroup) {
            AriKernel::import('Cache.FileCache');
            AriKernel::import('Config.ConfigWrapper');
            $cacheDir = AriGlobalPrefs::getCacheDir();
            $useLang = AriConfigWrapper::getConfigKey($configKey, 'en');
            AriFileCache::cacheFile($cacheDir, $fileGroup, $useLang, 'xml');
            return new ArisI18N($cacheDir.$fileGroup, $useLang, $cacheDir.'i18n/'.$fileGroup, $this->getCodeName(), 'en');
        }
    };
?>