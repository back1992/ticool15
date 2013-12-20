<?php
/**
 * Joomla! 1.5 component CommunityQuiz
 *
 * @version $Id: view.html.php 2010-11-15 13:08:52 svn $
 * @author Maverick
 * @package Joomla
 * @subpackage CommunityQuiz
 * @license GNU/GPL
 *
 * Community Quiz allow users to create and take quiz with easy and exiting user interface coupled with Ajax powered web 2.0 API.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class CommunityQuizViewConfig extends JView {
    function display($tpl = null) {
        $config = null;

        JToolBarHelper::title( JText::_('TITLE_COMMUNITY_QUIZ').': <small><small>[ ' . JText::_('LBL_CONFIG') .' ]</small></small>', 'quiz.png');
        
        if($this->getLayout() == 'form') {
            JToolBarHelper::save();
            
            $configt =& $this->get('configuration');
            foreach($configt as $ct){
                $config[$ct->config_name] = $ct->config_value;
            }

            $this->assignRef( 'config', $config );
        }
        parent::display($tpl);
    }
}
?>