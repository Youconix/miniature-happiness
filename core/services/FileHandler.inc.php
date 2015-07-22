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
        
        $directory = new \RecursiveDirectoryIterator($s_directory,\RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
        return $iterator;
    }
    
    public function directoryFilterName(\DirectoryIterator $directory, $a_names = array()){
        return new \core\classes\DirectoryFilterIteractor($directory,$a_names);
    }
    
    public function recursiveDirectoryFilterName(\RecursiveIteratorIterator $directory, $s_filter){
        echo($s_filter);
        return new \RegexIterator($directory, $s_filter,\RegexIterator::MATCH);
    }
    
    public function readFilteredDirectory($s_directory,$a_skipDirs = array(),$s_extension = ''){
        $a_dirs = array();
        $directory = $this->readDirectory($s_directory);
        foreach($directory AS $item){
            if($item->isDot()) continue;
            
            if( in_array($item->getPathname(),$a_skipDirs) )    continue;
                        
            if( $item->isDir() ){
                $a_dirs[$item->getBasename()] = $this->readFilteredDirectory($item->getPathname(),$a_skipDirs,$s_extension);
                continue;
            }
            
            if( !empty($s_extension) && !preg_match('/'.$s_extension.'$/', $item->getBasename())) continue;
            
            $a_dirs[] = clone $item;
        }
        
        return $a_dirs;
    }
    
    /**
     * Reads the content from the given file
     *
     * @param String $s_file
     *            name
     * @param boolean $bo_binary
     *            true for binary reading, optional
     * @return String The content from the requested file
     * @throws IOException when the file does not exists or is not readable
     */
    public function readFile($s_file, $bo_binary = false)
    {
    	\core\Memory::type('string', $s_file,true);
    	
    	if ( !preg_match("#^(http://|ftp://)#si", $s_file) && !$this->exists($s_file)) {
    		throw new \IOException('File ' . $s_file . ' does not exist!');
    	}
    	
    	$file = new \SplFileObject($s_file);
    	if( !$file->isReadable() ){
    		throw new \IOException('Can not read ' . $s_file . '. Check the permissions');
    	}
    	
    	$content = '';
    	while (!$file->eof()) {
    		$content .= $file->fgets();
    	}
    	
    	return $content;
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