<?php
/**
 * File handler class for manipulating files and directorys
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2006
 * @changed   		25/09/2010
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
class Service_File extends Service {
	/**
	 * Starts the service File and loads the exceptions
	 *
	 * @throws  Exception if the exception directory can not be read
	 */
	public function __construct(){
		$s_directory    = NIV.'include/exceptions';
		if( !is_readable($s_directory) ){
			throw new Exception('Can not read directory include/exceptions.');
		}

		/* read directory */
		$s_handle = opendir($s_directory);
		if( $s_handle === false ){
			throw new Exception('Can not open directory include/exceptions');
		}

		/* read all exceptions */
		require_once($s_directory.'/GeneralException.inc.php');
		while( false !== ($s_file = readdir($s_handle)) ){
			if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) == "~") )  continue;

			/* check of $file is a directory */
			if( is_dir($s_directory.'/'.$s_file) || stripos($s_file, ".php") === false )  continue;

			require_once($s_directory.'/'.$s_file);
		}

		/* close directory */
		closedir( $s_handle );
	}

	/**
	 * Generates a new file with the given rights
	 *
	 * @param      String  $s_file     The url
	 * @param      int     $i_rights   The permissions of the file, default 0644 (read/write for owner, read for rest)
	 * @param		boolean	$bo_binary	Set to true for binary writing, optional	
	 * @throws		IOException when the given directory is not writable
	 */
	public function newFile($s_file,$i_rights,$bo_binary = false){
		Memory::type('string',$s_file);
		Memory::type('int',$i_rights);

		$s_file2 = null;
		$a_dir = explode('/',$s_file);
		$s_dir = '';
		for($i=0; $i<count($a_dir)-1; $i++){
			if( $s_dir == '' ){	$s_dir .= $a_dir[$i];	}	else{	$s_dir .= '/'.$a_dir[$i];	}
		}

		if( !is_writable($s_dir) ){
			throw new IOException('Can not make file '.$s_file.' in directory '.$s_dir.'. Check the permissions');
		}

		($bo_binary) ? $s_mode = 'wb+' : $s_mode = 'w+';
		$s_file2 = fopen($s_file,$s_mode);
		fwrite($s_file2,"");
		fclose($s_file2);

		$this->rights($s_file,$i_rights);
	}

	/**
	 * Reads the content from the given file
	 *
	 * @param 	String	$s_file		The file name
	 * @param	boolean	$bo_binary	Set to true for binary reading, optional
	 * @return	String  The content from the requested file
	 * @throws IOException when the file does not exists or is not readable
	 */
	public function readFile($s_file,$bo_binary = false){
		Memory::type('string',$s_file);

		if( preg_match("#^(http://|ftp://)#si",$s_file) ){
			return $this->readExtenalFile($s_file,$bo_binary);
		}

		if( !$this->exists($s_file) ){
			throw new IOException('File '.$s_file.' does not exist!');
		}

		/* Check file */
		if( !is_readable($s_file) ){
			throw new IOException('Can not read '.$s_file.'. Check the permissionse');
		}

		filesize($s_file) != 0 ? $i_size = filesize($s_file) : $i_size = 1;
		($bo_binary) ? $s_mode = 'rb' : $s_mode = 'r';
		$s_file = fopen($s_file,$s_mode);
		$s_content = fread($s_file,$i_size);
		fclose($s_file);

		return $s_content;
	}

	/**
	 * Reads the content from the given file on a different server
	 *
	 * @param 	String	$s_file		The file name
	 * @param	boolean	$bo_binary	Set to true for binary reading, optional
	 * @return	String  The content from the requested file
	 */
	private function readExtenalFile($s_file,$bo_binary = false){
		$i_size = null; $s_content = null;

		$i_size = 50000;
		($bo_binary) ? $s_mode = 'rb' : $s_mode = 'r';
		$s_file = @fopen($s_file,$s_mode);
		$s_content = @fread($s_file,$i_size);
		@fclose($s_file);

		return $s_content;
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
	public function writeFile($s_file,$s_content,$i_rights = 0644,$bo_binary = false){
		Memory::type('string',$s_file);
		Memory::type('string',$s_content);
		Memory::type('int',$i_rights);

		/* Check file */
		if( !$this->exists($s_file) ){
			$this->newFile($s_file, $i_rights,$bo_binary);
		}

		if( !is_readable($s_file) ){
			throw new IOException('Can not open file '.$s_file.'. Check the permissions');
		}

		if( !is_writable($s_file) ){
			throw new IOException('Can not write file '.$s_file.'. Check the permissions');
		}

		($bo_binary) ? $s_mode = 'wb+' : $s_mode = 'w+';
		$s_file2 = fopen($s_file,$s_mode);
		fwrite($s_file2,$s_content);
		fclose($s_file2);
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
	public function writeLastFile($s_file,$s_content,$i_rights = 0644,$bo_binary = false){
		$s_file2 = null;

		Memory::type('string',$s_file);
		Memory::type('string',$s_content);
		Memory::type('int',$i_rights);

		/* Check file */
		if( !$this->exists($s_file) ){
			$this->newFile($s_file,$i_rights,$bo_binary);
		}

		if( !is_readable($s_file) ){
			throw new IOException('Can not open file '.$s_file.'. Check the permissions');
		}

		if( !is_writable($s_file) ){
			throw new IOException('Can not write file '.$s_file.'. Check the permissions');
		}

		($bo_binary) ? $s_mode = 'ab' : $s_mode = 'a';
		$s_file2 = fopen($s_file,$s_mode);
		fwrite($s_file2,$s_content);
		fclose($s_file2);
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
	public function writeFirstFile($s_file,$s_content,$i_rights = 0644,$bo_binary = false){
		$s_file2 = null;

		Memory::type('string',$s_file);
		Memory::type('string',$s_content);
		Memory::type('int',$i_rights);

		/* Check file */
		if( !$this->exists($s_file) ){
			$this->newFile($s_file,$i_rights);
		}

		if( !is_readable($s_file) ){
			throw new IOException('Can not open file '.$s_file.'. Check the permissions');
		}

		if( !is_writable($s_file) ){
			throw new IOException('Can not write file '.$s_file.'. Check the permissions');
		}

		($bo_binary) ? $s_mode = 'cb' : $s_mode = 'c';
		$s_file2 = fopen($s_file,$s_mode);
		fwrite($s_file2,$s_content);
		fclose($s_file2);
	}

	/**
	 * Renames the given file
	 *
	 * @param     string  $s_nameOld  The current url
	 * @param     string  $s_nameNew  The new url
	 * @throws    IOException when the file does not exist or is not writable (needed for renaming)
	 */
	public function renameFile($s_nameOld,$s_nameNew){
		Memory::type('string',$s_nameOld);
		Memory::type('string',$s_nameNew);

		/* Check file */
		if( !$this->exists($s_nameOld) ){
			throw new IOException('File '.$s_nameOld.' does not exist.');
		}

		if( !rename($s_nameOld,$s_nameNew) ){
			throw new IOException('File '.$s_nameOld.' can not be renamed.');
		}
	}

	/**
	 * Copy's the given file to the given directory
	 *
	 * @param    string  $s_file     The file to copy
	 * @param    string  $s_target   The target directory
	 * @throws   IOException when the file is not readable or the target directory is not writable
	 */
	public function copyFile($s_file,$s_target){
		Memory::type('string',$s_file);
		Memory::type('string',$s_target);

		/* Check file */
		if( !$this->exists($s_file) ){
			throw new IOException('Can not read file '.$s_file.'.');
		}

		/* Check target */
		if( !is_writable($s_target) ){
			throw new IOException('Can not write in directory '.$s_target.'.');
		}

		$a_filename	= explode('/',$s_file);
		$s_filename  = end($a_filename);

		copy($s_file,$s_target.'/'.$s_filename);
	}

	/**
	 * Moves the given file to the given directory
	 *
	 * @param     string  $s_file     The current url
	 * @param     string  $s_target   The target directory
	 * @throws    IOException when the target directory is not writable (needed for moving)
	 */
	public function moveFile($s_file,$s_target){
		Memory::type('string',$s_file);
		Memory::type('string',$s_target);

		/* Check file and target-directory */
		if( !$this->exists($s_file) ){
			throw new IOException('File '.$s_file.' does not exist');
		}

		if( !is_writable($s_file) ){
			throw new IOException('File '.$s_file.' is not writable, needed for deleting.');
		}

		if( !is_writable($s_target) ){
			throw new IOException('Directory '.$s_target.' is not writable');
		}

		/* Copy old file */
		$this->copyFile($s_file,$s_target);

		/* Delete old file */
		$this->deleteFile($s_file);
	}

	/**
	 * Deletes the given file
	 *
	 * @param    string  $s_file The file to delete
	 * @throws   IOException when the file does not exist or is not writable
	 */
	public function deleteFile($s_file){
		Memory::type('string',$s_file);

		/* Check file */
		if( !$this->exists($s_file) ){
			throw new IOException('File '.$s_file.' does not exist.');
		}

		if( !is_writable($s_file) ){
			throw new IOException('File '.$s_file.' is not writable.');
		}

		unlink($s_file);
	}

	/**
	 * Reads the given directory
	 *
	 * @param  string  $s_directory    The directory to read
	 * @param  boolean $bo_recursive   Set true for recursive reading
	 * @return array   The files and directorys of the directory
	 */
	public function readDirectory($s_directory,$bo_recursive = false){
		Memory::type('string',$s_directory);

		/* Check Directory */
		if( !is_readable($s_directory) ){
			throw new IOException('Can not read directory '.$s_directory.'.');
		}

		/* read directory */
		$s_handle = opendir($s_directory);
		if( $s_handle === false ){
			throw new IOException('Can not open directory '.$s_directory.'.');
		}

		$a_files        = array();

		/* read all files */
		while( false !== ($s_file = readdir($s_handle)) ){
			if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) == "~") )  continue;

			/* check of $file is a directory */
			if( is_dir($s_directory.'/'.$s_file) && $bo_recursive ){
				/* read subdirectory  */
				$a_files[] = $this->readDirectory($s_directory.'/'.$s_file,$bo_recursive);
			}
			else if( $bo_recursive ){
				$a_files[] = $s_directory.'/'.$s_file;
			}
			else {
				$a_files[] = $s_file;
			}
		}

		/* close directory */
		closedir( $s_handle );

		/* Sort arrays and merge arrays */
		sort($a_files);

		return $a_files;
	}

	/**
	 * Generates a new directory with the given name and rights
	 *
	 * @param   string  $s_name The name
	 * @param   int     $i_rights   The rights, defaul 0755 (write/write/excequte for owner, rest read + excequte)
	 * @throws  IOException when the target directory is not writable
	 */
	public function newDirectory($s_name,$i_rights = 0755){
		$a_dir = null; $i = null; $s_dir = null;

		Memory::type('string',$s_name);
		Memory::type('int',$i_rights);

		$s_dir = $s_name;
		if( substr($s_dir,-1) == '/' )		$s_dir = substr($s_dir, 0,-1);
		$i_pos	= strrpos($s_dir,'/');
		if( $i_pos === false ){
			throw new IOException("Invalid directory ".$s_name.".");
		}
		
		$s_dir = substr($s_dir, 0,$i_pos);

		if( !is_writable($s_dir) ){
			throw new IOException('Directory '.$s_name.' is not writable.');
		}

		mkdir($s_name,$i_rights);

		$this->rights($s_name,$i_rights);
	}

	/**
	 * Renames the given Directory
	 *
	 * @param	string  $s_nameOld  The current name
	 * @param  string  $s_nameNew  The new name
	 * @throws	IOException if the directory is not writable
	 */
	public function renameDirectory($s_nameOld,$s_nameNew){
		Memory::type('string',$s_nameOld);
		Memory::type('string',$s_nameNew);

		/* Check or the directory exists */
		if( !$this->exists($s_nameOld) ){
			throw new IOException('Directory '.$s_nameOld.' does not exist.');
		}

		/* Check for correct rights */
		if( !is_writable($s_nameNew) ){
			throw new IOException('Can not write new position for directory '.$s_nameOld.'.');
		}

		if( $this->copyDirectory($s_nameOld,$s_nameNew) ){
			$this->deleteDirectory($s_nameOld,$s_nameNew);
		}
	}

	/**
	 * Moves a directory too the given address
	 *
	 * @param  string  $s_directoryOld The current directory
	 * @param  string  $s_directoryNew The target address
	 * @return	boolean	True on success,  false on failure
	 */
	public function moveDirectory($s_directoryOld,$s_directoryNew){
		Memory::type('string',$s_directoryOld);
		Memory::type('string',$s_directoryNew);

		/* Copy map */
		if( $this->copyDirectory($s_directoryOld,$s_directoryNew) ){
			/* Delete old map */
			return $this->deleteDirectory($s_directoryOld);
		}
	}

	/**
	 * Copy's a directory to a new location
	 *
	 * @param  string  $s_directoryOld The current location
	 * @param  string  $s_directoryNew The new location
	 * @throws	IOException if the copy failes
	 */
	public function copyDirectory($s_directoryOld,$s_directoryNew){
		$a_dir = null; $i = null; $s_dir = null; $s_handle = null; $s_file = null;

		Memory::type('string',$s_directoryOld);
		Memory::type('string',$s_directoryNew);

		/* Check or new map exists */
		if( !$this->exists($s_directoryNew) ){
			$this->newDirectory($s_directoryNew);
		}

		$a_dir = explode('/',$s_directoryOld);
		$s_dir = '';
		for($i=0; $i<count($a_dir)-1; $i++){
			if( $s_dir == '' ){	$s_dir .= $a_dir[$i];	}	else{	$s_dir .= '/'.$a_dir[$i];	}
		}

		if( !is_writable($s_dir) ){
			throw new IOException('Can not write in directory '.$s_dir.'.');
		}

		/* Open old Directory */
		if( !$s_handle = opendir($s_directoryOld)){
			throw new IOException('Can not open directory '.$s_directoryOld.'.');
		}

		/* Read old map */
		while (false !== ($s_file = readdir($s_handle))){
			if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) != "~"))   continue;

			/* check or $file is a map */
			if( !is_dir($s_file) ){
				/* Copy file */
				$this->copyFile($s_directoryOld.'/'.$s_file,$s_directoryNew.'/'.$s_file);
			}
			else{
				/* Copy subdirectory */
				$this->copyDirectory($s_directoryOld.'/'.$s_file,$s_directoryNew.'/'.$s_file);
			}
		}
	}

	/**
	 * Deletes the given Directory
	 *
	 * @param   string  $s_directory    The directory to delete
	 * @return  boolean True on success, false on failure
	 * @throws  IOException   When the directory is not writetable
	 */
	public function deleteDirectory($s_directory){
		Memory::type('string',$s_directory);

		/* Delete all present files and maps */
		if( !$s_handle = opendir($s_directory)){
			throw new IOException('Can\'t read directory '.$s_directory.'.');
		}

		while (false !== ($s_file = readdir($s_handle))){
			if( ($s_file != '.') && ($s_file != '..') ){
				/* check or $file is a map */
				if( is_dir($s_directory.'/'.$s_file) ){
					$this->deleteDirectory($s_directory.$s_file);
				}
				else{
					$this->deleteFile($s_directory.'/'.$s_file);
				}
			}
		}

		if( !rmdir($s_directory) ){
			throw new IOException('Can\'t delete '.$s_directory.'.');
		}

		return true;
	}

	/**
	 * Checks if the given file or directory exists
	 *
	 * @param	  String	$s_file		The file name or directory name
	 * @return   boolean	True if file or directory exists, otherwise false
	 */
	public function exists($s_file){
		Memory::type('string',$s_file);

		if( file_exists($s_file) ){
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * Sets the rights from a file or directory. The rights must be in hexadecimal form (0644)
	 *
	 * @param  string  $s_file     The file
	 * @param  int     $i_rights   The new rights
	 * @return	boolean	True on success, false on failure
	 */
	public function rights($s_file,$i_rights){
		Memory::type('string',$s_file);
		Memory::type('int',$i_rights);

		if( function_exists('chmod') ) {
			eval("chmod(\$s_file, $i_rights);");

			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * Encodes attachment in requested format.
	 *
	 * @param   string  $s_path     The path to the file
	 * @param   string  $s_encoding The encoding (base64|7bit|8bit|binary), default base64
	 * @return  string  The encoded file
	 */
	public function encodeFile($s_path, $s_encoding = "base64") {
		try {
			$file_buffer = $this->readFile($s_path,'rb');
			$file_buffer = $this->encodeString($file_buffer, $s_encoding);
			return $file_buffer;
		}
		catch(IOException $e){
			return null;
		}
	}

	/**
	 * Encodes string to requested format. Returns an empty string on failure.
	 *
	 * @param   string  $s_string   The data
	 * @param   string  $s_encoding The encoding (base64|7bit|8bit|binary), default base64
	 * @return  string  The encoded text
	 */
	public function encodeString($s_string, $s_encoding = "base64") {
		$s_encoded = '';
		switch( strtolower($s_encoding) ){
			case 'base64':
				$s_encoded = chunk_split(base64_encode($s_string), 76, PHP_EOL);
				break;
			case "7bit":
			case "8bit":
				$s_encoded = $this->FixEOL($s_string);
				if(substr($s_encoded, -(strlen(PHP_EOL))) != PHP_EOL)
				$s_encoded .= PHP_EOL;
				break;
			case "binary":
				$s_encoded = $s_string;
				break;
			case "quoted-printable":
				$s_encoded = $this->encodeQP($s_string);
				break;
		}
		return $s_encoded;
	}


	/**
	 * Changes every end of line from CR or LF to CRLF.
	 *
	 * @param   string  $s_message  The text
	 * @return string   The changed text
	 */
	private function FixEOL($s_message) {
		$s_message = str_replace("\r\n", "\n", $s_message);
		$s_message = str_replace("\r", "\n", $s_message);
		$s_message = str_replace("\n", PHP_EOL, $s_message);
		return $s_message;
	}

	/**
	 * Encode string to quoted-printable.
	 *
	 * @param   string  $s_string   The string
	 * @return  string  The encoded string
	 */
	private function encodeQP ($s_string) {
		$s_encoded = $this->FixEOL($s_string);
		if (substr($s_encoded, -(strlen(PHP_EOL))) != PHP_EOL)
		$s_encoded .= PHP_EOL;

		// Replace every high ascii, control and = characters
		$s_encoded = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $s_encoded);
		// Replace every spaces and tabs when it's the last character on a line
		$s_encoded = preg_replace("/([\011\040])".$s_encoded."/e",
                  "'='.sprintf('%02X', ord('\\1')).'".PHP_EOL."'", $s_encoded);

		// Maximum line length of 76 characters before CRLF (74 + space + '=')
		$s_encoded = $this->wrapText($s_encoded, 74, true);

		return $s_encoded;
	}

	/**
	 * Wraps message for use with mailers that do not automatically perform wrapping
	 * Original written by philippe.
	 *
	 * @param   string  $s_message  The message
	 * @param   int     $i_length   The wrap-length
	 * @param   boolean $qp_mode    The qp mode
	 * @return  string  The wrapped message
	 */
	public function wrapText($s_message, $i_length, $qp_mode = false) {
		$s_soft_break = ($qp_mode) ? sprintf(" =%s", PHP_EOL) : PHP_EOL;

		$s_message = $this->FixEOL($message);
		if( substr($s_message, -1) == PHP_EOL )
		$s_message = substr($s_message, 0, -1);

		$a_lines = explode(PHP_EOL, $s_message);
		$s_message = "";
		foreach($a_lines AS $s_line){
			$a_line_part = explode(" ", $s_line);
			$s_buf = "";
			foreach($a_line_part AS $s_line_part){
				$s_word = $s_line_part;
				if( $qp_mode && (strlen($s_word) > $i_length) ){
					/* QP wrapping */
					$i_space_left = $i_length - strlen($s_buf) - 1;
					if( $s_buf != ''){
						if( $i_space_left > 20 ){
							$i_len = $i_space_left;
							if( substr($s_word, $i_len - 1, 1) == "=" ){
								$i_len--;
							}
							else if(substr($s_word, $i_len - 2, 1) == "=" ){
								$i_len -= 2;
							}
							$s_part     = substr($s_word, 0, $i_len);
							$s_word     = substr($s_word, $i_len);
							$s_buf      .= " " . $s_part;
							$s_message  .= $s_buf . sprintf("=%s", PHP_EOL);
						}
						else{
							$s_message .= $s_buf . $s_soft_break;
						}
						$s_buf = "";
					}
					while( strlen($s_word) > 0 ){
						$i_len = $i_length;
						if( substr($s_word, $i_len - 1, 1) == "=" ){
							$i_len--;
						}
						else if( substr($s_word, $i_len - 2, 1) == "=" ){
							$i_len -= 2;
						}
						$s_part = substr($s_word, 0, $i_len);
						$s_word = substr($s_word, $i_len);

						if( strlen($s_word) > 0)
						$s_message .= $s_part . sprintf("=%s", PHP_EOL);
						else
						$s_buf = $s_part;
					}
				}
				else {
					/* Normal wrapping */
					$s_buf_o = $s_buf;
					$s_buf .= ($s_buf == '' ) ? $s_word : (" " . $s_word);

					if (strlen($s_buf) > $i_length and $s_buf_o != "" ){
						$s_message .= $s_buf_o . $s_soft_break;
						$s_buf = $s_word;
					}
				}
			}
			$s_message .= $s_buf . PHP_EOL;
		}

		return $s_message;
	}
}
?>
