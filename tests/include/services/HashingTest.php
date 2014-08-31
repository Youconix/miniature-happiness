<?php

if( !defined('NIV') ){
  define('NIV', dirname(__FILE__) . '/../../../');
}

if( !class_exists('GeneralTest') ){
  require(NIV . 'tests/GeneralTest.php');
}

class testHashing extends GeneralTest{

  private $s_text = 'test text';
  private $s_salt = 'adhqhi1g23wnlds cu2pkleibdj so;fjnasbuojewdsa';
  private $s_hashNormal = '$2y$10$YWRocWhpMWcyM3dubGRzI.H9.VfRz3FlRkk5lMByYspSs2fFp.jsm';
  private $s_username = 'test user';
  private $s_password = 'test password';
  private $s_hashPassword = '$2y$10$YWRocWhpMWcyM3dubGRzI.J4wPoW4CzDNSE3Nc03vWg1JWPwIY1vy';
  private $service_Hashing;

  public function __construct(){
    parent::__construct();

    require_once(NIV . 'include/services/Hashing.inc.php');
    $this->loadStub('DummyLogs');
    $this->loadStub('DummySettings');
    $this->loadStub('DummyRandom');
  }

  public function setUp(){
    parent::setUp();

    $service_Logs = new DummyLogs();
    $service_Settings = new DummySettings();
    $service_Random = new DummyRandom();

    $service_Settings->setValue('settings/main/salt', $this->s_salt);

    $this->service_Hashing = new \core\services\Hashing($service_Logs, $service_Settings, $service_Random);
  }

  public function tearDown(){
    $this->service_Hashing = null;

    parent::tearDown();
  }

  /**
   * Tests normal hasing
   * 
   * @test
   */
  public function hash(){
    $this->assertEquals($this->s_hashNormal, $this->service_Hashing->hash($this->s_text, $this->s_salt));
  }

  /**
   * Verifies the saved hash
   * 
   * @test
   */
  public function verify(){
    $this->assertTrue($this->service_Hashing->verify($this->s_text, $this->s_hashNormal, $this->s_salt));
  }

  /**
   * Tests user password hashing
   * 
   * @test
   */
  public function hashUserPassword(){
    $this->assertEquals($this->s_hashPassword, $this->service_Hashing->hashUserPassword($this->s_username, $this->s_password));
  }

  /**
   * Verifies the saved user password hash
   * 
   * @test
   */
  public function verifyUserPassword(){
    $this->assertTrue($this->service_Hashing->verifyUserPassword($this->s_username, $this->s_password, $this->s_hashPassword));
  }

}