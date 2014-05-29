<?php

namespace core\services;

/**
 * Language-handler for making your website language-independand
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2014,2015,2016  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version       1.0
 * @since         1.0
 * @date          12/01/2006
 * @changed   		30/03/2014
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
class Language extends Xml{

  private $s_language = null;
  private $s_encoding = null;
  private $service_File;

  /**
   * PHP 5 constructor
   * 
   * @param core\services\Settings $service_Settings    The settings service
   * @param core\services\Cookie   $service_Cookie      The cookie handler
   * @param core\services\File     $service_File        The file service
   */
  public function __construct(\core\services\Settings $service_Settings, \core\services\Cookie $service_Cookie, \core\services\File $service_File){
    parent::__construct();

    /*  Check language */
    $this->service_File = $service_File;
    $a_languages = $this->getLanguages();
    $this->s_language = $service_Settings->get('defaultLanguage');

    if( isset($_GET[ 'lang' ]) ){
      if( in_array($_GET[ 'lang' ], $a_languages) ){
        $this->s_language = $_GET[ 'lang' ];
        $service_Cookie->set('language', $this->s_language, '/');
      }
      unset($_GET[ 'lang' ]);
    }
    else if( $service_Cookie->exists('language') ){
      if( in_array($service_Cookie->get('language'), $a_languages) ){
        $this->s_language = $service_Cookie->get('language');
        /* Renew cookie */
        $service_Cookie->set('language', $this->s_language, '/');
      }
      else {
        $service_Cookie->delete('language');
      }
    }

    $this->s_startTag = 'language';

    $this->readLanguage();
  }

  /**
   * Collects the installed languages
   *
   * @return array    The installed languages
   */
  public function getLanguages(){
    $a_languages = array();
    $a_languageFiles = $this->service_File->readDirectory(NIV . 'include/language');

    foreach( $a_languageFiles AS $s_languageFile ){
      if( strpos($s_languageFile, 'language_') === false ) continue;

      $s_languageFile = str_replace(array( 'language_', '.lang' ), array( '', '' ), $s_languageFile);

      $a_languages[] = $s_languageFile;
    }

    return $a_languages;
  }
  
  public function getLanguageCodes(){
    return array(
        'nl_BE' => 'Vlaams','nl_NL'=>'Nederlands',
        'en_AU' => 'English Australia','en_BW'=>'English (Botswana)','en_CA'=> 'English (Canada)',
        'en_DK' => 'English (Denmark)', 'en_GB'=>'English (Great Brittan)','en_UK'=>'English (Great Brittan)',
        'en_HK' => 'English (Hong Kong)', 'en_IE' => 'English (Ireland)','en_IN'=> 'English (India)',
        'en_NZ' => 'English (New Zealand)', 'en_PH' => 'English (Philippines)','en_SG' => 'English (Singapore)',
        'en_US' => 'English (United States)', 'en_ZA' => 'English (South Africa)', 'en_ZW' => 'English (Zimbabwe)'

    );
  }

  /**
   * Sets the language
   * 
   * @param String	$s_language	The language code
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
    $this->load(NIV . 'include/language/language_' . $this->s_language . '.lang');

    /* Get encoding */
    $this->s_encoding = $this->get('language/encoding');
  }

  /**
   * Returns the set language
   *
   * @return  String  The set language
   */
  public function getLanguage(){
    return $this->s_language;
  }

  /**
   * Returns the set encoding
   *
   * @return	String  The set encoding
   */
  public function getEncoding(){
    return $this->s_encoding;
  }

  /**
   * Gives the asked part of the loaded file
   *
   * @param   String      $s_path      The path to the language-part
   * @return  String       The content of the requested part
   * @throws  XMLException when the path does not exist
   */
  public function get($s_path){
    $s_text = parent::get($s_path);

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
  public function insert($s_text, $a_fields, $a_values){
    \core\Memory::type('string', $s_text);

    if( !is_array($a_fields) ){
      $s_text = str_replace('[' . $a_fields . ']', $a_values, $s_text);
    }
    else {
      for( $i = 0; $i < count($a_fields); $i++ ){
        $s_text = str_replace('[' . $a_fields[ $i ] . ']', $a_values[ $i ], $s_text);
      }
    }

    return $s_text;
  }

}
?>
