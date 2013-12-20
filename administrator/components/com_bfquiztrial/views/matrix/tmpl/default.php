<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

    global $mainframe;
    $user =& JFactory::getUser();
    $lists = 0;
    $items = 0;
    $limitstart = 0;
    $limit = 0;
    $disabled = 0;

	//Ordering allowed ?
	$ordering = ($lists['order'] == 'a.ordering');

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $items, $limitstart, $limit );

    $myFields[]="";
?>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">

	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
			</th>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th>
				<?php echo JText::_( 'Description' ); ?>
			</th>
			<th width="5%">
				<?php echo JText::_( 'Default' ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
			    <?php echo JText::_( 'Category' ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
				<?php echo JText::_( 'Published' ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
				<?php echo JText::_( 'Order' ); ?>
				<?php
				echo JHTML::_('grid.order',  $this->items);
				?>
			</th>

		</tr>
	</thead>
	<?php
	$k = 0;

	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_bfquiztrial&controller=matrix&task=matrixanswer&cid[]='. $row->id );

        //$id = JHTML::_('grid.id', ++$i, $row->id);

		// show tick or cross
		$published		= JHTML::_('grid.published', $row, $i );

		$id = JHTML::_('grid.id',  $i, $row->id );
		$order = JHTML::_('grid.order',  $i, $row->id );

		?>

		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->description; ?></a>
			</td>
			<td align="center">
			<?php
				if ($row->default == 1) {
			?>
 					<img src="./components/com_bfquiztrial/images/icon-16-default.png" alt="<?php echo JText::_( 'Default' ); ?>" />
			<?php
				} else {
			?>
					&nbsp;
			<?php
			}
			?>
			</td>
			<td align="center">
				<?php echo $row->category_name;?>
			</td>
			<td align="center">
				<?php echo $published;?>
			</td>
            <td class="order">

			    <span><?php echo $this->pagination->orderUpIcon($i, true, 'orderupmatrix', 'Move Up', isset($this->items[$i-1]) ); ?></span>
			    <span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdownmatrix', 'Move Down', isset($this->items[$i+1]) ); ?></span>

				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>"<?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>

		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	<tfoot>
	    <tr>
	      <td colspan="9"><?php //echo $this->pagination->getListFooter(); ?></td>
	    </tr>
	  </tfoot>

	</table>
</div>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="task" value="matrix" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
<input type="hidden" name="controller" value="matrix" />
<input type="hidden" name="c" value="matrix" />
<input type='hidden' name='view' value='matrix' />
<?php echo JHTML::_( 'form.token' ); ?>

</form>
