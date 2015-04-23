<?php
define('NIV', dirname(__FILE__) . '/../../../');

require (NIV . 'tests/GeneralTest.php');

class testFile extends GeneralTest
{

    private $service_File;

    private $s_file = 'example.txt';

    private $i_rights = 0600;

    private $s_content1 = 'content1';

    private $s_content2 = 'content2';

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/services/File.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->service_File = new \core\services\File();
    }

    public function tearDown()
    {
        $this->service_File = null;
        
        if (file_exists($this->s_temp . $this->s_file)) {
            unlink($this->s_temp . $this->s_file);
        }
        
        parent::tearDown();
    }

    /**
     * Generates a new file with the given rights
     *
     * @test
     */
    public function newFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        $this->service_File->newFile($s_file, $this->i_rights);
        if (! file_exists($s_file)) {
            $this->fail('File ' . $s_file . ' was not created');
        }
    }

    /**
     * Reads the content from the given file
     *
     * @test
     * @expectedException IOException
     */
    public function readFile()
    {
        $this->service_File->readFile($this->s_temp . $this->s_file);
    }

    /**
     * Overwrites the given file or generates it if does not exists
     *
     * @test
     */
    public function writeFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        
        $this->createFile($s_file,$this->s_content1,$this->i_rights);
                
        $this->assertEquals($this->s_content1, $this->service_File->readFile($s_file));
    }

    /**
     * Writes at the end of the given file or generates it if doens not exists
     *
     * @test
     */
    public function writeLastFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        
        $this->service_File->writeFile($s_file, $this->s_content1, $this->i_rights);
        $this->service_File->writeLastFile($s_file, $this->s_content2, $this->i_rights);
        
        $s_expected = $this->s_content1 . $this->s_content2;
        $this->assertEquals($s_expected, $this->service_File->readFile($s_file));
    }

    /**
     * Writes at the begin of the given file or generates it if doens not exists
     *
     * @test
     */
    public function writeFirstFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        
        $this->service_File->writeFile($s_file, $this->s_content1, $this->i_rights);
        $this->service_File->writeFirstFile($s_file, $this->s_content2, $this->i_rights);
        
        $s_expected = $this->s_content2 . $this->s_content1;
        $this->assertEquals($s_expected, $this->service_File->readFile($s_file));
    }

    /**
     * Renames the given file
     *
     * @test
     */
    public function renameFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        
        $this->newFile($s_file);
        $this->service_File->renameFile($s_file, $s_file . '_2');
        if (file_exists($s_file) || ! file_exists($s_file . '_2')) {
            $this->fail('Renaming ' . $s_file . ' to ' . $s_file . '_2 failed');
        }
        
        unlink($s_file . '_2');
    }

    /**
     * Copy's the given file to the given directory
     *
     * @test
     */
    public function copyFile()
    {
        $s_file = 'testFile.php';
        $this->createFile(NIV.'files/'.$s_file, 'lalalala', $this->i_rights);
        
        $this->service_File->copyFile(NIV.'files/'.$s_file, $this->s_temp);
        if (! file_exists($this->s_temp . $s_file)) {
            $this->fail('Copying ' . $s_file . ' to ' . $this->s_temp . ' failed');
        }
        
        unlink($this->s_temp . $s_file);
        unlink(NIV.'files/'.$s_file);
    }

    /**
     * Moves the given file to the given directory
     *
     * @test
     * @expectedException IOException
     */
    public function moveFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        $this->createFile(NIV.'files/'.$s_file, 'lalalala', $this->i_rights);
        
        $this->service_File->moveFile($s_file, '/');
    }

    /**
     * Deletes the given file
     *
     * @test
     * @expectedException IOException
     */
    public function deleteFile()
    {
        $s_file = $this->s_temp . $this->s_file;
        
        $this->service_File->deleteFile($s_file);
    }

    /**
     * Reads the given directory
     *
     * @param String $s_directory
     *            The directory to read
     * @param boolean $bo_recursive
     *            Set true for recursive reading
     * @return array The files and directorys of the directory
     */
    public function readDirectory($s_directory, $bo_recursive = false)
    {
        \core\Memory::type('string', $s_directory);
        
        /* Check Directory */
        if (! is_readable($s_directory)) {
            throw new \IOException('Can not read directory ' . $s_directory . '.');
        }
        
        /* read directory */
        $s_handle = opendir($s_directory);
        if ($s_handle === false) {
            throw new \IOException('Can not open directory ' . $s_directory . '.');
        }
        
        $a_files = array();
        
        /* read all files */
        while (false !== ($s_file = readdir($s_handle))) {
            if (($s_file == '.') || ($s_file == '..') || (substr($s_file, - 1) == "~"))
                continue;
                
                /* check of $file is a directory */
            if (is_dir($s_directory . '/' . $s_file) && $bo_recursive) {
                /* read subdirectory */
                $a_files[] = $this->readDirectory($s_directory . '/' . $s_file, $bo_recursive);
            } else 
                if ($bo_recursive) {
                    $a_files[] = $s_directory . '/' . $s_file;
                } else {
                    $a_files[] = $s_file;
                }
        }
        
        /* close directory */
        closedir($s_handle);
        
        /* Sort arrays and merge arrays */
        sort($a_files);
        
        return $a_files;
    }

    /**
     * Generates a new directory with the given name and rights
     *
     * @param String $s_name
     *            The name
     * @param int $i_rights
     *            The rights, defaul 0755 (write/write/excequte for owner, rest read + excequte)
     * @throws IOException when the target directory is not writable
     */
    public function newDirectory($s_name, $i_rights = 0755)
    {
        \core\Memory::type('string', $s_name);
        \core\Memory::type('int', $i_rights);
        
        $s_dir = $s_name;
        if (substr($s_dir, - 1) == '/')
            $s_dir = substr($s_dir, 0, - 1);
        $i_pos = strrpos($s_dir, '/');
        if ($i_pos === false) {
            throw new \IOException("Invalid directory " . $s_name . ".");
        }
        
        $s_dir = substr($s_dir, 0, $i_pos);
        
        if (! is_writable($s_dir)) {
            throw new \IOException('Directory ' . $s_name . ' is not writable.');
        }
        
        mkdir($s_name, $i_rights);
        
        $this->rights($s_name, $i_rights);
    }

    /**
     * Renames the given Directory
     *
     * @param String $s_nameOld
     *            The current name
     * @param String $s_nameNew
     *            The new name
     * @throws IOException if the directory is not writable
     */
    public function renameDirectory($s_nameOld, $s_nameNew)
    {
        \core\Memory::type('string', $s_nameOld);
        \core\Memory::type('string', $s_nameNew);
        
        /* Check or the directory exists */
        if (! $this->exists($s_nameOld)) {
            throw new \IOException('Directory ' . $s_nameOld . ' does not exist.');
        }
        
        /* Check for correct rights */
        if (! is_writable($s_nameNew)) {
            throw new \IOException('Can not write new position for directory ' . $s_nameOld . '.');
        }
        
        if ($this->copyDirectory($s_nameOld, $s_nameNew)) {
            $this->deleteDirectory($s_nameOld, $s_nameNew);
        }
    }

    /**
     * Moves a directory too the given address
     *
     * @param String $s_directoryOld
     *            The current directory
     * @param String $s_directoryNew
     *            The target address
     * @return boolean on success, false on failure
     */
    public function moveDirectory($s_directoryOld, $s_directoryNew)
    {
        \core\Memory::type('string', $s_directoryOld);
        \core\Memory::type('string', $s_directoryNew);
        
        /* Copy map */
        if ($this->copyDirectory($s_directoryOld, $s_directoryNew)) {
            /* Delete old map */
            return $this->deleteDirectory($s_directoryOld);
        }
    }

    /**
     * Copy's a directory to a new location
     *
     * @param String $s_directoryOld
     *            The current location
     * @param String $s_directoryNew
     *            The new location
     * @throws IOException if the copy failes
     */
    public function copyDirectory($s_directoryOld, $s_directoryNew)
    {
        \core\Memory::type('string', $s_directoryOld);
        \core\Memory::type('string', $s_directoryNew);
        
        /* Check or new map exists */
        if (! $this->exists($s_directoryNew)) {
            $this->newDirectory($s_directoryNew);
        }
        
        $a_dir = explode('/', $s_directoryOld);
        $s_dir = '';
        for ($i = 0; $i < count($a_dir) - 1; $i ++) {
            if ($s_dir == '') {
                $s_dir .= $a_dir[$i];
            } else {
                $s_dir .= '/' . $a_dir[$i];
            }
        }
        
        if (! is_writable($s_dir)) {
            throw new \IOException('Can not write in directory ' . $s_dir . '.');
        }
        
        /* Open old Directory */
        if (! $s_handle = opendir($s_directoryOld)) {
            throw new \IOException('Can not open directory ' . $s_directoryOld . '.');
        }
        
        /* Read old map */
        while (false !== ($s_file = readdir($s_handle))) {
            if (($s_file == '.') || ($s_file == '..') || (substr($s_file, - 1) != "~"))
                continue;
                
                /* check or $file is a map */
            if (! is_dir($s_file)) {
                /* Copy file */
                $this->copyFile($s_directoryOld . '/' . $s_file, $s_directoryNew . '/' . $s_file);
            } else {
                /* Copy subdirectory */
                $this->copyDirectory($s_directoryOld . '/' . $s_file, $s_directoryNew . '/' . $s_file);
            }
        }
    }

    /**
     * Deletes the given Directory
     *
     * @param String $s_directory
     *            The directory to delete
     * @return boolean True on success, false on failure
     * @throws IOException When the directory is not writetable
     */
    public function deleteDirectory($s_directory)
    {
        \core\Memory::type('string', $s_directory);
        
        /* Delete all present files and maps */
        if (! $s_handle = opendir($s_directory)) {
            throw new \IOException('Can\'t read directory ' . $s_directory . '.');
        }
        
        while (false !== ($s_file = readdir($s_handle))) {
            if (($s_file != '.') && ($s_file != '..')) {
                /* check or $file is a map */
                if (is_dir($s_directory . '/' . $s_file)) {
                    $this->deleteDirectory($s_directory . $s_file);
                } else {
                    $this->deleteFile($s_directory . '/' . $s_file);
                }
            }
        }
        
        if (! rmdir($s_directory)) {
            throw new \IOException('Can\'t delete ' . $s_directory . '.');
        }
        
        return true;
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
        \core\Memory::type('string', $s_file);
        
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
        \core\Memory::type('string', $s_file);
        \core\Memory::type('int', $i_rights);
        
        if (function_exists('chmod')) {
            eval("chmod(\$s_file, $i_rights);");
            
            return true;
        } else {
            return false;
        }
    }
    
    private function createFile($s_filename,$s_content,$i_rights){
        $this->service_File->writeFile($s_filename, $s_content, $i_rights);
    }
}