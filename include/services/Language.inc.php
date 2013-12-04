<?php
/**
 * Language-handler for making your website language-independand
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
class Service_Language extends Service_Xml {
	private $s_language;
	private $s_encoding;

	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		parent::__construct();

		$this->s_language	= null;
		$this->s_encoding       = null;

		$this->init();

		$this->readLanguage();
	}

	/**
	 * Destructor
	 */
	public function __destruct(){
		$this->s_language       = null;
		$this->s_encoding       = null;

		parent::__destruct();
	}

	/**
	 * Init the class Language
	 */
	private function init(){
		/*  Check language */
		$a_languages            = $this->getLanguages();

		$service_XmlSettings    = Memory::services('XmlSettings');
		$this->s_language       = $service_XmlSettings->get('defaultLanguage');

		$service_cookie         = Memory::services('Cookie');
		if( isset($_GET['lang']) ){
			if( in_array($_GET['lang'],$a_languages) ){
				$this->s_language   = $_GET['lang'];
				$service_cookie->set('language',$this->s_language,'/');
			}
			unset($_GET['lang']);
		}
		else if( $service_cookie->exists('language') ){
			if( in_array($service_cookie->get('language'),$a_languages) ){
				$this->s_language   = $service_cookie->get('language');
				/* Renew cookie */
				$service_cookie->set('language',$this->s_language,'/');
			}
			else {
				$service_cookie->delete('language');
			}
		}
		
		$this->s_startTag = 'language';
	}

	/**
	 * Collects the installed languages
	 *
	 * @return array    The installed languages
	 */
	public function getLanguages(){
		$a_languages		= array();
		$service_File		= Memory::services('File');
		$a_languageFiles	= $service_File->readDirectory(NIV.'include/language');

		foreach($a_languageFiles AS $s_languageFile){
			if( strpos($s_languageFile,'language_') === false )	continue;

			$s_languageFile	= str_replace(array('language_','.lang'),array('',''),$s_languageFile);

			$a_languages[]	= $s_languageFile;
		}

		return $a_languages;
	}

	/**
	 * Sets the language
	 * 
	 * @param	string	$s_language	The language code
	 * @throws IOException when the language code does not exist
	 */
	public function setLanguage($s_language){
		$this->s_language = $s_language;
		 
		$this->readLanguage();
	}

	/**
	 * Calls the set language-file and reads it
	 * 
	 * @throws	IOException when the file does not exist
	 */
	private function readLanguage(){
		$this->load(NIV.'include/language/language_'.$this->s_language.'.lang');

		/* Get encoding */
		$this->s_encoding   = $this->get('language/encoding');
	}

	/**
	 * Returns the set language
	 *
	 * @return  string  The set language
	 */
	public function getLanguage(){
		return $this->s_language;
	}

	/**
	 * Returns the set encoding
	 *
	 * @return	string  The set encoding
	 */
	public function getEncoding(){
		return $this->s_encoding;
	}
	
	/**
	 * Gives the asked part of the loaded file
	 *
	 * @param   string      $s_path      The path to the language-part
	 * @return  string       The content of the requested part
	 * @throws  XMLException when the path does not exist
	 */
	public function get($s_path) {
		$s_text	= parent::get($s_path);
    	
		return trim($s_text);
	}

	/**
	 * Changes the language-values with the given values
	 *
	 * @param	string	$s_text		The text
	 * @param	array	$a_fields	The fields, accepts also a string
	 * @param	array	$a_values	The values, accepts also a string
	 * @return	string	The changed language-string
	 */
	public function insert($s_text,$a_fields,$a_values){
		Memory::type('string',$s_text);

		if( !is_array($a_fields) ){
			$s_text     = str_replace('['.$a_fields.']', $a_values, $s_text);
		}
		else {
			for($i=0; $i<count($a_fields); $i++){
				$s_text     = str_replace('['.$a_fields[$i].']', $a_values[$i], $s_text);
			}
		}

		return $s_text;
	}
}
?>
