<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

class testHtmlFormItem extends \tests\GeneralTest
{

    private $obj_HtmlItem;

    private $s_tag = '<testTag {between}>{value}</testTag>';

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/helpers/Helper.inc.php');
        require_once (NIV . 'core/helpers/HTML.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->obj_HtmlItem = new \tests\stubs\helpers\html\HtmlFormItem($this->s_tag);
    }

    public function tearDown()
    {
        $this->obj_HtmlItem = null;
        
        parent::tearDown();
    }

    /**
     * Tests the enabled state
     *
     * @test
     */
    public function defaultState()
    {
        $s_expected = '<testTag ></testTag>';
        $this->assertEquals($s_expected, $this->obj_HtmlItem->generateItem());
    }

    /**
     * Enables or disables the item
     *
     * @test
     */
    public function setDisabled()
    {
        $this->obj_HtmlItem->setDisabled(true);
        
        $s_expected = '<testTag disabled="disabled"></testTag>';
        $this->assertEquals($s_expected, $this->obj_HtmlItem->generateItem());
    }
}
?>