<?php
namespace tests\stubs\services;

class Language implements \Language
{

    /**
	 * Gets the name belonging to the language code
	 *
	 * @param string $s_code    The language code
	 * @return string   The language name
	 */
	public function getLanguageText($s_code){
	    return $s_code;
	}
	
	/**
	 * Returns the language codes
	 *
	 * @return array    The codes
	 */
	public function getLanguageCodes(){
	    return array();
	}
	
	/**
	 * Sets the language
	 *
	 * @param string $s_language
	 *            code
	 * @throws IOException when the language code does not exist
	 */
	public function setLanguage($s_language){}
	
	/**
	 * Returns the set language
	 *
	 * @return string The set language
	 */
	public function getLanguage(){
	    return 'en_UK';
	}
	
	/**
	 * Returns the set encoding
	 *
	 * @return string The set encoding
	 */
	public function getEncoding(){
	    return 'UTF-8';
	}
	
	/**
	 * Gives the asked part of the loaded file
	 *
	 * @param string $s_path
	 *            The path to the language-part
	 * @return string The content of the requested part
	 * @throws XMLException when the path does not exist
	 */
	public function get($s_path){
	    return '';
	}
	
	/**
	 * Changes the language-values with the given values
	 * Collects the text from the language file via the path
	 *
	 * @param string $s_path
	 *            The path to the language-part
	 * @param array $a_fields
	 *            accepts also a string
	 * @param array $a_values
	 *            accepts also a string
	 * @return string changed language-string
	 * @throws XMLException when the path does not exist
	 */
	public function insertPath($s_path, $a_fields, $a_values){
	    return '';
	}
	
	/**
	 * Changes the language-values with the given values
	 *
	 * @param string $s_text
	 * @param array $a_fields
	 *            accepts also a string
	 * @param array $a_values
	 *            accepts also a string
	 * @return string changed language-string
	 */
	public function insert($s_text, $a_fields, $a_values){
	    return $text;
	}
	
	/**
	 * Checks of the given part of the loaded file exists
	 *
	 * @param string $s_path
	 *            The path to the language-part
	 * @return boolean, true if the part exists otherwise false
	 */
	public function exists($s_path){
	    return true;
	}
}