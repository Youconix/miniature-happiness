<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testDate extends GeneralTest {
	private $helper_Date;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/helpers/Date.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->helper_Date	= new Helper_Date();
		date_default_timezone_set('UTC');
	}

	public function tearDown(){
		$this->helper_Date	= null;

		parent::tearDown();
	}
	/**
	 * Test of calculating the timestamp from the given parameters
	 */
	public function testGetTime(){
		$this->assertEquals(time(),$this->helper_Date->getTime());
		$this->assertEquals(0,$this->helper_Date->getTime(0,0,0,1,1,1970));
		$this->assertEquals(mktime(1, 1, 1, 1, 1, 1),$this->helper_Date->getTime(1,1,1,1,1,1));
	}

	/**
	 * Test for calulation the timestamp in the future
	 */
	public function testGetFutureTime($i_days=0,$i_months=0,$i_years = 0 ,$i_hours = 0,$i_minutes = 0,$i_seconds = 0){
		$this->assertEquals(time(),$this->helper_Date->getFutureTime());
		$this->assertEquals((time()+86400),$this->helper_Date->getFutureTime(1));
		$this->assertEquals((time()+86399),$this->helper_Date->getFutureTime(0,0,0,23,59,59));
	}

	/**
	 * Test of calculation the timestamp from now added with the given seconds
	 */
	public function testGetTimeFrom(){
		$this->assertEquals(time(),$this->helper_Date->getTimeFrom());
		
		$i_seconds = 54352423;
		$this->assertEquals((time()+$i_seconds),$this->helper_Date->getTimeFrom($i_seconds));
	}

	/**
	 * Test of calculating the seconds in a day
	 */
	public function testGetDaySeconds(){
		$this->assertEquals(86400,$this->helper_Date->getDaySeconds());
	}

	/**
	 * Test of calculating the seconds in a week
	 */
	public function testGetWeekSeconds(){
		$this->assertEquals((86400*7),$this->helper_Date->getWeekSeconds());
	}

	/**
	 * Test of calculating the current timestamp
	 */
	public function testNow(){
		$this->assertEquals(time(),$this->helper_Date->now());
	}

	/**
	 * Test for validating the date and time
	 */
	public function testValidateDateTime(){
		$this->assertTrue($this->helper_Date->validateDateTime(1, 1, 2000, 23, 23, 23));
		$this->assertFalse($this->helper_Date->validateDateTime(32, 1, 2000, 23, 23, 23));
	}

	/**
	 * Test for validating the time
	 */
	public function testValidateTime(){
		$this->assertTrue($this->helper_Date->validateTime(23, 23, 23));
		$this->assertFalse($this->helper_Date->validateTime(24, 23, 23));
	}

	/**
	 * Test for validating the date
	 */
	public function testValidateDate(){
		$this->assertTrue($this->helper_Date->validateDate(1, 1, 2000));
		$this->assertFalse($this->helper_Date->validateDate(30, 2, 2000));
	}

	/**
	 * Test for getting  the number of days from the given month
	 */
	public function testGetDaysMonth(){
		$i_year = 2000;
		
		$this->assertEquals(31,$this->helper_Date->getDaysMonth(3,$i_year));
		$this->assertEquals(30,$this->helper_Date->getDaysMonth(4,$i_year));
		$this->assertEquals(29,$this->helper_Date->getDaysMonth(2,$i_year));
		$this->assertEquals(28,$this->helper_Date->getDaysMonth(2,($i_year-1) ));
	}
}
?>