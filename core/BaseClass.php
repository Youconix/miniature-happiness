<?php
namespace core;

/**
 * Base class for the framework.
 * Use this file as parent
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 * @date			12/01/2006
 * @changed 16/05/2012
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
abstract class BaseClass {
	protected $model_Config;
	protected $service_Language;
	protected $service_ErrorHandler;
	protected $service_Security;
	protected $service_Template;
	protected $init_post = array();
	protected $init_get = array();
	protected $init_request = array();
	protected $post = array();
	protected $get = array();
	protected $request = array();
	
	/**
	 * Destructor
	 */
	public function __destruct(){
		$this->model_Config = null;
		$this->service_Language = null;
		$this->service_ErrorHandler = null;
		$this->service_Template = null;
		$this->service_Security = null;
		
		$this->init_post = null;
		$this->init_get = null;
		$this->init_request = null;
		$this->post = null;
		$this->get = null;
		$this->request = null;
		
		if( class_exists('\core\Memory') ){
			Memory::endProgram();
		}
	}
	
	/**
	 * Inits the class BaseClass
	 *
	 * @throws Exception If loading from the framework failes
	 */
	protected function init(){
		require_once (NIV . 'core/Memory.php');
		Memory::startUp();
		
		$this->model_Config	= Memory::models('Config');
		$this->service_Language = Memory::services('Language');
		$this->service_ErrorHandler = Memory::services('ErrorHandler');
		$this->service_Security = Memory::services('Security');
		
		/* Check login */
		Memory::models('Privileges')->checkLogin();
		
		if( !defined("PROCESS") ){
			$this->service_Template = Memory::services('Template');
			
			$s_language = Memory::services('Language')->getLanguage();
			$this->service_Template->headerLink('<script src="{NIV}js/site.php?lang=' . $s_language . '" type="text/javascript"></script>');
			
			if( !Memory::isAjax() ){
				$this->loadView();
			}
			
			/* Call statistics */
			if( !Memory::isAjax() && stripos($_SERVER['PHP_SELF'], 'admin/') === false )
				require (NIV . 'stats/statsView.php');
		}
		
		/* Secure input */
		$this->get = $this->service_Security->secureInput('GET', $this->init_get);
		$this->post = $this->service_Security->secureInput('POST', $this->init_post);
		$this->request = $this->service_Security->secureInput('REQUEST', $this->init_request);
	}
	
	/**
	 * Forces the use of SSL/TSL for this page
	 *
	 * Disabled at default install
	 */
	protected function forceSSL(){
		return;
		/* Check SSL/TSL */
		if( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ){
			/* SSL do nothing */
		}
		else{
			/* force https */
			header('location: https://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']);
			exit();
		}
	}
	
	/**
	 * Loads the view
	 */
	protected function loadView(){
		try{
			/* Set language and encoding */
			$this->service_Template->set('lang', $this->service_Language->getLanguage());
			$this->service_Template->set('encoding', $this->service_Language->getEncoding());
			if( $this->service_Language->exists('title') ){
				$this->service_Template->set('mainTitle', $this->service_Language->get('title') . ',  ');
			}
		}
		catch( Exception $s_error ){
			$this->service_ErrorHandler->error($s_error);
			
			$this->throwError($s_error);
		}
	}
	
	/**
	 * API for reporting errors
	 *
	 * @param Exception $obj_notice The Exception object
	 * @param boolean $bo_fatal True if the framework should stop executing
	 */
	protected function throwError( Exception $obj_notice, $bo_fatal = false ){
		/* Display error */
		header('HTTP/1.1 500 Internal Server Error');
		
		echo ('<?xml version="1.0" encoding="UTF-8"?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<title>500 Internal Server Error</title>
				</head>
				<body>
				<h2 style="color:red; font-size:25px;">' . $obj_notice->getMessage() . '</h2>
				</body>
				</html>');
		die();
	}
	
	/**
	 * Formats the given value
	 *
	 * @param int $i_value unformatted value
	 * @param int $i_decimals number of decimals, default 0
	 * @return string formatted value
	 */
	protected function format( $i_value, $i_decimals = 0 ){
		if( $i_value < 10000 )
			return $i_value;
		
		return number_format($i_value, $i_decimals, ',', '.');
	}
}
/* Set error catcher */
function exception_handler( $exception ){
	$s_exception = $exception->getMessage() . '
			' . $exception->getTraceAsString();
	
	if( class_exists('Memory') ){
		if( !defined('Process') && Memory::isLoaded('service', 'Template') ){
			/* Disable output */
			Memory::delete('service', 'Template');
		}
		
		try{
			Memory::services('Logs')->errorLog($s_exception);
		}
		catch( \Exception $e ){
		}
	}
	
	if( defined('DEBUG') ){
		header('HTTP/1.1 500 Internal Server Error');
		echo ('<!DOCTYPE html>
		<html>
		<head>
			<title>500 Internal Server Error</title>
		</head>
		<body>
		<section id="container">
  			<h1>500 Internal Server Error</h1>
  			
  			<h3>Whoops something went wrong</h3>
  			
  			<h5>Whoops? What whoops?<br/> Computer deactivate the [fill in what you want]</h5>
  			
		    <p>' . nl2br($exception->__toString()) . '</p>
		</section>
		</body>
		</html>
  	');
		exit();
	}
	
	if( stripos($_SERVER["SCRIPT_NAME"], 'errors/500.php') === false ){
		//header('location: ' . NIV . 'errors/500.php');
		//exit();
	}
}

set_exception_handler('\core\exception_handler');
?>
