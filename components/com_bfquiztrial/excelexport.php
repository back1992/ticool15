<?php

   $myheader = $_POST['myheader'];
   $mydata = $_POST['mydata'];


header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=bfquiztrial_export.xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$myheader\n$mydata";

?>