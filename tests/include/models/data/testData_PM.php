<?php
define('NIV',dirname(__FILE__).'/../../../../');

if( !class_exists('GeneralTest') ){
	require(NIV.'tests/GeneralTest.php');
}

class testData_PM extends GeneralTest {
	private $model_PM;
	private $i_sender;
	private $i_receiverID;
	private $s_title;
	private $s_message;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/data/Data_PM.inc.php');
    $this->loadStub('DummyDAL');
    $this->loadStub('DummyQueryBuilder');
    $this->loadStub('DummySecurity');
    $this->loadStub('DummyModelUser');
    $this->loadStub('DummyGroups');
    $this->loadStub('DummyModelUserData');
	}

	public function setUp(){
		parent::setUp();

		$service_Database = new DummyDAL();
    $service_Security = new DummySecurity($service_Database);
    $service_Builder = new DummyQueryBuilder($service_Database);
    $model_UserData = new DummyModelUserData();
    
    $model_User = new DummyModelUser($model_UserData);

		$this->i_sender = 0;
		$this->i_receiverID = 0;
		$this->s_title	= 'test pm';
		$this->s_message	= 'test pm';
		$this->model_PM	= new \core\models\data\Data_PM($service_Builder, $service_Security,$model_User);
	}

	public function tearDown(){
		$this->model_PM = null;

		parent::tearDown();
	}

	/**
	 * Test of setting the sender
   * 
   * @test
	 */
	public function setSender(){
		$this->model_PM->setSender($this->i_sender);
		$this->assertInstanceOf('\core\models\data\Data_User',$this->model_PM->getSender());
		$this->assertEquals($this->i_sender,$this->model_PM->getSender()->getID());
	}

	/**
	 * Test of setting the receiver ID
   * 
   * @test
	 */
	public function setReceiver(){
		$this->model_PM->setSender($this->i_sender);
		$this->assertEquals($this->i_sender,$this->model_PM->getReceiver());
	}

	/**
	 * Test of setting the message title
   * 
   * @test
	 */
	public function setTitle(){
		$this->model_PM->setTitle($this->s_title);
		$this->assertEquals($this->s_title,$this->model_PM->getTitle());
	}

	/**
	 * Test of setting the message content
   * 
   * @test
	 */
	public function setMessage(){
		$this->model_PM->setMessage($this->s_message);
		$this->assertEquals($this->s_message,$this->model_PM->getMessage());
	}

	/**
	 * Test of setting the message as read
   * 
   * @test
	 */
	public function setRead(){
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
	public function deleteMessage(){
		$this->setData();
		
		$this->model_PM->deleteMessage();
	}

	/**
	 * Test of saving the new message
   * 
   * @test
	 */
	public function testSave(){
		$this->setData();
		
		$this->model_PM->save();
	}
	
	private function setData(){
		$this->model_PM->setData(array(
			'id' => 0,'fromUserid' => $this->i_sender,'toUserid' => $this->i_receiverID,'title' => $this->s_title,
			'message' => $this->s_message,'send'=>time(),'unread'=>1
		));
	}
}
?>