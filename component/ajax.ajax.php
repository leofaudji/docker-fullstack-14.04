<?php
  include 'df.php' ;

function _Browse($va){
  global $objData ;
  $va ['cSQL'] = str_replace("\'","'",rawurldecode($va ['cSQL'])) ;
  $va1 = array() ;
  $dbData = $objData->SQL($va ['cSQL']) ;
  while($dbRow = $objData->GetRow($dbData)){
    $key = "" ;
    foreach($dbRow as $field => $value){
      if($key == "") $key = $value ;
    }
    if($key !== "") $va1[$key] = $dbRow ;
  }
  echo(json_encode($va1)) ;
}

function UpdLogCloseMenu($va){
  SaveCloseForm($va) ;
}
?>