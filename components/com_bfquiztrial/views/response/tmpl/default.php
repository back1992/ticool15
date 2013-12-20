<?php defined('_JEXEC') or die('Restricted access'); ?>

    <table width="100%">
    <tr>
    	<td>
    	<?php
    	   $row =& $this->items[0];
    	   echo JText::_( 'Score = ' );
    	   echo $row->score;
    	?>
    	</td>
    </tr>
    </table>

    <table width="100%">
    <thead>
        <tr>
            <th width="50%">
                <?php echo JText::_( 'Question' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Answer' ); ?>
            </th>
        </tr>
    </thead>
    <?php


    $k = 0;
    for ($i=0, $n=count( $this->items2 ); $i < $n; $i++)
    {
        $row =& $this->items[0];
        $row2 =& $this->items2[$i];

        if(isset($row2->question)){

        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
			    <?php echo $row2->question; ?>

            </td>
			<td>
			    <?php

			    if(!isset($row2->field_name)){
			       $row2->field_name = "";
			    }

				if(!isset($row2->question_type)){
				   $row2->question_type = "";
				}

			    $tempvalue = $row2->field_name;

			    if(!isset($tempvalue)){
			       $tempvalue = "";
			    }


			    if(isset($row->$tempvalue)){
			      echo $row->$tempvalue;
			    }
			    ?>

			</td>
        </tr>
        <?php
        }

        $k = 1 - $k;
    }
    ?>
    </table>
