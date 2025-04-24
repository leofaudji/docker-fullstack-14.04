<?php
  include 'df.php' ;
  
function menuHorizontal($nTop=1,$nLeft=1,$cMultiForm='',$cMenuFileName='',$lMySQL=true){
  $oMenu = new oMenu ;
  $oMenu->menuHorizontal($nTop,$nLeft,$cMultiForm,$cMenuFileName,$lMySQL) ;
}
function menuVertical(){}

class oMenu{
  function GetArray($cMenuFileName=''){
    $vaSubModul = $this->__CheckSubModul() ;

    if(empty($cMenuFileName)){
      $cMenuFileName = "./menu.menu.php" ;
      if(!is_file($cMenuFileName)){
        $cMenuFileName = "./menu.menu" ;
      }
    }

    $vaL = file($cMenuFileName) ;
    $nLines = 0 ;
    foreach($vaL as $key=>$value){
      $lShowMenu = true ;    
      if(substr(trim($value),0,2) == "//") $lShowMenu = false ;

      // Ambil Sub Modul
      if(strtolower(substr(trim($value),0,9)) == "<submenu:"){
        $lShowMenu = false ;
        $value = strtolower($value) ;
        $nSpace = strpos($value,"<submenu:") ;
        $value = trim($value) ;
        $cKey = str_replace(">","",substr($value,9)) ;
        if(isset($vaSubModul [$cKey])){
          foreach($vaSubModul [$cKey] as $key1=>$value1){
            $vaLine [$nLines] = str_repeat(" ",$nSpace) . $value1 ;
            $nLines ++ ;
          }
        }
      }
      if($lShowMenu){
        $vaLine [$nLines] = $value ;
        $nLines ++ ;
      }
    }
    return $vaLine ;
  }

  function __CheckSubModul(){
    $vaRetval = array() ;
    $cSubModul = GetSetting("cSession_SubModul","") ;
    if(!empty($cSubModul) && is_dir($cSubModul)){
      if(is_file($cSubModul . "/submenu.jscript.php")) include $cSubModul . "/submenu.jscript.php" ;

      $cFile = $cSubModul . "/submenu.menu.php" ;
      if(is_file($cFile)){
        $vaLine = file($cFile) ;
        $cKey = "" ;
        foreach($vaLine as $key=>$value){      
          if(strtolower(substr(trim($value),0,9)) == "<submenu:"){
            $value = trim(strtolower($value)) ;
            $cKey = str_replace(">","",substr($value,9)) ;
          }else if(!empty($cKey)){
            $val = trim($value) ;
            $lShowMenu = true ;
            if(substr($val,0,2) == "//" || empty($val)){
              $lShowMenu = false ;
            }
            if($lShowMenu) $vaRetval [$cKey][$key] = $value ;
          }
        }
      }
    }
    return $vaRetval ;
  }

  // Create Menu
  function menuHorizontal($nTop=1,$nLeft=1,$cMultiForm='',$cMenuFileName='',$lMySQL=true){
    global $objData ;
    $vaLine = $this->GetArray($cMenuFileName) ;

    $nRow = 0 ;
    $cUserLevel = GetSetting("cSession_UserLevel") ;
    $__cLevel = md5($cUserLevel) ;
    $cRow = "" ;
    echo('<div id="hzMainMenu" class="menu_hrz_main" style="position:absolute;top:' . $nTop . 'px;left:' . $nLeft . 'px"><table id="table-menu-horizontal" width="10px"  border="0" cellspacing="1" cellpadding="2"><tr>') ;
    foreach($vaLine as $key=>$value){
      $nCurLevel = strpos($value,"[") / 2 ;
      if($nCurLevel == 0){      
        $value = trim($value) ;
        eval("\$va = array" . str_replace("]",")",str_replace("[","(",$value)) . " ;") ;      
        $nRow ++ ;
        $cSubMenuID = "__cMenuID-" . $nRow ;
        $lShow = true ;
        if($cUserLevel <> "0000" && $lMySQL){
          $cUserMenu = md5(trim($va [0])) ;
          $db = $objData->Browse("username_menu","*","Level='$__cLevel' and Keterangan = '$cUserMenu'") ;
          $lShow = $objData->Rows($db) > 0 ;
        }
        if($lShow){
          echo('<td id="cell-'.$cSubMenuID.'" nowrap class="menu_hrz_out no_txt_select">&nbsp;' . $nRow . ' ' . $va[0] . '&nbsp;</td>') ;
          $cRow .= $nRow . "," ;
        }
      }
    }
    echo('</tr></table></div>') ;
  
    $nRow = 0 ;
    $cDir = "0" ;
    $this->__SubMenu($cDir,$vaLine,$nRow,0,"",$cMultiForm,$lMySQL) ;  
 
    if(is_file("./menu.jscript.php")) include './menu.jscript.php' ;
    echo('<script language="javascript" type="text/javascript">hMenu.init("' . $cRow . '");</script>') ;
  }

  function __SubMenu($cParent,$vaLine,&$nRow,$nLevel,$cMenuNumber,$cMultiForm,$lMySQL){
    $vaMenu = array() ;
    $nMenu = 0 ;
    $nMenuNumber = 0 ;
    if(trim($cMenuNumber) <> ""){
      $cMenuNumber .= '.' ;
    }
    for($nRow;$nRow<count($vaLine);$nRow++){      
      $value = $vaLine [$nRow] ;
      $nCurLevel = strpos($value,"[") / 2 ;
      if($nCurLevel > $nLevel){
        $vaMenu [$nMenu]['SubMenu'] = true ;
        $nCurLevel = $this->__SubMenu($cDir . '~' . $va [0],$vaLine,$nRow,$nCurLevel,$cMenuNumber . $nMenuNumber,$cMultiForm,$lMySQL) ;
      }
      if($nRow < count($vaLine)){
        $value = $vaLine [$nRow] ;
        eval("\$va = array" . str_replace("]",")",str_replace("[","(",$value)) . " ;") ;      
        if(trim($va [0]) <> "-"){
          $nMenuNumber ++ ;
        }
        if($nCurLevel < $nLevel){
          $this->__WriteMenu($cDir,$vaMenu,$cMenuNumber,$cMultiForm,$lMySQL) ;        
          return $nCurLevel ;
        }
        $cDir = $cParent ; 
        $vaMenu [++$nMenu] = $va ;
        $vaMenu [$nMenu]['SubMenu'] = false ;
      }
    }
    $this->__WriteMenu($cDir,$vaMenu,$cMenuNumber,$cMultiForm,$lMySQL) ;
  }

  function __WriteMenu($cParent,$vaMenu,$cMenuNumber,$cMultiForm,$lMySQL){
    global $objData ;
    if($cParent <> "0" && !empty($vaMenu)){
      $cID = "__cMenuID-" . substr($cMenuNumber,0, strlen($cMenuNumber)-1) ;
      $nMenu = 0 ;
      echo('<div id="' . $cID . '" class="menu_vert_main fadein">') ;
      echo('<table id="table-'.$cID.'" class="menu_vert_content" width="100px"  border="0" cellspacing="0" cellpadding="0">') ;
      $cMenuParent = str_replace("~","",str_replace("0~","",$cParent)) ;
      $cUserLevel = GetSetting("cSession_UserLevel") ;
      $__cLevel = md5($cUserLevel) ;
      foreach($vaMenu as $key=>$value){
        $lShow = true ;
        $vaMenuConf = array("sub"=>0,"sub-id"=>"","url"=>"","cFunc"=>"","frmName"=>"","frmTitle"=>"","nWidth"=>0,"nHeight"=>0,"menuNumber"=>"") ;
        if($cUserLevel <> "0000" && $lMySQL){
          $cUserMenu = $cMenuParent . trim($value [0]) ;
          while(isset($vaOld [$cUserMenu]))  $cUserMenu .= "x" ;
          $vaOld [$cUserMenu] = "1" ;
          $cUserMenu = md5($cUserMenu) ;
          $db = $objData->Browse("username_menu","*","Level='$__cLevel' and Keterangan = '$cUserMenu'") ;
          $lShow = $objData->Rows($db) > 0 ;
        }
        if($value [0] <> "-") $nMenu ++ ;
        if($lShow){
          $cArrow = '' ;
          $vaMenuConf ["sub"] = 0 ;
          $nHeight = 5 ;
          $cClass = "menu_vert_item" ;
          $cMenuID = "" ;
          if($value [0] <> "-"){          
            $cFunc = str_replace("-","",str_replace("&","",str_replace("\\","",str_replace("/","",str_replace(".","",$value [1]))))) ;
            $cMenuItem = $cMenuNumber . $nMenu . "&nbsp;&nbsp;" . str_replace(" ","&nbsp;",$value [0]) . "&nbsp;&nbsp;&nbsp;&nbsp;" ;
            $cMenuID = 'id="menuid-' . $cMenuNumber . $nMenu . '"' ;
            if(isset($value [4])){
              $cFormTitle = "Formname" ;
              if(isset($value [4])) $cFormTitle = $value [4] ;
              $cFormTitle .= "&nbsp;&nbsp;-&nbsp;&nbsp;Menu&nbsp;:&nbsp;" . $cMenuNumber . $nMenu ;
              $cFormName = md5($cMenuNumber . $nMenu . $value [0]) ;
              $nWidth = 400 ;
              if(isset($value[5])) $nWidth = $value [5] ;
              $nHeight = 400 ;
              if(isset($value [6])) $nHeight = $value[6] ;
            
              $vaMenuConf ["url"] = getlink($value [1],false) ;
              $vaMenuConf ["cFunc"] = $cFunc ;
              $vaMenuConf ["frmName"] = $cFormName ;
              $vaMenuConf ["frmTitle"] = $cFormTitle ;
              $vaMenuConf ["nWidth"] = $nWidth ;
              $vaMenuConf ["nHeight"] = $nHeight ;
              $vaMenuConf ['menuNumber'] = $cMenuNumber . $nMenu ;
            }else{
              $vaMenuConf ["cFunc"] = $cFunc ;
              $vaMenuConf ["frmTitle"] = $value [0] ;
              $vaMenuConf ['menuNumber'] = $cMenuNumber . $nMenu ;
            }

            if($value ['SubMenu']){
              $cArrow = '<img src="' . compFolder() . '/menu/arrow.gif">' ;
              $vaMenuConf ["sub"] = 1 ;
              $cClick = "" ;
            }
            $vaMenuConf ['sub-id'] = "__cMenuID-" . $cMenuNumber . $nMenu ;            
          }else{
            $cArrow = "" ;
            $cMenuItem = '' ;
            $cClass = "menu_vert_item_sep" ;
          }
          $cImageLeft = '' ; 
          if(isset($value[2]) && trim($value[2]) !== ""){
            $cImageLeft = '<img src="'.$value[2].'" border="0px" alt="">' ;
          }else if($value ['SubMenu']){
            $cImageLeft = '<img src="' . compFolder() . '/menu/menu-foolder.gif" border="0px">' ;
          }
          $cMenuConfig = implode(",",$vaMenuConf) ;
          echo('<tr ' . $cMenuID . ' class="' . $cClass . '"><td align="center" nowrap>' . $cImageLeft . '</td><td style="display:none">' . rawurlencode($cMenuConfig) . '</td><td nowrap>' . $cMenuItem . '</td><td nowrap>' . $cArrow . '</td></tr>') ;
        }
      }
      echo('</table></div>') ;
    }
  }
}
$oMenu = new oMenu ;
?>