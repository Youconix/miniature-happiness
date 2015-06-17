<?php
use core\models\Config;
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

class testGroups extends \tests\GeneralTest
{

    private $model_Groups;

    private $i_userid;

    private $i_id;

    private $s_name;

    private $i_default;

    private $s_description;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/models/Groups.inc.php');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->i_userid = 0;
        $this->i_id = 10000;
        $this->s_name = 'test group';
        $this->i_default = 1;
        $this->s_description = 'test group';
        
        $service_Database = new \tests\stubs\database\DAL();
        $service_Validation = new \tests\stubs\services\Validation();
        $service_Security = new \tests\stubs\services\Security($service_Validation);
        $service_Builder = new \tests\stubs\services\QueryBuilder($service_Database);
        $service_Session = new \tests\stubs\services\Session();
        $service_File = new \tests\stubs\services\File();
        $service_Settings = new \tests\stubs\services\Settings();
        $service_Cookie = new \tests\stubs\services\Cookie($service_Security);
        $model_GroupData = new \tests\stubs\models\data\ModelGroupData();
        $model_Config = new \tests\stubs\models\Config($service_File, $service_Settings, $service_Cookie);
        
        $this->model_Groups = new \core\models\Groups($service_Builder, $service_Validation, $model_GroupData, $model_Config);
    }

    public function tearDown()
    {
        $this->model_Groups = null;
        
        parent::tearDown();
    }

    /**
     * Test of getting all the registrated groups
     *
     * @test
     */
    public function getGroups()
    {
        $this->assertCount(0, $this->model_Groups->getGroups());
    }

    /**
     * Test of getting the registrated group with the given ID
     *
     * @test
     * @expectedException OutOfBoundsException
     */
    public function getGroup()
    {
        $obj_group = $this->model_Groups->getGroup(0);
    }

    /**
     * Test of getting the user access level for current group
     *
     * @test
     */
    public function getLevel()
    {
        $this->assertEquals(\core\services\Session::ANONYMOUS, $this->model_Groups->getLevel($this->i_userid));
    }

    /**
     * Test of getting the user access level for the given group
     *
     * @test
     */
    public function getLevelByGroupID()
    {
        $this->assertEquals(\core\services\Session::ANONYMOUS, $this->model_Groups->getLevelByGroupID(0, 1));
    }

    /**
     * Test of generates a new group
     *
     * @test
     */
    public function generateGroup()
    {
        $this->assertInstanceOf('\core\models\data\DataGroup', $this->model_Groups->generateGroup());
    }

    /**
     * Test of geting the groups with level from the given user
     * Expected : no acces in all the groups
     *
     * @test
     */
    public function getGroupsLevel()
    {
        $a_expected = array();
        $a_groups = $this->model_Groups->getGroups();
        foreach ($a_groups as $obj_group) {
            $a_expected[$obj_group->getID()] = - 1;
        }
        
        $this->assertEquals($a_expected, $this->model_Groups->getGroupsLevel($this->i_userid));
    }

    /**
     * Test of adding a user to the default groups
     *
     * @test
     */
    public function addUserDefaultGroups()
    {
        $this->model_Groups->addUserDefaultGroups($this->i_userid, 1);
    }
}
?>