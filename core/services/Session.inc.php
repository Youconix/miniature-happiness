<?php
namespace core\services;

/**
 * Session service class for managing sessions and login status
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2014,2015,2016 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 *        @date 12/01/2006
 *
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class Session extends Service
{

    private $service_QueryBuilder;

    const FORBIDDEN = - 1; // Stil here for backwards compatibility

    const ANONYMOUS = - 1;

    const USER = 0;

    const MODERATOR = 1;

    const ADMIN = 2;

    const FORBIDDEN_COLOR = 'grey'; // Stil here for backwards compatibility

    const ANONYMOUS_COLOR = 'grey';

    const USER_COLOR = 'black';

    const MODERATOR_COLOR = 'green';

    const ADMIN_COLOR = 'red';

    /**
     * PHP 5 constructor
     *
     * @param core\services\Settings $service_Settings
     *            The settings service
     * @param core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     */
    public function __construct(\core\services\Settings $service_Settings, \core\services\QueryBuilder $service_QueryBuilder)
    {
        $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
        $s_sessionSetName = $service_Settings->get('settings/session/sessionName');
        $s_sessionSetPath = $service_Settings->get('settings/session/sessionPath');
        $s_sessionExpire = $service_Settings->get('settings/session/sessionExpire');
        
        if ($s_sessionSetName != '') {
            @session_name($s_sessionSetName);
        }
        if ($s_sessionSetPath != '') {
            @session_save_path($s_sessionSetPath);
        }
        if ($s_sessionExpire != '') {
            @ini_set("session.gc_maxlifetime", $s_sessionExpire);
        }
        
        @session_start();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        session_write_close();
    }

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
        $_SESSION[$s_sessionName] = $s_sessionData;
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
        
        unset($_SESSION[$s_sessionName]);
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
        
        $s_data = $_SESSION[$s_sessionName];
        
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
        
        if (isset($_SESSION[$s_sessionName])) {
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
            $_SESSION[$s_sessionName] = $_SESSION[$s_sessionName];
        }
    }

    /**
     * Destroys all sessions currently set
     */
    public function destroy()
    {
        session_destroy();
        $_SESSION = array();
    }

    /**
     * Logges the user in and sets the login-session
     * Destroys the current session array
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
        /* Get data */
        $this->service_QueryBuilder->update('users', 'lastLogin', 'i', time())
            ->getWhere()
            ->addAnd('id', 'i', $i_userid);
        $this->service_QueryBuilder->getResult();
        
        session_regenerate_id(true);
        $_SESSION = array();
        
        $this->set('login', '1');
        $this->set('userid', $i_userid);
        $this->set('username', $s_username);
        $this->set('fingerprint', $this->getFingerprint());
        $this->set('lastLogin', $i_lastLogin);
    }

    /**
     * Logs the admin in with the given userid and username
     * Admin session wil be restored at logout
     * Destroys the current session array
     *
     * @param int $i_userid
     *            of the user
     * @param String $s_username
     *            of the user
     * @param int $i_lastLogin
     *            login as a timestamp
     */
    public function setLoginTakeover($i_userid, $s_username, $i_lastLogin)
    {
        $a_lastLogin = array(
            'userid' => $this->get('userid'),
            'username' => $this->get('username'),
            'lastLogin' => $this->get('lastLogin')
        );
        
        $this->setLogin($i_userid, $s_username, $i_lastLogin);
        $this->set('last_login', $a_lastLogin);
    }

    /**
     * Destroys the users login session
     */
    public function destroyLogin()
    {
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
    public function getFingerprint()
    {
        return sha1($_SERVER['REMOTE_ADDR'] . '-' . $_SERVER['HTTP_USER_AGENT'] . '-' . $_SERVER['HTTP_HOST'] . '-' . $_SERVER['SERVER_SIGNATURE'] . '-' . strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) . '-' . $_SERVER['HTTP_ACCEPT_ENCODING']);
    }
}
class_alias('\core\services\Session', 'Session');
?>