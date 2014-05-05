<?php

if( !defined('NIV') ){
  define('NIV', dirname(__FILE__) . '/../../../');
}

if( !class_exists('GeneralTest') ){
  require(NIV . 'tests/GeneralTest.php');
}

class testLanguage extends GeneralTest{

  private $service_Language;

  public function __construct(){
    parent::__construct();

    require_once(NIV . 'include/services/Language.inc.php');
    $this->loadStub('DummySettings');
    $this->loadStub('DummyCookie');
    $this->loadStub('DummyFile');
  }

  public function setUp(){
    parent::setUp();

    $service_Settings = new DummySettings();
    $service_Cookie = new DummyCookie();
    $service_File = new DummyFile();
    
    $service_Settings->setValue('settings/defaultLanguage', 'en');
    
    $this->service_Language = new \core\services\Language($service_Settings, $service_Cookie, $service_File);
  }

  public function tearDown(){
    $this->service_Language = null;

    parent::tearDown();
  }

  /**
   * Collects the installed languages
   *
   * @test
   */
  public function getLanguages(){
    $this->assertEquals(array(),$this->service_Language->getLanguages());
  }

  /**
   * Sets the language
   * 
   * @test
   * @expectedException IOException
   */
  public function setLanguage(){
    $this->service_Language->setLanguage('eqweqwew');
  }

  /**
   * Returns the set language
   *
   * @test
   */
  public function getLanguage(){
    $this->assertEquals('en',$this->service_Language->getLanguage());
  }

  /**
   * Returns the set encoding
   *
   * @test
   */
  public function getEncoding(){
    $this->assertEquals('iso-8859-1',$this->service_Language->getEncoding());
  }

  /**
   * Gives the asked part of the loaded file
   *
   * @test
   */
  public function get(){
    $this->service_Language->get('admin/index/title');
  }

  /**
   * Changes the language-values with the given values
   *
   * @test
   */
  public function insert(){
    $s_text = 'It is a [description] day. Lets go [activity]!';
    $s_expected = 'It is a beautiful day. Lets go walk!';
    
    $this->assertEquals($s_expected,$this->service_Language->insert($s_text,array('description','activity'),array('beautiful','walk')));
  }
}