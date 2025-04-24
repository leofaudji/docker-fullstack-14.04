<?php
class DOMPDF {
  
  
  /**
   * DomDocument representing the HTML document
   *
   * @var DomDocument
   */
  protected $_xml;

  /**
   * Frame_Tree derived from the DOM tree
   *
   * @var Frame_Tree
   */
  protected $_tree;

  /**
   * Stylesheet for the document
   *
   * @var Stylesheet
   */
  protected $_css;

  /**
   * Actual PDF renderer
   *
   * @var Canvas
   */
  protected $_pdf;

  /**
   * Desired paper size ('letter', 'legal', 'A4', etc.)
   *
   * @var string
   */
  protected $_paper_size;

  /**
   * Paper orientation ('portrait' or 'landscape')
   *
   * @var string
   */
  protected $_paper_orientation;

  private $_cache_id;

  /**
   * Base hostname
   *
   * Used for relative paths/urls
   * @var string
   */
  protected $_base_host;

  /**
   * Absolute base path
   *
   * Used for relative paths/urls
   * @var string
   */
  protected $_base_path;

  /**
   * Protcol used to request file (file://, http://, etc)
   *
   * @var string
   */
  protected $_protocol;
  

  /**
   * Class constructor
   */
  function __construct() {
    $this->_messages = array();
    $this->_xml = new DomDocument();
    $this->_xml->preserveWhiteSpace = true;
    $this->_tree = new Frame_Tree($this->_xml);
    $this->_css = new Stylesheet();
    $this->_pdf = null;
    $this->_paper_size = "letter";
    $this->_paper_orientation = "portrait";
    $this->_base_host = "";
    $this->_base_path = "";
    $this->_cache_id = null;
  }

  /**
   * Returns the underlying {@link Frame_Tree} object
   *
   * @return Frame_Tree
   */
  function get_tree() { return $this->_tree; }

  //........................................................................ 

  /**
   * Sets the protocol to use
   *
   * @param string $proto
   */
  // FIXME: validate these
  function set_protocol($proto) { $this->_protocol = $proto; }

  /**
   * Sets the base hostname
   *
   * @param string $host
   */
  function set_host($host) { $this->_base_host = $host; }

  /**
   * Sets the base path
   *
   * @param string $path
   */
  function set_base_path($path) { $this->_base_path = $path; }

  /**
   * Returns the protocol in use
   *
   * @return string
   */
  function get_protocol() { return $this->_protocol; }

  /**
   * Returns the base hostname
   *
   * @return string
   */
  function get_host() { return $this->_base_host; }

  /**
   * Returns the base path
   *
   * @return string
   */
  function get_base_path() { return $this->_base_path; }
  
  /**
   * Return the underlying Canvas instance (e.g. CPDF_Adapter, GD_Adapter)
   *
   * @return Canvas
   */
  function get_canvas() { return $this->_pdf; }
  
  //........................................................................ 

  /**
   * Loads an HTML file
   *
   * Parse errors are stored in the global array _dompdf_warnings.
   *
   * @param string $file a filename or url to load
   */
  function load_html_file($file) {
    // Store parsing warnings as messages (this is to prevent output to the
    // browser if the html is ugly and the dom extension complains,
    // preventing the pdf from being streamed.)
    list($this->_protocol, $this->_base_host, $this->_base_path) = explode_url($file);
    
    if ( !DOMPDF_ENABLE_REMOTE &&
         ($this->_protocol != "" && $this->_protocol != "file://" ) )
      throw new DOMPDF_Exception("Remote file requested, but DOMPDF_ENABLE_REMOTE is false.");
         
    if ( !DOMPDF_ENABLE_PHP ) {
      set_error_handler("record_warnings");
      $this->_xml->loadHTMLFile($file);
      restore_error_handler();

    } else
      $this->load_html(file_get_contents($file));

  }

  /**
   * Loads an HTML string
   *
   * Parse errors are stored in the global array _dompdf_warnings.
   *
   * @param string $str HTML text to load
   */
  function load_html($str) {
    $str = str_replace("--page-break--",'<div style="page-break-before:always"></div>',$str) ;
    // Parse embedded php, first-pass
    if ( DOMPDF_ENABLE_PHP ) {
      ob_start();
      eval("?" . ">$str");
      $str = ob_get_contents();      
      ob_end_clean();
    }

    // Store parsing warnings as messages
    set_error_handler("record_warnings");
    $this->_xml->loadHTML($str);
    restore_error_handler();
  }

  /**
   * Builds the {@link Frame_Tree}, loads any CSS and applies the styles to
   * the {@link Frame_Tree}
   */
  protected function _process_html() {
    $this->_tree->build_tree();
    
    $this->_css->load_css_file(Stylesheet::DEFAULT_STYLESHEET);    

    // load <link rel="STYLESHEET" ... /> tags
    $links = $this->_xml->getElementsByTagName("link");    
    foreach ($links as $link) {
      if ( mb_strtolower($link->getAttribute("rel")) == "stylesheet" ||
           mb_strtolower($link->getAttribute("type")) == "text/css" ) {
        $url = $link->getAttribute("href");
        $url = build_url($this->_protocol, $this->_base_host, $this->_base_path, $url);
        
        $this->_css->load_css_file($url);
      }

    }

    // load <style> tags
    $styles = $this->_xml->getElementsByTagName("style");
    foreach ($styles as $style) {

      // Accept all <style> tags by default (note this is contrary to W3C
      // HTML 4.0 spec:
      // http://www.w3.org/TR/REC-html40/present/styles.html#adef-media
      // which states that the default media type is 'screen'
      if ( $style->hasAttributes() &&
           ($media = $style->getAttribute("media")) &&
           !in_array($media, Stylesheet::$ACCEPTED_MEDIA_TYPES) )
        continue;
      
      $css = "";
      if ( $style->hasChildNodes() ) {
        
        $child = $style->firstChild;
        while ( $child ) {
          $css .= $child->nodeValue; // Handle <style><!-- blah --></style>
          $child = $child->nextSibling;
        }
        
      } else
        $css = $style->nodeValue;

      // Set the base path of the Stylesheet to that of the file being processed
      $this->_css->set_protocol($this->_protocol);
      $this->_css->set_host($this->_base_host);
      $this->_css->set_base_path($this->_base_path);

      $this->_css->load_css($css);
    }
    
  }

  //........................................................................ 

  /**
   * Sets the paper size & orientation
   *
   * @param string $size 'letter', 'legal', 'A4', etc. {@link CPDF_Adapter::$PAPER_SIZES}
   * @param string $orientation 'portrait' or 'landscape'
   */
  function set_paper($size, $orientation = "portrait") {
    $this->_paper_size = $size;
    $this->_paper_orientation = $orientation;
  }
  
  //........................................................................ 

  /**
   * Enable experimental caching capability
   * @access private
   */
  function enable_caching($cache_id) {
    $this->_cache_id = $cache_id;
  }
  
  //........................................................................ 

  /**
   * Renders the HTML to PDF
   */
  function render() {

    //enable_mem_profile();
    
    $this->_process_html();

    $this->_css->apply_styles($this->_tree);

    $root = null;
    
    foreach ($this->_tree->get_frames() as $frame) {

      // Set up the root frame
      if ( is_null($root) ) {
        $root = Frame_Factory::decorate_root( $this->_tree->get_root(), $this );
        continue;
      }

      // Create the appropriate decorators, reflowers & positioners.
      $deco = Frame_Factory::decorate_frame($frame, $this);
      $deco->set_root($root);

      // FIXME: handle generated content
      if ( $frame->get_style()->display == "list-item" ) {
        
        // Insert a list-bullet frame
        $node = $this->_xml->createElement("bullet"); // arbitrary choice
        $b_f = new Frame($node);

        $style = $this->_css->create_style();
        $style->display = "-dompdf-list-bullet";
        $style->inherit($frame->get_style());
        $b_f->set_style($style);
        
        $deco->prepend_child( Frame_Factory::decorate_frame($b_f, $this) );
      }
    }
    
    $this->_pdf = Canvas_Factory::get_instance($this->_paper_size, $this->_paper_orientation);

    $root->set_containing_block(0, 0, $this->_pdf->get_width(), $this->_pdf->get_height());
    $root->set_renderer(new Renderer($this));
    
    // This is where the magic happens:
    $root->reflow();
    
    // Clean up cached images
    Image_Cache::clear();
  }
    
  //........................................................................ 

  /**
   * Streams the PDF to the client
   *
   * The file will open a download dialog by default.  The options
   * parameter controls the output headers.  Accepted headers are:
   *
   * 'Accept-Ranges' => 1 or 0 - if this is not set to 1, then this
   *    header is not included, off by default this header seems to
   *    have caused some problems despite tha fact that it is supposed
   *    to solve them, so I am leaving it off by default.
   *
   * 'compress' = > 1 or 0 - apply content stream compression, this is
   *    on (1) by default
   *
   * 'Attachment' => 1 or 0 - if 1, force the browser to open a
   *    download dialog, on (1) by default
   *
   * @param string $filename the name of the streamed file
   * @param array  $options header options (see above)
   */
  function stream($filename, $options = null) {
    if (!is_null($this->_pdf))
      $this->_pdf->stream($filename, $options);
  }

  /**
   * Returns the PDF as a string
   *
   * @return string
   */
  function output() {
    global $_dompdf_debug;
    if ( is_null($this->_pdf) )
      return null;
    
    return $this->_pdf->output( $_dompdf_debug );
  }
  
  //........................................................................ 
  
}
?>