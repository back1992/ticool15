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
    if (!defined('ARI_FRAMEWORK_LOADED')) {
        define('ARI_ROOT_NAMESPACE', '_ARISoft');
        define('ARI_CONSTANTS_NAMESPACE', 'Constants');
        define('ARI_FRAMEWORK_LOADED', true);
        class AriKernel {
            var $_loadedNamespace = array();
            var $_frameworkPathList = array();
            function &instance() {
                static $instance;
                if (!isset($instance)) {
                    $instance = new AriKernel();
                }
                return $instance;
            }
            function init() {
                $GLOBALS[ARI_ROOT_NAMESPACE] = array();
                $GLOBALS[ARI_ROOT_NAMESPACE][ARI_CONSTANTS_NAMESPACE] = array();
                AriKernel::addFrameworkPath(dirname(__FILE__) .'/');
            }
            function addFrameworkPath($path) {
                $inst = &AriKernel::instance();
                $inst->_frameworkPathList[] = $path;
            }
            function import($namespace) {
                $inst = &AriKernel::instance();
                if (isset($inst->_loadedNamespace[$namespace])) return;
                $part = explode('.', $namespace);
                $lastPos = count($part) -1;
                $part[$lastPos] = 'class.'.$part[$lastPos].'.php';
                $pathList = $inst->_frameworkPathList;
                $fileLocalPath = join('/', $part);
                foreach($pathList as $path) {
                    $filePath = $path.$fileLocalPath;
                    if (file_exists($filePath)) {
                        require_once $filePath;
                        $inst->_loadedNamespace[$namespace] = true;
                        break;
                    }
                }
            }
        }
        AriKernel::init();
        AriKernel::import('Core.Object');
    } else {
        AriKernel::addFrameworkPath(dirname(__FILE__) .'/');
        AriKernel::import('Core.Object');
    };
?>