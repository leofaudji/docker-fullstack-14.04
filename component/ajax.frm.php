<?php
  include 'df.php' ;
?>
var frm = {
  va:{},
  open: function(URL,cFormName,cTitle,nWidth,nHeight,cBackColor,lShowModal,cFormScroll,lHideToolBox,cFrameName,lReport){
    if(!cFormScroll) cFormScroll = "no" ;

    // Jika Disebutkan Target Framename nya maka kita cari Object nya untuk di jadikan Parent.
    var ow = window ;    
    if(cFrameName && cFrameName !== ""){
      ow = a.getById(cFrameName) ;
      ow = (ow !== null) ? ow = ow.contentWindow : ow = window ;
    }

    // Kalau Show Modal Kita Buat Background Sebesar Monitor supaya bagian Belakang Tidak Bisa di Buka
    if(lShowModal) ow.a.addBack(cFormName + "-showModal") ;
    if(lReport == null) lReport = false ;
    var lmdi = ow.name == "mainFrame" ;

    // Create Main Div Untuk Form utama
    var oDiv = ow.a.getById(cFormName) ;
    if(oDiv !== null){
      // Kalau Sudah Ada Berarti dia sudah di buat dengan status Minimize Tinggal kita munculkan saja dan abaikan menu di bawahnya
      ow.frm.frmReOpen(oDiv,ow,cFormName) ;
      a.g()._winList [cFormName]['min'] = false ;
      return true ;
    }else{
      oDiv = ow.a.addObj("div") ;
      oDiv.id = cFormName ;
      oDiv.className = "win_main fadein" ;
      
      // Parent -> Header
      // Parent -> Header -> Title
      // Parent -> Header -> Toolbar
      // Parent -> Body
      
      // Create Header - Parent (oDiv)
      var oh = ow.a.addObj("div",oDiv) ;
      oh.onmousedown = function(e){ow.frm.startMove(oDiv,e,cFormName) ;} ;
      oh.id = cFormName + '-header' ;
      oh.className = "win_header win_header_focus" ;     

      // Create Title - Parent (Header)
      var ot = ow.a.addObj("div",oh) ;
      ot.id = cFormName + '-title' ;
      ot.className = "win_title" ;
      ot.innerHTML = cTitle ;
      
      // Create Toolbar  - Parent (Header)
      if(!lHideToolBox){
        if(!lReport && !lShowModal && lmdi){
          var ot = ow.a.addObj("div",oh) ;
          ot.title = "Minimize" ;
          ot.id = cFormName + '-min' ;
          ot.className = "win_icon_min" ;
          ot.onclick = function(){ow.frm.min(cFormName,lReport)} ;
          ot.innerText = "-" ;
        }else{
          lmdi = false ;
        }
        
        var ot = ow.a.addObj("div",oh) ;
        ot.title = "Close" ;
        ot.id = cFormName + '-close' ;
        ot.className = "win_icon_close" ;
        ot.onclick = function(){ow.frm.close(cFormName,lReport)} ;
        ot.innerText = "x" ;
      }

      // Add Body - Parent (oDiv)
      var ob = ow.a.addObj("div",oDiv) ;
      ob.id = cFormName + "-border" ;
      ob.className = "win_border" ;
      var cLoad = (lReport) ? "" : 'onLoad = "frm.onLoad(\'' + cFormName + '\')"' ;
      ob.innerHTML = '<iframe name="' + cFormName + '"' + cLoad + ' id="' + cFormName + '_formbody" scrolling="'+cFormScroll+'" src="' + URL + '" width="100%" height="100%" frameborder="0"></iframe>' ;
    }

    // Tambahkan kedalam Windows List dengan Syarat jenis nya MDI
    if(lmdi) frm.addList(cFormName,cTitle,ow,oDiv,lReport) ;    

    var nWinHeight = Math.min(ow.document.body.clientHeight,screen.height) ;
    var nWinWidth = Math.min(ow.document.body.clientWidth,screen.width) ;
    var nTop = Math.max((nWinHeight - nHeight)/2,0) + document.body.scrollTop ;
    var nLeft = Math.max((nWinWidth - nWidth)/2,0) + document.body.scrollLeft ;
    with(oDiv.style){
      width = nWidth ;
      height = nHeight ;
      top = nTop ;
      left = nLeft ;
    }
    this.va [cFormName] = {oDiv:oDiv,report:lReport,title:cTitle};

    var oBorder = ow.a.getById(cFormName + "-border") ;
    var oHeader = ow.a.getById(cFormName + "-header") ;    
    oBorder.style.height = oDiv.offsetHeight - oHeader.offsetHeight ;
    oBorder.style.top = oHeader.offsetHeight ;
    oBorder.style.width = oDiv.offsetWidth ;
    oBorder.style.width = oDiv.offsetWidth - (oBorder.offsetWidth - oDiv.offsetWidth) ;
    
    var nILeft = oHeader.offsetWidth - 6 ;
    var oClose = ow.a.getById(cFormName + "-close") ;    
    if(oClose !== null){
      oClose.style.top = (oHeader.offsetHeight/2) - (oClose.offsetHeight/2) ;
      oClose.style.left = nILeft - oClose.offsetWidth ;
      nILeft = oClose.offsetLeft - 1 ;
    }

    var oMin = ow.a.getById(cFormName + "-min") ;
    if(oMin !== null){
      oMin.style.top = (oHeader.offsetHeight/2) - (oMin.offsetHeight/2) ;
      oMin.style.left = nILeft - oMin.offsetWidth - 1 ;
    }
    
    ow.setObjIndex(oDiv) ;
  },
  frmReOpen: function(oDiv,ow,cFormName){
    var o = ow.a.getById(cFormName) ;
    o.className = "win_main fadein" ;
    oDiv.style.display = "block" ;
    ow.setObjIndex(oDiv) ;
    ow.frm.changeForm(cFormName) ;
  },
  clickForm: function(cName){
    var o = a.getById(cName) ;
    var oh = a.getById(cName + "-header") ;
    if(oh !== null) oh.className = "win_header win_header_focus" ;
    
    // Jika Old Windows Tidak Kosong Dan Tidak sama dengan Name maka kita lost focus kan.
    this.changeForm(cName) ;

    var n = 0 ;
    var p = window ;
    while(n ++ < 10){
      p = p.self.parent ;
      if(p == null || p.name == "") n = 10 ;
    }
    if(o !== null) setObjIndex(o) ;
  },
  changeForm:function(cName){
    var cOldWin = a.g().fWin ;
    var o = a.getById(cName + "-header") ;
    // Jika ada di Windows List berarti mdi kalau tidak ada abaikan
    if(typeof a.g()._winList [cName] !== "undefined"){
      if(cOldWin !== "" && cOldWin !== cName){
        var old = a.getById(cOldWin + "-header") ;
        if(old !== null) old.className = "win_header win_header_blur" ;
        // Untuk Form yang di Leave maka kita kasih div seukuran Windows biar kalau di click bisa pindah langsung di posisi manapun
        // Dengan Syarat Bukan Merupakan Laporan, Untuk Menghindari Error.
        var oldDoc = a.getById(cOldWin + "_formbody") ;
        if(oldDoc !== null && !a.g()._winList[cOldWin]['report']) oldDoc.contentWindow.a.addBack("__divBack_blur") ;

        if(typeof a.g()._winList [cOldWin] !== "undefined") a.g()._winList [cOldWin]['active'] = false ;
      }
      if(o !== null){
        var od = a.getById(cName + "_formbody") ;
        // Hapus Background Div di Body kalau dia active dengan asumsi dia bukan Report
        if(od !== null && !a.g()._winList[cName]['report']){
          if(typeof od.contentWindow.a !== "undefined") od.contentWindow.a.delById("__divBack_blur") ;
        }
        o.className = "win_header win_header_focus" ;
      }
      a.g().fWin = cName ;
      a.g()._winList [cName]['active'] = true ;
    
      // Buat List untuk mengurutkan Windows yang aktif, sehingga kalau di tutup akan focus ke form terakhir
      a.g().fWList = _grandWin().fWList.replace("," + cName + ",",",") ;
      if(a.g().fWList.indexOf("," + cName + ",") == -1) a.g().fWList += cName + "," ;
    }
  },
  onLoad:function(cName){
    var o = a.getById(cName + "_formbody") ;
    this.changeForm(cName) ;
    if(o !== null){
      var ow = o.contentWindow ;
      ow.onfocus = function(){frm.clickForm(cName)} ;
      if(typeof ow.Form_onLoad == 'function') ow.Form_onLoad() ;

      if(o.contentDocument !== null){
        var od = o.contentDocument ;
        if(o.scrolling == "no"){
          o.contentDocument.onscroll = function(){
            with(o.contentDocument.body){
              scrollLeft = 0 ;
              scrollTop = 0 ;
            }
          }
        }
        // Tambah Hidden Field dengan Nama _MENU_ Untuk Update di Log MenuNumber
        if(typeof od.form1 !== "undefined"){
          if(typeof od.form1._MENU_ == "undefined"){
            var _in = a.addObj("input",od.form1) ;
            _in.setAttribute("type", "hidden");
            _in.setAttribute("name", "_MENU_");
            _in.setAttribute("value", _grandWin().a.mnuClick);
          }
        }
         _grandWin().a.mnuClick = "" ; // Reset Value Menu Click
      }
    }
  },
  min:function(cName,lReport){
    var o = a.getById(cName) ;
    if(o !== null){
      o.className = "win_main fadeout" ;
      // Pindahkan Posisi Form yang Hiden ke paling Depan artinya urutan terakhir
      // Supaya Form berikutnya bisa di Status Aktif
      a.g().fWList = a.g().fWList.replace("," + cName + ",",",") ;
      a.g().fWList = "," + cName + a.g().fWList ;

      // Buka Form yang terakhir
      frm.openLast(cName) ;

      var nWait = setInterval(function () {
        clearInterval(nWait);

        o.style.display = "none" ;
        a.g()._winList [cName]['active'] = false ;
        a.g()._winList [cName]['min'] = true ;      
      },800) ;
    }
  },
  close:function(cName,lReport){
    var o = window ;
    // Kalau Name Kosong Maka Kita ambil Dari Induk nya, karena dia di execusi dari Dalam Iframe.
    if(cName == null){
      cName = window.name ;
      o = self.parent ;
    }
    var od = o.a.getById(cName) ;
    var ob = o.a.getById(cName + "-showModal") ;
    var obody = o.a.getById(cName + "_formbody") ;
    var lClose = true ;
    if( od !== null){
      if(!lReport){
        if(obody !== null){
          if(typeof obody.contentWindow.Form_onClose == 'function') lClose = obody.contentWindow.Form_onClose() ;
        }
      }
      if(lClose){
        if(obody !== null){
          var _odb = obody.contentDocument ;
          if(_odb !== null){
            if(typeof _odb !== "undefined"){
              if(typeof _odb.form1 !== "undefined"){
                if(typeof _odb.form1._MENU_ !== "undefined"){
                  a.ajax(__COMPONENT_FOLDER__ + "/ajax.ajax.php","UpdLogCloseMenu()","_MENU_=" + _odb.form1._MENU_.value) ;
                }
              }
            }
          }
        }
        od.className = "win_main fadeout" ;

        // Hapus Element di json
        frm.delList(cName) ;

        var nWait = setInterval(function () {
          clearInterval(nWait);
          a.delObj(od);
          a.delObj(ob);

          // Hapus Terlebih dahulu Form yang terakhir
          a.g().fWList = a.g().fWList.replace("," + cName + ",",",") ;

          // Buka Form yang terakhir
          frm.openLast(cName) ;
        }, 800);
      }
    }
  },
  // Kita Akan mencari Form terakhir yang di buka kalau ada maka Form itu akan kita Buka sebagai Form Active
  openLast:function(cName){    
    var va = a.g().fWList.split(",") ;
    var cLast = "" ;
    if(va.length >= 3) cLast = va [va.length-2] ;
    if(cLast !== "") this.changeForm(cLast) ;
  },
  startMove: function(o,e,cName){
    var oMove = a.getById(cName) ;
    var oTitle = a.getById(cName + "-title") ;
    this.clickForm(cName) ;
    setObjIndex(oMove) ;
    o.style.cursor = "move" ;
    if(oTitle !== null) oTitle.style.cursor = "move" ;
    a.obj_move_start(oMove,e,function(){frm.moveStop(o,oTitle)}) ;
  },
  moveStop:function(o,ot){
    if(o !== null) o.style.cursor = "default" ;
    if(ot !== null) ot.style.cursor = "default" ;
  },
  addList: function(cName,cTitle,ow,oDiv,lReport){
    a.g()._winList [cName] = {title:cTitle,active:true,frm:oDiv,ow:ow,min:false,report:lReport};
    a.slideBarItem() ;
  },
  delList: function(cName){
    delete a.g()._winList [cName] ;
    a.g().a.delById("__sl_item_" + cName + "__") ;
  }
};

var tab = {
  oldTab:null,oldContent:null,nH:0,cTabName:"",
  init: function(tab,tabBody,cName,cTabList){
    var bd = a.getById(cName + "-body") ;
    var vaTab = cTabList.split(",") ;
    for(var n = 0;n < vaTab.length; n ++){
      // Check Tab Content didalam mainBody kalau tidak ada maka cek di document kalau ada pindahkan.
      var ct = a.getById(vaTab [n],bd) ;
      if(ct == null){
        ct = a.getById(vaTab [n]) ;
        if(ct !== null){
          ct.className = "tab_content" ;
          bd.appendChild(ct);
        }
      }
    }

    this.oldTab = null ;
    this.oldContent = null ;
    this.nH = 0 ;
    this.cTabName = cName ;
    this.Click(tab,tabBody,cName) ;
  },
  Click: function(tab,cidBody,cMainName){
    if(this.oldTab !== null && this.oldTab.id !== tab.id) this.oldTab.className = "tab_item tab_normal no_txt_select" ;
    tab.className = "tab_item tab_click no_txt_select" ;
    this.oldTab = tab ;

    var bd = a.getById(this.cTabName + "-body") ;
    if(this.nH == 0) this.nH = bd.offsetHeight ;

    var oc = a.getById(cidBody,bd) ;
    if(oc == null){
      var oc = a.getById(cidBody) ;
      if(oc !== null) bd.appendChild(oc);
    }

    if(oc !== null){
      oc.style.display = "block" ;
      oc.style.height = this.nH - 8 ;

      if(this.oldContent !== null && this.oldContent.id !== oc.id) this.oldContent.style.display = "none" ;
      this.oldContent = oc ;
    }
  }
}