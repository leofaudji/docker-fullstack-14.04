<?php
  include 'df.php' ;
  $cLink = "" ;
  
/*
  Kita Akan Definisikan Function yang Tidak ada di PHP Baru Supaya Bisa Kita Definisikan
  session_register($cKey) ;
  session_is_registered($cKey) ;
  split(csep,string,limit) ;
*/

if (!function_exists('session_is_registered')) {
  function session_is_registered($cName){
    return isset($_SESSION[$cName]) ;
  } 
}

if (!function_exists('session_register')) {
  function session_register($cName){
    $_SESSION[$cName] = "" ;
  }
}

if (!function_exists('split')){     
  function split($cSep,$cString,$nLimit=2147483647){
    //$cSep = str_replace('\','',$cSep) ; 
    return explode($cSep,$cString,$nLimit) ;
  }
}

if (!function_exists('mysql_connect')){      
  function mysql_connect($cHost,$cUserName,$cPassword,$cDatabase=""){
    $cLink = mysqli_connect($cHost,$cUserName,$cPassword,$cDatabase) ; 
    return $cLink ;
  }
} 

if (!function_exists('mysql_query')){
  function mysql_query($cSQL){ 
    global $objData ;
    return $objData->SQL($cSQL) ;
  }
} 

if (!function_exists('mysql_fetch_array')){      
  function mysql_fetch_array($dbData){ 
    global $objData ; 
    return $objData->FetchArray($dbData) ;
  }
} 

if (!function_exists('mysql_fetch_assoc')){      
  function mysql_fetch_assoc($dbData){ 
    global $objData ;
    return $objData->FetchAssoc($dbData) ;
  }
} 

if (!function_exists('mysql_error')){      
  function mysql_error(){ 
    global $objData ;
    return $objData->SQLError() ;
  }
} 

if (!function_exists('mysql_num_rows')){      
  function mysql_num_rows($dbData){ 
    global $objData ;
    return $objData->NumRows($dbData) ;
  }
} 

if (!function_exists('mysql_close')){      
  function mysql_close(){ 
    global $objData ;
    return $objData->SQLClose() ;
  }
} 

if (!function_exists('mysql_select_db')){      
  function mysql_select_db($cDatabase){ 
    global $objData ;
    return $objData->SelectDB($cDatabase) ;
  }
}
?>
