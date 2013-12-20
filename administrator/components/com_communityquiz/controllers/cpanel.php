<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: cpanel.php 2010-08-10 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Quiz
 * @license GNU/GPL
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

class CommunityQuizControllerCPanel extends JController {

    function __construct() {
        parent::__construct();
        $this->registerDefaultTask('CPanel');
    }

    function CPanel() {
        $view =& $this->getView('cpanel', 'html');
        $model =& $this->getModel('quiz');
        $view->setModel($model, true);
        $view->display();
    }
}
?>
