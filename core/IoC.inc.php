<?php
namespace  core;

class IoC {
	private $settings;
	protected static $a_rules = array();
	
	public function __construct(\Settings $settings){
		$this->settings = $settings;
		
		$this->setRules();
		
		$this->detectDatabase();
		$this->detectLogger();
		$this->detectDefaults();
		
		var_dump(IoC::$a_rules);
	}
	
	protected function setRules(){
		
	}
	
	private function detectDatabase(){
		$s_database = $this->settings->get('settings/SQL/type');
		
		IoC::$a_rules['DAL'] = '\core\database\\'.$s_database;
		IoC::$a_rules['Builder'] = '\core\database\Builder_'.$s_database;
	}
	
	private function detectLogger(){
		if( !interface_exists('\Logger') ){
			require(NIV.'core/interfaces/Logger.inc.php');
		}
		
		if (defined('LOGGER')) {
			$s_type = LOGGER;
		}
		
		if (! $this->settings->exists('main/logs')) {
			$s_type = 'default';
		} else {
			$s_type = $this->settings->get('main/logs');
		}
		
		switch ($s_type) {
			case 'default':
				IoC::$a_rules['Logger'] = '\core\services\logger\LoggerDefault';
				break;
		
			case 'error_log':
				IoC::$a_rules['Logger'] = '\core\services\logger\LoggerErrorLog';
				break;
		
			case 'sys_log':
				IoC::$a_rules['Logger'] ='\core\services\logger\LoggerSysLog';
				break;
		
			default:
				IoC::$a_rules['Logger'] = $s_type;
		}
	}
	
	private function detectDefaults(){
		$a_items = array('Header','Footer','Menu');
		foreach($a_items AS $s_item){
			if( file_exists(NIV.'includes/'.$s_item.'.inc.php') ){
				IoC::$a_rules[$s_item] = '\includes\\'.$s_item;
			}
			else {
				IoC::$a_rules[$s_item] = '\core\\'.$s_item;
			}
		}
		
		IoC::$a_rules['Input'] = '\core\Input';
		IoC::$a_rules['Output'] = '\core\Output';
		IoC::$a_rules['Settings'] = '\core\services\Settings';
	}
	
	public static function check($s_name){
		if( substr($s_name, 0,1) == '\\' ){
			$s_name = substr($s_name, 1);
		}
		
		/* Check for interface */
		$s_interface = '';
		if( file_exists(NIV.'core/interfaces/'.$s_name.'.inc.php') ){
			$s_interface = NIV.'core/interfaces/'.$s_name.'.inc.php';
		}
		else if( file_exists(NIV.'includes/interfaces/'.$s_name.'.inc.php') ){
			$s_interface = NIV.'includes/interfaces/'.$s_name.'.inc.php';
		}
		if( !empty($s_interface) && !interface_exists('\\'.$s_name) ){
			require($s_interface);
		}
		
		if( array_key_exists($s_name, IoC::$a_rules) ){
			echo(' found rule : '.IoC::$a_rules[$s_name].'<br>');
			return IoC::$a_rules[$s_name];
		}
		
		return null;
	}
}