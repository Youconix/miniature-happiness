<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}
if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testUBB extends GeneralTest
{

    private $helper_UBB;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/helpers/UBB.inc.php');
        $this->loadStub('DummyDAL');
        $this->loadStub('DummyQueryBuilder');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new DummyDAL();
        $service_Builder = new DummyQueryBuilder($service_Database);
        $this->helper_UBB = new \core\helpers\UBB($service_Builder);
    }

    public function tearDown()
    {
        $this->helper_UBB = null;
        
        parent::tearDown();
    }

    /**
     * Test for parsing UBB into HTML
     *
     * @test
     */
    public function parse()
    {
        $s_text = '[center][p]  [url=index.php]Look here![/url] [img]images/lol.png[/img]  [/p] [/center] [url=home.php][img]home.png[/img][/url]';
        $s_expected = '<div class="textCenter"><p>  <a href="index.php">Look here!</a> <img src="images/lol.png" alt=""/>  </p> </div> <a href="home.php"><img src="home.png" alt=""/></a>';
        
        $this->assertEquals($s_expected, $this->helper_UBB->parse($s_text));
    }

    /**
     * Test of reverting HTML to UBB
     *
     * @test
     */
    public function revert()
    {
        $s_text = '<div class="textCenter"><p>  <a href="index.php">Look here!</a> <img src="images/lol.png" alt="lol"/>  </p> </div> <a href="home.php"><img src="home.png" alt="home"/></a>';
        $s_expected = '[center][p]  [url=index.php]Look here![/url] [img=images/lol.png]lol[/img]  [/p] [/center] [url=home.php][img=home.png]home[/img][/url]';
        
        $this->assertEquals($s_expected, $this->helper_UBB->revert($s_text));
    }
}
?>