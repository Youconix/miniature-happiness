<?php
/**
 * Service class for handling and manipulating cookies
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2006
 * @changed   		03/03/2010
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
class Service_Cookie extends Service {
	private $service_Security;

	/**
	 * PHP5 constructor
	 */
	public function __construct(){
		$this->service_Security = Memory::services('Security');
	}

	/**
	 * Destructor
	 */
	public function __destruct(){
		$this->service_Security = null;
	}

	/**
	 * Encrypts the given string with base64_encode
	 *
	 * @param      string  $s_cookieData   The data that needs to be encrypted
	 * @return		string  The encrypted data
	 */
	private function encrypt($s_cookieData){
		$s_cookieData = base64_encode($s_cookieData);

		return $s_cookieData;
	}

	/**
	 * Decrypts the given string with base64_decode
	 *
	 * @param      string  $s_cookieData   The data that needs to be decrypted
	 * @return		string  The decrypted data
	 */
	private function decrypt($s_cookieData){
		$s_cookieData = base64_decode($s_cookieData);

		$s_cookieData = $this->service_Security->secureString($s_cookieData);

		return $s_cookieData;
	}

	/**
	 * Deletes the cookie with the given name and domain
	 *
	 * @param      string  $s_cookieName       The name of the cookie
	 * @param      string  $s_domain           The domain of the cookie
	 * @throws		Exception if the cookie does not exist
	 */
	public function delete($s_cookieName,$s_domain){
		Memory::type('string',$s_cookieName);
		Memory::type('string',$s_domain);

		if( !$this->exists($s_cookieName) ){
			throw new Exception("Cookie ".$s_cookieName." does not exist.");
		}

		setcookie($s_cookieName, "", time()-3600,$s_domain);
		if( isset($_COOKIE[$s_cookieName]) ){
			unset($_COOKIE[$s_cookieName]);
		}
	}

	/**
	 * Sets the cookie with the given name and data
	 *
	 * @param      string  $s_cookieName   The name of the cookie
	 * @param      string  $s_cookieData   The data to put into the cookie
	 * @param      string  $s_domain       The domain the cookie schould work on, default /
	 * @param      string  $s_url          The URL the cookie schould work on, optional
	 * @param      int     $i_secure       1 if the cookie schould be https-only otherwise 0, optional
	 * @return		boolean True if the cookie has been set, false if it has not
	 */
	public function set($s_cookieName, $s_cookieData,$s_domain,$s_url = "",$i_secure = 0){
		Memory::type('string',$s_cookieName);
		Memory::type('string',$s_cookieData);
		Memory::type('string',$s_domain);
		Memory::type('string',$s_url);
		Memory::type('int',$i_secure);

		$s_cookieData = $this->encrypt($s_cookieData);
		$_COOKIE[$s_cookieName] = $s_cookieData;

		if( setcookie($s_cookieName, $s_cookieData,time()+2592000,$s_domain,$s_url,$i_secure) ){
			return true;
		}
		else{
			if( !defined('DEBUG') )		unset($_COOKIE[$s_cookieName]);
			return false;
		}
	}

	/**
	 * Receives the content from the cookie with the given name
	 *
	 * @param      string  $s_cookieName   The name of the cookie
	 * @return		string  The requested cookie
	 * @throws     Exception if the cookie does not exist
	 */
	public function get($s_cookieName){
		$s_cookie = null;

		Memory::type('string',$s_cookieName);

		if($this->exists($s_cookieName) == 0){
			throw new Exception("Cookie ".$s_cookieName." does not exist.");
		}

		/* Read cookie */
		$s_cookie = $_COOKIE[$s_cookieName];
		$s_cookie = $this->decrypt($s_cookie);

		return $s_cookie;
	}

	/**
	 * Checks if the given cookie exists
	 *
	 * @param      string  $s_cookieName   The name of the cookie you want to check
	 * @return		boolean True if the cookie exists, false if it does not
	 */
	public function exists($s_cookieName){
		Memory::type('string',$s_cookieName);

		if(!isset($_COOKIE[$s_cookieName])){
			return false;
		}
		else{
			return true;
		}
	}
}
?>

