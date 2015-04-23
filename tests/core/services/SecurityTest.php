<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testSecurity extends GeneralTest
{

    private $service_Security;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/services/Security.inc.php');
        
        $this->loadStub('DummyValidation');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Validation = new DummyValidation();
        $this->service_Security = new \core\services\Security($service_Validation);
    }

    public function tearDown()
    {
        $this->service_Security = null;
        
        parent::tearDown();
    }

    /**
     * Test for correct boolean value checking
     *
     * @test
     */
    public function secureBoolean()
    {
        $this->assertEquals('0', $this->service_Security->secureBoolean('0'));
        $this->assertEquals(1, $this->service_Security->secureBoolean(1));
        $this->assertFalse($this->service_Security->secureBoolean('-'));
    }

    /**
     * Test for correct int value checking
     *
     * @test
     */
    public function secureInt()
    {
        $this->assertEquals(0, $this->service_Security->secureInt('lalala'));
        $this->assertEquals(0, $this->service_Security->secureInt(- 10, true));
        $this->assertEquals(10, $this->service_Security->secureInt(10));
    }

    /**
     * Test for correct float value checking
     *
     * @test
     */
    public function secureFloat()
    {
        $this->assertEquals(0.00, $this->service_Security->secureFloat('lalala'));
        $this->assertEquals(0.00, $this->service_Security->secureFloat(- 10.1, true));
        $this->assertEquals(10.1, $this->service_Security->secureFloat(10.1));
    }

    /**
     * Disables code in the given string
     *
     * @test
     */
    public function secureString()
    {
        $this->assertEquals('gffdgq sdgfvserty3r dfgber5t6423wefv asfsdfsd', $this->service_Security->secureString('gffdgq sdgfvserty3r dfgber5t6423wefv asfsdfsd'));
        $this->assertNotEquals('gffdgq <script>sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>', $this->service_Security->secureString('gffdgq <script>sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>'));
        $this->assertNotEquals('gffdgq <span onclick="alert(\'test\')">sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>', $this->service_Security->secureString('gffdgq <span onclick="alert(\'test\')">sdgfvserty3r dfgber5t6423wefv asfsdfsd</script>'));
        $this->assertNotEquals('gffdgq sdgfvserty3r & dfgber5t6423wefv & asfsdfsd', $this->service_Security->secureString('gffdgq sdgfvserty3r & dfgber5t6423wefv & asfsdfsd'));
    }
}
?>
