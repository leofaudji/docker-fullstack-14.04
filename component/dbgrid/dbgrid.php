<?php

class DBGrid{
  var $AddColumn = array() ;
  var $Array = array() ;
  var $Height = "200px" ;
  var $Col ;
  var $Width = "100%" ;
  var $Name = "" ;
  var $count = 0 ;
  var $Caption = "" ;
  var $onClick = "" ;
  var $ShowFooter = false ;
  var $AutoWidth = false ;
  var $BorderColor = "#56569d" ;
  var $tmpDir = "" ;
  var $Scrolling = "auto" ;
  function Init(){
    $this->AddColumn = array() ;
    $this->Array = array() ;
    $this->Height = "200px" ;
    $this->Col = array() ;    
    $this->Width = "100%" ;
    $this->Name = "" ;
    $this->Caption = "" ;
    $this->onClick = "" ;
    $this->ShowFooter = false ;
    $this->AutoWidth = false ;
    $this->BorderColos = "#56569d" ;
    $this->tmpDir = "" ;
    $this->Scrolling = "auto" ;
  }

  function dataSource($dbData){
    global $objData ;
    $nRow = 0 ;    
    while($dbRow = $objData->GetRow($dbData)){
      $this->Array [++$nRow] = $dbRow ;
    }
  }

  function SQL($cSQL){
    global $objData ;
    $dbData = $objData->SQL($cSQL) ;
    $this->dataSource($dbData) ;
  }

  function dataBind(){
    if(empty($this->Name)){
      $this->count ++ ;
      $this->Name =  "DBGRID" . $this->count ;
    }

    $vaColWidth = array() ;
    $vaColType = array() ;
    $vaColAlign = array() ;
    $vaColDisp = array() ;
    $vaColEdit = array() ;
    $vaColName = array() ;

    $vaBody = $this->writeBody($vaColWidth,$vaColType,$vaColAlign,$vaColDisp,$vaColEdit,$vaColName) ;
    $cHeader = $this->writeHeader($vaBody [1]) ;
    $cFooter = $this->writeFooter($vaBody [1]) ;
    if($cFooter <> ""){
      $cFooter = '<div id="dbg_footer_' . $this->Name . '" class="dbg_footer">' . $cFooter . '</div>' ;
    }

    $css = "" ;
    $nCol = 0 ;
    foreach($vaColWidth as $key=>$value){
      if(strpos($value,"px") === false) $value .= "px" ;
      $css .= '<style id="css_' . $nCol . "_" . $this->Name . '">.' . $this->Name . '_' . $nCol . ' {width:' . $value . ';max-width:' . $value . ';min-width:' . $value . '}</style>' ;
      $nCol ++ ;
    }
    echo($css) ;

    $cCaption = "" ;
    if($this->Caption <> "") $cCaption = '<div class="dbg_caption no_txt_select" id="dbg_caption_' . $this->Name . '">' . $this->Caption . '</div>' ;
    
    $cScrolling = strtolower($this->Scrolling) ;
    $cScr = "" ;
    if($cScrolling == "vertical"){
      $cScr = "overflow:auto;overflow-x: hidden;" ;
    }else if($cScrolling == "horizontal"){
      $cScr = "overflow:auto;overflow-y: hidden;" ;
    }else if($cScrolling == "none"){
      $cScr = "overflow: hidden;" ;
    }else if($cScrolling == "scroll"){
      $cScr = "overflow: scroll;" ;
    }
    if($cScr <> "") $cScr = 'style="' . $cScr . '"' ;

    $cColType = rawurlencode(implode(",",$vaColType)) ;
    $cColAlign = rawurlencode(implode(",",$vaColAlign)) ;
    $cColDisp = rawurlencode(implode(",",$vaColDisp)) ;
    $cColEdit = rawurlencode(implode(",",$vaColEdit)) ;
    $cColName = rawurlencode(implode(",",$vaColName)) ;
    $cStyle = "height:" . $this->Height . ";width:" . $this->Width ; 
    echo('<div style="' . $cStyle . '" class="dbg_border" id="dbg_border_' . $this->Name . '">') ;
    echo('<table onMouseOver="' . $this->Name . '.recheckGrid();" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">') ;
    if($cCaption <> "") echo('<tr><td height="10px">' . $cCaption . '</td></tr>') ;
    echo('<tr><td>') ;   
    echo('<div id="dbg_main_' . $this->Name . '" class="dbg_main" ' . $cScr . ' onScroll="' . $this->Name . '.onScroll(this)"><div id="dbg_header_' . $this->Name . '" class="dbg_header">' . $cHeader . '</div><div id="dbg_body_' . $this->Name . '" class="dbg_body">' . $vaBody [0] . '</div>' . $cFooter . '</div>') ;
    echo('</td></tr></table></div>') ;
    echo('<script language="javascript" type="text/javascript">var ' . $this->Name . ' = new DBGRID();' . $this->Name . '.init(\'' . $this->Name . '\',\'' . $cColType . '\',\'' . $cColAlign . '\',\'' . $cColDisp . '\',\'' . $cColEdit . '\',\'' . $cColName . '\',\'' . $this->AutoWidth . '\');</script>') ;
    $this->Init() ; 
  }

  function writeHeader($vaHead){
    $html = '<table border="0" cellspacing="0" cellpadding="0" id="tbHead_' . $this->Name . '" height="100%"><tr>' ;

    $nColNum = 0 ;
    $cBGColor = "#0099ff" ;
    foreach($vaHead as $key=>$value){
      $cCollID = "cell_$nColNum" ;
      $cAlign = "center" ;
      $cStyle = "" ;
      $nColWidth = $value['Width'] ;
      $cCaption = $value['Name'] ;
      if(isset($this->Col[$value['Name']]['Caption'])) $cCaption = $this->Col[$value['Name']]['Caption'] ;
      if(isset($value ['Type'])){
        if($value ['Type'] == "checkbox"){
          $cCaption = '<img style="padding-top:1px" id="ck_cell_all" src="' . compFolder() . '/dbgrid/images/uncheck.gif" border="0" onClick="' . $this->Name . '.checkMark(this,'.$nColNum.')"  onMouseOver="' . $this->Name . '.checkOver(this,0,' . $nColNum . ')" onMouseOut="' . $this->Name . '.checkOut(this,0,' . $nColNum . ')">' ;
        }
      }

      // Display Kolom Boleh di Hidden
      if(isset($this->Col[$value['Name']]['Display']) && strtolower($this->Col[$value['Name']]['Display']) == "hidden"){
        $cStyle .= ';display:none' ;
      }
      if(isset($this->Col[$value['Name']]['Header']['Font-Color'])) $cStyle .= ";color:" . $this->Col[$value['Name']]['Header']['Font-Color'] ;
      if(isset($this->Col[$value['Name']]['Header']['Back-Color'])) $cStyle .= ";background:" . $this->Col[$value['Name']]['Header']['Back-Color'] ;      

      if($cStyle <> "") $cStyle = ' style="' . $cStyle . '" ' ;
      $html .= '<td ' . $cStyle . ' onClick="' . $this->Name . '.HeadClick(' . $nColNum . ');" id="' . $cCollID . '" class="dbg_cell_header no_txt_select '. $this->Name . '_' . $nColNum . '"  align="' . $cAlign . '">' . $cCaption . '</td>' ;
      $nColNum ++ ;
    }
    $html .= '</tr></table>' ; 
    return $html ;
  }

  function writeFooter($vaHead){
    $html = "" ;
    if($this->ShowFooter){
      $html = '<table border="0" cellspacing="0" cellpadding="0" id="tbFooter_' . $this->Name . '" height="100%"><tr>' ;

      $nColNum = 0 ;
      $cBGColor = "#0099ff" ;
      foreach($vaHead as $key=>$value){
        $cCollID = "cell_$nColNum" ;
        
        $cStyle = "" ;
        $nColWidth = $value['Width'] ;
        $cCaption = "&nbsp;" ;
        if(isset($this->Col[$value['Name']]['FooterText'])) $cCaption = $this->Col[$value['Name']]['FooterText'] ;

        // Footer Align
        $cAlign = "center" ;
        if(isset($this->Col[$value['Name']]['FooterAlign'])) $cAlign = $this->Col[$value['Name']]['FooterAlign'] ;

        // Display Kolom Boleh di Hidden
        if(isset($this->Col[$value['Name']]['Display']) && strtolower($this->Col[$value['Name']]['Display']) == "hidden") $cStyle .= ';display:none' ;

        if($cStyle <> "") $cStyle = ' style="' . $cStyle . '" ' ;
        $html .= '<td ' . $cStyle . ' class="dbg_cell_footer no_txt_select '. $this->Name . '_' . $nColNum . '"  align="' . $cAlign . '">' . $cCaption . '</td>' ;
        $nColNum ++ ;
      }
      $html .= '</tr></table>' ;     
    }
    return $html ;
  }

  function writeBody(&$vaColWidth,&$vaColType,&$vaColAlign,&$vaColDisp,&$vaColEdit,&$vaColName){
    $vaHead = array() ;
    $html = '' ;
    $nRow = 0 ;
    $lWithData = true ;
    if(empty($this->Array) && !empty($this->AddColumn)){
      $this->Array = array()  ;
      foreach($this->AddColumn as $key=>$value){
        $this->Array [0][$value] = " " ;
        $lWithData = false ; 
      }
    }
    foreach($this->Array as $key=>$value){      
      $vaRow = $value ;
      $html .= '<tr onClick="' . $this->Name . '.ClickRow(this)">' ;
      $cRow = "['" . str_replace("--~--","','",str_replace("'","\'",implode("--~--",$value))) . "']" ;
      $nColNum = 0 ;
      foreach($vaRow as $nCol=>$cCol){
        $cAlign = "left" ;        
        if(isset($this->Col[$nCol]['Align'])) $cAlign = $this->Col[$nCol]['Align'] ;

        $cColWidth = "" ;        
        if(!isset($this->Col[$nCol]['Width'])) $this->Col[$nCol]['Width'] = 100 ;
        if(!isset($this->Col[$nCol]['Edit'])) $this->Col[$nCol]['Edit'] = false ;
        $cCellEdit = "false" ;
        if($this->Col[$nCol]['Edit']) $cCellEdit = "true" ;
        $nColWidth = $this->Col[$nCol]['Width'] ;

        $vaColEdit [$nCol] = $cCellEdit ;
        $vaColName [$nCol] = $nCol ;
        $vaColType [$nCol] = "text" ;
        $vaColWidth [$nCol] = $this->Col[$nCol]['Width'] ;
        if(isset($this->Col[$nCol]['Type'])) $vaColType [$nCol] = strtolower($this->Col[$nCol]['Type']) ;        

        if(isset($this->Col[$nCol]['Type']) && strtoupper($this->Col[$nCol]['Type']) == "NUMBER"){
          if($cCol == 0){
            $cCol = "" ;
          }else{
            $cCol = number_format($cCol,2) ;
          }
          $cAlign = "Right" ;
        }
        $vaColAlign [$nCol] = $cAlign ;

        $vaHead [$nCol]['Name'] = $nCol ;
        $vaHead [$nCol]['Width'] = $nColWidth ;
        $vaHead [$nCol]['Type'] = "Text" ;
        $conClick = $this->onClick ;
        if(empty($this->onClick)) $conClick = $this->Name . '.ClickCell(this);' ;
        $cColValue = $cCol ;

        if(isset($this->Col[$nCol]['Type'])){
          $cColType = strtolower($this->Col[$nCol]['Type']) ;
          $vaHead [$nCol]['Type'] = $cColType ;
          if($cColType == "checkbox" || $cColType == "radiobutton"){
            $cChecked = "uncheck.gif" ;
            if($cCol == 1 || $cCol == true) $cChecked = "check.gif" ;
            if($cColType == "radiobutton") $cChecked = "radio-" . $cChecked ;
            $cColValue = '<img style="padding-top:1px" id="ck_cell_' . $nRow . '_' . $nColNum . '" src="' . compFolder() . '/dbgrid/images/' . $cChecked . '" border="0" onClick="' . $this->Name . '".cbClick(' . $nRow . ',' . $nColNum . ')" onMouseOver="if(typeof checkOver==\'function\') checkOver(this,' . $nRow . ',' . $nColNum . ')" onMouseOut="if(typeof checkOut==\'function\') checkOut(this,' . $nRow . ',' . $nColNum . ')">' ;
          }
        }

        // Display Setting 
        $cStyle = "" ;
        $vaColDisp [$nCol] = "show" ;
        if(isset($this->Col[$nCol]['Display']) && strtolower($this->Col[$nCol]['Display']) == "hidden"){
          $cStyle = ' style="display:none"' ;
          $vaColDisp [$nCol] = "hidden" ;
        }

        $html .= '<td' . $cStyle . ' oncontextmenu="return ' . $this->Name . '.conMenu(this)" class="dbg_cell_body ' . $this->Name . '_' . $nColNum . '" align="' . $cAlign . '"' . $cColWidth . ' ondblclick="' . $this->Name . '.ClickCell(this,true)" onClick="' . $conClick . '">' . $cColValue . '</td>' ;
        $nColNum ++ ;
      }
      $html .= '</tr>' ;
      $nRow ++ ;
    }
    // Jika hanya menggunakan AddTable tr tidak usah di tuliskan
    if(!$lWithData) $html = '' ;

    $html = '<table border="0" cellspacing="0" cellpadding="0" id="tbBody_' . $this->Name . '" height="100%">' . $html . '</table>' ;
    return array($html,$vaHead) ;
  }
  
  function GridContent($cXMLContent){
    $vaRow = split("~~rw~~",$cXMLContent) ;
    $vaHead = split("~~cl~~",$vaRow [0]) ;
    $vaRetval = array() ;
    foreach($vaRow as $key=>$value){
      if($key > 0){
        $vaCol = split("~~cl~~",$value) ;
        foreach($vaCol as $key1=>$value1){
          if(isset($vaHead [$key1])){
            $vaRetval [$key-1][$vaHead [$key1]] = $value1 ;
          }
        }
      }
    }
    return $vaRetval ;
  }

  function LoadArray($vaArray,$cGridName='DBGRID1'){
    echo("$cGridName.DeleteRowAll() ;") ;
    if(!empty($vaArray)){
      foreach($vaArray as $key=>$value){
        $vaRow = "['" . join("','",$value) . "']" ;
        echo("$cGridName.AppendRow($vaRow) ;") ;
      }
    }
    echo("if(typeof " . $cGridName . "_onAfterLoadArray == 'function') " . $cGridName . "_onAfterLoadArray() ;");
  }
}
$dbg = new DBGrid ;
?>