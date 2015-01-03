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
 * @changed   31/05/2014
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
  
  private static $a_memory = null;
  private static $bo_testing = false;
  private static $a_service;
  private static $a_serviceData;
  private static $a_model;
  private static $a_modelData;
  private static $a_helper;
  private static $a_helperData;
  private static $a_class;
  private static $a_interface;
  private static $bo_prettyUrls;

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
    Memory::$a_memory[ 'service-data' ] = array();
    Memory::$a_memory[ 'model' ] = array();
    Memory::$a_memory[ 'model-data' ] = array();
    Memory::$a_memory[ 'helper' ] = array();
    Memory::$a_memory[ 'helper-data' ] = array();
    
    Memory::$a_service = array(
    	'systemPath' => NIV . 'core/services/',
    	'userPath' => NIV . 'includes/services/',
    	'systemNamespace' => '\core\services\\',
    	'userNamespace' => '\includes\services\\',
    );
    Memory::$a_serviceData = array(
    'systemPath' => NIV . 'core/services/data/',
    'userPath' => NIV . 'includes/services/data/',
    'systemNamespace' => '\core\services\data\\',
    'userNamespace' => '\includes\services\data\\',
    );
    Memory::$a_model = array(
    'systemPath' => NIV . 'core/models/',
    'userPath' => NIV . 'includes/models/',
    'systemNamespace' => '\core\models\\',
    'userNamespace' => '\includes\models\\',
    );
    Memory::$a_modelData = array(
    'systemPath' => NIV . 'core/models/data/',
    'userPath' => NIV . 'includes/models/data/',
    'systemNamespace' => '\core\models\data\\',
    'userNamespace' => '\includes\models\data\\',
    );
    Memory::$a_helper = array(
    'systemPath' => NIV . 'core/helpers/',
    'userPath' => NIV . 'includes/helpers/',
    'systemNamespace' => '\core\helpers\\',
    'userNamespace' => '\includes\helpers\\',
    );
    Memory::$a_helperData = array(
    'systemPath' => NIV . 'core/helpers/data/',
    'userPath' => NIV . 'includes/helpers/data/',
    'systemNamespace' => '\core\helpers\data\\',
    'userNamespace' => '\includes\helpers\data\\',
    );
    Memory::$a_class = array(
    'systemPath' => NIV . 'core/classes/',
    'userPath' => NIV . 'includes/classes/',
    'systemNamespace' => '\core\classes\\',
    'userNamespace' => '\includes\classes\\',
    );
    Memory::$a_interface = array(
    'systemPath' => NIV . 'core/interface/',
    'userPath' => NIV . 'includes/interface/',
    'systemNamespace' => '\core\interfaces\\',
    'userNamespace' => '\includes\interfaces\\',
    );
    
    require_once(Memory::$a_service['systemPath'] . 'Service.inc.php');
    require_once(Memory::$a_model['systemPath'] . 'Model.inc.php');
    require_once(Memory::$a_helper['systemPath'] . 'Helper.inc.php');
  
    /* Load standard services */
    require_once(Memory::$a_service['systemPath'] . 'File.inc.php');
    $service_File = new \core\services\File();
    Memory::$a_memory[ 'service' ][ 'File' ] = $service_File;

    require_once(Memory::$a_service['systemPath'] . 'Settings.inc.php');
    $service_Settings = new \core\services\Settings();
    Memory::$a_memory[ 'service' ][ 'XmlSettings' ] = $service_Settings;
    Memory::$a_memory[ 'service' ][ 'Settings' ] = $service_Settings;

    date_default_timezone_set($service_Settings->get('settings/main/timeZone'));

    require_once(Memory::$a_service['systemPath'] . 'FileData.inc.php');
    $service_FileData = new \core\services\FileData();
    Memory::$a_memory[ 'service' ][ 'FileData' ] = $service_FileData;

    require_once(Memory::$a_service['systemPath'] . 'Logs.inc.php');
    $service_Logs = new \core\services\Logs($service_File, $service_FileData);
    Memory::$a_memory[ 'service' ][ 'Logs' ] = $service_Logs;

    require_once(Memory::$a_service['systemPath'] . 'Session.inc.php');
    
    require_once(Memory::$a_service['systemPath'] . 'Security.inc.php');
    $service_Security = new \core\services\Security();
    Memory::$a_memory[ 'service' ][ 'Security' ] = $service_Security;
    
    require_once(Memory::$a_service['systemPath'] . 'Cookie.inc.php');
    $service_Cookie = new \core\services\Cookie($service_Security);
    Memory::$a_memory[ 'service' ][ 'Cookie' ] = $service_Cookie;
    
    require_once(Memory::$a_model['systemPath'] . 'Config.inc.php');
    $model_Config = new \core\models\Config($service_File, $service_Settings, $service_Cookie);
    Memory::$a_memory[ 'model' ][ 'Config' ] = $model_Config;

    Memory::setDefaultValues($service_Security,$service_Settings);
  }

  private static function setDefaultValues($service_Security,$service_Settings){
    
      
    if( isset($_SERVER[ 'SERVER_ADDR' ]) && in_array($_SERVER[ 'SERVER_ADDR' ], array( '127.0.0.1', '::1' )) && !defined('DEBUG') ){
      define('DEBUG', null);
    }

    if( defined('DEBUG') ){
      ini_set('display_errors', 'on');
    }
    else {
      ini_set('display_errors', 'off');
    }

    error_reporting(E_ALL);
    
    Memory::$bo_prettyUrls = false;
    if( $service_Settings->exists('settings/main/pretty_urls') && $service_Settings->get('settings/main/pretty_urls') == 1 ){
      Memory::$bo_prettyUrls = true;
    }
  }

  /**
   * Returns the used protocol
   *
   * @return String	The protocol
   * @deprecated since version 2.   See include/models/Config:getProtocol
   */
  public static function getProtocol(){
    return Memory::getMemory('model', 'Config')->getProtocol();
  }

  /**
   * Returns the current page
   *
   * @return String	The page
   * @deprecated since version 2.   See include/models/Config:getPage
   */
  public static function getPage(){
    return Memory::getMemory('model', 'Config')->getPage();
  }

  /**
   * Checks if ajax-mode is active
   *
   * @return boolean	True if ajax-mode is active
   * @deprecated since version 2.   See include/models/Config:isAjax
   */
  public static function isAjax(){
    return Memory::getMemory('model', 'Config')->isAjax();
  }

  /**
   * Sets the framework in ajax-
   * 
   * @deprecated since version 2
   */
  public static function setAjax(){
    Memory::getMemory('model', 'Config')->setAjax();
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
   * @deprecated since version 2.   See include/models/Config:getBase
   */
  public static function getBase(){
    return Memory::getMemory('model', 'Config')->getBase();
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
   * @param   String  $s_type  The type (helper|helper-data|model|model-data|service|service-data)
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
   * @param   String  $s_type  The type (helper|helper-data|model|model-data|service|service-data)
   * @param   String  $s_name  The name of the service or model
   * @return  object  The requested service or model
   */
  private static function getMemory($s_type, $s_name){
    return Memory::$a_memory[ $s_type ][ $s_name ];
  }

  /**
   * Saves the given service or model in the memory
   *
   * @param   String  $s_type     The type (helper|helper-data|model|model-data|service|service-data)
   * @param   String  $s_name     The name of the service or model
   * @param   object  $obj_value  The service or model
   */
  private static function setMemory($s_type, $s_name, $obj_value){
    Memory::$a_memory[ $s_type ][ $s_name ] = $obj_value;
  }

  private static function isModule($s_name, $a_memoryItems, $s_path, $s_namespace, $s_fallback = ''){
    $s_name = ucfirst($s_name);

    if( (Memory::checkMemory($a_memoryItems[ 0 ], $s_name)) || (isset($a_memoryItems[1]) && Memory::checkMemory($a_memoryItems[ 1 ], $s_name)) ){
      return true;
    }

    /* Call class file */
    $service_File = Memory::getMemory('service', 'File');
    $s_path = $s_path . '/' . $s_name . '.inc.php';

    if( !$service_File->exists($s_path) ){
      return false;
    }

    $s_item = $service_File->readFile($s_path);
    
    if( (strpos($s_item, 'namespace ' . $s_namespace . ';') !== false) || (strpos($s_item, 'namespace ' . $s_namespace . '\data;') !== false) ){
      return true;
    }
    
    if( !empty($s_fallback) && (strpos($s_item, 'class ' . $s_fallback . '_' . $s_name) !== false) ){
      return true;
    }

    return false;
  }

  /**
   * Loads the requested module
   * Automaticaly overrides the system module with the user defined one
   *
   * @param  String  $s_name  The name of the module
   * @param	 String  $s_memoryType   The memory-type that the data is saved in
   * @param  String  $a_data         The locations array
   * @param  String  $s_fallback     The fallback class type (V1)
   * @return Object  The module
   * @throws  \MemoryException If the requested module does not exist
   * @throws  \OverrideException If the override module is not a child of the system module
   */
  private static function loadModule($s_name, $s_memoryType, $a_data, $s_fallback = ''){
    $s_name = ucfirst($s_name);

    if( Memory::checkMemory($s_memoryType, $s_name) ){
      return Memory::getMemory($s_memoryType, $s_name);
    }

    /* Check service */
    $service_File = Memory::getMemory('service', 'File');
    $s_callerParent = '';
    $s_path = '';

    if( $service_File->exists($a_data['userPath'].$s_name.'.inc.php') ){
		$s_caller = $a_data['userNamespace'].$s_name;
		$s_path = $a_data['userPath'].$s_name.'.inc.php';
    	
    	if( $service_File->exists($a_data['systemPath'].$s_name.'.inc.php') ){
    		$s_callerParent = $a_data['systemNamespace'].$s_name;
    		
    		if( !class_exists($s_callerParent) ){
    			require($a_data['systemPath'].$s_name.'.inc.php');
    		}
    	}

    	require_once($a_data['userPath'].$s_name.'.inc.php');
    }
    elseif( $service_File->exists($a_data['systemPath'].$s_name.'.inc.php') ){
    	require_once($a_data['systemPath'].$s_name.'.inc.php');
    	$s_caller = $a_data['systemNamespace'].$s_name;
    	$s_path = $a_data['systemPath'].$s_name.'.inc.php';
    }
    else {
      throw new \MemoryException('Can not find ' . $s_memoryType . ' ' . $s_name);
    }

    if( (class_exists($a_data['userNamespace'] .  $s_name)) || (class_exists($a_data['systemNamespace'] .  $s_name))  ){ }
    else if( !empty($s_fallback) && class_exists($s_fallback . '_' . $s_name) ){
      /* Fallback */
      $s_caller = $s_fallback . '_' . $s_name;
    }
    else {
      throw new \MemoryException('Class '.$s_name . ' and class '.$s_memoryType . $s_fallback . '_' . $s_name . ' not found in ' . $a_data['userPath'] . ' and in '.$a_data['systemPath'].'.
      		Namespaces '.$a_data['userNamespace'].' and '.$a_data['systemNamespace'].'.');
    }

    $object = Memory::injection($s_caller, $s_path);
    if( !empty($s_callerParent) && !($object instanceof $s_callerParent) ){
    	throw new \OverrideException('Override ' . $s_memoryType . ' ' . $s_caller . ' is not a child of ' . $s_callerParent . '.');
    }

    Memory::setMemory($s_memoryType, $s_name, $object);

    return $object;
  }

  /**
   * API for checking or a helper exists
   *
   * @param		String $s_name  The name of the helper
   * @param		bool	$bo_data	 Set to true to use the data directory
   * @return  bool	True if the helper exists, otherwise false
   */
  public static function isHelper($s_name, $bo_data = false){
    $a_memoryItems = array( 'helper', 'helper-data' );
    $s_fallback = 'Helper';

    if( $bo_data ){
      $s_path = Memory::$s_helperPath . 'data/';
      $s_namespace = 'core\helpers\data';
    }
    else {
      $s_path = Memory::$s_helperPath;
      $s_namespace = 'core\helpers';
    }

    return Memory::isModule($s_name, $a_memoryItems, $s_path, $s_namespace, $s_fallback);
  }

  /**
   * Loads the requested helper
   *
   * @param   String  $s_name  The name of the helper
   * @param		bool		$bo_data	 Set to true to use the data directory
   * @return  Helper  The requested helper
   * @throws  Exception If the requested helper does not exist
   * @throws  \OverrideException If the override module is not a child of the system module
   */
  public static function helpers($s_name, $bo_data = false){
    if( $bo_data ){
      return Memory::loadModule($s_name, 'helper-data', Memory::$a_helperData);
    }

    $a_data = Memory::$a_helper;
    if( $s_name == 'HTML' ){
    	$a_data['systemNameSpace'] = '\core\helpers\html\\';
    }


    return Memory::loadModule($s_name, 'helper', $a_data, 'Helper');
  }

  /**
   * API for checking or a service exists
   *
   * @param   String $s_name  The name of the service
   * @param		bool	$bo_data	 Set to true to use the data directory 
   * @return  bool	True if the service exists, otherwise false
   */
  public static function isService($s_name, $bo_data = false){
    $a_memoryItems = array( 'service','service-data' );
    $s_fallback = 'Service';

    if( $bo_data ){
      return Memory::isModule($s_name, $a_memoryItems,Memory::$a_serviceData);
    }
    else {
      return Memory::isModule($s_name, $a_memoryItems,Memory::$a_service, $s_fallback);
    }
  }

  /**
   * Loads the requested service
   *
   * @param   String  $s_name  The name of the service
   * @param		bool		$bo_data	 Set to true to use the data directory
   * @return  Service  The requested service
   * @throws  Exception If the requested service does not exist
   * @throws  \OverrideException If the override module is not a child of the system module
   */
  public static function services($s_name, $bo_data = false){
    /* Check for DAL */
    if( $s_name == 'DAL' ){
      return Memory::loadDAL();      
    }    
    
    if( $bo_data ){
      return Memory::loadModule($s_name, 'service', Memory::$a_serviceData);
    }

    return Memory::loadModule($s_name, 'service', Memory::$a_service, 'Service');
  }
  
  /**
   * Loads the data access layer
   * @return DAL    The DAL
   */
  private static function loadDAL(){
    $s_name = 'Database';
      
    $service_Settings = Memory::services('Settings');
    require(NIV.'core/services/Database.inc.php');
    $obj_Query_main = new \core\database\Query_main($service_Settings);
    $object = $obj_Query_main->loadDatabase();

    Memory::setMemory('service', $s_name, $object);

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
    $a_memoryItems = array( 'model','model-data' );
    $s_fallback = 'Model';

    if( $bo_data ){
    	return Memory::isModule($s_name, $a_memoryItems, Memory::$a_modelData);
    }
    else {
    	return Memory::isModule($s_name, $a_memoryItems, Memory::$a_model, $s_fallback);
    }
  }

  /**
   * Loads the requested model
   *
   * @param   String  $s_name  The name of the model
   * @param		bool		$bo_data	 Set to true to use the data directory
   * @return  Model  The requested model
   * @throws  Exception If the requested model does not exist
   * @throws  \OverrideException If the override module is not a child of the system module
   */
  public static function models($s_name, $bo_data = false){
    if( $bo_data ){
      return Memory::loadModule($s_name, 'model-data', Memory::$a_modelData);
    }

    return Memory::loadModule($s_name, 'model', Memory::$a_model);
  }

  /**
   * Checks if a helper, service or model is loaded
   *
   * @param  String 	$s_type     The type (helper|helper-data|model|model-data|service|service-data)
   * @param  String	$s_name     The name of the object
   * @return boolean	True if the value exists in the memory, false if it does not
   */
  public static function isLoaded($s_type, $s_name){
    $s_type = strtolower($s_type);
    $s_name = ucfirst($s_name);

    if( !array_key_exists($s_type, Memory::$a_memory) ){
      return false;
    }
    if( array_key_exists($s_name, Memory::$a_memory[ $s_type ]) ){
      return true;
    }
    return false;
  }

  /**
   * Loads a class
   * 
   * @param String $s_class   The class name
   * @return Object   The Object
   * @throws \MemoryException If the class does not exist
   * @throws \OverrideException	If the override class is not a child of the default class
   */
  public static function loadClass($s_class){
    $s_class = ucfirst($s_class);

    /* Check model */
    $service_File = Memory::getMemory('service', 'File');
    $bo_override = false;
    $s_path = '';
    $s_caller = '';
    $s_callerParent = '';
    
    if( $service_File->exists(Memory::$a_class['userPath'].$s_class.'.inc.php') ){
    	if( $service_File->exists(Memory::$a_class['systemPath'].$s_class.'.inc.php') ){
    		$bo_override = true;
    		$s_callerParent = Memory::$a_class['systemNamespace'].$s_class;
    		
    		if( !class_exists($s_callerParent) ){
    			require(Memory::$a_class['systemPath'].$s_class.'.inc.php');
    		}
    	}
    	
    	require_once(Memory::$a_class['userPath'].$s_class.'.inc.php');
    	$s_path = Memory::$a_class['userPath'].$s_class.'.inc.php';
    	$s_caller = Memory::$a_class['userNamespace'].$s_class;
    }
    else if( $service_File->exists(Memory::$a_class['systemPath'].$s_class.'.inc.php') ){
    	require_once(Memory::$a_class['systemPath'].$s_class.'.inc.php');    	
    	$s_path = Memory::$a_class['systemPath'].$s_class.'.inc.php';
    	$s_caller = Memory::$a_class['systemNamespace'].$s_class;
    }
    else {
      throw new \MemoryException('Can not find class ' . $s_class);
    }
    
    $object = Memory::injection($s_caller, $s_path);
    
    if( !empty($s_callerParent) && !($object instanceof $s_callerParent) ){
		throw new \OverrideException('Override '.$s_caller.' is not a child of '.$s_callerParent.'.');
    }
    return $object;
  }
  
  /**
   * Loads an interface
   * 
   * @param String $s_interface		The interface
   * @throws \MemoryException If the interface does not exist
   */
  public static function loadInterface($s_interface){
    $s_interface = ucfirst($s_interface);

    /* Check model */
    $service_File = Memory::getMemory('service', 'File');
    
    if( $service_File->exists(Memory::$a_interface['userPath'].$s_interface.'.inc.php') ){
    	require_once(Memory::$a_interface['userPath'].$s_interface.'.inc.php');
    }
    else if( $service_File->exists(Memory::$a_interface['systemPath'].$s_interface.'.inc.php') ){
    	require_once(Memory::$a_interface['systemPath'].$s_interface.'.inc.php');
    }
    else {
      throw new \MemoryException('Can not find interface ' . $s_interface);
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

    $a_matches = Memory::getConstructor($s_filename);
    
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
   * Gets the constructor parameters
   * 
   * @param String	$s_filename	The file name
   * @return array	The parameters 
   */
  private static function getConstructor($s_filename){
  	$s_file = Memory::services('File')->readFile($s_filename);
  	if( stripos($s_file,'__construct') === false ){
  		/* Check if file has parent */
  		preg_match('#class\\s+[a-zA-Z0-9\-_]+\\s+extends\\s+([\\\a-zA-Z0-9_\-]+)#si',$s_file,$a_matches);
  		if( count($a_matches) == 0 ){
  			return array();
  		} 
  		
  		switch( $a_matches[1] ){
  			case '\core\models\Model':
  			case 'Model':
  				$s_filename = NIV.'core/models/Model.inc.php';
  				break;
  			
  			case '\core\services\Service':
  			case 'Service' :
  				$s_filename = NIV.'core/services/Service.inc.php';
  				break;
  				
  			case '\core\helpers\Helper':
  			case 'Helper' :
  				$s_filename = NIV.'core/helpers/Helper.inc.php';
  				break;
  				
  			default :
  			$s_filename = NIV.str_replace('\\','/',$a_matches[1]).'.inc.php';  		
  		}
  		
  		return Memory::getConstructor($s_filename);
  	}
  	
  	preg_match('#function\\s+__construct\\s?\({1}\\s?([\\a-zA-Z\\s\$\-_,]+)\\s?\){1}#si', $s_file, $a_matches);
  	
  	return $a_matches;
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
        if( !is_bool($value) ){ $bo_oke = false;  }
        break;

      case 'int':
        if( !is_int($value) ){ $bo_oke = false; }
        break;

      case 'float':
        if( !is_float($value) ){ $bo_oke = false; }
        break;

      case 'string':
        if( !is_String($value) ){ $bo_oke = false; }
        break;

      case 'object':
        if( !is_object($value) ){ $bo_oke = false; }
        break;

      case 'array':
        if( !is_array($value) ){ $bo_oke = false; }
        break;

      case 'null' :
        if( !is_null($value) ){ $bo_oke = false; }
        break;
    }

    if( !$bo_oke ){
      throw new \TypeException('Wrong datatype found. Expected ' . $s_type . ' but found ' . gettype($value) . '.');
    }
  }

  /**
   * Removes a value from the global memory
   *
   * @param	String  $s_type The type (helper|helper-data|model|model-data|service|service-data)
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
    Memory::$a_memory = null;
    Memory::$bo_testing = null;

    Memory::$a_service = null;
    Memory::$a_serviceData = null;
    Memory::$a_model = null;
    Memory::$a_modelData = null;
    Memory::$a_helper = null;
    Memory::$a_helperData = null;
    Memory::$a_class = null;
    Memory::$a_interface = null;
    Memory::$bo_prettyUrls = null;
  }
  
  /**
   * Transforms a relative url into a absolute url (site domain only)
   *
   * @param   String  $s_url  The relative url
   * @param   array  $a_payload The fields to send with the url, optional
   * @return  String The absolute url
   */
  public static function parseUrl($s_url,$a_payload = array()){
    if( substr($s_url, 0,1) != '/' ){ $s_url = '/'.$s_url; }
        
    if( Memory::$bo_prettyUrls ){
      $s_url .= '/';
      $s_url .= implode('/',$a_payload);
    }
    else {
      if(substr($s_url, -1) == '/'){
      	  if( count($a_payload) > 0 ){
      	  	$s_url .= 'index.php?';
      	  }
      }
      else {
        $s_url .= '.php?';
      }
      $a_items = array();
      foreach($a_payload AS $s_key => $s_value){
        $a_items[] = $s_key.'='.$s_value;
      }
      $s_url .= implode('&amp;',$a_items);
    }
    return $s_url;    
  }

  /**
   * Transforms a relative url into a absolute url (site domain only)
   *
   * @param   String  $s_url  The relative url
   * @return  String The absolute url
   * @deprecated See parseUrl
   */
  public static function generateUrl($s_url){
    return Memory::parseUrl($s_url);
  }
  
  /**
   * Redirects the visitor to the given site url
   *
   * @param   String  $s_url  The relative url
   * @param   array  $a_payload The fields to send with the url, optional
   */
  public static function redirect($s_url,$a_payload = array()){
      $s_url = Memory::parseUrl($s_url,$a_payload);
      header('location: '.$s_url);
      exit();
  }

  /**
   * Detects the system base
   * Should only be used on install (redirection)
   * 
   * @throws \MemoryException	If the base could not be found
   * @return string	The base
   */
  public static function detectBase(){
  	$s_url	= $_SERVER['SCRIPT_NAME'];
  	if( substr($s_url, 0,1) == '/' ){	$s_url = substr($s_url, 1); } 
  	$s_url = substr($s_url, 0, strrpos($s_url, '/'));
  	
  	
  	$a_dirs	= explode('/',$s_url);
  	$s_wwwRoot	= $_SERVER['DOCUMENT_ROOT'];
  	
  	/* Seach for Memory */
  	$s_base = '';
  	$i_pos = 0;
  	$i_max = count($a_dirs);
  	
  	while( $i_pos <= $i_max ){
  		if( file_exists($s_wwwRoot.'/'.$s_base.'/include/Memory.php') ){
  			return '/'.$s_base;
  		}
  		
  		if( !empty($s_base) ){ $s_base .= '/';	}
  		$s_base .= $a_dirs[$i_pos];
  		$i_pos++;
  	}
  	
  	throw new \MemoryException('Unable to detect website root for '.$_SERVER['SCRIPT_NAME'].'.');
  }
}

if( !function_exists('class_alias') ){

  function class_alias($original, $alias){
    eval('class ' . $alias . ' extends ' . $original . ' {}');
  }

}
?>
