<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testSession extends GeneralTest {
	private $service_Session;
	private $s_name;
	private $s_data;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/services/Session.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->service_Session = new Service_Session();
		
		$this->s_name	= 'testSession';
		$this->s_data	= 'lalalala';
		
		unset($_SESSION[$this->s_name]);
	}
	
	public function tearDown(){
		$this->service_Session	= null;
		
		parent::tearDown();
	}

	/**
	 * Tests the deleting of a session
	 * 
	 * @Test
	 */
	public function testDelete(){	
		$this->service_Session->set($this->s_name,$this->s_data);
		$this->assertTrue(isset($_SESSION[$this->s_name]),'Session '.$this->s_name.' should exist.');
		
		$this->service_Session->delete($this->s_name);
		
		$this->assertFalse(isset($_SESSION[$this->s_name]),'Session '.$this->s_name.' should not exist.');
	}

	/**
	 * Test setting a session
	 * 
	 * @Test
	 */
	public function testSet(){		
		$this->service_Session->set($this->s_name,$this->s_data);
		
		$this->assertTrue(isset($_SESSION[$this->s_name]),'Session '.$this->s_name.' should exist.');
	}

	/**
	 * Tests the retreaval of a cookie
	 * 
	 * @Test
	 */
	public function testGet($s_cookieName){
		$this->service_Session->set($this->s_name,$this->s_data);
		$this->assertEquals($this->s_data,$this->service_Session->get($this->s_name));
	}

	/**
	 * Test the session existing check
	 * 
	 * @Test
	 */
	public function testExists($s_cookieName){
		$this->assertFalse($this->service_Session->exists($this->s_name));
		
		$this->service_Session->set($this->s_name,$this->s_data);
		
		$this->assertTrue($this->service_Session->exists($this->s_name));
	}
}