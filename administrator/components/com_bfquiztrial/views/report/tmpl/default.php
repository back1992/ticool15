<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

$rowX =& $this->items2[0];
$row2X =& $this->items3[0];



$header=""; // excel export
$data="";

$Submit	= JRequest::getVar( 'Submit' );
$condition1_1	= JRequest::getVar( 'condition1_1' );
$condition1_2	= JRequest::getVar( 'condition1_2' );
$condition1_3	= JRequest::getVar( 'condition1_3' );
$operation_1	= JRequest::getVar( 'operation_1' );
$condition2_1	= JRequest::getVar( 'condition2_1' );
$condition2_2	= JRequest::getVar( 'condition2_2' );
$condition2_3	= JRequest::getVar( 'condition2_3' );
$input	= JRequest::getVar( 'input' );
$input = trim($input);

$input2	= explode(", ", $input);

$datefrom	= JRequest::getVar( 'datefrom' );
$dateto	= JRequest::getVar( 'dateto' );
$daterange	= JRequest::getVar( 'daterange' );

?>

<?php

// Tim to change to Joomla date command
if($daterange=="30"){
   // last 30 days
   $dateto = date("Y-m-d");
   $datefrom = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y") ));
}

if($daterange=="120"){
   // last 120 days
   $dateto = date("Y-m-d");
   $datefrom = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-120, date("Y") ));
}

   $myFields="";

   $catid	= JRequest::getVar( 'cid', 0, '', 'int' );

   $db =& JFactory::getDBO();
   $table=$mainframe->getCfg('dbprefix')."bfquiztrial_".$catid;

   // Grab the fields for the selected table
   $fields =& $db->getTableFields( $table, true );
   if(!$fields){
      JError::raiseWarning( 500, 'No answer table exists!' );
	 $mainframe->redirect( 'index.php?option='. $option );
   }
   if( sizeof( $fields[$table] ) ) {
      // We found some fields so let's create the list
      $options = array();
      foreach( $fields[$table] as $field => $type ) {
          $options[] = $field;
          if($myFields == ""){ // first one
             $myFields = "'".$field."'";
          }else{
             $myFields .= ",'".$field."'";
          }
      }
   }

   if($input==""){
      $input2=$options;
   }

?>

<script language=javascript>

//need to populate this dynamically
var from_array = new Array(<?php echo $myFields ?>);
var to_array = new Array(); 		  // this array has the values for the destination list(if any)
</script>

<script language=javascript>
function moveoutid()
{
	var sda = document.getElementById('xxx');;
	var len = sda.length;
	var sda1 = document.getElementById('yyy');

	for(var j=0; j<len; j++)
	{
	    if(sda[j].selected)
		{
			var tmp = sda.options[j].text;
			var tmp1 = sda.options[j].value;
			sda.remove(j);
			j--;
			len--;
			var y=document.createElement('option');
			y.text=tmp;
			y.value=tmp1;
			try
			{sda1.add(y,null);
			}
			catch(ex)
			{
			sda1.add(y);
			}
		}

	}

}

function getFields(){
	var input = document.getElementById("input");
	var sda1 = document.getElementById('yyy');
	var len = sda1.length;
	var temp = '';

	for(var j=0; j<len; j++)
	{
		if(j==0){
		   temp=sda1.options[j].value;
		}else{
		   temp=temp+", "+sda1.options[j].value;
		}
	}

	input.setAttribute("value", temp);
}


function moveinid()
{
	var sda = document.getElementById('xxx');
	var sda1 = document.getElementById('yyy');
	var len = sda1.length;
	for(var j=0; j<len; j++)
	{
		if(sda1[j].selected)
		{
			var tmp = sda1.options[j].text;
			var tmp1 = sda1.options[j].value;
			sda1.remove(j);
			j--;
			len--;
			var y=document.createElement('option');
			y.text=tmp;
 			y.value=tmp1;
			try
			{
			sda.add(y,null);}
			catch(ex){
			sda.add(y);
			}

		}
	}
}
</script>

<script language="Javascript">
<!--

function ShowHideCustomDate(){

   var mydaterange = ""
   len = document.Report1.daterange.length

   for (i = 0; i <len; i++) {
      if (document.Report1.daterange[i].checked) {
         mydaterange = document.Report1.daterange[i].value
      }
   }

   if(mydaterange == "Custom"){
      // show date
      document.getElementById("CustomDate").style.display = '';
   }else{
      // hide date
      document.getElementById("CustomDate").style.display = 'none';
   }
}

//-->
</script>

<?php

if($Submit==null){ // if clicked on Submit button.

?>

<fieldset><legend><?php echo JText::_( 'Dynamic Report' ); ?></legend>

   <form id="Report1" name="Report1" method="POST" onsubmit="getFields()">

<table>
<tr>
<td>

<center>
<?php echo JText::_( 'PLEASE SELECT FIELDS YOU WOULD LIKE TO INCLUDE IN YOUR REPORT.' ); ?>
</center>
<table border=0 align=center valign=center>
<tr><td><?php echo JText::_( 'Available Fields' ); ?></td><td></td><td><?php echo JText::_( 'To use in Report' ); ?></td></tr>
<tr><td>
<select id=xxx name="xxx" multiple size=10 style="width:150px">
<script language=javascript>
for(var i=0;i<from_array.length;i++)
{
	document.write('<option value='+from_array[i]+'>'+from_array[i]+'</option>');
}
</script>
</select>
</td>
<td>
<input type=button value=">>" onclick=moveoutid()>
<input type=button value="<<" onclick=moveinid()>
</td>
<td>
<select name="yyy" id=yyy multiple size=10 style="width:150px">
<script language=javascript>
for(var j=0;j<to_array.length;j++)
{
	document.write('<option value='+to_array[j]+'>'+to_array[j]+'</option>');
}
</script>

</select>

</td></tr>
</table>

</td>
<td width="100">&nbsp;</td>
<td>

   <?php echo JText::_( 'Date range for report' ); ?><br>
   <input type="radio" name="daterange" value="All" checked onclick="ShowHideCustomDate()"><?php echo JText::_( 'All available' ); ?><br>
   <input type="radio" name="daterange" value="30" onclick="ShowHideCustomDate()"><?php echo JText::_( 'Last 30 days' ); ?><br>
   <input type="radio" name="daterange" value="120" onclick="ShowHideCustomDate()"><?php echo JText::_( 'Last 120 days' ); ?><br>
   <input type="radio" name="daterange" value="Custom" onclick="ShowHideCustomDate()"><?php echo JText::_( 'Custom date range' ); ?><br>

<?php JHTML::_('behavior.calendar'); ?>

   <DIV ID="CustomDate" style="display:none;">
   <table>
   <tr>
   <td>

   <?php

   echo '<input class="inputbox" type="text" id="datefrom" name="datefrom" size="10" maxlength="25" value="" />';
   echo '<input type="reset" class="button" value="..." onclick="return showCalendar(\'datefrom\',\'%Y-%m-%d\');" />';

   ?>

   </td>
   <td><?php echo JText::_( 'to' ); ?></td>
   <td>

   <?php

      echo '<input class="inputbox" type="text" id="dateto" name="dateto" size="10" maxlength="25" value="" />';
      echo '<input type="reset" class="button" value="..." onclick="return showCalendar(\'dateto\',\'%Y-%m-%d\');" />';

   ?>
   </td>
   </tr>
   </table>
   </div>

</td>
</tr>
</table>

<br>
<?php echo JText::_( 'Please select the criteria for the report.' ); ?><br>
<input name='input' id='input' type="hidden">

   <?php echo JText::_( 'condition' ); ?> 1<br>
   <select name="condition1_1">

   <option value="0"><?php echo JText::_( 'PLEASE SELECT' ); ?></option>
   <?php
   if( sizeof( $options ) ) {
      // We found some fields so let's create the list
      foreach( $options as $field) {
         echo '<option value="'.$field.'">'.$field.'</option>';
      }
   }
   print '</select>';

   ?>
   <select name="condition1_2">
   <option value="0"><?php echo JText::_( 'PLEASE SELECT' ); ?></option>
   <option value="1"><?php echo JText::_( 'is less than' ); ?></option>
   <option value="2"><?php echo JText::_( 'is equal to' ); ?></option>
   <option value="3"><?php echo JText::_( 'is greater than' ); ?></option>
   <option value="4"><?php echo JText::_( 'is NOT equal to' ); ?></option>
   </select>

   <?php

   print '<input name="condition1_3"><br><br>';
   ?>

   <?php echo JText::_( 'operator' ); ?>
   <br>
   <?php
   print "<select name=\"operation_1\">";
   ?>
   <option value="0"><?php echo JText::_( 'PLEASE SELECT' ); ?></option>
   <option value="AND"><?php echo JText::_( 'AND' ); ?></option>
   <option value="OR"><?php echo JText::_( 'OR' ); ?></option>
   </select><br><br>

   <?php echo JText::_( 'condition' ); ?> 2<br>
   <select name="condition2_1">

   <option value="0"><?php echo JText::_( 'PLEASE SELECT' ); ?></option>
   <?php
   if( sizeof( $options ) ) {
      // We found some fields so let's create the list
      foreach( $options as $field) {
         echo '<option value="'.$field.'">'.$field.'</option>';
      }
   }
   print '</select>';

   ?>

   <select name="condition2_2">
   <option value="0"><?php echo JText::_( 'PLEASE SELECT' ); ?></option>
   <option value="1"><?php echo JText::_( 'is less than' ); ?></option>
   <option value="2"><?php echo JText::_( 'is equal to' ); ?></option>
   <option value="3"><?php echo JText::_( 'is greater than' ); ?></option>
   <option value="4"><?php echo JText::_( 'is NOT equal to' ); ?></option>
   </select>

   <?php

   print '<input name="condition2_3"><br><br>';


?>



   <input name="Submit" type="submit" id="Submit" value="<?php echo JText::_( 'Submit' ); ?>" />

<?php

   print "</form>";

   print '</fieldset>';


}else{

  if($input == ""){
      $input=ereg_replace("'", "", $myFields);
  }

  $sql='Select '.$input.' FROM '.$table.' ';

  if($condition1_1=="0"){

     if($daterange!="All"){
     	 //$datefrom2=str_to_date('".$datefrom."','%Y-%m-%d');
         $sql.=" where date(DateReceived)>=str_to_date('".$datefrom."','%Y-%m-%d') and date(DateReceived)<=str_to_date('".$dateto."','%Y-%m-%d')    order by id;";
         //$sql.=" where DateReceived>=".$datefrom2." and DateReceived<=str_to_date('".$dateto."','%Y-%m-%d')    order by id;";
     }else{
         $sql.=' order by id;';
     }

  }else{

     $sql.='where '.$condition1_1.'';
     switch($condition1_2){
     case 1: 	$sql.='<';
  			break;
     case 2:  	$sql.='=';
  			break;
     case 3:  	$sql.='>';
			break;
     case 4:  	$sql.='<>';
  			break;

     }

     if($condition1_1=="Time"){
        $sql.="".$condition1_3."";
     }else{
        $sql.='"'.$condition1_3.'"';
     }


     if($condition2_1=="0" or $operation_1=="0"){
        // do nothing
     }else{
        $sql.=' '.$operation_1.' ';
        $sql.=''.$condition2_1.'';
        switch($condition2_2){
        case 1: 	$sql.='<';
  			break;
        case 2:  	$sql.='=';
  			break;
        case 3:  	$sql.='>';
			break;
        case 4:  	$sql.='<>';
     			break;

        }

        $sql.='"'.$condition2_3.'"';
     }

     if($daterange!="All"){
         $sql.=" and DateReceived>=str_to_date('".$datefrom."','%Y-%m-%d') and DateReceived<=str_to_date('".$dateto."','%Y-%m-%d')    order by id;";
     }else{
         $sql.=' order by id;';
     }
  }

   $db 		=& JFactory::getDBO();
   $db->setQuery( $sql );
   if (!$db->query()) {
      JError::raiseError(500, $db->getErrorMsg() );
   }
   $result = $db->loadObjectList( );

  $numrows= count( $result );

  if($numrows == 0) {
       //print "Your query did not return any results.";
     }else {

     	for ($i=0, $n=count( $result ); $i < $n; $i++){

			$row =& $result[$i];

			for ($z=0, $n2=count( $options ); $z < $n2; $z++){
			    $row2 =& $options[$z];
 	    		if(strpos($input,$row2) !== FALSE){
 	    		   $tempvalue="".$row2."".($i+1);
 	    		   if(isset($row->$row2)){
   	    		      $$tempvalue = $row->$row2;
   	    		   }else{
   	    		      $$tempvalue = "";
   	    		   }
	    		}
	    	}
        }
   }

  $header=""; // excel export

  print '<table>';
  print '<tr>';

  for ($z2=0, $n3=count( $input2 ); $z2 < $n3; $z2++){
     $row3 =& $input2[$z2];

     for ($z=0, $n2=count( $options ); $z < $n2; $z++){
	    $row2 =& $options[$z];

         if($row3==$row2){
      	    print '<td class="bfquiztrialReportHeader">'.$row2.'</td>';
            $header .= "".$row2."" . "\t";
         }
     }

  }

  print '</tr>';


  $data="";

  for($z=0; $z < $numrows; $z++){
     $line = '';
     $value="";
     print '<tr>';

   	for ($z3=0, $n3=count( $input2 ); $z3 < $n3; $z3++){
		$row3 =& $input2[$z3];
		for ($z2=0, $n2=count( $options ); $z2 < $n2; $z2++){
        $row2 =& $options[$z2];
        	if($row3==$row2){
     		   $tempname="".$row2."".($z+1);
     		   print '<td class="bfquiztrialReportBody">'.$$tempname.'</td>';
     		   $value = str_replace( '"' , '""' , $$tempname );
     		   $value = str_replace( chr(10) , ' ' , $value );   // this will get rid of newline for summation
			   $value = str_replace( '\"' , '' , $value );  // this will get rid of \"
			   $value = str_replace( '\'' , '' , $value );  // this will get rid of \"
     		   $value = ''.$value.'' . "\t";
     		   $line .= $value;
     		}
     	}
     }

     print '</tr>';
     $data .= trim( $line ) . "\n";
  }

  print '</table>';

  // for excel export
  $data = str_replace( "\r" , "" , $data );

  if ( $data == "" )
  {
    $data = "\n(0) Records Found!\n";
  }

   // excel export
   print '<form id="ExcelExport" name="ExcelExport" method="POST" action="./components/com_bfquiztrial/excelexport.php">';

   print '<input type=hidden name="myheader"  value="'.$header.'">';

   print '<DIV ID="ExcelDate" style="display:none;"><textarea name="mydata">'.$data.'</textarea></div>';

   ?>
   <input name="Submit" type="submit" id="Submit" value="<?php echo JText::_( 'Export to Excel' ); ?>" />
   <?php
   print "</form>";

  //print "<br>";
  //print "<br>";
  //print "SQL Command Used:<br>";
  //print $sql;


}

?>