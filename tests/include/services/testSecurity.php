<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testSecurity extends GeneralTest {
	private $service_Security;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/services/Security.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_Security	= new Service_Security();
	}

	public function tearDown(){
		$this->service_FileData = null;

		parent::tearDown();
	}


	/**
	 * Test for correct boolean value checking
	 */
	public function testSecureBoolean(){
		$this->assertEquals('0', $this->service_Security->secureBoolean('0'));
		$this->assertEquals(1, $this->service_Security->secureBoolean(1));
		$this->assertFalse($this->service_Security->secureBoolean('-'));
	}

	/**
	 * Test for correct int value checking
	 */
	public function testSecureInt(){
		$this->assertEquals(0,$this->service_Security->secureInt('lalala'));
		$this->assertEquals(0,$this->service_Security->secureInt(-10,true));
		$this->assertEquals(10,$this->service_Security->secureInt(10));
	}

	/**
	 * Test for correct float value checking
	 */
	public function testSecureFloat($fl_input,$bo_positive = false){
		$this->assertEquals(0.00,$this->service_Security->secureFloat('lalala'));
		$this->assertEquals(0.00,$this->service_Security->secureFloat(-10.1,true));
		$this->assertEquals(10.1,$this->service_Security->secureFloat(10.1));
	}

	/**
	 * Disables code in the given string
	 *
	 * @param   string  $s_input The value to make safe
	 * @return  string  The secured value
	 */
	public function testSecureString(){
		$this->assertEquals('gffdgq sdgfvserty3r dfgber5t6423wefv asfsdfsd',$this->service_Security->secureString('gffdgq sdgfvserty3r dfgber5t6423wefv asfsdfsd'));
		$this->assertNotEquals('gffdgq <script>sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>',$this->service_Security->secureString('gffdgq <script>sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>'));
		$this->assertNotEquals('gffdgq <span onclick="alert(\'test\')">sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>',$this->service_Security->secureString('gffdgq <span onclick="alert(\'test\')">sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>'));
		$this->assertNotEquals('gffdgq sdgfvserty3r & dfgber5t6423wefv & asfsdfsd',$this->service_Security->secureString('gffdgq sdgfvserty3r & dfgber5t6423wefv & asfsdfsd'));
	}
	
	/**
	 * Test for validating an email address
	 */
	public function testCheckEmail(){
		$this->assertTrue($this->service_Security->checkEmail('admin@example.com'));
		$this->assertTrue($this->service_Security->checkEmail('admin32@example.museum'));
		$this->assertTrue($this->service_Security->checkEmail('admin_32-new@example-new.museum'));
		
		$this->assertFalse($this->service_Security->checkEmail('admin_32-newexample-new.museum'));
		$this->assertFalse($this->service_Security->checkEmail('admin_32-newe@xample-new'));
		$this->assertFalse($this->service_Security->checkEmail('admin 32-new@example-new.museum'));
	}

	/**
	 * Validates the given URI
	 *
	 * @param   string  $s_uri    The URI
	 * @return  boolean True if the URI is valid, otherwise false
	 */
	public function testCheckURI(){
		$this->assertTrue($this->service_Security->checkURI('http://example.com'));
		$this->assertTrue($this->service_Security->checkURI('http://example.com/sdfsdfs/wtewrfsfg/afsfsadfqw0-sdafsdf/index.cgi'));
		$this->assertTrue($this->service_Security->checkURI('http://example.com/sdfsdfs/wtewrfsfg/afsfsadfqw0-sdafsdf/index.cgi#top'));
		
		$this->assertFalse($this->service_Security->checkURI('http://example.com/sdfsdfs/wtew rfsfg/afsfsa dfqw0-sdafsdf/index.cgi'));
		$this->assertFalse($this->service_Security->checkURI('http://example#.com/sdfsdfs/wtew#rfsfg/afsfsa@dfqw0-sdafsdf/index.cgi'));
	}

	/**
	 * Test for validating dutch postal addresses
	 */
	public function testCheckPostalNL(){
		$this->assertTrue($this->service_Security->checkPostalNL('2341AA'));
		$this->assertTrue($this->service_Security->checkPostalNL('2341 AA'));
		$this->assertFalse($this->service_Security->checkPostalNL('AA 2341'));
		$this->assertFalse($this->service_Security->checkPostalNL('2341'));
	}

	/**
	 * Test for validating the given belgium postal addresses
	 */
	public function testCheckPostalBE(){
		$this->assertTrue($this->service_Security->checkPostalBE(1252));
		$this->assertFalse($this->service_Security->checkPostalBE('sdafsdfsd'));
		$this->assertFalse($this->service_Security->checkPostalBE(252));
		$this->assertFalse($this->service_Security->checkPostalBE(10000));
	}
}
?>
