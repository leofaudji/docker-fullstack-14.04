<?php
class SisTab{
  var $vaTab = array() ;
  var $Height = "" ;
  var $TabCount = 0 ;
  var $Name = "" ;
  function Show(){
    global $txt ;
    $nHeight = ( strpos($this->Height,"%") === false) ? $this->Height + 23 : $this->Height ;
    $this->Name = ($this->Name == "") ? md5(now() . rand(0,1000000)) : $this->Name ;
    if(!empty($this->vaTab)){
      $cFirst = "" ;
      $lFoundSelected = false ;
      $n = 0 ;
      foreach($this->vaTab as $key=>$value){
        $n ++ ;
        $vaKey1 [$n] = $key ;
        if($cFirst == "") $cFirst = $key ;
        if($lFoundSelected){
          $this->vaTab [$key]["Selected"] = false ; 
          $cFirst = $key ;
        }else if($this->vaTab[$key]['Selected']){
          $lFoundSelected = true ;
        }
      }
      if(!$lFoundSelected) $this->vaTab [$cFirst]['Selected'] = true ;
      $cIDClick = "" ;
      $cIDBodyClick = "" ;
      echo("<div id='{$this->Name}' class='tab_main' style='height:$nHeight'><div class='tab_tab'>") ;
      foreach($this->vaTab as $key=>$value){
        $cClass = ($value ['Selected'])? "tab_item tab_click no_txt_select":"tab_item tab_normal no_txt_select" ;
        if(substr(trim($value ['URL']),0,1) == "#"){
          $this->vaTab [$key]['id'] = substr($value ['URL'],1) ;
        }else{
          $this->vaTab [$key]['id'] = $value ['Name'] . "-d";
        }
        if($value ['Selected']){
          $cIDClick = $value ['Name'] ;
          $cIDBodyClick = $this->vaTab [$key]['id'] ;
        }

        echo('<div class="' . $cClass . '" id="' . $value ['Name'] . '" onClick="tab.Click(this,\'' . $this->vaTab [$key]['id'] . '\',\'' . $this->Name . '\');">' . $value ['Title'] . '</div>') ;
      }
      echo('</div><div class="tab_body" id="' . $this->Name . '-body">') ;
      $cTabDiv = "" ;
      foreach($this->vaTab as $key=>$value){
        if(substr(trim($value ['URL']),0,1) <> "#"){
          echo('<div id="' . $key . '-d" class="tab_content">') ;
          if(!empty($value ['URL'])) include $value ['URL'] ;
          echo('</div>') ;          
        }
        if(isset($this->vaTab [$key]['id'])) $cTabDiv .= $this->vaTab [$key]['id'] . "," ;
      }
      echo('</div></div>') ;
      echo('<script type="text/javascript">tab.init(a.getById("' . $cIDClick . '"),"' . $cIDBodyClick . '","' . $this->Name . '","' . $cTabDiv . '")</script>') ;
    }
  }

  function Add($cTitle,$cURL='',$lSelected=false){
    $this->TabCount ++ ;
    $cName = "t" . md5($this->TabCount . session_id() . time() . rand(1,1000000)) ;
    $this->vaTab[$cName] = array("Name"=>$cName,"Title"=>$cTitle,"Selected"=>$lSelected,"URL"=>$cURL) ;
  }
}
?>