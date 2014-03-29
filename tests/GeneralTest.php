<?php
abstract class GeneralTest extends PHPUnit_Framework_TestCase {
	protected $s_base;
	
	public function __construct(){
		require_once(NIV.'include/Memory.php');
				
		parent::__construct();
		
		if( !defined('DATA_DIR') )
			define('DATA_DIR',NIV.'admin/data/');
		if( !defined('LEVEL') )
			define('LEVEL',NIV);
		
		/* First run for inclusion */
		Memory::setTesting();
		$this->s_base = '/';
		
		error_reporting(E_ALL);
		ini_set('display_errors','on');
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
