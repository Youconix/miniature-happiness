<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testGroups extends GeneralTest {
	private $service_Database;
	private $model_Groups;
	private $i_userid;
	private $i_id;
	private $s_name;
	private $i_default;
	private $s_description;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/Groups.inc.php');
	}

	public function setUp(){
		parent::setUp();

		$this->service_Database	= Memory::services('Database');
		$this->i_userid = 0;
		$this->i_id = 10000;
		$this->s_name = 'test group';
		$this->i_default = 1;
		$this->s_description = 'test group';

		$this->model_Groups = new Model_Groups();		
		Memory::services('Session');
	}

	public function tearDown(){
		$this->service_Database = null;
		$this->model_Groups	= null;

		parent::tearDown();
	}


	/**
	 * Test of getting all the registrated groups
	 */
	public function testGetGroups(){
		$this->assertNotCount(0, $this->model_Groups->getGroups());
	}

	/**
	 * Test of getting the registrated group with the given ID
	 */
	public function testGetGroup() {
		$obj_group = $this->model_Groups->getGroup(0);
		$this->assertEquals(0, $obj_group->getID());
	}

	/**
	 * Test of getting the user access level for current group
	 */
	public function testGetLevel() {
		$this->assertEquals(Session::FORBIDDEN,$this->model_Groups->getLevel($this->i_userid));
	}

	/**
	 * Test of getting the user access level for the given group
	 * Testing the rights from the site admin in the group admin
	 */
	public function testGetLevelByGroupID() {
		$this->assertEquals(Session::ADMIN,$this->model_Groups->getLevelByGroupID(0,1));
	}

	/**
	 * Test of generates a new group
	 */
	public function testGenerateGroup(){
		$this->assertInstanceOf(Data_Group,$this->model_Groups->generateGroup());
	}

	/**
	 * Test of geting the groups with level from the given user
	 * Expected : no acces in all the groups
	 */
	public function testGetGroupsLevel() {
		$a_expected = array();
		$a_groups	= $this->model_Groups->getGroups();
		foreach($a_groups AS $obj_group){
			$a_expected[$obj_group->getID()] = -1;
		}
		
		$this->assertEquals($a_expected,$this->model_Groups->getGroupsLevel($this->i_userid));
	}

	/**
	 * Test of adding a user to the default groups
	 */
	public function testAddUserDefaultGroups() {
		$this->service_Database->transaction();
		
		$a_groups = array();
		$a_groupsPre	= $this->model_Groups->getGroups();
		foreach($a_groupsPre AS $model_group){
			if( $model_group->isDefault() )		$a_groups[$model_group->getID()] = -1;
		}
		
		$this->model_Groups->addUserDefaultGroups($this->i_userid,1);
		
		$a_keys = array_keys($a_groups);
		foreach($a_keys AS $i_key){
			$a_groups[$i_key] = $this->model_Groups->getLevelByGroupID($i_key, $this->i_userid);
		}
		
		foreach($a_keys AS $i_key){
			$this->assertEquals(1, $a_groups[$i_key],"Wrong level in default group ".$i_key.".  Level ".$a_groups[$i_key]." instead of 1.");
		}
	}
}
?>