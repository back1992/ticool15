<?php
/**
 * Joomla! 1.5 component Community Quiz
 *
 * @version $Id: answers.php 2010-01-02 03:45:15 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage Community Quiz
 * @license GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class CommunityQuizControllerCategories extends JController {
    function __construct() {
        parent::__construct();
        $this->registerDefaultTask('get_categories');
        $this->registerTask('save', 'save_category');
        $this->registerTask('sort', 'sort_category');
        $this->registerTask('edit', 'add');
        $this->registerTask('delete', 'delete');
        $this->registerTask('movedown', 'movedown');
        $this->registerTask('moveup', 'moveup');
        $this->registerTask('refresh', 'refresh');
    }

    function get_categories() {
        $view = &$this->getView('categories', 'html');
        $model = &$this->getModel('categories');
        $view->setModel($model, true);
        $view->setLayout('list');
        $view->display();
    }

    function sort_category(){
        $strid = trim(JRequest::getVar('id',null,'post','STRING'));
        $strparent = trim(JRequest::getVar('parent',null,'post','STRING'));
        $id = intval(substr($strid, strpos($strid, '-')+1));
        $new_parent = intval(substr($strparent, strpos($strparent, '-')+1));
        $model = &$this->getModel('categories');
        if(!$model->sort($id, $new_parent)){
            echo JText::_('MSG_ERROR');
        }
        jexit();
    }

    function delete(){
        $id = JRequest::getVar('id',0,'','INT');
        if(!$id){
            $msg = 'Invalid category id requested';
        }else{
            $model = $this->getModel('categories');
            $msg = JText::_('MSG_SUCCESS');
            if(!$model->delete($id)){
                $msg = JText::_('MSG_ERROR');
            }
        }
        $link = 'index.php?option='.Q_APP_NAME.'&view=categories&task=list';
        $this->setRedirect($link, $msg);
    }

    function add(){
        $view = &$this->getView('categories', 'html');
        $model = &$this->getModel('categories');
        $view->setModel($model, true);
        $view->setLayout('add');
        $view->display();
    }

    function cancel(){
        $msg = 'Operation cancelled.';
        $link = 'index.php?option='.Q_APP_NAME.'&view=categories&task=list';
        $this->setRedirect($link, $msg);
    }

    function save(){
        $model = &$this->getModel('categories');
        $msg = JText::_('MSG_SUCCESS');
        if(!$model->save()){
            $msg = JText::_('MSG_ERROR');
        }
        $link = 'index.php?option='.Q_APP_NAME.'&view=categories&task=list';
        $this->setRedirect($link, $msg);
    }

    function movedown(){
        $model = &$this->getModel('categories');
        $id = JRequest::getInt('id');
        $msg = JText::_('MSG_SUCCESS');
        if(!$model->movedown($id)){
            $msg = JText::_('MSG_ERROR').$model->getError();
        }
        $link = 'index.php?option='.Q_APP_NAME.'&view=categories&task=list';
        $this->setRedirect($link, $msg);
    }

    function moveup(){
        $model = &$this->getModel('categories');
        $id = JRequest::getInt('id');
        $msg = JText::_('MSG_SUCCESS');
        if(!$model->moveup($id)){
            $msg = JText::_('MSG_ERROR');
        }
        $link = 'index.php?option='.Q_APP_NAME.'&view=categories&task=list';
        $this->setRedirect($link, $msg);
    }
    
    function refresh(){
        $model = &$this->getModel('categories');
        $model->rebuild_categories();
        $link = 'index.php?option='.Q_APP_NAME.'&view=categories&task=list';
        $this->setRedirect($link, JText::_('MSG_CATEGORIES_REFRESHED'));
    }
}
?>
