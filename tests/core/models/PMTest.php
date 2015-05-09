<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

class testPM extends \tests\GeneralTest
{

    private $model_PM;

    private $s_title;

    private $s_message;

    private $i_sender;

    private $obj_receiver;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/models/PM.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->s_title = 'test message';
        $this->s_message = 'test message';
        $this->i_sender = 1;
        
        $service_Validation = new \tests\stubs\services\Validation();
        $service_Database = new \tests\stubs\database\DAL();
        $service_Builder = new \tests\stubs\services\QueryBuilder($service_Database);
        $this->obj_receiver = new \tests\stubs\models\data\ModelUserData();
        $this->obj_receiver->i_userid = 0;
        $model_User = new \tests\stubs\models\ModelUser($this->obj_receiver);
        $model_PM = new \tests\stubs\models\data\ModelPmData($service_Builder, $service_Validation, $model_User);
        $service_Mailer = new \tests\stubs\services\Mailer();
        
        $this->model_PM = new \core\models\PM($service_Builder, $service_Validation, $model_PM, $service_Mailer);
        if ( ! defined('USERID') ) {
            define('USERID', $this->i_sender);
        }
    }

    public function tearDown()
    {
        $this->model_PM = null;
        $this->obj_receiver = null;
        
        parent::tearDown();
    }

    /**
     * Sends a message from system
     *
     * @test
     */
    public function sendSystemMessage()
    {
        $i_id = $this->model_PM->sendSystemMessage($this->obj_receiver, $this->s_title, $this->s_message);
        $obj_message = $this->model_PM->getMessage($i_id);
        
        $this->assertEquals($obj_message->getTitle(), $this->s_title);
        $this->assertEquals($obj_message->getMessage(), $this->s_message);
        $this->assertTrue($obj_message->isUnread());
    }

    /**
     * Sends a message
     *
     * @test
     */
    public function sendMessage()
    {
        $i_id = $this->model_PM->sendSystemMessage($this->obj_receiver, $this->s_title, $this->s_message, $this->i_sender);
        $obj_message = $this->model_PM->getMessage($i_id);
    }

    /**
     * Test of collecting all the messages send to the logged in user
     *
     * @test
     */
    public function getMessages()
    {
        $this->assertEquals(array(), $this->model_PM->getMessages());
    }

    /**
     * Test of deleting the message with the given ID
     *
     * @test
     * @expectedException DBException
     */
    public function deleteMessage()
    {
        $i_id = $this->model_PM->sendSystemMessage($this->obj_receiver, $this->s_title, $this->s_message);
        $this->model_PM->deleteMessage($i_id);
        $this->model_PM->getMessage($i_id);
    }
}
?>