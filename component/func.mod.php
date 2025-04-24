<?php
  include 'df.php' ;

  $_db = function_exists('mysql_query') ? 'func.db.mod.php' : 'func.mysqli.mod.php' ;  
  include 'func.comp.mod.php' ;  
  include 'func.date.mod.php' ;
  include 'func.dec2text.mod.php' ;
  include 'func.odt.mod.php' ;  
  include $_db ;
  include 'func.menu.mod.php' ;
  include 'func.log.mod.php' ;

function compFolder($lFullPath = false){
  $cFile = dirname(__FILE__) ;
  if(!$lFullPath) $cFile = str_replace($_SERVER['DOCUMENT_ROOT'],"",$cFile) ;
  $vaPath = explode("/",$cFile) ;
  $cFile = $vaPath [count($vaPath)-1] ;
  return "../" . $cFile ;
}

function MyConfig(){
  $cFileConfig = "./.project.cfg" ;
  $vaConfig = array("version"=>"1.0.0") ;
  if(is_file($cFileConfig)){
    $vaFile = file($cFileConfig,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ;
    foreach($vaFile as $key=>$value){      
      $vaTag = explode("=",$value) ;
      if(count($vaTag) >= 2){
        $cKey = strtolower(trim($vaTag [0])) ;
        $vaConfig [$cKey] = trim($vaTag [1]) ;
      }
    }
  }
  return $vaConfig ;
}

function NextMonth($nTime,$nNextMonth){
  $nDay = date("d",$nTime) ;
  $nMonth = date("m",$nTime) ;
  $nYear = date("Y",$nTime) ;
  
  $n1 = mktime(0,0,0,$nMonth + $nNextMonth,$nDay,$nYear) ;
  $n2 = mktime(0,0,0,$nMonth+$nNextMonth+1,0,$nYear) ;
  return min($n1,$n2) ;
}

function NextDay($nTime,$nNextDay){
  $nDay = date("d",$nTime) ;
  $nMonth = date("m",$nTime) ;
  $nYear = date("Y",$nTime) ;
  
  $n = mktime(0,0,0,$nMonth,$nDay+$nNextDay,$nYear) ;
  return $n ;
}

function NextWeek($nTime,$nNextWeek){
  return NextDay($nTime,$nNextWeek*7) ;
}

function Date2String($dTgl){
  $cRetval = substr($dTgl,0,10) ;
  $va = split("-",$dTgl) ;
  // Jika Array 1 Bukan Tahun maka akan berisi 2 Digit
  if(strlen($va [0]) == 2){
    $cRetval = $va [2] . "-" . $va [1] . "-" . $va[0] ;
  }
  return $cRetval ;
}

function String2Date($cString){
  $cRetval = substr($cString,0,10) ;
  $va = split("-",$cString) ;
  // Jika Array 1 Tahun maka akan berisi 4 Digit
  if(strlen($va [0]) == 4){
    $cRetval = $va [2] . "-" . $va [1] . "-" . $va[0] ;
  }
  return $cRetval ;
}

function String2Number($cString){
  return str_replace(",","",$cString) ;
}

function Number2String($nNumber,$nDecimals=2){
  $nNumber = floatval(String2Number($nNumber)) ;
  return number_format($nNumber,$nDecimals,".",",") ;
}

function getVar(){
  $cRetval = "" ;
  if(!empty($_GET)){
    foreach($_GET as $key=>$value){
      $cRetval .= "\$" . $key . " = \$_GET['" . $key . "'] ;" ;
    }
  }

  if(!empty($_POST)){
    foreach($_POST as $key=>$value){
      $cRetval .= "\$" . $key . " = \$_POST['" . $key . "'] ;" ;
    }
  }

  return $cRetval ;
}

function Devide($a,$b){
  $nRetval = 0 ;
  if(empty($a) || empty($b) || $a == 0 || $b == 0){
    $nRetval = 0 ;
  }else{
    $nRetval = $a / $b ;
  }
  return $nRetval ;
}

function GetFileModul($cFileName,$cExt){
  $cDir = dirname($cFileName) ;
  $vaFile = split('\.',basename($cFileName)) ;  
  $cFile = $vaFile [0] . $cExt ;
  if($cExt == "xx.jscript.php"){
    $cDir .= "/" . $cFile ;
    if(is_file($cDir)){
      $vaIgnore = array(";"=>"","}"=>"","{"=>"") ;
      $c = "" ;
      $va = file($cDir, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach($va as $key=>$value){
        $value = trim($value) ;
        $nPos = strpos($value,"//") ;
        if ($nPos !== false) $value = substr($value,0,$nPos) ;
        
        $cLast = substr(trim($value),-1,1) ;
        if(!isset($vaIgnore [$cLast])){
          $value .= chr(10) ;
        }
        $c .= $value ;
      }
      $c = str_replace("{\n","{",$c) ;
      $cFile = _getFile($cFile) ;
      $handle = fopen($cFile, "w");
      fwrite($handle,$c) ;
      fclose($handle) ;
    }
  }
  return $cFile ;
}

function _getFile($cFile){
  $cDir = $_SERVER['DOCUMENT_ROOT'] . "/.tmp/D" ;

  $nDir = date("H")%3 ;
  $nDir1 = $nDir + 1 ;
  if($nDir1 == 3) $nDir1 = 0 ;

  if(is_dir($cDir . $nDir1)) _delTmpDir($cDir . $nDir1);
  if(!is_dir($cDir . $nDir)) mkdir($cDir . $nDir,0777); 

  return $cDir . $nDir . "/" . $cFile ;
}

function _delTmpDir($cDir){
  if(is_dir($cDir)){
    $d = dir($cDir) ;            
    while (false !== ($entry = $d->read())) {
      if(is_dir($cDir . '/' . $entry)){
        if($entry !== "." && $entry !== ".."){
          DeleteDirectory($cDir . '/' . $entry) ;
        }
      }else{
        if(is_file($cDir . '/' . $entry)){
          unlink($cDir . '/' . $entry) ;
        }
      }
    }
    $d->close();
    rmdir($cDir) ;
  }
}

function getBrowser($cUserAgent){ 
  $u_agent = $cUserAgent ;
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version= "";

  //First get the platform?
  if (preg_match('/linux/i', $u_agent)) {
    $platform = 'linux';
  }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
    $platform = 'mac';
  }elseif (preg_match('/windows|win32/i', $u_agent)) {
    $platform = 'windows';
  }
    
  // Next get the name of the useragent yes seperately and for good reason
  if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){ 
    $bname = 'Internet Explorer'; 
    $ub = "MSIE"; 
  }elseif(preg_match('/Firefox/i',$u_agent)){ 
    $bname = 'Mozilla Firefox'; 
    $ub = "Firefox"; 
  }elseif(preg_match('/Chrome/i',$u_agent)){ 
    $bname = 'Google Chrome'; 
    $ub = "Chrome"; 
  }elseif(preg_match('/Safari/i',$u_agent)){ 
    $bname = 'Apple Safari'; 
    $ub = "Safari"; 
  }elseif(preg_match('/Opera/i',$u_agent)){ 
    $bname = 'Opera'; 
    $ub = "Opera"; 
  }elseif(preg_match('/Netscape/i',$u_agent)){ 
    $bname = 'Netscape'; 
    $ub = "Netscape"; 
  } 
    
  // finally get the correct version number
  $known = array('Version', $ub, 'other');
  $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
  if (!preg_match_all($pattern, $u_agent, $matches)) {
    // we have no matching number just continue
  }
    
  // see how many we have
  $i = count($matches['browser']);
  if ($i != 1) {
    //we will have two since we are not using 'other' argument yet
    //see if version is before or after the name
    if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
      $version= $matches['version'][0];
    }else{
      $version= $matches['version'][1];
    }
  }else{
    $version= $matches['version'][0];
  }
    
  // check if we have a number
  if ($version==null || $version=="") {$version="?";}
    
  return array('userAgent' => $u_agent,'name'=>$bname,'version'=>$version,'platform'=>$platform,'pattern'=>$pattern);
} 
?>