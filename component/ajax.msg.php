<?php
  include 'df.php' ;
  // Jangan Dirubah Headernya utk Deteksi Scrip php setelah tanda ?>
?>
var msg = {
  od:[null,null,null,null],lShow:false,cTitle:"",_w:0,
  rd: function(o){a.delObj(o);},
  focus: function(){var o = a.getById("_bwait") ;if(o !== null) o.focus() ;},
  unload: function(field){this.focus() ;this.bClick();},
  waitStart : function (nTimeout=0,cTitle=""){
    var nW = document.body.offsetWidth ;
    this.cTitle = "Tunggu Sebentar, Data sedang diproses....." ;
    if(cTitle !== "") this.cTitle = cTitle ;
    this.lShow = false ;
    this.od[0] = a.getById("_oWait") ;
    if(this.od[0] == null){
      this.od[0] = a.addObj("div") ;
      this.od[0].id = "_oWait" ;
      this.od[0].onclick = eval("(function(){msg.bClick();});") ;
      this.od[0].innerHTML = '<input type="button" onBlur="msg.unload(this)" style="width:1px;height:1px;left:-5;position:absolute" value="" name="_bwait" id="_bwait" onClick="msg.bClick();">' ;
    }    
    with(this.od[0].style){
      position = "absolute" ;top = 0 ;left = 0 ;width = nW ;height = window.innerHeight ;backgroundColor = "#999999" ;
      opacity = 0.2;display = "block" ;cursor = "wait";zIndex=1000 ;
    }
    this.focus() ;
    this.stopWait();
    if(nTimeout !== 0){
      this._w = setTimeout(this.waitEnd, nTimeout * 1000,this,1) ;
    }
  },  
  waitEnd: function(o,nTimeout){
    if(o == null) o = this ;
    o.stopWait() ;
    if(nTimeout !== null && typeof nTimeout !== 'undefined' && typeof waitTimeout == "function") waitTimeout() ;
    o.rd(o.od[0]);o.rd(o.od[1]);o.rd(o.od[2]);o.rd(o.od[3]);
  },
  stopWait: function(){
    if(this._w !== 0) clearTimeout(this._w);
  },
  bClick: function(){
    if(this.lShow) return true ;
    this.lShow = true ;
    this.focus() ;
    this.od[1] = a.getById("divWait") ;
    if(this.od[1] == null){
      this.od[1] = a.addObj("div") ;
      this.od[1].id = "divWait" ;
      this.od[1].innerHTML = this.cTitle.replace(" ","&nbsp;") ;
    }
    var w = window.innerWidth ; var h = window.innerHeight ;
    with(this.od[1].style){
      position = "absolute" ;padding="10px 20px 10px 20px" ;border="1px solid #0000FF" ;
      display = "block" ;cursor = "wait";zIndex=1003;
    }
    this.od[1].style.top = (h/2) - (this.od[1].offsetHeight/2) ;
    this.od[1].style.left = (w/2) - (this.od[1].offsetWidth/2) ;
    
    this.od[2] = a.getById("divWaitBG") ;
    if(this.od[2] == null){
      this.od[2] = a.addObj("div") ;
      this.od[2].id = "divWaitBG" ;
    }
    with(this.od[2].style){
      position = "absolute" ;display = "block" ;cursor = "wait";backgroundColor="#3983c8";zIndex=1002;
      opacity = 0.2;height=this.od[1].offsetHeight;width=this.od[1].offsetWidth;top=this.od[1].offsetTop;left=this.od[1].offsetLeft;
    }

    this.od[3] = a.getById("divWaitBW") ;
    if(this.od[3] == null){
      this.od[3] = a.addObj("div") ;
      this.od[3].id = "divWaitBW" ;
    }
    with(this.od[3].style){
      position = "absolute" ;display = "block" ;cursor = "wait";backgroundColor="#bbbbbb";zIndex=1001;
      height=this.od[1].offsetHeight;width=this.od[1].offsetWidth;top=this.od[1].offsetTop;left=this.od[1].offsetLeft;
    }
  },
  al:{back:null,win:null,callback:null,tout:0,button:"_AlertButton",type:""},
  initAlert:function(h,w){
    this.al={back:null,win:null,callback:null,tout:0,button:"_AlertButton",type:""} ;
    this.al['back'] = a.getById("_oBack") ;
    if(this.al['back'] == null) this.al['back'] = a.addBack("_oBack") ;
  },
  // Show Alert, Confirm
  _show: function(type,msg,title,callback){
    var w = window.innerWidth ; 
    var h = window.innerHeight ;
    var aw = 400 ;
    var ah = 50 ;
    this.initAlert(h,w) ;
    this.al['type'] = type ;
    msg.replace(" ","&nbsp;") ;
    if(title == null) title = type ;
    if(title == "") title = type ;
    if(callback) this.al['callback'] = callback ;

    // Create Alert Windows
    this.al['win'] = a.getById("_alWindows") ;
    if(this.al['win'] == null){
      button = '<input id="_AlertButton" onKeyDown="return msg._kd(1,event);" onClick="msg.alrClick(true)" type="Button" class="Button" name="cc" value="&nbsp;&nbsp;OK&nbsp;&nbsp;">' ;
      if(type == "Confirm") button += '&nbsp;&nbsp;<input id="_AlertCancel" onKeyDown="return msg._kd(2,event);" onClick="msg.alrClick(false)" type="Button" class="Button" name="cc" value="&nbsp;&nbsp;Cancel&nbsp;&nbsp;">' ;
      this.al['win'] = a.addObj("div") ;
      this.al['win'].className = "alr_main no_txt_select" ;
      this.al['win'].id = "_alWindows" ;
      var oDiv = this.al['win'] ;
      this.al['win'].onmousedown = function(e){oh.style.cursor = "move";oDiv.style.cursor = "move";a.obj_move_start(oDiv,e,function(){oDiv.style.cursor = "default";oh.style.cursor = "default";}) ;} ;

      // Parent -> Header 
      var oh = a.addObj("div",this.al['win']) ;      
      oh.className = "alr_head" ;
      oh.id= "_divAlertHead" ;
      
      // Parent -> header -> Title
      var ot = a.addObj("div",oh) ;
      ot.className = "alr_title no_txt_select" ;
      ot.innerHTML = title ;

      // Parent -> Message
      var ob = a.addObj("div",this.al['win']) ;
      ob.id="_divAlertMsg" ;
      ob.className = "alr_msg no_txt_select" ;
      ob.align = "left" ;
          
      // Parent -> Button
      var ob = a.addObj("div",this.al['win']) ;
      ob.id = "_divAlertButton" ;
      ob.className = "alr_button no_txt_select" ;
      ob.align = "center" ;
      ob.innerHTML = button ;
    }    

    var om = a.getById("_divAlertMsg") ;    
    var ob = a.getById("_divAlertButton") ;
    var oh = a.getById("_divAlertHead") ;
    om.innerText = msg ;
    ah = om.offsetHeight + ob.offsetHeight + oh.offsetHeight ;
    var wt = Math.max(0,(h/2) - (ah/2)) ;
    var wl = Math.max(0,(w/2) - (aw/2)) ;
    this.tout();    
    with(this.al['win'].style){top=wt ;left=wl ;width = aw ;height = ah;}
  },
  // KeyDown untuk Alert/Confirm hanya tombol Panah tertentu yang boleh, selainnya kita matikan
  _kd:function(n,e){
    var num = e.keyCode ;
    if(num == 37 || num == 38){
      n -- ;
      if(n == 0) n = 2 ;
    }else if(num == 39 || num == 40){
      n ++ ;
      if(n == 3) n == 1 ;
    }else if(num == 13){
      return true ;
    }
    if(this.al ['type'] == "Alert") n = 1 ;
    this.al ["button"] = (n == 2) ? "_AlertCancel":"_AlertButton" ;
    var o = a.getById(this.al ["button"]) ;
    if(o !== null) o.focus() ;
    return false ;
  },
  tout:function(){
    var o = a.getById(msg.al ["button"]) ;
    if(o !== null) o.focus() ;
    msg.al ['tout'] = setTimeout(msg.tout,10) ;
  },
  alrClick: function(par){
    clearTimeout(msg.al ['tout']) ;
    this.rd(this.al ['back']);
    this.al ['back'] = null ;
    this.rd(this.al ['win']) ;
    this.al ['win'] = null ;
    if(this.al['callback']) this.al['callback'](par) ;
  },
  // Function Untuk Memunculkan Show Supaya tidak menggunakan Alert
  debugClick:function(par){
    var ow = _grandWin() ;
    var oDiv = ow.a.getById("__div_Debug__") ;
    if(oDiv !== null){
      if(par == 0){
        a.delObj(oDiv) ;
      }else{
        oDiv.style.display = "none" ;
      }
    }
  },
  debugRow:0,
  debug:function(cMsg){
    var ow = _grandWin() ;
    if(typeof ow.__debug !== "undefined" && ow.__debug == true){
      var oDiv = ow.a.getById("__div_Debug__") ;
      if(oDiv == null){
        oDiv = ow.a.addObj("div") ;
        oDiv.className = "div_debug_main" ;
        oDiv.id = "__div_Debug__" ;
        
        // Add Div Content
        // Parent -> Debug -> Header
        // Parent -> Icon Close
        // Parent -> Icon Hide
        // Parent -> Body

        // Debug Induk ( oDiv )
        var oDebug = ow.a.addObj("div",oDiv) ;
        oDebug.className = "div_debug" ;
        
        // Header Induk (oDebug) 
        var oHeader = ow.a.addObj("div",oDebug) ;
        oHeader.className = "div_debug_header" ;
        oHeader.innerText = "Info.." ;
        oHeader.onmousedown = function(e){ow.msg.debugMove(this,oDiv,e)} ;

        // Icon Close, Hide, Body Induk ( oDiv)
        var o = ow.a.addObj("div",oDiv) ;
        o.className = "div_debug_icon div_debug_hide no_txt_select" ;
        o.onclick = function(){ow.msg.debugClick(1)} ; 
        o.title = "Hide" ;
        o.innerText = "-" ;
        
        o = ow.a.addObj("div",oDiv) ;
        o.className = "div_debug_icon div_debug_close no_txt_select" ;
        o.onclick = function(){ow.msg.debugClick(0)} ; 
        o.title = "Close" ;
        o.innerText = "x" ;

        o = ow.a.addObj("div",oDiv) ;
        o.onmousedown = function(e){ow.msg.debugMove(this,oDiv,e)} ;
        o.id = "div_debug_body" ;
        o.className = "div_debug_body" ;
      }

      if(oDiv !== null){
        oDiv.style.display = "block" ;
        var ob = ow.a.getById("div_debug_body") ;
        if(ob !== null){
          var lScrollBottom = (ob.scrollTop + ob.offsetHeight) >= ob.scrollHeight ;
          ob.style.width = oDiv.offsetWidth - 10 ;
          ob.style.height = oDiv.offsetHeight - 35 ;
          
          var oItem = ow.a.addObj("div",ob) ;
          oItem.className = "div_debug_item wordwrap" ;
          oItem.innerHTML = ++ow.msg.debugRow + '.&nbsp;' + cMsg ;
          
          if(lScrollBottom) ob.scrollTop = ob.scrollHeight ;
        }
      }
    }
  },
  debugMove: function(o,oDiv,e){    
    o.style.cursor = "move" ;
    a.obj_move_start(oDiv,e,function(){o.style.cursor = "default"}) ;
  },
}