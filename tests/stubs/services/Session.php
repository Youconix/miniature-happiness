<?php
namespace tests\stubs\services;

class Session implements \Session
{

    private $a_set = array();

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
     * Logs the admin in with the given userid and username
     * Admin session wil be restored at logout
     * Destroys the current session array
     *
     * @param int $i_userid
     *            of the user
     * @param string $s_username
     *            of the user
     * @param int $i_lastLogin
     *            login as a timestamp
     */
    public function setLoginTakeover($i_userid, $s_username, $i_lastLogin){}
    
    /**
     * Destroys the users login session
     */
    public function destroyLogin(){
        if ($this->exists('login'))
            $this->delete('login');
        if ($this->exists('userid'))
            $this->delete('userid');
        if ($this->exists('username'))
            $this->delete('username');
        if ($this->exists('fingerprint'))
            $this->delete('fingerprint');
        if ($this->exists('lastLogin'))
            $this->delete('lastLogin');
        if ($this->exists('type'))
            $this->delete('type');
        
        if ($this->exists('last_login')) {
            $a_login = $this->get('last_login');
            $this->setLogin($a_login['userid'], $a_login['username'], $a_login['lastLogin']);
        }
    }
    
    /**
     * Returns the visitors browser fingerprint
     *
     * @return String fingerprint
    */
    public function getFingerprint(){
        return sha1(time());
    }
}
?>

