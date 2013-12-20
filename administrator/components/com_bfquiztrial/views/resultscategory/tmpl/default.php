<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5%">
		        <?php echo JText::_( 'ID' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Category' ); ?>
            </th>
        </tr>
    </thead>

    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
        $link 		= JRoute::_( 'index.php?option=com_bfquiztrial&controller=results&task=results&cid='. $row->id );
        $checked 	= JHTML::_('grid.id',   $i, $row->id );
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <a href="<?php echo $link; ?>"><?php echo $row->id; ?></a>
            </td>
            <td>
				<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			</td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>

<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="" />

</form>