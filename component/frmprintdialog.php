<?php
  include 'df.php' ;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Assistindo.Net</title>
</head>
<?php include 'frmprintdialog.jscript.php' ?>
<body onLoad="document.form1.cmdPreview.focus() ;"> 
<form name="form1" method="post" action="<?php echo($_SERVER['PHP_SELF'] . '?__par=' . getlink($__par,false)) ?>">
<table width="100%" height="100%" border="0" cellspacing="3" cellpadding="0" class="cell_eventrow">
  <tr>
    <td height="20px" style="border:1px solid #999999;padding:4px">
      <table width="100%"  border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td colspan="3"><strong>Margin</strong></td>
        </tr>
        <tr>
          <td width="120px">&nbsp;Top Margin</td>
          <td width="5px">:</td>
          <td>
          <?php
            $txt->Caption = "mm" ;
            $txt->NumberField("nTop","10.00","6","6") ;
          ?>
          </td>
        </tr>
        <tr>
          <td width="100px">&nbsp;Left Margin</td>
          <td width="5px">:</td>
          <td>
          <?php
            $txt->Caption = "mm" ;
            $txt->NumberField("nLeft","13.00","6","6") ;
          ?>
          </td>
        </tr>
        <tr>
          <td width="100px">&nbsp;Bottom Margin</td>
          <td width="5px">:</td>
          <td>
          <?php
            $txt->Caption = "mm" ;
            $txt->NumberField("nBottom","10.00","6","6") ;
          ?>
          </td>
        </tr>
        <tr>
          <td width="100px">&nbsp;Right Margin</td>
          <td width="5px">:</td>
          <td>
          <?php
            $txt->Caption = "mm" ;
            $txt->NumberField("nRight","7.00","6","6") ;
          ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="20px" style="border:1px solid #999999;padding:4px">
      <table width="100%"  border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td colspan="3"><strong>Paper Source</strong></td>
        </tr>
        <tr>
          <td width="120px">&nbsp;Paper</td>
          <td width="5px">:</td>
          <td>
          <select name="cPaper" size="1" onKeyUp="validate(this,event)">
            <option value="0" onClick="PaperSize();">A3</option>
            <option value="1" onClick="PaperSize();">A4</option>
            <option value="2" onClick="PaperSize();">FOLIO</option>
            <option value="3" onClick="PaperSize();">LEGAL</option>
            <option value="4" onClick="PaperSize();" selected>LETTER</option>
            <option value="99" onClick="SetCustom();">CUSTOM</option>
          </select>
          </td>
        </tr>
        <tr>
          <td width="100px">&nbsp;Width</td>
          <td width="5px">:</td>
          <td>
          <?php
            $txt->Caption = "Inc" ;
            $txt->onBlur = "FieldFormat(this)" ;
            $txt->NumberField("nWidth","","6","6",true) ;
          ?>
          </td>
        </tr>
        <tr>
          <td width="100px">&nbsp;Height</td>
          <td width="5px">:</td>
          <td>
          <?php
            $txt->Caption = "Inc" ;
            $txt->onBlur = "FieldFormat(this)" ;
            $txt->NumberField("nHeight","","6","6",true) ;
          ?>
          </td>
        </tr>
        <tr>
          <td width="100px">&nbsp;</td>
          <td width="5px"></td>
          <td>
          <?php
            $txt->Caption = "Export CSV" ;
            $txt->CheckBox("ckExportCSV","1") ;
          ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="20px" style="border:1px solid #999999">
      <table width="100%" style="padding:2px">
        <tr>
          <td align="right">
          <?php
            $txt->ButtonField("cmdPreview","Preview") ;

            $txt->onClick = "CloseForm();" ;
            $txt->ButtonField("cmdCancel","Cancel") ;
          ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>
</body>
</html>