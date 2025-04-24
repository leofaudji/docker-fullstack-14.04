<?php
  /* Untuk Supaya Bisa di Debug buat File di DOCUMENT_ROOT buat parameter __debug=true */
  $cFileDebug = $_SERVER['DOCUMENT_ROOT'] . "/global.ini" ;
  $cDebug = "false" ;
  if(is_file($cFileDebug)){
    $va = file($cFileDebug);
    foreach($va as $key=>$value){
      $c = explode("=",trim($value)) ;
      if(count($c) >= 2){
        $c [0] = strtolower($c [0]) ;
        if($c [0] == "__debug") $cDebug = $c [1] ;
      }
    }
  }

  $cDir = dirname(__FILE__) ;
  $vaJS = array("dbg","msg","txt","frm","main","menu","sys") ;  
  $cTotal = "__debug = $cDebug;" ;
  $vaReplace = array("\n"=>' ',"\r"=>' ','  '=>' ',' ;'=>';','; '=>';',' }'=>'}','} '=>'}','{ '=>'{',' {'=>'{',' =='=>'==','== '=>'==',' ='=>'=','= '=>'=') ;
  foreach($vaJS as $key=>$_cJS){
    $cJS = "" ;
    $cFile = $cDir . "/ajax." . $_cJS . ".php" ;
    if($cFile <> "" && is_file($cFile)){
      $va = file($cFile);
      $lStart = false ;
      foreach($va as $key=>$value){
        $nPos = strpos($value,"//") ;
        if ($nPos !== false) $value = substr($value,0,$nPos) ;
        if(!$lStart){
          if(strpos($value,'?>') !== false) $lStart = true ;
        }else{
          $cJS .= $value ;
        }
      }
      foreach($vaReplace as $src=>$rep){
        $nLoop = 0 ;
        while(strpos($cJS,$src) !== false && ++$nLoop < 20){
          $cJS = str_replace($src,$rep,$cJS) ;
        }
      }
      $cTotal .= $cJS . ";\n" ;
    }
  }
  echo($cTotal) ;
?>