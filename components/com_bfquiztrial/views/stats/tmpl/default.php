<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

if($this->catid != 0){  // make sure category is selected

$config =& JComponentHelper::getParams( 'com_bfquiztrial' );
$scoringMethod = $config->get( 'scoringMethod' );

?>

<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th>
                <?php echo JText::_( 'Overall Statistics' ); ?>
            </th>
        </tr>
    </thead>

    <tr class="<?php echo "row$k"; ?>">
    <td>
    <?php
       echo JText::_( '到目前, ' );
       echo $this->totalResponses;
       echo JText::_(' 参加此次测验的用户');
    ?>
    </td>
    </tr>

    <?php if($scoringMethod == 1){
       // do nothing.
    }else{
    ?>

    <tr class="<?php echo "row$k"; ?>">
    <td>
    <?php
       echo JText::_( '满分为 ' );
       echo $this->maxScore;
    ?>
    </td>
    </tr>
	</tr>
    <tr class="<?php echo "row$k"; ?>">
    <td>
    <?php
       echo JText::_( '平均分为 ' );
       echo $this->averageScore;
       echo JText::_( ' 出自 ' );
       echo $this->maxScore;
       echo JText::_( ' 或 ' );
       echo round((($this->averageScore/$this->maxScore)*100),2);
       echo JText::_( '%' );
    ?>
    </td>
    </tr>
    <tr class="<?php echo "row$k"; ?>">
    <td>
    <?php
       echo JText::_( '最高分为 ' );
       echo $this->highScore;
    ?>
    </td>
    </tr>
    </tr>
    <tr class="<?php echo "row$k"; ?>">
    <td>
    <?php
       echo JText::_( '最低分为 ' );
       echo $this->lowScore;
    ?>
    </td>
    </tr>

    <?php
    } // end ABCD
    ?>

	</table>
</div>
<br>



<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'ID' ); ?>
            </th>
            <th>
                <?php echo JText::_( '题目' ); ?>
            </th>
        </tr>
    </thead>

    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items2 ); $i < $n; $i++)
    {
		$row =& $this->items2[$i];


        ?>
  		<tr class="<?php echo "row$k"; ?>">
  		    <td>
  		       <?php echo $row->id; ?>
  		    </td>
  		    <td>
  		       <?php echo $row->question; ?>
  		    </td>
  		</tr>

  		<tr>
  		<td colspan=2>
		<?php
		$question_type = $row->question_type;
		$total = 0;
		$myOption = array();
		$myAnswer = array();
		?>
  		Question type = <?php

  		echo bfquiztrialHelper::ShowQuestionType( $row->question_type );

		for ($i3=1; $i3 < 21; $i3++)
		{
			$fieldName=$row->field_name;
			$name = 'option'.$i3;
			$response=$row->$name;

			if($response==""){
			    // do nothing
			}else{
				$answer = bfquiztrialController::getStats($fieldName, $response, $this->catid);
				$myAnswer[$i3]=$answer;
				$myOption[$i3]=$response;

				if($answer > 0){
			       $total=$total + $answer;
		    	}
			}
		}

		if($question_type == 0){ //text
			$fieldName=$row->field_name;
			$response=$row->option1;
			$myAnswer[2]=0;

			for($x=0; $x < count($this->items3); $x++){
				$row2 =& $this->items3[$x];

				$tempanswer=$row2->$fieldName;

				if(strtoupper($tempanswer) == strtoupper($response)){
				   // do nothing
				}else if($tempanswer==""){
				   // do nothing
				}else{
					$myOption[2]='Incorrect';
					$myAnswer[2]=$myAnswer[2]+1;

					if($answer > 0){
					   $total=$total + 1;
		    	    }
				}
			}
		}

		?>

		<table>
		<tr>
		   <th width="400">Option</th>
		   <th width="50">Count</th>
		   <th width="100">Percent</th>
		   <th width="100">Graph</th>
		</tr>
		<?php
			$colourCount=0;
			for ($z=1; $z < count($myOption)+1; $z++){
			    $colourCount++;
			    if($colourCount > 5){
			       $colourCount = 1;
			    }
			    echo "<tr>";
			    echo "<td>".$myOption[$z]."</td>";
			    echo "<td>".$myAnswer[$z]."</td>";
			    if($total > 0){
			       echo "<td>".number_format((($myAnswer[$z]/$total)*100),2)."%</td>";
				}else{
				   echo "<td>0.00%</td>";
				}


			    $myclass = 'polls_color_'.$colourCount;
			?>
			    <td width=300 >
			    <?php
			    if($total > 0){
			    ?>
		           <div class="<?php echo $myclass ?>" style="height:5px;width:<?php echo (($myAnswer[$z]/$total)*100) ?>%"></div>
		        <?php
		        }else{
		        ?>
		           <div class="<?php echo $myclass ?>" style="height:5px;width:1%"></div>
		        <?php
		        }
		        ?>
		        </td>
			    </tr>
			<?php

			}

		?>
		</table>
		<?php
		echo JText::_( 'Total: ');
		echo $total;
		?>
        </td>
        </tr>

        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>

<?php

}else{

   echo "您必须在参数中选择目录.";

}

?>