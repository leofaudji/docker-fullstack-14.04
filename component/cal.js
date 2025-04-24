var fontSize=9;
var titleWidth=110;
var dayWidth=25;
var dayDigits=3;
var titleColor="#cccccc";
var daysColor="#cccccc";
var bodyColor="#ffffff";
var dayColor="#ffffff";
var currentDayColor="#333333";
var footColor="#cccccc";
var borderColor="#666666";
var titleFontColor = "#333333";
var daysFontColor = "#333333";
var dayFontColor = "#333333";
var currentDayFontColor = "#ffffff";
var calFormat = "dd-mm-yyyy";
var weekDay = 0;
var calWin=null;
var oMain=null ;
var cals=new Array();
var currentCal=null;
var yxMonths=new Array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "Desember");
var yxDays=new Array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
var isOpera=(navigator.userAgent.indexOf("Opera")!=-1)?true:false;
var isOpera5=(navigator.appVersion.indexOf("MSIE 5")!=-1 && navigator.userAgent.indexOf("Opera 5")!=-1)?true:false;
var isOpera6=(navigator.appVersion.indexOf("MSIE 5")!=-1 && navigator.userAgent.indexOf("Opera 6")!=-1)?true:false;
var isN6=(navigator.userAgent.indexOf("Gecko")!=-1);
var isN4=(document.layers)?true:false;
var isMac=(navigator.userAgent.indexOf("Mac")!=-1);
var isIE=(document.all && !isOpera && (!isMac || navigator.appVersion.indexOf("MSIE 4")==-1))?true:false;
var lShow=false;
var lMouseOut=true ;
var dField ;
if(isN4){fontSize+=2}

function moveYear(dy){cY+=dy;var nd=new Date(cY,cM,1);changeCal(nd)}
function getDayName(y,m,d){var wd=new Date(y,m,d);return yxDays[wd.getDay()].substring(0,3)}
function getLeftN4(l) {return l.pageX}
function getTopN4(l) {return l.pageY}
function getLeftN6(l) {return l.offsetLeft}
function getTopN6(l) {return l.offsetTop}
function hideCal(){lShow = false;if(oMain !== null){oMain.style.display="none"}}
function checkHideCal(){if(lMouseOut&&lShow){hideCal()}}
function calOBJ(name, title, field, form){this.name = name;this.title = title;this.field = field;this.formName = form;this.form = null}
function firstDay(d){var yy=d.getFullYear(), mm=d.getMonth();var fd=new Date(yy,mm,1);return fd.getDay()}
function getMonthFromName(m3){for (var i = 0; i < yxMonths.length; i++){if (yxMonths[i].toLowerCase().substring(0,3) == m3.toLowerCase()){return i}}return 0}
function dayDisplay(i){if(dayDigits == 0){return yxDays[i]}else{return yxDays[i].substring(0,dayDigits)}}
function get2Digits(n){return ((n<10)?"0":"")+n}

function getFormat() {
  var calF = calFormat;

  calF = calF.replace(/\\/g, '\\\\');
  calF = calF.replace(/\//g, '\\\/');
  calF = calF.replace(/\[/g, '\\\[');
  calF = calF.replace(/\]/g, '\\\]');
  calF = calF.replace(/\(/g, '\\\(');
  calF = calF.replace(/\)/g, '\\\)');
  calF = calF.replace(/\{/g, '\\\{');
  calF = calF.replace(/\}/g, '\\\}');
  calF = calF.replace(/\</g, '\\\<');
  calF = calF.replace(/\>/g, '\\\>');
  calF = calF.replace(/\|/g, '\\\|');
  calF = calF.replace(/\*/g, '\\\*');
  calF = calF.replace(/\?/g, '\\\?');
  calF = calF.replace(/\+/g, '\\\+');
  calF = calF.replace(/\^/g, '\\\^');
  calF = calF.replace(/\$/g, '\\\$');

  calF = calF.replace(/dd/i, '\\d\\d');
  calF = calF.replace(/mm/i, '\\d\\d');
  calF = calF.replace(/yyyy/i, '\\d\\d\\d\\d');
  calF = calF.replace(/day/i, '\\w\\w\\w');
  calF = calF.replace(/mon/i, '\\w\\w\\w');

  return new RegExp(calF);
}

function getDateNumbers(date){
  var y, m, d;

  var yIdx = calFormat.search(/yyyy/i);
  var mIdx = calFormat.search(/mm/i);
  var m3Idx = calFormat.search(/mon/i);
  var dIdx = calFormat.search(/dd/i);

  y=date.substring(yIdx,yIdx+4)-0;
  if (mIdx != -1) {
    m=date.substring(mIdx,mIdx+2)-1;
  }else{
    var m = getMonthFromName(date.substring(m3Idx,m3Idx+3));
  }
  d=date.substring(dIdx,dIdx+2)-0;

  return new Array(y,m,d);
}

function lastDay(d) {
  var yy=d.getFullYear(), mm=d.getMonth();
  for (var i=31; i>=28; i--){
    var nd=new Date(yy,mm,i);
    if (mm == nd.getMonth()){
      return i;
    }
  }
}

function lastDayPreviouse(d){
  var yy=d.getFullYear(), mm=d.getMonth()-1;
  if(mm < 0){
    yy -- ;
    mm = 11 ;
  }
  for (var i=31; i>=28; i--){
    var nd=new Date(yy,mm,i);
    if (mm == nd.getMonth()){
      return i;
    }
  }
}

function calTitle(d){
  cStyle = "color:"+dayFontColor+"; text-decoration:none;font-size:"+fontSize ;
  var yy=d.getFullYear(), mm=yxMonths[d.getMonth()];
  var s;

  s="<tr align='center' bgcolor='"+titleColor+"'><td colspan='7'><table style='-moz-opacity:0.9;opacity:.9' cellpadding='0' cellspacing='0' border='0'><tr align='center' valign='middle'><td><b><a href='javascript:moveYear(-1)' style='"+cStyle+"'>&nbsp;&#171;</a>&nbsp;<a href='javascript:prepMonth("+d.getMonth()+")' style='"+cStyle+"'>&#139;&nbsp;</a></b>"+"</td><td width='"+titleWidth+"' style='"+cStyle+"'><nobr><b>"+mm+" "+yy+"</b></nobr></td><td><b><a href='javascript:nextMonth("+d.getMonth()+")' style='"+cStyle+"'>&nbsp;&#155;</a>&nbsp;<a href='javascript:moveYear(1)' style='"+cStyle+"'>&#187;&nbsp;</a></b>"+"</td></tr></table></td></tr><tr align='center' bgcolor='"+daysColor+"'>";
  for (var i=weekDay; i<weekDay+7; i++) {
    cFontColor = dayFontColor ;
    if(i==0){cFontColor = "#FF0000"}
    cStyle = "color:"+cFontColor+"; text-decoration:none;font-size:"+fontSize+";cursor:default;" ;
    s+="<td width='"+dayWidth+"' style='"+cStyle+"'>"+dayDisplay(i)+"</td>";
  }
  s+="</tr>";

  return s;
}

function calHeader(){return "<table style='-moz-opacity:0.9;opacity:.9' onMouseOver='lMouseOut=false;' onMouseOut='lMouseOut=true;' id='__CalenderMainTable__' align='center' border='0' bgcolor='"+borderColor+"' cellspacing='0' cellpadding='0'><tr><td><table cellspacing='1' cellpadding='3' border='0'>"}
function calFooter(){
  var cStyle = "color:"+cFontColor+"; text-decoration:none;font-size:"+fontSize ;
  return "<tr bgcolor='"+footColor+"'><td colspan='7' align='center'><b><a href='javascript:setToDay()' style='"+cStyle+"'>[ Today : " + getCurrentDate() + " ]</a></b>"+"</td></tr></table></td></tr></table>";
}

function cellOver(cell){  
  if(typeof cell.tag == 'undefined'){cell.tag = cell.bgColor + '~' + cell.style.color}
  cell.bgColor = "#316AC5" ;
  cell.style.color = "#ffffff" ;
}

function cellOut(cell){
  if(typeof cell.tag !== 'undefined'){
    var va = cell.tag.split('~') ;
    cell.bgColor = va[0] ;
    cell.style.color = va[1] ;
  }
}

function calBody(d,day){
  var s="", dayCount=1, fd=firstDay(d), ld=lastDay(d);
  var nNextMonth = 0 ;
  var nLastDayPrev = lastDayPreviouse(d) ;
  if (weekDay > 0 && fd == 0) {
    fd = 7;
  }  
  nLastDayPrev -= fd - 1 ;
  for (var i=0; i<6; i++) {    
    s+="<tr align='center' bgcolor='"+bodyColor+"'>";
    for (var j=weekDay; j<weekDay+7; j++){      
      var bgColor=dayColor;
      var fgTag="day";
      var fgTagA="daya";
      if(j==0){
        cFontColor = "#FF0000" ;
      }else{
        cFontColor = dayFontColor ;
      } 
      cStyle = "color:"+cFontColor+";cursor:default; text-decoration:none;font-size:"+fontSize ;
      if (i*7+j<fd) {
        s+="<td bgcolor='DDDDDD' onMouseOut='javascript:cellOut(this)' onMouseOver='javascript:cellOver(this)' style='"+cStyle+"' onMouseDown='javascript:pickDate("+nLastDayPrev+",-1)'>"+nLastDayPrev+"</td>";
        nLastDayPrev ++ ;
      }else if(dayCount>ld){
        nNextMonth ++ ;
        s+="<td bgcolor='DDDDDD' onMouseOut='javascript:cellOut(this)' onMouseOver='javascript:cellOver(this)' style='"+cStyle+"' onMouseDown='javascript:pickDate("+nNextMonth+",1)'>"+nNextMonth+"</td>";
      }else{
        if (dayCount==day) { 
          bgColor=currentDayColor; 
          fgTag="currentDay";
          fgTagA="currenta";
          cFontColor = currentDayFontColor ;          
        }     
        cStyle = "color:"+cFontColor+"; text-decoration:none;cursor:default;font-size:"+fontSize ;
        cTitle = yxDays[j] + ", " + dayCount + " " + yxMonths[d.getMonth()] + " " + d.getFullYear() ;
        s+="<td title='"+cTitle+"' bgcolor='"+bgColor+"' onMouseOut='javascript:cellOut(this)' onMouseOver='javascript:cellOver(this)' style='"+cStyle+"' onMouseDown='javascript:pickDate("+dayCount+")'>"+dayCount+"</td>";
        dayCount ++ ;
      }
    }
    s+="</tr>";
  }

  return s;
}

function setToDay(){
var d = new Date() ;
var a = 0 ;

  cM = d.getMonth() ;
  cY = d.getFullYear() ;
  a = d.getDate()
  pickDate(a) ;
}

function prepMonth(m) {
  cM=m-1;
  if(cM<0){cM=11;cY--;}
  var nd=new Date(cY,cM,1);
  changeCal(nd);
}

function nextMonth(m){cM=m+1;if (cM>11) { cM=0; cY++;}var nd=new Date(cY,cM,1);changeCal(nd)}
function CheckDate(field){
  if(!isDateValided(field.value)){
    alert("Tanggal Tidak Valid ....") ;
    fieldfocus(field) ;
  }
}

function isDateValided(date){
var calRE = getFormat();
  lRetval = false ;
  if(calRE.test(date)){
    var va = getDateNumbers(date) ;
    var d = new Date(va[0],va[1],va[2]);
    if(parseInt(va[0])==d.getFullYear() && parseInt(va[1])==d.getMonth() && parseInt(va[2])==d.getDate()){
      lRetval = true ;
    }
  }
  return lRetval ;
}

function changeCal(d){
var dd=0;

  if(currentCal != null){
    if(dField.value!="" && isDateValided(dField.value)){
      var cd = getDateNumbers(dField.value);
      if (cd[0] == d.getFullYear() && cd[1] == d.getMonth()){
        dd=cd[2];
      }
    }else{
      var cd = new Date();
      if(cd.getFullYear() == d.getFullYear() && cd.getMonth() == d.getMonth()){
        dd=cd.getDate();
      }
    }
  }
  calWin.innerHTML = calHeader()+calTitle(d)+calBody(d,dd)+calFooter();
}

function showCal(field,oWin,lNotCheck) {
  if(!oWin){
    oWin = window ;
  }
  if(window.name !== "mainFrame" && !lNotCheck){
    var w = oWin ;
    var nCount = 0 ;
    while(w.name !== "mainFrame" && w !== null && nCount < 10){
      if(w.name !== w.self.parent.name){
        w = w.self.parent ;
      }else{
        nCount = 10 ;
      }
      nCount ++ ;
    }
    w.showCal(field,oWin,true) ;
  }else{
    fieldPos(field,oWin) ;
    CalOpen(field) ;
  }
}

function CalOpen(field){
var d=new Date();
  dField = field ;
  lShow = true ;
  if (dField.value!="" && isDateValided(dField.value)){
    var cd = getDateNumbers(dField.value);    
    d=new Date(cd[0],cd[1],cd[2]);
    
    cY=cd[0];
    cM=cd[1];
    dd=cd[2];
  }else{
    cY=d.getFullYear();
    cM=d.getMonth();
    dd=d.getDate();
  }
  var nTop = fieldY ;
  var w = window ;
  var _d = w.document ;
  
  oMain = _d.getElementById("__Show_Calendar__Main__") ;    
  if(oMain == null){
    oMain = _d.createElement("div");    
    oMain.id = "__Show_Calendar__Main__" ;
    _d.body.appendChild(oMain ) ;
    oMain.innerHTML = '<div style="position:absolute" id="__Show_Calendar__"></div>' ;
  }
  oMain.style.display = "block" ;
  oMain.style.position = "absolute" ;
  setObjIndex(oMain) ;
  oMain.style.top = 0 ;
  oMain.style.left = 0 ;
  oMain.style.width = _d.body.scrollWidth-5 ;
  oMain.style.height = _d.body.scrollHeight-5 ;
  oMain.onmousedown=checkHideCal ;
  
  calWin = _d.getElementById("__Show_Calendar__") ;
  var cSD = "<div id='__Show_Calendar_SR__' style='position:absolute;display:none;background-color: rgb(142,142,142);-moz-opacity:0.4;opacity:.4'></div>" ;
  cSD += "<div id='__Show_Calendar_SB__' style='position:absolute;display:none;background-color: rgb(142,142,142);-moz-opacity:0.4;opacity:.4'></div>" ;
  calWin.innerHTML = calHeader()+calTitle(d)+calBody(d,dd)+calFooter()+cSD;
  calWin.style.display = "block" ;
  calWin.style.position = "absolute" ;
  calWin.style.top = -500 ;  
  calWin.style.width = 189 ;  
  
  var oo = _d.getElementById("__CalenderMainTable__") ;
  calWin.style.width = oo.offsetWidth+3 ;
  calWin.style.height= oo.offsetHeight+3;
  var nLeft = fieldX ;
  if(nLeft < 0) nLeft = 0 ;
  if(nLeft+oo.offsetWidth+20 > window.innerWidth+_d.body.scrollLeft) nLeft = window.innerWidth+_d.body.scrollLeft - oo.offsetWidth-20 ;
  if(nTop+oo.offsetHeight+20 > window.innerHeight+_d.body.scrollTop) nTop = nTop - 20 - oo.offsetHeight ;

  calWin.style.left = nLeft ;
  calWin.style.top = nTop ;
  
  var oSR = _d.getElementById("__Show_Calendar_SR__") ;
  with(oSR.style){display="block";left=oo.offsetWidth;top=4;width=3;height=oo.offsetHeight-4}
  
  var oSB = _d.getElementById("__Show_Calendar_SB__") ;
  with(oSB.style){display="block";left=4;top=oo.offsetHeight;width=oo.offsetWidth-1;height=3}
}

function pickDate(d,nNext) {
  hideCal();
  window.focus();
  
  var date=calFormat;
  
  if(nNext){
    cM += nNext ;
    if(cM > 11){
      cM = 0 ;
      cY ++ ;
    }else if(cM < 0){
      cM = 11 ;
      cY -- ;
    }
  }
  date = date.replace(/yyyy/i, cY);
  date = date.replace(/mm/i, get2Digits(cM+1));
  date = date.replace(/MON/, yxMonths[cM].substring(0,3).toUpperCase());
  date = date.replace(/Mon/i, yxMonths[cM].substring(0,3));
  date = date.replace(/dd/i, get2Digits(d));
  date = date.replace(/DAY/, getDayName(cY,cM,d).toUpperCase());
  date = date.replace(/day/i, getDayName(cY,cM,d));
  dField.value=date;
  dField.focus();
}

function getCurrentDate(){
  var date=calFormat, d = new Date();
  date = date.replace(/yyyy/i, d.getFullYear());
  date = date.replace(/mm/i, get2Digits(d.getMonth()+1));
  date = date.replace(/dd/i, get2Digits(d.getDate()));

  return date;
}