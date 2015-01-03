<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/GeneralTest.php');

class testTemplateSettings extends GeneralTest {
  private $service_Settings;
  private $s_defaultDir = 'standard';
  
  public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/services/TemplateSettings.inc.php');
    $this->loadStub('DummySettings');
    $this->loadStub('DummyCookie');
    $this->loadStub('DummyFile');
	}

	public function setUp(){
		parent::setUp();
        
    $this->service_Settings = new DummySettings();
	}
	
	public function tearDown(){
		$this->service_Settings	= null;
		
		parent::tearDown();
	}
  
  private function initSettings($service_Cookie = null){
    $service_Settings = new DummySettings();
    if( is_null($service_Cookie) ){
      $service_Cookie = new DummyCookie();
    }
    $service_File = new DummyFile();
    
    $service_Settings->setValue('settings/templates/dir',$this->s_defaultDir);
    
		$this->service_Settings = new \core\services\TemplateSettings($service_File, $service_Settings, $service_Cookie);
    
  }
  
  /**
   * Returns the template directory without user user override
   * 
   * @test
   */
  public function loadDefault(){
    $this->initSettings();
    
    $this->assertEquals($this->s_defaultDir,$this->service_Settings->getTemplateDir());
  }
  
  /**
   * Returns the template directory with user user override
   * 
   * @test
   */
  public function loadUserSelected(){
    $s_dir = 'default';
    $_GET[ 'private_style_dir' ] = $s_dir;
    
    $this->initSettings();
    
    $this->assertEquals($s_dir,$this->service_Settings->getTemplateDir());
  }
  
  /**
   * Returns the template directory with user user override with a non existing directory
   * 
   * @test
   */
  public function loadUserSelectedInvalid(){
    $s_dir = 'myTemplatedir';
    $_GET[ 'private_style_dir' ] = $s_dir;
    
    $this->initSettings();
    
    $this->assertNotEquals($s_dir,$this->service_Settings->getTemplateDir());
  }
  
  /**
   * Returns the template directory with cookie override
   * 
   * @test
   */
  public function loadCookieSelected(){
    $s_dir = 'default';    
    $service_Cookie = new DummyCookie();
    $service_Cookie->set('private_style_dir', $s_dir, '/');
    $this->initSettings($service_Cookie);
    
    $this->assertEquals($s_dir,$this->service_Settings->getTemplateDir());
  }
  
  /**
   * Returns the template directory with cookie override with a non existing directory
   * 
   * @test
   */
  public function loadCookieSelectedInvalid(){
    $s_dir = 'myTemplatedir';
    $service_Cookie = new DummyCookie();
    $service_Cookie->set('private_style_dir', $s_dir, '/');
    $this->initSettings($service_Cookie);
    
    $this->assertNotEquals($s_dir,$this->service_Settings->getTemplateDir());
  }
  
  /**
   * Returns the loaded template directory
   *
   * @test
   */
  public function getStylesDir(){
    $this->initSettings();
    $s_expected = '/styles/' . $this->s_defaultDir . '/';
    
    $this->assertEquals($s_expected,$this->service_Settings->getStylesDir());
  }
}