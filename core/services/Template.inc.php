<?php

namespace core\services;

/**
 * Template parser for joining the templates and the PHP code
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2014,2015,2016  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version       1.0
 * @since         1.0
 * @date          12/01/2006
 * @changed   		04/07/2014
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
class Template extends Service{

  private $model_Config;
  private $service_File;
  private $s_layout;
  private $s_template;
  private $s_templateDir;
  private $a_blocks;
  private $a_parser;
  private $a_headerParser;
  private $a_parts;
  private $bo_compression = false;
  private $a_partsUsed = array();

  /**
   * PHP 5 constructor
   * 
   * @param core\services\File  $service_File   The File service
   * @param core\models\Config  $model_Config The configuration model
   * @throws TemplateException if the layout does not exist
   * @throws IOException if the layout is not readable
   */
  public function __construct(\core\services\File $service_File, \core\models\Config $model_Config){
    $this->service_File = $service_File;
    $this->model_Config = $model_Config;
    $this->s_templateDir = $model_Config->getTemplateDir();

    $this->a_blocks = array();
    $this->a_parser = array();
    $this->a_parts = array();

    $this->a_headerParser = array(
        'css_link' => array(),
        'js_link' => array(),
        'meta' => array(),
        'css' => array(),
        'js' => array()
    );

    if( defined('PROCES') || \core\Memory::isTesting() ){ return; }

    /* Load layout */
    if( !$this->model_Config->isAjax() ){
      if( defined('LAYOUT') ){
        $s_url = 'styles/' . $this->s_templateDir . '/templates/layouts/' . LAYOUT . '.tpl';
      }
      else {
        $s_url = 'styles/' . $this->s_templateDir . '/templates/layouts/default.tpl';
      }

      if( $this->service_File->exists(NIV . $s_url) ){
        $this->s_layout = $this->service_File->readFile(NIV . $s_url);
      }
      else {
        throw new \TemplateException('Can not load layout ' . $s_url);
      }
    }

    $this->loadView();

    $this->compression();
  }

  /**
   * Returns the template directory
   * 
   * @deprecated    Replaced by include/models/Config:getTemplateDir
   * @return String The template directory
   */
  public function getTemplateDir(){
    return $this->model_Config->getTemplateDir();
  }

  /**
   * Returns the loaded template directory
   *
   * @deprecated    Replaced by include/models/Config:getStylesDir;
   * @return String template directory
   */
  public function getStylesDir(){
    return $this->model_Config->getStylesDir();
  }

  /**
   * Checks if gzip compression is available and enables it
   */
  private function compression(){
    /* Check encoding */
    if( empty($_SERVER[ 'HTTP_ACCEPT_ENCODING' ]) ){ return;  }

    /* Check server wide compression */
    if( (ini_get('zlib.output_compression') == 'On' || ini_get('zlib.output_compression_level') > 0) || ini_get('output_handler') == 'ob_gzhandler' ){ return;  }

    if( extension_loaded('zlib') && (stripos($_SERVER[ 'HTTP_ACCEPT_ENCODING' ], 'gzip') !== FALSE) && !defined('DEBUG') ){
      ob_start('ob_gzhandler');
      $this->bo_compression = true;
    }
  }

  /**
   * Loads the given view into the parser
   *
   * @param String $s_view  The view relative to the template-directory
   * @throws TemplateException if the view does not exist
   * @throws IOException if the view is not readable
   */
  public function loadView($s_view = ''){
    \core\Memory::type('string',$s_view);
    
    /* Check view */
    if( empty($s_view) ){
      $s_view = $this->model_Config->getCommand();
    }

    if( substr($s_view, - 4) != '.tpl' ){ $s_view .= '.tpl';  }

    $this->s_viewName = preg_replace("#/[a-z0-0_]+\.tpl#si", '', $s_view);
    $s_view = 'styles/' . $this->s_templateDir . '/templates/' . str_replace('.php', '', $this->model_Config->getPage()) . '/' . $s_view;

    if( !$this->service_File->exists(NIV . $s_view) ){
      /* View not found */
      throw new \TemplateException('Can not load view ' . $s_view . '.');
    }

    $s_view = $this->service_File->readFile(NIV . $s_view);

    if( !$this->model_Config->isAjax() ){
      $this->s_template = str_replace("{body_content}", $s_view, $this->s_layout);
    }
    else {
      $this->s_template = $s_view;
    }
  }

  /**
   * Writes a script link to the head
   * 
   * @param String $s_link    The link
   */
  public function setJavascriptLink($s_link){
    \core\Memory::type('string', $s_link);
    
    $this->a_headerParser[ 'js_link' ][] = $s_link;
  }

  /**
   * Writes javascript code to the head
   * 
   * @param String $s_javascript    The code
   */
  public function setJavascript($s_javascript){
    \core\Memory::type('string', $s_javascript);
    
    if( stripos($s_javascript,'<script') === false ){
      $s_javascript = '<script type="text/javascript">'
        . '<!--'
        . $s_javascript.''
        . '//-->'
        . '</script>';
    }

    $this->a_headerParser[ 'js' ][] = trim($s_javascript);
  }
  
  /**
   * Writes a stylesheet link to the head
   * 
   * @param String $s_link    The link
   */
  public function setCssLink($s_link){
    \core\Memory::type('string', $s_link);
    
    $this->a_headerParser[ 'css_link' ][] = $s_link;
  }
  
  /**
   * Writes CSS code to the head
   * 
   * @param String $s_css    The code
   */
  public function setCSS($s_css){
    \core\Memory::type('string', $s_css);
    
    if( stripos($s_css,'<style') === false ){
      $s_css = '<style type="text/css">'
        . '<!--'
        . $s_css.''
        . '//-->'
        . '</style>';
    }
    
    $this->a_headerParser[ 'css' ][] = $s_css;
  }
  
  /**
   * Writes a metatag to the head
   * 
   * @param String $s_meta    The metatag
   */
  public function setMetaLink($s_meta){
    \core\Memory::type('string', $s_meta);
    
    $this->a_headerParser['meta'][] = $s_meta;
  }

  /**
   * Sets the link to the page-header
   *
   * @param String/CoreHtmlItem $s_link The link
   * @throws Exception if $s_link is not a string and not a subclass of CoreHtmlItem
   * @deprecated    Use one of the next functions :
   *  - setJavascriptLink
   *  - setJavascript
   *  - setCssLink
   *  - setCSS
   *  - setMetaLink
   */
  public function headerLink($s_link){
    if( is_object($s_link) && is_subclass_of($s_link, 'CoreHtmlItem') ){
      $s_link = $s_link->generateItem();
    }
    else if( is_object($s_link) ){
      throw new \Exception("Only types of CoreHTMLItem or strings can be added.");
    }

    if( strpos($s_link, '<link rel') !== false ){
      $this->setCssLink($s_link);
    }
    else if( stripos($s_link, '<script') !== false ){
      if( stripos($s_link,'src=') !== false ){
        $this->setJavascript($s_link);
      }
      else {
        $this->setJavascript($s_link);
      }
    }
    else if( stripos($s_link, '<meta') !== false ){
      $this->setMetaLink($s_link);
    }
    else if( stripos($s_link, '<style') !== false ){
      $this->setCSS($s_link);
    }
  }

  /**
   * Loads a subtemplate into the template
   *
   * @param String $s_key   The key in the template
   * @param String $s_url   The URI of the subtemplate
   * @throws TemplateException if the view does not exist
   * @throws IOException if the view is not readable
   */
  public function loadTemplate($s_key, $s_url){
    \core\Memory::type('string',$s_key);
    \core\Memory::type('string',$s_url);
    
    if( substr($s_url, - 4) != '.tpl' ){ $s_url .= '.tpl';  }

    if( !$this->service_File->exists(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_url) ){
      throw new \TemplateException('Can not find template ' . $s_url);
    }

    $s_subTemplate = $this->service_File->readFile(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_url);

    $this->s_template = str_replace('{[' . $s_key . ']}', $s_subTemplate, $this->s_template);
  }

  /**
   * Sets a subtemplate into the template
   *
   * @param String $s_key       The key in the template
   * @param String $s_template  The template to add
   */
  public function setTemplate($s_key, $s_template){
    \core\Memory::type('string',$s_key);
    \core\Memory::type('string',$s_template);
    
    $this->s_template = str_replace('{[' . $s_key . ']}', $s_template, $this->s_template);
  }

  /**
   * Loads a template and returns it as a string
   *
   * @param String $s_url The URI of the template
   * @param String $s_dir to search from, optional
   * @return String template
   * @throws TemplateException if the view does not exist
   * @throws IOException if the view is not readable
   */
  public function loadTemplateAsString($s_url, $s_dir = ''){
    \core\Memory::type('string',$s_url);
    \core\Memory::type('string',$s_dir);
    
    if( substr($s_url, - 4) != '.tpl' ){ $s_url .= '.tpl';  }

    if( empty($s_dir) ){
      $s_dir = str_replace('.php', '', \core\Memory::getPage());
    }

    if( !$this->service_File->exists(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_dir . '/' . $s_url) ){
      throw new \TemplateException('Can not load template templates/' . $this->s_templateDir . '/' . $s_dir . '/' . $s_url . '.');
    }

    $s_subTemplate = $this->service_File->readFile(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_dir . '/' . $s_url);

    return $s_subTemplate;
  }

  /**
   * Sets the given value in the template on the given key
   *
   * @param String $s_key   The key in template
   * @param String/CoreHtmlItem $s_value    The value to write in the template
   * @throws TemplateException if no template is loaded yet
   * @throws Exception if $s_value is not a string and not a subclass of CoreHtmlItem
   */
  public function set($s_key, $s_value){
    \core\Memory::type('string',$s_key);
    
    if( $this->s_template == null ){
      throw new \TemplateException('No template is loaded for ' . $_SERVER[ 'PHP_SELF' ] . '.');
    }

    if( is_object($s_value) ){
    	if( ($s_value instanceOf \core\helpers\Display )){
    		$s_value = $s_value->generate();
    	}
    	else if( is_subclass_of($s_value, 'CoreHtmlItem') ){
    		$s_value = $s_value->generateItem();
    	}
    	else {
	      throw new \Exception("Only types of CoreHTMLItem or strings can be added.");
	    }
  	}

    $this->a_parser[ $s_key ] = $s_value;
  }

  /**
   * Writes a repeating block to the template
   *
   * @param String $s_key The key in template
   * @param array $a_data block data
   */
  public function setBlock($s_key, $a_data){
    \core\Memory::type('string',$s_key);
    \core\Memory::type('array',$a_data);
    
    if( !array_key_exists($s_key, $this->a_blocks) ){
      $this->a_blocks[ $s_key ] = array();
    }

    $a_keys = array_keys($a_data);
    $i_num = count($a_keys);
    for( $i = 0; $i < $i_num; $i ++ ){
      $a_keys[ $i ] = '{' . $a_keys[ $i ] . '}';
    }
    $this->a_blocks[ $s_key ][] = array(
        'keys' => $a_keys,
        'data' => $a_data
    );
  }

  /**
   * Displays the if part with the given key
   *
   * @param String $s_key The key in template
   */
  public function displayPart($s_key){
    \core\Memory::type('string',$s_key);
    
    $this->a_parts[] = $s_key;
  }

  /**
   * Writes the values to the given keys on the given template
   *
   * @param array $a_keys        	
   * @param array $a_values        	
   * @param String $s_template  The template to parse
   * @return String parsed template
   */
  public function writeTemplate($a_keys, $a_values, $s_template){
    \core\Memory::type('array',$a_keys);
    \core\Memory::type('array',$a_values);
    \core\Memory::type('string',$s_template);
    
    $i_number = count($a_keys);
    for( $i = 0; $i < $i_number; $i ++ ){
      if( substr($a_keys[ $i ], 0, 1) != '{' && substr($a_keys[ $i ], - 1) != '}' ){ $a_keys[ $i ] = '{' . $a_keys[ $i ] . '}'; }
    }

    return str_replace($a_keys, $a_values, $s_template);
  }

  /**
   * Prints the page to the screen and pushes it to the visitor
   */
  public function printToScreen(){
    $this->set('style_dir', NIV . 'styles/' . $this->s_templateDir . '/');

    if( !\core\Memory::isAjax() ){
      /* Write header-blok to template */
      $s_headblock = '';
      $a_keys = array_keys($this->a_headerParser);
      foreach( $a_keys as $s_headerKey ){
        foreach( $this->a_headerParser[ $s_headerKey ] as $s_item ){
          $s_headblock .= $s_item . '
	';
        }
      }

      $this->s_template = str_replace("{headblock}", $s_headblock, $this->s_template);
    }

    $this->writeIFS();

    $this->writeBlocks();

    /* Write blocks to the template */
    $a_keys = array_keys($this->a_blocks);
    foreach( $a_keys as $s_key ){
      $this->writeBlock($s_key);
    }

    /* Write data to template */
    $a_keys = array_keys($this->a_parser);
    foreach( $a_keys as $s_key ){
      $this->s_template = str_replace("{" . $s_key . "}", $this->a_parser[ $s_key ], $this->s_template);
    }
    
    
    $this->s_template = str_replace(array('{STYLE_DIR}','{LEVEL}'),array('{LEVEL}styles/' . $this->s_templateDir,$this->model_Config->getBase()),$this->s_template);

    /* Delete unused template-variables */
    $this->removeBlocks('block', 0);

    $this->s_template = preg_replace("#{+[a-zA-Z_0-9/]+}+#si", "", $this->s_template);
    $this->s_template = preg_replace("#{\[+[a-zA-Z_0-9/]+\]}+#si", "", $this->s_template);

    echo ($this->s_template);
  }

  /**
   * Writes the blocks
   * 
   * @throws \TemplateException   If the blocks are invalid
   */
  private function writeBlocks(){
    $i_start = preg_match_all('#<block#', $this->s_template, $a_matches);
    $i_end = preg_match_all('#</block>#', $this->s_template, $a_matches);

    if( $i_start > $i_end ){
      throw new \TemplateException("Template validation error : number of <block> is bigger than the number of </block>.");
    }
    else if( $i_end > $i_start ){
      throw new \TemplateException("Template validation error : number of </block> is bigger than the number of <block>.");
    }
    else if( $i_start == 0 ) return;
  }

  /**
   * Writes the block with the given key
   *
   * @param String $s_key The block key
   */
  private function writeBlock($s_key){
    /* Get block */
    $s_search = '<block {' . $s_key . '}>';

    $i_pos = stripos($this->s_template, $s_search);
    if( $i_pos === false ){
      \core\Memory::services('ErrorHandler')->error(new \TemplateException('Notice : Call to undefined template block ' . $s_key . '.'));
      return;
    }

    /* Find end */
    $i_end = stripos($this->s_template, '</block>', $i_pos);

    /* Check for between blocks */
    $i_pos2 = stripos($this->s_template, '<block', $i_pos + 1);
    $i_extra = 0;
    while( $i_pos2 !== false && ($i_pos2 < $i_end) ){
      $i_pos2 = stripos($this->s_template, '<block', $i_pos2 + 1);
      $i_extra ++;
    }

    for( $i = $i_extra; $i > 0; $i -- ){
      $i_end = stripos($this->s_template, '</block>', $i_end + 1);
    }

    $i_start = $i_pos + strlen($s_search) + 1;
    $s_blockPre = substr($this->s_template, $i_pos, ($i_end - $i_pos + 8));
    $i_search = strlen($s_search);
    $s_block = trim(substr($s_blockPre, $i_search, (strlen($s_blockPre) - 8 - $i_search)));

    /* Parse block */
    $s_blockData = '';
    foreach( $this->a_blocks[ $s_key ] as $a_item ){
      $s_blockData .= str_replace($a_item[ 'keys' ], $a_item[ 'data' ], $s_block) . '
';
    }

    $this->s_template = str_replace($s_blockPre, $s_blockData, $this->s_template);
  }

  /**
   * Removes the unused blocks
   *
   * @param int $i_start  The start position
   */
  private function removeBlocks($s_key, $i_start){
    $i_pos = strpos($this->s_template, '<' . $s_key . ' {', $i_start);
    while( $i_pos !== false ){
      /* Find end */
      $i_end = strpos($this->s_template, '</' . $s_key . '>', $i_pos);

      /* check for nested blocks */
      $i_pos2 = strpos($this->s_template, '<' . $s_key . ' {', $i_pos + 1);
      if( $i_pos2 !== false && ($i_pos2 < $i_end) ){
        $this->removeBlocks($s_key, $i_pos2 + 1);

        $i_end = strpos($this->s_template, '</' . $s_key . '>', $i_pos);
      }

      $this->s_template = substr_replace($this->s_template, '', $i_pos, ($i_end - $i_pos + strlen('</' . $s_key . '>')));
      $i_pos = strpos($this->s_template, '<' . $s_key . ' {', $i_start);
    }
  }

  /**
   * Writes the if blocks
   * 
   * @throws TemplateException
   */
  private function writeIFS(){
    $a_matches = array();
    $i_start = preg_match_all('#<if#', $this->s_template, $a_matches);
    $i_end = preg_match_all('#</if>#', $this->s_template, $a_matches);

    if( $i_start > $i_end ){
      throw new \TemplateException("Template validation error : number of &lt;if&gt; is bigger than the number of &lt;/if>.");
    }
    else if( $i_end > $i_start ){
      throw new \TemplateException("Template validation error : number of &lt;/if&gt; is bigger than the number of &lt;if&gt;.");
    }
    else if( $i_start == 0 ) return;

    /* Write ifs to the template */
    $i_start = strrpos($this->s_template, '<if');
    while( $i_start !== false ){
      $i_end = strpos($this->s_template, '</if>', $i_start);
      $s_key = substr($this->s_template, $i_start + 5, (strpos($this->s_template, "}", $i_start) - $i_start - 5));

      if( $i_end === false ){
        throw new \TemplateException("Template validation error : found <if {" . $s_key . "}> without matching </if>.");
      }

      if( in_array($s_key, $this->a_parts) ){
        $this->s_template = substr_replace($this->s_template, '', ($i_end), 5);
        $this->s_template = substr_replace($this->s_template, '', $i_start, strlen('<if {' . $s_key . '}>'));

        $this->a_partsUsed[] = $s_key;
      }
      else {
        $this->s_template = substr_replace($this->s_template, '', $i_start, ($i_end - $i_start + 5));
      }

      $i_start = strrpos($this->s_template, '<if');
    }

    foreach( $this->a_parts as $s_key ){
      if( !in_array($s_key, $this->a_partsUsed) ){
        \core\Memory::services('ErrorHandler')->error(new \TemplateException('Notice : Call to undefined template if ' . $s_key . '.'));
      }
    }
  }

}
?>