<?php include 'df.php' ; ?>
<script language="javascript" type="text/javascript">
var oDisp = null ;
var cFolder = "" ;
function DBGRID1_onClick(vaRow,nCol){
  if(oDisp == null) oDisp = a.getById("frmDisplay") ;
  oDisp.src = compFolder() + "/app/frmchangethemes.disp.php?css=" + vaRow [2] + "&compFolder=" + compFolder() ;
  cFolder = vaRow [2] ;
}

function cmdApply_onClick(){
  a.confirm("Tema disimpan ?","Confirm",function(par){
    if(par){
      a.ajax('','ApplayThemes()',"cFolder=" + cFolder,function(cData,nStatus){
        if(cData == "ok"){
          a.confirm("Untuk mengaktifkan Themes ini anda harus refresh Halaman. \n Anda ingin Refreh halaman ini ?","Confirm",function(par){
            if(par){
              var win = self.parent ;
              var n = 0 ;
              while(win.name !== "mainFrame" && n < 10){
                n ++ ;
                win = win.self.parent ;
              }
              win.self.parent.location.reload() ;
            }
          }) ;
        }else{
          a.alert(cData) ;
        }
      }) ;
    }
  }) ;
}
</script>