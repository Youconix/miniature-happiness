<?php
namespace core\services;

class FileHandler extends \core\services\Service
{
    public function readDirectory($s_directory){
        \core\Memory::type('string', $s_directory);
        
        return new \DirectoryIterator($s_directory);
    }
    
    public function readRecursiveDirectory($s_directory){
        \core\Memory::type('string', $s_directory);
        
        return new \RecursiveDirectoryIterator($s_directory);
    }
    
    public function directoryFilterName(\DirectoryIterator $directory, $a_names = array()){
        return new \core\classes\DirectoryFilterIteractor($directory,$a_names);
    }
    


    /**
     * Checks if the given file or directory exists
     *
     * @param String $s_file
     *            name or directory name
     * @return boolean if file or directory exists, otherwise false
     */
    public function exists($s_file)
    {
        \core\Memory::type('string', $s_file,true);
    
        if (file_exists($s_file)) {
            return true;
        }
    
        return false;
    }
    
    /**
     * Sets the rights from a file or directory.
     * The rights must be in hexadecimal form (0644)
     *
     * @param String $s_file
     *            The file
     * @param int $i_rights
     *            The new rights
     * @return boolean on success, false on failure
     */
    public function rights($s_file, $i_rights)
    {
        \core\Memory::type('string', $s_file,true);
        \core\Memory::type('int', $i_rights);
    
        if (function_exists('chmod')) {
            eval("chmod(\$s_file, $i_rights);");
    
            return true;
        } else {
            return false;
        }
    }
}
?>