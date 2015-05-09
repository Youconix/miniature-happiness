<?php
namespace tests\stubs\services;

class DummySession extends Session
{

    private $a_set = array();

    public function __construct()
    {}

    public function __destruct()
    {}

    /**
     * Sets the session with the given name and content
     *
     * @param String $s_sessionName
     *            of the session
     * @param mixed $s_sessionData
     *            of the session
     */
    public function set($s_sessionName, $s_sessionData)
    {
        \core\Memory::type('string', $s_sessionName);
        
        /* Set session */
        $this->a_set[$s_sessionName] = $s_sessionData;
    }

    /**
     * Deletes the session with the given name
     *
     * @param String $s_sessionName
     *            of the session
     * @throws IOException if the session does not exist
     */
    public function delete($s_sessionName)
    {
        \core\Memory::type('string', $s_sessionName);
        
        if (! $this->exists($s_sessionName)) {
            throw new \IOException('Session ' . $s_sessionName . ' does not exist');
        }
        
        unset($this->a_set[$s_sessionName]);
    }

    /**
     * Collects the content of the given session
     *
     * @param String $s_sessionName
     *            name of the session
     * @return String asked session
     * @throws IOException if the session does not exist
     */
    public function get($s_sessionName)
    {
        \Core\Memory::type('string', $s_sessionName);
        
        if (! $this->exists($s_sessionName)) {
            throw new \IOException('Session ' . $s_sessionName . ' does not exist');
        }
        
        $s_data = $this->a_set[$s_sessionName];
        
        return $s_data;
    }

    /**
     * Checks or the given session exists
     *
     * @param String $s_sessionName
     *            name of the session
     * @return boolean True if the session exists, false if it does not
     */
    public function exists($s_sessionName)
    {
        \core\Memory::type('string', $s_sessionName);
        
        if (isset($this->a_set[$s_sessionName])) {
            return true;
        }
        
        return false;
    }

    /**
     * Renews the given session
     *
     * @param String $s_sessionName
     *            The name of the session
     */
    public function renew($s_sessionName)
    {
        \core\Memory::type('string', $s_sessionName);
        
        if ($this->exists($s_sessionName)) {
            $this->a_set[$s_sessionName] = $this->a_set[$s_sessionName];
        }
    }

    /**
     * Destroys all sessions currently set
     */
    public function destroy()
    {
        $this->a_set = array();
    }

    /**
     * Logges the user in and sets the login-session
     *
     * @param int $i_userid
     *            of the user
     * @param String $s_username
     *            of the user
     * @param int $i_lastLogin
     *            login as a timestamp
     */
    public function setLogin($i_userid, $s_username, $i_lastLogin)
    {
        $this->a_set = array();
    }

    /**
     * Checks or the user is logged in and haves enough rights.
     * Define the groep and level to overwrite the default rights for the page
     *
     * @param int $i_group
     *            id, optional
     * @param int $i_level
     *            level, optional
     */
    public function checkLogin($i_group = -1, $i_level = -1)
    {
        define('USERID', 9999);
    }
}
?>

