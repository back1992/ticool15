<?php defined('_JEXEC') or die('Restricted access'); ?>
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

    $catid = $this->catid;

    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
        $link 		= JRoute::_( 'index.php?option=com_bfquiztrial&controller=response&cid='. $row->id.'&catid='. $catid );
        $checked 	= JHTML::_('grid.id',   $i, $row->id );
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <a href="<?php echo $link; ?>"><?php echo $row->id; ?></a>
            </td>
            <td>
				<?php echo $checked; ?>
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
            <td>
				<?php
                   if(!isset($row->uid)){
                      $row->uid = "";
                   }
                ?>
			    <?php echo $row->uid; ?>
            </td>
            <td>
			    <?php
			        $user =& JFactory::getUser();
			        $dateReceived = JFactory::getDate($row->DateReceived);
			        $dateReceived->setOffset($user->getParam('timezone'));
			        echo $dateReceived->toFormat();
			    ?>
			</td>
            <td>
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

  	<tfoot>
    <tr>
      <td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  	</tfoot>

    </table>
</div>


<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="results" />
<input type="hidden" name="catid" value="<?php echo $catid ?>" />

</form>