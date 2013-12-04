<?php
/**
 * Xml-handler for parsing XML-files
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2006
 * @changed   		03/03/2010
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
class Service_Xml extends Service {
    protected $dom_document;
    protected $obj_document;
    protected $s_startTag = '';

    /**
     * PHP 5 constructor
     */
    public function __construct(){
        $this->dom_document     = null;
        $this->obj_document     = null;
    }

    /**
     * Destructor
     */
    public function __destruct(){
        $this->dom_document     = null;
        $this->obj_document	= null;
    }

    /**
     * Loads the requested XML-file
     *
     * @param   string $s_file  The path to the XML-file
     * @throws  IOException when the file does not exist
     */
    public function load($s_file){
        $this->dom_document     = new DOMDocument('1.0', 'iso-8859-1');

	// We don't want to bother with white spaces
	$this->dom_document->preserveWhiteSpace = false;
	
	$this->dom_document->resolveExternals = true; // for character entities

        $this->dom_document->formatOutput = true;   // keep output alignment

	if( !$this->dom_document->Load($s_file) ){
		throw new IOException("Can not load XML-file ".$s_file.". Check the address");
	}

        $this->obj_document = new DOMXPath($this->dom_document);
    }

    /**
     * Gives the asked part of the loaded file
     *
     * @param   string      $s_path      The path to the language-part
     * @return  string       The content of the requested part
     * @throws  XMLException when the path does not exist
     */
    public function get($s_path) {
        $s_path = $this->getRealPath($s_path);

        $a_return = $this->obj_document->query("//".$s_path);

        if( $a_return->length < 1 ){
            /* Part not found */
            throw new XMLException("Can not find ".$s_path);
        }

        foreach ($a_return as $entry) {
            $s_return = $entry->textContent;
        }

        return $s_return;
    }

    /**
     * Saves the value at the given place
     *
     * @param   string      $s_path      The path to the language-part
     * @param   string      $s_content   The content to save
     * @throws  XMLException when the path does not exist
     */
    public function set($s_path,$s_content) {
        Memory::type('string',$s_content);

        $s_path = $this->getRealPath($s_path);

        $a_data	  = explode('/',$s_path);
        $s_name   = end($a_data);
        $a_return = $this->obj_document->query("//".$s_path);

        if( $a_return->length < 1 ){
            /* Part not found */
            throw new XMLException("Can not find ".$s_path);
        }
        $oldnode   = $a_return->item(0);

        $newNode    = $this->dom_document->createElement($s_name);
        $newNode->appendChild($this->dom_document->createCDataSection($s_content));
        $oldnode->parentNode->replaceChild($newNode, $oldnode);
    }

    /**
     * Saves the XML file loaded to the given file
     *
     * @param   string $s_file  The filename
     * @throws	Exception	If the directory is not writable
     */
    public function save($s_file){
        Memory::type('string',$s_file);

	$s_dir	=  dirname($s_file);

	if( !is_writable($s_dir) ){
		throw new Exception("Can not write directory ".$s_dir.'.');
	}

	$this->dom_document->save($s_file);
    }
    
    /**
     * Checks of the given part of the loaded file exists
     *
     * @param  	string   $s_path   The path to the language-part
     * @return boolean, true if the part exists otherwise false
     */
    public function exists($s_path){
        $s_path = $this->getRealPath($s_path);

        $a_return = $this->obj_document->query("//".$s_path);

        if( $a_return->length < 1 ){
            /* Part not found */
            return false;
        }

        return true;
    }

    /**
     * Replaces the geven keys in the given text with the given values
     *
     * @param	string  $s_path     The path to the text that need to be changed
     * @param  array   $a_keys     The keys for the change. Also accepts a string
     * @param  array   $a_values   The values for the change. Also accepts a string
     * @return string  The changed text
     * @throws Exception when the path does not exist
     */
    public function insert($s_path,$a_keys,$a_values){
        $s_text     = $this->get($s_path);

        if( is_array($a_keys) ){
            for($i=0; $i<count($a_keys); $i++){
                $s_text = str_replace('['.$a_keys[$i].']',$a_values[$i],$s_text);
            }
        }
        else {
            $s_text = str_replace('['.$a_key.']',$a_values,$s_text);
        }

        return $s_text;
    }
    
    /**
     * Checks the path and adds the default start tag
     *
     * @param	String	$s_path	The defined path
     * @return String	The real path
     * @throws XMLException If the path is invalid
     */
    protected function getRealPath($s_path){
	Memory::type('string',$s_path);
    
        if( substr($s_path,-1) == '/' ){
            throw new XMLException('Invalid XML query : '.$s_path);
        }
        
        if( empty($this->s_startTag) ){
	  return $s_path;
        }
        
        $i_length = strlen($this->s_startTag);
        if( substr($s_path,0,$i_length) != $this->s_startTag ){
	  $s_path = $this->s_startTag.'/'.$s_path;
        }
        
        return $s_path;
    }
}
?>
