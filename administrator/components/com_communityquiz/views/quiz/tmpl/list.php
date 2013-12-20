<?php
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
    function submitbutton(pressbutton) {
        if(pressbutton == 'remove'){
            if(!confirm('<?php echo JText::_("MSG_DELETE_CONFIRM");?>')){
                return false;
            }
        }
        document.adminForm.task.value=pressbutton;
        submitform(pressbutton);
    }
</script>
<form id="adminForm" action="index.php?option=<?php echo Q_APP_NAME;?>&view=quiz" method="post" name="adminForm">
<table>
    <tr>
        <td align="left" nowrap="nowrap">
            <?php echo JText::_( 'Filter' ); ?>:
            <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
            <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
            <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
        </td>
        <td width="100%"></td>
        <td>
        	<select name="status" id="status" onchange="javascript: document.adminForm.submit();">
        		<option value="0" <?php echo ($this->lists['status'] == '0' ? 'selected="selected"':'');?>><?php echo JText::_('LBL_STATUS_ALL');?></option>
        		<option value="1" <?php echo ($this->lists['status'] == '1' ? 'selected="selected"':'');?>><?php echo JText::_('LBL_STATUS_PUBLISHED');?></option>
        		<option value="2" <?php echo ($this->lists['status'] == '2' ? 'selected="selected"':'');?>><?php echo JText::_('LBL_STATUS_UNPUBLISHED');?></option>
        		<option value="3" <?php echo ($this->lists['status'] == '3' ? 'selected="selected"':'');?>><?php echo JText::_('LBL_STATUS_PENDING');?></option>
        	</select>
		</td>
    </tr>
</table>
<?php 
if($this->quizzes){
?>
<table class="adminlist">
    <thead>
        <tr>
            <th width="20"><?php echo JText::_( '#' ); ?></th>
            <th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->quizzes ); ?>);" /></th>
            <th class="title"><?php echo JHTML::_( 'grid.sort', JText::_( 'LBL_TITLE' ), 'a.title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            <th width="10%" class="title"><?php echo JHTML::_( 'grid.sort', JText::_( 'LBL_CATEGORY' ), 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            <th width="10%" class="title"><?php echo JHTML::_( 'grid.sort', JText::_( 'LBL_USERNAME' ), 'a.created_by', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            <th width="8%" class="title"><?php echo JHTML::_( 'grid.sort', JText::_( 'LBL_CREATED' ), 'a.created', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            <th width="5%" class="title"><?php echo JHTML::_( 'grid.sort', JText::_( 'LBL_RESPONSES' ), 'a.responses', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            <th width="5%" class="title"><?php echo JHTML::_( 'grid.sort', JText::_( 'LBL_PUBLISHED' ), 'a.published', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            <th width="20"><?php echo JText::_( 'ID' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $k = 0;
    $i=0;
    foreach($this->quizzes as $row){
    $checked    = JHTML::_( 'grid.id', $i, $row->id );
    ?>
    <tr class="<?php echo "row$k"; ?>">
        <td>
            <?php echo $this->pagination->getRowOffset( $i ); ?>
        </td>
        <td>
            <?php echo $checked; ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task=details&id='.$row->id);?>"><?php echo $this->escape($row->title); ?></a>
        </td>
        <td>
            <?php echo $this->escape($row->category); ?>
        </td>
        <td title="<?php echo $this->escape($row->name);?>">
            <?php echo $this->escape($row->username); ?>
        </td>
        <td>
            <?php echo $this->escape($row->created); ?>
        </td>
        <td align="center">
        	<?php echo $this->escape($row->responses); ?>
        </td>
        <td align="center">
        	<?php $status = (($row->published == '1') ? 'unpublish':'publish');?>
        	<a href="<?php echo JRoute::_('index.php?option='.Q_APP_NAME.'&view=quiz&task='.$status.'&cid[]='.$row->id);?>">
        		<img src="<?php echo JURI::base(true).'/components/'.Q_APP_NAME.'/assets/images/'.(($row->published == '1') ? 'published.png' : (($row->published == 2) ? 'pending.png' : 'unpublished.png'))?>">
        	</a>
        </td>
        <td>
            <?php echo $row->id; ?>
        </td>
    </tr>
    <?php
    $k = 1 - $k;
    $i++;
}
?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
    </tfoot>
</table>
<?php
}else{
    echo JText::_('TXT_NO_RESULTS');
}
?>
<input type="hidden" name="option" value="<?php echo Q_APP_NAME;?>" />
<input type="hidden" name="task" value="list" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="Itemid" value="0" />
<input type="hidden" name="filter_order" value="<?php if($this->lists['order']) echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php if($this->lists['order_Dir']) echo $this->lists['order_Dir']; ?>" />
</form>