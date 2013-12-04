<?php
define('NIV',dirname(__FILE__).'/../../../../');

require(NIV.'tests/include/GeneralTest.php');

class testStats extends GeneralTest {
	private $service_QueryBuilder;
	private $obj_User;
	private $i_userid;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/GeneralUser.inc.php');
		require_once(NIV.'include/models/data/Data_User.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_QueryBuilder = Memory::services('QueryBuilder')->createBuilder();

		$this->i_userid = 0;
		$this->obj_User = new Data_User();
		$this->obj_User->loadData($this->i_userid);
	}

	public function tearDown(){
		$this->obj_User = null;

		parent::tearDown();
	}

	/**
	 * Test of setting the username
	 */
	public function testSetUsername() {
		$s_username = 'test user';
		
		$this->obj_User->setUsername($s_username);
		$this->assertEquals($s_username,$this->obj_User->getUsername());
	}

	/**
	 * Test of setting the email address
	 */
	public function testSetEmail() {
		$s_email	= 'tester@example.com';
		
		$this->obj_User->setEmail($s_email);
		$this->assertEquals($s_email,$this->obj_User->getEmail());
	}

	/**
	 * Test of setting a new password
	 * Note : username has to be set first!
	 */
	public function testSetPassword() {
		$s_password	= 'new password';

		$this->service_QueryBuilder->transaction();
		
		$this->obj_User->setPassword($s_password);

		$this->service_QueryBuilder->select('users','password')->getWhere()->addAnd('id','i',$this->i_userid);
		$s_setPassword = $this->service_QueryBuilder->getResult()->result(0,'password');
		
		$this->service_QueryBuilder->rollback();
		
		$this->assertNotEquals('', $s_setPassword);
	}

	/**
	 * Test of setting the account as a normal or system account
	 */
	public function testSetBot() {
		$this->assertTrue($this->obj_User->isBot());
		$this->obj_User->setBot(false);
		$this->assertFalse($this->obj_User->isBot());
	}

	/**
	 * Test of (Un)Blocking the account
	 */
	public function testSetBlocked() {
		$this->obj_User->setBlocked(true);
		$this->assertTrue($this->obj_User->isBlocked());
		
		$this->obj_User->setBlocked(false);
		$this->assertFalse($this->obj_User->isBlocked());
	}

	/**
	 * Test of getting the profile
	 */
	public function testGetProfile() {
		$this->obj_User->loadData(1);
		$this->assertEquals('',$this->obj_User->getProfile());
	}

	/**
	 * Test of collecting the groups where the user is in
	 */
	public function testGetGroups(){
		Memory::services('Session');
		
		$this->assertEquals(array(),$this->obj_User->getGroups());
	}

	/**
	 * Test of collecting the access level for the current group
	 */
	public function testGetLevel() {
		Memory::services('Session');
		
		$this->assertEquals(Session::FORBIDDEN,$this->obj_User->getLevel());
	}

	/**
	 * Test of changing the password
	 */
	public function testChangePassword() {
		$s_password	= 'new password';

		$this->service_QueryBuilder->transaction();
		
		$this->obj_User->setPassword($s_password);

		$this->service_QueryBuilder->select('users','password')->getWhere()->addAnd('id','i',$this->i_userid);
		$s_setPassword = $this->service_QueryBuilder->getResult()->result(0,'password');
		
		$this->service_QueryBuilder->rollback();

echo('password : '.$s_setPassword.'   ');
		
		$this->assertNotEquals('', $s_setPassword);
	}
	
	/**
	 * Test of disabling and enabling the account
	 */
	public function testIsEnabled(){
		$this->assertTrue($this->obj_User->isEnabled());
		
		$this->obj_User->disableAccount();
		$this->assertFalse($this->obj_User->isEnabled());
		
		$this->obj_User->enableAccount();
		$this->assertTrue($this->obj_User->isEnabled());
	}

	/**
	 * Test of collecting the color corosponding the users level
	 */
	public function testGetColor() {
		Memory::services('Session');

		$this->assertEquals(Session::FORBIDDEN_COLOR,$this->obj_User->getColor());
	}

	/**
	 * Test of checking if the visitor has moderator rights
	 * Expected : no rights
	 */
	public function TestIsModerator() {
		$this->assertFalse($this->obj_User->isModerator());
	}

	/**
	 * Test of checking if the visitor has administrator rights
	 * Expected : no rights
	 */
	public function testIsAdmin() {
		$this->assertFalse($this->obj_User->isAdmin());
	}

	/**
	 * Test of collecting the set user language
	 */
	public function testGetLanguage(){
		$this->assertNotNull($this->obj_User->getLanguage());
	}
}
?>
