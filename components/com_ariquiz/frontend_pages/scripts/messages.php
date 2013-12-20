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
    AriKernel::import('Web.Page.AjaxPageBase');
    class messagesAriPage extends AriPageBase {
        function execute() {
            $messages = $this->_getMessages();
            $data = 'YAHOO.ARISoft.page._locale["'.AriQuizComponent::getCodeName() .'"] = '.AriJSONHelper::encode($messages);
            $this->sendResponse($data, 'utf-8');
        }
        function _getMessages() {
            $arisI18N = &AriGlobalPrefs::getI18N();
            $messages = $arisI18N->getMessages();
            return $messages;
        }
    };
?>