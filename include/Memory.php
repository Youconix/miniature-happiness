<?php

namespace core;

/**
 * Memory-handler for controlling memory and autostarting the framework
 *
 * This file is part of Scripthulp framework
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
class Memory{

  private static $bo_ajax;
  private static $a_memory = null;
  private static $s_base;
  private static $s_page;
  private static $s_protocol;
  private static $bo_testing = false;
  private static $s_servicePath;
  private static $s_modelPath;
  private static $s_helperPath;
  private static $s_classPath;
  private static $s_interfacePath;

  /**
   * Destructor
   */
  public function __destruct(){
    Memory::reset();
  }

  /**
   * Starts the framework in testing mode.
   * DO NOT USE THIS IN PRODUCTION
   */
  public static function setTesting(){
    Memory::$bo_testing = true;
    if( !defined('DEBUG') ){
      define('DEBUG', 'true');
    }

    if( !defined('PROCESS') ){
      define('PROCESS', 1);
    }

    Memory::startUp();

    error_reporting(E_ERROR);
  }

  /**
   * Starts the framework
   */
  public static function startUp(){
    if( !is_null(Memory::$a_memory) ){
      return;
    }

    if( !defined('DATA_DIR') ){
      if( Memory::$bo_testing ){
        define('DATA_DIR', NIV . 'admin/data/tests/');
      }
      else {
        define('DATA_DIR', NIV . 'admin/data/');
      }
    }

    /* Prepare cache */
    Memory::$a_memory = array();
    Memory::$a_memory[ 'service' ] = array();
    Memory::$a_memory[ 'model' ] = array();
    Memory::$a_memory[ 'helper' ] = array();
    Memory::$bo_ajax = false;

    Memory::$s_servicePath = NIV . 'include/services/';
    Memory::$s_modelPath = NIV . 'include/models/';
    Memory::$s_helperPath = NIV . 'include/helpers/';
    Memory::$s_classPath = NIV . 'include/classes/';
    Memory::$s_interfacePath = NIV . 'include/interface/';

    require_once(Memory::$s_servicePath . 'Service.inc.php');
    require_once(Memory::$s_modelPath . 'Model.inc.php');
    require_once(Memory::$s_helperPath . 'Helper.inc.php');

    /* Load standard services */
    require_once(Memory::$s_servicePath . 'File.inc.php');
    $service_File = new \core\services\File();
    Memory::$a_memory[ 'service' ][ 'File' ] = $service_File;

    require_once(Memory::$s_servicePath . 'Settings.inc.php');
    $service_Settings = new \core\services\Settings();
    Memory::$a_memory[ 'service' ][ 'XmlSettings' ] = $service_Settings;
    Memory::$a_memory[ 'service' ][ 'Settings' ] = $service_Settings;

    date_default_timezone_set($service_Settings->get('settings/main/timeZone'));
    
    require_once(Memory::$s_servicePath . 'FileData.inc.php');
    $service_FileData = new \core\services\FileData();
    Memory::$a_memory[ 'service' ][ 'FileData' ] = $service_FileData;

    require_once(Memory::$s_servicePath . 'Logs.inc.php');
    $service_Logs = new \core\services\Logs($service_File,$service_FileData);
    Memory::$a_memory[ 'service' ][ 'Logs' ] = $service_Logs;
    
    require_once(Memory::$s_servicePath.'Session.inc.php');

    if( !defined('DB_PREFIX') ){
      define('DB_PREFIX', $service_Settings->get('settings/SQL/prefix'));
    }

    $s_base = $service_Settings->get('settings/main/base');
    if( substr($s_base, 0, 1) != '/' ){
      Memory::$s_base = '/' . $s_base;
    }
    else {
      Memory::$s_base = $s_base;
    }

    if( !defined('BASE') ){
      define('BASE', NIV);
    }

    /* Get page */
    $s_page = $_SERVER[ 'SCRIPT_NAME' ];

    while( substr($s_page, 0, 1) == '/' ){
      $s_page = substr($s_page, 1);
    }
    Memory::$s_page = $s_page;

    if( $s_base == '/' ){
      Memory::$s_page = substr(Memory::$s_page, 1);
    }
    else if( stripos($s_page, $s_base) !== false ){
      Memory::$s_page = substr(Memory::$s_page, strlen($s_base));
    }

    /* Get protocol */
    Memory::$s_protocol = ((!empty($_SERVER[ 'HTTPS' ]) && $_SERVER[ 'HTTPS' ] != 'off') || (isset($_SERVER[ 'SERVER_PORT' ]) && $_SERVER[ 'SERVER_PORT' ] == 443) ) ? "https://" : "http://";

    if( defined('DEBUG') ){
      ini_set('display_errors', 'on');
    }
    else {
      ini_set('display_errors', 'off');
    }

    error_reporting(E_ALL);
  }

  /**
   * Returns the used protocol
   *
   * @return String	The protocol
   */
  public static function getProtocol(){
    return Memory::$s_protocol;
  }

  /**
   * Returns the current page
   *
   * @return String	The page
   */
  public static function getPage(){
    return Memory::$s_page;
  }

  /**
   * Checks if ajax-mode is active
   *
   * @return boolean	True if ajax-mode is active
   */
  public static function isAjax(){
    return Memory::$bo_ajax;
  }

  /**
   * Sets the framework in ajax-mode
   */
  public static function setAjax(){
    Memory::$bo_ajax = true;
  }

  /**
   * Checks if testing-mode is active 
   *
   * return boolean	True if testing-mode is active
   */
  public static function isTesting(){
    return Memory::$bo_testing;
  }

  /**
   * Returns the base directory
   *
   * @return String	The directory
   */
  public static function getBase(){
    return Memory::$s_base;
  }

  /**
   * Ensures that the given class is loaded
   *
   * @param String $s_class		The class name
   * @throws MemoryException		If the class does not exists in include/class/
   */
  public static function ensureClass($s_class){
    if( !class_exists($s_class) ){
      $service_File = Memory::getMemory('service', 'File');
      if( !$service_File->exists(Memory::$s_classPath . $s_class . '.inc.php') ){
        throw new \MemoryException('Can not find class ' . $s_class);
      }

      require_once(Memory::$s_classPath . $s_class . '.inc.php');
    }
  }

  /**
   * Ensures that the given interface is loaded
   *
   * @param String $s_interface		The interface name
   * @throws MemoryException			If the interface does not exists in include/interface/
   */
  public static function ensureInterface($s_interface){
    if( !interface_exists($s_interface) ){
      $service_File = Memory::getMemory('service', 'File');
      if( !$service_File->exists(Memory::$s_interfacePath . $s_interface . '.inc.php') ){
        throw new \MemoryException('Can not find interface ' . $s_interface);
      }

      require_once(Memory::$s_interfacePath . $s_interface . '.inc.php');
    }
  }

  /**
   * Checks or the given service or model allready is loaded
   *
   * @param   String  $s_type  The type (model|service)
   * @param   String  $s_name  The name of the service or model
   * @return  boolean True if the given service or model is allready loaded, otherwise false
   */
  private static function checkMemory($s_type, $s_name){
    if( array_key_exists($s_name, Memory::$a_memory[ $s_type ]) ){
      return true;
    }

    return false;
  }

  /**
   * Returns the requested service or model from the memory
   *
   * @param   String  $s_type  The type (model|service)
   * @param   String  $s_name  The name of the service or model
   * @return  object  The requested service or model
   */
  private static function getMemory($s_type, $s_name){
    return Memory::$a_memory[ $s_type ][ $s_name ];
  }

  /**
   * Saves the given service or model in the memory
   *
   * @param   String  $s_type     The type (model|service)
   * @param   String  $s_name     The name of the service or model
   * @param   object  $obj_value  The service or model
   */
  private static function setMemory($s_type, $s_name, $obj_value){
    Memory::$a_memory[ $s_type ][ $s_name ] = $obj_value;
  }

  /**
   * API for checking or a helper exists
   *
   * @param		String $s_name  The name of the helper
   * @param		bool	$bo_data	 Set to true to use the data directory
   * @return  bool	True if the helper exists, otherwise false
   */
  public static function isHelper($s_name, $bo_data = false){
    $s_name = ucfirst($s_name);

    /* Check or class is in the global memory */
    if( !$bo_data && Memory::checkMemory('helper', $s_name) ){
      return true;
    }

    /* Call class file */
    $service_File = Memory::getMemory('service', 'File');

    /* Check or the file exists */
    if( $bo_data ){
      $s_path = Memory::$s_helperPath . 'data/' . $s_name . '.inc.php';
    }
    else {
      $s_path = Memory::$s_helperPath . $s_name . '.inc.php';
    }
    if( !$service_File->exists($s_path) ){
      return false;
    }

    $s_helper = $service_File->readFile($s_path);
    if( !$bo_data && strpos($s_helper, 'namespace core\models;') !== false ){
      return true;
    }
    if( $bo_data && strpos($s_helper, 'namespace core\models\data;') !== false ){
      return true;
    }
    if( strpos($s_helper, 'class Helper_' . $s_name) !== false ){
      return true;
    }

    return false;
  }

  /**
   * Loads the requested helper
   *
   * @param   String  $s_helper  The name of the helper
   * @param		bool		$bo_data	 Set to true to use the data directory
   * @return  Helper  The requested helper
   * @throws  Exception If the requested helper does not exist
   */
  public static function helpers($s_helper, $bo_data = false){
    $s_helper = ucfirst($s_helper);

    if( !$bo_data && Memory::checkMemory('helper', $s_helper) ){
      return Memory::getMemory('helper', $s_helper);
    }

    /* Check service */
    $service_File = Memory::getMemory('service', 'File');
    if( $bo_data ){
      $s_path = Memory::$s_helperPath . 'data/' . $s_helper . '.inc.php';
    }
    else {
      $s_path = Memory::$s_helperPath . $s_helper . '.inc.php';
    }

    if( !$service_File->exists($s_path) ){
      throw new \MemoryException('Can not find helper ' . $s_helper);
    }

    require_once($s_path);

    if( $s_helper == 'HTML' ){
      $s_caller = 'core\helpers\html\\' . $s_helper;
    }
    else {
      if( class_exists('core\helpers\\' . $s_helper) ){
        $s_caller = 'core\helpers\\' . $s_helper;
      }
      else if( class_exists('HTML_' . $s_helper) ){
        /* Legancy way */
        $s_caller = 'Helper_' . $s_helper;
      }
      else {
        throw new \MemoryException('Could not find helper ' . $s_helper . ' in file ' . $s_path . '.');
      }
    }
    $object = Memory::injection($s_caller, $s_path);

    if( !$bo_data ){
      Memory::setMemory('helper', $s_helper, $object);
    }

    return $object;
  }

  /**
   * API for checking or a service exists
   *
   * @param   String $s_name  The name of the service
   * @param		bool	$bo_data	 Set to true to use the data directory 
   * @return  bool	True if the service exists, otherwise false
   */
  public static function isService($s_name, $bo_data = false){
    $s_name = ucfirst($s_name);

    /* Check or class is in the global memory */
    if( Memory::checkMemory('service', $s_name) ){
      return true;
    }

    /* Call class File */
    $service_File = Memory::getMemory('service', 'File');

    /* Check or the file exists */
    if( $bo_data ){
      $s_path = Memory::$s_servicePath . 'data/' . $s_name . '.inc.php';
    }
    else {
      $s_path = Memory::$s_servicePath . $s_name . '.inc.php';
    }
    if( !$service_File->exists($s_path) ){
      return false;
    }

    $s_service = $service_File->readFile($s_path);
    if( !$bo_data && strpos($s_service, 'namespace core\services;') !== false ){
      return true;
    }
    if( $bo_data && strpos($s_service, 'namespace core\services\data;') !== false ){
      return true;
    }
    if( strpos($s_service, 'class Service_' . $s_name) !== false ){
      return true;
    }

    return false;
  }

  /**
   * Loads the requested service
   *
   * @param   String  $s_service  The name of the service
   * @param		bool		$bo_data	 Set to true to use the data directory
   * @return  Service  The requested service
   * @throws  Exception If the requested service does not exist
   */
  public static function services($s_service, $bo_data = false){
    $s_service = ucfirst($s_service);

    if( !$bo_data && Memory::checkMemory('service', $s_service) ){
      return Memory::getMemory('service', $s_service);
    }

    /* Check service */
    $service_File = Memory::getMemory('service', 'File');
    if( $bo_data ){
      $s_path = Memory::$s_servicePath . 'data/' . $s_service . '.inc.php';
    }
    else {
      $s_path = Memory::$s_servicePath . $s_service . '.inc.php';
    }
    if( !$service_File->exists($s_path) ){
      throw new \MemoryException('Can not find service ' . $s_service);
    }

    require_once($s_path);

    if( $s_service == 'Database' ){
      $obj_Query_main = new core\database\Query_main();
      $object = $obj_Query_main->loadDatabase();
    }
    else {
      if( class_exists('core\services\\' . $s_service) ){
        $s_caller = 'core\services\\' . $s_service;
      }
      else if( class_exists('Service_' . $s_service) ){
        /* Legancy way */
        $s_caller = 'Service_' . $s_service;
      }
      else {
        throw new \MemoryException('Could not find service ' . $s_service . ' in file ' . $s_path . '.');
      }

      $object = Memory::injection($s_caller, $s_path);
    }

    if( !$bo_data ){
      Memory::setMemory('service', $s_service, $object);
    }

    return $object;
  }

  /**
   * API for checking or a model exists
   *
   * @param		String $s_name  The name of the model
   * @param		bool	$bo_data	 Set to true to use the data directory
   * @return  bool	True if the model exists, otherwise false
   */
  public static function isModel($s_name, $bo_data = false){
    $s_name = ucfirst($s_name);

    /* Check or class is in the global memory */
    if( !$bo_data && Memory::checkMemory('model', $s_name) ){
      return true;
    }

    /* Call class File */
    $service_File = Memory::getMemory('service', 'File');

    /* Check or the file exists */
    if( $bo_data ){
      $s_path = Memory::$s_modelPath . 'data/' . $s_name . '.inc.php';
    }
    else {
      $s_path = Memory::$s_modelPath . $s_name . '.inc.php';
    }
    if( !$service_File->exists($s_path) ){
      return false;
    }

    /* Check or the class exists */
    $s_model = $service_File->readFile($s_path);
    if( !$bo_data && strpos($s_model, 'namespace core\models;') !== false ){
      return true;
    }
    if( $bo_data && strpos($s_model, 'namespace core\models\data;') !== false ){
      return true;
    }
    if( strpos($s_model, 'class Model_' . $s_name) !== false ){
      return true;
    }

    return false;
  }

  /**
   * Loads the requested model
   *
   * @param   String  $s_model  The name of the model
   * @param		bool		$bo_data	 Set to true to use the data directory
   * @return  Model  The requested model
   * @throws  Exception If the requested model does not exist
   */
  public static function models($s_model, $bo_data = false){
    $s_model = ucfirst($s_model);

    if( !bo_data && Memory::checkMemory('model', $s_model) ){
      return Memory::getMemory('model', $s_model);
    }

    /* Check model */
    $service_File = Memory::getMemory('service', 'File');
    if( $bo_data ){
      $s_path = Memory::$s_modelPath . 'data/' . $s_model . '.inc.php';
    }
    else {
      $s_path = Memory::$s_modelPath . $s_model . '.inc.php';
    }
    if( !$service_File->exists($s_path) ){
      throw new \MemoryException('Can not find model ' . $s_model);
    }

    require_once($s_path);

    if( class_exists('core\models\\' . $s_model) ){
      $s_caller = 'core\modesl\\' . $s_model;
    }
    else if( class_exists('Model_' . $s_model) ){
      /* Legancy way */
      $s_caller = 'Model_' . $s_model;
    }
    else {
      throw new \MemoryException('Could not find model ' . $s_model . ' in file ' . $s_path . '.');
    }

    $object = Memory::injection($s_caller, $s_path);

    if( !$bo_data ){
      Memory::setMemory('model', $s_model, $object);
    }

    return $object;
  }

  /**
   * Checks if a helper, service or model is loaded
   *
   * @param  String 	$s_type     The type (Service|Model|Helper)
   * @param  String	$s_name     The name of the object
   * @return boolean	True if the value exists in the memory, false if it does not
   */
  public static function isLoaded($s_type, $s_name){
    $s_type = strtolower($s_type);
    $s_name = ucfirst($s_name);

    if( array_key_exists($s_name, Memory::$a_memory[ $s_type ]) ){
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Performs the dependency injection
   * 
   * @param String $s_caller		The class name
   * @param String $s_filename	The source file name
   * @throws MemoryException		If the object is not instantiable.
   * @return Object	The called object
   */
  private static function injection($s_caller, $s_filename){
    $ref = new \ReflectionClass($s_caller);
    if( !$ref->isInstantiable() ){
      throw new \MemoryException('Can not create a object from class ' . $s_caller . '.');
    }

    $s_file = Memory::services('File')->readFile(NIV . $s_filename);
    preg_match('#function\\s+__construct\\s?\({1}\\s?([\\a-zA-Z\\s\$\-_,]+)\\s?\){1}#si', $s_file, $a_matches);
    if( count($a_matches) == 0 ){
      /* No arguments */
      return new $s_caller();
    }
    $a_argumentNamesPre = explode(',', $a_matches[ 1 ]);

    $a_argumentNames = array();
    $a_arguments = array();
    foreach( $a_argumentNamesPre AS $s_name ){
      $s_name = trim($s_name);
      if( strpos($s_name, ' ') === false ){
        continue;
      }
      if( substr($s_name, 0, 1) == '\\' ){
        $s_name = substr($s_name, 1);
      }

      $a_item = explode(' ', $s_name);
      $a_argumentNames[] = $a_item[ 0 ];
    }

    foreach( $a_argumentNames AS $s_name ){
      $a_path = explode('\\', $s_name);

      if( count($a_path) == 1 ){
        /* No namespace */
        if( strpos($s_name, 'Helper_') !== false ){
          $s_name = str_replace('Helper_', '', $s_name);
          $a_arguments[] = Memory::helpers($s_name);
        }
        else if( strpos($s_name, 'Service_') !== false ){
          $s_name = str_replace('Service_', '', $s_name);
          $a_arguments[] = Memory::services($s_name);
        }
        else if( strpos($s_name, 'Model_') !== false ){
          $s_name = str_replace('Model_', '', $s_name);
          $a_arguments[] = Memory::models($s_name);
        }
        else {
          /* Try to load object */
          $a_arguments[] = Memory::injection($s_caller);
        }
      }
      else {
        $s_name = end($a_path);
        $bo_data = false;
        if( $a_path[ 2 ] == 'data' ){
          $bo_data = true;
        }

        if( ($a_path[ 1 ] == 'helpers') || ($a_path[ 1 ] == 'html') ){
          $a_arguments[] = Memory::helpers($s_name, $bo_data);
        }
        else if( ($a_path[ 1 ] == 'services') || ($a_path[ 1 ] == 'database') ){
          $a_arguments[] = Memory::services($s_name, $bo_data);
        }
        else if( $a_path[ 1 ] == 'models' ){
          $a_arguments[] = Memory::models($s_name, $bo_data);
        }
      }
    }

    return $ref->newInstanceArgs($a_arguments);
  }

  /**
   * API for checking the type of the given value.
   * Kills the program when the type of the variable is not the requested type
   *
   * @param	 String	$s_type	The type the variable schould be
   * @param	 object	$value	The variable that needs to be checked
   * @throws  NullPointerException if $value is null and $s_type is not 'null'.
   * @throws  TypeException if $value has the wrong type.
   */
  public static function type($s_type, $value){
    $bo_oke = true;

    if( is_null($s_type) ){
      throw new \NullPointerException('Type can not be a null-pointer');
    }

    if( $s_type != 'null' && is_null($value) ){
      throw new \NullPointerException('Null found when expected ' . $s_type . '.');
    }

    switch( $s_type ){
      case 'bool':
        if( !is_bool($value) )
          $bo_oke = false;
        break;

      case 'int':
        if( !is_int($value) )
          $bo_oke = false;
        break;

      case 'float':
        if( !is_float($value) )
          $bo_oke = false;
        break;

      case 'String':
        if( !is_String($value) )
          $bo_oke = false;
        break;

      case 'object':
        if( !is_object($value) )
          $bo_oke = false;
        break;

      case 'array':
        if( !is_array($value) )
          $bo_oke = false;
        break;

      case 'null' :
        if( !is_null($value) )
          $bo_oke = false;
        break;
    }

    if( !$bo_oke ){
      throw new \TypeException('Wrong datatype found. Expected ' . $s_type . ' but found ' . gettype($value) . '.');
    }
  }

  /**
   * Removes a value from the global memory
   *
   * @param	String  $s_type The type (Service|Model|Helper)
   * @param	String  $s_name The name of the data
   */
  public static function delete($s_type, $s_name){
    \core\Memory::type('String', $s_type);
    \core\Memory::type('String', $s_name);

    $s_type = strtolower($s_type);
    $s_name = ucfirst(strtolower($s_name));

    if( \core\Memory::isLoaded($s_type, $s_name) ){
      unset(\core\Memory::$a_memory[ $s_type ][ $s_name ]);
    }
    else {
      throw new \MemoryException("Trying to delete " . $s_type . " " . $s_name . " that does not exist");
    }
  }

  /**
   * Stops the framework and writes all the content to the screen
   */
  public static function endProgram(){
    if( \core\Memory::checkMemory('service', 'Template') ){
      $service_Template = \core\Memory::getMemory('service', 'Template');
      $service_Template->printToScreen();
    }

    die();
  }

  public static function reset(){
    Memory::$bo_ajax = null;
    Memory::$a_memory = null;
    Memory::$s_base = null;
    Memory::$s_page = null;
    Memory::$s_protocol = null;
    Memory::$bo_testing = null;

    Memory::$s_servicePath = null;
    Memory::$s_modelPath = null;
    Memory::$s_helperPath = null;
    Memory::$s_classPath = null;
    Memory::$s_interfacePath = null;
  }

  /**
   * Transforms a relative url into a absolute url (site domain only)
   *
   * @param   String  $s_url  The relative url
   * @return  String The absolute url
   */
  public static function generateUrl($s_url){
    $s_url = str_replace('../', '', $s_url);
    $s_url = str_replace('./', '', $s_url);
    $s_url = Memory::$s_base . $s_url;

    return $s_url;
  }

}
if( !function_exists('class_alias') ){

  function class_alias($original, $alias){
    eval('class ' . $alias . ' extends ' . $original . ' {}');
  }

}
?>
