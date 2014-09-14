<?php

namespace core\services\data;

/**
 * Language-handler for making your website language-independand
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2014,2015,2016 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 2.0
 * @since 2.0
 * @date 14/09/2014
 * @changed 14/09/2014
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class LanguageMO extends \core\services\service {
	private $service_File;
	private $s_language = null;
	private $a_documents = array();
	
	/**
	 * PHP 5 constructor
	 *
	 * @param \core\services\File	$service_File	The file parser
	 * @param string $s_language code
	 */
	public function __construct( \core\services\File $service_File, $s_language ){
		$this->service_File = $service_File;
		$this->s_language = $s_language;
		
		putenv('LC_ALL=' . $s_language);
		setlocale(LC_ALL, $s_language);
		
		$this->readLanguages();
	}
	
	/**
	 * Loads the language files
	 */
	private function readLanguages(){
		$a_files = $this->service_File->readDirectory(NIV . 'include/language/' . $this->s_language . '/LC_MESSAGES');
		foreach( $a_files as $s_file ){
			if( $s_file == '.' || $s_file == '..' || substr($s_file, -3) != '.mo' ){
				continue;
			}
			
			$s_name = substr($s_file, 0, -3);
			$this->a_documents[] = $s_name;
			
			bindtextdomain($s_name, NIV . 'include/language');
		}
		
		if( !in_array('system', $this->a_documents) ){
			throw new \IOException('Missing system language file for language ' . $this->s_language . '.');
		}
		if( !in_array('site', $this->a_documents) ){
			throw new \IOException('Missing site language file for language ' . $this->s_language . '.');
		}
		
		/* Get encoding */
		$this->s_encoding = $this->get('language/encoding');
	}
	
	/**
	 * Gives the asked part of the loaded file
	 *
	 * @param String $s_path The path to the language-part
	 * @return String The content of the requested part
	 * @throws XMLException when the path does not exist
	 */
	public function get( $s_path ){
		$a_path = explode('/', $s_path);
		if( !array_key_exists($a_path[0], $this->a_documents) ){
			textdomain('site');
		}
		else{
			textdomain($a_path[0]);
		}
		$s_path = str_replace('/', '_', $s_path);
		
		$s_text = gettext($s_path);
		
		if( $s_text == $s_path ){
			/* Part not found */
			throw new \XMLException("Can not find " . $s_path);
		}
		
		return trim($s_text);
	}
	
	/**
	 * Changes the language-values with the given values
	 * Collects the text from the language file via the path
	 *
	 * @param String $s_path The path to the language-part
	 * @param array $a_fields accepts also a string
	 * @param array $a_values accepts also a string
	 * @return string changed language-string
	 * @throws XMLException when the path does not exist
	 */
	public function insertPath( $s_path, $a_fields, $a_values ){
		$s_text = $this->get($s_path);
		return $this->insert($s_text, $a_fields, $a_values);
	}
	
	/**
	 * Changes the language-values with the given values
	 *
	 * @param string $s_text
	 * @param array $a_fields accepts also a string
	 * @param array $a_values accepts also a string
	 * @return string changed language-string
	 */
	public function insert( $s_text, $a_fields, $a_values ){
		\core\Memory::type('string', $s_text);
		
		if( !is_array($a_fields) ){
			$s_text = str_replace('[' . $a_fields . ']', $a_values, $s_text);
		}
		else{
			for( $i = 0; $i < count($a_fields); $i++ ){
				$s_text = str_replace('[' . $a_fields[$i] . ']', $a_values[$i], $s_text);
			}
		}
		
		return $s_text;
	}
	
	/**
	 * Checks of the given part of the loaded file exists
	 *
	 * @param String $s_path The path to the language-part
	 * @return boolean, true if the part exists otherwise false
	 */
	public function exists( $s_path ){
		$a_path = explode('/', $s_path);
		if( !array_key_exists($a_path[0], $this->a_documents) ){
			textdomain('site');
		}
		else{
			textdomain($a_path[0]);
		}
		$s_path = str_replace('/', '_', $s_path);
		
		$s_text = gettext($s_path);
		
		if( $s_text == $s_path ){
			/* Part not found */
			return false;
		}
		
		return true;
	}
}
?>
