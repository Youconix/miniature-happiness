<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testStack extends GeneralTest
{

    private $obj_Stack;

    private $a_data;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'include/classes/Stack.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->obj_Stack = new \core\classes\Stack();
        $this->a_data = array(
            23,
            456,
            1332,
            42354
        );
    }

    public function tearDown()
    {
        $this->obj_Stack = null;
        
        parent::tearDown();
    }

    /**
     * Tests setting initial value
     *
     * @test
     */
    public function construct()
    {
        $this->assertTrue($this->obj_Stack->isEmpty());
        
        $this->obj_Stack = new \core\classes\Stack($this->a_data);
        
        $this->assertFalse($this->obj_Stack->isEmpty());
    }

    /**
     * Test for adding another stack
     *
     * @test
     */
    public function addStack()
    {
        $this->assertTrue($this->obj_Stack->isEmpty());
        
        $obj_Queue = new \core\classes\Stack($this->a_data);
        $this->obj_Stack->addStack($obj_Queue);
        
        $this->assertFalse($this->obj_Stack->isEmpty());
    }

    /**
     * Test for adding a array
     *
     * @test
     */
    public function addArray()
    {
        $this->assertTrue($this->obj_Stack->isEmpty());
        
        $this->obj_Stack->addArray($this->a_data);
        
        $this->assertFalse($this->obj_Stack->isEmpty());
    }

    /**
     * Test of pushing a item to the end of the stack
     *
     * @test
     */
    public function push()
    {
        $i_item = 32543245;
        $this->obj_Stack->push($i_item);
        $this->assertEquals($i_item, $this->obj_Stack->peek());
    }

    /**
     * Test of retreaving a item from the end of the stack
     *
     * @test
     */
    public function pop()
    {
        $this->obj_Stack->addArray($this->a_data);
        $this->assertEquals(end($this->a_data), $this->obj_Stack->pop());
        $this->assertNotEquals(end($this->a_data), $this->obj_Stack->pop());
    }

    /**
     * Test of retreaving a item from the end of the stack without removing it
     *
     * @test
     */
    public function peek()
    {
        $this->obj_Stack->addArray($this->a_data);
        $this->assertEquals(end($this->a_data), $this->obj_Stack->peek());
        $this->assertEquals(end($this->a_data), $this->obj_Stack->peek());
    }

    /**
     * Test for checking or a item exists in the stack
     *
     * @test
     */
    public function search()
    {
        $this->assertFalse($this->obj_Stack->search($this->a_data[1]));
        $this->obj_Stack->addArray($this->a_data);
        $this->assertTrue($this->obj_Stack->search($this->a_data[1]));
    }

    /**
     * Tests the empty detection of the stack
     *
     * @test
     */
    public function myIsEmpty()
    {
        $this->assertTrue($this->obj_Stack->isEmpty());
        $this->obj_Stack->addArray($this->a_data);
        $this->assertFalse($this->obj_Stack->isEmpty());
    }

    /**
     * Test for clearing the stack
     *
     * @test
     */
    public function clear()
    {
        $this->obj_Stack->addArray($this->a_data);
        $this->assertFalse($this->obj_Stack->isEmpty());
        $this->obj_Stack->clear();
        $this->assertTrue($this->obj_Stack->isEmpty());
    }
}
?>