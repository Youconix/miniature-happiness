<?php

if( !defined('NIV') ){
  define('NIV',dirname(__FILE__).'/../../../');
}

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

class testQueue extends GeneralTest {
	private $obj_Queue;
	private $a_data;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/classes/Queue.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->obj_Queue	= new \core\classes\Queue();
		$this->a_data	= array(23,456,1332,42354);
	}
	
	public function tearDown(){
		$this->obj_Queue = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests setting initial value
	 * 
	 * @test
	 */
	public function construct(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		
		$this->obj_Queue	= new \core\classes\Queue($this->a_data);
		
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test for adding another queue
	 * 
	 * @test
	 */
	public function addQueue(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		
		$obj_Queue	= new \core\classes\Queue($this->a_data);
		$this->obj_Queue->addQueue($obj_Queue);
		
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test for adding a array
	 * 
	 * @test
	 */
	public function addArray(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		
		$this->obj_Queue->addArray($this->a_data);
		
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test of pushing a item to the end of the queue
	 * 
	 * @test
	 */
	public function push(){
		$i_item = 32543245;
		$this->obj_Queue->push($i_item);
		$this->assertEquals($i_item,$this->obj_Queue->peek());
	}

	/**
	 * Test of retreaving a item from the front of the queue
	 * 
	 * @test
	 */
	public function pop(){
		$this->obj_Queue->addArray($this->a_data);
		$this->assertEquals($this->a_data[0],$this->obj_Queue->pop());
		$this->assertNotEquals($this->a_data[0],$this->obj_Queue->pop());
	}

	/**
	 * Test of retreaving a item from the front of the queue without removing it
	 * 
	 * @test
	 */
	public function peek(){
		$this->obj_Queue->addArray($this->a_data);
		$this->assertEquals($this->a_data[0],$this->obj_Queue->peek());
		$this->assertEquals($this->a_data[0],$this->obj_Queue->peek());
	}

	/**
	 * Test for checking or a item exists in the queue
	 * 
	 * @test
	 */
	public function search(){
		$this->assertFalse($this->obj_Queue->search($this->a_data[1]));
		$this->obj_Queue->addArray($this->a_data);
		$this->assertTrue($this->obj_Queue->search($this->a_data[1]));
	}

	/**
	 * Tests the empty detection of the queue
	 * 
	 * @test
	 */
	public function myIsEmpty(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		$this->obj_Queue->addArray($this->a_data);
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test for clearing the queue
	 * 
	 * @test
	 */
	public function clear(){
		$this->obj_Queue->addArray($this->a_data);
		$this->assertFalse($this->obj_Queue->isEmpty());
		$this->obj_Queue->clear();
		$this->assertTrue($this->obj_Queue->isEmpty());
	}
}
?>