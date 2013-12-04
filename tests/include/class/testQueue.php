<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testQueue extends GeneralTest {
	private $obj_Queue;
	private $a_data;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/class/Queue.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->obj_Queue	= new Queue();
		$this->a_data	= array(23,456,1332,42354);
	}
	
	public function tearDown(){
		$this->obj_Queue = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests setting initial value
	 */
	public function testConstruct(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		
		$this->obj_Queue	= new Queue($this->a_data);
		
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test for adding another queue
	 */
	public function testAddQueue(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		
		$obj_Queue	= new Queue($this->a_data);
		$this->obj_Queue->addQueue($obj_Queue);
		
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test for adding a array
	 */
	public function testAddArray(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		
		$this->obj_Queue->addArray($this->a_data);
		
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test of pushing a item to the end of the queue
	 */
	public function testPush(){
		$i_item = 32543245;
		$this->obj_Queue->push($i_item);
		$this->assertEquals($i_item,$this->obj_Queue->peek());
	}

	/**
	 * Test of retreaving a item from the front of the queue
	 */
	public function testPop(){
		$this->obj_Queue->addArray($this->a_data);
		$this->assertEquals($this->a_data[0],$this->obj_Queue->pop());
		$this->assertNotEquals($this->a_data[0],$this->obj_Queue->pop());
	}

	/**
	 * Test of retreaving a item from the front of the queue without removing it
	 */
	public function testPeek(){
		$this->obj_Queue->addArray($this->a_data);
		$this->assertEquals($this->a_data[0],$this->obj_Queue->peek());
		$this->assertEquals($this->a_data[0],$this->obj_Queue->peek());
	}

	/**
	 * Test for checking or a item exists in the queue
	 */
	public function testSearch(){
		$this->assertFalse($this->obj_Queue->search($this->a_data[1]));
		$this->obj_Queue->addArray($this->a_data);
		$this->assertTrue($this->obj_Queue->search($this->a_data[1]));
	}

	/**
	 * Tests the empty detection of the queue
	 */
	public function testIsEmpty(){
		$this->assertTrue($this->obj_Queue->isEmpty());
		$this->obj_Queue->addArray($this->a_data);
		$this->assertFalse($this->obj_Queue->isEmpty());
	}

	/**
	 * Test for clearing the queue
	 */
	public function testClear(){
		$this->obj_Queue->addArray($this->a_data);
		$this->assertFalse($this->obj_Queue->isEmpty());
		$this->obj_Queue->clear();
		$this->assertTrue($this->obj_Queue->isEmpty());
	}
}
?>