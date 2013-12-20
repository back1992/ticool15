<?php
/**
 * Joomla! 1.5 component Community Polls
 *
 * @version $Id: categories.php 2009-08-10 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Polls
 * @license GNU/GPL
 *
 * The Community questions allows the members of the Joomla website to create and manage questions from the front-end.
 * The administrator has the powerful tools provided in the back-end to manage the questions published by all users.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Description of AnswersControllerConfig
 *
 * @author maverick
 */
class CommunityQuizControllerConfig extends JController {

    function __construct() {
        parent::__construct();
        $this->registerDefaultTask('getConfig');
    }
    
    function getConfig() {
        $view = & $this->getView('config', 'html');
        $model = & $this->getModel('config');
        $view->setModel($model, true);
        $view->setLayout('form');
        $view->display();
    }

    function save() {
        $model =& $this->getModel('config');
        if ($model->save()) {
            $message = JText::_('MSG_CONFIG_SAVED');
        }else {
            $message = $model->getError();
        }

        $this->setRedirect('index.php?option='.Q_APP_NAME.'&view=config', $message);
    }

    function cancel() {
        $msg = JText::_( 'Operation Cancelled' );
        $this->setRedirect( 'index.php?option='.Q_APP_NAME, $msg );
    }
}
?>
