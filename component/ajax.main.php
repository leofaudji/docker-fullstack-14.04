<?php
  include 'df.php' ;
?>
var cRetval = "" ;
var loadedobjects = "" ;
function ajax(url,cKey,cParameter){a.ajax(url,cKey,cParameter)}
function URL_Ajax(){return a.urlByName() ;}
function __onKeyPress(e){return txt.keyNum(e)}
function validate(field,e){txt.keyEnter(field,txt.keyNum(e))}
function GetFormContent(par=null){return a.fContent(par) ;}

function loadpageornot(url,containnerid){
var div = a.getById(containnerid) ;
  if(div !== null){
    if(div.innerHTML == ""){
      a.ajax(url,"","",function(cData,nStatus){div.innerHTML = cData}) ;
    }else{
      div.innerHTML = "" ;
    }
  }
  return false ;
}

function loadpage(url,containerid,param){
  a.ajax(url,"",param,function(cData,cStatus){
    var o = a.getById(containerid) ;
    if(o !== null) o.innerHTML = cData ;
  }) ;
}

function dragStart(oRow,event, id) {
  var o = a.getById(id) ;
  if(o !== null)a.obj_move_start(o,event) ;
}

function SetOpt(opt,cValue){  
  for(n=0;n<opt.length;n++){
    if(opt[n].value == cValue){
      opt[n].checked = true ;
    }
  }
}

function GetOpt(opt){
var cRetval = "" ;
  for(n=0;n<opt.length;n++){
    if(opt[n].checked){
      cRetval = opt[n].value ;
    }
  }  
  return cRetval ;
}

function readCookie(name){
  var cookieValue = "";
  var search = name + "=";
  if(document.cookie.length > 0){ 
    offset = document.cookie.indexOf(search);
    if (offset != -1){ 
      offset += search.length;
      end = document.cookie.indexOf(";", offset);
      if (end == -1) end = document.cookie.length;
      cookieValue = unescape(document.cookie.substring(offset, end)) ;
    }
  }
  return cookieValue;
}

function writeCookie(name, value, hours){
  var expire = "";
  if(hours == null){
    expire = new Date((new Date()).getTime() + hours * 3600000);
    expire = "; expires=" + expire.toGMTString();
  }
  document.cookie = name + "=" + escape(value) + expire;
}

function Number2String(nNumber,nDecimals){  
  var n = 0 ;
  var cNumber = "" ;
  var cDigit = "" ;
  var nDigit = 0 ;
  var cRetval = "" ;
  var nLen = 0 ;
  var i = 0 ;
  var cSplit = "" ;
  var nCount = 0 ;

  if (Number2String.arguments.length == 1){
    nDecimals = 2 ;
  }  

  nCount = "00000000000000000000000000000" ;
  if (nNumber == ""){
    cRetval = "0" ;
    if(nDecimals > 0) cRetval = cRetval + "." + nCount.substring(0,nDecimals) ;
    return cRetval ;
  }
  nCount = "1" + nCount.substr(0,nDecimals) ;
  nCount = parseFloat(nCount) ;  
  n = Math.round(String2Number(nNumber) * nCount) ;
  n = n / nCount ;  
  cNumber = n.toString() ;  
  nDigit = cNumber.indexOf(".",1) ;
  // Periksa apakah ada Koma Untuk Bilangan tersebut
  if (nDigit < 0){
    if (nDecimals !== 0){
      cDigit = ".00" ;
    }else{
      cDigit = "" ;
    }
  }else{
    cDigit = cNumber.substring(nDigit) ;
    cNumber = cNumber.substring(0,nDigit) ;    
    if (cDigit.length < 3){
      cDigit = cDigit + "0" ;
    }
  }
  cRetval = "" ;
  nLen = cNumber.length ;
  for(i=nLen - 3;i > -3;i -= 3){
    cSplit = cNumber.substring(i,i+3) ;    
    if (cSplit !== ""){
      cRetval =  cSplit + "," + cRetval ;
    }
  }
  cRetval = cRetval.substring(0,cRetval.length -1) ;
  return cRetval + cDigit ;
}

function String2Number(cString){
  var i;
  var cRetval = "";
  var ValidChars = "0123456789." ;
  var cChar = "" ;
  cString = cString.toString() ;
  for(i=0;i<cString.length;i++){
    cChar = cString.charAt(i) ;    
    if (ValidChars.indexOf(cChar) >= 0){
      cRetval = cRetval + cChar ;
    }
  }
  cRetval=parseFloat(cRetval);
  return cRetval;
}

function fieldfocus(field){
var nEnd = field.value.length ;

  field.focus() ;
  if(field.type == "text"){
    if(browserType() == "ie"){
      var range = field.createTextRange();
      range.collapse(true);
      range.moveStart("character", 0);
      range.moveEnd("character", nEnd);
      range.select();
    }else{
      field.selectionStart = 0 ;
      field.selectionEnd = nEnd ;
    }
  }
}

function browserType(){
var is_gecko = /gecko/i.test(navigator.userAgent);
var is_ie    = /MSIE/.test(navigator.userAgent);
  if(is_gecko){
    return "firefox" ;
  }else if(is_ie){
    return "ie" ;
  }else{
    return "" ;
  }
}

var fieldX=0, fieldY=0;
function fieldPos(elm,w){
var x = 0, y = 0;
  var el = elm ;
  var n = 0 ;
  while(el !== null && n++ < 50){
    if(typeof el.tagName == "string" && el.tagName.toLowerCase() == "div"){
      if(typeof el.scrollTop == "number") y -= parseInt(el.scrollTop) ;
    }
    el = el.parentNode ;
  }
  x += elm.offsetLeft + 1;
  y += elm.offsetTop + 1 ;

  elm = elm.offsetParent;
  while(elm != null){
    x = parseInt(x) + parseInt(elm.offsetLeft);
    y = parseInt(y) + parseInt(elm.offsetTop);
    elm = elm.offsetParent;
    
    if(elm == null && w.name !== ""){
      var cWinName = w.name ;
      w = w.self.parent ;
      elm = w.a.getById(cWinName) ;
      
      // Lihat Kalau ada Header maka kita ambil Tinggi header nya
      if(w.name !== "mainFrame"){
        var oHead = w.a.getById(cWinName + "-header") ;
        if(oHead !== null){
          x = parseInt(x) + 3 ;
          y = parseInt(y) + parseInt(oHead.offsetHeight) + 1;
        }
      }
    }
  }
  fieldX = x ;
  fieldY = y ;
  return [fieldX,fieldY]
}

function Browse(cSQL,cField){
  var field = cField ;
  if(typeof cField == "string") eval("field = document.form1." + cField) ;
  a.Browse(cSQL,field) ;
}

var nDivIndex = 0 ;
function setObjIndex(obj){
  if(obj.style.zIndex == "" || nDivIndex < 0 || obj.style.zIndex < nDivIndex){
    obj.style.zIndex = ++nDivIndex ;
  }
}

function OpenForm(URL,cFormName,cTitle,nWidth,nHeight,cBackColor,lShowModal,cFormScroll,lHideToolBox,cFrameName,lReport){
  frm.open(URL,cFormName,cTitle,nWidth,nHeight,cBackColor,lShowModal,cFormScroll,lHideToolBox,cFrameName,lReport) ;
}

function CloseForm(cFormName){
  frm.close(cFormName) ;
}

var cReportURL = "" ;
function OpenReport(URL,lPrintDialog){
  cReportURL = URL ;  
  if(lPrintDialog){
    var cName = window.name ;
    self.parent.OpenForm("main.php?__par=" + __COMPONENT_FOLDER__ + "/frmprintdialog.php&cFormName="+cName,"FrmPrintDialog","Report Type",500,320,'',true,'no',true,'',true) ;
  }else{
    __OpenReport() ;
  }
}

function __OpenReport(){
var now = new Date();
var cTime = now.getTime() ;

  var nWidth = Math.min(self.parent.innerWidth,screen.width) - 20;
  var nHeight = Math.min(self.parent.innerHeight,screen.height) - 10;
  var cPar = "&"+GetFormContent() ;
  self.parent.OpenForm(cReportURL+cPar,"Report"+cTime,"Laporan",nWidth,nHeight,'',false,'no',false,'',true) ;
}

function _Ajax_Event(cFunction){
  ajax('',cFunction,GetFormContent()) ;
}

function IsNumeric(sText,lComma){
var ValidChars = "0123456789.-";
var cChar = 0 ;
var IsNumber = true ;
  if(lComma) ValidChars += "," ;
  for (i = 0; i < sText.length && IsNumber == true; i++){ 
    cChar = sText.charAt(i) ; 
    if (ValidChars.indexOf(cChar) == -1){
      IsNumber = false ;
    }
  }
  return IsNumber ;
}

function CheckNumber(field){
  if(!IsNumeric(field.value,true)){
    alert("Invalid Number ....!") ;
    field.value = 0 ;
    fieldfocus(field) ;
    return false ;
  }
  return true ;
}

function Date2String(dTgl){
  cRetval = dTgl.substring(0,10) ;
  va = dTgl.split("-") ;
  // Jika Array 1 Bukan Tahun maka akan berisi 2 Digit
  if(va [0].length == 2 && va.length >= 3){
    cRetval = va [2] + "-" + va [1] + "-" + va[0] ;
  }
  return cRetval ;
}

function String2Date(cString){
  cRetval = cString.substring(0,10) ;
  va = cString.split("-") ;
  // Jika Array 1 Tahun maka akan berisi 4 Digit
  if(va [0].length == 4 && va.length >= 3){
    cRetval = va [2] + "-" + va [1] + "-" + va[0] ;
  }
  return cRetval ;
}

function __cf(cKey,field){
var cFunc = field.name + "_" + cKey ;
  if(eval("typeof " + cFunc) == "function") eval(cFunc + "(field);") ; ;
}

function ShowAlert(){}