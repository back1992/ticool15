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

	$context="";
	$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'cc.title',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
	$filter_catid		= $mainframe->getUserStateFromRequest( $context.'filter_catid',		'filter_catid',		'',			'int' );
	$filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',			'word' );

	$lists = array();

	// build list of categories
	$javascript		= 'onchange="document.adminForm.submit();"';
	$lists['catid'] = JHTML::_('list.category',  'filter_catid', 'com_bfquiztrial', (int) $filter_catid, $javascript );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
?>

<form action="index.php" method="post" name="adminForm">
<table>
<tr>
	<td nowrap="nowrap">
		<?php
		echo $lists['catid'];
		?>
	</td>
</tr>
</table>
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
				<?php echo JText::_( 'Question' ); ?>
			</th>
			<th width="5%">
				<?php echo JText::_( 'Type' ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
			    <?php echo JText::_( 'Category' ); ?>
			</th>
			<th width="5%">
				<?php echo JText::_( 'Next Qn' ); ?>
			</th>
			<th width="5%">
				<?php echo JText::_( 'DB Field Name' ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
				<?php echo JText::_( 'Published' ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
				<?php echo JText::_( 'Order' ); ?>
				<?php
				echo JHTML::_('grid.order',  $this->items );
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
		$link 		= JRoute::_( 'index.php?option=com_bfquiztrial&controller=question&task=edit&cid[]='. $row->id );

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
				<a href="<?php echo $link; ?>"><?php echo $row->question; ?></a>
			</td>
			<td>
				<?php echo bfquiztrialHelper::ShowQuestionType( $row->question_type ); ?>
			</td>
			<td align="center">
				<?php echo $row->category_name;?>
			</td>
			<td align="center">
				<?php
				    for($z=1; $z < 20; $z++){
				       $tempname = "next_question".$z;
				       if($row->$tempname <> 0){
				          echo $row->$tempname;
				          echo " ";
				       }
				    }
				?>
			</td>
			<td>
				<?php echo $row->field_name; ?>
				<?php if(!isset($myFields[$row->catid])){
				   $myFields[$row->catid] = "";
				}
				?>
				<?php $myFields[$row->catid].= "`".$row->field_name."` varchar($row->fieldSize) default NULL,";	?>
			</td>
			<td align="center">
				<?php echo $published;?>
			</td>
            <td class="order">
			    <span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', isset($this->items[$i-1]) ); ?></span>
			    <span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown', 'Move Down', isset($this->items[$i+1]) ); ?></span>

				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>

		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	<tfoot>
	    <tr>
	      <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
	    </tr>
	  </tfoot>

	</table>
</div>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />

<?php
// insert a hidden token to the form field
echo JHTML::_('form.token');
?>


</form>

<?php

//Automatic Database Table Builder

global $mainframe;
$database =& JFactory::getDBO();

$mycategory =& bfquiztrialController::getCategory();

$db =& JFactory::getDBO();

if( sizeof( $mycategory ) ) {
    foreach( $mycategory as $mycat  ) {
	    $myid = $mycat->id;
	    $table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$myid;

	    $result = $database->getTableList();
	    if (!in_array($mainframe->getCfg('dbprefix')."bfquiztrial_".$myid, $result)) {
	       //Table does not exist

           $myFieldsMissing="";
		   for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		   {
		    	$found=0;
		   		$row = &$this->items[$i];

				if($row->catid == $myid){
				   if($row->question_type == 9){ //rating
			        	// don't add field for rating question
			       }else{
	 			      $myFieldsMissing.= "`".$row->field_name."` varchar($row->fieldSize) default NULL,";
	 			   }
				}
       		}


	       $query="CREATE TABLE `".$table."` (
  			    `id` int(11) NOT NULL auto_increment,
      			`Name` varchar(150) default NULL,
      			`Email` varchar(150) default NULL,
      			`uid` int(11) NOT NULL default 0,
      			`DateReceived` datetime NOT NULL,
      			`ip` varchar(50) default NULL,
      			`score` int(11) NOT NULL default 0,
      			`matrixid` int(11) NOT NULL default 0,
      			`answerseq` varchar(255) default NULL,
      			`DateCompleted` datetime NOT NULL,
  			    ".$myFieldsMissing."
      			PRIMARY KEY  (`id`)
	    	);";

	       $db->setQuery( $query );
	       if (!$db->query())
	       {
	       	   echo $db->getErrorMsg();
	    	   return false;
	       }
	       //Finished Table creation


	    }else{
	       //Table already exists

		    $db =& JFactory::getDBO();
		    // Grab the fields for the selected table
		    $fields =& $db->getTableFields( $table, true );
		    if( sizeof( $fields[$table] ) ) {
		       // We found some fields so let's create the HTML list
		       $options = array();
		       foreach( $fields[$table] as $field => $type ) {
			           $options[] = JHTML::_( 'select.option', $field, $field );
		       }
		    }

			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	 		{
 		    	$found=0;
			    $row = &$this->items[$i];
			    foreach( $fields[$table] as $field => $type ) {

		           if ($row->field_name == $field) {
			          $found=1;
			   	   }
			    }

			    if($row->question_type == 9){ //rating
			        // don't add field for rating question
			        $found=1;
			    }

	 		    if($found == 0 & $row->catid == $myid){
			      $query="ALTER TABLE `".$table."`
		    			    ADD `".$row->field_name."` VARCHAR( $row->fieldSize ) NOT NULL
  	  	    	  ;";

		  	       $db->setQuery( $query );
		  	       if (!$db->query())
		  	       {
		  	       	   echo $db->getErrorMsg();
		  	    	   return false;
		  	       }
			    }

			    // now special case for rating question
			    if($row->question_type == 9){   // rating question
			        for ($y=0, $x=20; $y < $x; $y++ ) {
			            $tempvalue="option".($y+1);
			            $tempfield=$row->field_name;
			            $tempfield.="".($y+1);

						$found=0;

			            if($row->$tempvalue == ""){
			                 //do nothing
			            }else{
			    			foreach( $fields[$table] as $field => $type ) {
				       			if ($tempfield == $field) {
					       		   	$found=1;
				  	   			}
				  			}

							if($found == 0 & $row->catid == $myid){
						       $query="ALTER TABLE `".$table."`
					    			   ADD `".$tempfield."` VARCHAR( $row->fieldSize ) NOT NULL
			  	  	    	   ;";

					  	       $db->setQuery( $query );
					  	       if (!$db->query())
					  	       {
					  	       	   echo $db->getErrorMsg();
					  	    	   return false;
					  	       }
						    }

				  		}

			    	}

			    }

				// now check if new field exists
			    $found=0;
			    foreach( $fields[$table] as $field => $type ) {
				    if ($field == "uid") {
		       		   	$found=1;
	  	   			}
	  			}
				if($found == 0){
					$query="ALTER TABLE `".$table."`
		    			   ADD `uid` int(11) NOT NULL default 0,
						   ADD `score` int(11) NOT NULL default 0,
						   ADD `matrixid` int(11) NOT NULL default 0,
      					   ADD `answerseq` varchar(255) default NULL
  	  	    	   ;";

		  	       $db->setQuery( $query );
		  	       if (!$db->query())
		  	       {
		  	       	   echo $db->getErrorMsg();
		  	    	   return false;
		  	       }
			    }
			}

        }
   }

}

?>