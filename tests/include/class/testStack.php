<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testStack extends GeneralTest {
	private $obj_Stack;
	private $a_data;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/class/Stack.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->obj_Stack	= new Stack();
		$this->a_data	= array(23,456,1332,42354);
	}
	
	public function tearDown(){
		$this->obj_Stack = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests setting initial value
	 */
	public function testConstruct(){
		$this->assertTrue($this->obj_Stack->isEmpty());
		
		$this->obj_Stack	= new Stack($this->a_data);
		
		$this->assertFalse($this->obj_Stack->isEmpty());
	}

	/**
	 * Test for adding another stack
	 */
	public function testAddStack(){
		$this->assertTrue($this->obj_Stack->isEmpty());
		
		$obj_Queue	= new Stack($this->a_data);
		$this->obj_Stack->addStack($obj_Queue);
		
		$this->assertFalse($this->obj_Stack->isEmpty());
	}

	/**
	 * Test for adding a array
	 */
	public function testAddArray(){
		$this->assertTrue($this->obj_Stack->isEmpty());
		
		$this->obj_Stack->addArray($this->a_data);
		
		$this->assertFalse($this->obj_Stack->isEmpty());
	}

	/**
	 * Test of pushing a item to the end of the stack
	 */
	public function testPush(){
		$i_item = 32543245;
		$this->obj_Stack->push($i_item);
		$this->assertEquals($i_item,$this->obj_Stack->peek());
	}

	/**
	 * Test of retreaving a item from the end of the stack
	 */
	public function testPop(){
		$this->obj_Stack->addArray($this->a_data);
		$this->assertEquals(end($this->a_data),$this->obj_Stack->pop());
		$this->assertNotEquals(end($this->a_data),$this->obj_Stack->pop());
	}

	/**
	 * Test of retreaving a item from the end of the stack without removing it
	 */
	public function testPeek(){
		$this->obj_Stack->addArray($this->a_data);
		$this->assertEquals(end($this->a_data),$this->obj_Stack->peek());
		$this->assertEquals(end($this->a_data),$this->obj_Stack->peek());
	}

	/**
	 * Test for checking or a item exists in the stack
	 */
	public function testSearch(){
		$this->assertFalse($this->obj_Stack->search($this->a_data[1]));
		$this->obj_Stack->addArray($this->a_data);
		$this->assertTrue($this->obj_Stack->search($this->a_data[1]));
	}

	/**
	 * Tests the empty detection of the stack
	 */
	public function testIsEmpty(){
		$this->assertTrue($this->obj_Stack->isEmpty());
		$this->obj_Stack->addArray($this->a_data);
		$this->assertFalse($this->obj_Stack->isEmpty());
	}

	/**
	 * Test for clearing the stack
	 */
	public function testClear(){
		$this->obj_Stack->addArray($this->a_data);
		$this->assertFalse($this->obj_Stack->isEmpty());
		$this->obj_Stack->clear();
		$this->assertTrue($this->obj_Stack->isEmpty());
	}
}
?>