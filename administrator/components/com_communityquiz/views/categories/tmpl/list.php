<?php
defined('_JEXEC') or die('Restricted access');
?>
<form name="adminForm" id="adminForm" action="index.php?option=<?php echo Q_APP_NAME;?>" method="post">
	<?php
	if (count($this->categories) > 1){
	?>
	<table align="left" class="adminlist">
	    <thead>
	        <tr>
	        	<th width="20px">#</th>
	            <th><?php echo JText::_('LBL_CATEGORY');?></th>
	            <th width="50px"><?php echo JText::_('LBL_QUIZZES');?></th>
	            <th width="25px"></th>
	            <th width="25px"></th>
	            <th width="50px"><?php echo JText::_('LBL_EDIT');?></th>
	            <th width="50px"><?php echo JText::_('LBL_DELETE');?></th>
	            <th width="50px">ID</th>
	        </tr>
	    </thead>
	    <tbody>
	    <?php 
	    $i=0;
	    foreach ($this->categories as $category){
		if($category->parent_id > 0){
        ?>
		<tr class="row<?php echo $i%2;?>">
        	<td align="center"><?php echo $i+1;?></td>
            <td>
            	<?php echo str_repeat('.', ($category->nlevel-1) * 4)?><a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=categories&task=edit&id='.$category->id);?>"><?php echo CommunityQuizHelper::escape($category->title);?></a>
            </td>
            <td align="center"><a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=list&catid='.$category->id);?>"><?php echo $category->quizzes?></a></td>
            <td align="center">
            	<?php if($this->categories[$i]->nlevel <= $this->categories[$i+1]->nlevel):?>
            	<?php
            	$flag = true;
            	if($this->categories[$i]->nlevel != $this->categories[$i+1]->nlevel){
            		$flag = false;
	            	for($j=$i+1; $j<count($this->categories); $j++){
    	        		if(($this->categories[$j]->nlevel == $category->nlevel) && ($this->categories[$j]->parent_id == $category->parent_id)){
    	        			$flag = true;
    	        			break;
    	        		}
        	    	}
            	}
            	?>
            	<?php if($flag):?>
            	<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=categories&task=movedown&id='.$category->id);?>">
            		<img src="components/<?php echo Q_APP_NAME;?>/assets/images/move_down.png" border="0" title="<?php echo JText::_( 'TXT_MOVE_DOWN' ); ?>" alt="<?php echo JText::_( 'TXT_MOVE_DOWN' ); ?>" />
            	</a>
            	<?php endif;?>
            	<?php endif;?>
            </td>
            <td align="center">
            	<?php if($category->norder > 1):?>
            	<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=categories&task=moveup&id='.$category->id);?>">
            		<img src="components/<?php echo Q_APP_NAME;?>/assets/images/move_up.png" border="0" title="<?php echo JText::_( 'TXT_MOVE_UP' ); ?>" alt="<?php echo JText::_( 'TXT_MOVE_UP' ); ?>" />
            	</a>
            	<?php endif;?>
            </td>
            <td align="center">
            	<?php if($category->parent_id):?>
            	<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=categories&task=edit&id='.$category->id);?>"><?php echo JText::_('LBL_EDIT');?></a>
            	<?php endif;?>
            </td>
            <td align="center">
            	<?php if($category->parent_id):?>
            	<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=categories&task=delete&id='.$category->id);?>"><?php echo JText::_('LBL_DELETE');?></a>
            	<?php endif;?>
            </td>
            <td align="center">
            	<?php echo $category->parent_id ? $category->id : '';?>
            </td>
        </tr>
        <?php
        $i++;
		}
	    }
	    ?>
	    </tbody>
	</table>
	<?php
	}else{
	    echo 'No categories found';
	}
	?>
    <input type="hidden" name="view" value="categories">
    <input type="hidden" name="task" value="add">
</form>
