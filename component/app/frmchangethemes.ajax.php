<?php
  include 'df.php' ;
  
function ApplayThemes($va){
  $cDirConfig = $_SERVER['DOCUMENT_ROOT'] . "/.themes-config" ;
  if(!is_dir($cDirConfig)) mkdir($cDirConfig) ;

  $_cssfn = $cDirConfig . "/" . md5(GetSetting("cSession_UserName") . "-" . dirname($_SERVER['PHP_SELF'])) ;
  if(is_file($_cssfn)) unlink($_cssfn) ;        
  $handle = fopen($_cssfn, "w");
  fwrite($handle,$va ['cFolder']) ;
  fclose($handle) ;  

  echo("ok") ;
}
?>