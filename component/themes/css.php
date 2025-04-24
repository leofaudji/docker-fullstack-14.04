<?php
  $__cDir = "default" ;
  $__cDirConfig = $_SERVER['DOCUMENT_ROOT'] . "/.themes-config" ;
  if(!is_dir($__cDirConfig)) mkdir($__cDirConfig) ;
  $_cssfn = $__cDirConfig . "/" . md5(GetSetting("cSession_UserName") . "-" . dirname($_SERVER['PHP_SELF'])) ;
  if(is_file($_cssfn)) $__cDir = trim(file_get_contents($_cssfn));

  if($__cDir == "" || !is_dir(compFolder() . "/themes/" . $__cDir)) $__cDir = "default" ;
  echo('<link rel="stylesheet" type="text/css" href="' . compFolder() . "/themes/" . $__cDir . '/css.css"> ') ;  
?>