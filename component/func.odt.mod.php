<?php
  include 'df.php' ;

class ODT{
  var $tmpDir = "" ;
  var $outFile = "" ;
  var $vaFile = array() ;
  var $cExtension = "odt" ;
  var $lZip = false ;
  var $fileSeparator = "--file-separator--" ;
  public function Open($cFile){
    $this->vaFile = array() ;
    $this->tmpDir = $this->tmp() ;
    $vaFile = pathinfo($cFile) ;
    $this->lZip = true ;
    $this->cExtension = strtolower($vaFile ['extension']) ;
    if($this->cExtension == "odt" || $this->cExtension == "ods"){
      $this->vaFile = array($this->tmpDir . "/content.xml"=>0,$this->tmpDir . "/styles.xml"=>1) ;
    }else if($this->cExtension == "docx"){
      $this->vaFile = array($this->tmpDir . "/word/document.xml"=>0) ;
    }else if($this->cExtension == "xlsx"){
      $this->vaFile = array($this->tmpDir . "/xl/sharedStrings.xml"=>0) ;
    }else{
      $this->vaFile = array($cFile=>0) ;
      $this->lZip = false ;
    }

    // Untuk File Doc bukan berbasis xml jadi dia bukan merupakan file xml yang di zip.
    if($this->lZip){
      $cContent = "" ;
      $zip = new ZipArchive;
      if ($zip->open($cFile) === TRUE) {
        $zip->extractTo($this->tmpDir);
        $zip->close();
      }
    }

    $cContent = "" ;
    foreach($this->vaFile as $key=>$value){
      if(is_file($key)){
        if($cContent <> "") $cContent .= $this->fileSeparator ;

        $file = fopen($key,"r");
        $size_of_file = filesize($key);
        $cContent .= fread($file, $size_of_file);
        fclose($file);
      }
    }
    return $cContent ;
  }

  public function Save($content,$cPassword=null){    
    $this->outFile = "output_" . md5(rand(0,10000) . time() ) . "." . $this->cExtension ;
    if($this->lZip){
      $zip = new ZipArchive();
      $zip->open($this->tmpDir . "/" . $this->outFile, ZIPARCHIVE::OVERWRITE);
      $this->CreateODT($content,$this->tmpDir,$zip) ;
      /*
      // Jika kata sandi disediakan, simpan dalam file manifest
        if ($cPassword) {
            $zip->addFromString('meta.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <office:document-meta
                xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
                xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0">
                <meta:password>' . htmlspecialchars($cPassword) . '</meta:password>
            </office:document-meta>');
          //$zip->setPassword($cPassword) ;
        }
      */
      $zip->close();
    }else{
      $file = fopen($this->tmpDir . "/" . $this->outFile, "w");
      fwrite($file,$content) ;
      fclose($file) ; 
    }
    
    /*
    $cPassword = "aseqwewqe" ;
    if ($cPassword !== null) {
        $convert = 'libreoffice --headless --convert-to odt --outdir ' . escapeshellarg($this->tmpDir) . ' --password="' . escapeshellarg($cPassword) . '" ' . escapeshellarg($this->tmpDir . "/" . $this->outFile);
        exec($convert);
    }
    */
    
    return $this->tmpDir . "/" . $this->outFile ;
    
    /*
    // Escape file paths to prevent injection issues
    $inputFile        = $this->tmpDir . "/" . $this->outFile ;
    $outputDir        = $this->tmpDir ;
    $escapedInputFile = escapeshellarg($inputFile);
    $escapedOutputDir = escapeshellarg($outputDir);
    
    // Define the output PDF file path
    $outputFile = $outputDir . '/' . basename($inputFile, pathinfo($inputFile, PATHINFO_EXTENSION)) . '.pdf';
    $escapedOutputFile = escapeshellarg($outputFile);
    
    // Construct the LibreOffice command for conversion
    $cmd = "libreoffice --headless --convert-to pdf --outdir $escapedOutputDir $escapedInputFile";
     echo $cmd ;
    // Execute the command
    exec($cmd, $output, $return_var);

    // Check for errors
    if ($return_var !== 0) {
        throw new Exception("Error converting file to PDF: " . implode("\n", $output));
    }

    return $outputFile;
    */
  }
  
  private function convertToPdf($inputFile, $outputDir) {
    // Escape file paths to prevent injection issues
    $escapedInputFile = escapeshellarg($inputFile);
    $escapedOutputDir = escapeshellarg($outputDir);
    
    // Define the output PDF file path
    $outputFile = $outputDir . '/' . basename($inputFile, pathinfo($inputFile, PATHINFO_EXTENSION)) . '.pdf';
    $escapedOutputFile = escapeshellarg($outputFile);
    
    // Construct the LibreOffice command for conversion
    $cmd = "libreoffice --headless --convert-to pdf --outdir $escapedOutputDir $escapedInputFile";

    // Execute the command
    exec($cmd, $output, $return_var);

    // Check for errors
    if ($return_var !== 0) {
        throw new Exception("Error converting file to PDF: " . implode("\n", $output));
    }

    return $outputFile;
}

  function br(){
    $cRetval = '' ;
    if($this->cExtension == "docx"){
      $cRetval = "<w:p/>" ;
    }else if($this->cExtension == "odt"){
      $cRetval = '<text:line-break/>' ;
    }
    return  $cRetval ;
  }

  // Private Function
  private function CreateODT($content,$cDir,$zip){
    $vaContent = split($this->fileSeparator,$content) ;
    if(is_dir($cDir)){
      $d = dir($cDir) ;            
      while (false !== ($entry = $d->read())) {
        if(is_dir($cDir . '/' . $entry)){
          if($entry !== "." && $entry !== ".."){
            $this->CreateODT($content,$cDir . '/' . $entry,$zip) ;
          }
        }else{
          $cFileToZip = str_replace($this->tmpDir . "/","",$cDir. "/" . $entry) ;
          if(is_file($cDir . '/' . $entry) && $entry <> $this->outFile){
            $cF = $cDir . "/" . $entry ;
            if(isset($this->vaFile [$cF])){
              $nc = $this->vaFile [$cF] ;
              if(isset($vaContent [$nc])){
                $zip->addFromString($cFileToZip,$vaContent [$nc]);
              }
            }else{
              $zip->addFile($cDir . '/' . $entry,$cFileToZip);
            }
          }
        }
      }
      $d->close();
    }
  }
  
  private function tmp(){  
    $cDir = "../tmp" ;
    if(!is_dir($cDir)) mkdir($cDir,0777);
  
    $cDir = "../tmp/tmp" ;  
    $nDir = date("H")%3 ;
    $nDir1 = $nDir + 1 ;
    if($nDir1 == 3) $nDir1 = 0 ;

    if(is_dir($cDir . $nDir1)) $this->DelDir($cDir . $nDir1);
    if(!is_dir($cDir . $nDir)) mkdir($cDir . $nDir,0777);
    
    $cDir .= $nDir . "/w_" . md5(rand(0,10000) . time()) ;
    if(!is_dir($cDir)) mkdir($cDir,0777)  ;
    return  $cDir ;
  }

  private function DelDir($cDir){
    if(is_dir($cDir)){
      $d = dir($cDir) ;            
      while (false !== ($entry = $d->read())) {
        if(is_dir($cDir . '/' . $entry)){
          if($entry !== "." && $entry !== ".."){
            $this->DelDir($cDir . '/' . $entry) ;
          }
        }else{
          if(is_file($cDir . '/' . $entry)){
            unlink($cDir . '/' . $entry) ;
          }
        }
      }
      $d->close();
      rmdir($cDir) ;
    }
  }
}
$odt = new ODT ;
?>