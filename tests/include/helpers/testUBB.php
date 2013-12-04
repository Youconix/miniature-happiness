<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testString extends GeneralTest {
	private $helper_UBB;
	
	public function __construct(){
		parent::__construct();
		
		require_once(NIV.'include/helpers/UBB.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->helper_UBB	= new Helper_UBB();
	}
	
	public function tearDown(){
		$this->helper_UBB = null;
		
		parent::tearDown();
	}
	
	/**
	 * Test for parsing UBB into HTML
	 */
	public function testUBB(){
		$s_text	= '[center][p]  [url=index.php]Look here![/url] [img]images/lol.png[/img]  [/p] [/center] [url=home.php][img]home.png[/img][/url]';
		$s_expected	= '<div class="textCenter"><p>  <a href="index.php">Look here!</a> <img src="images/lol.png" alt=""/>  </p> </div> <a href="home.php"><img src="home.png" alt=""/></a>';
		
		$this->assertEquals($s_expected,$this->helper_UBB->parse($s_text));
	}
	
	/**
	 * Test for parsing the smileys
	 */
	public function testSmileys(){
		$s_text	= '[p] :) :( :@ [/p]';
		$s_expected	= '<p> <img src="'.NIV.'images/smileys/blij.png" alt=":)"> '.
			'<img src="'.NIV.'images/smileys/nietblij.png" alt=":("> '.
			'<img src="'.NIV.'images/smileys/angry.png" alt=":@"> </p>';
		
		$this->assertEquals($s_expected,$this->helper_UBB->parse($s_text));	
	}
}
?>