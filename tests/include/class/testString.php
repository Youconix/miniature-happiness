<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testString extends GeneralTest {
	private $obj_String;
	private $s_data;
	private $s_data2;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/class/String.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->obj_String	= new String();
		$this->s_data	= 'sdgfswstrw35trwersdfghdfhgasdfsvddfg';
		$this->s_data2	= 'QWEfdgsd32q4erdsagfsdesarfsdf';
	}
	
	public function tearDown(){
		$this->obj_Stack = null;
		
		parent::tearDown();
	}

	/**
	 * Test of setting the initial value
	 */
	public function testConstruct(){
		$this->obj_String	= new String($this->s_data);
		$this->assertEquals($this->s_data,$this->obj_String->value());
	}

	/**
	 * Test of setting the value
	 */
	public function testSet(){
		$this->obj_String->set($this->s_data);
		$this->assertEquals($this->s_data,$this->obj_String->value());
	}

	/**
	 * Test of appending a value
	 */
	public function testAppend(){
		$s_append = '+append';
		
		$this->obj_String->set($this->s_data);
		$this->obj_String->append($s_append);
		
		$this->assertEquals($this->s_data.$s_append,$this->obj_String->value());
	}

	/**
	 * Test of the size
	 */
	public function testLength(){
		$this->assertEquals(0,$this->obj_String->length());
		$this->obj_String->set($this->s_data);
		
		$this->assertEquals(strlen($this->s_data),$this->obj_String->length());
	}

	/**
	 * Tests the value
	 */
	public function testValue(){
		$this->assertEquals('',$this->obj_String->value());
		$this->obj_String->set($this->s_data);
		$this->assertEquals($this->s_data,$this->obj_String->value());
	}

	/**
	 * Test of the string starting check
	 */
	public function testStartsWith($s_text){
		$this->obj_String->set($this->s_data);
		$this->obj_String->append('sdkjhsdjghsdlfadsjkhvbadfasdfa');
		$this->obj_String->append('ssdl,vnml d329-p0rifojnsajfs[d,afgadf');
		$this->obj_String->append('sjdkhfsfajkfnhbafdsd');
		
		
		$this->assertTrue($this->obj_String->startsWith($this->s_data));
	}

	/**
	 * Test of the string ending check
	 */
	public function testEndsWith(){		
		$this->obj_String->set('sdkjhsdjghsdlfadsjkhvbadfasdfa');
		$this->obj_String->append('ssdl,vnml d329-p0rifojnsajfs[d,afgadf');
		$this->obj_String->append('sjdkhfsfajkfnhbafdsd');
		$this->obj_String->append($this->s_data);
		
		$this->assertTrue($this->obj_String->endsWith($this->s_data));
	}

	/**
	 * Test for the containing check
	 */
	public function testContains(){		
		$this->obj_String->set('sdkjhsdjghsdlfadsjkhvbadfasdfa');
		$this->obj_String->append('ssdl,vnml d329-p0rifojnsajfs[d,afgadf');
		$this->obj_String->append($this->s_data2);
		$this->obj_String->append($this->s_data);
		
		$this->assertFalse($this->obj_String->contains(strtoupper($this->s_data2),false));
		$this->assertTrue($this->obj_String->contains(strtoupper($this->s_data2)));
	}

	/**
	 * Test for case sensitive comparison
	 */
	public function testEquals(){
		$this->obj_String->set(strtolower($this->s_data2));
		$this->obj_String->append($this->s_data);
		
		$this->assertTrue($this->obj_String->equals(strtolower($this->s_data2).$this->s_data));
		$this->assertFalse($this->obj_String->equals($this->s_data2.$this->s_data));
	}

	/**
	 * Test for case insensitive comparison
	 */
	public function testEqualsIgnoreCase(){		
		$this->obj_String->set(strtolower($this->s_data2));
		$this->obj_String->append($this->s_data);
		
		$this->assertTrue($this->obj_String->equalsIgnoreCase(strtolower($this->s_data2).$this->s_data));
		$this->assertTrue($this->obj_String->equalsIgnoreCase($this->s_data2.$this->s_data));
	}

	/**
	 * Test of searching the location of a string
	 */
	public function testIndexOf(){
		$this->obj_String->set($this->s_data);
		$this->assertEquals(-1,$this->obj_String->indexOf('=-2342354234252'));
		$this->assertEquals(2,$this->obj_String->indexOf('gfsw'));
	}

	/**
	 * Test for the empty check
	 */
	public function testIsEmpty(){
		$this->assertTrue($this->obj_String->isEmpty());
		$this->obj_String->set($this->s_data);
		$this->assertFalse($this->obj_String->isEmpty());
	}

	/**
	 * Test of removes the spaces at the begin and end
	 */
	public function testTrim(){
		$this->obj_String->set('           ');
		$this->obj_String->append($this->s_data);
		$this->assertNotEquals($this->s_data,$this->obj_String->value());
		$this->assertEquals($this->s_data,$this->obj_String->trim());
	}

	/**
	 * Test of replacing the given search with the given text if the value contains the given search
	 */
	public function testReplace(){
		$this->obj_String->set('           ');
		$this->obj_String->append($this->s_data);
		$this->obj_String->replace('           ',$this->s_data2);
		$this->assertEquals($this->s_data2.$this->s_data,$this->obj_String->value());
	}

	/**
	 * Test of returning a part from the set value
	 */
	public function testSubstring(){
		$this->obj_String->set($this->s_data);
		$this->assertEquals('fhgasdfsvddfg',$this->obj_String->substring(-13));
	}

	/**
	 * Test of cloning the object
	 */
	public function testCopy(){
		$this->obj_String->set($this->s_data);
		$obj_string	= $this->obj_String->copy();
		$this->assertEquals($obj_string->value(),$this->obj_String->value());
		
		$obj_string->append($this->s_data2);
		$this->assertNotEquals($obj_string,$this->obj_String);
	}
}
?>