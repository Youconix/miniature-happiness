<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testUser extends GeneralTest
{

    private $service_Builder;

    private $model_User;

    private $s_username;

    private $service_Database;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/models/User.inc.php');
        $this->loadStub('DummyDAL');
        $this->loadStub('DummyQueryBuilder');
        $this->loadStub('DummyHashing');
        $this->loadStub('DummyRandom');
        $this->loadStub('DummySession');
        $this->loadStub('DummyModelGroupData');
        $this->loadStub('DummyGroups');
        $this->loadStub('DummyModelUserData');
        $this->loadStub('DummyValidation');
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->service_Database = new DummyDAL();
        $service_Validation = new DummyValidation();
        $this->service_Builder = new DummyQueryBuilder($this->service_Database);
        $service_Random = new DummyRandom();
        $service_Hashing = new DummyHashing($service_Random);
        $service_Session = new DummySession();
        $model_GroupsData = new DummyModelGroupData();
        $model_Groups = new DummyGroups($model_GroupsData);
        $model_UserData = new DummyModelUserData();
        
        $this->model_User = new \core\models\User($this->service_Builder, $service_Validation, $service_Hashing, $service_Session, $model_Groups, $model_UserData);
        $this->s_username = 'System';
    }

    public function tearDown()
    {
        $this->model_User = null;
        
        parent::tearDown();
    }

    /**
     * Test of getting the requested users
     *
     * @test
     */
    public function getUsersById()
    {
        $a_data = array(
            1 => array(
                'id' => 1
            ),
            10 => array(
                'id' => 10
            ),
            19 => array(
                'id' => 19
            ),
            21 => array(
                'id' => 21
            )
        );
        
        $builder = $this->service_Builder->createBuilder();
        $builder->getDatabase()->enqueueData($a_data);
        
        $a_keys = array_keys($a_data);
        $a_users = $this->model_User->getUsersById($a_keys);
        $this->assertEquals(4, count($a_users));
        foreach ($a_users as $obj_user) {
            $this->assertInstanceOf('\core\models\data\DataUser', $obj_user);
            $this->assertTrue(in_array($obj_user->getID(), $a_keys), 'Id ' . $obj_user->getID() . ' not in expected set.');
        }
    }

    /**
     * Test of getting the requested user
     *
     * @test
     */
    public function get()
    {
        $i_userid = 0;
        $obj_user = $this->model_User->get($i_userid);
        
        $this->assertInstanceOf('\core\models\data\DataUser', $obj_user);
        $this->assertEquals($i_userid, $obj_user->getID());
    }

    /**
     * Test of collecting 25 of the users sorted on nick.
     * Start from the given position, default 0
     *
     * @test
     */
    public function getUsers()
    {
        $a_data = array(
            0 => array(
                'id' => 1,
                'amount' => 4
            ),
            1 => array(
                'id' => 10
            ),
            2 => array(
                'id' => 19
            ),
            3 => array(
                'id' => 21
            )
        );
        
        $builder = $this->service_Builder->createBuilder();
        $builder->getDatabase()->enqueueData($a_data);
        $builder->getDatabase()->enqueueData(array(
            0 => array(
                'amount' => 4
            )
        ));
        
        
        $a_users = $this->model_User->getUsers();

        $this->assertEquals(4,$a_users['number']);
        
        foreach ($a_users['data'] as $obj_user) {
            $this->assertInstanceOf('\core\models\data\DataUser', $obj_user);
        }
    }

    /**
     * Test of searching an user
     *
     * @test
     */
    public function searchUser()
    {
        $a_users = $this->model_User->searchUser($this->s_username);
        
        $this->assertTrue(is_array($a_users), 'Expected searchUser to return an array.');
        $this->assertTrue(array_key_exists('data', $a_users), 'Expected key number.');
        $this->assertTrue(array_key_exists('number', $a_users), 'Expected key number.');
    }

    /**
     * Test of chaning the saved password with known current password
     *
     * @test
     */
    public function changePassword()
    {
        $this->assertFalse($this->model_User->changePassword(0, $this->s_username, '', ''));
    }

    /**
     * Test of creating a new user object
     *
     * @test
     */
    public function createUser()
    {
        $this->assertInstanceOf('\core\models\data\DataUser', $this->model_User->createUser());
    }

    /**
     * Test of checking if the username is available
     *
     * @test
     */
    public function checkUsername()
    {
        $this->assertTrue($this->model_User->checkUsername($this->s_username));
    }

    /**
     * Test of checking or the given email address is available
     *
     * @test
     */
    public function checkEmail()
    {
        $this->assertTrue($this->model_User->checkEmail($this->s_username . '@example.com'));
    }

    /**
     * Test of collecting the site admins (control panel)
     *
     * @test
     */
    public function getSiteAdmins()
    {
        $this->assertEquals(array(), $this->model_User->getSiteAdmins());
    }

    /**
     * Test of getting the id from all the activated users
     *
     * @test
     */
    public function getUserIDs()
    {
        $this->assertEquals(array(), $this->model_User->getUserIDs());
    }
}
?>
