<?php
if (! defined('NIV')){
    define('NIV', dirname(__FILE__) . '/../../../');
}

class testTemplate extends \tests\GeneralTest
{
    
    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/services/Template.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new \tests\stubs\database\DAL();
        $service_File = new \tests\stubs\services\File();
        $service_Settings = new \tests\stubs\services\Settings();
        $service_Validation = new \tests\stubs\services\Validation;
        
        $service_Security = new \tests\stubs\services\Security($service_Validation);
        $service_Cookie = new \tests\stubs\services\Cookie($service_Security);
        $service_QueryBuilder = new \tests\stubs\services\QueryBuilder($service_Database);
        
        $service_Config = new \tests\stubs\models\Config($service_File, $service_Settings, $service_Cookie);
        $service_Headers = new \tests\stubs\services\Headers($service_Config);
        $service_Cache = new \tests\stubs\services\Cache($service_File, $service_Config, $service_Headers, $service_QueryBuilder);
        
        $this->service_Template = new \core\services\Template($service_File, $service_Config, $service_Cache, $service_Headers);
    }

    public function tearDown()
    {
        $this->service_Template = null;
        
        parent::tearDown();
    }

    /**
     * Loads the given view into the parser
     *
     * @test
     * @expectedException TemplateException
     */
    public function loadView()
    {
        $this->service_Template->loadView();
    }

    /**
     * Loads the given view into the parser
     *
     * @test
     * @expectedException TemplateException
     */
    public function loadViewInvalid()
    {
        $this->service_Template->loadView('lalalalaa');
    }

    /**
     * Writes a script link to the head
     *
     * @test
     */
    public function setJavascriptLink()
    {
        $this->service_Template->setJavascriptLink('<script src="test.js"></script>');
    }

    /**
     * Writes a script link to the head, nullpointer check
     *
     * @test
     * @expectedException NullPointerException
     */
    public function setJavascriptLinkInvalid()
    {
        $this->service_Template->setJavascriptLink(null);
    }

    /**
     * Writes javascript code to the head
     *
     * @test
     */
    public function setJavascript()
    {
        $this->service_Template->setJavascript('function test(){ alert("test"); }');
    }

    /**
     * Writes javascript code to the head, nullpointer check
     *
     * @test
     * @expectedException NullPointerException
     */
    public function setJavascriptInvalid()
    {
        $this->service_Template->setJavascript(null);
    }

    /**
     * Writes a stylesheet link to the head
     *
     * @test
     */
    public function setCssLink()
    {
        $this->service_Template->setCssLink('<link type="stylesheet" href="css/style.css">');
    }

    /**
     * Writes a stylesheet link to the head, nullpointer check
     *
     * @test
     * @expectedException NullPointerException
     */
    public function setCssLinkInvalid()
    {
        $this->service_Template->setCssLink(null);
    }

    /**
     * Writes CSS code to the head
     *
     * @test
     */
    public function setCSS()
    {
        $this->service_Template->setCSS('body { color:#111; }');
    }

    /**
     * Writes CSS code to the head, nullpointer check
     *
     * @test
     * @expectedException NullPointerException
     */
    public function setCSSInvalid()
    {
        $this->service_Template->setCSS(null);
    }

    /**
     * Writes a metatag to the head
     *
     * @test
     */
    public function setMetaLink()
    {
        $this->service_Template->setMetaLink('<meta name="keywords" content="HTML, CSS, XML, XHTML, JavaScript">');
    }

    /**
     * Writes a metatag to the head, nullpointer check
     *
     * @test
     * @expectedException NullPointerException
     */
    public function setMetaLinkInvalid()
    {
        $this->service_Template->setMetaLink(null);
    }

    /**
     * Loads a subtemplate into the template
     *
     * @test
     * @expectedException TemplateException
     */
    public function loadTemplate()
    {
        $this->service_Template->loadTemplate('mytemplate', 'not_found.tpl');
    }

    /**
     * Loads an invalid subtemplate into the template
     *
     * @test
     * @expectedException TypeException
     */
    public function loadTemplateInvalid()
    {
        $this->service_Template->loadTemplate('mytemplate', 12324234);
    }

    /**
     * Loads a template and returns it as a string
     *
     * @test
     * @expectedException TemplateException
     */
    public function loadTemplateAsString()
    {
        $this->service_Template->loadTemplateAsString('mytemplate');
    }

    /**
     * Loads an invalid template and returns it as a string
     *
     * @test
     * @expectedException TypeException
     */
    public function loadTemplateAsStringInvalid()
    {
        $this->service_Template->loadTemplateAsString('mytemplate', 1232138123);
    }

    /**
     * Writes the values to the given keys on the given template
     *
     * @test
     */
    public function writeTemplate()
    {
        $s_string = 'Susan is {action} on the {place}.';
        $s_expected = 'Susan is sitting on the chair.';
        
        $this->assertEquals($s_expected, $this->service_Template->writeTemplate(array(
            'action',
            'place'
        ), array(
            'sitting',
            'chair'
        ), $s_string));
    }
}