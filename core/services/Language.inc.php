<?php
namespace core\services;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Language-handler for making your website language-independand
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
class Language extends Service
{
    private $s_language = null;

    /**
     * @var \core\services\File
     */
    private $service_File;

    /**
     * @var \core\services\Settings
     */
    private $service_Settings;

    private $a_documents = array();

    private $bo_fallback = false;

    private $obj_parser;
    
    private $obj_parserFallback = null;

    private static $_instance;

    /**
     * PHP 5 constructor
     *
     * @param core\models\Config $model_Config
     *            The configuration
     * @param core\services\Cookie $service_Cookie
     *            The cookie handler
     * @param core\services\File $service_File
     *            The file service
     */
    public function __construct(\core\models\Config $model_Config, \core\services\Cookie $service_Cookie, \core\services\File $service_File)
    {        
        /* Check language */
        $this->service_File = $service_File;
        $this->service_Settings = $model_Config->getSettings();
        $this->s_language = $model_Config->getLanguage();
        $s_languageFallback = $this->service_Settings->get('fallbackLanguage');
        
        $this->obj_parser = $this->loadLanguageParser($this->s_language);
        if( $s_languageFallback != $this->s_language ){
            $this->obj_parserFallback = $this->loadLanguageParser($s_languageFallback);
        }
    }

    /**
     * Returns if the object schould be treated as singleton
     *
     * @return boolean True if the object is a singleton
     */
    public static function isSingleton()
    {
        return true;
    }

    /**
     * Loads the language parser
     * @param string $s_language    The language code
     * @return Object   The language parser
     */
    private function loadLanguageParser($s_language)
    {
        if ($this->service_Settings->exists('language/type') && $this->service_Settings->get('language/type') == 'mo') {
            $obj_parser = \Loader::Inject('\core\services\data\LanguageMO', array(
                $s_language
            ));
        } else {
            $obj_parser = \Loader::Inject('\core\services\data\LanguageXML', array(
                $s_language,
                $this->bo_fallback
            ));
        }
        
        if (! function_exists('t')) {
            require (NIV . 'core/services/data/languageShortcut.inc.php');
        }
        
        return $obj_parser;
    }

    /**
     * Gets the name belonging to the language code
     * 
     * @param string $s_code    The language code
     * @return string   The language name
     */
    public function getLanguageText($s_code)
    {
        $a_codes = $this->getLanguageCodes();
        if (array_key_exists($s_code, $a_codes)) {
            return $a_codes[$s_code];
        }
        
        return $s_code;
    }

    /**
     * Returns the language codes
     * 
     * @return array    The codes
     */
    public function getLanguageCodes()
    {
        return array(
            'nl-BE' => 'Vlaams',
            'nl-NL' => 'Nederlands',
            'en-AU' => 'English Australia',
            'en-BW' => 'English (Botswana)',
            'en-CA' => 'English (Canada)',
            'en-DK' => 'English (Denmark)',
            'en-GB' => 'English (Great Brittan)',
            'en-UK' => 'English (Great Brittan)',
            'en-HK' => 'English (Hong Kong)',
            'en-IE' => 'English (Ireland)',
            'en-IN' => 'English (India)',
            'en-NZ' => 'English (New Zealand)',
            'en-PH' => 'English (Philippines)',
            'en-SG' => 'English (Singapore)',
            'en-US' => 'English (United States)',
            'en-ZA' => 'English (South Africa)',
            'en-ZW' => 'English (Zimbabwe)'
        );
    }

    /**
     * Sets the language
     *
     * @param string $s_language
     *            code
     * @throws IOException when the language code does not exist
     */
    public function setLanguage($s_language)
    {
        $this->s_language = $s_language;
        
        $this->obj_parser = $this->loadLanguageParser($this->s_language);
    }

    /**
     * Returns the set language
     *
     * @return string The set language
     */
    public function getLanguage()
    {
        return $this->s_language;
    }

    /**
     * Returns the set encoding
     *
     * @return string The set encoding
     */
    public function getEncoding()
    {
        return $this->get('encoding');
    }

    /**
     * Gives the asked part of the loaded file
     *
     * @param string $s_path
     *            The path to the language-part
     * @return string The content of the requested part
     * @throws XMLException when the path does not exist
     */
    public function get($s_path)
    {
        try {
            return $this->obj_parser->get($s_path);
        }
        catch(\XMLException $e){
            if( !is_null($this->obj_parserFallback) ){
                return $this->obj_parserFallback->get($s_path);
            }
            
            throw $e;
        }
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
    public function insertPath($s_path, $a_fields, $a_values)
    {
        try {
            return $this->obj_parser->insertPath($s_path, $a_fields, $a_values);
        }
        catch(\XMLException $e ){
            if( !is_null($this->obj_parserFallback) ){
                return $this->obj_parserFallback->insertPath($s_path, $a_fields, $a_values);
            }
            
            throw $e;
        }
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
    public function insert($s_text, $a_fields, $a_values)
    {
        return $this->obj_parser->insert($s_text, $a_fields, $a_values);
    }

    /**
     * Checks of the given part of the loaded file exists
     *
     * @param string $s_path
     *            The path to the language-part
     * @return boolean, true if the part exists otherwise false
     */
    public function exists($s_path)
    {
        return $this->obj_parser->exists($s_path);
    }

    /**
     * Returns the text
     * Alias of get()
     * 
     * @param string $s_path
     *            The path to the language-part
     * @return string The content of the requested part
     * @throws XMLException when the path does not exist
     */
    public static function text($s_key)
    {
        if (is_null(Language::$_instance)) {
            Language::$_instance = \Loader::inject('\core\services\Language');
        }
        
        return Language::$_instance->get($s_key);
    }
}