<?php
/**************************************************************************************************************************************************************************************************/
function PrintIO($vaPrintLine,$lEject=true,$nCharSpace=0,$cTextInit="\033\033\017\017",$cPortDefault=''){
  if(!empty($vaPrintLine)){
    if($cPortDefault == ''){
      $cPort = "/dev/lp0" ;
      if(IsWindowsOS()) $cPort = "LPT1" ;
    }else{
      $cPort = $cPortDefault ;
    }
    echo('<applet CODEBASE="' . compFolder() . '/io/" code="printio.class" height="0" width="0">') ;
    echo('<param name="PORT" Value = "' . $cPort . '">') ;
    $nRow = 0 ;
    foreach($vaPrintLine as $key=>$value){
      $v = $value ;
      if($nCharSpace > 0){
        $v = "" ;
        for($x=0;$x<strlen($value);$x++){
          $v .= substr($value,$x,1) . str_repeat(" ",$nCharSpace) ;
        }
      }
      $v = $cTextInit . $v ;

      echo('<param name="cPrint' . $nRow . '" Value = "x' . $v . '">') ;
      $nRow ++ ;
    }
    echo('<param name="nJumlah" Value = "' . count($vaPrintLine) . '">') ;
    $cEject = "true" ;
    if(!$lEject) $cEject = "false" ;
    echo('<param name="Eject" Value = "' . $cEject . '">') ;
    echo('</applet>') ;
  }
}

/**************************************************************************************************************************************************************************************************/
function IsWindowsOS(){
  $lRetval = false ;
  $cAgent = $_SERVER['HTTP_USER_AGENT'] ;
  if(preg_match("/Windows/i",$cAgent)){
    $lRetval = true ;
  }
  return $lRetval ;
}
?>