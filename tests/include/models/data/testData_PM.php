<?php
define('NIV',dirname(__FILE__).'/../../../../');

require(NIV.'tests/include/GeneralTest.php');

class testStats extends GeneralTest {
	private $service_Database;
	private $model_PM;
	private $i_id;
	private $i_sender;
	private $i_receiverID;
	private $s_title;
	private $s_message;
	private $i_sendTime;
	private $i_unread;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/data/Data_PM.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_Database = Memory::services('Database');

		$this->i_sender = 0;
		$this->i_receiverID = 0;
		$this->s_title	= 'test pm';
		$this->s_message	= 'test pm';
		$this->model_PM	= new Data_PM();
	}

	public function tearDown(){
		$this->service_Database	= null;
		$this->model_PM = null;

		parent::tearDown();
	}

	/**
	 * Test of setting the sender
	 */
	public function testSetSender(){
		$this->model_PM->setSender($this->i_sender);
		$this->assertInstanceOf(Data_User,$this->model_PM->getSender());
		$this->assertEquals($this->i_sender,$this->model_PM->getSender()->getID());
	}

	/**
	 * Test of setting the receiver ID
	 */
	public function testSetReceiver(){
		$this->model_PM->setSender($this->i_sender);
		$this->assertEquals($this->i_sender,$this->model_PM->getReceiver());
	}

	/**
	 * Test of setting the message title
	 */
	public function testSetTitle(){
		$this->model_PM->setTitle($this->s_title);
		$this->assertEquals($this->s_title,$this->model_PM->getTitle());
	}

	/**
	 * Test of setting the message content
	 */
	public function testSetMessage(){
		$this->model_PM->setMessage($this->s_message);
		$this->assertEquals($this->s_message,$this->model_PM->getMessage());
	}

	/**
	 * Test of setting the message as read
	 */
	public function testSetRead(){
		$this->setData();
		
		$this->service_Database->transaction();
		
		$this->assertTrue($this->model_PM->isUnread());
		$this->model_PM->setRead();
		$this->assertFalse($this->model_PM->isUnread());
		
		$this->service_Database->rollback();
	}

	/**
	 * Test of deleting the message
	 */
	public function testDeleteMessage(){
		$this->setData();
		
		try {
			$this->model_PM->deleteMessage();
		}
		catch(DBException $e){
			$this->fail("Exception : ".$e->getMessage());
		}
	}

	/**
	 * Test of saving the new message
	 */
	public function testSave(){
		$this->setData();
		
		$this->service_Database->transaction();
		
		$this->model_PM->save();
		
		$i_id = $this->model_PM->getID();
		
		$this->service_Database->rollback();
		
		$this->assertNotNull($i_id);
	}
	
	private function setData(){
		$this->model_PM->setData(array(
			'id' => 0,'fromUserid' => $this->i_sender,'toUserid' => $this->i_receiverID,'title' => $this->s_title,
			'message' => $this->s_message,'send'=>time(),'unread'=>1
		));
	}
}
?>