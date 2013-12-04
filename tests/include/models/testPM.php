<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testModifications extends GeneralTest {
	private $service_Database;
	private $model_PM;
	private $s_title;
	private $s_message;
	private $i_sender;
	private $obj_receiver;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/PM.inc.php');
		require_once(NIV.'include/models/GeneralUser.inc.php');
		require_once(NIV.'include/models/data/Data_User.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_Database = Memory::services('Database');

		$this->s_title = 'test message';
		$this->s_message	= 'test message';
		$this->i_sender = 1;
		$this->obj_receiver	 = new Data_User();
		$this->obj_receiver->loadData(0);

		$this->model_PM = new Model_PM();
		define('USERID',$this->i_sender);
	}

	public function tearDown(){
		$this->service_Database	= null;
		$this->model_PM = null;

		parent::tearDown();
	}

	/**
	 * Sends a message from system
	 *
	 * @param	Data_User	$obj_receiver	The receiver
	 * @param	string  $s_title    The title of the message
	 * @param	string  $s_message  The content of the message
	 */
	public function testSendSystemMessage($obj_receiver,$s_title, $s_message){
		$this->service_Database->transaction();
		
		$i_id = $this->model_PM->sendSystemMessage($this->obj_receiver, $this->s_title, $this->s_message);
		$obj_message = $this->model_PM->getMessage($i_id);
		
		$this->service_Database->rollback();
		
		$this->assertEquals($obj_message->getTitle(),$this->s_title);
		$this->assertEquals($obj_message->getMessage(),$this->s_message);
		$this->assertTrue($obj_message->isUnread());
	}

	/**
	 * Sends a message
	 *
	 * @param	Data_User	$obj_receiver	The receiver
	 * @param	string  $s_title    The title of the message
	 * @param	string  $s_message  The content of the message
	 */
	public function testSendMessage($obj_receiver,$s_title, $s_message) {
		$this->service_Database->transaction();
		
		$i_id = $this->model_PM->sendSystemMessage($this->obj_receiver, $this->s_title, $this->s_message,$this->i_sender);
		$obj_message = $this->model_PM->getMessage($i_id);
		
		$this->service_Database->rollback();
		
		$this->assertEquals($obj_message->getTitle(),$this->s_title);
		$this->assertEquals($obj_message->getMessage(),$this->s_message);
		$this->assertTrue($obj_message->isUnread());
	}

	/**
	 * Test of collecting all the messages send to the logged in user
	 */
	public function testGetMessages() {
		$this->assertEquals(array(),$this->model_PM->getMessages());
	}

	/**
	 * Test of deleting the message with the given ID
	 */
	public function testDeleteMessage() {
		$this->service_Database->transaction();
		
		$i_id = $this->model_PM->sendSystemMessage($this->obj_receiver, $this->s_title, $this->s_message);
		$this->model_PM->deleteMessage($i_id);
		
		try {
			$this->model_PM->getMessage($i_id);
		
			$this->service_Database->rollback();
			
			$this->fail("Expected DBException for not existing PM id.");
		}
		catch(DBException $e ){
			$this->service_Database->rollback();
		}
	}
}
?>