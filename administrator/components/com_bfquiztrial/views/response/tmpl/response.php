<?php defined('_JEXEC') or die('Restricted access'); ?>


<form action="index.php" method="post" name="adminForm">
<div id="editcell">

    <table class="adminlist">
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

    <table class="adminlist">
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
</div>

<input type="hidden" name="option" value="com_bfquiztrial" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="results" />

</form>

<br>
<a href="http://www.tamlyncreative.com.au/software/" target="_blank"><img src="./components/com_bfquiztrial/images/bflogo.jpg" width="125" height="42" align="right" border="0"></a>