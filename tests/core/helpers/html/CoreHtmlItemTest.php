<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

class testCoreHtmlItem extends \tests\GeneralTest
{

    private $obj_CoreHtmlItem;

    private $s_tag = '<testTag {between}>{value}</testTag>';

    private $s_id = 'testID';

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/helpers/Helper.inc.php');
        require_once (NIV . 'core/helpers/HTML.inc.php');      
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->obj_CoreHtmlItem = new \tests\stubs\helpers\html\CoreHtmlItem($this->s_tag);
    }

    public function tearDown()
    {
        $this->obj_CoreHtmlItem = null;
        
        parent::tearDown();
    }

    /**
     * Tests setting the id on the item.
     *
     * @test
     */
    public function setID()
    {
        $this->obj_CoreHtmlItem->setID($this->s_id);
        
        $s_expected = '<testTag id="' . $this->s_id . '"></testTag>';
        $this->assertEquals($this->obj_CoreHtmlItem->generateItem(), $s_expected);
    }

    /**
     * Test of setting a data item
     * HTML 5 only
     *
     * @test
     */
    public function setData()
    {
        $s_name = 'item';
        $s_value = 'item-value';
        
        $this->obj_CoreHtmlItem->setData($s_name, $s_value);
        
        $s_expected = '<testTag data-' . $s_name . '="' . $s_value . '"></testTag>';
        $this->assertEquals($this->obj_CoreHtmlItem->generateItem(), $s_expected);
    }

    /**
     * Tests setting the rel-attribute
     *
     * @test
     */
    public function setRelation()
    {
        $s_relation = 'my-relation';
        
        $this->obj_CoreHtmlItem->setRelation($s_relation);
        
        $s_expected = '<testTag rel="' . $s_relation . '"></testTag>';
        $this->assertEquals($this->obj_CoreHtmlItem->generateItem(), $s_expected);
    }
}