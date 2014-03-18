<?php
define('NIV',dirname(__FILE__).'/../../');

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

class testMemory extends GeneralTest {
	/**
	 * Tests the protocol detection
	 * 
	 * @test
	 */
	public function getProtocol(){
		$this->assertEquals('http://',Memory::getProtocol());
	}

	/**
	 * Tests the page detection
	 * 
	 * @test
	 */
	public function getPage(){
		$a_page	= explode('/',Memory::getPage());
		$this->assertEquals('phpunit',end($a_page));
	}

	/**
	 * Tests the ajax mode
	 * 
	 * @test
	 */
	public function isAjax(){
		$this->assertFalse(Memory::isAjax());
	}

	/**
	 * Tests the ajax mode
	 * 
	 * @test
	 */
	public function setAjax(){
		$this->assertFalse(Memory::isAjax());
		
		Memory::setAjax();
		$this->assertTrue(Memory::isAjax());
	}

	/**
	 * Tests the base detection
	 * 
	 * @test
	 */
	public function getBase(){
		$this->assertEquals($this->s_base,Memory::getBase());
	}

	/**
	 * Checks if the class gets loaded
	 * 
	 * @test
	 */
	public function ensureClass(){
		$s_class = 'Queue';
		
		if( !class_exists($s_class) ){
			Memory::ensureClass($s_class);
		}
		
		$this->assertTrue(class_exists($s_class));
	}

	/**
	 * Checks if a interface gets loaded
	 * 
	 * @test
	 */
	public function ensureInterface(){
		$s_interface = 'Observer';
		
		$this->assertFalse(interface_exists($s_interface));
		
		Memory::ensureInterface($s_interface);
		$this->assertTrue(interface_exists($s_interface));
	}

	/**
	 * Tests the helper existance check
	 * 
	 * @test
	 */
	public function isHelper(){
		$this->assertFalse(Memory::isHelper('lalalallaa'));
		$this->assertTrue(Memory::isHelper('UBB'));
	}

	/**
	 * Tests the helper loading
	 * 
	 * @test
	 */
	public function helpers(){
		$helper = Memory::helpers('UBB');
		if( !($helper instanceof Helper_UBB) ){
			$this->fail('Called wrong helper. expected helper UBB');
		}
		
		try {
			Memory::helpers('lalalallaa');
			
			$this->fail('Calling helper lalalallaa must throw a Memory exception.');
		}
		catch(MemoryException $e){}
	}

	/**
	 * Tests the service existance check
	 */
	public function testIsService(){
		$this->assertFalse(Memory::isService('lalalallaa'));
		$this->assertTrue(Memory::isService('Template'));
	}

	/**
	 * Tests the service loading
	 * 
	 * @test
	 */
	public function services(){
		try {
			Memory::services('lalalallaa');
			
			$this->fail('Calling service lalalallaa must throw a Memory exception.');
		}
		catch(MemoryException $e){}
		
		$service = Memory::services('Cookie');
		if( !($service instanceof Service_Cookie) ){
			$this->fail('Called wrong service. expected service Cookie');
		}
	}

	/**
	 * Tests the model existance check
	 * 
	 * @test
	 */
	public function isModel(){
		$this->assertFalse(Memory::isModel('lalalallaa'));
		$this->assertTrue(Memory::isModel('PM'));
	}

	/**
	 * Tests the service loading
	 * 
	 * @test
	 */
	public function models(){
		try {
			Memory::models('lalalallaa');
			
			$this->fail('Calling model lalalallaa must throw a Memory exception.');
		}
		catch(MemoryException $e){}
		
		$model = Memory::models('PM');
		if( !($model instanceof Model_PM) ){
			$this->fail('Called wrong model. expected modem PM');
		}
	}

	/**
	 * Tests the loaded check
	 * 
	 * @test
	 */
	public function isLoaded(){
		$this->assertTrue(Memory::isLoaded('service', 'XmlSettings'));
		$this->assertTrue(Memory::isLoaded('service', 'File'));
		
		Memory::models('Stats');
		$this->assertTrue(Memory::isLoaded('model', 'Stats'));
	}

	/**
	 * Test for the type check
	 * 
	 * @test
	 */
	public function type(){
		try {
			Memory::type('string', null);
			
			$this->fail("Expected a nullpointer exception");
		}
		catch(NullPointerException $e){}
		
		try {
			Memory::type('int','lalala');
			
			$this->fail("Expected a type exception");
		}
		catch(TypeException $e){}
		
		Memory::type('array',array());
	}

	/**
	 * Test for deleting a object from the memory
	 * 
	 * @test
	 */
	public function delete(){
		$this->assertTrue(Memory::isLoaded('service', 'File'));
		Memory::delete('service', 'File');
		$this->assertFalse(Memory::isLoaded('service', 'File'));
	}

	/**
	 * Test for the url creation 
	 * 
	 * @test
	 */
	public function generateUrl(){
		$s_url = 'lalalal.php';
		$this->assertEquals($this->s_base.$s_url, Memory::generateUrl('./../../'.$s_url));
	}
}
?>