<?php
if( ! defined('NIV') ) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

class testXML extends \tests\GeneralTest
{

    private $service_XML;

    private $s_file = 'example.xml';

    private $s_path = 'example';

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/services/Xml.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->service_XML = new \core\services\XML();
    }

    public function tearDown()
    {
        $this->service_XML = null;
        
        parent::tearDown();
    }

    /**
     * Creates a new XML-file
     *
     * @test
     */
    public function createDocument()
    {
        $this->service_XML->createDocument();
    }

    /**
     * Loads the requested XML-file
     *
     * @test
     * @expectedException IOException
     */
    public function load()
    {
        $this->service_XML->load($this->s_file);
    }

    /**
     * Gives the asked part of the loaded file
     *
     * @test
     * @expectedException XMLException
     */
    public function get()
    {
        $this->service_XML->createDocument();
        $this->service_XML->get($this->s_path);
    }

    /**
     * Saves the value at the given place
     *
     * @test
     * @expectedException XMLException
     */
    public function set()
    {
        $this->service_XML->createDocument();
        $this->service_XML->get($this->s_path, '');
    }

    /**
     * Saves the XML file loaded to the given file
     *
     * @test
     */
    public function save()
    {
        $this->service_XML->createDocument();
        $this->service_XML->save($this->s_temp . $this->s_file);
        
        if (! file_exists($this->s_temp . $this->s_file)) {
            $this->fail('File ' . $this->s_temp . $this->s_file . ' was not created.');
        }
        
        unlink($this->s_temp . $this->s_file);
    }

    /**
     * Checks of the given part of the loaded file exists
     *
     * @test
     */
    public function exists()
    {
        $this->service_XML->createDocument();
        $this->assertFalse($this->service_XML->exists($this->s_path));
    }

    /**
     * Replaces the geven keys in the given text with the given values
     *
     * @test
     * @expectedException XMLException
     */
    public function insert()
    {
        $this->service_XML->insert($this->s_path, array(), array());
    }
}