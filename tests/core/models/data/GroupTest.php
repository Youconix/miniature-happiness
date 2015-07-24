<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

class testData_Group extends \tests\GeneralTest
{

    private $model_Group;
    private $service_Database;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/models/data/Group.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->service_Database = new \tests\stubs\database\DAL();
        $service_Validation = new \tests\stubs\services\Validation();
        $service_Builder = new \tests\stubs\services\QueryBuilder($this->service_Database);
        
        $this->i_id = 10000;
        $this->s_name = 'test group';
        $this->i_default = 1;
        $this->s_description = 'test group';
        
        $this->model_Group = new \core\models\data\Group($service_Builder, $service_Validation);
    }

    public function tearDown()
    {
        $this->model_Group = null;
        
        parent::tearDown();
    }

    /**
     * Test of setting the group data
     *
     * @test
     */
    public function setData()
    {
        $a_data = array(
            'id' => 1,
            'name' => 'test name',
            'automatic' => 1,
            'description' => 'test description'
        );
        $this->model_Group->setData($a_data);
        
        $this->assertEquals($a_data['id'], $this->model_Group->getID());
        $this->assertEquals($a_data['name'], $this->model_Group->getName());
        $this->assertTrue($this->model_Group->isDefault());
        $this->assertEquals($a_data['description'], $this->model_Group->getDescription());
    }

    /**
     * Test of setting the name
     *
     * @test
     */
    public function setNames()
    {
        $s_newName = 'lalala';
        $this->model_Group->setName($s_newName);
        $this->assertEquals($s_newName, $this->model_Group->getName());
    }

    /**
     * Test of setting the description
     *
     * @test
     */
    public function setDescription()
    {
        $s_description = 'oke';
        $this->model_Group->setDescription($s_description);
        $this->assertEquals($s_description, $this->model_Group->getDescription());
    }

    /**
     * Test of setting the group as not default
     *
     * @test
     */
    public function setDefault()
    {
        $this->model_Group->setDefault(false);
        $this->assertFalse($this->model_Group->isDefault());
    }

    /**
     * Test of geting the user access level
     * Expected Session::ANONYMOUS (no access)
     *
     * @test
     */
    public function getLevelByGroupID()
    {
        $this->assertEquals(\core\services\Session::ANONYMOUS, $this->model_Group->getLevelByGroupID($this->i_id));
    }

    /**
     * Test of getting all the members from the group
     * Expected : empty array
     *
     * @test
     */
    public function getMembersByGroup()
    {
        $this->assertEquals(array(), $this->model_Group->getMembersByGroup());
    }

    /**
     * Saves the new group
     *
     * @test
     */
    public function save()
    {
        try {
            $this->model_Group->save();
            $this->fail('Expected validation Exception');
        } catch (ValidationException $ex) {} catch (Exception $ex) {
            $this->fail('Unexpected exception : ' . $ex->getMessage());
        }
        
       $this->service_Database->i_numRows = 1;
       $this->service_Database->a_data  = array(0=>array('id'=>4,'staff'=>0),1=>array('id'=>5,'staff'=>1));
       
        
        $this->model_Group->setName('test name');
        $this->model_Group->setDescription('test description');
        $this->model_Group->setDefault(true);
        $this->model_Group->save();
    }

    /**
     * Test of saving the changed group
     *
     * @test
     */
    public function persist()
    {
        $this->model_Group->setData(array(
            'id' => 12121,
            'name' => 'newName',
            'automatic' => 1,
            'description' => ''
        ));
        
        try {
            $this->model_Group->persist();
            $this->fail('Expected ValidationException');
        } catch (ValidationException $ex) {} catch (Exception $ex) {
            $this->fail('Unexpected exception : ' . $ex->getMessage());
        }
        
        $this->model_Group->setDescription('test description');
        $this->model_Group->persist();
    }

    /**
     * Deletes the group
     *
     * @test
     */
    public function deleteGroup()
    {
        $this->model_Group->deleteGroup();
    }

    /**
     * Test of adding an user to the group
     *
     * @test
     */
    public function addUser()
    {
        $this->model_Group->addUser(10, 1);
        $this->model_Group->addUser(10, - 1);
    }

    /**
     * Test of editing the users access rights for this group
     *
     * @test
     */
    public function editUser()
    {
        $this->model_Group->editUser(- 1, 0);
    }

    /**
     * Adds all the users to this group if the group is default
     *
     * @test
     */
    public function addUsersToDefault()
    {
        $this->model_Group->addUsersToDefault();
    }

    /**
     * Deletes the user from the group
     *
     * @test
     */
    public function deleteUser()
    {
        $this->model_Group->deleteUser(- 1);
    }

    /**
     * Test of the in use check
     *
     * @test
     */
    public function inUse()
    {
        $this->assertFalse($this->model_Group->inUse());
    }
}
?>