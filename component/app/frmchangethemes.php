<?php
  include 'df.php' ;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Assistindo.Net</title>
</head>
<?php include GetFileModul(__FILE__,'.jscript.php') ?>
<body>
<form name="form1" method="post" action="<?php echo($_SERVER['PHP_SELF'] . '?__par=' . getlink($__par,false)) ?>">
<table width="100%" height="100%" border="0" cellspacing="3" cellpadding="0" class="cell_eventrow">
  <tr>
    <td style="border:1px solid #999999;padding:4px">
      <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td width="300px">
          <?php
            $va [1] = array("No"=>1,"Name"=>"Default","Folder"=>"default") ;
            $va [2] = array("No"=>2,"Name"=>"Aero Lite","Folder"=>"aero") ;
            $va [3] = array("No"=>3,"Name"=>"Modern Aero","Folder"=>"win10") ;
            $va [4] = array("No"=>4,"Name"=>"Radiant Red","Folder"=>"merah") ;
            $va [6] = array("No"=>5,"Name"=>"Cool Green","Folder"=>"hijauui") ;
            $va [9] = array("No"=>6,"Name"=>"Cool Orange","Folder"=>"jinggaui") ;
            $va [10] = array("No"=>7,"Name"=>"Modern Blue","Folder"=>"biruui") ;
            $va [11] = array("No"=>8,"Name"=>"Radiant Mazarine","Folder"=>"nilaui") ;
            $va [12] = array("No"=>9,"Name"=>"Radiant Magenta","Folder"=>"purple") ;
            $va [13] = array("No"=>10,"Name"=>"Black Mate","Folder"=>"blackui") ;
            
            $dbg->Array = $va ;

            $dbg->Height = "100%";
            $dbg->Col ['No'] = array("Align"=>"center","Width"=>40) ;
            $dbg->Col ['Name'] = array("Width"=>245) ;
            $dbg->Col ['Folder'] = array("Display"=>"hidden") ;
            $dbg->Scrolling = "vertical" ;
            $dbg->dataBind() ;
          ?>
          </td>
          <td width="2px" nowrap></td>
          <td>
          <iframe width="100%" height="100%" style="border:1px solid #999999" id="frmDisplay"></iframe>
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
            $txt->ButtonField("cmdApply","Apply") ;

            $txt->onClick = "frm.close();" ;
            $txt->ButtonField("cmdCancel","Close") ;
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