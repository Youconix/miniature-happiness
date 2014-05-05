<?php

if( !defined('NIV') ){
  define('NIV', dirname(__FILE__) . '/../../');
}

if( !class_exists('GeneralTest') ){
  require(NIV . 'tests/GeneralTest.php');
}

class testMemory extends GeneralTest{

  /**
   * Tests the protocol detection
   * 
   * @test
   */
  public function getProtocol(){
    $this->assertEquals('http://', \core\Memory::getProtocol());
  }

  /**
   * Tests the page detection
   * 
   * @test
   */
  public function getPage(){
    $a_page = explode('/', \core\Memory::getPage());
    $this->assertEquals('phpunit', end($a_page));
  }

  /**
   * Tests the ajax mode
   * 
   * @test
   */
  public function isAjax(){
    $this->assertFalse(\core\Memory::isAjax());
  }

  /**
   * Tests the ajax mode
   * 
   * @test
   */
  public function setAjax(){
    $this->assertFalse(\core\Memory::isAjax());

    \core\Memory::setAjax();
    $this->assertTrue(\core\Memory::isAjax());
  }

  /**
   * Tests the base detection
   * 
   * @test
   */
  public function getBase(){
    $this->assertEquals($this->s_base, \core\Memory::getBase());
  }

  /**
   * Checks if the class gets loaded
   * 
   * @test
   */
  public function ensureClass(){
    $s_class = 'Queue';

    if( !class_exists($s_class) ){
      \core\Memory::ensureClass($s_class);
    }

    $this->assertTrue(class_exists('\core\classes\\' . $s_class));
  }

  /**
   * Checks if a interface gets loaded
   * 
   * @test
   */
  public function ensureInterface(){
    $s_interface = 'Observer';

    $this->assertFalse(interface_exists($s_interface));

    \core\Memory::ensureInterface($s_interface);
    $this->assertTrue(interface_exists($s_interface));
  }

  /**
   * Tests the helper existance check
   * 
   * @test
   */
  public function isHelper(){
    $this->assertFalse(\core\Memory::isHelper('lalalallaa'));
    $this->assertTrue(\core\Memory::isHelper('UBB'));
  }

  /**
   * Tests the helper loading
   * 
   * @test
   */
  public function helpers(){
    $helper = \core\Memory::helpers('Date');
    $this->assertInstanceOf('\core\helpers\Date', $helper);

    try{
      \core\Memory::helpers('lalalallaa');

      $this->fail('Calling helper lalalallaa must throw a Memory exception.');
    }
    catch( MemoryException $e ){
      
    }
  }

  /**
   * Tests the service existance check
   * 
   * @test
   */
  public function testIsService(){
    $this->assertFalse(\core\Memory::isService('lalalallaa'));
    $this->assertTrue(\core\Memory::isService('Template'));
  }

  /**
   * Tests the service loading
   * 
   * @test
   */
  public function services(){
    try{
      \core\Memory::services('lalalallaa');

      $this->fail('Calling service lalalallaa must throw a Memory exception.');
    }
    catch( MemoryException $e ){
      
    }

    $service = \core\Memory::services('Random');
    $this->assertInstanceOf('\core\services\Random', $service);
  }

  /**
   * Tests the model existance check
   * 
   * @test
   */
  public function isModel(){
    $this->assertFalse(\core\Memory::isModel('lalalallaa'));
    $this->assertTrue(\core\Memory::isModel('PM'));
  }

  /**
   * Tests the service loading
   * 
   * @expectedException MemoryException
   * @test
   */
  public function models(){
    \core\Memory::models('lalalallaa');
  }

  /**
   * Tests the loaded check
   * 
   * @test
   */
  public function isLoaded(){
    $this->assertTrue(\core\Memory::isLoaded('service', 'File'));
  }

  /**
   * Test for the type check
   * 
   * @test
   */
  public function type(){
    try{
      \core\Memory::type('string', null);

      $this->fail("Expected a nullpointer exception");
    }
    catch( NullPointerException $e ){
      
    }

    try{
      \core\Memory::type('int', 'lalala');

      $this->fail("Expected a type exception");
    }
    catch( TypeException $e ){
      
    }

    \core\Memory::type('array', array());
  }

  /**
   * Test for deleting a object from the memory
   * 
   * @test
   */
  public function delete(){
    $this->assertTrue(\core\Memory::isLoaded('service', 'File'));
    \core\Memory::delete('service', 'File');
    $this->assertFalse(\core\Memory::isLoaded('service', 'File'));
  }

  /**
   * Test for the url creation 
   * 
   * @test
   */
  public function generateUrl(){
    $s_url = 'lalalal.php';
    $this->assertEquals($this->s_base . $s_url, \core\Memory::generateUrl('./../../' . $s_url));
  }

}
?>