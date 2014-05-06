<?php

if( !defined('NIV') ){
  define('NIV', dirname(__FILE__) . '/../../../../');
}

if( !class_exists('GeneralTest') ){
  require(NIV . 'tests/GeneralTest.php');
}

class testDiv extends GeneralTest{

  private $helper_HTML;
  private $s_content = 'test content 2';

  public function __construct(){
    parent::__construct();

    require_once(NIV . 'include/helpers/HTML.inc.php');
    require_once(NIV . 'include/helpers/html/Div.php');

    $this->helper_HTML = new core\helpers\html\HTML();
  }

  /**
   * Tests generating a div
   * 
   * @test
   */
  public function div(){
    $s_expected = '<div >' . $this->s_content . '</div>';
    $this->assertEquals($s_expected, $this->helper_HTML->div($this->s_content)->generateItem());
  }

  /**
   * Tests generating a header block
   * 
   * @test
   */
  public function pageHeader(){
    $s_expected = "<header >\n" . $this->s_content . "\n</header>\n";
    $this->assertEquals($s_expected, $this->helper_HTML->pageHeader($this->s_content)->generateItem());
  }

  /**
   * Tests generating a footer block
   * 
   * @test
   */
  public function footer(){
    $s_expected = "<footer >\n" . $this->s_content . "\n</footer>\n";
    $this->assertEquals($s_expected, $this->helper_HTML->pageFooter($this->s_content)->generateItem());
  }

  /**
   * Tests generating a navigation item
   * 
   * @test
   */
  public function nav(){
    $s_expected = "<nav >\n" . $this->s_content . "\n</nav>\n";
    $this->assertEquals($s_expected, $this->helper_HTML->navigation($this->s_content)->generateItem());
  }

  /**
   * Tests generating an article item
   * 
   * @test
   */
  public function article(){
    $s_expected = "<article >\n".$this->s_content."\n</article>\n";
    $this->assertEquals($s_expected,$this->helper_HTML->article($this->s_content)->generateItem());
  }

  /**
   * Tests generating an article item
   * 
   * @test
   */
  public function section(){
    $s_expected = "<section >\n".$this->s_content."\n</section>\n";
    $this->assertEquals($s_expected,$this->helper_HTML->section($this->s_content)->generateItem());
  }
}