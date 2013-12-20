<?php 
defined('_JEXEC') or die('Restricted access');
$user = &JFactory::getUser();
$config = CommunityQuizHelper::getConfig();
$itemid = CommunityQuizHelper::getItemId();
$catparam = isset($this->catid)? '&catid='.$this->catid : '';
$task = JRequest::getCmd('task', 'latest');
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    QuizFactory.init_quiz_list();
});
</script>
<div id="quiz-wrapper-list">
	<?php echo CommunityQuizHelper::loadModulePosition('quiz_list_above_navigation');?>
	<?php if($config[CQ_CLEAN_HOME_PAGE] != '1'):?>
	<div class="navigation_table ui-widget-content ui-corner-all">
		<?php if($config[CQ_ENABLE_CATEGORY_BOX] == '1'):?>
	    <div class="page_title"><?php echo JText::_('TXT_CATEGORIES') . (isset($this->page_header)?' - '.$this->page_header:'');?></div>
	    <?php if(!empty($this->categories) && count($this->categories) > 1):?>
	    <table class="categorybox">
	    <?php
        $current = 0;
        $cols = 3;
        foreach($this->categories as $category){
	        if($category->id != isset($this->catid) && $category->parent_id > 0 && $category->nlevel > 0){
    	    $category_href = JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task='.$task.'&catid='.$category->id.(!empty($category->alias)?':'.$category->alias:'').$itemid);
        	$title = sprintf(JText::_('TXT_CATEGORY_TOOLTIP'), $category->title, $category->quizzes);
        	if(($current)%$cols == 0){
        		?>
	        	<tr>
        		<?php 
        		}
        		?>
            	<td>
                	<a href="<?php echo $category_href;?>" title="<?php echo $title;?>"><?php echo $this->escape($category->title).' ('.$category->quizzes.')';?></a>
            	</td>
        		<?php if(($current)%$cols == ($cols - 1)):?>
        		</tr>
        		<?php endif;?>
        		<?php
        		$current++;
	    	}
        }
	    ?>
	    </table>
	    <?php endif; //end categories exist?>
	    <?php endif; //end enable category box?>
	    <div class="searchform-wrapper">
            <form id="searchform" action="index.php?option=<?php echo Q_APP_NAME;?>&view=quiz&task=search<?php echo $itemid;?>" method="post">
                <input name="searchkey" id="cq_search" type="text" size="15"/>
                <input type="submit" id="btn_search" value="<?php echo JText::_('LBL_SEARCH');?>">
                <input type="hidden" name="option" value="<?php echo Q_APP_NAME;?>">
                <input type="hidden" name="view" value="quiz">
                <input type="hidden" name="task" value="search">
                <input type="hidden" name="Itemid" value="<?php echo isset($mnuitem)?$mnuitem->id:'';?>">
            </form>
        </div>
	</div>
	<div class="actionbar">
		<div class="actions">
        	<?php if(CAuthorization::authorise('quiz.create')):?>
        	<a id="btn_create_new" href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=create'.$itemid);?>"><?php echo JText::_('LBL_CREATE_NEW');?></a>
        	<?php endif;?>
		</div>
		<div class="bookmarks">
            	<?php 
				echo JHtml::link(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz'.$itemid), JText::_('LBL_HOME'), 'id="btn_home"'.($this->page == '0' ? 'class="ui-state-active"':''));
				$buttons = explode(',', !empty($config[CQ_TOOLBAR_BUTTONS]) ? $config[CQ_TOOLBAR_BUTTONS] : 'L,P');
				foreach ($buttons as $button){
					switch ($button) {
						case 'L':
							echo JHtml::link(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=latest'.$catparam.$itemid), JText::_('LBL_LATEST_QUIZZES'), 'id="btn_open"'.($this->page == '1' ? ' class="ui-state-active"':''));
							break;        			
						case 'P':
							echo JHtml::link(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=popular'.$catparam.$itemid), JText::_('LBL_MOST_POPULAR_QUIZZES'), 'id="btn_most_popular"'.($this->page == '2' ? ' class="ui-state-active"':''));
							break;        			
						case 'T':
							echo JHtml::link(JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=toprated'.$catparam.$itemid), JText::_('LBL_TOP_RATED_QUIZZES'), 'id="btn_top_rated"'.($this->page == '3' ? ' class="ui-state-active"':''));
							break;        			
					}
	            }
	            ?>
		</div>
		<div class="clear"></div>
	</div>
	<?php if(!$user->guest):?>
	<div class="toolbar ui-widget-content ui-corner-all">
		<ul class="submenu">
			<li><a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=my_quizzes'.$itemid)?>" class="active"><?php echo JText::_('LBL_MY_QUIZZES')?></a></li>
			<li><a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=my_responses'.$itemid)?>" class="active" style="border-right: 0px;"><?php echo JText::_('LBL_MY_RESPONSES')?></a></li>
		</ul>
	</div>
	<?php endif;?>
	<?php endif; //end clean home page?>
	
	<?php echo CommunityQuizHelper::loadModulePosition('quiz_list_below_navigation');?>
	
	<div class="subtitle"><?php echo $this->list_header;?></div>
	<?php if(!empty($this->list)):?>
	<ul class="data_table">
	<?php
	$i=0;
	foreach($this->list as $count=>$quiz){
		$quiz_href = JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz->id.(!empty ($quiz->alias)?':'.$quiz->alias:'').$itemid );
		if(isset($this->user_responses)){
			$quiz_href = JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=quiz&task=results&id='.$quiz->response_id.(!empty ($quiz->alias)?':'.$quiz->alias:'').$itemid );
		}
		$category_href = JHtml::link(
    		JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=quiz&task='.$task.'&catid='.$quiz->catid.(!empty ($quiz->calias)?':'.$quiz->calias:'').$itemid),
    		$quiz->category
    	);
    	$user_profile_link = $quiz->created_by ? CommunityQuizHelper::getUserProfileUrl($quiz->created_by, $quiz->username) : JText::_('LBL_GUEST');
    	$quiz_date = CommunityQuizHelper::getFormattedDate($quiz->created);
	    ?>
	    <li class="ui-widget-content <?php echo ((count($this->list) > $count+1) ? 'dataitem' : '').($count == 0 ? ' ui-corner-top':'').($count+1 == count($this->list) ? ' ui-corner-bottom':'');?>">
	        <div class="avatar ui-widget-content"><?php echo ($config[CQ_SHOW_AVATAR_IN_LISTING] == '1') ? CommunityQuizHelper::getUserAvatar($quiz->created_by, $config[CQ_AVATAR_SIZE]) : '';?></div>
	        <div class="responsebox ui-widget-content">
				<div class="responsecount"><?php echo $quiz->responses;?></div>
				<div class="responsedesc"><?php echo ($quiz->responses == 1) ? JText::_('TXT_RESPONSE') : JText::_('TXT_RESPONSES');?></div>
	        </div>
	        <?php if($config[CQ_ENABLE_RATINGS] == '1'):?>
	        <div class="quiz-list-rating">
	        	<div class="star-rating">
					<input type="radio" name="newrate" value="1" title="Poor" <?php echo ($quiz->rating > 0 & $quiz->rating < 2) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="2" title="Average" <?php echo ($quiz->rating >= 2 & $quiz->rating < 3) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="3" title="Good" <?php echo ($quiz->rating >= 3 & $quiz->rating < 4) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="4" title="Very good" <?php echo ($quiz->rating >= 4 & $quiz->rating < 5) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="5" title="Excellent" <?php echo ($quiz->rating == 5) ? 'checked="checked"' : ''?>  />
	        	</div>
	        	<div class="rating-details"><?php echo sprintf(JText::_('LBL_RATING_NUM'), empty($quiz->rating) ? '0' : $quiz->rating);?></div>
	        </div>
	        <?php endif;?>
	        <div class="quiz-item-main">
		        <div class="quiz_title">
		            <a href="<?php echo $quiz_href; ?>"><?php echo $this->escape($quiz->title);?></a>
		            <?php if(isset($this->user_quizzes) && $this->user_quizzes == true):?>
		            <img 
		            	src="<?php echo $templateUrlPath.'/images/'.(($quiz->published==1)?'published.png':(($quiz->published==2)?'pending.png':'unpublished.png'))?>"
		            	title="<?php echo ($quiz->published==1)?JText::_('TXT_PUBLISHED'):(($quiz->published==2)?JText::_('TXT_PENDING'):JText::_('TXT_UNPUBLISHED'))?>"/>
		            <?php endif;?>
		            <?php if((($quiz->created_by == $user->id) && CAuthorization::authorise('quiz.edit')) || CAuthorization::authorise('quiz.manage')):?>
		            &nbsp;-&nbsp;<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=edit&id='.$quiz->id.':'.$quiz->alias.$itemid);?>"><?php echo JText::_('LBL_EDIT');?></a>
		            &nbsp;-&nbsp;<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=reports&id='.$quiz->id.':'.$quiz->alias.$itemid);?>"><?php echo JText::_('LBL_REPORTS');?></a>
		            <?php endif;;?>
		        </div>
		        <div class="quiz_meta">
		        	<?php echo sprintf(JText::_('LBL_QUIZ_META'), $category_href, $user_profile_link, $quiz_date)?>
		        </div>
		        <div class="quiz_meta">
		            <?php if(isset($quiz->responded_on)):?>
		            <?php echo JText::_('TXT_RESPONDED').' '.CommunityQuizHelper::getFormattedDate($quiz->responded_on);?>
		            <?php endif;?>
		        </div>
			</div>
	        <div class="clear"></div>
	    </li>
	    <?php
	    $i=1-$i;
	} // end for
	?>
	</ul>
	<?php echo CommunityQuizHelper::loadModulePosition('quiz_below_list');?>
	<?php if(!empty($this->pagination)): ?>
	<table width="100%">
	    <tr>
	        <td colspan="<?php echo $cols; ?>">
	            <div style="float: left;">
	                <?php echo $this->pagination->getPagesLinks(); ?>
	            </div>
	            <div style="float: right;">
	                <?php echo $this->pagination->getResultsCounter(); ?>
	            </div>
	            <div style="clear:both;"></div>
	        </td>
	    </tr>
	</table>
	<?php endif; //end pagination?>	
	<?php else : ?>
	    <?php echo JText::_('NO_RESULTS');?>
	<?php endif;//end list?>
	
	<?php if(!empty($this->popular)):?>
	<div class="subtitle" style="margin-top: 20px;"><?php echo JText::_('TXT_MOST_POPULAR_QUIZZES');?></div>
	<ul class="data_table">
	<?php
	$i=0;
	foreach($this->popular as $count=>$quiz){
		$quiz_href = JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=quiz&task=respond&id='.$quiz->id.(!empty ($quiz->alias)?':'.$quiz->alias:'').$itemid );
		if(isset($this->user_responses)){
			$quiz_href = JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=quiz&task=results&id='.$quiz->response_id.(!empty ($quiz->alias)?':'.$quiz->alias:'').$itemid );
		}
		$category_href = JHtml::link(
    		JRoute::_( 'index.php?option='.Q_APP_NAME.'&view=quiz&task='.$task.'&catid='.$quiz->catid.(!empty ($quiz->calias)?':'.$quiz->calias:'').$itemid),
    		$quiz->category
    	);
    	$user_profile_link = $quiz->created_by ? CommunityQuizHelper::getUserProfileUrl($quiz->created_by, $quiz->username) : JText::_('LBL_GUEST');
    	$quiz_date = CommunityQuizHelper::getFormattedDate($quiz->created);
    	$user_avatar = ($config[CQ_SHOW_AVATAR_IN_LISTING] == '1') ? CommunityQuizHelper::getUserAvatar($quiz->created_by, $config[CQ_AVATAR_SIZE]) : '';
	    ?>
	    <li class="ui-widget-content <?php echo ((count($this->popular) > $count+1) ? 'dataitem' : '').($count == 0 ? ' ui-corner-top':'').($count+1 == count($this->popular) ? ' ui-corner-bottom':'');?>">
	        <div class="avatar ui-widget-content"><?php echo ($config[CQ_SHOW_AVATAR_IN_LISTING] == '1') ? CommunityQuizHelper::getUserAvatar($quiz->created_by, $config[CQ_AVATAR_SIZE]) : '';?></div>
	        <div class="responsebox ui-widget-content">
				<div class="responsecount"><?php echo $quiz->responses;?></div>
				<div class="responsedesc"><?php echo ($quiz->responses == 1) ? JText::_('TXT_RESPONSE') : JText::_('TXT_RESPONSES');?></div>
	        </div>
	        <?php if($config[CQ_ENABLE_RATINGS] == '1'):?>
	        <div class="quiz-list-rating">
	        	<div class="star-rating">
					<input type="radio" name="newrate" value="1" title="Poor" <?php echo ($quiz->rating > 0 & $quiz->rating < 2) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="2" title="Average" <?php echo ($quiz->rating >= 2 & $quiz->rating < 3) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="3" title="Good" <?php echo ($quiz->rating >= 3 & $quiz->rating < 4) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="4" title="Very good" <?php echo ($quiz->rating >= 4 & $quiz->rating < 5) ? 'checked="checked"' : ''?> />
					<input type="radio" name="newrate" value="5" title="Excellent" <?php echo ($quiz->rating == 5) ? 'checked="checked"' : ''?>  />
	        	</div>
	        	<div class="rating-details"><?php echo sprintf(JText::_('LBL_RATING_NUM'), empty($quiz->rating) ? '0' : $quiz->rating);?></div>
	        </div>
	        <?php endif;?>
	        <div class="quiz-item-main">
		        <div class="quiz_title">
		            <a href="<?php echo $quiz_href; ?>"><?php echo $this->escape($quiz->title);?></a>
		            <?php if(isset($this->user_quizzes) && $this->user_quizzes == true):?>
		            <img 
		            	src="<?php echo $templateUrlPath.'/images/'.(($quiz->published==1)?'published.png':(($quiz->published==2)?'pending.png':'unpublished.png'))?>"
		            	title="<?php echo ($quiz->published==1)?JText::_('TXT_PUBLISHED'):(($quiz->published==2)?JText::_('TXT_PENDING'):JText::_('TXT_UNPUBLISHED'))?>"/>
		            <?php endif;?>
		            <?php if((($quiz->created_by == $user->id) && CAuthorization::authorise('quiz.edit')) || CAuthorization::authorise('quiz.manage')):?>
		            &nbsp;-&nbsp;<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=edit&id='.$quiz->id.':'.$quiz->alias.$itemid);?>"><?php echo JText::_('LBL_EDIT');?></a>
		            &nbsp;-&nbsp;<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=reports&id='.$quiz->id.':'.$quiz->alias.$itemid);?>"><?php echo JText::_('LBL_REPORTS');?></a>
		            <?php endif;;?>
		        </div>
		        <div class="quiz_meta">
		        	<?php echo sprintf(JText::_('LBL_QUIZ_META'), $category_href, $user_profile_link, $quiz_date)?>
		        </div>
		        <div class="quiz_meta">
		            <?php if(isset($quiz->responded_on)):?>
		            <?php echo JText::_('TXT_RESPONDED').' '.CommunityQuizHelper::getFormattedDate($quiz->responded_on);?>
		            <?php endif;?>
		        </div>
			</div>
	        <div class="clear"></div>
	    </li>
	    <?php
	    $i=1-$i;
	}
	?>
	</ul>
	<?php endif; //end popular list?>
</div>
<?php echo CommunityQuizHelper::loadModulePosition('quiz_list_bottom');?>

<span id="url_search" style="display: none;"><?php echo JRoute::_("index.php?option=".Q_APP_NAME."&view=quiz&task=ajxsearch".$itemid);?></span>
<span id="lbl_search" style="display: none;"><?php echo JText::_("LBL_SEARCH");?></span>