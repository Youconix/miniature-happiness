<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testStats extends GeneralTest {
	private $service_Database;
	private $model_User;
	private $s_username;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/Stats.inc.php');
		require_once(NIV.'include/models/User.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_Database = Memory::services('Database');

		$this->model_User = new Model_User();
		$this->s_username	= 'System';
	}

	public function tearDown(){
		$this->model_User = null;

		parent::tearDown();
	}

	/**
	 * Test of getting the requested users
	 */
	public function testGetUsersById(){
		$a_ids	= array(0,1);
		
		$a_users = $this->model_User->getUsersById($a_ids);
		$this->assertTrue(is_array($a_users),'Expected getUsersById to return an array.');
		foreach($a_ids AS $i_id){
			$this->assertEquals($a_ids[$i_id],$a_users[$i_id]->getID());
		}
	}

	/**
	 * Test of getting the requested user
	 */
	public function testGet(){
		try {
			$this->model_User->get(-2);
			
			$this->fail('Expected DBException.');
		}
		catch(DBException $e){}
		
		$i_userid = 0;
		
		$obj_user = $this->model_User->get(0);
		
		$this->assertInstanceOf(Data_User, $obj_user);
		$this->assertEquals($i_userid,$obj_user->getID());
	}

	/**
	 * Test of collecting 25 of the users sorted on nick. Start from the given position, default 0
	 */
	public function testGetUsers(){
		$a_users = $this->model_User->getUsers();
		
		$this->assertTrue(is_array($a_users),'Expected getUsers to return an array.');
		if( array_key_exists(0, $a_users) ){
			$this->assertInstanceOf(Data_User, $a_users[0]);
		}
	}
	
	/**
	 * Test of searching an user
	 */
	public function testSearchUser(){
		$a_users = $this->model_User->searchUser($this->s_username);
		
		$this->assertTrue(is_array($a_users),'Expected searchUser to return an array.');
		$this->assertTrue(array_key_exists('data', $a_users),'Expected key number.');
		$this->assertTrue(array_key_exists('number', $a_users),'Expected key number.');
		
		foreach($a_users['data'] AS $obj_user){
			$this->assertContains($this->s_username,$obj_user->getUsername());
		}
	}

	/**
	 * Registers the login try
	 *
	 * @return int	The number of tries done including this one
	 */
	public function testRegisterLoginTries(){
		$_SERVER['REMOTE_ADDR']	= '::1';
		
		$this->service_Database->transaction();
		
		$i_run1 = $this->model_User->registerLoginTries();
		$i_run2 = $this->model_User->registerLoginTries();
		
		$this->service_Database->rollback();
		
		$this->assertEquals(1,$i_run1);
		$this->assertEquals(2,$i_run2);
	}

	/**
	 * Clears the login tries
	 */
	public function testClearLoginTries(){
		$_SERVER['REMOTE_ADDR']	= '::1';
		
		$this->service_Database->transaction();
		
		$i_run1 = $this->model_User->registerLoginTries();
		$this->model_User->clearLoginTries();
		$i_run2 = $this->model_User->registerLoginTries();
		
		$this->service_Database->rollback();
		
		$this->assertEquals(1,$i_run1);
		$this->assertEquals(1,$i_run2);
	}

	/**
	 * Test of chaning the saved password with known current password
	 */
	public function testChangePassword($i_userid,$s_username,$s_passwordOld,$s_password){
		$this->assertFalse($this->model_User->changePassword(0, $this->s_username, '', ''));
	}

	/**
	 * Test of creating a new user object
	 */
	public function testCreateUser(){
		$this->assertInstanceOf(Data_User, $this->model_User->createUser());
	}

	/**
	 * Test of checking if the username is available
	 */
	public function testCheckUsername($s_username,$i_userid = -1,$s_type = 'normal'){
		$this->assertFalse($this->model_User->checkUsername($this->s_username));
		$this->assertTrue($this->model_User->checkUsername('Free_Username'));
	}

	/**
	 * Test of checking or the given email address is available
	 */
	public function testCheckEmail($s_email,$i_userid = -1){
		$this->assertTrue($this->model_User->checkEmail($this->s_username.'@example.com'));
	}

	/**
	 * Test of collecting the site admins (control panel)
	 */
	public function testGetSiteAdmins(){
		$a_admins	= $this->model_User->getSiteAdmins();
		
		$this->assertTrue(is_array($a_admins),'Expected getSiteAdmins to return an array.');
	}

	/**
	 * Test of getting the id from all the activated users
	 * /
	public function testGetUserIDs(){
		$a_users	= $this->model_User->getUserIDs();
		$this->assertTrue(is_array($a_users),'Expected getUserIDs to return an array.');
	} */
}
?>
