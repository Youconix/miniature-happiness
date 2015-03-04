<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testItems extends GeneralTest
{

    private $helper_HTML;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'include/helpers/HTML.inc.php');
        require_once (NIV . 'include/helpers/html/Div.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->helper_HTML = new core\helpers\html\HTML();
    }

    public function tearDown()
    {
        parent::tearDown();
        
        $this->helper_HTML = null;
    }

    /**
     * Tests generating a form
     *
     * @test
     */
    public function form()
    {
        $s_link = 'index.php';
        $s_method = 'post';
        $s_content = '<input type="button" value="submit">';
        
        $object = $this->helper_HTML->form($s_link, $s_method);
        $object->setContent($s_content);
        $s_expected = '<form action="' . $s_link . '" method="' . $s_method . '">' . "\n" . $s_content . "\n</form>\n";
        $this->assertEquals($s_expected, $object->generateItem());
        
        $object = $this->helper_HTML->form($s_link, $s_method, true);
        $object->setContent($s_content);
        $s_expected = '<form action="' . $s_link . '" method="' . $s_method . '" enctype="multipart/form-data">' . "\n" . $s_content . "\n</form>\n";
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Tests generating canvas
     *
     * @test
     */
    public function canvas()
    {
        $s_expected = '<canvas ></canvas>';
        $this->assertEquals($s_expected, $this->helper_HTML->canvas()
            ->generateItem());
    }

    /**
     * Tests generating a stylesheet link
     *
     * @test
     */
    public function stylesheetLink()
    {
        $s_link = 'style.css';
        $s_media = 'print';
        $s_expected = '<link rel="stylesheet" href="' . $s_link . '" media="' . $s_media . '" >';
        $this->assertEquals($s_expected, $this->helper_HTML->stylesheetLink($s_link, $s_media)
            ->generateItem());
    }

    /**
     * Tests generating a CSS code block
     *
     * @test
     */
    public function stylesheet()
    {
        $s_content = 'body { color:black; margin:10px; padding:10px; }';
        $s_expected = "<style>\n<!--\n" . $s_content . "\n-->\n</style>\n";
        $this->assertEquals($s_expected, $this->helper_HTML->stylesheet($s_content)
            ->generateItem());
    }

    /**
     * Tests generating a javascript link
     *
     * @test
     */
    public function javascriptLink()
    {
        $s_link = 'general.js';
        $s_expected = '<script src="' . $s_link . '" ></script>' . "\n";
        $this->assertEquals($s_expected, $this->helper_HTML->javascriptLink($s_link)
            ->generateItem());
    }

    /**
     * Tests generating a javascript code block
     *
     * @test
     */
    public function javascript()
    {
        $s_javascript = 'function start(){  alert("hi"); }';
        $s_expected = "<script>\n<!--\n" . $s_javascript . "\n//-->\n</script>\n";
        $this->assertEquals($s_expected, $this->helper_HTML->javascript($s_javascript)
            ->generateItem());
    }

    /**
     * Tests generating a metatag
     *
     * @test
     */
    public function metatag()
    {
        $s_name = 'charset';
        $s_content = 'UTF-8';
        $s_expected = '<meta http-equiv="' . $s_name . '" content="' . $s_content . '" >' . "\n";
        $this->assertEquals($s_expected, $this->helper_HTML->metatag($s_name, $s_content)
            ->generateItem());
    }

    /**
     * Tests genereating a H-header
     *
     * @test
     */
    public function header()
    {
        $i_nr = 2;
        $s_content = '<bold>test header</bold> <emp>really!</emp>';
        $s_expected = '<h' . $i_nr . ' >' . $s_content . '</h' . $i_nr . '>';
        $this->assertEquals($s_expected, $this->helper_HTML->header($i_nr, $s_content)
            ->generateItem());
    }

    /**
     * Tests generating an image
     *
     * @test
     */
    public function image()
    {
        $s_url = 'images/home.png';
        $s_alt = 'back to home';
        $s_title = 'lets go back';
        $s_expected = '<img src="' . $s_url . '" title="' . $s_title . '" alt="' . $s_alt . '" >';
        
        $object = $this->helper_HTML->image($s_url);
        $object->setTitle($s_title);
        $object->setValue($s_alt);
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Tests generating a link
     *
     * @test
     */
    public function link()
    {
        $s_link = 'index.php';
        $s_text = 'home';
        $s_expected = '<a href="' . $s_link . '" >' . $s_text . '</a>';
        $this->assertEquals($s_expected, $this->helper_HTML->link($s_link, $s_text)
            ->generateItem());
    }

    /**
     * Tests generating a paragraph
     *
     * @test
     */
    public function paragraph()
    {
        $s_content = 'klads aslkjnda as;dasd bnlasd ';
        $s_expected = "<p >" . $s_content . "</p>\n";
        $this->assertEquals($s_expected, $this->helper_HTML->paragraph($s_content)
            ->generateItem());
    }

    /**
     * Tests generating a span
     *
     * @test
     */
    public function span()
    {
        $s_content = 'klads aslkjnda as;dasd bnlasd ';
        $s_expected = '<span >' . $s_content . '</span>';
        $this->assertEquals($s_expected, $this->helper_HTML->span($s_content)
            ->generateItem());
    }

    /**
     * Tests generating an audio element
     *
     * @test
     */
    public function audio()
    {
        $s_url = 'new.ogg';
        $s_type = 'ogg';
        $object = $this->helper_HTML->audio($s_url, $s_type);
        $object->autoplay(true);
        $object->controls(true);
        $object->loop(false);
        $object->setLoader('auto');
        $s_expected = '<audio autoplay="autoplay" controls="controls"><source src="' . $s_url . '" type="audio/' . $s_type . '"></audio>';
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Tests generating a video element
     *
     * @test
     */
    public function video()
    {
        $s_url = 'new.ogg';
        $s_type = 'ogg';
        $object = $this->helper_HTML->video($s_url, $s_type);
        $object->autoplay(true);
        $object->controls(true);
        $object->loop(false);
        $object->setPreLoader('auto');
        $s_expected = '<video autoplay="autoplay" controls="controls"><source src="' . $s_url . '" type="video/' . $s_type . '"></video>';
        $this->assertEquals($s_expected, $object->generateItem());
    }
}