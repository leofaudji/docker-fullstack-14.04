<?php
class TextBox{
  var $MaxLength = 0 ;
  var $Width = 20 ;
  var $Value = "" ;
  var $Name = "" ;
  var $ReadOnly = false ;  
  var $Type = "Text" ;
  var $Button = false ;
  var $ButtonClick="" ;
  var $onButtonClick="" ;
  var $Caption = "" ;
  var $LCaption = "" ;
  var $LCapWidth = "100px" ;
  var $LCapSemiColon = true ;
  var $Checked = false ;
  var $Class = "" ;
  var $Style = "" ;
  var $Disabled = false ;
  var $ID = 0 ;

  var $onClick = "" ;
  var $onBlur="" ;
  var $onChange = "" ;
  var $onDblClick = "" ;
  var $onFocus = "" ;
  var $onKeyDown = "" ;
  var $onKeyPress = "" ;
  var $onKeyUp = "" ;
  var $onMouseDown = "" ;
  var $onMouseMove = "" ;
  var $onMouseOut = "" ;
  var $onMouseOver = "" ;
  var $onMouseUp = "" ;
  var $onSelect = "" ;

  function myDir(){
    $cDir = dirname(__FILE__) ;
    $cRoot = $_SERVER['DOCUMENT_ROOT'] ;
    $cDir = str_replace($cRoot,"",$cDir) ;
    return $cDir ;
  }

  function Init(){
    $this->MaxLength = 0 ;
    $this->Width = 20 ;
    $this->Value = "" ;
    $this->Name = "" ;
    $this->ReadOnly = false ;  
    $this->Disabled = false ;
    $this->Type = "Text" ;
    $this->Button = false ;
    $this->ButtonClick="" ;
    $this->onButtonClick="" ;
    $this->Caption = "" ;
    $this->LCaption = "" ;
    $this->LCapWidth = "100px" ;
    $this->LCapSemiColon = true ;
    $this->Checked = false ;
    $this->Class = "" ;
    $this->Style = "" ;

    $this->onBlur="" ;
    $this->onChange = "" ;
    $this->onClick = "" ;
    $this->onDblClick = "" ;
    $this->onFocus = "" ;
    $this->onKeyDown = "" ;
    $this->onKeyPress = "" ;
    $this->onKeyUp = "" ;
    $this->onMouseDown = "" ;
    $this->onMouseMove = "" ;
    $this->onMouseOut = "" ;
    $this->onMouseOver = "" ;
    $this->onMouseUp = "" ;
    $this->onSelect = "" ;
  }

  function updPar($cFunc){
    return rawurlencode(str_replace('this',"a.getById('txt-" . $this->ID . "')",$cFunc)) ;
  }

  function HiddenField($cName='',$cValue=''){
    $this->Type = "hidden" ;
    $this->Show($cName,$cValue) ;
  }

  function ButtonField($cName='',$cValue='',$lReadOnly=''){
    $this->Type = "Button" ;
    $this->Class = "Button" ;
    $this->Show($cName,$cValue,0,0,$lReadOnly) ;
  }

  function NumberField($cName='',$cValue='',$nMaxLength=0,$nWidth=0,$lReadOnly=''){
    $this->Type = "Number" ;
    $this->Show($cName,$cValue,$nMaxLength,$nWidth,$lReadOnly) ;
  }

  function DateField($cName='',$cValue='',$lReadOnly=''){
    $this->Type = "Date" ;
    $this->Show($cName,$cValue,0,0,$lReadOnly) ;
  }

  function RadioButton($cName='',$cValue='',$lReadOnly=''){
    $this->Type = "Radio" ;
    $this->Show($cName,$cValue,0,0,$lReadOnly) ;
  }

  function CheckBox($cName='',$cValue='',$lReadOnly=''){
    $this->Type = "CheckBox" ;
    $this->Show($cName,$cValue,0,0,$lReadOnly) ;
  }

  function Show($cName='',$cValue='',$nMaxLength=0,$nWidth=0,$lReadOnly=''){
    $cTxtType = strtoupper($this->Type) ;
    if(empty($cName)) $cName = $this->Name ;
    if($cValue == '') $cValue = $this->Value ;
    if(empty($nMaxLength)) $nMaxLength = $this->MaxLength ;
    if(empty($nWidth)) $nWidth = $this->Width ;
    if(empty($lReadOnly)) $lReadOnly = $this->ReadOnly ;
    $this->Name = $cName ;
    $this->ID = _txtID() ;

    $cReadOnly = "" ;
    if($lReadOnly ) $cReadOnly = ' readOnly="true"' ;
    if($this->Disabled) $cReadOnly = ' disabled' ;

    $cStyle = "" ;
    if($cTxtType == "NUMBER"){
      $cStyle = ";text-align:right" ;      
      if($cValue == "") $cValue = "0" ;
    }

    $cButton = "" ;
    $cImgName = $this->myDir() . "/images/pick-button.gif" ;
    if($this->Button || $cTxtType == "DATE" ){
      $cLink = "" ;
      if(strtoupper($this->Type) == "DATE"){
        $cStyle .= ";width:85px" ;
        $cLink = "showCal(document.form1." . $cName . ")" ;
        $nWidth = 10 ;
        $nMaxLength = 10 ;
        $cImgName = $this->myDir() . "/images/date-button.gif" ;
      }else if($this->ButtonClick == ''){
        $this->ButtonClick = $this->onButtonClick ;
      }
      $cButton = '<img class="input-button" src="' . $cImgName . '" onMouseDown="txt.bmd(\'' . $this->ID . '\');" onClick="txt.bClick(\'' . $this->ID . '\',\'' . $cTxtType . '\',\'' . $this->updPar($this->ButtonClick) . '\')" align="top">' ;
    }

    $cClass = "" ;
    if(!empty($this->Class)) $cClass = ' Class="' . $this->Class . '"' ;

    $cType = "Text" ;
    $cSize = ' size="' . $nWidth . '" ' ;
    $cMaxLength = "" ;
    if(!empty($nMaxLength)) $cMaxLength = ' maxlength="' . $nMaxLength . '" ' ;
    $cChecked = "" ;
    if($cTxtType == "RADIO"){
      $cStyle = ";height:14px;width:14px;border-width:0px" ;
      $cType = "Radio" ;
      $cSize = '' ;
      $cMaxLength = '' ;
      if($this->Checked) $cChecked = ' checked ' ;
    }else if($cTxtType == "CHECKBOX"){  // Check Box      
      $cStyle = ";height:14px;width:14px;border:1px solid #b8bab3" ;
      $cType = "Checkbox" ;
      $cSize = '' ;
      $cMaxLength = '' ;
      if($this->Checked) $cChecked = ' checked ' ;
    }else if($cTxtType  == "BUTTON"){    // Jika Button
      $this->Button = false ;
      $cType = "Button" ;
      $cSize = '' ;
      $cMaxLength = '' ;
    }else if($cTxtType  == "HIDDEN"){
      $cType = "hidden" ;
      $cSize = '' ;
      $cMaxLength = '' ;
      $cClass = '' ;
      $cChecked = '' ;      
    }else if($cTxtType == "PASSWORD"){
      $cType = "Password" ;
    }else if($cTxtType == "FILE"){
      $cType = "file" ;
    }
    $va = array("onBlur"=>"return txt.onBlur(this,'" . $cTxtType . "','" . $this->updPar($this->onBlur) . "')",
                "onFocus"=>"txt.onFocus(this,'" . $this->updPar($this->onFocus) . "')",
                "onKeyDown"=>"txt.keyDown(this,event,'" . $this->updPar($this->onKeyDown) . "')",
                "onKeyPress"=>"return txt.keyPress(this,event,'" . $cTxtType . "','" . $this->updPar($this->onKeyPress) . "')",
                "onKeyUp"=>"txt.keyUp(this,event,'" . $cTxtType . "','" . $this->updPar($this->onKeyUp) . "')",
                "onMouseOver"=>"txt.mOver(this,'" . $this->updPar($this->onMouseOver) . "');" ,
                "onMouseMove"=>$this->onMouseMove,"onMouseOut"=>$this->onMouseOut,"onClick"=>$this->onClick,"onMouseDown"=>$this->onMouseDown,
                "onMouseUp"=>$this->onMouseUp,"onDblClick"=>$this->onDblClick,"onChange"=>$this->onChange,"onSelect"=>$this->onSelect) ;
    $cEvent = " " ;
    foreach($va as $key=>$value){
      if($value <> ""){
        if(strtolower(substr($value,0,5)) == "ajax:"){
          $cFunction = substr($value,5) ;
          $value = "txt.onAjax('$cFunction');" ;
        }
        $cEvent .= $key . '="' . $value . '" ' ;
      }
    }

    if(trim($this->Style) !== "") $cStyle .= ";" . $this->Style ;
    $nStyleWidth = strpos(strtolower($cStyle),"width") ;
    if($nStyleWidth === false){
      $nWidthStyle = $nWidth ;
      if($nWidthStyle == 0) $nWidthStyle = 20 ;
      if($cTxtType == "TEXT" || $cTxtType == "NUMBER" || $cTxtType == "PASSWORD"){
        $cStyle .= ";width:" . (($nWidthStyle*7) + 18) . "px;" ;
      }else if($cTxtType == "FILE"){
        $cStyle .= ";width:" . (($nWidthStyle*6) + 100) . "px;" ;
      }
    }

    $_c = ' ' . $cSize . $cMaxLength . $cChecked . $cReadOnly . $cEvent . $cClass . ' Style="' . $cStyle . '"' ;
    if($cTxtType == "HIDDEN") $_c = "" ;

    $cSpan = "" ;
    $cClass = "" ;
    $cLabel = "" ;
    $cLabel1 = "" ;
    if($cTxtType == "CHECKBOX" || $cTxtType == "RADIO"){
      $cSpan = '<span></span>' ;
      $cClass = ' class="input-check" ' ;
      $cLabel = "<label>" ;
      $cLabel1 = "</label>" ;
    }
    $cInput = ' <input ' . $cClass . 'id="txt-' . $this->ID . '" name="' . $cName . '" type="' . $cType . '" value="' . $cValue . '"' . $_c . '>' . $cSpan ;
    $cCaption = '' ;
    if($this->Caption <> "") $cCaption = '<span style=\'vertical-align:text-top; height:20px\'> ' . $this->Caption . ' </span>' ;

    $cLCap = "" ;
    if($this->LCaption <> ""){
      $cLCap = '<input name="Cap_' . $cName . '" disabled readOnly style="width:' . $this->LCapWidth . ';background:transparent;border:0px;color:#000000;padding:1px 1px 1px 1px" value="' . $this->LCaption . '">' ;
      if($this->LCapSemiColon){
        $cLCap .= '<span style=\"vertical-align:middle; height:20px\">:</span>' ;
      }
    }
    echo($cLabel . $cLCap . '<span style="height:20px">' . $cInput . $cButton . '</span>' . $cCaption . $cLabel1) ;

    $this->Init() ;
  }
}
$txt = new TextBox ;

function _txtID(){
  static $nID = 0 ;
  return ++$nID . "-" . rand(0,999999) ;
}
?>