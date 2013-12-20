<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
if($this->catid != 0){  // make sure category is selected
?>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table width="100%">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'ID' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Name' ); ?>
            </th>
            <th>
				<?php echo JText::_( 'Email' ); ?>
            </th>
            <th>
				<?php echo JText::_( 'UID' ); ?>
            </th>
            <th>
				<?php echo JText::_( 'Date' ); ?>
            </th>
            <th>
				<?php echo JText::_( 'Score' ); ?>
            </th>
        </tr>
    </thead>
    <?php
    $k = 0;
    $catid	= JRequest::getVar( 'cid', 0, '', 'int' );

    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
        $link 		= JRoute::_( 'index.php?option=com_bfquiztrial&view=response&cid='. $row->id.'&catid='. $this->catid.'&'.JUtility::getToken().'=1');
        $checked 	= JHTML::_('grid.id',   $i, $row->id );
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <a href="<?php echo $link; ?>"><?php echo $row->id; ?></a>
            </td>
            <td>
                <a href="<?php echo $link; ?>">
                <?php
                   if(!isset($row->Name )){
                      $row->Name  = "";
                   }

                   if($row->Name == ""){
                      echo "< blank >";
                   }else{
                      echo $row->Name;
                   }
                ?>
                </a>
            </td>
            <td>
				<?php
                   if(!isset($row->Email)){
                      $row->Email = "";
                   }
                ?>
			    <?php echo $row->Email; ?>
            </td>
            <td align="center">
				<?php
                   if(!isset($row->uid)){
                      $row->uid = "";
                   }
                ?>
			    <?php echo $row->uid; ?>
            </td>
            <td>
				<?php
                   if(!isset($row->DateReceived)){
                      $row->DateReceived = "";
                   }
                ?>
			    <?php echo $row->DateReceived; ?>
            </td>
            <td align="right">
				<?php
                   if(!isset($row->score)){
                      $row->score = "";
                   }
                ?>
			    <?php echo $row->score; ?>
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

}else{

   echo JText::_( '<br>You must select a category in Parameters Basic');

}

?>