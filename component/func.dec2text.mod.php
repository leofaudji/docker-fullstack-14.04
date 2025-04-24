<?php
  include 'df.php' ;

function Dec2Text($nDec,$lRupiah=true,$nRound=2){
  $nDec = number_format($nDec,$nRound,'.','') ;
  $vaDec = split("\.",$nDec) ;
  $cRetval = "" ;
  if($vaDec [0] < 11){
    $cRetval .= Satuan($vaDec [0]) ;
  }else if($vaDec [0] <= 99){
    $cRetval .= Puluan($vaDec [0]) ;
  }else if($vaDec [0] <= 999){
    $cRetval .= Ratusan($vaDec [0]) ;
  }else if($vaDec [0] <= 999999){
    $cRetval .= Ribuan($vaDec [0]) ;
  }else if($vaDec [0] <= 999999999){
    $cRetval .= Jutaan($vaDec [0]) ;
  }else if($vaDec [0] <= 999999999999){
    $cRetval .= Milyard($vaDec [0]) ;
  }else if($vaDec [0] <= 999999999999999){
    $cRetval .= Trilyon($vaDec [0]) ;
  }

  if(isset($vaDec [1]) && floatval($vaDec [1]) > 0){
    $cRetval .= "Koma " . Dec2Text($vaDec [1],false) ;
  }
  if($lRupiah) $cRetval .= "Rupiah " ;

  return $cRetval ;
}

function Satuan($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){
    $vaSatuan = array("Satu", "Dua", "Tiga", "Empat", "Lima","Enam", "Tujuh", "Delapan", "Sembilan",
                      "Sepuluh", "Sebelas") ;
    $cRetval = $vaSatuan [$nDec-1] . " " ;
  }
  return $cRetval ;
}

function Puluan($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){
    if($nDec <= 11){
      $cRetval .= Satuan($nDec) ;
    }else if($nDec <= 19){
      $cRetval .= Satuan(substr($nDec,1,1)) . "Belas " ;
    }else if($nDec <= 99){
      $cRetval .= Satuan(substr($nDec,0,1)) . "Puluh " ;
      $cRetval .= Satuan(substr($nDec,1,1)) ;
    }
  }
  return $cRetval ;
}

function Ratusan($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){  
    if($nDec <= 99){
      $cRetval .= Puluan($nDec) ;
    }else if($nDec <= 199){
      $cRetval .= "Seratus " . Puluan(substr($nDec,1)) ;
    }else if($nDec <= 999){
      $cRetval = Satuan(substr($nDec,0,1)) . "Ratus " . Puluan(substr($nDec,1)) ;
    }
  }
  return $cRetval ;
}

function Ribuan($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){
    if($nDec <= 999){
      $cRetval .= Ratusan($nDec) ;
    }else if($nDec <= 1999){
      $cRetval .= "Seribu " . Ratusan(substr($nDec,1)) ;
    }else if($nDec <= 999999){
      $cDecimal = str_pad($nDec,6,"0",STR_PAD_LEFT) ;
      $cRetval .= Ratusan(substr($cDecimal,0,3)) . "Ribu " . Ratusan(substr($cDecimal,3)) ;
    }
  }
  return $cRetval ;
}

function Jutaan($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){
    if($nDec <= 999999){
      $cRetval .= Ribuan($nDec) ;
    }else if($nDec <= 999999999){
      $cDecimal = str_pad($nDec,9,"0",STR_PAD_LEFT) ;
      $cRetval .= Ratusan(substr($cDecimal,0,3)) . "Juta " ;
      $cRetval .= Ribuan(substr($cDecimal,3)) ;
    }
  }
  return $cRetval ;
}

function Milyard($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){
    if($nDec <= 999999999){
      $cRetval .= Jutaan($nDec) ;
    }else if($nDec <= 999999999999){
      $cDecimal = str_pad($nDec,12,"0",STR_PAD_LEFT) ;
      $cRetval .= Ratusan(substr($cDecimal,0,3)) . "Milyard " ;
      $cRetval .= Jutaan(substr($cDecimal,3)) ;
    }
  }
  return $cRetval ;
}

function Trilyon($nDec){
  $cRetval = "" ;
  $nDec = number_format($nDec,0,'','') ;
  if($nDec > 0){
    if($nDec <= 999999999999){
      $cRetval .= Milyard($nDec) ;
    }else if($nDec <= 999999999999999){
      $cDecimal = str_pad($nDec,15,"0",STR_PAD_LEFT) ;
      $cRetval .= Ratusan(substr($cDecimal,0,3)) . "Trilyon " ;
      $cRetval .= Milyard(substr($cDecimal,3)) ;
    }
  }
  return $cRetval ;
}
?>