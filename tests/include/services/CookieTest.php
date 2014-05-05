<?php

if( !defined('NIV') ){
  define('NIV', dirname(__FILE__) . '/../../../');
}

if( !class_exists('GeneralTest') ){
  require(NIV . 'tests/GeneralTest.php');
}

class testCookie extends GeneralTest{

  private $service_Security;
  private $service_Cookie;
  private $s_name;
  private $s_data;
  private $s_domain;

  public function __construct(){
    parent::__construct();

    require_once(NIV . 'include/services/Cookie.inc.php');
    $this->loadStub('DummyDAL');
    $this->loadStub('DummySecurity');

    $service_DAL = new DummyDAL();
    $this->service_Security = new DummySecurity($service_DAL);
  }

  public function setUp(){
    parent::setUp();

    $this->service_Cookie = new \core\services\Cookie($this->service_Security);

    $this->s_name = 'testCookie';
    $this->s_data = 'lalalala';
    $this->s_domain = '/';

    unset($_COOKIE[ $this->s_name ]);
  }

  public function tearDown(){
    $this->service_Cookie = null;

    parent::tearDown();
  }

  /**
   * Tests the deleting of a cookie
   * 
   * @test
   */
  public function delete(){
    $this->service_Cookie->set($this->s_name, $this->s_data, $this->s_domain);
    $this->assertTrue(isset($_COOKIE[ $this->s_name ]), 'Cookie ' . $this->s_name . ' should exist.');

    $this->service_Cookie->delete($this->s_name, $this->s_domain);

    $this->assertFalse(isset($_COOKIE[ $this->s_name ]), 'Cookie ' . $this->s_name . ' should not exist.');
  }

  /**
   * Test setting a cookie
   * 
   * @test
   */
  public function set(){
    $this->service_Cookie->set($this->s_name, $this->s_data, $this->s_domain);

    $this->assertTrue(isset($_COOKIE[ $this->s_name ]), 'Cookie ' . $this->s_name . ' should exist.');
  }

  /**
   * Tests the retreaval of a cookie
   * 
   * @test
   */
  public function get(){
    $this->service_Cookie->set($this->s_name, $this->s_data, $this->s_domain);
    $this->assertEquals($this->s_data, $this->service_Cookie->get($this->s_name));
  }

  /**
   * Test the cookie existing check
   * 
   * @test
   */
  public function exists(){
    $this->assertFalse($this->service_Cookie->exists($this->s_name));

    $this->service_Cookie->set($this->s_name, $this->s_data, $this->s_domain);

    $this->assertTrue($this->service_Cookie->exists($this->s_name));
  }

}
?>