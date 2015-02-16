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
 *        @date 14/09/2014
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
class LanguageXML extends \core\services\Xml
{

    private $s_language = null;

    private $s_encoding = null;

    private $service_File;

    private $a_documents = array();

    private $bo_fallback = false;

    /**
     * PHP 5 constructor
     *
     * @param core\services\File $service_File
     *            The file service
     * @param string $s_lanugage
     *            language
     */
    public function __construct(\core\services\File $service_File, $s_language, $bo_fallback)
    {
        parent::__construct();
        
        $this->service_File = $service_File;
        $this->s_startTag = 'language';
        $this->s_language = $s_language;
        $this->bo_fallback = $bo_fallback;
        
        $this->readLanguage();
    }

    /**
     * Calls the set language-file and reads it
     *
     * @throws IOException If the system of site language file is missing
     */
    private function readLanguage()
    {
        if ($this->bo_fallback) {
            if (! $this->service_File->exists(NIV . 'language/language_' . $this->s_language . '.lang')) {
                throw new IOException('Missing site language file for language ' . $this->s_language . '.');
            }
            
            $this->load(NIV . 'language/language_' . $this->s_language . '.lang');
            $this->a_documents['site'] = $this->obj_document;
            $this->obj_document = null;
        }
        
        /* Get files */
        $a_files = $this->service_File->readDirectory(NIV . 'language/' . $this->s_language . '/LC_MESSAGES');
        foreach ($a_files as $s_file) {
            if (strpos($s_file, '.lang') === false) {
                continue;
            }
            
            $s_name = str_replace('.lang', '', $s_file);
            
            $this->load(NIV . 'language/' . $this->s_language . '/LC_MESSAGES/' . $s_file);
            $this->a_documents[$s_name] = $this->obj_document;
            $this->obj_document = null;
        }
        
        if (! array_key_exists('system', $this->a_documents)) {
            throw new \IOException('Missing system language file for language ' . $this->s_language . '.');
        }
        if (! array_key_exists('site', $this->a_documents)) {
            throw new \IOException('Missing site language file for language ' . $this->s_language . '.');
        }
        
        /* Get encoding */
        $this->s_encoding = $this->get('language/encoding');
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
        $a_path = explode('/', $s_path);
        if (! array_key_exists($a_path[0], $this->a_documents)) {
            $obj_file = $this->a_documents['site'];
        } else {
            $obj_file = $this->a_documents[$a_path[0]];
        }
        
        $a_return = $obj_file->query("//" . $s_path);
        
        if ($a_return->length < 1) {
            /* Part not found */
            throw new \XMLException("Can not find " . $s_path);
        }
        
        foreach ($a_return as $entry) {
            $s_text = $entry->textContent;
        }
        
        return trim($s_text);
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
        $s_text = $this->get($s_path);
        return $this->insert($s_text, $a_fields, $a_values);
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
        \core\Memory::type('string', $s_text);
        
        if (! is_array($a_fields)) {
            $s_text = str_replace('[' . $a_fields . ']', $a_values, $s_text);
        } else {
            for ($i = 0; $i < count($a_fields); $i ++) {
                $s_text = str_replace('[' . $a_fields[$i] . ']', $a_values[$i], $s_text);
            }
        }
        
        return $s_text;
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
        $a_path = explode('/', $s_path);
        if (! array_key_exists($a_path[0], $this->a_documents)) {
            $obj_file = $this->a_documents['site'];
        } else {
            $obj_file = $this->a_documents[$a_path[0]];
        }
        
        $a_return = $obj_file->query("//" . $s_path);
        
        if ($a_return->length < 1) {
            /* Part not found */
            return false;
        }
        
        return true;
    }
}
?>