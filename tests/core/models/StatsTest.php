<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testStats extends GeneralTest
{

    private $service_Builder;

    private $model_Stats;

    private $i_date;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'include/models/Stats.inc.php');
        $this->loadStub('DummyDAL');
        $this->loadStub('DummyQueryBuilder');
        $this->loadStub('DummySecurity');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new DummyDAL();
        $service_Security = new DummySecurity($service_Database);
        $this->service_Builder = new DummyQueryBuilder($service_Database);
        
        $this->model_Stats = new \core\models\Stats($this->service_Builder, $service_Security);
        $this->i_date = mktime(0, 0, 0, date("n"), 1, date("Y"));
    }

    public function tearDown()
    {
        $this->model_Stats = null;
        
        parent::tearDown();
    }

    /**
     * Test saving the vistor hits
     *
     * @test
     */
    public function saveIP()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $s_ip = '53.2543.536.24';
        $s_page = 'index.php';
        
        $builder = $this->service_Builder->createBuilder();
        
        $this->assertTrue($this->model_Stats->saveIP($s_ip, $s_page)); // unique vistor
        
        $builder->getDatabase()->i_affectedRows = 1;
        
        $this->model_Stats->saveIP($s_ip, $s_page); // repeating visitor
        
        $this->assertEquals(4, $builder->i_update);
        $this->assertEquals(4, $builder->i_insert);
        $this->assertEquals(2, $builder->i_select);
    }

    /**
     * Test saving the visitors OS
     *
     * @test
     */
    public function saveOS()
    {
        $s_os = 'Linux';
        $s_osType = 'Unknown';
        
        $builder = $this->service_Builder->createBuilder();
        
        $this->model_Stats->saveOS($s_os, $s_osType);
        $builder->getDatabase()->i_affectedRows = 1;
        $this->model_Stats->saveOS($s_os, $s_osType);
        
        $this->assertEquals(2, $builder->i_update);
        $this->assertEquals(1, $builder->i_insert);
    }

    /**
     * Test saving the visitors browser
     *
     * @test
     */
    public function saveBrowser()
    {
        $s_browser = 'Firefox';
        $s_version = '14';
        
        $builder = $this->service_Builder->createBuilder();
        
        $this->model_Stats->saveBrowser($s_browser, $s_version);
        $builder->getDatabase()->i_affectedRows = 1;
        $this->model_Stats->saveBrowser($s_browser, $s_version);
        
        $this->assertEquals(2, $builder->i_update);
        $this->assertEquals(1, $builder->i_insert);
    }

    /**
     * Test of saving the visitors reference
     *
     * @test
     */
    public function saveReference()
    {
        $s_reference = 'example2.com';
        
        $builder = $this->service_Builder->createBuilder();
        
        $this->model_Stats->saveReference($s_reference);
        $builder->getDatabase()->i_affectedRows = 1;
        $this->model_Stats->saveReference($s_reference);
        
        $this->assertEquals(2, $builder->i_update);
        $this->assertEquals(1, $builder->i_insert);
    }

    /**
     * Test of saving the visitors screen size
     *
     * @test
     */
    public function saveScreenSize()
    {
        $i_width = 1600;
        $i_height = 1000;
        
        $builder = $this->service_Builder->createBuilder();
        
        $this->model_Stats->saveScreenSize($i_width, $i_height);
        $builder->getDatabase()->i_affectedRows = 1;
        $this->model_Stats->saveScreenSize($i_width, $i_height);
        
        $this->assertEquals(2, $builder->i_update);
        $this->assertEquals(1, $builder->i_insert);
    }

    /**
     * Test of saving the visitors screen colors
     *
     * @test
     */
    public function saveScreenColors()
    {
        $s_screenColors = '356';
        
        $builder = $this->service_Builder->createBuilder();
        
        $this->model_Stats->saveScreenColors($s_screenColors);
        $builder->getDatabase()->i_affectedRows = 1;
        $this->model_Stats->saveScreenColors($s_screenColors);
        
        $this->assertEquals(2, $builder->i_update);
        $this->assertEquals(1, $builder->i_insert);
    }

    /**
     * Test of collecting the hits from the given month
     *
     * @test
     */
    public function getHits()
    {
        $this->assertEquals(array(), $this->model_Stats->getHits($this->i_date));
    }

    /**
     * Test of collecting the pages from the given month
     *
     * @test
     */
    public function getPages()
    {
        $this->assertEquals(array(), $this->model_Stats->getPages($this->i_date));
    }

    /**
     * Test of collecting the unique visitors from the given month
     *
     * @test
     */
    public function getUnique()
    {
        $this->assertEquals(array(), $this->model_Stats->getUnique($this->i_date));
    }

    /**
     * Test of returning the lowest date saved as a timestamp
     *
     * @test
     */
    public function getLowestDate()
    {
        $this->assertEquals(- 1, $this->model_Stats->getLowestDate());
        
        $i_expected;
        
        $builder = $this->service_Builder->createBuilder();
        $builder->getDatabase()->i_numRows = 1;
        $builder->getDatabase()->a_data = array(
            0 => array(
                'date' => $i_expected
            )
        );
        
        $this->assertEquals($i_expected, $this->model_Stats->getLowestDate());
    }
}
?>
