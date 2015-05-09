<?php
namespace tests\stubs\services;

if (! class_exists('\core\services\Cookie')) {
    require (NIV . 'core/services/Cookie.inc.php');
}

class DummyCookie extends \core\services\Cookie
{

    private $a_data = array();

    public function __construct()
    {}

    /**
     * Deletes the cookie with the given name and domain
     *
     * @param String $s_cookieName
     *            The name of the cookie
     * @param String $s_domain
     *            The domain of the cookie
     * @throws Exception if the cookie does not exist
     */
    public function delete($s_cookieName, $s_domain)
    {
        \core\Memory::type('string', $s_cookieName);
        \core\Memory::type('string', $s_domain);
        
        if (! $this->exists($s_cookieName)) {
            throw new \Exception("Cookie " . $s_cookieName . " does not exist.");
        }
        
        if (isset($this->a_data[$s_cookieName])) {
            unset($this->a_data[$s_cookieName]);
        }
    }

    /**
     * Sets the cookie with the given name and data
     *
     * @param String $s_cookieName
     *            The name of the cookie
     * @param String $s_cookieData
     *            The data to put into the cookie
     * @param String $s_domain
     *            The domain the cookie schould work on, default /
     * @param String $s_url
     *            The URL the cookie schould work on, optional
     * @param int $i_secure
     *            1 if the cookie schould be https-only otherwise 0, optional
     * @return boolean True if the cookie has been set, false if it has not
     */
    public function set($s_cookieName, $s_cookieData, $s_domain, $s_url = "", $i_secure = 0)
    {
        \core\Memory::type('string', $s_cookieName);
        \core\Memory::type('string', $s_cookieData);
        \core\Memory::type('string', $s_domain);
        \core\Memory::type('string', $s_url);
        \core\Memory::type('int', $i_secure);
        
        $this->a_data[$s_cookieName] = $s_cookieData;
    }

    /**
     * Receives the content from the cookie with the given name
     *
     * @param String $s_cookieName
     *            The name of the cookie
     * @return String The requested cookie
     * @throws Exception if the cookie does not exist
     */
    public function get($s_cookieName)
    {
        \core\Memory::type('string', $s_cookieName);
        
        if ($this->exists($s_cookieName) == 0) {
            throw new \Exception("Cookie " . $s_cookieName . " does not exist.");
        }
        
        /* Read cookie */
        $s_cookie = $this->a_data[$s_cookieName];
        
        return $s_cookie;
    }

    /**
     * Checks if the given cookie exists
     *
     * @param String $s_cookieName
     *            The name of the cookie you want to check
     * @return boolean True if the cookie exists, false if it does not
     */
    public function exists($s_cookieName)
    {
        \core\Memory::type('string', $s_cookieName);
        
        if (! isset($this->a_data[$s_cookieName])) {
            return false;
        }
        
        return true;
    }
}
?>