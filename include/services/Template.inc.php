<?php
/**
 * Template parser for joining the templates and the PHP code
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			20/03/2011
 * @changed   		17/07/2012
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
class Service_Template extends Service {
	private $service_Settings;
	private $service_File;
	private $s_layout;
	private $s_view;
	private $s_template;
	private $s_templateDir;
	private $a_blocks;
	private $a_blockData;
	private $a_parser;
	private $a_headerParser;
	private $a_parts;
	private $bo_compression = false;
	private $a_partsUsed = array();
	
	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		$this->init();
	}
	
	/**
	 * Destructor
	 */
	public function __destruct(){
		$this->service_Settings = null;
		$this->service_File = null;
		$this->s_layout = null;
		$this->s_template = null;
		$this->s_view = null;
		$this->s_templateDir = null;
		$this->a_blocks = null;
		$this->a_parser = null;
		$this->a_headerParser = null;
		$this->a_parts = null;
	}
	
	/**
	 * Inits the template parser
	 *
	 * @throws TemplateException if the layout does not exist
	 * @throws IOException if the layout is not readable
	 */
	private function init(){
		$this->service_File = Memory::services('File');
		$this->service_Settings = Memory::services('Settings');
		
		/* Load template-dir */
		$s_templateDir = $this->service_Settings->get('settings/templates/dir');
		$service_Cookie = Memory::services('Cookie');
		if( isset($_GET['private_style_dir']) ){
			$s_styleDir = $this->clearLocation($_GET['private_style_dir']);
			if( $this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts') ){
				$s_templateDir = $s_styleDir;
				$service_Cookie->set('private_style_dir', $s_templateDir, '/');
			}
			$service_Cookie->delete('private_style_dir', '/');
		}
		else if( $service_Cookie->exists('private_style_dir') ){
			$s_styleDir = $this->clearLocation($service_Cookie->get('private_style_dir'));
			if( $this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts') ){
				$s_templateDir = $s_styleDir;
				$service_Cookie->set('private_style_dir', $s_templateDir, '/');
			}
			else{
				$service_Cookie->delete('private_style_dir', '/');
			}
		}
		$this->s_templateDir = $s_templateDir;
		
		$this->a_blocks = array();
		$this->a_parser = array();
		$this->a_parts = array();
		
		$this->a_headerParser = array(
				'css_link' => array(),
				'js_link' => array(),
				'meta' => array(),
				'css' => array(),
				'js' => ''
		);
		
		if( defined('PROCES') || Memory::isTesting() ) return;
		
		/* Load layout */
		if( ! Memory::isAjax() ){
			if( defined('LAYOUT') ){
				$s_url = 'styles/' . $this->s_templateDir . '/templates/layouts/' . LAYOUT . '.tpl';
			}
			else{
				$s_url = 'styles/' . $this->s_templateDir . '/templates/layouts/default.tpl';
			}
			
			if( $this->service_File->exists(NIV . $s_url) ){
				$this->s_layout = $this->service_File->readFile(NIV . $s_url);
			}
			else{
				throw new TemplateException('Can not load layout ' . $s_url);
			}
		}
		
		$this->loadView();
		
		$this->compression();
		Memory::helpers('HTML');
	}
	
	/**
	 * Clears the location path from evil input 
	 *
	 * @param	string	$s_location	The path
	 * @return	string	The path
	 */
	private function clearLocation($s_location){
		while( (strpos($s_location, './') !== false) || (strpos($s_location, '../') !== false) ){
			$s_location	= str_replace(array('./','../'),array('',''),$s_location);
		}
	}
	
	/**
	 * Checks if gzip compression is available and enables it
	 */
	private function compression(){
		/* Check encoding */
		if( empty($_SERVER['HTTP_ACCEPT_ENCODING']) ) return;
		
		/* Check server wide compression */
		if( (ini_get('zlib.output_compression') == 'On' || ini_get('zlib.output_compression_level') > 0) || ini_get('output_handler') == 'ob_gzhandler' ) return;
		
		if( extension_loaded('zlib') && (stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) && ! defined('DEBUG') ){
			ob_start('ob_gzhandler');
			$this->bo_compression = true;
		}
	}
	
	public function getLocationDir(){
		return $this->s_templateDir;
	}
	
	/**
	 * Returns the loaded template directory
	 *
	 * @return string template directory
	 */
	public function getStylesDir(){
		return LEVEL . 'styles/' . $this->s_templateDir . '/';
	}
	
	/**
	 * Loads the given view into the parser
	 *
	 * @param string $s_view
	 *        	The view relative to the template-directory
	 * @throws TemplateException if the view does not exist
	 * @throws IOException if the view is not readable
	 */
	public function loadView($s_view = ''){
		/* Check view */
		if( $s_view == '' ){
			if( isset($_GET['command']) ){
				$s_view = $_GET['command'];
			}
			else if( isset($_POST['command']) ){
				$s_view = $_POST['command'];
			}
			else{
				$s_view = 'index';
			}
		}
		
		if( substr($s_view, - 4) != '.tpl' ) $s_view .= '.tpl';
		
		$this->s_viewName = preg_replace("#/[a-z0-0_]+\.tpl#si", '', $s_view);
		$s_view = 'styles/' . $this->s_templateDir . '/templates/' . str_replace('.php', '', Memory::getPage()) . '/' . $s_view;
		
		if( ! $this->service_File->exists(NIV . $s_view) ){
			/* View not found */
			throw new TemplateException('Can not load view ' . $s_view . '.');
		}
		
		$s_view = $this->service_File->readFile(NIV . $s_view);
		
		if( ! Memory::isAjax() ){
			$this->s_template = str_replace("{body_content}", $s_view, $this->s_layout);
		}
		else{
			$this->s_template = $s_view;
		}
	}
	
	/**
	 * Sets the link to the page-header
	 *
	 * @param string/CoreHtmlItem $s_link
	 *        	The link
	 * @throws Exception if $s_link is not a string and not a subclass of CoreHtmlItem
	 */
	public function headerLink($s_link){
		if( is_object($s_link) && is_subclass_of($s_link, 'CoreHtmlItem') ){
			$s_link = $s_link->generateItem();
		}
		else if( is_object($s_link) ){
			throw new Exception("Only types of CoreHTMLItem or strings can be added.");
		}
		
		if( strpos($s_link, '<link rel') !== false ){
			/* CSS -link */
			$this->a_headerParser['css_link'][] = $s_link;
		}
		else if( stripos($s_link, '<script') !== false ){
			if( stripos($s_link, 'src=') !== false ){
				$this->a_headerParser['js_link'][] = $s_link;
			}
			else{
				if( strpos($s_link, '<!--') !== false ){
					$a_link = explode('<!--', $s_link);
					$a_link = explode('//-->', $a_link[1]);
					$s_link = $a_link[0];
				}
				
				if( empty($this->a_headerParser['js']) ) $this->a_headerParser['js'] = trim($s_link);
				else{
					$this->a_headerParser['js'] .= '
                    
' . trim($s_link);
				}
			}
		}
		else if( strpos($s_link, '<meta') !== false ){
			$this->a_headerParser['meta'][] = $s_link;
		}
		else if( strpos($s_link, '<style') !== false ){
			$this->a_headerParser['css'][] = $s_link;
		}
	}
	
	/**
	 * Loads a subtemplate into the template
	 *
	 * @param string $s_key
	 *        	The key in the template
	 * @param string $s_url
	 *        	The URI of the subtemplate
	 * @throws TemplateException if the view does not exist
	 * @throws IOException if the view is not readable
	 */
	public function loadTemplate($s_key, $s_url){
		if( substr($s_url, - 4) != '.tpl' ) $s_url .= '.tpl';
		
		if( ! $this->service_File->exists(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_url) ){
			throw new TemplateException('Can not find template ' . $s_url);
		}
		
		$s_subTemplate = $this->service_File->readFile(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_url);
		
		$this->s_template = str_replace('{[' . $s_key . ']}', $s_subTemplate, $this->s_template);
	}
	
	/**
	 * Sets a subtemplate into the template
	 *
	 * @param string $s_key
	 *        	The key in the template
	 * @param string $s_template
	 *        	The template to add
	 */
	public function setTemplate($s_key, $s_template){
		$this->s_template = str_replace('{[' . $s_key . ']}', $s_template, $this->s_template);
	}
	
	/**
	 *
	 *
	 * Loads a template and returns it as a string
	 *
	 * @param string $s_url
	 *        	The URI of the template
	 * @param string $s_dir
	 *        	to search from, optional
	 * @return string template
	 * @throws TemplateException if the view does not exist
	 * @throws IOException if the view is not readable
	 */
	public function loadTemplateAsString($s_url, $s_dir = ''){
		if( substr($s_url, - 4) != '.tpl' ) $s_url .= '.tpl';
		
		if( empty($s_dir) ) $s_dir = str_replace('.php', '', Memory::getPage());
		
		if( ! $this->service_File->exists(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_dir . '/' . $s_url) ){
			throw new TemplateException('Can not load template templates/' . $this->s_templateDir . '/' . $s_dir . '/' . $s_url . '.');
		}
		
		$s_subTemplate = $this->service_File->readFile(NIV . 'styles/' . $this->s_templateDir . '/templates/' . $s_dir . '/' . $s_url);
		
		return $s_subTemplate;
	}
	
	/**
	 * Sets the given value in the template on the given key
	 *
	 * @param string $s_key
	 *        	The key in template
	 * @param string/CoreHtmlItem $s_value
	 *        	The value to write in the template
	 * @throws TemplateException if no template is loaded yet
	 * @throws Exception if $s_value is not a string and not a subclass of CoreHtmlItem
	 */
	public function set($s_key, $s_value){
		if( $this->s_template == null ){
			throw new Exception('No template is loaded for ' . $_SERVER['PHP_SELF'] . '.');
		}
		
		if( is_object($s_value) && is_subclass_of($s_value, 'CoreHtmlItem') ){
			$s_value = $s_value->generateItem();
		}
		else if( is_object($s_value) ){
			throw new Exception("Only types of CoreHTMLItem or strings can be added.");
		}
		
		$this->a_parser[$s_key] = $s_value;
	}
	
	/**
	 * Writes a repeating block to the template
	 *
	 * @param string $s_key
	 *        	The key in template
	 * @param array $a_data
	 *        	block data
	 */
	public function setBlock($s_key, $a_data){
		if( ! array_key_exists($s_key, $this->a_blocks) ) $this->a_blocks[$s_key] = array();
		
		$a_keys = array_keys($a_data);
		$i_num = count($a_keys);
		for( $i = 0; $i < $i_num; $i ++ ){
			$a_keys[$i] = '{' . $a_keys[$i] . '}';
		}
		$this->a_blocks[$s_key][] = array(
				'keys' => $a_keys,
				'data' => $a_data
		);
	}
	
	/**
	 * Displays the if part with the given key
	 *
	 * @param string $s_key
	 *        	The key in template
	 */
	public function displayPart($s_key){
		$this->a_parts[] = $s_key;
	}
	
	/**
	 * Writes the values to the given keys on the given template
	 *
	 * @param array $a_keys        	
	 * @param array $a_values        	
	 * @param string $s_template
	 *        	to parse
	 * @return string parsed template
	 */
	public function writeTemplate($a_keys, $a_values, $s_template){
		$i_number = count($a_keys);
		for( $i = 0; $i < $i_number; $i ++ ){
			if( substr($a_keys[$i], 0, 1) != '{' && substr($a_keys[$i], - 1) != '}' ) $a_keys[$i] = '{' . $a_keys[$i] . '}';
		}
		
		return str_replace($a_keys, $a_values, $s_template);
	}
	
	/**
	 * Prints the page to the screen and pushes it to the visitor
	 */
	public function printToScreen(){
		$this->set('style_dir', NIV . 'styles/' . $this->s_templateDir . '/');
		
		if( ! Memory::isAjax() ){
			$helper_HTML = Memory::helpers('HTML');
			
			/* Write header-blok to template */
			$s_headblock = '';
			$a_keys = array_keys($this->a_headerParser);
			foreach( $a_keys as $s_headerKey ){
				if( $s_headerKey == 'js' ){
					if( trim($this->a_headerParser[$s_headerKey]) == '' ) continue;
					
					$s_headblock .= $helper_HTML->javascript(trim($this->a_headerParser[$s_headerKey]))->generateItem();
					
					continue;
				}
				foreach( $this->a_headerParser[$s_headerKey] as $s_item ){
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
			$this->s_template = str_replace("{" . $s_key . "}", $this->a_parser[$s_key], $this->s_template);
		}
		
		/* Delete unused template-variables */
		$this->removeBlocks('block', 0);
		
		$this->s_template = preg_replace("#{+[a-zA-Z_0-9/]+}+#si", "", $this->s_template);
		$this->s_template = preg_replace("#{\[+[a-zA-Z_0-9/]+\]}+#si", "", $this->s_template);
		
		echo ($this->s_template);
	}
	private function writeBlocks(){
		$i_start = preg_match_all('#<block#', $this->s_template, $a_matches);
		$i_end = preg_match_all('#</block>#', $this->s_template, $a_matches);
		
		if( $i_start > $i_end ){
			throw new TemplateException("Template validation error : number of <block> is bigger than the number of </block>.");
		}
		else if( $i_end > $i_start ){
			throw new TemplateException("Template validation error : number of </block> is bigger than the number of <block>.");
		}
		else if( $i_start == 0 ) return;
	}
	
	/**
	 * Writes the block with the given key
	 *
	 * @param string $s_key
	 *        	block key
	 */
	private function writeBlock($s_key){
		/* Get block */
		$s_search = '<block {' . $s_key . '}>';
		
		$i_pos = stripos($this->s_template, $s_search);
		if( $i_pos === false ){
			Memory::services('ErrorHandler')->error(new TemplateException('Notice : Call to undefined template block ' . $s_key . '.'));
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
		foreach( $this->a_blocks[$s_key] as $a_item ){
			$s_blockData .= str_replace($a_item['keys'], $a_item['data'], $s_block) . '
';
		}
		
		$this->s_template = str_replace($s_blockPre, $s_blockData, $this->s_template);
	}
	
	/**
	 * Removes the unused blocks
	 *
	 * @param int $i_start
	 *        	start position
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
	private function writeIFS(){
		$i_start = preg_match_all('#<if#', $this->s_template, $a_matches);
		$i_end = preg_match_all('#</if>#', $this->s_template, $a_matches);
		
		if( $i_start > $i_end ){
			throw new TemplateException("Template validation error : number of &lt;if&gt; is bigger than the number of &lt;/if>.");
		}
		else if( $i_end > $i_start ){
			throw new TemplateException("Template validation error : number of &lt;/if&gt; is bigger than the number of &lt;if&gt;.");
		}
		else if( $i_start == 0 ) return;
		
		/* Write ifs to the template */
		$i_start = strrpos($this->s_template, '<if');
		while( $i_start !== false ){
			$i_end = strpos($this->s_template, '</if>', $i_start);
			$s_key = substr($this->s_template, $i_start + 5, (strpos($this->s_template, "}", $i_start) - $i_start - 5));
			
			if( $i_end === false ) throw new TemplateException("Template validation error : found <if {" . $s_key . "}> without matching </if>.");
			
			if( in_array($s_key, $this->a_parts) ){
				$this->s_template = substr_replace($this->s_template, '', ($i_end), 5);
				$this->s_template = substr_replace($this->s_template, '', $i_start, strlen('<if {' . $s_key . '}>'));
				
				$this->a_partsUsed[] = $s_key;
			}
			else{
				$this->s_template = substr_replace($this->s_template, '', $i_start, ($i_end - $i_start + 5));
			}
			
			$i_start = strrpos($this->s_template, '<if');
		}
		
		foreach( $this->a_parts as $s_key ){
			if( ! in_array($s_key, $this->a_partsUsed) ) Memory::services('ErrorHandler')->error(new TemplateException('Notice : Call to undefined template if ' . $s_key . '.'));
		}
	}
}
?>