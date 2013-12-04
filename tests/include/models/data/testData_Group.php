<?php
define('NIV',dirname(__FILE__).'/../../../');

require(NIV.'tests/include/GeneralTest.php');

class testData_Group extends GeneralTest {
	private $service_Database;
	private $model_Group;
	private $i_id;
	private $s_name;
	private $i_default;
	private $s_description;

	public function __construct(){
		parent::__construct();

		require_once(NIV.'include/models/data/Data_Group.inc.php');
	}

	public function setUp(){
		parent::setUp();
		
		$this->service_Database = Memory::services('Database');

		$this->i_id = 10000;
		$this->s_name = 'test group';
		$this->i_default = 1;
		$this->s_description = 'test group';
		
		$a_data = array('id'=>$this->i_id,'name'=>$this->s_name,'automatic'=>$this->i_default,'description'=>$this->s_description);
		$this->model_Group = new Data_Group($a_data);
	}

	public function tearDown(){
		$this->model_Group = null;

		parent::tearDown();
	}

	/**
	 * Test of setting the group data
	 *
	 * @param array $a_data		The group data
	 */
	public function testSetData() {		
		$this->assertEquals($this->i_id,$this->model_Group->getID());
		$this->assertEquals($this->s_name,$this->model_Group->getName());
		$this->assertTrue($this->model_Group->isDefault());
		$this->assertEquals($this->s_description,$this->model_Group->getDescription());
	}


	/**
	 * Test of setting the name
	 */
	public function testSetName() {
		$s_newName = 'lalala';
		$this->model_Group->setName($s_newName);
		$this->assertEquals($s_newName,$this->model_Group->getName());		
	}

	/**
	 * Test of setting the description
	 */
	public function testSetDescription() {
		$s_description = 'oke';
		$this->model_Group->setDescription($s_description);
		$this->assertEquals($s_description,$this->model_Group->getDescription());
	}

	/**
	 * Test of setting the group as not default
	 */
	public function testSetDefault() {
		$this->model_Group->setDefault(false);
		$this->assertFalse($this->model_Group->isDefault());
	}

	/**
	 * Test of geting the user access level
	 * Expected Session::FORBIDDEN (no access)
	 */
	public function testGetLevelByGroupID() {
		Memory::services('Session');
		$this->assertEquals(Session::FORBIDDEN,$this->model_Group->getLevelByGroupID($this->i_id));
	}

	/**
	 * Test of getting all the members from the group
	 * Expected : empty array
	 */
	public function testGetMembersByGroup() {
		$this->assertEquals(array(),$this->model_Group->getMembersByGroup());
	}

	/**
	 * Saves the new group
	 */
	public function testSave() {
		$this->service_Database->transaction();
		
		$this->addGroup();
		
		$this->service_Database->queryBinded("SELECT id FROM ".DB_PREFIX."groups WHERE name = ?",'s',$this->s_name);
		$i_num = $this->service_Database->num_rows();
		
		$this->service_Database->rollback();
		
		$this->assertEquals(1,$i_num,'Saving of the group failed');
	}

	/**
	 * Test of saving the changed group
	 */
	public function testPersist(){
		$s_newName	= 'newName';
		
		$this->service_Database->transaction();
		
		$this->addGroup();
		
		$this->model_Group->setName($s_newName);
		
		$this->model_Group->persist();
		
		$this->service_Database->queryBinded("SELECT id FROM ".DB_PREFIX."groups WHERE name = ?",'s',$s_newName);
		$i_num = $this->service_Database->num_rows();
		
		$this->service_Database->rollback();
		
		$this->assertEquals(1,$i_num,'Updating of the group failed');
	}

	/**
	 * Deletes the group
	 */
	public function testDeleteGroup() {
		$this->service_Database->transaction();
		
		$this->addGroup();
		
		$this->model_Group->deleteGroup();
		
		$this->service_Database->queryBinded("SELECT id FROM ".DB_PREFIX."groups WHERE name = ?",'s',$this->s_name);
		$i_num = $this->service_Database->num_rows();
		
		$this->service_Database->rollback();
		
		$this->assertEquals(0,$i_num,'Removing of the group failed');
	}

	/**
	 * Test of adding an user to the group
	 */
	public function testAddUser(){
		$this->service_Database->transaction();
		
		$this->addUser();
		
		$this->service_Database->queryBinded("SELECT id FROM ".DB_PREFIX."group_users WHERE groupID = ? AND userid = ?",array('i','i'),array($this->i_id,-1));
		$i_num = $this->service_Database->num_rows();
		
		$this->service_Database->rollback();
		
		$this->assertEquals(1, $i_num,'Adding a user to the group failed');
	}

	/**
	 * Test of editing the users access rights for this group
	 */
	public function testEditUser(){
		$this->service_Database->transaction();
		
		$this->addUser();
		$this->model_Group->editUser(-1,0);
		
		$this->service_Database->queryBinded("SELECT level FROM ".DB_PREFIX."group_users WHERE groupID = ? AND userid = ?",array('i','i'),array($this->i_id,-1));
		$i_num = $this->service_Database->num_rows();
		( $i_num > 0 )	? $i_level = $this->service_Database->result(0,'level') : $i_level = null; 
		
		$this->service_Database->rollback();
		
		$this->assertEquals(1, $i_num,'Adding a user to the group failed');
		$this->assertNotNull($i_level,'Updating the user failed');
	}

	/**
	 * Adds all the users to this group if the group is default
	 */
	public function testAddUsersToDefault(){
		$this->service_Database->transaction();
		
		$this->model_Group->addUsersToDefault();
		
		$this->service_Database->queryBinded("SELECT id FROM ".DB_PREFIX."group_users WHERE groupID = ?",'i',$this->i_id);
		$i_num = $this->service_Database->num_rows();
		
		$this->service_Database->rollback();
		
		$this->assertNotEquals(0, $i_num);
	}

	/**
	 * Deletes the user from the group
	 *
	 * @param int $i_userid	The userid
	 */
	public function testDeleteUser(){
		$this->service_Database->transaction();
		
		$this->addUser();
		$this->model_Group->deleteUser(-1);
		
		$this->service_Database->queryBinded("SELECT level FROM ".DB_PREFIX."group_users WHERE groupID = ? AND userid = ?",array('i','i'),array($this->i_id,-1));
		$i_num = $this->service_Database->num_rows(); 
		
		$this->service_Database->rollback();
		
		$this->assertEquals(0, $i_num,'Deleting a user to the group failed');
	}

	/**
	 * Test of the in use check
	 */
	public function testInUse(){
		$this->assertFalse($this->model_Group->inUse());
	}
	
	private function addGroup(){
		$a_data = array('id'=>null,'name'=>$this->s_name,'automatic'=>$this->i_default,'description'=>$this->s_description);
		$this->model_Group = new Data_Group($a_data);
		
		$this->model_Group->save();
	}
	
	private function addUser(){
		$this->model_Group->addUser(-1,2);
	}
}
?>