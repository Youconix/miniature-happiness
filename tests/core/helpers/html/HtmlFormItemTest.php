<?php

if( !defined('NIV') ){
  define('NIV',dirname(__FILE__).'/../../../../');
}

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

require_once(NIV.'include/helpers/Helper.inc.php');
require_once(NIV.'include/helpers/HTML.inc.php');

class DummyHtmlFormItem extends core\helpers\html\HtmlFormItem {
	public function __construct($s_tag){
		$this->s_tag = $s_tag;
		$this->s_htmlType = 'html5';
	}	
}

class testHtmlFormItem extends GeneralTest {
	private $obj_HtmlItem;
	private $s_tag = '<testTag {between}>{value}</testTag>';

	public function setUp(){
		parent::setUp();

		$this->obj_HtmlItem	= new DummyHtmlFormItem($this->s_tag);
	}

	public function tearDown(){
		$this->obj_HtmlItem	= null;

		parent::tearDown();
	}
  
  /**
   * Tests the enabled state
   * 
   * @test
   */
  public function defaultState(){
     $s_expected = '<testTag ></testTag>';
    $this->assertEquals($s_expected,$this->obj_HtmlItem->generateItem());
  }
	
  /**
	 * Enables or disables the item
	 *
	 * @test
	 */
	public function setDisabled(){
		$this->obj_HtmlItem->setDisabled(true);
    
    $s_expected = '<testTag disabled="disabled"></testTag>';
    $this->assertEquals($s_expected,$this->obj_HtmlItem->generateItem());
	}
}
?>