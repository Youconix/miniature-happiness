<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testStats extends GeneralTest {
	private $service_Database;
	private $model_Stats;
	private $i_month;
	private $i_date;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/Stats.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_Database = Memory::services('Database');

		$this->model_Stats = new Model_Stats();
		$this->i_date   = mktime(0,0,0,date("n"),1,date("Y"));
	}

	public function tearDown(){
		$this->model_Stats = null;

		parent::tearDown();
	}


    /**
     * Test saving the vistor hits
     */
    public function testSaveIP($s_ip,$s_page){
    	$_SERVER['HTTP_HOST']	= 'example.com';
    	$s_ip = '53.2543.536.24';
    	$s_page = 'index.php';
    	
    	$this->service_Database->transaction();
    	
    	$this->assertTrue($this->model_Stats->saveIP($s_ip, $s_page));  // unique vistor
    	$this->assertFalse($this->model_Stats->saveIP($s_ip, $s_page));	// repeating visitor
    	
    	$this->service_Database->rollback();
    }

    /**
     * Test saving the visitors OS
     * Shortend version
     */
    public function testSaveOS($s_os,$s_osType){
    	$s_os = 'Linux';
    	$s_osType = 'Unknown';
    	
    	try {
	    	$this->service_Database->transaction();
	    	
	    	$this->model_Stats->saveOS($s_os, $s_osType);
	    	
	    	$a_os	= $this->model_Stats->getOS($this->i_date);
	    	
	    	$this->service_Database->rollback();
    	}
    	catch(DBException $e){
				$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(is_array($a_os));
    	
    	$this->assertEquals($s_os,$a_os[$s_os]['name']);
    }
    
    /**
     * Test saving the visitors OS
     * Long version
     */
    public function testGetOSLong(){
    	$s_os = 'Linux';
    	$s_osType = 'Unknown';
    	
    	try {
	    	$this->service_Database->transaction();
	    	
	    	$this->model_Stats->saveOS($s_os, $s_osType);
	    	
	    	$a_os	= $this->model_Stats->getOSLong($this->i_date);
	    	
	    	$this->service_Database->rollback();
    	}
    	catch(DBException $e){
				$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$bo_found = false;
    	foreach($a_os AS $a_item){
    		if( $a_item['name'] == $s_os && $a_item['type'] == $s_osType ){
    			$bo_found = true;
    			break;
    		}
    	}
    	
    	$this->assertTrue($bo_found,"Missing OS ".$s_os." with type ".$s_osType.'.');
    }

    /**
     * Test saving the visitors browser
     * Shortend version
     */
    public function testSaveBrowser(){
    	$s_browser = 'Firefox';
    	$s_version = '14';
    	
    	try {
    		$this->service_Database->transaction();
    	
    		$this->model_Stats->saveBrowser($s_browser, $s_version);
    		
    		$a_browser = $this->model_Stats->getBrowsers($this->i_date);
    		
    		$this->service_Database->rollback();
    	}
    	catch(DBException $e){
    		$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());    		
    	}
    	
    	$this->assertTrue(array_key_exists($s_browser,$a_browser));
    }
    
    /**
     * Test saving the visitors browser
     * Long version
     */
    public function testGetBrowsersLong(){
    	$s_browser = 'Firefox';
    	$s_version = '14';
    	
    	try {
    		$this->service_Database->transaction();
    	
    		$this->model_Stats->saveBrowser($s_browser, $s_version);
    		
    		$a_browsers = $this->model_Stats->getBrowsersLong($this->i_date);
    		
    		$this->service_Database->rollback();
    	}
    	catch(DBException $e){
    		$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());    		
    	}
    	
    	$bo_found = false;
    	foreach($a_browsers AS $a_browser){
    		if( $a_browser['name'] == $s_browser && $a_browser['version'] == $s_version ){
    			$bo_found = true;
    			break;
    		}
    	}
    	
    	$this->assertTrue($bo_found,"Missing browser ".$s_browser." with version ".$s_version.'.');
    }

    /**
     * Test of saving the visitors reference
     */
    public function testSaveReference($s_reference){
    	$s_reference	= 'example2.com';
    	
    	try {
	    	$this->service_Database->transaction();
	    	
	    	$this->model_Stats->saveReference($s_reference);
	    	
	    	$a_references	= $this->model_Stats->getReferences($this->i_date);
	    	
	    	$this->service_Database->rollback();
    	}
    	catch(DBException $e){
    		$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(array_key_exists($s_reference,$a_references),'Missing reference '.$s_reference.'.');
    	$this->assertEquals($s_reference,$a_references[$s_reference]['name']);
    }

    /**
     * Test of saving the visitors screen size	
     */
    public function testSaveScreenSize($i_width,$i_height){
    	$i_width  = 1600;
    	$i_height = 1000;
    	
    	try {
    		$this->service_Database->transaction();
    		
    		$this->model_Stats->saveScreenSize($i_width,$i_height);
    		
    		$a_sizes	= $this->model_Stats->getScreenSizes($this->i_date);
    		
    		$this->service_Database->rollback();
    	}
    	catch(DBException $e){
    		$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$bo_found = false;
    	foreach($a_sizes AS $a_size){
    		if( $a_size['width'] == $i_width && $a_size['height'] == $i_height ){
    			$bo_found = true;
    			break;
    		}
    	}
    	
    	$this->assertTrue($bo_found,"Missing screen size with width ".$i_width." and height ".$i_height.'.');
    }

    /**
     * Test of saving the visitors screen colors
     */
    public function testSaveScreenColors(){
    	$s_screenColors = '356';
    	
    	try {
    		$this->service_Database->transaction();
    		
    		$this->model_Stats->saveScreenColors($s_screenColors);
    		
    		$a_colors	= $this->model_Stats->getScreenColors($this->i_date);
    		
    		$this->service_Database->rollback();
    	}
    	catch(DBException $e){
    		$this->service_Database->rollback();
    		
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(array_key_exists($s_screenColors,$a_colors),'Missing screen colors '.$s_screenColors.'.');
    	$this->assertEquals($s_screenColors, $a_colors[$s_screenColors]['name']);
    }

    /**
     * Test of collecting the hits from the given month
     */
    public function testGetHits($i_date){
    	try {
    		$a_hits = $this->model_Stats->getHits($this->i_date);
    	}
    	catch(DBException $e){
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(is_array($a_hits),'Expected that getHits returns an array');
    }
    
    /**
     * Test of collecting the pages from the given month
     */
    public function testGetPages(){
    	try {
    		$a_pages = $this->model_Stats->getPages($this->i_date);
    	}
    	catch(DBException $e){
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(is_array($a_pages),'Expected that getPages returns an array');
    }
    
    /**
     * Test of collecting the unique visitors from the given month
     */
    public function testGetUnique(){
    	try {
    		$a_unique = $this->model_Stats->getUnique($this->i_date);
    	}
    	catch(DBException $e){
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(is_array($a_unique),'Expected that getUnique returns an array');
    }

    /**
     * Test of returning the lowest date saved as a timestamp
     */
    public function testGetLowestDate(){
    	try {
    		$i_date	= $this->model_Stats->getLowestDate();
    	}
    	catch(DBException $e){
    		$this->fail("Exception : ".$e->getMessage());
    	}
    	
    	$this->assertTrue(is_int($i_date),'Expected that getLowestDate returns an int');
    }
}
?>
