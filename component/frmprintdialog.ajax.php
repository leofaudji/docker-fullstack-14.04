<?php
  include 'df.php' ;
  
function SaveConfig($va){
  $cExportCSV = "0" ;
  if(isset($va ['ckExportCSV'])) $cExportCSV = "1" ;

  $_SESSION["print_Paper"]  = $va ['cPaper'] ;
  $_SESSION["print_Width"]  = $va ['nWidth'] ;
  $_SESSION["print_Height"] = $va ['nHeight'] ;

  $_SESSION["print_Top"]  = $va ['nTop'] ;
  $_SESSION["print_Left"]  = $va ['nLeft'] ;
  $_SESSION["print_Bottom"] = $va ['nBottom'] ;
  $_SESSION["print_Right"]  = $va ['nRight'] ;
  $_SESSION["print_Dialog"]  = 1 ;
  $_SESSION["print_ExportCSV"]  = $cExportCSV ;
  
  echo('Preview();') ;
}
?>