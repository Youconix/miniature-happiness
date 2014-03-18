<?php
define('NIV',dirname(__FILE__).'/../../../../');

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}


require_once(NIV.'include/helpers/Helper.inc.php');
require_once(NIV.'include/helpers/HTML.inc.php');
echo('test1'); 
class DummyHtmlItem extends HtmlItem {
	public function __construct($s_tag){
		$this->s_tag = $s_tag;
		$this->s_htmlType = 'html5';
	}	
}
echo('test2');

class testCoreHtmlItem extends GeneralTest {
	private $obj_HtmlItem;
	private $s_tag = '<testTag {between}>{value}</testTag>';
	
	public function __construct(){
		echo('starting');
	}
/*
	public function setUp(){
		parent::setUp();

		$this->obj_HtmlItem	= new DummyHtmlItem($this->s_tag);
		echo('test3');
	}

	public function tearDown(){
		$this->obj_HtmlItem	= null;

		parent::tearDown();
	}
	
	/**
	 * Tests setting the given event on the item
	 *
	 * @test
	 * /
	public function setEvent(){
		$s_name = 'onclick';
		$s_value = 'alert(\"hoi\");';
		
		$this->obj_HtmlItem->setEvent($s_name,$s_value);
		
		$this->assertEquals($this->obj_HtmlItem->generateItem(),'<testTag '.$s_name.'="'.$s_value.'"></testTag>');
	}
	
	/**
	 * Tests setting the style on the item.  Adds the style if a style is allready active
	 *
	 * @test
	 * /
	public function setStyle(){
		$s_style="display:none";
		$s_style2="border:red";
		
		$this->obj_HtmlItem->setStyle($s_style);
		$this->obj_HtmlItem->setStyle($s_style2);
	
		$this->assertEquals($this->obj_HtmlItem->generateItem(),'<testTag style="'.$s_style.'; '.$s_style2.'"></testTag>');
	}
	
	/**
	 * Tests setting the class on the item.  Adds the class if a class is allready active
	 *
	 * @test
	 * /
	public function setClass($s_class){
		$s_class="class1";
		$s_class2="class2";
		
		$this->obj_HtmlItem->setClass($s_class);
		$this->obj_HtmlItem->setClass($s_class2);
	
		$this->assertEquals($this->obj_HtmlItem->generateItem(),'<testTag class="'.$s_class.' '.$s_class2.'"></testTag>');
	}
	
	/**
	 * Tests the value on the item.  Adds the value if a value is allready set
	 *
	 * @test
	 * /
	public function setValue(){
		$s_value = 'lalallalalala';
		$s_value2 = 'mimimimi';
	
		$this->obj_HtmlItem->setValue($s_value);
		$this->obj_HtmlItem->setValue($s_value2);
	
		$this->assertEquals($this->obj_HtmlItem->generateItem(),'<testTag >'.$s_value.$s_value2.'</testTag>');
	} */
}
?>