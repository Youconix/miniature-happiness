<?php

namespace core\models;

/**
 * Config contains the main runtime configuration of the framework.
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 2.0
 * @changed 14/09/2014
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class Config extends Model {
	private $service_Settings;
	private $service_File;
	private $service_Cookie;
	private $s_templateDir;
	private $bo_ajax = false;
	private $s_base;
	private $s_page;
	private $s_protocol;
	private $s_command = 'view';
	
	/**
	 * PHP 5 constructor
	 *
	 * @param core\services\File $service_File The File service
	 * @param core\services\Settings $service_Settings The settings service
	 * @param core\services\Cookie $service_Cookie The cookie service
	 */
	public function __construct( \core\services\File $service_File, \core\services\Settings $service_Settings, \core\services\Cookie $service_Cookie ){
		$this->service_File = $service_File;
		$this->service_Settings = $service_Settings;
		$this->service_Cookie = $service_Cookie;
		
		$this->loadTemplateDir();
		
		$this->setDefaultValues($service_Settings);
	}
	
	/**
	 * Sets the default values
	 *
	 * @param core\services\Settings $service_Settings The settings service
	 */
	private function setDefaultValues( $service_Settings ){
		if( !defined('DB_PREFIX') ){
			define('DB_PREFIX', $service_Settings->get('settings/SQL/prefix'));
		}
		
		$s_base = $service_Settings->get('settings/main/base');
		if( substr($s_base, 0, 1) != '/' ){
			$this->s_base = '/' . $s_base;
		}
		else{
			$this->s_base = $s_base;
		}
		
		if( !defined('BASE') ){
			define('BASE', NIV);
		}
		
		/* Get page */
		$s_page = $_SERVER['SCRIPT_NAME'];		
		while( substr($s_page, 0, 1) == '/' ){
			$s_page = substr($s_page, 1);
		}

		if( stripos($s_page, $s_base) !== false ){
			$s_page = substr($s_page, strlen($s_base));
		}
		
		while( substr($s_page, 0, 1) == '/' ){
			$s_page = substr($s_page, 1);
		}
		$this->s_page = $s_page;
		
		/* Get protocol */
		$this->s_protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
		
		$this->detectAjax();		
		
		if( !defined('LEVEL') ){
			define('LEVEL', '/');
		}
		
		$this->s_command = 'index';
		if( isset($_GET['command']) ){
			$this->s_command = $_GET['command'];
		}
		else if( isset($_POST['command']) ){
			$this->s_command = $_POST['command'];
		}
	}
	
	/**
	 * Detects an AJAX call
	 */
	private function detectAjax(){
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
			$this->bo_ajax = true;
		}
		else if( function_exists('apache_request_headers') ){
			$a_headers = apache_request_headers();
			$this->bo_ajax = (isset($a_headers['X-Requested-With']) && $a_headers['X-Requested-With'] == 'XMLHttpRequest');
		} 
		else if( (isset($_GET['AJAX']) && $_GET['AJAX'] == 'true') || (isset($_POST['AJAX']) && $_POST['AJAX'] == 'true') ){
			$this->bo_ajax = true;
		}
	}
	
	/**
	 * Loads the template directory
	 */
	private function loadTemplateDir(){
		$s_templateDir = $this->service_Settings->get('settings/templates/dir');
		
		if( isset($_GET['private_style_dir']) ){
			$s_styleDir = $this->clearLocation($_GET['private_style_dir']);
			if( $this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts') ){
				$s_templateDir = $s_styleDir;
				$this->service_Cookie->set('private_style_dir', $s_templateDir, '/');
			}
			else if( $this->service_Cookie->exists('private_style_dir') ){
				$this->service_Cookie->delete('private_style_dir', '/');
			}
		}
		else if( $this->service_Cookie->exists('private_style_dir') ){
			$s_styleDir = $this->clearLocation($this->service_Cookie->get('private_style_dir'));
			if( $this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts') ){
				$s_templateDir = $s_styleDir;
				$this->service_Cookie->set('private_style_dir', $s_templateDir, '/');
			}
			else{
				$this->service_Cookie->delete('private_style_dir', '/');
			}
		}
		$this->s_templateDir = $s_templateDir;
	}
	
	/**
	 * Clears the location path from evil input
	 *
	 * @param String $s_location
	 * @return String path
	 */
	private function clearLocation( $s_location ){
		while( (strpos($s_location, './') !== false) || (strpos($s_location, '../') !== false) ){
			$s_location = str_replace(array( 
					'./',
					'../' 
			), array( 
					'',
					'' 
			), $s_location);
		}
		
		return $s_location;
	}
	
	/**
	 * Returns the template directory
	 *
	 * @return String The template directory
	 */
	public function getTemplateDir(){
		return $this->s_templateDir;
	}
	
	/**
	 * Returns the loaded template directory
	 *
	 * @return String template directory
	 */
	public function getStylesDir(){
		return 'styles/' . $this->s_templateDir . '/';
	}
	
	/**
	 * Returns the used protocol
	 *
	 * @return String protocol
	 */
	public function getProtocol(){
		return $this->s_protocol;
	}
	
	/**
	 * Returns the current page
	 *
	 * @return String page
	 */
	public function getPage(){
		return $this->s_page;
	}
	
	/**
	 * Checks if ajax-mode is active
	 *
	 * @return boolean if ajax-mode is active
	 */
	public function isAjax(){
		return $this->bo_ajax;
	}
	
	/**
	 * Sets the framework in ajax-
	 *
	 * @deprecated since version 2
	 */
	public function setAjax(){
		$this->bo_ajax = true;
	}
	
	/**
	 * Returns the request command
	 *
	 * @return String The command
	 */
	public function getCommand(){
		return $this->s_command;
	}
	
	/**
	 * Returns the path to the website root
	 * This value gets set in {LEVEL}
	 * 
	 * @return String	The path
	 */
	public function getBase(){
		return $this->s_base;
	}
	
	public function getLoginRedirect(){
	 $s_page = $this->getBase().'index/view';
	 
	 if( $this->service_Settings->exists('main/login') ){
	  $s_page = $this->getBase().$this->service_Settings->get('main/login');
	 }
	 
	 return $s_page;
	}
	
	public function getLogoutRedirect(){
	 $s_page = $this->getBase().'index/view';
	
	 if( $this->service_Settings->exists('main/logout') ){
	  $s_page = $this->getBase().$this->service_Settings->get('main/logout');
	 }
	
	 return $s_page;
	}
	
	public function getRegistrationRedirect(){
	 $s_page = $this->getBase().'index/view';
	
	 if( $this->service_Settings->exists('main/registration') ){
	  $s_page = $this->getBase().$this->service_Settings->get('main/registration');
	 }
	
	 return $s_page;
	}
	
	/**
	 * Returns if the normal login is activated
	 * 
	 * @return boolean True if the normal login is activated
	 */
	public function isNormalLogin(){
	 if( !$this->service_Settings->exists('login/normalLogin') || $this->service_Settings->get('login/normalLogin') != 1 ){
	  return false;
	 }
	 return true;
	}
	
	/**
	 * Returns if the facebook login is activated
	 *
	 * @return boolean True if the facebook login is activated
	 */
	public function isFacebookLogin(){
	 if( !$this->service_Settings->exists('login/facebook') || $this->service_Settings->get('login/facebook') != 1 ){
	  return false;
	 }
	 return true;
	}
	
	/**
	 * Returns if the openOD login is activated
	 *
	 * @return boolean True if the openID login is activated
	 */
	public function isOpenIDLogin(){
	 if( !$this->service_Settings->exists('login/openID') || $this->service_Settings->get('login/openID') != 1 ){
	  return false;
	 }
	 return true;
	}
	
	/**
	 * Returns if the LDAP login is activated
	 *
	 * @return boolean True if the LDAP login is activated
	 */
	public function isLDAPLogin(){
	 if( !$this->service_Settings->exists('login/ldap') || $this->service_Settings->get('login/ldap') != 1 ){
	  return false;
	 }
	 return true;
	}
}

?>
