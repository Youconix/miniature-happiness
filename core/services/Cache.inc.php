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
 * Cache service
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 2.0
 */
class Cache extends \core\services\Service
{

    protected $service_QueryBuilder;
    
    protected $service_File;

    protected $model_Config;

    protected $service_Settings;

    protected $service_Headers;

    protected $s_language;
    
    protected $bo_cache;

    public function __construct(\core\services\File $service_File, \core\models\Config $model_Config, \core\services\Headers $service_Headers,\core\services\QueryBuilder $service_QueryBuilder)
    {
        $this->model_Config = $model_Config;
        $this->service_File = $service_File;
        $this->service_Settings = $model_Config->getSettings();
        $this->service_Headers = $service_Headers;
        $this->service_QueryBuilder = $service_QueryBuilder;
        
        $s_directory = $this->getDirectory();
        if (! $this->service_File->exists($s_directory . 'site')) {
            $this->service_File->newDirectory($s_directory . 'site');
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
     * Returns the cache directory
     *
     * @return string The directory
     */
    protected function getDirectory()
    {
        return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the cache file part seperator
     *
     * @return string The seperator
     */
    protected function getSeperator()
    {
        return '===============|||||||||||||||||||||||=================';
    }

    /**
     * Returns if the file should be cached
     *
     * @return boolean
     */
    protected function shouldCache()
    {
        if( !is_null($this->bo_cache) ){
            return $this->bo_cache;
        }
        
        if ( !$this->service_Settings->exists('cache/status') || $this->service_Settings->get('cache/status') != 1){
            $this->bo_cache = false;
        }
        else {
            $s_page = $_SERVER['REQUEST_URI']; 
            $a_pages = explode('?',$s_page);
            
            $this->service_QueryBuilder->select('no_cache','id')->getWhere()->addOr(array('page','page'),array('s','s'),array($s_page,$a_pages[0]));
            $service_database = $this->service_QueryBuilder->getResult();
            if( $service_database->num_rows() > 0 ){
                $this->bo_cache = false;
            }
            else {
                $this->bo_cache = true;
            }
        }
        
        return $this->bo_cache;
    }

    /**
     * Checks the cache and displays it
     *
     * @return boolean False if no cache is present
     */
    public function checkCache()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET' || ! $this->shouldCache()) {
            return false;
        }
        
        $s_language = $this->model_Config->getLanguage();
        $s_directory = $this->getDirectory();
        
        if (! $this->service_File->exists($s_directory . 'site' . DIRECTORY_SEPARATOR . $s_language)) {
            $this->service_File->newDirectory($s_directory . 'site' . DIRECTORY_SEPARATOR . $s_language);
            return false;
        }
        
        if (! $this->service_File->exists($s_directory . 'site' . DIRECTORY_SEPARATOR . $s_language)) {
            $this->service_File->newDirectory($s_directory . 'site' . DIRECTORY_SEPARATOR . $s_language);
            return false;
        }
        
        $s_target = $this->getCurrentPage();
        
        if (! $this->service_File->exists($s_target)) {
            return false;
        }
        
        $a_file = explode($this->getSeperator(), $this->service_File->readFile($s_target));
        $i_timeout = $this->service_Settings->get('cache/timeout');
        
        if ((time() - $a_file[0]) > $i_timeout) {
            $this->service_File->deleteFile($s_target);
            return false;
        }
        
        $this->displayCache($a_file);
    }

    /**
     * Displays the cached page
     *
     * @param array $a_file
     *            The page parts
     */
    protected function displayCache($a_file)
    {
        $a_headers = unserialize($a_file[1]);
        $this->service_Headers->importHeaders($a_headers);
        $this->service_Headers->printHeaders();
        echo ($a_file[2]);
        die();
    }

    /**
     * Writes the renderd page to the cache
     *
     * @param string $s_output
     *            The rendered page
     */
    public function writeCache($s_output)
    {
        if (! $this->shouldCache()) {
            return;
        }
        
        $s_headers = serialize($this->service_Headers->getHeaders());
        
        $s_output = time() . $this->getSeperator() . $s_headers . $this->getSeperator() . $s_output;
        
        $s_language = $this->model_Config->getLanguage();
        $s_target = $this->getCurrentPage();
        $this->service_File->writeFile($s_target, $s_output);
    }
    
    /**
     * Returns the current page address
     * 
     * @return string   The address
     */
    protected function getCurrentPage(){
        return $this->getAddress($_SERVER['REQUEST_URI']);
    }
    
    /**
     * Returns the full address for the given page
     * 
     * @param string $s_page    The page ($_SERVER['REQUEST_URI'])
     * @return string   The address
     */
    protected function getAddress($s_page){
        return $this->getDirectory() . 'site' . DIRECTORY_SEPARATOR . $s_language . DIRECTORY_SEPARATOR . str_replace('/', '_',$s_page) . '.html';
    }
    
    /**
     * Clears the given page from the site cache
     * 
     * @param string $s_page    The page ($_SERVER['REQUEST_URI'])
     */
    public function clearPage($s_page){
        $s_page = $this->getAddress($s_page);
        
        if( $this->service_File->exists($s_page) ){
            $this->service_File->deleteFile($s_page);
        }
    }

    /**
     * Clears the language cache (.mo)
     */
    public function cleanLanguageCache()
    {
        if ($this->service_Settings->exists('language/type') && $this->service_Settings->get('language/type') == 'mo') {
            clearstatcache();
        }
    }

    /**
     * Clears the site cache
     */
    public function clearSiteCache()
    {
        $s_dir = $this->getDirectory() . 'site';
        
        $a_files = $this->service_File->readDirectory($s_dir);
        foreach ($a_files as $s_file) {
            if ($s_file == '.' || $s_file == '.') {
                continue;
            }
            
            if (is_dir($s_dir . DIRECTORY_SEPARATOR . $s_file)) {
                $this->service_File->deleteDirectory($s_dir . DIRECTORY_SEPARATOR . $s_file);
            } else {
                $this->service_File->deleteFile($s_dir . DIRECTORY_SEPARATOR . $s_file);
            }
        }
    }
}