<?php
class DummyConfig extends \core\models\Config{

    /**
     * Loads the language
     */
    protected function loadLanguage()
    {
        $this->s_language = 'en_UK';
    }
    
    public function setLanguage($s_language){
        $this->s_language = $s_language;
    }

    /**
     * Collects the installed languages
     *
     * @return array The installed languages
     */
    public function getLanguages()
    {
        $a_languages = array('nl_NL', 'en_UK');
        return $a_languages;
    }

     /**
     * Sets the default values
     *
     * @param core\services\Settings $service_Settings
     *            The settings service
     */
    protected function setDefaultValues($service_Settings)
    {
        if (! defined('DB_PREFIX')) {
            define('DB_PREFIX', '');
        }

        $this->s_base = '';
        
        if (! defined('BASE')) {
            define('BASE', NIV);
        }

        $this->s_page = 'php_unit';

        /* Get protocol */
        $this->s_protocol = 'http';

        $this->bo_ajax = false;

        if (! defined('LEVEL')) {
            define('LEVEL', '/');
        }

        $this->s_command = 'index';
        if (! defined('WEBSITE_ROOT')) {
            define('WEBSITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->s_base);
        }
    }

    /**
     * Loads the template directory
     */
    protected function loadTemplateDir()
    {
        $this->s_templateDir = 'default';

        if (defined('LAYOUT')) {
            $this->s_layout = LAYOUT;
        }
    }

    /**
     * Returns the server host
     *
     * @return String The host
     */
    public function getHost()
    {
        return 'localhost';
    }

    public function getLoginRedirect()
    {
        $s_page = $this->getBase() . 'index/view';

        return $s_page;
    }

    public function getLogoutRedirect()
    {
        $s_page = $this->getBase() . 'index/view';

        return $s_page;
    }

    public function getRegistrationRedirect()
    {
        $s_page = $this->getBase() . 'index/view';

        return $s_page;
    }

    /**
     * Returns if the normal login is activated
     *
     * @return boolean True if the normal login is activated
     */
    public function isNormalLogin()
    {
        return true;
    }

    /**
     * Returns if the facebook login is activated
     *
     * @return boolean True if the facebook login is activated
     */
    public function isFacebookLogin()
    {
        return false;
    }

    /**
     * Returns if the openOD login is activated
     *
     * @return boolean True if the openID login is activated
     */
    public function isOpenIDLogin()
    {
       return false;
    }

    /**
     * Returns if the LDAP login is activated
     *
     * @return boolean True if the LDAP login is activated
     */
    public function isLDAPLogin()
    {
        return false;
    }

    /**
     * Returns the logging setting
     *
     * default Create log files in the given location (see getLogLocation)
     * error_log Use the webserver error log
     * sys_log Use the system log
     * [else] defined log location in constant LOGGER
     *
     * @return object The logger object
     *
     */
    public function logging()
    {
        if (defined('LOGGER')) {
            $s_type = LOGGER;
        }

        $s_type = 'default';

        switch ($s_type) {
            case 'default':
                $obj_logger = \Loader::Inject('\core\services\logger\LoggerDefault', array(
                $this->getLogLocation(),
                $this->getLogfileMaxSize()
                ));
                break;
        }

        return $obj_logger;
    }

    /**
     * Returns the log location (default admin/data/logs/)
     *
     * @return string The location
     */
    public function getLogLocation()
    {
        return str_replace(NIV, WEBSITE_ROOT, DATA_DIR) . 'logs' . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the maximun log file size
     *
     * @return int The maximun size in bytes
     */
    public function getLogfileMaxSize()
    {
         return Config::LOG_MAX_SIZE;
    }

    /**
     * Returns the admin name and email for logging
     *
     * @return array The name and email
     */
    public function getAdminAddress()
    {
        return array(
            'name' => 'phpunit',
            'email' => 'test@phpunit.org'
        );
    }
}