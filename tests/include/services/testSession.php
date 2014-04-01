<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/GeneralTest.php');

class testSession extends GeneralTest {
  private $service_Settings;
  private $service_Database;
  private $model_Groups;
	private $service_Session;
	private $s_name;
	private $s_data;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/services/Session.inc.php');
    
    $this->loadStub('DummySettings');
    $this->loadStub('DummyDAL');
    $this->loadStub('DummyGroups');
    
    $this->service_Settings = new DummySettings();
    $this->service_Database = new DummyDAL();
    $this->model_Groups = new DummyGroups();
	}

	public function setUp(){
		parent::setUp();
		
		$this->service_Session = new \core\services\Session($this->service_Settings,$this->service_Database,$this->model_Groups);
		
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
	 * @test
	 */
	public function delete(){	
		$this->service_Session->set($this->s_name,$this->s_data);
		$this->assertTrue(isset($_SESSION[$this->s_name]),'Session '.$this->s_name.' should exist.');
		
		$this->service_Session->delete($this->s_name);
		
		$this->assertFalse(isset($_SESSION[$this->s_name]),'Session '.$this->s_name.' should not exist.');
	}

	/**
	 * Test setting a session
	 * 
	 * @test
	 */
	public function set(){		
		$this->service_Session->set($this->s_name,$this->s_data);
		
		$this->assertTrue(isset($_SESSION[$this->s_name]),'Session '.$this->s_name.' should exist.');
	}

	/**
	 * Tests the retreaval of a cookie
	 * 
	 * @test
	 */
	public function get(){
		$this->service_Session->set($this->s_name,$this->s_data);
		$this->assertEquals($this->s_data,$this->service_Session->get($this->s_name));
	}

	/**
	 * Test the session existing check
	 * 
	 * @test
	 */
	public function existsTest(){
		$this->assertFalse($this->service_Session->exists($this->s_name));
		
		$this->service_Session->set($this->s_name,$this->s_data);
		
		$this->assertTrue($this->service_Session->exists($this->s_name));
	}
}