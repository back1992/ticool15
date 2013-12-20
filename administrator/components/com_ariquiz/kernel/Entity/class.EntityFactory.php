<?php

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');
AriKernel::import('Constants.ClassConstants');
AriKernel::import('Entity.AriDBTable');

class AriEntityFactoryConstants extends AriClassConstants {

    var $CommonEntity = '_CommonEntity';

    function getClassName() {
        return strtolower('AriEntityFactoryConstants');
    }

}

new AriEntityFactoryConstants();

class AriEntityFactory {

    function createInstance($className, $group = null) {
        if (!preg_match('/^[A-z]+$/', $className))
            return null; if (empty($group)) {
            $group = AriConstantsManager::getVar('CommonEntity', AriEntityFactoryConstants::getClassName());
        } $kernel = & AriKernel::instance();
        $pathList = $kernel->_frameworkPathList;
        foreach ($pathList as $includePath) {
            $path = sprintf('%sEntity/%sclass.%s.php', $includePath, $group != null ? $group . '/' : '', strtolower($className));
            if (file_exists($path)) {
                require_once $path;
                if (class_exists($className)) {
                    $args = null;
                    $numargs = func_num_args();
                    $funcArgs = '';
                    if ($numargs > 2) {
                        $args = func_get_args();
                        $args = array_slice($args, 2);
                        for ($i = 0; $i < $numargs - 2; $i++) {
                            $funcArgs .= ',$args[' . $i . ']';
                        }
                    } $inst = eval(sprintf('global $database;return new %s($database%s);', $className, $funcArgs));
                    return $inst;
                }
            }
        } return null;
    }

}

define('ARI_ENTITYFACTORY_DIR', dirname(__FILE__) . '/');
?>