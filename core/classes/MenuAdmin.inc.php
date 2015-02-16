<?php
namespace core\classes;

/**
 * Displays the admin menu
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 * @changed 06/02/2015
 *       
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class MenuAdmin {
 private $service_Language;
 private $service_File;
 private $service_XML;
 private $service_Template;
 
 /**
  * Starts the class menuAdmin
  */
 public function __construct( \core\services\Language $service_Language, \core\services\File $service_File, \core\services\Xml $service_XML, \core\services\Template $service_Template ){
  $this->service_Language = $service_Language;
  $this->service_File = $service_File;
  $this->service_XML = $service_XML;
  $this->service_Template = $service_Template;
  
  $this->text();
  
  $this->modules();
 }
 
 /**
  * Displays the text
  */
 private function text(){}
 
 /**
  * Displays the modules
  */
 private function modules(){
  $s_dir = NIV . 'admin/modules';
  $a_directory = $this->service_File->readDirectory($s_dir);
  
  $i = 1;
  $a_js = array();
  foreach( $a_directory as $s_module ){
   if( !$this->service_File->exists($s_dir . '/' . $s_module . '/settings.xml') ){
    continue;
   }
   
   $obj_settings = $this->service_XML->cloneService();
   $obj_settings->load($s_dir . '/' . $s_module . '/settings.xml');
   
   $s_title = $obj_settings->get('module/title');
   $s_jsLink = $obj_settings->get('module/js');
   $s_css = $obj_settings->get('module/css');
   
   $this->setJS($s_module, $s_jsLink);
   $this->setCSS($s_module, $s_css);
   
   ($i == 1) ? $s_class = 'tab_header_active' : $s_class = '';
   $this->service_Template->setBlock('menu_tab_header', array( 
     'class' => $s_class,
     'id' => $i,
     'title' => $this->service_Language->get($s_title) 
   ));
   
   $this->service_Template->setBlock('menu_tab_content', array( 
     'id' => $i 
   ));
   
   $a_items = $obj_settings->getBlock('module/block');
   foreach( $a_items as $block ){
    $a_data = array( 
      'id' => $i 
    );
    
    $a_links = array();
    
    foreach( $block->childNodes as $item ){
     if( $item->tagName == 'link' ){
      $a_links[] = $item;
      continue;
     }
     else if( $item->tagName == 'title' ){
      $a_data['title'] = $item->nodeValue;
      if( $this->service_Language->exists($item->nodeValue) ){
       $a_data['title'] = $this->service_Language->get($item->nodeValue);
      }
     }
     else{
      $a_data[$item->tagName] = $item->nodeValue;
     }
    }
    $a_data['item_id'] = $a_data['id'];
    
    if( count($a_links) > 0 ){
     $a_data['link_block'] = $a_data['id'];
    }
    $this->service_Template->setBlock('tab_' . $i, $a_data);
    
    $this->addLinks($a_data['id'], $a_links);
   }
   
   $i++;
  }
 }
 
 /**
  * Adds the links for the block
  * 
  * @param int $i_id       The block ID
  * @param array $a_links  The XML elements
  */
 private function addLinks( $i_id, $a_links ){
  foreach( $a_links AS $link ){
   $a_data = array();
   foreach( $link->childNodes AS $item ){
    if( $item->tagName == 'title' ){
     $a_data[ 'link_'.$item->tagName ] = $item->nodeValue;
     if( $this->service_Language->exists($item->nodeValue) ){
      $a_data[ 'link_'.$item->tagName ] = $this->service_Language->get($item->nodeValue);
     }
    }
    else {
     $a_data[ 'link_'.$item->tagName ] = $item->nodeValue;
    }
   }
   
   $this->service_Template->setBlock('links_'.$i_id,$a_data);
  }
 }
 
 /**
  * Sets the javascript links
  *
  * @param string $s_module The module name
  * @param string $s_jsLink The JS links, seperated with a comma
  */
 private function setJS( $s_module, $s_jsLink ){
  if( empty($s_jsLink) ){
   return;
  }
  
  $a_js = explode(',', $s_jsLink);
  foreach( $a_js as $s_jsLink ){
   $this->service_Template->setJavascriptLink('<script src="{NIV}admin/modules/' . $s_module . '/' . trim($s_jsLink) . '"></script>');
  }
 }
 
 /**
  * Sets the css links
  *
  * @param string $s_module The module name
  * @param string $s_css The CSS links, seperated with a comma
  */
 private function setCSS( $s_module, $s_css ){
  if( empty($s_css) ){
   return;
  }
  
  $a_css = explode(',', $s_css);
  foreach( $a_css as $s_css ){
   $this->service_Template->setCssLink('<link rel="stylesheet" href="{NIV}admin/modules/' . $s_module . '/' . $s_css . '">');
  }
 }
}
?>
