<?php include 'df.php' ; ?>
<script language="javascript" type="text/javascript">
var cFormName = "<?php echo($cFormName) ?>" ;
function cmdPreview_onClick(field){
  ajax('','SaveConfig()',GetFormContent()) ;
}

function Preview(){
var o = self.parent.a.getById(cFormName + "_formbody") ;
  if(o !== null){
    o.contentWindow.__OpenReport() ;
  }
  CloseForm() ;
}

function Form_onLoad(){
  PaperSize(document.form1.cPaper.value) ;
  document.form1.cmdPreview.focus() ;
}

function PaperSize(){
var vaPaper = [[11.69,16.54],[8.27,11.69],[8.5,13],[8.5,14],[8.5,11]] ;
  with(document.form1){
    
    nWidth.readOnly = true ;
    nHeight.readOnly = true ;
    
    nWidth.value = vaPaper[cPaper.value][0] ;
    nHeight.value = vaPaper[cPaper.value][1] ;
    
    nWidth.value = Number2String(nWidth.value,2) ;
    nHeight.value = Number2String(nHeight.value,2) ;
  }
}

function SetCustom(){
  with(document.form1){
    nWidth.readOnly = false ;
    nHeight.readOnly = false ;
    
    fieldfocus(nWidth) ;
  }
}

function FieldFormat(field){
  field.value = String2Number(field.value) ;
  field.value = Number2String(field.value,2) ;
}
</script>