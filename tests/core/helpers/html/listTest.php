<?php

if( !defined('NIV') ){
  define('NIV', dirname(__FILE__) . '/../../../../');
}

if( !class_exists('GeneralTest') ){
  require(NIV . 'tests/GeneralTest.php');
}

class testList extends GeneralTest{
  private $s_content = 'test list cell';
  private $listFactory;

  public function __construct(){
    parent::__construct();

    require_once(NIV . 'include/helpers/HTML.inc.php');

    $helper = new core\helpers\html\HTML();
    $this->listFactory  = $helper->ListFactory();
  }
  
  /**
   * Test of an empty unnumbered list
   * 
   * @test
   */
  public function emptyList(){
    $s_expected = "<ul ></ul>\n";
    $this->assertEquals($s_expected,$this->listFactory->uNumberedList()->generateItem());
  }
  
  /**
   * Test of an empty numbered list
   * 
   * @test
   */
  public function emptyNumberedList(){
    $s_expected = "<ol ></ol>\n";
    $this->assertEquals($s_expected,$this->listFactory->numberedList()->generateItem());
  }
  
  /**
   * Test of generating a list item
   * 
   * @test
   */
  public function listItem(){
    $s_expected = '<li >'.$this->s_content.'</li>';
    $this->assertEquals($s_expected,$this->listFactory->createItem($this->s_content)->generateItem());
  }
  
  /**
   * Test of a filled list
   * 
   * @test
   */
  public function filledList(){
    $s_expected = '<ul >';
    
    $object = $this->listFactory->uNumberedList();
    for($i=1; $i<=5; $i++){
      $object->addRow($this->listFactory->createItem($this->s_content));
      $s_expected .= '<li >'.$this->s_content."</li>\n";
    }
      
    $s_expected .= "</ul>\n";
    $this->assertEquals($s_expected,$object->generateItem());
  }
}