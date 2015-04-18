<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

if (! class_exists('GeneralTest')) {
    require (NIV . 'tests/GeneralTest.php');
}

class testDataUser extends GeneralTest
{

    private $obj_User;

    private $i_userid;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/models/GeneralUser.inc.php');
        require_once (NIV . 'core/models/data/DataUser.inc.php');
        
        $this->loadStub('DummyDAL');
        $this->loadStub('DummyQueryBuilder');
        $this->loadStub('DummySecurity');
        $this->loadStub('DummyModelGroupData');
        $this->loadStub('DummyGroups');
        $this->loadStub('DummyLanguage');
        $this->loadStub('DummyHashing');
    }

    public function setUp()
    {
        parent::setUp();
        
        $service_Database = new DummyDAL();
        $service_Security = new DummySecurity($service_Database);
        $service_Builder = new DummyQueryBuilder($service_Database);
        $model_GroupsData = new DummyModelGroupData();
        $model_Groups = new DummyGroups($model_GroupsData);
        $service_Language = new DummyLanguage();
        $service_Hashing = new DummyHashing();
        
        $this->i_userid = 0;
        $this->obj_User = new \core\models\data\DataUser($service_Builder, $service_Security, $service_Hashing, $model_Groups, $service_Language);
    }

    public function tearDown()
    {
        $this->obj_User = null;
        
        parent::tearDown();
    }

    /**
     * Tests loading the data
     *
     * @test
     * @expectedException DBException
     */
    public function loadData()
    {
        $this->obj_User->loadData($this->i_userid);
    }

    /**
     * Test of setting the username
     *
     * @test
     */
    public function setUsername()
    {
        $s_username = 'test user';
        
        $this->obj_User->setUsername($s_username);
        $this->assertEquals($s_username, $this->obj_User->getUsername());
    }

    /**
     * Test of setting the email address
     *
     * @test
     */
    public function setEmail()
    {
        $s_email = 'tester@example.com';
        
        $this->obj_User->setEmail($s_email);
        $this->assertEquals($s_email, $this->obj_User->getEmail());
    }

    /**
     * Test of setting a new password
     * Note : username has to be set first!
     *
     * @test
     */
    public function setPassword()
    {
        $s_username = 'my username';
        $s_password = 'new password';
        
        $this->obj_User->setUsername($s_username);
        $this->obj_User->setPassword($s_password);
        
        try {
            $this->obj_User->save();
            
            $this->fail('Expected validation exception.');
        } catch (ValidationException $ex) {
            $s_error = $ex->getMessage();
            
            if (strpos($s_error, 'Error validating non existing field s_password') !== false) {
                $this->fail('Username is not set');
            }
        }
    }

    /**
     * Test of setting the account as a normal or system account
     *
     * @test
     */
    public function setBot()
    {
        $this->assertFalse($this->obj_User->isBot());
        $this->obj_User->setBot(true);
        $this->assertTrue($this->obj_User->isBot());
    }

    /**
     * Test of (Un)Blocking the account
     *
     * @test
     */
    public function setBlocked()
    {
        $this->obj_User->setBlocked(true);
        $this->assertTrue($this->obj_User->isBlocked());
        
        $this->obj_User->setBlocked(false);
        $this->assertFalse($this->obj_User->isBlocked());
    }

    /**
     * Test of getting the profile
     *
     * @test
     */
    public function getProfile()
    {
        $s_profile = 'lal lal al a';
        
        $this->assertEquals('', $this->obj_User->getProfile());
        $this->obj_User->setProfile($s_profile);
        $this->assertEquals($s_profile, $this->obj_User->getProfile());
    }

    /**
     * Test of collecting the groups where the user is in
     *
     * @test
     */
    public function getGroups()
    {
        $this->assertEquals(array(), $this->obj_User->getGroups());
    }

    /**
     * Test of collecting the access level for the current group
     *
     * @test
     */
    public function getLevel()
    {
        $this->assertEquals(Session::ANONYMOUS, $this->obj_User->getLevel());
    }

    /**
     * Test of changing the password
     *
     * @test
     */
    public function changePassword()
    {
        $s_password = 'new password';
        
        $this->obj_User->setPassword($s_password);
    }

    /**
     * Test of disabling and enabling the account
     *
     * @test
     */
    public function isEnabled()
    {
        $this->assertFalse($this->obj_User->isEnabled());
        
        $this->obj_User->enableAccount();
        $this->assertTrue($this->obj_User->isEnabled());
        
        $this->obj_User->disableAccount();
        $this->assertFalse($this->obj_User->isEnabled());
    }

    /**
     * Test of collecting the color corosponding the users level
     *
     * @test
     */
    public function getColor()
    {
        $this->assertEquals(\core\services\Session::ANONYMOUS_COLOR, $this->obj_User->getColor());
    }

    /**
     * Test of checking if the visitor has moderator rights
     * Expected : no rights
     *
     * @test
     */
    public function isModerator()
    {
        $this->assertFalse($this->obj_User->isModerator());
    }

    /**
     * Test of checking if the visitor has administrator rights
     * Expected : no rights
     *
     * @test
     */
    public function isAdmin()
    {
        $this->assertFalse($this->obj_User->isAdmin());
    }

    /**
     * Test of collecting the set user language
     *
     * @test
     */
    public function getLanguage()
    {
        $this->assertNull($this->obj_User->getLanguage());
    }
}
?>
