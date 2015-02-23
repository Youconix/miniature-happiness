<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testCountries extends GeneralTest
{

    private $helper_countries;

    private $s_name = 'test country list';

    private $s_id = 'countryList';

    private $service_QueryBuilder;

    private $service_Language;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'include/helpers/Countries.inc.php');
        
        $this->loadStub('DummyDAL');
        $this->loadStub('DummyQueryBuilder');
        $this->loadStub('DummyLanguage');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new DummyDAL();
        $this->service_QueryBuilder = new DummyBuilder($service_Database);
        $this->service_Language = new DummyLanguage();
    }

    public function tearDown()
    {
        parent::tearDown();
        
        $this->helper_countries = null;
        $this->service_QueryBuilder = null;
        $this->service_Language = null;
    }

    /**
     * Tests a empty list
     *
     * @test
     */
    public function emptyList()
    {
        $this->createHelper();
        
        $s_expected = '<select id="' . $this->s_id . '" name="' . $this->s_name . '">' . "\n</select>\n";
        // $this->assertEquals($s_expected,$this->helper_countries->getList($this->s_name,$this->s_id));
    }

    private function createHelper()
    {
        echo ("test1");
        $this->helper_countries = new \core\helpers\Countries();
        echo ('test2');
        exit();
    }
}