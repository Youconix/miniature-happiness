<?php
namespace core\services;

/**
 * Language-handler for making your website language-independand
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2014,2015,2016 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 *        @date 12/01/2006
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
class Language extends Xml
{

    private $s_language = null;

    private $s_encoding = null;

    private $service_File;

    private $service_Settings;

    private $a_documents = array();

    private $bo_fallback = false;

    private $obj_parser;

    private static $_instance;

    /**
     * PHP 5 constructor
     *
     * @param core\services\Settings $service_Settings
     *            The settings service
     * @param core\services\Cookie $service_Cookie
     *            The cookie handler
     * @param core\services\File $service_File
     *            The file service
     */
    public function __construct(\core\services\Settings $service_Settings, \core\services\Cookie $service_Cookie, \core\services\File $service_File)
    {
        parent::__construct();
        
        /* Check language */
        $this->service_File = $service_File;
        $this->service_Settings = $service_Settings;
        $a_languages = $this->getLanguages();
        $this->s_language = $service_Settings->get('defaultLanguage');
        
        if (isset($_GET['lang'])) {
            if (in_array($_GET['lang'], $a_languages)) {
                $this->s_language = $_GET['lang'];
                $service_Cookie->set('language', $this->s_language, '/');
            }
            unset($_GET['lang']);
        } else 
            if ($service_Cookie->exists('language')) {
                if (in_array($service_Cookie->get('language'), $a_languages)) {
                    $this->s_language = $service_Cookie->get('language');
                    /* Renew cookie */
                    $service_Cookie->set('language', $this->s_language, '/');
                } else {
                    $service_Cookie->delete('language');
                }
            }
        
        $this->loadLanguageParser();
    }

    private function loadLanguageParser()
    {
        if ($this->service_Settings->exists('language/type') && $this->service_Settings->get('language/type') == 'mo') {
            if (! class_exists('\core\services\data\LanguageMO')) {
                require (NIV . 'core/services/data/LanguageMO.inc.php');
            }
            $this->obj_parser = new \core\services\data\LanguageMO($this->service_File, $this->s_language);
        } else {
            if (! class_exists('\core\services\data\LanguageXML')) {
                require (NIV . 'core/services/data/LanguageXML.inc.php');
            }
            $this->obj_parser = new \core\services\data\LanguageXML($this->service_File, $this->s_language, $this->bo_fallback);
        }
        
        require (NIV . 'core/services/data/languageShortcut.php');
    }

    public function getLanguageFiles()
    {
        $a_files = array_keys($this->a_documents);
        $a_data = array(
            'language' => $this->s_language,
            'files' => array()
        );
        
        foreach ($a_files as $s_file) {
            if ($s_file == 'site' && $this->bo_fallback) {
                $a_data['files'][$s_file] = 'language/language_' . $this->s_language . '.lang';
            } else {
                $a_data['files'][$s_file] = 'language/' . $this->s_language . '/' . $s_file . '.lang';
            }
        }
        return $a_data;
    }

    /**
     * Collects the installed languages
     *
     * @return array The installed languages
     */
    public function getLanguages()
    {
        $a_languages = array();
        $a_languageFiles = $this->service_File->readDirectory(NIV . 'language');
        
        foreach ($a_languageFiles as $s_languageFile) {
            if (strpos($s_languageFile, 'language_') !== false) {
                /* Fallback */
                return $this->getLanguagesOld();
            }
            
            if ($s_languageFile == '..' || $s_languageFile == '.' || strpos($s_languageFile, '.') !== false) {
                continue;
            }
            
            $a_languages[] = $s_languageFile;
        }
        
        return $a_languages;
    }

    /**
     * Collects the installed languages
     * Old way of storing
     *
     * @return array The installed languages
     */
    private function getLanguagesOld()
    {
        $a_languages = array();
        $a_languageFiles = $this->service_File->readDirectory(NIV . 'include/language');
        
        foreach ($a_languageFiles as $s_languageFile) {
            if (strpos($s_languageFile, 'language_') === false)
                continue;
            
            $s_languageFile = str_replace(array(
                'language_',
                '.lang'
            ), array(
                '',
                ''
            ), $s_languageFile);
            
            $a_languages[] = $s_languageFile;
        }
        
        $this->bo_fallback = true;
        
        return $a_languages;
    }

    public function getLanguageCodes()
    {
        return array(
            'nl_BE' => 'Vlaams',
            'nl_NL' => 'Nederlands',
            'en_AU' => 'English Australia',
            'en_BW' => 'English (Botswana)',
            'en_CA' => 'English (Canada)',
            'en_DK' => 'English (Denmark)',
            'en_GB' => 'English (Great Brittan)',
            'en_UK' => 'English (Great Brittan)',
            'en_HK' => 'English (Hong Kong)',
            'en_IE' => 'English (Ireland)',
            'en_IN' => 'English (India)',
            'en_NZ' => 'English (New Zealand)',
            'en_PH' => 'English (Philippines)',
            'en_SG' => 'English (Singapore)',
            'en_US' => 'English (United States)',
            'en_ZA' => 'English (South Africa)',
            'en_ZW' => 'English (Zimbabwe)'
        );
    }

    /**
     * Sets the language
     *
     * @param String $s_language
     *            code
     * @throws IOException when the language code does not exist
     */
    public function setLanguage($s_language)
    {
        $this->s_language = $s_language;
        
        $this->loadLanguageParser();
    }

    /**
     * Returns the set language
     *
     * @return String The set language
     */
    public function getLanguage()
    {
        return $this->s_language;
    }

    /**
     * Returns the set encoding
     *
     * @return String The set encoding
     */
    public function getEncoding()
    {
        return $this->s_encoding;
    }

    /**
     * Gives the asked part of the loaded file
     *
     * @param String $s_path
     *            The path to the language-part
     * @return String The content of the requested part
     * @throws XMLException when the path does not exist
     */
    public function get($s_path)
    {
        return $this->obj_parser->get($s_path);
    }

    /**
     * Changes the language-values with the given values
     * Collects the text from the language file via the path
     *
     * @param String $s_path
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
        return $this->obj_parser->insertPath($s_path, $a_fields, $a_values);
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
     * @param String $s_path
     *            The path to the language-part
     * @return boolean, true if the part exists otherwise false
     */
    public function exists($s_path)
    {
        return $this->obj_parser->exists($s_path);
    }

    public static function text($s_key)
    {
        if (is_null(Language::$_instance)) {
            Language::$_instance = \core\Memory::services('Language');
        }
        
        return Language::$_instance->get($s_key);
    }
}
?>
