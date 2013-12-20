<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$imagePath = JURI::base(true) . '/components/'.Q_APP_NAME.'/assets/images/';
$images	= array();

$quizzes->path			= $imagePath . '/quizzes.png';
$quizzes->title			= JText::_('LBL_QUIZZES');
$quizzes->href			= 'index.php?option='.Q_APP_NAME.'&view=quiz';
$images[]				= $quizzes;

$approval->path			= $imagePath . '/approval.png';
$approval->title		= JText::_('LBL_APPROVAL');
$approval->href			= 'index.php?option='.Q_APP_NAME.'&view=quiz&status=3';
$images[]               = $approval;

$categories->path       = $imagePath . '/categories.png';
$categories->title      = JText::_('LBL_CATEGORIES');
$categories->href       = 'index.php?option='.Q_APP_NAME.'&view=categories';
$images[]               = $categories;

$configuration->path    = $imagePath . '/configuration.png';
$configuration->title   = JText::_('LBL_CONFIG');
$configuration->href    = 'index.php?option='.Q_APP_NAME.'&view=config';
$images[]               = $configuration;
?>
<table class="contentpaneopen" width="100%">
    <tr>
        <td width="50%" valign="top">
            <div id="cpanel">
                <?php
                foreach($images as $image) { ?>
                <div class="icon">
                    <a href="<?php echo $image->href; ?>">
                        <img src="<?php echo $image->path; ?>" alt="<?php echo $image->title; ?>" align="top" border="0" />
                        <span><?php echo JText::_( $image->title ); ?></span>
                    </a>
                </div>
                    <?php } ?>
            </div>
        </td>
        <td width="50%" valign="top">
            <?php
            jimport('joomla.html.pane');
            $pane =& JPane::getInstance('sliders');
            echo $pane->startPane( 'pane' );
            echo $pane->startPanel( JText::_('LBL_PENDING_APPROVAL'), 'cpanel-pending-approval' );
            if($this->pending) {
            ?>
            <table class="adminlist">
            <?php
            foreach($this->pending as $quiz) {
            ?>
                <tr>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=details&id='.$quiz->id);?>">
                        	<?php echo $this->escape($quiz->title)?>
						</a>
                    </td>
                    <td>
                        <?php echo ($quiz->username)?$quiz->username:JText::_('LBL_GUEST'); ?>
                    </td>
                    <td>
			        	<a title="<?php echo JText::_('LBL_PUBLISH');?>" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=publish&cid[]='.$quiz->id);?>">
			        		<img src="<?php echo JURI::base(true).'/components/'.Q_APP_NAME.'/assets/images/pending.png';?>">
			        	</a>
                    </td>
                </tr>
            <?php
            }
            ?>
            </table>
            <?php
            }else {
                echo '<p style="margin: 5px">' . JText::_('NO_RESULTS') . '</p>';
            }
            echo $pane->endPanel();
            echo $pane->startPanel( JText::_('LBL_LATEST_QUIZZES'), 'cpanel-latest-quizzes' );
            if($this->latest) {
            ?>
            <table class="adminlist">
            <?php
            foreach($this->latest as $quiz) {
            ?>
                <tr>
                    <td>
                    	<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=details&id='.$quiz->id);?>">
                        	<?php echo $this->escape($quiz->title)?>
						</a>			                        	
                    </td>
                    <td>
                        <?php echo ($quiz->username)?$quiz->username:JText::_('LBL_GUEST'); ?>
                    </td>
                </tr>
            <?php 
            }
            ?>
            </table>
            <?php
            }else {
                echo '<p style="margin: 5px">' . JText::_('NO_RESULTS') . '</p>';
            }
            echo $pane->endPanel();
            echo $pane->endPane();
            ?>
            <?php
            if($this->update){
            ?>
            <table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="2">
                            <?php echo JText::_( 'COMMUNITYQUIZ_VERSION_INFO' ); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="40%"><?php echo JText::_('CQ_INSTALLED_VERSION');?></td>
                        <td>
                            <?php echo CQ_CURR_VERSION; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('CQ_LATEST_VERSION');?></td>
                        <td>
                            <strong><?php echo $this->update['version']; ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('CQ_VERSION_RELEASED');?></td>
                        <td>
                            <strong><?php echo $this->update['released']; ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('CQ_VERSION_STATUS');?></td>
                        <td>
                            <?php
                            if($this->update['status'] == 0){
                                echo '<img src="' . JURI::base(true) . '/components/'.Q_APP_NAME.'/assets/images/ok.png">';
                                echo JText::_('CQ_VERSION_OK');
                            }else{
                                echo '<img src="' . JURI::base(true) . '/components/'.Q_APP_NAME.'/assets/images/caution.png">';
                                echo JText::_('CQ_VERSION_OBSOLETE');
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
            }
            ?>
        </td>
    </tr>
</table>