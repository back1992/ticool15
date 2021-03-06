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
    AriKernel::import('Web.Controls.Validators.Validator');
    class AriRegExValidatorWebControl extends AriValidatorWebControl {
        var $_regEx;
        function __construct($id, $regEx, $config) {
            $this->setRegEx($regEx);
            $this->extendConfig(array('clientRegEx'=>null));
            parent::__construct($id, $config);
        }
        function setClientRegEx($regEx) {
            $this->setConfifValue('clientRegEx', $regEx);
        }
        function getClientRegEx() {
            $clientRegEx = $this->getConfigValue('clientRegEx');
            if (is_null($clientRegEx)) $clientRegEx = $this->getRegEx();
            return $clientRegEx;
        }
        function getRegEx() {
            return $this->_regEx;
        }
        function setRegEx($regEx) {
            $this->_regEx = $regEx;
        }
        function validate() {
            $isValid = true;
            $control = &$this->getControlToValidate();
            if ($control && method_exists($control, 'getValidateValue')) {
                $value = $control->getValidateValue();
                if (!empty($value)) {
                    $regEx = $this->getRegEx();
                    $isValid = !(!preg_match($regEx, $value));
                }
            }
            $this->setIsValid($isValid);
            return $isValid;
        }
        function _renderJsValidator() {
            $ctrlId = $this->getControlToValidateId();
            $regEx = $this->getClientRegEx();
            $config = array('errorMessage'=>$this->getErrorMessage(), 'validationGroups'=>$this->getGroups(), 'enabled'=>$this->getEnabled());
            $jsConfig = AriJSONHelper::encode($config);
            echo 'YAHOO.ARISoft.validators.validatorManager.addValidator('.'	new YAHOO.ARISoft.validators.regexpValidator(\''.$ctrlId.'\', '.$regEx.','.$jsConfig.'))';
        }
    };
?>