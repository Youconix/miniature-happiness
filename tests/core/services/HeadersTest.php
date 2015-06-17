<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

class testHeaders extends \tests\GeneralTest
{

    private $service_Headers;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/services/Headers.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        $service_File = new \tests\stubs\services\File();
        $service_Validation = new \tests\stubs\services\Validation();
        $service_Settings = new \tests\stubs\services\Settings();
        
        $service_Security = new \tests\stubs\services\Security($service_Validation);
        $service_Cookie = new \tests\stubs\services\Cookie($service_Security);
        
        $service_Config = new \tests\stubs\models\Config($service_File, $service_Settings, $service_Cookie);
        
        $this->service_Headers = new \core\services\Headers($service_Config);
    }

    public function tearDown()
    {
        $this->service_Headers = null;
        
        parent::tearDown();
    }

    /**
     * Clears the headers
     *
     * @test
     */
    public function clear()
    {
        $this->service_Headers->clear();
        
        $this->assertNotEquals(array(), $this->service_Headers->getHeaders());
    }

    /**
     * Sets the given content type
     *
     * @test
     */
    public function contentType()
    {
        $s_contentType = 'application/pdf';
        
        $this->service_Headers->contentType($s_contentType);
        $a_headers = $this->service_Headers->getHeaders();
        
        $this->assertEquals(1, count($a_headers));
        $this->assertEquals(array(
            'Content-Type',
            $s_contentType
        ), $a_headers['Content-Type']);
    }

    /**
     * Sets the javascript content type
     *
     * @test
     */
    public function setJavascript()
    {
        $s_contentType = 'application/javascript';
        
        $this->service_Headers->contentType($s_contentType);
        $a_headers = $this->service_Headers->getHeaders();
        
        $this->assertEquals(1, count($a_headers));
        $this->assertEquals(array(
            'Content-Type',
            $s_contentType
        ), $a_headers['Content-Type']);
    }

    /**
     * Sets the CSS content type
     *
     * @test
     */
    public function setCSS()
    {
        $s_contentType = 'text/css';
        
        $this->service_Headers->contentType($s_contentType);
        $a_headers = $this->service_Headers->getHeaders();
        
        $this->assertEquals(1, count($a_headers));
        $this->assertEquals(array(
            'Content-Type',
            $s_contentType
        ), $a_headers['Content-Type']);
    }

    /**
     * Sets the XML content type
     *
     * @test
     */
    public function setXML()
    {
        $s_contentType = 'application/xml';
        
        $this->service_Headers->contentType($s_contentType);
        $a_headers = $this->service_Headers->getHeaders();
        
        $this->assertEquals(1, count($a_headers));
        $this->assertEquals(array(
            'Content-Type',
            $s_contentType
        ), $a_headers['Content-Type']);
    }

    /**
     * Sets the last modified header
     *
     * @test
     */
    public function modified()
    {
        $i_modified = time();
        $expected = array(
            'Content-Type' => array(
                'Content-Type',
                'text/html'
            ),
            'Last-Modified' => array(
                'Last-Modified',
                gmdate('D, d M Y H:i:s', $i_modified) . ' GMT'
            )
        );
        
        $this->service_Headers->modified($i_modified);
        $this->assertEquals($expected, $this->service_Headers->getHeaders());
    }

    /**
     * Sets the cache time, -1 for no cache
     *
     * @param            
     *
     */
    public function cache()
    {
        $i_cache = 20;
        
        $this->service_Headers->cache($i_cache);
        $expected = array(
            'Expires',
            gmdate('D, d M Y H:i:s', (time() + $i_cache)) . ' GMT'
        );
        $a_header = $this->service_Headers->getHeaders();
        
        $this->assertEquals($expected, $a_header['Last-Modified']);
    }

    /**
     * Sets the cache time, -1 for no cache
     *
     * @test
     */
    public function cacheDisabled()
    {
        $expected = array(
            'Content-Type' => array(
                'Content-Type',
                'text/html'
            ),
            array(
                'Expires',
                'Thu, 01-Jan-70 00:00:01 GMT'
            ),
            'Last-Modified' => array(
                'Last-Modified',
                gmdate('D, d M Y H:i:s') . ' GMT'
            ),
            array(
                'Cache-Control',
                'no-store, no-cache, must-revalidate'
            ),
            array(
                'Cache-Control',
                'post-check=0, pre-check=0',
                false
            ),
            array(
                'Pragma',
                'no-cache'
            )
        );
        
        $this->service_Headers->cache(- 1);
        $this->assertEquals($expected, $this->service_Headers->getHeaders());
    }

    /**
     * Sets the content length
     *
     * @test
     */
    public function contentLength()
    {
        $i_length = 2342345234;
        
        $this->service_Headers->contentLength($i_length);
        $a_header = $this->service_Headers->getHeaders();
        
        $expected = array(
            'Content-Length',
            $i_length
        );
        $this->assertEquals($expected, $a_header['Content-Length']);
    }

    /**
     * Sets a header
     *
     * @test
     */
    public function setHeader()
    {
        $s_key = 'Mime-Type';
        $s_content = 'application/wrong';
        
        $this->service_Headers->setHeader($s_key, $s_content);
        $expected = array(
            'Content-Type' => array(
                'Content-Type',
                'text/html'
            ),
            array(
                $s_key,
                $s_content
            )
        );
        
        $this->assertEquals($expected, $this->service_Headers->getHeaders());
    }

    /**
     * Sends the 304 not modified header
     *
     * @test
     */
    public function http304()
    {
        $expected = array(
            'HTTP/1.1',
            '304 Not Modified'
        );
        
        $this->service_Headers->http304();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Sends the 400 bad request header
     *
     * @test
     */
    public function http400()
    {
        $expected = array(
            'HTTP/1.1',
            '400 Bad Request'
        );
        
        $this->service_Headers->http400();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Sends the 401 unauthorized header
     *
     * @test
     */
    public function http401()
    {
        $expected = array(
            'HTTP/1.1',
            '401 Unauthorized'
        );
        
        $this->service_Headers->http401();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Sends the 403 forbidden header
     *
     * @test
     */
    public function http403()
    {
        $expected = array(
            'HTTP/1.1',
            '403 Forbidden'
        );
        
        $this->service_Headers->http403();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Sends the 404 not found header
     *
     * @test
     */
    public function http404()
    {
        $expected = array(
            'HTTP/1.1',
            '404 Not Found'
        );
        
        $this->service_Headers->http404();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Sends the 500 internal server header
     *
     * @test
     */
    public function http500()
    {
        $expected = array(
            'HTTP/1.1',
            '500 Internal Server Error'
        );
        
        $this->service_Headers->http500();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Sends the 503 service unavailable header
     *
     * @test
     */
    public function http503()
    {
        $expected = array(
            'HTTP/1.1',
            '503 Service Unavailable'
        );
        
        $this->service_Headers->http503();
        $a_header = $this->service_Headers->getHeaders();
        $this->assertEquals($expected, $a_header['http']);
    }

    /**
     * Returns if a force download was excequted
     *
     * @test
     */
    public function isForceDownload()
    {
        $this->assertFalse($this->service_Headers->isForceDownload());
    }
}