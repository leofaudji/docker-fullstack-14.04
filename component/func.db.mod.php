<?php
  include 'df.php' ;  

class myData { 
  var $Error = "" ;
  function Connect($cIP,$cUserName,$cPassword,$cDatabase){
    $lLink = mysql_connect($cIP, $cUserName,$cPassword) ;
    if($lLink) mysql_select_db($cDatabase) ;
    return $lLink ;
  }

  function ABrowse($cTableName,$cFieldList = "*",$cWhere = "",$vaJoin = array(),$cGroupBy = "",$cOrderBy = "",$cLimit = ""){
    $dbData = $this->Browse($cTableName,$cFieldList,$cWhere,$vaJoin,$cGroupBy,$cOrderBy,$cLimit) ;
    $nRow = 0 ;
    $vaArray = array();
    while($dbRow = $this->GetRow($dbData)){
      $vaArray [++$nRow] = $dbRow ;
    }
    return $vaArray ;
  }

  function Browse($cTableName,$cFieldList = "*",$cWhere = "",$vaJoin = array(),$cGroupBy = "",$cOrderBy = "",$cLimit = ""){
    $cTableName = strtolower($cTableName) ;
    if(trim($cWhere) <> "" && substr(strtoupper(trim($cWhere)),0,5) <> "WHERE") $cWhere = "Where $cWhere " ;
    if(trim($cGroupBy) <> "") $cGroupBy = "Group By $cGroupBy " ;
    if(trim($cOrderBy) <> "") $cOrderBy = "Order By $cOrderBy " ;
    if(trim($cLimit) <> "") $cLimit = "Limit $cLimit" ;
    $cJoin = "" ;
    if(!empty($vaJoin) && is_array($vaJoin)){
      foreach($vaJoin as $key=>$value){
        $cJoin .= $value . " " ;
      }
    }
    $cSQL = "Select $cFieldList from $cTableName $cJoin $cWhere $cGroupBy $cOrderBy $cLimit" ;
    return $this->SQL($cSQL) ;
  }
  
  function Insert($cTableName,$vaArray,$lSaveLog = true){
    $cTableName = strtolower($cTableName) ;
    $cField = "" ;
    $cValue = "" ;
    foreach($vaArray as $key=>$value){
      $cField .= $key . "," ;
      $cValue .= $value . "','" ;
      if(substr($value,0,1) == "&"){
        $cValue = str_replace("'" . $value . "'",substr($value,1),$cValue) ;
      }
    }
    $cField = "(" . substr($cField,0,strlen($cField)-1) . ")" ;
    $cValue = "('" . substr($cValue,0,strlen($cValue)-2) . ")" ;
    $cSQL = "INSERT INTO $cTableName $cField VALUES $cValue" ;
    $this->SQL($cSQL) ;
    
    if($lSaveLog) SaveLog_DBInsert($cTableName,$vaArray) ;
    return empty($this->Error) ;    
  }
  
  function Edit($cTableName,$vaArray,$cWhere = "",$lSaveLog = true){
    $cTableName = strtolower($cTableName) ;
    
    if($lSaveLog){
      $dbCount = $this->Browse($cTableName,"count(*) as Jumlah",$cWhere) ;
      $nJumlah = 0 ;
      if($dbRow = $this->GetRow($dbCount)){
        $nJumlah = $dbRow ['Jumlah'] ;      
      }
      // Simpan Old Value
      if($nJumlah > 0 && $nJumlah <= 20){
        $vaRow = $this->ABrowse($cTableName,"*",$cWhere) ;
        SaveLog_DBEdit($cTableName,$vaRow,"05") ;
      }
    }

    $cSQL = "" ;
    foreach($vaArray as $key=>$value){
      if(substr($value,0,1) == "&"){
        $value = substr($value,1) ;
      }else{
        $value = "'$value'" ;
      }
      $cSQL .= "$key = $value," ;
    }
    $cSQL = substr($cSQL,0,strlen($cSQL)-1) ;
    
    if($cWhere <> "") $cWhere = "WHERE $cWhere" ;
    $cSQL = "UPDATE $cTableName SET $cSQL $cWhere" ;
    $this->SQL($cSQL) ;

    if($lSaveLog){
      // Simpan New Value
      if($nJumlah > 0 && $nJumlah <= 20){
        $vaRow = $this->ABrowse($cTableName,"*",$cWhere) ;
        SaveLog_DBEdit($cTableName,$vaRow,"06") ;
      }
    }

    return empty($this->Error) ;
  }
  
  function Update($cTableName,$vaArray,$cWhere = "",$lSaveLog = true){
    $cTableName = strtolower($cTableName) ;
    $cWhere = trim($cWhere) ;
    $lNew = true ;
    if($cWhere !== ""){
      $dbData = $this->Browse($cTableName,"*",$cWhere,"","","","0,1") ;
      if($this->Rows($dbData) > 0){
        $lNew = false ;
      }
    }
  
    if($lNew){
      $this->Insert($cTableName,$vaArray,$lSaveLog) ;
    }else{
      $this->Edit($cTableName,$vaArray,$cWhere,$lSaveLog) ;
    }
    return empty($this->Error) ;
  }

  function Delete($cTableName,$cWhere = "",$lSaveLog = true){
    $cTableName = strtolower($cTableName) ;
    
    if($lSaveLog){
      // Check Record Untuk Simpan di Log
      $dbCount = $this->Browse($cTableName,"count(*) as Jumlah",$cWhere) ;
      $nJumlah = 0 ;
      if($dbRow = $this->GetRow($dbCount)){
        $nJumlah = $dbRow ['Jumlah'] ;
      
        if($dbRow ['Jumlah'] > 0 && $dbRow ['Jumlah'] <= 20){
          $vaRow = $this->ABrowse($cTableName,"*",$cWhere) ;
          SaveLog_DBEdit($cTableName,$vaRow,"07") ;
        }
      }
    }

    if(trim($cWhere) <> "") $cWhere = "WHERE $cWhere" ;
    $cSQL = "DELETE FROM $cTableName $cWhere" ;
    $this->SQL($cSQL) ;
    return empty($this->Error) ;
  }
  
  function GetRow($dbData){
    if(empty($this->Error)){
      $va = mysql_fetch_assoc($dbData) ;
      if(!empty($va) && is_array($va)){
        foreach($va as $key=>$value){
          $va [$key] = str_replace("'","\'",str_replace('"','\"',str_replace("\\","\\\\",$value))) ; 
        }
      }
      return $va ;
    }else{
      echo($this->Error) ;
    }
  }
  
  function Rows($dbData){ 
    return mysql_num_rows($dbData) ;
  }
  
  function Cols($dbData){
    return mysql_num_fields($dbData) ;
  }
  
  function GetInsertID(){
    return mysql_insert_id() ;
  }
  
  function SQL($cSQL){
    $dbData = mysql_query($cSQL) ;
    $this->Error = mysql_error() ;
    return $dbData ;
  }
}
$objData = new myData ;
?>