<?php
/**
 * FTP abstraction layer
 * Handles S-FTP, FTP and FTP-S connections
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2010
 * @changed   		18/01/2013
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
class Service_FTP extends Service {
	private $s_type;
	private $obj_con;
	private $obj_stream;
	private $i_timeout  = 3;

	/**
	 * Connects with the preset connection data
	 */
	public function defaultConnect(){
		$service_XmlSettings	= Memory::services('XmlSettings');
			
		$s_username	= $service_XmlSettings->get('settings/ftp/username');
		$s_password	= $service_XmlSettings->get('settings/ftp/password');
		$s_host	= $service_XmlSettings->get('settings/ftp/host');
		$i_port	= $service_XmlSettings->get('settings/ftp/port');
		$s_type	= $service_XmlSettings->get('settings/ftp/type');
			
		$this->connectFTP($s_username,$s_password,$s_host,$i_port,$s_type);
	}

	/**
	 * Destructor
	 */
	public function __destruct(){
		if( !is_null($this->obj_con) ){
			$this->closeFTP($this->obj_con, $this->s_type);
			$this->obj_con	= null;
			$this->obj_stream	= null;
		}
	}

	/**
	 * Connects the DAL with the given data
	 *
	 * @param string	$s_username		The username
	 * @param string	$s_password		The password
	 * @param string	$s_host			The host
	 * @param int		$i_port			The port, default 21
	 * @param string	$s_type			The type (S-FTP|FTP|FTP-S), default FTP
	 */
	public function connectFTP($s_username,$s_password,$s_host,$i_port = 21,$s_type = 'FTP'){
		$this->obj_con	= $this->connect($s_username, $s_password, $s_host,$i_port,$s_type);
		$this->s_type	= $s_type;
	}

	/**
	 * Connects with the given data
	 *
	 * @param string	$s_username		The username
	 * @param string	$s_password		The password
	 * @param string	$s_host			The host
	 * @param int		$i_port			The port, default 21
	 * @param string	$s_type			The type (S-FTP|FTP|FTP-S), default FTP
	 * @return	Resource		The connection resource
	 */
	private function connect($s_username,$s_password,$s_host,$i_port = 21,$s_type = 'FTP'){
		$this->s_type	= $s_type;

		switch($s_type){
			case 'FTP' :
				$obj_connection = @ftp_connect($s_host,"'".$i_port."'",$this->i_timeout);
				if( $obj_connection == false )  throw new Exception("Unable to open a FTP connection to ".$s_host." on port ".$i_port);

				$this->loginFTP($obj_connection,$s_username,$s_password);
				break;

			case 'FTP-S' :
				$obj_connection = @ftp_ssl_connect($s_host,"'".$i_port."'",$this->i_timeout);
				if( $obj_connection == false )  throw new Exception("Unable to open a FTP-S connection to ".$s_host." on port ".$i_port);
					
				$this->loginFTP($obj_connection, $s_username, $s_password);
				break;

			case 'S-FTP' :
				$obj_connection = ssh2_connect($s_host,$i_port);
				if( $obj_connection == false )  throw new Exception("Unable to open a S-FTP connection to ".$s_host." on port ".$i_port);
				/* Should implement fingerprint and public key later on */
				$this->loginFTP($obj_connection, $s_username, $s_password);

				$this->obj_stream	= ssh2_sftp($obj_connection);
				break;
		}

		return $obj_connection;
	}

	/**
	 * Checks the connection with the given data
	 *
	 * @param string	$s_username		The username
	 * @param string	$s_password		The password
	 * @param string	$s_host			The host
	 * @param int		$i_port			The port, default 21
	 * @param string	$s_type			The type (S-FTP|FTP|FTP-S), default FTP
	 * @return boolean	True if the data is correct
	 */
	public function checkLogin($s_username,$s_password,$s_host,$i_port = 21,$s_type = 'FTP'){
		try {
			$con    = $this->connect($s_username,$s_password,$s_host,$i_port,$s_type);
			$this->closeFTP($con,$s_type);

			return true;
		}
		catch(Exception $e){
			return false;
		}
	}

	/**
	 * Performs a login
	 *
	 * @param Resource	$obj_connection	The connection resource
	 * @param string	$s_username		The username
	 * @param string	$s_password		The password
	 * @throws Exception	If the username or password is incorrect
	 */
	private function loginFTP($obj_connection,$s_username,$s_password){
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				if( !@ftp_login($obj_connection, $s_username, $s_password) ){
					throw new Exception("Invalid username or password for FTP connection");
				}
				break;
				 
			case 'S-FTP' :
				if( !ssh2_auth_password($obj_connection, $s_username, $s_password) ){
					throw new Exception("Invalid username or password for S-FTP connection");
				}
				break;
		}
	}

	/**
	 * Closes the FTP connection
	 *
	 * @param Resource	$obj_connection	The connection resource
	 * @param string	$s_type			The type (S-FTP|FTP|FTP-S)
	 * @throws Exception	If the connection was allready closed
	 */
	private function closeFTP($obj_connection,$s_type){
		if( $obj_connection == null ){
			throw new Exception("Unable to close connection. Allready closed?");
		}

		switch($s_type){
			case 'FTP' :
			case 'FTP-S' :
				if( !ftp_close($obj_connection) ){
					throw new Exception("Unable to close connection. Allready closed?");
				}
				break;
					
			case 'S-FTP' :
				ssh2_exec($this->obj_con,'exit');
				break;
		}
	}

	public function cd($s_directory){
		if( !@ftp_chdir($this->obj_con,$s_directory) ){
			return false;
		}

		return true;
	}

	public function getChangedDate($s_file){
		return ftp_mdtm($this->obj_con,"'".$s_file."'");
	}

	/**
	 * Puts a local file on the server
	 *
	 * @param string	$s_filename				The remote file path
	 * @param string	$s_localFilename		The local file path
	 * @param boolean	$bo_binairy				Set to true for binairy transfer
	 * @throws IOException	If the local file is not readable
	 * @return boolean		True if the file is uploaded
	 */
	public function put($s_filename,$s_localFilename,$bo_binairy = false,$i_permissions = 0644){
		if( !$bo_binairy)
			$mode	= FTP_ASCII;
		else
			$mode	=  FTP_BINARY;
			
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				if( !file_exists($s_localFilename) ){
					throw new IOException('Local file '.$s_localFilename.' does not exist!');
				}

				if( !is_readable($s_localFilename) ){
					throw new IOException('Can not read local file '.$s_localFilename.'. Check the permissions');
				}

				$file	= fopen($s_localFilename,'r');
				$s_remoteDir	= dirname($s_filename);
				$s_remoteFile	= basename($s_filename);
					
				if( !@ftp_chdir($this->obj_con,$s_remoteDir) ){
					$this->newDirectory($s_remoteDir);
					@ftp_chdir($this->obj_con,$s_remoteDir);
				}
				$bo_result	= ftp_fput($this->obj_con, $s_remoteFile, $file, $mode);
					
				fclose($file);
				break;
					
			case 'S-FTP' :
				$bo_result = ssh2_scp_send($this->obj_con,$s_localFilename,$s_filename);				
				break;
		}
			
		return $bo_result;
	}

	/**
	 * Downloads a local file from the server
	 *
	 * @param string	$s_filename				The remote file path
	 * @param string	$s_localFilename		The local file path
	 * @param boolean	$bo_binairy				Set to true for binairy transfer
	 * @throws IOException	If the local directory is not writable
	 * @return boolean		True if the file is downloaded
	 */
	public function get($s_filename,$s_localFilename,$bo_binairy = false){
		if( !$bo_binairy)
		$mode	= FTP_ASCII;
		else
		$mode	=  FTP_BINARY;
			
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				$s_dir	= dirname($s_localFilename);
				if( !is_writable($s_dir) ){
					throw new IOException("Can not write in directory ".$s_dir.". Check the permissions");
				}
					
				$bo_result	= ftp_get($this->obj_con, $s_localFilename, $s_filename, $mode);
				break;
					
			case 'S-FTP' :
				$bo_result = ssh2_scp_recv($this->obj_con,$s_filename,$s_localFilename);
				break;
		}
			
		return $bo_result;
	}

	/**
	 * Deletes the given file on the server
	 *
	 * @param string $s_filename	The remote file path
	 * @return boolean	True if the file is deleted
	 */
	public function delete($s_filename){
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				$bo_result	= ftp_delete($this->obj_con,$s_filename);
				break;
					
			case 'S-FTP' :
				$bo_result = ssh2_sftp_unlink($this->obj_stream,$s_filename);
				break;
		}
			
		return $bo_result;
	}

	/**
	 * Creates a new directory on the server
	 *
	 * @param string $s_name	The remote directory path
	 * @param octal  $i_mode	The permissions, default 755
	 * @return boolean	True if the directory is created
	 */
	public function newDirectory($s_name,$i_mode = 0755){
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				$a_dirs	= explode('/',$s_name);
				$s_path = "";
				$bo_result = true;
					
				foreach($a_dirs AS $s_dir){
					if( !empty($s_path) )	$s_path .= '/';

					$s_path += $s_dir;
					if( !@ftp_chdir($this->obj_con,$s_path) ){
						$s_name	= ftp_mkdir($this->obj_con,$s_path);
						if($s_name === false){
							$bo_result = false;
							break;
						}
							
						ftp_chdir($this->obj_con,$s_path);
					}
				}

				ftp_chmod($this->obj_con,$i_mode,$s_name);

				break;
					
			case 'S-FTP' :
				$bo_result	= ssh2_sftp_mkdir($this->obj_stream,$s_name,$i_mode,true);
				if( $bo_result ){
					$this->chmod($s_name,$i_mode);
				}

				break;
		}

		return $bo_result;
	}

	/**
	 * Sets the given permissions on the given file
	 * 
	 * @param string $s_file		The file path
	 * @param octal $i_rights		The permissions, default 644
	 */
	public function chmod($s_file,$i_rights = 644){
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				ftp_chmod($this->obj_con,$i_mode,$s_file);
				break;
					
			case 'S-FTP':
				ssh2_sftp_chmod($this->obj_stream,$s_file,$i_rights);
				break;
		}
	}

	/**
	 * Returns the last modified date as a timestamp
	 * 
	 * @param string $s_file		The file path
	 * @return int	The date
	 */
	public function getLastModified($s_file){
		switch($this->s_type){
			case 'FTP':
			case 'FTP-S':
				return ftp_mdtm($this->obj_con,'"'.$s_file.'"');


			case 'S-FTP':
				$a_info	= ssh2_sftp_stat($this->obj_stream,'"'.$s_file.'"');
				return $a_info['mtime'];
		}
	}

	/**
	 * Reads the given directory
	 * 
	 * @param string $s_directory		The directory path
	 * @return	array	The files
	 * @throws IOException	If the remote directory can not be accesed
	 */
	public function readDirectory($s_directory){
		switch($this->s_type){
			case 'FTP':
			case 'FTP-S':
				return ftp_nlist($this->obj_con,'"'.$s_directory.'"');

			case 'S-FTP':
				$a_files = array();

				$handle = opendir("ssh2.sftp://".$this->obj_stream.":/$s_directory");
				if( $handle === false ){
					throw new IOException('Can not open remote FTP directory '.$s_directory.'.');
				}

				while( false !== ($s_file = readdir($handle)) ){
					if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) == "~") )  continue;

					$a_files[] = $s_file;
				}

				closedir($handle);
				return $a_files;
		}
	}

	/**
	 * Deletes the directory on the server
	 *
	 * @param string $s_name	The remote directory path
	 * @return boolean	True if the directory is deleted
	 */
	public function deleteDirectory($s_name){
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				$bo_result	= ftp_rmdir($this->obj_con,'"'.$s_name.'"');
				break;
					
			case 'S-FTP' :
				$bo_result	= ssh2_sftp_rmdir($this->obj_stream,'"'.$s_name.'"');
				break;
		}

		return $bo_result;
	}

	/**
	 * Renames the file or directory on the server
	 *
	 * @param string $s_currentName		The remote path
	 * @param string $s_newName			The new remote path
	 * @return boolean	True if the file or directory is renamed
	 */
	public function rename($s_currentName,$s_newName){
		switch($this->s_type){
			case 'FTP' :
			case 'FTP-S' :
				$bo_result	= ftp_rename($this->obj_con,$s_currentName,$s_newName);
				break;
					
			case 'S-FTP' :
				$bo_result = ssh2_sftp_rename($this->obj_stream,$s_currentName,$s_newName);
				break;
		}
			
		return $bo_result;
	}
}
?>