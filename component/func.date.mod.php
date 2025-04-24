<?php
  include 'df.php' ;
  
class sisDate {
  const sec_Min = 60 ;
  const sec_Hour = 3600 ;
  const sec_Day = 86400 ;
  const sec_Year = 31536000 ;
  const sec_Year4 = 31622400 ;

  function Now(){
    return time() ;
  }
  
  function Date($cFormat,$nTime){
    $vaDate = $this->GetDate($nTime) ;
    foreach($vaDate as $key=>$value){
      $cFormat = str_ireplace($key,$value,$cFormat) ;
    }
    return $cFormat ;
  }
  
  private function GetDate($nTime){
    $va = array("Y"=>1970,"m"=>1,"d"=>1,"H"=>0,"i"=>0,"s"=>0) ;
    $nYear = self::sec_Year ;
    while($nTime >= $nYear){
      $nTime -= $nYear ;

      $va ['Y'] ++ ;
      $nYear = self::sec_Year ;
      if($this->isKabisat($va ['Y'])) $nYear = self::sec_Year4 ;
    }
    
    $nMonth = $this->GetMonSec($va ['m'],$va ['Y']) ;
    while($nTime >= $nMonth && $va ['m'] < 12){
      $nTime -= $nMonth ;
      $va ['m'] ++ ;

      $nMonth = $this->GetMonSec($va ['m'],$va ['Y']) ;
    }

    while($nTime >= self::sec_Day){
      $nTime -= self::sec_Day ;
      $va ['d'] ++ ;
    }

    while($nTime >= self::sec_Hour){
      $nTime -= self::sec_Hour ;
      $va ['H'] ++ ;
    }
    return $va ;
  }

  function mkTime($nHour=0,$nMinute=0,$nSecond=0,$nMonth=1,$nDay=1,$nYear=1970){
    $nRetval = $nHour * self::sec_Hour ;
    $nRetval += $nMinute * self::sec_Min ;
    $nRetval += $nSecond ;
    $nRetval += ($nDay-1) * self::sec_Day ;
    
    for($n=1;$n<$nMonth;$n++){
      $nRetval += $this->GetMonSec($n,$nYear) ;
    }
    
    for($n=1970;$n<$nYear;$n++){
      $nRetval += $this->GetYearSec($n) ;
    }
    return $nRetval ;
  }
  
  private function GetYearSec($nYear){
    $nRetval = self::sec_Year ;
    if($this->isKabisat($nYear)) $nRetval = self::sec_Year4 ;
    return $nRetval ;
  }
  
  private function GetMonSec($nMonth,$nYear){
    $nRetval = 0 ;
    $nMonth -- ;
    $va = array(31,28,31,30,31,30,31,31,30,31,30,31) ;
    if(isset($va [$nMonth])) $nRetval = $va [$nMonth] * self::sec_Day ;
    if($nMonth == 1 && $this->isKabisat($nYear)) $nRetval += self::sec_Day ;
    return $nRetval ;
  }
  
  private function isKabisat($nYear){
    return $nYear % 4 == 0 ;
  }
}
$date = new sisDate ;
?>