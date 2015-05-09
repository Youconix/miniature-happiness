<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

class testCountries extends \tests\GeneralTest
{

    private $helper_countries;

    private $s_name = 'test country list';

    private $s_id = 'countryList';

    private $service_QueryBuilder;

    private $service_Language;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/helpers/Countries.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new \tests\stubs\database\DAL();
        $this->service_QueryBuilder = new \tests\stubs\services\QueryBuilder($service_Database);
        $this->service_Language = new \tests\stubs\services\Language();
        
        $this->helper_countries = new \core\helpers\Countries($this->service_QueryBuilder, $this->service_Language);
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
        $s_expected = '<select id="' . $this->s_id . '" name="' . $this->s_name . '">' . "\n</select>\n";
        $this->assertEquals($s_expected,$this->helper_countries->getList($this->s_name,$this->s_id));
    }
    
}