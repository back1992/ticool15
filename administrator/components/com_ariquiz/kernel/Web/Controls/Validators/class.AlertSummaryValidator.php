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
    class AriAlertSummaryValidatorWebControl extends AriObject {
        var $_id;
        function __construct($id) {
            $this->setId($id);
            $pageHelper = &AriPageHelper::getInstance();
            $currentPage = &$pageHelper->getCurrentPage();
            $currentPage->addControl($this);
        }
        function getId() {
            return $this->_id;
        }
        function setId($id) {
            $this->_id = $id;
        }
        function render($valGroups = null) {
            $pageHelper = &AriPageHelper::getInstance();
            $currentPage = &$pageHelper->getCurrentPage();
            $failedValidators = &$currentPage->getFailedValidators($valGroups);
            if ($failedValidators) {
                $errorMessages = array();
                foreach($failedValidators as $validator) {
                    $errorMessage = AriWebHelper::translateResValue($validator->getErrorMessageResourceKey());
                    $errorMessage = addslashes($errorMessage);
                    $errorMessages[] = $errorMessage;
                }
                echo '<script type="text/javascript">'.'alert("'.join('\\r\\n', $errorMessages) .'")'.'</script>';
            }
        }
    };
?>