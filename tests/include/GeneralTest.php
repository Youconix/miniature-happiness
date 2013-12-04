<?php
abstract class GeneralTest extends PHPUnit_Framework_TestCase {
	protected $s_base;
	
	public function __construct(){
		require_once(NIV.'include/Memory.php');
				
		parent::__construct();
		
		define('DATA_DIR',NIV.'admin/data/');
		define('LEVEL',NIV);
		
		/* First run for inclusion */
		Memory::setTesting();
/*		$this->s_base = Memory::services('XmlSettings')->get('settings/main/base');
		if( !empty($this->s_base) && substr($this->s_base,0,1) != '/' )	$this->s_base = '/'.$this->s_base;
		
		Memory::reset(); */
	}
	
	public function setUp(){
		Memory::setTesting();
		
		ob_start();
	}
	
	public function tearDown(){
		Memory::reset();
		
		ob_flush();
	}
}
