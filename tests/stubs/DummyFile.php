<?php

if( !class_exists('\core\services\File') ){
  require(NIV . 'include/services/File.inc.php');
}

class DummyFile extends \core\services\File{

  public function __construct(){
    
  }

  public function newFile($s_file, $i_rights, $bo_binary = false){
    
  }

  /**
   * Reads the content from the given file
   *
   * @param 	String	$s_file		The file name
   * @param	boolean	$bo_binary	Set to true for binary reading, optional
   * @return	String  The content from the requested file
   * @throws IOException when the file does not exists or is not readable
   */
  public function readFile($s_file, $bo_binary = false){
    \core\Memory::type('string', $s_file);

    return '';
  }

  /**
   * Overwrites the given file or generates it if does not exists
   *
   * @param      String  $s_file     The url
   * @param      String  $s_content  The content
   * @param      int     $i_rights   The permissions of the file, default 0644 (read/write for owner, read for rest)
   * @param		boolean	$bo_binary	Set to true for binary writing, optional
   * @throws		IOException When the file is not readable or writable
   */
  public function writeFile($s_file, $s_content, $i_rights = 0644, $bo_binary = false){
    \core\Memory::type('string', $s_file);
    \core\Memory::type('string', $s_content);
    \core\Memory::type('int', $i_rights);
  }

  /**
   * Writes at the end of the given file or generates it if doens not exists
   *
   * @param      String  $s_file     The url
   * @param      String  $s_content  The content
   * @param      int     $i_rights   The permissions of the file, default 0644 (read/write for owner, read for rest)
   * @param		boolean	$bo_binary	Set to true for binary writing, optional
   * @throws		IOException When the file is not readable or writable
   */
  public function writeLastFile($s_file, $s_content, $i_rights = 0644, $bo_binary = false){
    \core\Memory::type('string', $s_file);
    \core\Memory::type('string', $s_content);
    \core\Memory::type('int', $i_rights);
  }

  /**
   * Writes at the begin of the given file or generates it if doens not exists
   *
   * @param      String  $s_file     The url
   * @param      String  $s_content  The content
   * @param      int     $i_rights   The permissions of the file, default 0644 (read/write for owner, read for rest)
   * @param		boolean	$bo_binary	Set to true for binary writing, optional
   * @throws		IOException When the file is not readable or writable
   */
  public function writeFirstFile($s_file, $s_content, $i_rights = 0644, $bo_binary = false){
    \core\Memory::type('string', $s_file);
    \core\Memory::type('string', $s_content);
    \core\Memory::type('int', $i_rights);
  }

  /**
   * Renames the given file
   *
   * @param     String  $s_nameOld  The current url
   * @param     String  $s_nameNew  The new url
   * @throws    IOException when the file does not exist or is not writable (needed for renaming)
   */
  public function renameFile($s_nameOld, $s_nameNew){
    \core\Memory::type('string', $s_nameOld);
    \core\Memory::type('string', $s_nameNew);
  }

  /**
   * Copy's the given file to the given directory
   *
   * @param    String  $s_file     The file to copy
   * @param    String  $s_target   The target directory
   * @throws   IOException when the file is not readable or the target directory is not writable
   */
  public function copyFile($s_file, $s_target){
    \core\Memory::type('string', $s_file);
    \core\Memory::type('string', $s_target);
  }

  /**
   * Moves the given file to the given directory
   *
   * @param     String  $s_file     The current url
   * @param     String  $s_target   The target directory
   * @throws    IOException when the target directory is not writable (needed for moving)
   */
  public function moveFile($s_file, $s_target){
    \core\Memory::type('string', $s_file);
    \core\Memory::type('string', $s_target);
  }

  /**
   * Deletes the given file
   *
   * @param    String  $s_file The file to delete
   * @throws   IOException when the file does not exist or is not writable
   */
  public function deleteFile($s_file){
    \core\Memory::type('string', $s_file);
  }

  /**
   * Reads the given directory
   *
   * @param  String  $s_directory    The directory to read
   * @param  boolean $bo_recursive   Set true for recursive reading
   * @return array   The files and directorys of the directory
   */
  public function readDirectory($s_directory, $bo_recursive = false){
    \core\Memory::type('string', $s_directory);

    return array();
  }

  /**
   * Generates a new directory with the given name and rights
   *
   * @param   String  $s_name The name
   * @param   int     $i_rights   The rights, defaul 0755 (write/write/excequte for owner, rest read + excequte)
   * @throws  IOException when the target directory is not writable
   */
  public function newDirectory($s_name, $i_rights = 0755){
    \core\Memory::type('string', $s_name);
    \core\Memory::type('int', $i_rights);
  }

  /**
   * Moves a directory too the given address
   *
   * @param  String  $s_directoryOld The current directory
   * @param  String  $s_directoryNew The target address
   * @return	boolean	True on success,  false on failure
   */
  public function moveDirectory($s_directoryOld, $s_directoryNew){
    \core\Memory::type('string', $s_directoryOld);
    \core\Memory::type('string', $s_directoryNew);
  }

  /**
   * Copy's a directory to a new location
   *
   * @param  String  $s_directoryOld The current location
   * @param  String  $s_directoryNew The new location
   * @throws	IOException if the copy failes
   */
  public function copyDirectory($s_directoryOld, $s_directoryNew){
    \core\Memory::type('string', $s_directoryOld);
    \core\Memory::type('string', $s_directoryNew);
  }

  /**
   * Deletes the given Directory
   *
   * @param   String  $s_directory    The directory to delete
   * @return  boolean True on success, false on failure
   * @throws  IOException   When the directory is not writetable
   */
  public function deleteDirectory($s_directory){
    \core\Memory::type('string', $s_directory);

    return true;
  }

  /**
   * Sets the rights from a file or directory. The rights must be in hexadecimal form (0644)
   *
   * @param  String  $s_file     The file
   * @param  int     $i_rights   The new rights
   * @return	boolean	True on success, false on failure
   */
  public function rights($s_file, $i_rights){
    return true;
  }

}
?>