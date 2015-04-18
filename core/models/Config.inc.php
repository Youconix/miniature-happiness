<?php
namespace core\models;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Config contains the main runtime configuration of the framework.
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Config extends Model implements \SplSubject
{

    protected $service_Settings;

    protected $service_File;

    protected $service_Cookie;

    protected $s_templateDir;

    protected $bo_ajax = false;

    protected $s_base;

    protected $s_page;

    protected $s_protocol;

    protected $s_command = 'view';

    protected $s_layout = 'default';

    protected $a_observers;

    const LOG_MAX_SIZE = 10000000;

    protected $s_language;

    /**
     * PHP 5 constructor
     *
     * @param core\services\File $service_File
     *            The File service
     * @param core\services\Settings $service_Settings
     *            The settings service
     * @param core\services\Cookie $service_Cookie
     *            The cookie service
     */
    public function __construct(\core\services\File $service_File, \core\services\Settings $service_Settings, \core\services\Cookie $service_Cookie)
    {
        $this->service_File = $service_File;
        $this->service_Settings = $service_Settings;
        $this->service_Cookie = $service_Cookie;
        
        $this->a_observers = new \SplObjectStorage();
        
        $this->loadTemplateDir();
        
        $this->loadLanguage();
        
        $this->setDefaultValues($service_Settings);
    }

    /**
     * Returns the settings service
     *
     * @return \core\services\Settings The service
     */
    public function getSettings()
    {
        return $this->service_Settings;
    }

    /**
     * Returns if the object schould be treated as singleton
     *
     * @return boolean True if the object is a singleton
     */
    public static function isSingleton()
    {
        return true;
    }

    public function attach(\SplObserver $observer)
    {
        $this->a_observers->attach($observer);
    }
    
    public function detach(\SplObserver $observer) {
        $this->_observers->detach($observer);
    }

    public function notify()
    {
        foreach ($this->a_observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Loads the language
     */
    protected function loadLanguage()
    {
        /* Check language */
        $a_languages = $this->getLanguages();
        $this->s_language = $this->service_Settings->get('defaultLanguage');
        
        if (isset($_GET['lang'])) {
            if (in_array($_GET['lang'], $a_languages)) {
                $this->s_language = $_GET['lang'];
                $this->service_Cookie->set('language', $this->s_language, '/');
            }
            unset($_GET['lang']);
        } else 
            if ($this->service_Cookie->exists('language')) {
                if (in_array($this->service_Cookie->get('language'), $a_languages)) {
                    $this->s_language = $this->service_Cookie->get('language');
                    /* Renew cookie */
                    $this->service_Cookie->set('language', $this->s_language, '/');
                } else {
                    $this->service_Cookie->delete('language');
                }
            }
    }

    /**
     * Collects the installed languages
     *
     * @return array The installed languages
     */
    public function getLanguages()
    {
        $a_languages = array();
        $a_languageFiles = $this->service_File->readDirectory(NIV . 'language');
        
        foreach ($a_languageFiles as $s_languageFile) {
            if (strpos($s_languageFile, 'language_') !== false) {
                /* Fallback */
                return $this->getLanguagesOld();
            }
            
            if ($s_languageFile == '..' || $s_languageFile == '.' || strpos($s_languageFile, '.') !== false) {
                continue;
            }
            
            $a_languages[] = $s_languageFile;
        }
        
        return $a_languages;
    }

    /**
     * Collects the installed languages
     * Old way of storing
     *
     * @return array The installed languages
     */
    protected function getLanguagesOld()
    {
        $a_languages = array();
        $a_languageFiles = $this->service_File->readDirectory(NIV . 'include/language');
        
        foreach ($a_languageFiles as $s_languageFile) {
            if (strpos($s_languageFile, 'language_') === false)
                continue;
            
            $s_languageFile = str_replace(array(
                'language_',
                '.lang'
            ), array(
                '',
                ''
            ), $s_languageFile);
            
            $a_languages[] = $s_languageFile;
        }
        
        $this->bo_fallback = true;
        
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
            define('DB_PREFIX', $service_Settings->get('settings/SQL/prefix'));
        }
        
        $s_base = $service_Settings->get('settings/main/base');
        if (substr($s_base, 0, 1) != '/') {
            $this->s_base = '/' . $s_base;
        } else {
            $this->s_base = $s_base;
        }
        
        if (! defined('BASE')) {
            define('BASE', NIV);
        }
        
        /* Get page */
        $s_page = $_SERVER['SCRIPT_NAME'];
        while (substr($s_page, 0, 1) == '/') {
            $s_page = substr($s_page, 1);
        }
        
        if ($s_base != '/') {
            if (stripos($s_page, $s_base) !== false) {
                $s_page = substr($s_page, strlen($s_base));
            }
        }
        
        while (substr($s_page, 0, 1) == '/') {
            $s_page = substr($s_page, 1);
        }
        $this->s_page = $s_page;
        
        /* Get protocol */
        $this->s_protocol = ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        
        $this->detectAjax();
        
        if (! defined('LEVEL')) {
            define('LEVEL', '/');
        }
        
        $this->s_command = 'index';
        if (isset($_GET['command'])) {
            $this->s_command = $_GET['command'];
        } else 
            if (isset($_POST['command'])) {
                $this->s_command = $_POST['command'];
            }
        if (! defined('WEBSITE_ROOT')) {
            define('WEBSITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->s_base);
        }
    }

    /**
     * Detects an AJAX call
     */
    protected function detectAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            $this->bo_ajax = true;
        } else 
            if (function_exists('apache_request_headers')) {
                $a_headers = apache_request_headers();
                $this->bo_ajax = (isset($a_headers['X-Requested-With']) && $a_headers['X-Requested-With'] == 'XMLHttpRequest');
            }
        if (! $this->bo_ajax && ((isset($_GET['AJAX']) && $_GET['AJAX'] == 'true') || (isset($_POST['AJAX']) && $_POST['AJAX'] == 'true'))) {
            $this->bo_ajax = true;
        }
    }

    /**
     * Loads the template directory
     */
    protected function loadTemplateDir()
    {
        $s_templateDir = $this->service_Settings->get('settings/templates/dir');
        
        if (isset($_GET['protected_style_dir'])) {
            $s_styleDir = $this->clearLocation($_GET['protected_style_dir']);
            if ($this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts')) {
                $s_templateDir = $s_styleDir;
                $this->service_Cookie->set('protected_style_dir', $s_templateDir, '/');
            } else 
                if ($this->service_Cookie->exists('protected_style_dir')) {
                    $this->service_Cookie->delete('protected_style_dir', '/');
                }
        } else 
            if ($this->service_Cookie->exists('protected_style_dir')) {
                $s_styleDir = $this->clearLocation($this->service_Cookie->get('protected_style_dir'));
                if ($this->service_File->exists(NIV . 'styles/' . $s_styleDir . '/templates/layouts')) {
                    $s_templateDir = $s_styleDir;
                    $this->service_Cookie->set('protected_style_dir', $s_templateDir, '/');
                } else {
                    $this->service_Cookie->delete('protected_style_dir', '/');
                }
            }
        $this->s_templateDir = $s_templateDir;
        
        if (defined('LAYOUT')) {
            $this->s_layout = LAYOUT;
        }
    }

    /**
     * Clears the location path from evil input
     *
     * @param String $s_location            
     * @return String path
     */
    protected function clearLocation($s_location)
    {
        while ((strpos($s_location, './') !== false) || (strpos($s_location, '../') !== false)) {
            $s_location = str_replace(array(
                './',
                '../'
            ), array(
                '',
                ''
            ), $s_location);
        }
        
        return $s_location;
    }

    /**
     * Returns the template directory
     *
     * @return String The template directory
     */
    public function getTemplateDir()
    {
        return $this->s_templateDir;
    }

    /**
     * Returns the loaded template directory
     *
     * @return String template directory
     */
    public function getStylesDir()
    {
        return 'styles/' . $this->s_templateDir . '/';
    }

    /**
     * Returns the main template layout
     *
     * @return String The layout
     */
    public function getLayout()
    {
        return $this->s_layout;
    }

    /**
     * Returns the current language from the user
     *
     * @return string The language code
     */
    public function getLanguage()
    {
        return $this->s_language;
    }

    /**
     * Returns the used protocol
     *
     * @return String protocol
     */
    public function getProtocol()
    {
        return $this->s_protocol;
    }

    /**
     * Checks if the connection is via SSL/TSL
     *
     * @return bool True if the connection is encrypted
     */
    public function isSLL()
    {
        return ($this->getProtocol() == 'https://');
    }

    /**
     * Returns the current page
     *
     * @return String page
     */
    public function getPage()
    {
        return $this->s_page;
    }

    /**
     * Sets the current page
     *
     * @param String $s_page
     *            The new page
     * @param String $s_command
     *            The new command
     *            @parma String $s_layout The new layout
     */
    public function setPage($s_page, $s_command, $s_layout = 'default')
    {
        $this->s_page = $s_page;
        $this->s_command = $s_command;
        $this->s_layout = $s_layout;
        
        $this->notifyObservers();
    }

    /**
     * Checks if ajax-mode is active
     *
     * @return boolean if ajax-mode is active
     */
    public function isAjax()
    {
        return $this->bo_ajax;
    }

    /**
     * Sets the framework in ajax-
     *
     * @deprecated since version 2
     */
    public function setAjax()
    {
        $this->bo_ajax = true;
    }

    /**
     * Returns the request command
     *
     * @return String The command
     */
    public function getCommand()
    {
        return $this->s_command;
    }

    /**
     * Returns the server host
     *
     * @return String The host
     */
    public function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Returns the path to the website root
     * This value gets set in {LEVEL}
     *
     * @return String path
     */
    public function getBase()
    {
        return $this->s_base;
    }

    public function getLoginRedirect()
    {
        $s_page = $this->getBase() . 'index/view';
        
        if ($this->service_Settings->exists('main/login')) {
            $s_page = $this->getBase() . $this->service_Settings->get('main/login');
        }
        
        return $s_page;
    }

    public function getLogoutRedirect()
    {
        $s_page = $this->getBase() . 'index/view';
        
        if ($this->service_Settings->exists('main/logout')) {
            $s_page = $this->getBase() . $this->service_Settings->get('main/logout');
        }
        
        return $s_page;
    }

    public function getRegistrationRedirect()
    {
        $s_page = $this->getBase() . 'index/view';
        
        if ($this->service_Settings->exists('main/registration')) {
            $s_page = $this->getBase() . $this->service_Settings->get('main/registration');
        }
        
        return $s_page;
    }

    /**
     * Returns if the normal login is activated
     *
     * @return boolean True if the normal login is activated
     */
    public function isNormalLogin()
    {
        if (! $this->service_Settings->exists('login/normalLogin') || $this->service_Settings->get('login/normalLogin') != 1) {
            return false;
        }
        return true;
    }

    /**
     * Returns if the facebook login is activated
     *
     * @return boolean True if the facebook login is activated
     */
    public function isFacebookLogin()
    {
        if (! $this->service_Settings->exists('login/facebook') || $this->service_Settings->get('login/facebook') != 1) {
            return false;
        }
        return true;
    }

    /**
     * Returns if the openOD login is activated
     *
     * @return boolean True if the openID login is activated
     */
    public function isOpenIDLogin()
    {
        if (! $this->service_Settings->exists('login/openID') || $this->service_Settings->get('login/openID') != 1) {
            return false;
        }
        return true;
    }

    /**
     * Returns if the LDAP login is activated
     *
     * @return boolean True if the LDAP login is activated
     */
    public function isLDAPLogin()
    {
        if (! $this->service_Settings->exists('login/ldap') || $this->service_Settings->get('login/ldap') != 1) {
            return false;
        }
        return true;
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
        
        if (! $this->service_Settings->exists('main/logs')) {
            $s_type = 'default';
        } else {
            $s_type = $this->service_Settings->get('main/logs');
        }
        
        switch ($s_type) {
            case 'default':
                $obj_logger = \Loader::Inject('\core\services\logger\LoggerDefault', array(
                    $this->getLogLocation(),
                    $this->getLogfileMaxSize()
                ));
                break;
            
            case 'error_log':
                $obj_logger = \Loader::Inject('\core\services\logger\LoggerErrorLog');
                break;
            
            case 'sys_log':
                $obj_logger = \Loader::Inject('\core\services\logger\LoggerSysLog');
                break;
            
            default:
                $obj_logger = Loader::Inject($s_type);
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
        if (! $this->service_Settings->exists('main/log_location')) {
            return str_replace(NIV, WEBSITE_ROOT, DATA_DIR) . 'logs' . DIRECTORY_SEPARATOR;
        }
        
        return $this->service_Settings->get('main/log_location');
    }

    /**
     * Returns the maximun log file size
     *
     * @return int The maximun size in bytes
     */
    public function getLogfileMaxSize()
    {
        if (! $this->service_Settings->exists('main/log_max_size')) {
            return Config::LOG_MAX_SIZE;
        }
        
        return $this->service_Settings->get('main/log_max_size');
    }

    /**
     * Returns the admin name and email for logging
     *
     * @return array The name and email
     */
    public function getAdminAddress()
    {
        if (! $this->service_Settings->exists('main/admin/email')) {
            $service_QueryBuilder = \core\Memory::services('QueryBuilder')->createBuilder();
            
            /* Send to first user */
            $service_QueryBuilder->select('users', 'nick,email')
                ->getWhere()
                ->addAnd('id', 'i', 1);
            $database = $service_QueryBuilder->getResult();
            $a_data = $database->fetch_assoc();
            
            return array(
                'name' => $a_data[0]['nick'],
                'email' => $a_data[0]['email']
            );
        }
        
        return array(
            'name' => $this->service_Settings->get('main/admin/name'),
            'email' => $this->service_Settings->get('main/admin/email')
        );
    }
}