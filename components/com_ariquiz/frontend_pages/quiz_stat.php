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
    AriKernel::import('Web.Page.PageBase');
    AriKernel::import('Controllers.AriQuiz.ResultController');
    AriKernel::import('Components.AriQuiz.Util');
    AriKernel::import('Web.Controls.Data.MultiPageDataTable');
    class quiz_statAriPage extends AriPageBase {
        var $_resultController;
        function _init() {
            $this->_resultController = new AriQuizResultController();
            parent::_init();
        }
        function execute() {
            global $my;
            $userId = !empty($my) ? $my->get('id') : 0;
            if (empty($userId)) {
                AriQuizUtils::redirectToInfo('');
                exit ();
            }
            $dataTable = $this->_createDataTable();
            $this->addVar('dataTable', $dataTable);
            parent::execute();
        }
        function _createDataTable() {
            global $option, $mosConfig_live_site, $Itemid;
            $dsUrl = $mosConfig_live_site.'/index.php?option='.$option.'&task='.$this->executionTask.'$ajax|getStatList&Itemid='.$Itemid;
            $columns = array(new AriDataTableControlColumn(array('key'=>'', 'label'=>AriWebHelper::translateResValue('Label.NumberPos'), 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatPosition', 'className'=>'dtCenter dtColMin')), new AriDataTableControlColumn(array('key'=>'QuizName', 'label'=>AriWebHelper::translateResValue('Label.Quiz'), 'sortable'=>true)), new AriDataTableControlColumn(array('key'=>'StartDate', 'label'=>AriWebHelper::translateResValue('Label.StartDate'), 'sortable'=>true, 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatUtcToLocal')), new AriDataTableControlColumn(array('key'=>'EndDate', 'label'=>AriWebHelper::translateResValue('Label.EndDate'), 'sortable'=>true, 'formatter'=>'YAHOO.ARISoft.widgets.dataTable.formatters.formatUtcToLocal')), new AriDataTableControlColumn(array('key'=>'MaxScore', 'hidden'=>true)), new AriDataTableControlColumn(array('key'=>'UserScore', 'hidden'=>true)), new AriDataTableControlColumn(array('key'=>'PercentScore', 'label'=>AriWebHelper::translateResValue('Label.Score'), 'sortable'=>true, 'formatter'=>'YAHOO.ARISoft.Quiz.formatters.formatResultScore', 'className'=>'dtNoWrap')), new AriDataTableControlColumn(array('key'=>'Passed', 'label'=>AriWebHelper::translateResValue('Label.Passed'), 'sortable'=>true, 'formatter'=>'YAHOO.ARISoft.Quiz.formatters.formatPassed')), new AriDataTableControlColumn(array('key'=>'DetailsLink', 'label'=>AriWebHelper::translateResValue('Label.Details'), 'formatter'=>'YAHOO.ARISoft.Quiz.formatters.formatUserResultDetails')));
            $dataTable = new AriMultiPageDataTableControl('dtUserQuizStat', $columns, array('dataUrl'=>$dsUrl));
            return $dataTable;
        }
        function _registerAjaxHandlers() {
            $this->_registerAjaxHandler('getStatList', 'ajaxGetStatList');
        }
        function ajaxGetStatList() {
            global $my;
            $userId = !empty($my) ? $my->get('id') : 0;
            $filter = new AriDataFilter(array('startOffset'=>0, 'limit'=>25, 'sortField'=>'StartDate', 'sortDirection'=>'desc'), true, null, array('StartDate', 'QuizName', 'EndDate', 'Passed', 'PercentScore'));
            $totalCnt = $this->_resultController->call('getResultsCount', 0, $userId, $filter);
            $filter->fixFilter($totalCnt);
            $stats = $this->_resultController->call('getResults', 0, $userId, $filter);
            $stats = $this->_extendStats($stats);
            $data = AriMultiPageDataTableControl::createDataInfo($stats, $filter, $totalCnt);
            AriResponse::sendJsonResponse($data);
        }
        function _extendStats($stats) {
            global $option, $Itemid;
            if (empty($stats)) return $stats;
            for ($i = 0;$i<count($stats);$i++) {
                $statsItem = &$stats[$i];
                $statsItem->DetailsLink = AriJoomlaBridge::getLink('index.php?option='.$option.'&task=quiz_finished&ticketId='.$statsItem->TicketId.'&Itemid='.$Itemid, false, false);
            }
            return $stats;
        }
    };
?>