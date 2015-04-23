<?php
define('NIV', dirname(__FILE__) . '/../../../');
require (NIV . 'tests/GeneralTest.php');

class testLDAP extends GeneralTest
{

    private $service_Settings;

    private $service_LDAP;

    private $s_server;

    private $i_port;

    private $s_username;

    private $s_password;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/exceptions/LdapException.inc.php');
        require_once (NIV . 'core/services/LDAP.inc.php');
        
        $this->loadStub('DummySettings');
        $this->service_Settings = new DummySettings();
        $this->service_Settings->setValue('settings/LDAP/server', 'test_server.loc');
        $this->service_Settings->setValue('settings/LDAP/port', 1050);
        $this->service_Settings->setValue('settings/LDAP/version', 3);
        $this->service_Settings->setValue('settings/LDAP/active', 1);
    }

    public function setUp()
    {
        parent::setUp();
        
        $this->service_LDAP = new \core\services\LDAP($this->service_Settings);
    }

    public function tearDown()
    {
        $this->service_LDAP = null;
        
        parent::tearDown();
    }

    /**
     * Tests connecting to the default LDAP server
     *
     * @expectedException LdapConnectionException
     * @test
     */
    public function bind()
    {
        if (! extension_loaded('ldap')) {
            $this->markTestSkipped(
                'The LDAP extension is not available.'
                );
        }
        
        $this->service_LDAP->bind($this->s_username, $this->s_password);
    }

    /**
     * Test connection to the given LDAP server
     *
     * @expectedException	LdapConnectionException
     * @test
     */
    public function bindManual()
    {
        if (! extension_loaded('ldap')) {
            $this->markTestSkipped(
                'The LDAP extension is not available.'
            );
        }
        
        $this->service_LDAP->bindManual($this->s_server, $this->i_port, $this->s_username, $this->s_password);
    }

    /**
     * Test closing the connection to the current LDAP server
     * Should do nothing
     *
     * @depends bind
     * @test
     */
    public function unbind()
    {
        $this->service_LDAP->unbind();
    }

    /**
     * Test adding a item to the LDAP server
     *
     * @test
     * @depends bind
     * @expectedException	LdapException
     */
    public function add()
    {
        $this->login();
        
        $a_data = array();
        $this->service_LDAP->add('newItem', $a_data);
    }

    /**
     * Tests deleting a item from the LDAP server
     *
     * @test
     * @depends bind
     * @expectedException	LdapException
     */
    public function delete()
    {
        $this->login();
        
        $this->service_LDAP->delete('item');
    }

    /**
     * Test searching on the baseDN on the LDAP server
     *
     * @test
     * @depends bind
     * @expectedException	LdapException
     */
    public function search()
    {
        $this->login();
        
        $this->service_LDAP->search('baseDN', 'searchItem');
    }

    /**
     * Test reading the baseDN on the LDAP server
     *
     * @test
     * @depends bind
     * @expectedException	LdapException
     */
    public function readItem()
    {
        $this->login();
        
        $this->service_LDAP->search('baseDN', 'item');
    }

    /**
     * Tests modifying a item on the LDAP server
     *
     * @test
     * @depends bind
     * @expectedException	LdapException
     */
    public function modify()
    {
        $this->login();
        
        $a_data = array();
        $this->service_LDAP->modify('newItem', $a_data);
    }

    /**
     * Tests renaming a item to a new name
     *
     * @test
     * @depends bind
     * @expectedException	LdapException
     */
    public function rename()
    {
        $this->login();
        
        $this->service_LDAP->rename('old_name', 'new_name', 'new_parent');
    }

    /**
     * Test checking if the login to the LDAP is correct
     *
     * @test
     * @depends bind
     */
    public function checkLogin()
    {
        $this->assertFalse($this->service_LDAP->checkLogin($this->s_server, $this->i_port, $this->s_username, $this->s_password));
    }

    /**
     * Sets the LDAP in debugging mode
     */
    private function login()
    {
        $this->service_LDAP->activateDebug();
        $this->service_LDAP->bindManual($this->s_server, $this->i_port, $this->s_username, $this->s_password);
    }
}