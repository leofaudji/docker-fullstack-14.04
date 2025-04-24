<?php
  include 'df.php' ;
?>
window.addEventListener('load', function(){a.init()}, false ) ;
var a = {
  m:msg,g:function(){return _grandWin()},
  Browse: function(cSQL,field,callback){txt.Browse(cSQL,field,callback)},
  mnuClick:"",
  f: document.form1,
  init: function(){
    this.f = document.form1 ;
  },
  keyNum: function(e){
    var keynum = 0 ;
    if(window.event){ // IE
      keynum = e.keyCode ;
    }else if(e.which){ // Netscape/Firefox/Opera
      keynum = e.which ;
    }
    return keynum ;
  },
  alert: function(msg,title,callback){
    this.m._show("Alert",msg,title,callback) ;    
  },  
  confirm: function(msg,title,callback){
    this.m._show("Confirm",msg,title,callback) ;
  },
  wait: function(nTimeout,title){
    this.m.waitStart(nTimeout,title) ;
  },
  endwait: function(){
    this.m.waitEnd() ;
  },
  debug:function(cMsg){
    this.m.debug(cMsg) ;
  },
  deb:function(cMsg){
    this.m.debug(cMsg) ;
  },
  delObj:function(obj){
    if(obj !== null && obj.parentNode !== null) obj.parentNode.removeChild(obj) ;
  },
  delById:function(cID){
    var oDiv = a.getById(cID) ;
    this.delObj(oDiv) ;
  },
  getById(id,d){
    if(d == null) d = document ;
    var o = null ;
    if(d.nodeType == 9){
      o = d.getElementById(id) ;
    }else{
      for(var node=0;node<d.childNodes.length;node++){
        if(d.childNodes [node].id == id) o = d.childNodes [node] ;
      }
    }
    return o ;
  },
  addObj(cType,oParent){
    if(oParent == null) oParent = document.body ;
    var o = document.createElement(cType) ;
    oParent.appendChild(o) ;
    return o ;
  },
  // Function Ajax
  ajax: function(url,cKey,cParameter,callBack){
    var page = false ;  
    if(!url || url == "") url = a.urlByName() ;
    if (window.XMLHttpRequest){ // if Mozilla, Safari etc
      page = new XMLHttpRequest() ;
    }else if (window.ActiveXObject){ // if IE
      try {
        page = new ActiveXObject("Msxml2.XMLHTTP") ;
      }catch (e){
        try {
          page = new ActiveXObject("Microsoft.XMLHTTP") ;
        }  catch (e){}
      }
    }else{
      return false
    }

    page.onreadystatechange=function(){
      if(page !== null){
        try {
          if (page.readyState == 4) {
            if (page.status == 200) {
              cRetval = page.responseText ;
              if(callBack){
                callBack(cRetval.trim(),page.status) ;
              }else{
                eval(cRetval) ;
              }
            }
          }
        }catch(e){
          if(e.message.indexOf('NS_ERROR_NOT_AVAILABLE') < 0){
            cRetval = page.responseText ;
            if(callBack){
              callBack(cRetval.trim(),page.status) ;
            }else{
              eval(cRetval) ;
            }
          }
        }
      }
    } ;

    url += "&cKey=" + cKey ;
    if(!cParameter) cParameter = "" ;
    page.open('POST', "ajax.php?__par=" + url, true) ;
    page.setRequestHeader('Content-Type','application/x-www-form-urlencoded') ;
    page.send(cParameter);  
  },
  urlByName: function(){
    var o = a.getById("__currentFile") ;
    var cFile = "" ;
    if(o !== null){
      nStart = o.innerHTML.indexOf(".php") ;
      cFile = o.innerHTML.substring(0,nStart) + ".ajax.php" ;    
    }
    return cFile ;
  },
  
  // Get Form Content
  fContent: function(elem = null){
    var sXml = "" ;
    var frm = document.forms[0] ;
    var el = null ;
    if(elem !== null){
      if(elem.tagName && elem.tagName == 'FORM'){
        frm = elem ;
      }else{
        el = elem ;
      }
    }
    if (frm && frm.tagName == 'FORM'){
      if(el == null) el = frm.elements ;
      for( var i=0; i < el.length; i++){
        if (!el[i].name)
          continue;
        if (el[i].type && (el[i].type == 'radio' || el[i].type == 'checkbox') && el[i].checked == false)
          continue;
        if (el[i].disabled && el[i].disabled == true)
          continue;

        var name = el[i].name;
        if(name){
          if (sXml != ''){
            sXml += '&';
          }
          if(el[i].type=='select-multiple'){
            for (var j = 0; j < el[i].length; j++){
              if (el[i].options[j].selected == true){
                sXml += name + "=" + encodeURIComponent(el[i].options[j].value) + "&" ;
              }
            }
          }else{
            sXml += name + "=" + encodeURIComponent(el[i].value);
          }
        } 
      }
    }
    return sXml;
  },
  // Kita Gunakan Kalau kita mau membuat Div Modal maka kita buat Background Terlebih dahulu.
  addBack:function(cID){
    cID = (cID == null) ? "frmBackModal" : cID ;
    var oBack = a.getById(cID) ;

    if(oBack == null){
      oBack = a.addObj("div") ;
      oBack.id = cID ;
      oBack.className = "back_modal" ;
      with(oBack.style){
        width = document.body.scrollWidth-1 ;
        height = document.body.scrollHeight-1 ;        
      }
      setObjIndex(oBack) ;
    }
    return oBack ;
  },
  // Class Slide Bar Untuk Menyimpan Daftar Windows List kalau ada yang di minimize
  slideBar:function(){
    var ow = this.g() ;
    var h = ow.document.body.clientHeight ;

    oDiv = ow.a.getById("__div_slide_bar__") ;
    if(oDiv == null){
      oDiv = ow.a.addObj("div") ;
      oDiv.id = "__div_slide_bar__" ;
      oDiv.className = "slide_bar" ;
      oDiv.onmouseover = function(){a.slideBarItem();} ;
      with(oDiv.style){
        height = h - 5 ;
      }
    }
  },
  slideBarItem:function(){
    var ow = a.g() ;
    var cID = "" ;
    var oItem = null ;
    var oDiv = ow.a.getById("__div_slide_bar__") ;
    var h = ow.document.body.clientHeight ;

    if(oDiv !== null){
      with(oDiv.style){
        top = 0 ;
        height = h - 5 ;
      }
      var ot = {} ;
      ow.setObjIndex(oDiv) ;
      for(var key in ow._winList){
        el = ow._winList [key] ;
        cID = "__sl_item_" + key + "__" ;
        oItem = ow.a.getById(cID,oDiv) ;
        if(el !== null && oItem == null){
          var ot = ow.a.addObj("div",oDiv) ;
          ot.title = el['title'] ;
          ot.id= cID ;
          ot.className = "slide_bar_item" ;
          ot.onclick = function(){ow.a.slideBarItemClick(key)} ;
          ot.innerHTML = '<div style="margin-right:5px;overflow:hidden">' + el ['title'] + '</div>' ;
        }

        oItem = ow.a.getById(cID,oDiv) ;        
        if(oItem !== null){
          oItem.className = (el ['active']) ? "slide_bar_item" : "slide_bar_item slide_bar_item_blur" ;
        }
      }
    }
  },
  slideBarItemClick: function(cName){
    var el = this.g()._winList [cName] ;
    if(el ['min'] || !el ['active']){
      el ['min'] = false ;
      el['ow'].frm.frmReOpen(el['frm'],el['ow'],cName) ;
    }else{
      el ['min'] = true ;
      el['ow'].frm.min(cName) ;
    }
  },

  // Class Move Object Untuk Semua Form dll.
  x_win:0,y_win:0,x_pos:0,y_pos:0,oMove:null,
  obj_move_start(o,e,callback,callBackWileMove){
    if(callback) this.move_callback = callback ;
    if(callBackWileMove) this.callBackWileMove = callBackWileMove ;

    this.oMove = o ;
    this.x_win = o.offsetLeft ;
    this.y_win = o.offsetTop ;
    this.x_pos = document.all ? window.event.clientX : e.pageX;
    this.y_pos = document.all ? window.event.clientY : e.pageY;

    (function(a,callback,callBackWileMove){
      document.onmousemove = function(e){a.obj_move(e,callBackWileMove) ;} ;
      document.onmouseup = function(e){a.obj_move_stop(e,callback);} ;
    })(this,callback,callBackWileMove) ;
  },
  obj_move(e,callback){
    var x = document.all ? window.event.clientX : e.pageX;
    var y = document.all ? window.event.clientY : e.pageY;
    if (a.oMove !== null) {
      x = Math.max(a.x_win + x - a.x_pos,0) ;
      y = Math.max(a.y_win + y - a.y_pos,0) ;
      a.oMove.style.left = x + 'px';
      a.oMove.style.top = y + 'px';
      if(callback) callback() ;
    }
  },
  obj_move_stop(e,callback){
    document.onmousemove = null ;
    document.onmouseup = null ;
    if(callback) callback() ;
  },
  getCursor(e){   
    var x = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
    var y = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    return [x,y] ;
  },
};

// Ini Adalah Daftar Variable yang akan digunakan Secara Global Tidak boleh ada yang kembar.
var mainWindow = null ;
var fWin = "" ;
var fWList = "," ;
var _winList = {} ;
function _grandWin(){
  var w = window ;

  for(var n = 0; n < 10;n++){
    if(w.name !== "mainFrame"){
      w = w.self.parent ;
    }
    if(w.name == "mainFrame") n = 10 ;
  }
  
  if(w.name !== "mainFrame"){
    var a = w.document.getElementById("mainFrame") ;
    if(a !== null) w = a.contentWindow ;
  }

  mainWindow = w ;
  return mainWindow ;
} ;