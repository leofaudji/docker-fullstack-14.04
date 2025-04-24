<?php
  include 'df.php' ;
  // Akan Dibaca Mulai Baris Ke 5 Setelah Tanda ?>
?>
txt = {
  va:{},
  browse: function(){this.Browse(cSQL,cFileName)},
  init: function(f){
    var ce = ["onMouseMove","onMouseOut","onMouseDown","onMouseOver","onMouseUp","onDblClick","onChange","onClick","onSelect"] ;
    if(typeof this.va[f.id] == "undefined") this.va [f.id] = {init:false,press:false,bclick:false,kd:null,kp:null,ku:null,kd_num:0,kp_num:0,ku_num:0} ;
    if(this.va [f.id]["init"] == false){
      this.va [f.id]["init"] = true ;
      for(var n=0;n<ce.length;n++){
        this.checkEvent(f,ce[n].toLowerCase(),ce[n]) ;
      }
    }
  },
  checkEvent: function(f,e,c){  
    c = f.name + "_" + c ;
    if(eval("typeof f." + e) !== "function" && eval("typeof " + c) == "function") eval("f." + e + " = function(){" + c + "(this);};") ;
  },
  keyNum: function(e){
    return a.keyNum(e) ;
  },
  keyDown: function(f,e,cf){
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;
    if(!ludf) cf = f.name + "_onKeyDown" ;
    this.va [f.id]['kd_num'] = this.keyNum(e) ;
    this.va [f.id]['kp_num'] = 0 ;
    this.va [f.id]['ku_num'] = 0 ;

    if(this.va[f.id]['kd'] == null) this.va[f.id]['kd'] = eval("typeof " + cf) == 'function' ;
    if(ludf) eval(cf) ;
    else if(this.va[f.id]['kd']) eval(cf + "(f," + this.keyNum(e) + ");") ;
    this.va [f.id]['press'] = true ;
  },
  lastKeyPress: function(f){
    return this.va [f.id]['kp_num'] ;
  },
  lastKeyDown: function(f){
    return this.va [f.id]['kd_num'] ;
  },
  lastKeyUp: function(f){
    return this.va [f.id]['ku_num'] ;
  },
  keyUp: function(f,e,cType,cf){
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;
    var keynum = this.keyNum(e) ;
    this.va [f.id]['ku_num'] = keynum ;
    if(!ludf) cf = f.name + "_onKeyUp" ;
    if(this.va[f.id]['ku'] == null) this.va[f.id]['ku'] = eval("typeof " + cf) == 'function' ;
    if(ludf) eval(cf) ;
    else if(this.va[f.id]['ku']) eval(cf + "(f," + keynum + ")") ;
    this.va [f.id]['press'] = false ;

    if(this.va [f.id]['kd_num'] == this.va [f.id]['ku_num'] && this.va [f.id]['kp_num'] == 0) this.keyEnter(f,keynum);
  },
  keyPress: function(f,e,cType,cf){
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;
    var keynum = this.keyNum(e) ;
    if(!ludf) cf = f.name + "_onKeyPress" ;    
    this.va [f.id]['kp_num'] = keynum ;
    
    if(this.va[f.id]['kp'] == null) this.va[f.id]['kp'] = eval("typeof " + cf) == 'function' ;
    if(ludf) eval(cf) ;
    else if(this.va[f.id]['kp']) eval(cf + "(f," + keynum + ")") ;
    this.keyEnter(f,keynum);
    if(cType == "NUMBER"){
      if(!((e.shiftKey && keynum == 0) || e.ctrlKey)){
        if (!(keynum == 0 || keynum == 46 || keynum == 8 || keynum == 37 ||keynum == 39 || (keynum >= 48 && keynum <= 57))) {
          e.preventDefault();
        }
      }
    }

    return true ;
  },
  bmd: function(cName){
    var f = a.getById("txt-" + cName) ;
    this.init(f);
    this.va[f.id]['bclick'] = true ;
  },  
  bClick: function(cName,cType,cf){  
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;
    var f = a.getById("txt-" + cName) ;
    if(!ludf) cf = f.name + "_onButtonClick" ;
    if(!f.readOnly && !f.disabled){    
      if(cType == "DATE"){
        showCal(f) ;
      }else{
        if(ludf) eval(cf) ;
        else if(eval("typeof " + cf) == 'function') eval(cf + "(f)") ;
      }
    }
    return false ;
  },
  onFocus: function(f,cf){    
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;    
    if(!ludf) cf = f.name + "_onFocus" ;
    this.init(f) ;
    this.va[f.id]['bclick'] = false ;
    this.va [f.id]['kd_num'] = 0 ;
    this.va [f.id]['kp_num'] = 0 ;
    this.va [f.id]['ku_num'] = 0 ;

    if(ludf) eval(cf) ;
    else if(eval("typeof " + cf) == 'function') eval(cf + "(f)") ;
  },
  mOver: function(f,cf){
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;
    if(!ludf) cf = f.name + "_onMouseOver" ;
    this.init(f) ;

    if(ludf) eval(cf) ;
    else if(eval("typeof " + cf) == 'function') eval(cf + "(f)") ;
  },
  onAjax: function(cFunc){
    ajax('',cFunc,GetFormContent()) ;
  },
  onBlur: function(f,cType,cf){
    cf = decodeURIComponent(cf) ;
    var ludf = cf !== "" ;
    if(!ludf) cf = f.name + "_onBlur" ;

    var l = true ;
    if(!this.va[f.id]['bclick']){
      if(cType == "DATE"){
        if(!isDateValided(f.value)){
          alert("Tanggal Tidak Valid ....") ;
          fieldfocus(f) ;
          return false ;
        }
      }else if(cType == "NUMBER"){
        if(!CheckNumber(f)){
          return false ;
        }
      }

      if(ludf){
        if(cf.substring(0,5).toLowerCase() == "ajax:"){
          this.onAjax(cf.substring(5)) ;
        }else{
          eval(cf) ;
        }
      }else if(eval("typeof " + cf) == "function"){
        l = eval(cf+"(f);") ;
      }
    }    
    return l ;
  },
  keyEnter:function(f,nKeyCode){
    if ((nKeyCode == 13 && f.type !== "button") || ((nKeyCode == 38 || nKeyCode == 40) && f.type !== "select-one" && f.type !== "select-multiple")){
      var x = 0 ;
      var i = f.form.length ;
      var n ;
      var lFocus = false ;

      while(x<i){
        if(nKeyCode == 38 || nKeyCode == 37){
          n = i - x - 1 ;
        }else{
          n = x ;
        }
        if (lFocus && !f.form[n].disabled && !f.form[n].readOnly && f.form[n].type.toLowerCase() !== "hidden" && f.form[n].name !== f.name){
          lFocus = false ;

          // Jika Menemukan Jenis Variable Radio Button maka akan kita cari yang posisi di Checked itu yang dijadikan standart Focus
          if(f.form[n].type.toLowerCase() == "radio"){
            var rd = n ;
            var nFound = 0 ;
            while(nFound == 0 && f.form[n].type == "radio" && n < i && f.form[n].name == f.form[rd].name){
              if(f.form[n].checked){
                nFound = n ;
                n = i ;
              }
              n ++ ;
            }
            n = (nFound > 0) ? nFound : rd ;
          }

          f.form[n].focus() ;
          if (f.form[n].type.toLowerCase() == "text") fieldfocus(f.form[n]) ;
          x = i ;
        }
        if (f.form[n] == f) lFocus = true ;
        x ++ ;
      }
    }
  },
  // Text Browse Untuk yang menggunakan Button Click
  Browse: function(cSQL,field,callBack){
    var url = __COMPONENT_FOLDER__ + "/ajax.ajax.php" ;
    var win = _grandWin() ;

    cSQL = encodeURIComponent(cSQL) ;
    a.ajax(url,"_Browse()","cSQL=" + cSQL + "&cFieldName=" + field.name,function(cData,cStatus){
      va = JSON.parse(cData) ;
      nRow = 0 ;
      var vaCol = {} ;
      var vaRow = {} ;
      for(row in va){
        if(nRow == 0){
          var nCol = 0 ;
          for(col in va[row]){
            vaCol [col] = {"type":"text","align":"left","display":"show","edit":"false","caption":col,"width":"100px","height":"400px"} ;
            vaRow [nCol] = va [row][col] ;
            nCol ++ ;
          }
        }
        nRow ++ ;
      }
      if(nRow > 1 || field.value.trim() == ""){
        // Buat Background
        var cID = "dbg-open-brs" ;
        var back = win.a.getById(cID) ;
        if(back == null) back = win.a.addBack(cID) ;
        back.id = cID ;
        back.style.opacity = 0 ;
        (function(txt,win){
          back.onclick = function(){win.txt.closeBrowse(field);fieldfocus(field);} ;
        })(this,win) ;

        // Buat Box Untuk Grid
        var vaPos = fieldPos(field,window) ;
        var cID = "dbg-box-brs" ;
        var bx = win.a.getById(cID) ;
        if(bx == null) bx = win.a.addObj("div") ;
        bx.id = cID ;
        bx.innerHTML = "" ;
        var nWidth = 400 ;
        var nHeight = 200 ;
        bx.style.cssText = "top:" + vaPos [1] + ";left:" + vaPos [0] + ";background-color:#eeeeee;width:" + nWidth + "px;height:" + nHeight + "px;position:absolute;display:block" ;
        win.setObjIndex(bx) ;

        with(win){
          dbg = new DBGRID() ;
          dbg.create(vaCol,"dbgBrowse") ;
          dbg.dataBind(bx) ;
          dbg.onClick = function(vaRow,nCol,lMouse){win.txt.BrowseClick(vaRow,nCol,lMouse,field,callBack);} ;
          dbg.onDblClick = function(vaRow,nCol,lMouse){win.txt.BrowseDblClick(vaRow,nCol,lMouse,field,callBack);} ;
          dbg.onKeyPress = function(e){win.txt.BrowseKeyPress(e,field);} ;
          for(row in va){
            var nCol = 0 ;
            var vaRow = [] ;
            for(col in va[row]){
              vaRow [nCol] = va [row][col] ;
              nCol ++ ;
            }
            dbg.AppendRow(vaRow) ;
          }
          dbg.focus() ;
          dbg.AutoWidth() ;

          var nWinHeight = window.innerHeight ;
          var nWinWidth = window.innerWidth ;
          var nScrollTop = window.document.body.scrollTop ;
          var nScrollLeft = window.document.body.scrollLeft ; 
          nHeight = Math.max(40,Math.min(nWinHeight,Math.min(dbg.bodyHeight() + 20,nHeight))) ;
          nWidth = Math.max(20,Math.min(nWinWidth,Math.min(dbg.bodyWidth() + 20,nWidth))) ;
          bx.style.width = nWidth ;
          bx.style.height = nHeight ;
          if(bx.offsetHeight + bx.offsetTop > nWinHeight + nScrollTop) bx.style.top = nWinHeight - bx.offsetHeight + nScrollTop ;
          if(bx.offsetWidth + bx.offsetLeft > nWinWidth + nScrollLeft) bx.style.left = nWinWidth - bx.offsetWidth + nScrollLeft;
        }
      }else if (nRow == 1){
        txt.PickField(vaRow,field,callBack) ;
      }else{
        a.alert("Data Tidak Ditemukan .....","Error....",function(){fieldfocus(field)}) ;
      }
    }) ;
  },
  BrowseKeyPress: function(e,field){
    var keynum = a.keyNum(e) ;
    if(keynum == 27){
      this.closeBrowse(field) ;
      fieldfocus(field) ;
    }
  },
  BrowseClick: function(vaRow,nCol,lMouse,field,callBack){
    if(lMouse){
      this.PickField(vaRow,field,callBack) ;
    }
  },
  BrowseDblClick: function(vaRow,nCol,lMouse,field,callBack){
    this.PickField(vaRow,field,callBack) ;
  },
  PickField: function(vaRow,field,callBack){
    field.value = vaRow [0] ;
    this.closeBrowse(field) ;
    if(callBack){
      callBack(vaRow) ;
      this.keyEnter(field,13) ;
    }else{
      fieldfocus(field) ;
    }
  },
  closeBrowse: function(field){
    var win = _grandWin() ;
    win.a.delById("dbg-open-brs") ;
    win.a.delById("dbg-box-brs") ;
  },
};

// Class Status Bar
var sBar = {
  vasBar:{},nCell:0,oDiv:null,Name:"",
  show: function(cName,parent){
    nWidth = "100%" ;
    
    this.name = cName ;
    this.ot = a.addObj("table",parent) ;
    this.ot.className =  "sbar_main" ;
    this.ot.id = cName ;
    this.ot.width = nWidth ;

    this.oDiv = a.addObj("tr",this.ot) ;
    for(var key in this.vasBar){
      var ele = this.vasBar [key] ;

      var o = a.addObj("td",this.oDiv) ;
      o.className = "sbar_item no_txt_select" ;
      o.id = ele ['name'] + "-cell-content-" ;
      o.innerHTML = ele ['title'] ;
      if(ele ['width'] !== null) o.style.width = ele ['width'] ;
    }

    this.vasBar = {} ;
  },
  add: function(cName,title,width){
    this.nCell ++ ;
    this.vasBar [this.nCell] = {name:cName,title:title,width:width} ;
  },
  getItem(cItem){
    return a.getById(cItem + "-cell-content-") ;
  }
};

// Tool Bar
var tBar = {
  vatBar:{},nCell:0,oDiv:null,Name:"",
  show: function(cName,nTop,nLeft,nHeight,nWidth,parent){
    if(nTop == null) nTop = 0 ;
    if(nLeft == null) nLeft = 0 ;
    if(nHeight == null) nHeight = 24 ;
    if(nWidth == null) nWidth = "100%" ;
    
    this.name = cName ;
    this.oDiv = a.addObj("div",parent) ;
    this.oDiv.className =  "tbar_main" ;
    this.oDiv.id = cName ;
    with(this.oDiv.style){
      top = nTop ;
      left = nLeft ;
      height = nHeight ;
      width = nWidth ;
    }

    for(var key in this.vatBar){
      var ele = this.vatBar [key] ;
      if(ele ['name'] == "--separator--"){
        var o = a.addObj("div",this.oDiv) ;
        o.className = "tbar_item_sep no_txt_select" ;
        o.style.height = this.oDiv.offsetHeight - 6 ;
      }else{
        var o = a.addObj("div",this.oDiv) ;
        o.className = "tbar_item_div" ;
        o.id = ele ['name'] + "-toolBar-div" ;

        if(ele ['img'] == ""){
          var img = a.addObj("div",o) ;
          img.className = "tbar_item_txt no_txt_select" ;
          img.innerHTML = ele ['title'] ;
        }else{
          var img = a.addObj("img",o) ;
          img.src = ele['img'] ;
          img.className = "tbar_item no_txt_select" ;
        }
        img.title = ele ['title'] ;
        img.id = ele['name'] + "-toolBar-Item" ;

        (function(ele){
          img.onclick = function(){if(ele ['callBack']) ele ['callBack']()} ;
        })(ele) ;
        var nTop = Math.max((this.oDiv.offsetHeight - img.offsetHeight) / 2,0) ;
        if(ele ['img'] !== "") img.style.marginTop = 2 ; // nTop ;
      }
    }

    this.vatBar = {} ;
  },
  add: function(cName,title,img,callBack){
    this.nCell ++ ;
    this.vatBar [this.nCell] = {name:cName,title:title,img:img,callBack:callBack} ;
  },
  addSep: function(){
    this.nCell ++ ;
    this.vatBar [this.nCell] = {name:"--separator--"} ;
  },
  getItem(cItem){
    return a.getById(cItem + "-toolBar-Item") ;
  }
}