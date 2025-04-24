<?php
  include 'df.php' ;
?>
function DBGRID(){
  this.om = null;             // dbg_main_name
  this.oh = null ;            // dbg_header_name
  this.ob = null ;            // dbg_body_name
  this.of = null ;            // dbg_footer_name
  this.oCap = null ;          // dbg_caption_name
  this.oBorder = null ;       // dbg_border_name
  this.tbBody = null ;        // tbBody_name
  this.tbHead = null ;        // tbHead_name
  this.tbFoot = null ;        // tbFooter_name
  this.Name = "" ;
  this.nCurrRow = 0 ;
  this.nCurrCol = 0 ;
  this.oldCell = null ;         // Kita gunakan untuk menyimpan Cell terakhir yang di Click 
  this.noldClassName = "" ;     // kita juga menyimpan Class namenya Supaya Bisa dikembalikan
  this.css = null ;
  this.button = null ;
  this.lEdit = false ;
  this.Caption = "" ;
  this.lShowFooter = false ;
  this.vaCol = {} ;
  this.onClick = null ;
  this.onDblClick = null ;
  this.onKeyPress = null ;
  this.lrecheckGrid = false ;
  // Kita juga bisa membuat dbgrid Full Dari Javascript Caranya kita Definisikasn Column nya dengan Function ini
  this.create = function(oCol,cName,cCaption,lShowFooter){
    this.Name = (cName == null) ? "DBGRID1" : cName ;
    this.Caption = (cCaption == null) ? "" : cCaption ;
    this.lShowFooter = (lShowFooter == null) ? false : lShowFooter ;

    var col = "" ;
    var n = 0 ;
    this.vaCol = {} ;
    for(col in oCol){
      if ((n in this.vaCol) == 0) this.vaCol [n] = {"type":"text","align":"left","display":"show","edit":"false","caption":"","width":"100px"} ;
      for(var i in oCol [col]){
        this.vaCol [n][i.toLowerCase()] = oCol[col][i] ;
      }
      n ++ ;
    }
  };
  // Untuk Memunculkan dbgrid yang dari Javascript sama dengan php dengan memanggil function bindData hanyasaja kalau di javascript
  // harus mengirimkan parameter id Parent nya.
  this.dataBind = function(idParent){  
    var op = idParent ;
    if(typeof op == "string") op = a.getById(idParent) ;

    // Struktur Object Grid
    // db_border
    //   tdbTable
    //     dbgTable -> Row -> Col
    //       div - dbg_caption
    //     dbgTable -> Row -> Col
    //       div - dbg_main
    //         div - dbg_header
    //         div - dbg_Body
    //         div - dbg_footer
    this.oBorder = a.addObj("div",op) ;
    this.oBorder.id = "dbg_border_" + this.Name ;
    this.oBorder.className = "dbg_border" ;
    this.oBorder.style.height = "100%" ;

    this.oBorder.innerHTML = '<table id="tdb_main_' + this.Name + '" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0"></table>' ;
    var _ot = a.getById("tdb_main_" + this.Name) ;
    (function(dbg){
      _ot.onmouseover = function(){dbg.recheckGrid()} ;
    })(this) ;

    // Buat Kalau ada Caption
    if(this.Caption !== ""){
      var _or = a.addObj("tr",_ot) ;
      var _cell = a.addObj("td",_or) ;
      _cell.height = "20px" ;
      this.oCap = a.addObj("div",_cell) ;
      this.oCap.id = "dbg_caption_" + this.Name ;
      this.oCap.className = "dbg_caption no_txt_select" ;
      this.oCap.innerText = this.Caption ;
    }
    
    // Buat Row Untuk Header,Body, Footer
    var _or = a.addObj("tr",_ot) ;
    var _cell = a.addObj("td",_or) ;

    // Grid Main
    this.om = a.addObj("div",_cell) ;
    this.om.id = "dbg_main_" + this.Name ;
    this.om.className = "dbg_main" ;    
    (function(dbg){
      dbg.om.onscroll = function(){dbg.onScroll(this)} ;
    })(this) ;

    // Header
    this.oh = a.addObj("div",this.om) ;
    this.oh.id = "dbg_header_" + this.Name ;
    this.className = "dbg_header" ;
    this.oh.style.display = "block" ;
    this.oh.style.position = "absolute" ;
    this.oh.innerHTML = '<table border="0" cellspacing="0" cellpadding="0" id="tbHead_' + this.Name + '" height="100%"></table>' ;
    this.tbHead = a.getById("tbHead_" + this.Name) ;
    var r = a.addObj("tr",this.tbHead) ;
    for(var n in this.vaCol){      
      var c = a.addObj("td",r) ;
      c.className = "dbg_cell_header no_txt_select " + this.Name + "_" + n ;
      c.align = "center" ;
      if(this.vaCol [n]['type'] == "checkbox"){
        // Jika Jenis Checkbox maka Kita siapkan Untuk Event nya sekalian
        c.innerHTML = '<img style="padding-top:1px" id="ck_cell_all" src="' + compFolder() + '/dbgrid/images/check.gif" border="0">' ;        
        var _img = a.getById("ck_cell_all",c) ;
        if(_img !== null){
          (function(dbg,n){
            _img.onclick = function(){dbg.checkMark(this,n) ;} ;
            _img.onmouseover = function(){dbg.checkOver(this,0,n) ;} ;
            _img.onmouseout = function(){dbg.checkOut(this,0,n) ;} ;
          })(this,n) ;
        }
      }else{
        c.innerText = this.vaCol [n]['caption'] ;
      }

      var css = a.getById("css_" + n + "_" + this.Name) ;
      if(css == null) css = a.addObj("style",idParent) ;
      css.id = "css_" + n + "_" + this.Name ;
      css.innerHTML = "." + this.Name + "_" + n + " {width:" + this.vaCol [n]['width'] + ";min-width:" + this.vaCol [n]['width'] + ";max-width:" + this.vaCol [n]['width'] + "}";
    }

    // Body
    this.ob = a.addObj("div",this.om) ;
    this.ob.id = "dbg_body_" + this.Name ;
    this.ob.className = "dbg_body" ;
    this.ob.innerHTML = '<table border="0" cellspacing="0" cellpadding="0" id="tbBody_' + this.Name + '" height="100%"></table>' ;

    // Footer
    if(this.lShowFooter){
      this.of = a.addObj("div",this.om) ;
      this.of.id = "dbg_footer_" + this.Name ;
      this.of.className = "dbg_footer" ;
    }
    this.buildFrame() ;
    this.loadGrid(false);
  };
  this.recheckGrid = function(){
    if(!this.lrecheckGrid){
      this.lrecheckGrid = true ;
      this.initColResize() ;
    }    
  };
  this.a2o = function(va,key){
    for(var n = 0 ; n < va.length ; n++){
      if ((n in this.vaCol) == 0) this.vaCol [n] = {"type":"text","align":"left","display":"show","edit":"false","caption":"","width":"100px"} ;

      this.vaCol [n][key] = va [n] ;
    }
  };
  this.init = function(cName,colType,colAlign,colDisp,colEdit,colName,lAutoWidth){
    this.vaCol = {} ;
    this.a2o(decodeURIComponent(colType).split(","),"type") ;
    this.a2o(decodeURIComponent(colAlign).split(","),"align") ;
    this.a2o(decodeURIComponent(colDisp).split(","),"display") ;
    this.a2o(decodeURIComponent(colEdit).split(","),"edit") ;
    this.a2o(decodeURIComponent(colName).split(","),"caption") ;
    this.Name = cName ;

    this.buildFrame() ;
    var dbg = this ;
    var oldLoad = window.onload ;
    window.onload = function(){if(typeof oldLoad == "function") oldLoad();dbg.loadGrid(lAutoWidth);} ;
  };
  this.buildFrame = function(){
    this.tbBody = a.getById("tbBody_" + this.Name) ;
    this.tbHead = a.getById("tbHead_" + this.Name) ;
    this.css = a.getById("css_" + this.Name) ;
    this.oCap = a.getById("dbg_caption_" + this.Name) ;
    this.oBorder = a.getById("dbg_border_" + this.Name) ;

    // Munculkan Grid Caption 
    if(this.oCap !== null){
      this.oCap.style.display = "block" ;
      this.oCap.style.position = "relative" ;
    }
    this.om = a.getById("dbg_main_" + this.Name) ;

    // Setting Div Header
    this.oh = a.getById("dbg_header_" + this.Name) ;
    this.oh.style.display = "block" ;
    this.oh.style.position = "absolute" ;

    // Setting Grid Footer
    this.tbFoot = a.getById("tbFooter_" + this.Name) ;
    this.of = a.getById("dbg_footer_" + this.Name) ;
    var nMBottom = 0 ;
    if(this.of !== null){
      nMBottom = this.of.offsetHeight + 4 ;
    }

    // Setting Grid Body
    this.ob = a.getById("dbg_body_" + this.Name) ;
    this.ob.style.display = "block" ;
    this.ob.style.position = "absolute" ;
    // Kalau Definisi Header = 0 maka Body Top Tetap Minimal 20px
    this.ob.style.marginTop = Math.max(this.oh.offsetHeight,20) ;
    this.ob.style.paddingBottom = nMBottom ;
  
    var nw = Math.max(this.ob.offsetWidth,this.oh.offsetWidth) ;
    this.ob.style.width = nw ;
    this.oh.style.width = nw ;

    setObjIndex(this.ob) ;
    setObjIndex(this.oh) ;
    if(this.oCap !== null) setObjIndex(this.oCap) ;
    if(this.of !== null) setObjIndex(this.of) ;
    
    this.initColResize() ;
    
    this.button = a.addObj("input",this.om) ;
    this.button.type = "button" ;
    this.button.id = "button_" + this.Name ;
    this.button.disabled = true ;
    with(this.button.style){
      width = 1 ;
      height = 1 ;
      opacity = 0 ;
    }
  };
  this.focus = function(){
    var r = this.nCurrRow ;
    var c = this.nCurrCol ;    
    if(this.button !== null) this.button.focus() ;
    if(this.tbBody.rows.length > this.nCurrRow && this.tbBody.rows [this.nCurrRow].cells.length > this.nCurrCol) this.CellPos(this.nCurrRow,this.nCurrCol) ;
  };
  this.loadGrid = function(lAutoWidth){
    if(lAutoWidth == "true" || lAutoWidth == 1) this.AutoWidth() ;
    //this.footerLocation() ;
  };
  this.CurrRow = function(){return this.nCurrRow};
  this.CurrCol = function(){return this.nCurrCol};
  this.ColName = function(nCol){
    return this.vaCol [nCol]["caption"] ;
  };
  this.DeleteRow = function(nRow){
    if(this.tbBody.rows.length > nRow) this.tbBody.deleteRow(nRow) ;
  };
  this.initColResize = function(){
    // Buat Div Untuk Resize Kolom
    var nl = 0 ;
    for(var n = 0;n < this.tbHead.rows[0].cells.length;n++){
      nl += this.tbHead.rows[0].cells[n].offsetWidth ;
      
      var s = a.getById(n,this.oh) ;
      if(s == null) s = a.addObj("div",this.oh) ;
      s.style.cssText = "display:block;position:absolute;left:" + (nl-3) + ";top:0px;height:20;width:5px;cursor:col-resize" ;
      s.id = n ;
      (function(dbg,s,n){
        s.onmousedown = function(e){dbg.startColResize(e,this);} ;
        s.ondblclick = function(){dbg.AutoWidthColl(n);dbg.initColResize();} ;
      })(this,s,n) ;
    }
  };
  this.startColResize = function(e,div){
    var dbg = this ;
    var nLeft = div.offsetLeft ;
    var cell = this.tbHead.rows[0].cells[div.id] ;
    var nw = cell.offsetWidth ;
    var nt = div.offsetTop ;
    var css = a.getById("css_" + div.id + "_" + this.Name) ;
    a.obj_move_start(div,e,
      // Stop Event
      function(){
        dbg.initColResize() ;
        dbg.oh.style.width = "auto" ;
        dbg.ob.style.width = "auto" ;
        dbg.footerLocation() ;
      },
      // Move Event
      function(){
        var nMove = nw + (div.offsetLeft - nLeft - 2) ;
        div.style.top = nt ;

        css.innerHTML = "." + dbg.Name + "_" + div.id + " {width:" + nMove + "px;min-width:" + nMove + "px;max-width:" + nMove + "px}";
      });
  };
  this.cellValue = function(nRow,nCol){return this.CellValue(nRow,nCol)} ;
  this.CellValue = function(nRow,nCol){
    if(this.vaCol [nCol]["type"] == "checkbox"){
      var cRetval = 0 ;
      var img = this.GetCellContent(nRow,nCol,"img") ;
      if(img !== null && img.length > 0){
        if(img[0].src.indexOf("check.gif") >= 0) cRetval = this.isChecked(img[0]) ;
      }
    }else{
      var cRetval = this.GetRow(nRow)[nCol] ;
    }
    return cRetval ;
  };
  this.isChecked = function(img){
    var lRetval = 1 ;
    if(img.src.indexOf("uncheck.gif") >= 0) lRetval = 0 ;
    return lRetval ;
  };
  this.GetCellContent = function(nRow,nCol,cTag){
    var oRetval = null ;
    if(this.tbBody.rows.length > nRow){
      if(this.tbBody.rows [nRow].cells.length > nCol){
        if(cTag !== null && typeof cTag !== "undefined" && cTag.trim() !== "" && cTag == "img"){
          oRetval = this.tbBody.rows[nRow].cells[nCol].getElementsByTagName(cTag) ;
        }else{
          oRetval = this.tbBody.rows[nRow].cells[nCol] ;
        }
      }
    }
    return oRetval ;
  };
  this.nOldTop = null ;
  this.onScroll = function(div){
    this.oh.style.top = this.om.scrollTop ; 
    this.footerLocation() ;

    var nTop = this.om.scrollTop ;
    var nLeft = this.om.scrollLeft ;
    var nMax = this.MaxScroll() ;

    // Check Apabila scrollTop Pertamakali menyentuh angka = 0 maka jalankan event onTopScroll    
    if(nTop == 0 && this.nOldTop !== nTop) if(eval("typeof " + this.Name + "_onTopScroll") == 'function') eval(this.Name + "_onTopScroll")(0,nLeft) ;
    // Jika Pertama kali menyentuh Scroll Bottom jalankan Event onBottomScroll
    if(nTop == nMax && this.nOldTop !== nTop) if(eval("typeof " + this.Name + "_onBottomScroll") == 'function') eval(this.Name + "_onBottomScroll")(nTop,nLeft) ;
    this.nOldTop = nTop ;
  };
  this.footerLocation = function(){
    if(this.of !== null && this.om.clientHeight > 0){
      var nH = this.of.offsetHeight ;
      var nTop = this.om.clientHeight - nH + this.om.scrollTop ;
      this.of.style.height = nH ;
      this.of.style.top = nTop ;
    }
  } ;
  this.HeadClick = function(nCol){
    if(eval("typeof " + this.Name + "_onHeaderClick") == 'function'){
      eval(this.Name + "_onHeaderClick(" + nCol + ");") ;
    }
  };
  this.DeleteRowAll = function(){
    this.tbBody.innerHTML = "" ;
  } ;
  this.Rows = function(){
    return this.tbBody.rows.length ;
  };
  this.Cols = function(){
    return this.tbHead.rows[0].cells.length ;
  };
  this.AppendRow = function(vaValue){
    var nRow = this.Rows() ;
    this.InsertRow(nRow,vaValue) ;
    return nRow ;
  };
  this.ClickRow = function(row){
    this.nCurrRow = row.rowIndex ;
  };
  this.GetRow = function(nRow){
    var va = [] ;
    var _or = this.tbBody.rows[nRow] ;
    for(var n = 0;n< _or.cells.length;n++){
      if(this.vaCol [n]["type"] == "checkbox"){
        va [n] = this.CellValue(nRow,n) ;
      }else{
        if(typeof(_or) !== "undefined") va [n] = _or.cells[n].textContent ;
      }
    }    
    return va ;
  },
  this.CellType = function(nRow,nCol){
    var cRetval = "text" ;
    var img = this.GetCellContent(nRow,nCol,"img") ;
    if(img !== null && img.length > 0){
      cRetval = "checkbox" ;
    }
    return cRetval ;
  };
  this.cellUpdate = function(nRow,nCol,cValue){this.CellUpdate(nRow,nCol,cValue)};
  this.CellUpdate = function(nRow,nCol,cValue){
    var img = this.GetCellContent(nRow,nCol,"img") ;
    if(img !== null && img.length > 0){
      if(img[0].src.indexOf("check.gif") >= 0){
        if(cValue == 1 || cValue == true){
          img[0].src = compFolder() + "/dbgrid/images/check.gif" ;
        }else{
          img[0].src = compFolder() + "/dbgrid/images/uncheck.gif" ;
        }
      }
    }else{
      var o = this.GetCellContent(nRow,nCol) ;
      if(o !== null) o.innerHTML = decodeURIComponent(cValue) ;
    }
  };
  this.ClickCell = function(cell,lDbl,lMouseClick){
    if(lMouseClick == null) lMouseClick = false ;
    if(!this.lEdit){
      var nCol = cell.cellIndex ;
      var nRow = cell.parentNode.rowIndex ;
      if(this.oldCell !== null) this.oldCell.className = this.noldClassName ;    
      this.noldClassName = cell.className ;
      this.oldCell = cell ;
      this.nCurrRow = nRow ;    
      this.nCurrCol = nCol ;

      cell.className = "dbg_cell_body dbg_cell_body_click " + this.Name + "_" + nCol ;
      if(lDbl){
        if(this.vaCol[nCol]["edit"] == "true" || this.vaCol[nCol]["edit"] == true){
          var lValidEdit = true ;
          if(eval("typeof " + this.Name + "_onBeforeEdit") == 'function') lValidEdit = eval(this.Name + "_onBeforeEdit")(this.GetRow(nRow),nRow,nCol) ;
          if(lValidEdit){
            var dbg = this ;
            var old = cell.textContent ;
            cell.className = "dbg_cell_body " + this.Name + "_" + nCol ;          
            cell.contentEditable = true ;
            this.lEdit = true ;
            cell.focus() ;
            cell.onkeydown = function(e){dbg.editKeyDown(e,this,old,1);} ;
            cell.onkeypress = function(e){dbg.editKeyDown(e,this,old,2);} ;
            cell.onkeyup = function(e){dbg.editKeyDown(e,this,old,3);} ;
            cell.onblur = function(){dbg.editBlur(this,old);} ;
          }
        }
      }
      var vaRow = this.GetRow(nRow) ;
      if(!lDbl){
        if(this.onClick){
          this.onClick(vaRow,nCol,lMouseClick) ;
        }else{
          if(eval("typeof " + this.Name + "_onClick") == 'function') eval(this.Name + "_onClick")(vaRow,nCol,lMouseClick);
        }
      }else{
        if(this.onDblClick){
          this.onDblClick(vaRow,nCol,lMouseClick) ;
        }else{
          if(eval("typeof " + this.Name + "_onDblClick") == 'function') eval(this.Name + "_onDblClick")(vaRow,nCol,lMouseClick) ;
        }
      }
      if(!this.lEdit) this.waitKey() ;
    }
  };
  this.CellKeyPress = function(e){
    if(this.onKeyPress){
      this.onKeyPress(e) ;
    }else{
      if(eval("typeof " + this.Name + "_onKeyPress") == 'function') eval(this.Name + "_onKeyPress")(e);
    }
  };
  // Apabila kita Click Cell maka posisi Kursor kita tempatkan di Button supaya bisa di cek Button yang di click ;
  this.waitKey = function(){
    var t = this.om.scrollTop ;
    var l = this.om.scrollLeft ;

    this.button.disabled = false ;
    this.button.focus() ;
    var dbg = this ;
    this.button.onkeydown = function(e){return dbg.waitKeyPress(e);} ;
    this.button.onblur = function(){dbg.button.disabled = true;} ;
    this.om.scrollTop = t ;
    this.om.scrollLeft = l ;
  };
  this.waitKeyPress = function(e){
    var key = txt.keyNum(e) ;
    var r = this.nCurrRow ;
    var c = this.nCurrCol ;
    var cell = this.tbBody.rows[r].cells[c] ;
    var celHeight = cell.offsetHeight ;

    if(key == 32){    // Spasi
      if(this.CellType(r,c) == "checkbox"){
        this.cbClick(r,c) ;
        key = 40 ;
      }
    }

    if(key == 38){          // Up
      r -- ;
    }else if(key == 40){    // Down
      r ++ ;
    }else if(key == 37){    // Left
      c -- ;
    }else if(key == 39){    // Right
      c ++ ;
    }else if(key == 33){    // pgUp
      if(e.altKey){
        r = 0 ;
      }else{
        r -= parseInt(this.om.clientHeight / celHeight) ;
      }
    }else if(key == 34){    // pgDown
      if(e.altKey){
        r = this.Rows() - 1 ;
      }else{
        r += parseInt(this.om.clientHeight / celHeight) ;
      }
    }else if(key == 35){    // end
      r = this.Rows() - 1 ;
      c = this.Cols() -1 ;
    }else if(key == 36){    // Home
      r = 0 ;
      c = 0 ;
    }else if(key == 13){
      this.ClickCell(cell,true) ;
    }

    this.CellPos(r,c) ;
    this.CellKeyPress(e) ;
    return false ;
  };
  this.CellPos = function(row,col){
    if(typeof row == "number" && typeof col == "number"){
      row = Math.max(0,Math.min(row,this.Rows()-1)) ;
      col = Math.max(0,Math.min(col,this.Cols()-1)) ;
      var cell = this.tbBody.rows[row].cells[col] ;

      // Check Posisi Top Cell kalau terlalu kebawah kita scroll
      var ft = (this.of !== null) ? this.of.offsetHeight : 0 ;
      var mt = parseFloat(this.ob.style.marginTop) ;
      var ct = cell.offsetTop ;
      var ch = cell.offsetHeight ;
      var cw = cell.offsetWidth ;
      var cl = cell.offsetLeft ;
      var clH = this.om.clientHeight ;
      var clW = this.om.clientWidth ;

      var scT = this.om.scrollTop ;
      var scL = this.om.scrollLeft ;

      // Jika Panah Turun
      if(ct + ch + mt + ft >= (clH + scT)) this.om.scrollTop = (ct-clH+ch+mt+ft) ;
      if(ct <= scT) this.om.scrollTop = ct ;
      if(cl + cw > scL + clW) this.om.scrollLeft = cl + cw - clW ;
      if(cl < scL) this.om.scrollLeft = cl ;

      this.ClickCell(cell,false) ;
    }
    return Array(this.nCurrRow,this.nCurrCol) ;
  };
  this.editBlur = function(cell,old,keynum){
    // Jika Dalam Kondisi Edit langsung di click di luarnya maka akan kita cek layak dirubah apa tidak.
    if(this.lEdit) this.validEdit(cell,old) ;
    if(keynum == null) keynum = 0 ;

    cell.onkeydown = null ;
    cell.onkeypress = null ;
    cell.onkeyup = null ;
    cell.onblur = null ;
    cell.contentEditable = false ;
    cell = this.oldCell ;
    cell.className = this.noldClassName ;
    this.lEdit = false ;

    var r = this.nCurrRow ;
    var c = this.nCurrCol ;
    if(keynum == 13 || keynum == 40){
      r ++ ;
    }else if(keynum == 38){
      r -- ;
    }
    this.CellPos(r,c) ;
  };
  this.editKeyDown = function(e,cell,old,type){
    var keynum = txt.keyNum(e) ;
    if(type == 1){
      if(keynum == 38 || keynum == 40 || keynum == 13){
        this.editBlur(cell,old,keynum) ;
      }
    }
  };
  this.validEdit = function(cell,old){
    var lUpdate = true ;
    var vaRow = this.GetRow(this.nCurrRow) ;
    var vaOld = vaRow ;
    var cValue = cell.textContent ;
    cell.contentEditable = false ;
    this.lEdit = false ;
    vaOld [this.nCurrCol] = old ;
    cell.innerText = old ;
    if(eval("typeof " + this.Name + "_onBeforeUpdate") == 'function') lUpdate = eval(this.Name + "_onBeforeUpdate")(vaOld,this.nCurrRow,this.nCurrCol,cValue);
    if(lUpdate){
      cell.innerText = cValue ;
      if(eval("typeof " + this.Name + "_onAfterUpdate") == 'function') eval(this.Name + "_onAfterUpdate")(vaRow,this.nCurrRow,this.nCurrCol,cValue) ;
    }
  };
  this.InsertRow = function(nRow,vaValue){
    var cValue = "" ;
    var nCol = this.Cols() ;
    var _or = this.tbBody.insertRow(nRow) ;
    (function(db){
      _or.onclick = function(){db.ClickRow(this);} ;
    })(this);
    for(n=0;n<nCol;n++){
      cell = this.tbBody.rows[nRow].insertCell(n) ;
      with(cell){      
        className = "dbg_cell_body " + this.Name + "_" + n ;
        align = this.vaCol [n]["align"] ;
        cValue = "" ;
        if(typeof vaValue == "object" && vaValue.length > n){
          if(this.vaCol [n]["type"] == "checkbox"){
            var cChecked = "uncheck.gif" ;
            if(vaValue [n] == "1" || vaValue [n] == true) cChecked = "check.gif" ;

            cValue = '<img style="padding-top:1px" id="ck_cell_' + nRow + '_' + n + '" src="' + compFolder() + '/dbgrid/images/' + cChecked + '" border="0">' ;
          }else{
            cValue = vaValue [n] ;
          }
        }
        innerHTML = cValue ;
        (function(db,cell){
          onclick = function(){db.ClickCell(cell,false,true);} ;
          ondblclick = function(){db.ClickCell(cell,true,true);} ;
          oncontextmenu = function(e){return db.conMenu(e,cell);};
        })(this,cell);
        
        // Kalau Jenisnya Check Box kita setting Event Click nya
        var _img = a.getById("ck_cell_" + nRow + "_" + n,cell) ;
        if(_img !== null){
          (function(dbg,nRow,n){
            _img.onmouseover = function(){dbg.checkOver(this,nRow,n);} ;
            _img.onmouseout = function(){dbg.checkOut(this,nRow,n);} ;
            _img.onclick = function(){dbg.cbClick(nRow,n);} ;
          })(this,nRow,n) ;
        }
        
        if(this.vaCol[n]["display"] == "hidden") style = "display:none" ;
      }
    }
    return nRow ;
  };
  // Untuk Field yang Jenis CheckBox kalau di click maka dia akan setting Variable
  this.cbClick = function(nRow,nCol){
    var img = this.GetCellContent(nRow,nCol,"img") ; ;
    var c = "uncheck.gif" ;
    if(img !== null && img.length > 0){ ;
      if(!this.isChecked(img[0])) c = "check.gif" ;
      img[0].src = compFolder() + "/dbgrid/images/" + c ;    
    }
  };
  // Field Checkbox jika di click posisi Header
  this.checkMark = function(img,nCol){
    var lCheck = false ;
    if(!this.isChecked(img)){
      lCheck = true ;
      img.src = compFolder() + "/dbgrid/images/check.gif" ;
    }else{
      img.src = compFolder() + "/dbgrid/images/uncheck.gif" ;
    }
    for(n=0;n<this.Rows();n++){
      this.CellUpdate(n,nCol,lCheck) ;
    }
  };
  this.checkOver = function(img,nRow,nCol){
    var c = "uncheck.gif" ;
    var c1 = "" ;
    if(this.isChecked(img)) c = "check.gif" ;  
    if(this.vaCol [nCol]["type"] == "radiobutton") c1 = "radio-" ;
    img.src = compFolder() + "/dbgrid/images/" + c1 + "over-"+c;
  };
  this.checkOut = function (img,nRow,nCol){
    var c = "uncheck.gif" ;
    var c1 = "" ;
  
    if(this.isChecked(img)) c = "check.gif" ;
    if(this.vaCol [nCol]["type"] == "radiobutton") c1 = "radio-" ;
    img.src = compFolder() + "/dbgrid/images/"+c1 + c;
  };
  this.HeaderValue = function(nCol){
    cRetval = "" ;
    if(this.tbHead.rows.length >= 1 && this.tbHead.rows[0].cells.length >= nCol) cRetval = this.tbHead.rows[0].cells[nCol].innerHTML ;
    return cRetval ;
  };
  this.HeaderUpdate = function(nCol,cValue){
    if(this.tbHead.rows.length >= 1 && this.tbHead.rows[0].cells.length >= nCol) this.tbHead.rows[0].cells[nCol].innerHTML = cValue ;
  };
  this.FooterValue = function(nCol){
    var cRetval = null ;    
    if(this.tbFoot !== null){
      if(this.tbFoot.rows.length >= 1 && this.tbFoot.rows[0].cells.length >= nCol) cRetval = this.tbFoot.rows[0].cells[nCol].innerHTML ;
    }
    return cRetval ;
  };
  this.FooterUpdate = function(nCol,cValue){
    if(this.tbFoot !== null){
      if(this.tbFoot.rows.length >= 1 && this.tbFoot.rows[0].cells.length >= nCol) this.tbFoot.rows[0].cells[nCol].innerHTML = cValue ;
    }
  };
  this.ScrollTop = function(nTop){
    if(typeof nTop == "number") this.om.scrollTop = Math.min(nTop,this.MaxScroll()) ;
    return this.om.scrollTop ;
  };  
  this.ScrollLeft = function(nLeft){
    if(typeof nLeft == "number") this.om.scrollLeft = nLeft ;
    return this.om.scrollLeft ;
  };
  this.MaxScroll = function(){
    return (this.om.scrollHeight - this.om.clientHeight) ;
  };
  this.GridContent = function(){    
    var cXML = "" ;    
    var nRow = this.Rows() ;
    var nCol = this.Cols() ;
    var n = 0 ;
    for(n=0;n<nCol;n++){
      if(cXML !== "") cXML += "~~cl~~" ;
      cXML += this.vaCol [n]['caption'] ;
    }
    for(n=0;n<nRow;n++){
      var vaRow = this.GetRow(n) ;
      cXML += encodeURIComponent("~~rw~~" + vaRow.join('~~cl~~')) ;
    }
    return cXML ;
  };
  this.AutoWidth = function(){
    for(var n = 0; n < this.Cols() ;n++){
      this.AutoWidthColl(n) ;
    }
    this.oh.style.width = "auto" ;
    this.ob.style.width = "auto" ;
    if(this.of !== null) this.of.style.width = "auto" ;
    this.initColResize() ;
  };
  this.AutoWidthColl = function(nCol){
    var css = a.getById("css_" + nCol + "_" + this.Name) ;
    css.innerHTML = "." + this.Name + "_" + nCol + "{}" ;
      
    var nh = (this.tbHead !== null && this.tbHead.rows.length > 0 && this.tbHead.rows [0].cells.length > nCol) ? this.tbHead.rows[0].cells[nCol].offsetWidth : 0 ;
    var nb = (this.tbBody !== null && this.tbBody.rows.length > 0 && this.tbBody.rows [0].cells.length > nCol) ? this.tbBody.rows[0].cells[nCol].offsetWidth : 0 ;
    var nf = (this.tbFoot !== null && this.tbFoot.rows.length > 0 && this.tbFoot.rows [0].cells.length > nCol) ? this.tbFoot.rows[0].cells[nCol].offsetWidth : 0 ;
    var w = Math.max(Math.max(nh,nb),nf) ;
    css.innerHTML = "." + this.Name + "_" + nCol + "{width:" + w + "px;min-width:" + w + "px;max-width:" + w + "px}";
  };
  this.bodyWidth = function(){
    if(this.ob !== null) return this.ob.offsetWidth ;
    if(this.oh !== null) return this.oh.offsetWidth ;
    return 0 ;
  };
  this.bodyHeight = function(){
    if(this.ob !== null) return this.ob.offsetHeight ;
    return 0 ;
  } ;
  this.conMenu = function(e,cell){
    var bg = a.addBack("RighMenu-Back") ;
    bg.id = "RighMenu-Back" ;
    bg.style.opacity = 0 ;
    (function(dbg,bg){
      bg.onclick = function(){dbg.mnuClose();} ;
    })(this,bg);

    var vaMouse = a.getCursor(e) ;
    var mb = a.addObj("div") ;    
    mb.id = "GRID-Contect-Menu" ;
    mb.className = "dbg_mnu_context no_txt_select" ;
    with(mb.style){
      top = vaMouse [1] ;
      left = vaMouse [0] ;
      width = "auto" ;
      height = "auto" ;
      display = "block" ;      
    }
    setObjIndex(mb) ;
    var mn = a.addObj("table",mb) ;
    mn.border = 0 ;
    mn.cellspacing = 0 ;
    mn.cellpadding = 0 ;
  
    var dbg = this ;
    this.addMenuItem("Copy",mn,function(){dbg.clickMenu(1,cell);}) ;
    this.addMenuItem("Copy HTML Format",mn,function(){dbg.clickMenu(2,cell);}) ;
    
    return false ;
  };
  this.clickMenu = function(nMenu,cell){
    var txt = (nMenu == 1) ? cell.textContent : cell.innerHTML ;
    this.copyToClipboard(txt) ;
    this.mnuClose() ;
  };
  this.addMenuItem = function(title,parent,callBack){
    var tr = a.addObj("tr",parent) ;
    var item = a.addObj("td",tr) ;
    item.className = "dbg_mnu_context_item no_txt_select" ;
    item.innerText = title ;
    item.onclick = callBack ;
  };
  this.mnuClose = function(){
    a.delById("RighMenu-Back") ;
    a.delById("GRID-Contect-Menu") ;
  };
  this.copyToClipboard = function(text) {
    var txt = a.addObj("textarea");
    with(txt.style){
      position='fixed';top=0;left=0;width='2em';height='2em';padding=0;border='none';outline='none';boxShadow='none';background='transparent';opacity=0 ;
    }
    txt.value = text;
    txt.select();
    try {
      document.execCommand('copy');
    } catch (err) {}
    a.delObj(txt) ;
  }
}