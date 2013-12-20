<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<script language="javascript" type="text/javascript">

	function tableOrdering( order, dir, task )
	{
		var form = document.adminForm;

		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( task );
	}
</script>
<div style="clear:both;padding-top:10px;"></div>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
<div>
<?php if ($this->params->get('filter') || $this->params->get('show_pagination_limit')) : ?>
		<div>
		<?php if ($this->params->get('filter')) : ?>
			<div style="width:60%;float:left;text-align:left;">
				<?php echo JText::_($this->params->get('filter_type') . ' Filter').'&nbsp;'; ?>
				<input type="text" name="filter" value="<?php echo $this->escape($this->lists['filter']);?>" class="inputbox" onchange="document.adminForm.submit();" />
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div style="width:40%;float:left;">
			<?php
				echo '&nbsp;&nbsp;&nbsp;'.JText::_('Display Num').'&nbsp;';
				echo $this->pagination->getLimitBox();
			?>
			</div>
			<div style="clear:both;"></div>
		<?php endif; ?>
		</div>
<?php endif; ?>
<?php if ($this->params->get('show_headings')) : ?>
<div>
	<div class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" style="width:5%;float:left;">
		<?php echo JText::_('Num'); ?>
	</div>
	<?php if ($this->params->get('show_title')) : ?>
 	<div class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" style="width:25%;float:left;">
		<?php echo JHTML::_('grid.sort',  'Item Title', 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_date')) : ?>
	<div class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" style="width:25%;float:left;">
		<?php echo JHTML::_('grid.sort',  'Date', 'a.created', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_hits')) : ?>
	<div class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" style="width:8%;float:right;">
		<?php echo JHTML::_('grid.sort',  'Hits', 'a.hits', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_author')) : ?>
	<div class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"  style="width:12%;float:right;">
		<?php echo JHTML::_('grid.sort',  'Author', 'author', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</div>
	<?php endif; ?>
	
	<div style="clear:both;"></div>
</div>
<?php endif; ?>
<?php foreach ($this->items as $item) : ?>
<div class="sectiontableentry<?php echo ($item->odd +1 ) . $this->escape($this->params->get('pageclass_sfx')); ?>" >
	<div class="artnumber">
		<?php echo $this->pagination->getRowOffset( $item->count ); ?>
	</div>
	<?php if ($this->params->get('show_title')) : ?>
	<?php if ($item->access <= $this->user->get('aid', 0)) : ?>
	<div class="arttitle">
		<a href="<?php echo $item->link; ?>">
			<?php echo $this->escape($item->title); ?></a>
			<?php $this->item = $item; echo JHTML::_('icon.edit', $item, $this->params, $this->access) ?>
	</div>
	<?php else : ?>
	<div style="float:left;">
		<?php
			echo $this->escape($item->title).' : ';
			$link = JRoute::_('index.php?option=com_user&view=login');
			$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid), false);
			$fullURL = new JURI($link);
			$fullURL->setVar('return', base64_encode($returnURL));
			$link = $fullURL->toString();
		?>
		<a href="<?php echo $link; ?>">
			<?php echo JText::_( 'Register to read more...' ); ?></a>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	<?php if ($this->params->get('show_date')) : ?>
	<div class="artcreated">
		<?php echo $item->created; ?>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_hits')) : ?>
	<div class="arthits">
		<?php echo $this->escape($item->hits) ? $this->escape($item->hits) : '-'; ?>
	</div>
	<?php endif; ?>	
	<?php if ($this->params->get('show_author')) : ?>
	<div class="artauthor">
		<?php echo $this->escape($item->created_by_alias) ? $this->escape($item->created_by_alias) : $this->escape($item->author); ?>
	</div>
	<?php endif; ?>

	<div style="clear:both;"></div>
</div>
<?php endforeach; ?>
<?php if ($this->params->get('show_pagination')) : ?>

<div style="text-align:center;" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div>
		<?php echo $this->pagination->getPagesCounter(); ?>
</div>
<?php endif; ?>
</div>

<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="sectionid" value="<?php echo $this->category->sectionid; ?>" />
<input type="hidden" name="task" value="<?php echo $this->lists['task']; ?>" />
<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="limitstart" value="0" />
<input type="hidden" name="viewcache" value="0" />
</form>
<br/><br/>