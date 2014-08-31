<?php

if( !defined('NIV') ){
  define('NIV',dirname(__FILE__).'/../../../../');
}

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

require_once(NIV.'include/helpers/Helper.inc.php');
require_once(NIV.'include/helpers/HTML.inc.php');

class DummyCoreHtmlItem extends core\helpers\html\CoreHtmlItem {
	public function __construct($s_tag){
		$this->s_tag = $s_tag;
		$this->s_htmlType = 'html5';
	}	
}

class testCoreHtmlItem extends GeneralTest {
	private $obj_CoreHtmlItem;
	private $s_tag = '<testTag {between}>{value}</testTag>';
	
	private $s_id = 'testID';

	public function setUp(){
		parent::setUp();

		$this->obj_CoreHtmlItem	= new DummyCoreHtmlItem($this->s_tag);
	}

	public function tearDown(){
		$this->obj_CoreHtmlItem	= null;

		parent::tearDown();
	}
	
	/**
	 * Tests setting the id on the item.
	 *
	 * @test
	 */
	public function setID(){
		$this->obj_CoreHtmlItem->setID($this->s_id);
	
		$s_expected = '<testTag id="'.$this->s_id.'"></testTag>';
		$this->assertEquals($this->obj_CoreHtmlItem->generateItem(),$s_expected);
	}
	
	/**
	 * Test of setting a data item
	 * HTML 5 only
	 *
	 * @test
	 */
	public function setData(){
		$s_name = 'item';
		$s_value = 'item-value';
		
		$this->obj_CoreHtmlItem->setData($s_name,$s_value);
	
		$s_expected = '<testTag data-'.$s_name.'="'.$s_value.'"></testTag>';
		$this->assertEquals($this->obj_CoreHtmlItem->generateItem(),$s_expected);
	}
	
	/**
	 * Tests setting the rel-attribute
	 *
	 * @test
	 */
	public function setRelation(){
		$s_relation = 'my-relation';
		
		$this->obj_CoreHtmlItem->setRelation($s_relation);
		
		$s_expected = '<testTag rel="'.$s_relation.'"></testTag>';
		$this->assertEquals($this->obj_CoreHtmlItem->generateItem(),$s_expected);
	}
}