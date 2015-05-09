<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

class testData_PM extends \tests\GeneralTest
{

    private $model_PM;

    private $i_sender;

    private $i_receiverID;

    private $s_title;

    private $s_message;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/models/data/DataPM.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new \tests\stubs\database\DAL();
        $service_Validation = new \tests\stubs\services\Validation();
        $service_Builder = new \tests\stubs\services\QueryBuilder($service_Database);
        $model_UserData = new \tests\stubs\models\data\ModelUserData();
        
        $model_User = new \tests\stubs\models\ModelUser($model_UserData);
        
        $this->i_sender = 0;
        $this->i_receiverID = 0;
        $this->s_title = 'test pm';
        $this->s_message = 'test pm';
        $this->model_PM = new \core\models\data\DataPM($service_Builder, $service_Validation, $model_User);
    }

    public function tearDown()
    {
        $this->model_PM = null;
        
        parent::tearDown();
    }

    /**
     * Test of setting the sender
     *
     * @test
     */
    public function setSender()
    {
        $this->model_PM->setSender($this->i_sender);
        $this->assertInstanceOf('\core\models\data\DataUser', $this->model_PM->getSender());
        $this->assertEquals($this->i_sender, $this->model_PM->getSender()
            ->getID());
    }

    /**
     * Test of setting the receiver ID
     *
     * @test
     */
    public function setReceiver()
    {
        $this->model_PM->setSender($this->i_sender);
        $this->assertEquals($this->i_sender, $this->model_PM->getReceiver());
    }

    /**
     * Test of setting the message title
     *
     * @test
     */
    public function setTitle()
    {
        $this->model_PM->setTitle($this->s_title);
        $this->assertEquals($this->s_title, $this->model_PM->getTitle());
    }

    /**
     * Test of setting the message content
     *
     * @test
     */
    public function setMessage()
    {
        $this->model_PM->setMessage($this->s_message);
        $this->assertEquals($this->s_message, $this->model_PM->getMessage());
    }

    /**
     * Test of setting the message as read
     *
     * @test
     */
    public function setRead()
    {
        $this->setData();
        
        $this->assertTrue($this->model_PM->isUnread());
        $this->model_PM->setRead();
        $this->assertFalse($this->model_PM->isUnread());
    }

    /**
     * Test of deleting the message
     *
     * @test
     */
    public function deleteMessage()
    {
        $this->setData();
        
        $this->model_PM->deleteMessage();
    }

    /**
     * Test of saving the new message
     *
     * @test
     */
    public function testSave()
    {
        $this->setData();
        
        $this->model_PM->save();
    }

    private function setData()
    {
        $this->model_PM->setData(array(
            'id' => 0,
            'fromUserid' => $this->i_sender,
            'toUserid' => $this->i_receiverID,
            'title' => $this->s_title,
            'message' => $this->s_message,
            'send' => time(),
            'unread' => 1
        ));
    }
}
?>