<?php

define('NIV', dirname(__FILE__) . '/../../../');

require(NIV . 'tests/GeneralTest.php');

class testFileData extends GeneralTest{

  private $service_FileData;
  private $s_fileName;

  public function __construct(){
    parent::__construct();

    require_once(NIV . 'include/services/FileData.inc.php');
  }

  public function setUp(){
    parent::setUp();

    $this->service_FileData = new \core\services\FileData();
    $this->s_fileName = NIV . 'tests/GeneralTest.php';
  }

  public function tearDown(){
    $this->service_FileData = null;

    parent::tearDown();
  }

  /**
   * Test for checking the mime type
   * 
   * @test
   */
  public function getMimeType(){
    $s_mime = $this->service_FileData->getMimeType($this->s_fileName);
    if( !in_array($s_mime, array( 'text/php', 'text/x-php' )) ){
      $this->fail("Expected mimetype text/php or text/x-php but got " . $s_mime . '.');
    }
  }

  /**
   * Test of calulating the file size
   * 
   * @test
   */
  public function getFileSize(){
    $i_expected = filesize($this->s_fileName);
    $this->assertEquals($i_expected, $this->service_FileData->getFileSize($this->s_fileName));
  }

  /**
   * Test of retreaving the last access date
   * 
   * @test
   */
  public function getLastAccess(){
    $this->assertNotEquals(-1, $this->service_FileData->getLastAccess($this->s_fileName));
  }

  /**
   * Test of retreaving the last modified date
   * 
   * @test
   */
  public function getLastModified($s_file){
    $this->assertNotEquals(-1, $this->service_FileData->getLastModified($this->s_fileName));
  }

}
?>
