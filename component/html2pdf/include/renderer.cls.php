<?php
/**
 * DOMPDF - PHP5 HTML to PDF renderer
 *
 * File: $RCSfile: renderer.cls.php,v $
 * Created on: 2004-06-03
 *
 * Copyright (c) 2004 - Benj Carson <benjcarson@digitaljunkies.ca>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library in the file LICENSE.LGPL; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * Alternatively, you may distribute this software under the terms of the
 * PHP License, version 3.0 or later.  A copy of this license should have
 * been distributed with this file in the file LICENSE.PHP .  If this is not
 * the case, you can obtain a copy at http://www.php.net/license/3_0.txt.
 *
 * The latest version of DOMPDF might be available at:
 * http://www.digitaljunkies.ca/dompdf
 *
 * @link http://www.digitaljunkies.ca/dompdf
 * @copyright 2004 Benj Carson
 * @author Benj Carson <benjcarson@digitaljunkies.ca>
 * @package dompdf
 * @version 0.5.1
 */

/* $Id: renderer.cls.php,v 1.7 2006/07/07 21:31:04 benjcarson Exp $ */

/**
 * Concrete renderer
 *
 * Instantiates several specific renderers in order to render any given
 * frame.
 *
 * @access private
 * @package dompdf
 */
class Renderer extends Abstract_Renderer {

  /**
   * Array of renderers for specific frame types
   *
   * @var array
   */
  protected $_renderers;
    
  /**
   * Advance the canvas to the next page
   */  
  function new_page() {
    $this->_canvas->new_page();
  }

  /**
   * Render frames recursively
   *
   * @param Frame $frame the frame to render
   */
  function render(Frame $frame) {    
    global $_dompdf_debug;

    if ( $_dompdf_debug ) {
      echo $frame;
      flush();
    }                      

/*    $node = $frame->get_node();
    if($node->nodeName == "page-break"){
      $a = $this->get_tcpdf() ;
      echo($a) ;
    }*/

    $display = $frame->get_style()->display;
    
    switch ($display) {
      
    case "block":
    case "inline-block":
    case "table":
    case "table-row-group":
    case "table-header-group":
    case "table-footer-group":
    case "inline-table":
      $this->_render_frame("block", $frame);
      break;

    case "inline":
      if ( $frame->get_node()->nodeName == "#text" )
        $this->_render_frame("text", $frame);
      else
        $this->_render_frame("inline", $frame);
      break;

    case "table-cell":
      $this->_render_frame("table-cell", $frame);
      break;

    case "-dompdf-list-bullet":
      $this->_render_frame("list-bullet", $frame);
      break;

    case "-dompdf-image":
      $this->_render_frame("image", $frame);
      break;
      
    case "none":
      $node = $frame->get_node();
          
      if ( $node->nodeName == "script" &&
           ( $node->getAttribute("type") == "text/php" ||
             $node->getAttribute("language") == "php" ) ) {
        // Evaluate embedded php scripts
        $this->_render_frame("php", $frame);
      }

      // Don't render children, so skip to next iter
      return;
      
    default:
      break;

    }

    foreach ($frame->get_children() as $child)
      $this->render($child);

  }

  /**
   * Render a single frame
   *
   * Creates Renderer objects on demand
   *
   * @param string $type type of renderer to use
   * @param Frame $frame the frame to render
   */
  protected function _render_frame($type, $frame) {

    if ( !isset($this->_renderers[$type]) ) {
      
      switch ($type) {
      case "block":
        $this->_renderers["block"] = new Block_Renderer($this->_dompdf);
        break;

      case "inline":
        $this->_renderers["inline"] = new Inline_Renderer($this->_dompdf);
        break;

      case "text":
        $this->_renderers["text"] = new Text_Renderer($this->_dompdf);
        break;

      case "image":
        $this->_renderers["image"] = new Image_Renderer($this->_dompdf);
        break;
      
      case "table-cell":
        $this->_renderers["table-cell"] = new Table_Cell_Renderer($this->_dompdf);
        break;

      case "list-bullet":
        $this->_renderers["list-bullet"] = new List_Bullet_Renderer($this->_dompdf);
        break;

      case "php":
        $this->_renderers["php"] = new PHP_Evaluator($this->_canvas);
        break;

      }
    }
    
    $this->_renderers[$type]->render($frame);

  }
}

?>