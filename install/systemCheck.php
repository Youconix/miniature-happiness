<?php
/**
 * System check class
 * This class checks if the server has the framework requirements
 *
 * This file is part of the Scripthulp framework installer
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   09/01/2013
 *
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
class SystemCheck {
	private $s_systemConfig = '';
	private $bo_mysql	= false;
	private $bo_postgres	= false;
	private $bo_valid	= true;
	private $s_valid	= '<span class="Notice">V</span>';
	private $s_inValid	= '<span class="errorNotice">X</span>';

	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		$s_protocol	= 'http://';
		if( $_SERVER['SERVER_PORT'] == 443 )
		$s_protocol	= 'https://';

		$this->s_systemConfig = file_get_contents($s_protocol.$_SERVER['HTTP_HOST'].str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'/serverInfo.php');
	}

	/**
	 * Validates the server
	 */
	public function validate(){
		$s_output	= '<h1>System check</h1>
    	    
    	    <table>
    	    <tr>
    	    	<td>Minimun PHP 5.2</td>
    	    	<td>'.$this->phpVersion().'</td>
    	    </tr>
    	    <tr>
    	    	<td>MySQL 5+</td>
    	    	<td>'.$this->mysql().'</td>
    	    </tr>
    	    <tr>
    	    	<td>Postgres 9+</td>
    	    	<td>'.$this->postgres().'</td>
    	    </tr>
    	    <tr>
    	    	<td>GD</td>
    	    	<td>'.$this->gd().'</td>
    	    </tr>
    	    <tr>
    	    	<td>XML support</td>
    	    	<td>'.$this->xml().'</td>
    	    </tr>
    	    <tr>
    	    	<td>Curl</td>
    	    	<td>'.$this->curl().'</td>
    	    </tr>
    	    <tr>
    	    	<td colspan="2"><br/></td>
    	    </tr>
    	    <tr>
    	    	<td colspan="2">Checking write permissions</td>
    	    </tr>
    	    '.$this->checkData().'
    	    </table>';

		if( !$this->bo_mysql && !$this->bo_postgres )
		$this->bo_valid	= false;
			
		return $s_output;
	}

	/**
	 * Returns if the server is valid
	 * 
	 * @return boolean	True if the server is valid
	 */
	public function isValid(){
		return $this->bo_valid;
	}

	/**
	 *  Checks php version
	 *  
	 *  @return string	The status code
	 */
	private function phpVersion(){
		$s_result	= $this->s_valid;
		$a_version	= explode('.',phpversion());
		if( ($a_version[0] < 5) || ($a_version[0] == 5 && $a_version[1] < 2) ){
			$this->bo_valid	= false;
			$s_result	= $this->s_inValid;
		}

		return $s_result;
	}

	/**
	 * Checks the Mysqli and Mysql support
	 * 
	 * @return string	The status code
	 */
	private function mysql(){
		/* Check mysqli first */
		if( class_exists('mysqli') ){
			$this->bo_mysql	= true;
			return $this->s_valid;
		}
		
		return $this->s_inValid;
	}

	/**
	 * Checks the postgresql support
	 * 
	 * @return string	The status code
	 */
	private function postgres(){
		if( function_exists('pg_connect') ){
			$this->bo_postgres	= true;
			return $this->s_valid;
		}

		return $this->s_inValid;
	}
	
	/**
	 * Checks the PHP GD support
	 * 
	 * @return string	The status code
	 */
	private function gd(){
		if( !function_exists('ImageCreate') ){
			$this->bo_valid	= false;
			return $this->s_inValid;
		}
		
		return $this->s_valid;
	}
	
	/**
	 * Checks the XML support
	 * 
	 * @return string	The status code
	 */
	private function xml(){
		if( !class_exists('DOMDocument') || !class_exists('DOMXPath') ){
			$this->bo_valid	= false;
			return $this->s_inValid;
		}
		
		return $this->s_valid;
	}
	
	/**
	 * Checks the Curl support
	 * 
	 * @return string	The status code
	 */
	private function curl(){
		if( !function_exists('curl_init') ){
			$this->bo_valid	= false;
			return $this->s_inValid;
		}
		
		return $this->s_valid;
	}
	
	private function checkData(){
		$s_data_dir	= DATA_DIR;
		
		$s_output = '<tr>
			<td>'.$s_data_dir.'/logs</td>
			<td>';
		
		if( is_writable($s_data_dir.'/logs') ){
			$s_output .= $this->s_valid;	
		}
		else {
			$s_output .= $this->s_inValid;
			$this->bo_valid	= false;
		}
		$s_output .= '</td>
		</tr>
		<tr>
			<td>'.$s_data_dir.'/settings</td>
			<td>';
		
		if( is_writable($s_data_dir.'/settings') ){
			$s_output .= $this->s_valid;	
		}
		else {
			$s_output .= $this->s_inValid;
			$this->bo_valid	= false;
		}
		
		$s_output .= '</td>
		</tr>';
		
		return $s_output;
	}
}
?>