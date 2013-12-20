<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
if($this->uid == 0){
   echo JText::_("您必须登录后才有权查看");
}else{
?>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table width="100%">
    <thead>
        <tr class="bfquiztrialQuestion">
            <th align="center">
                <?php echo JText::_( 'Date' ); ?>
            </th>
            <th align="center">
                <?php echo JText::_( 'Quiz Name' ); ?>
            </th>
            <th align="center">
				<?php echo JText::_( 'Result' ); ?>
            </th>
            <th align="center">
				<?php echo JText::_( 'Time Taken' ); ?>
            </th>
        </tr>
    </thead>
    <?php
    $k = 0;
    $catid	= JRequest::getVar( 'cid', 0, '', 'int' );

    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
        ?>
        <tr class="bfquiztrialOptions">
            <td align="center">
                <?php echo $row->DateReceived; ?>
            </td>
            <td align="center">
			    <?php echo $row->title; ?>
            </td>
            <td align="center">
				<?php echo $row->score; ?>
            </td>
            <td align="center">
				<?php echo $row->TimeTaken; ?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>


<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="task" value="response" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="catid" value="<?php echo $catid ?>" />
</form>

<?php
}
?>