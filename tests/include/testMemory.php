<?php
define('NIV',dirname(__FILE__).'/../../');

require(NIV.'tests/include/GeneralTest.php');

class testMemory extends GeneralTest {
	/**
	 * Tests the protocol detection
	 */
	public function testGetProtocol(){
		$this->assertEquals('http://',Memory::getProtocol());
	}

	/**
	 * Tests the page detection
	 */
	public function testGetPage(){
		$this->assertEquals('/phpunit',Memory::getPage());
	}

	/**
	 * Tests the ajax mode
	 */
	public function testIsAjax() {
		$this->assertFalse(Memory::isAjax());
	}

	/**
	 * Tests the ajax mode
	 */
	public function testSetAjax() {
		$this->assertFalse(Memory::isAjax());
		
		Memory::setAjax();
		$this->assertTrue(Memory::isAjax());
	}

	/**
	 * Tests the base detection
	 */
	public function testGetBase() {
		$this->assertEquals($this->s_base,Memory::getBase());
	}

	/**
	 * Checks if the class gets loaded
	 */
	public function testEnsureClass(){
		$s_class = 'Queue';
		
		$this->assertFalse(class_exists($s_class));
		
		Memory::ensureClass($s_class);
		$this->assertTrue(class_exists($s_class));
	}

	/**
	 * Checks if a interface gets loaded
	 */
	public function testEnsureInterface(){
		$s_interface = 'Observer';
		
		$this->assertFalse(interface_exists($s_interface));
		
		Memory::ensureInterface($s_interface);
		$this->assertTrue(interface_exists($s_interface));
	}

	/**
	 * Tests the helper existance check
	 */
	public function testIsHelper() {
		$this->assertFalse(Memory::isHelper('lalalallaa'));
		$this->assertTrue(Memory::isHelper('UBB'));
	}

	/**
	 * Tests the helper loading
	 */
	public function testHelpers() {
		try {
			Memory::helpers('lalalallaa');
			
			$this->fail('Calling helper lalalallaa must throw a Memory exception.');
		}
		catch(MemoryException $e){}
		
		$helper = Memory::helpers('UBB');
		if( !($helper instanceof Helper_UBB) ){
			$this->fail('Called wrong helper. expected helper UBB');
		}
	}

	/**
	 * Tests the service existance check
	 */
	public function testIsService() {
		$this->assertFalse(Memory::isService('lalalallaa'));
		$this->assertTrue(Memory::isService('Template'));
	}

	/**
	 * Tests the service loading
	 */
	public function testServices() {
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
	 */
	public function testIsModel() {
		$this->assertFalse(Memory::isModel('lalalallaa'));
		$this->assertTrue(Memory::isModel('PM'));
	}

	/**
	 * Tests the service loading
	 */
	public function testModels() {
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
	 */
	public function testIsLoaded() {
		$this->assertTrue(Memory::isLoaded('service', 'XmlSettings'));
		$this->assertTrue(Memory::isLoaded('service', 'File'));
		
		Memory::models('Stats');
		$this->assertTrue(Memory::isLoaded('model', 'Stats'));
	}

	/**
	 * Test for the type check
	 */
	public function testType() {
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
	 */
	public function testDelete() {
		$this->assertTrue(Memory::isLoaded('service', 'File'));
		Memory::delete('service', 'File');
		$this->assertFalse(Memory::isLoaded('service', 'File'));
	}

	/**
	 * Test for the url creation 
	 */
	public function testGenerateUrl() {
		$s_url = 'lalalal.php';
		$this->assertEquals($this->s_base.$s_url, Memory::generateUrl('./../../'.$s_url));
	}
}
?>