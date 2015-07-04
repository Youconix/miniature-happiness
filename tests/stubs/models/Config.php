<?php
namespace tests\stubs\models;

class Config implements \Config{
    private $settings;
    private $s_language;
    private $s_templateDir;
    private $s_layout;
    private $s_base = '';
    private $s_page;
    private $s_protocol;
    private $bo_ajax;
    private $s_command;
    
    public function __construct(\Settings $settings){
        $this->settings = $settings;
        
        $this->s_language = 'en_UK';
        $this->s_page = 'php_unit';
        
        if (! defined('DB_PREFIX')) {
            define('DB_PREFIX', '');
        }
        
        if (! defined('BASE')) {
            define('BASE', NIV);
        }
        
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
     * Returns the settings service
     *
     * @return \Settings The service
     */
    public function getSettings(){
        return $this->settings;
    }
    
    /**
     * Collects the installed languages
     *
     * @return array The installed languages
    */
    public function getLanguages(){
        $a_languages = array('nl_NL', 'en_UK');
        return $a_languages;
    }
    
    /**
     * Detects the template directory and layout
    */
    public function detectTemplateDir(){
        $this->s_templateDir = 'default';
        
        if (defined('LAYOUT')) {
            $this->s_layout = LAYOUT;
        }
    }
    
    /**
     * Returns the template directory
     *
     * @return String The template directory
    */
    public function getTemplateDir(){
        return $this->s_templateDir;
    }
    
    /**
     * Returns the loaded template directory
     *
     * @return String template directory
    */
    public function getStylesDir();
    
    public function getSharedStylesDir();
    
    /**
     * Returns the main template layout
     *
     * @return String The layout
    */
    public function getLayout();
    
    /**
     * Returns the current language from the user
     *
     * @return string The language code
    */
    public function getLanguage(){
        return $this->s_language;
    }
    
    /**
     * Returns the used protocol
     *
     * @return String protocol
    */
    public function getProtocol(){
        return $this->s_protocol;
    }
    
    /**
     * Checks if the connection is via SSL/TSL
     *
     * @return bool True if the connection is encrypted
    */
    public function isSLL(){
        return ($this->getProtocol() == 'https');
    }
    
    /**
     * Returns the current page
     *
     * @return String page
    */
    public function getPage(){
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
    public function setPage($s_page, $s_command, $s_layout = 'default'){
        $this->s_page = $s_page;
        $this->s_command = $s_command;
        $this->s_layout = $s_layout;
    }
    
    /**
     * Sets the layout
     *
     * @param string $s_layout
     *            The layout
    */
    public function setLayout($s_layout){
        $this->s_layout = $s_layout;
    }
    
    /**
     * Checks if ajax-mode is active
     *
     * @return boolean if ajax-mode is active
    */
    public function isAjax(){
        return $this->bo_ajax;
    }
    
    /**
     * Sets the framework in ajax-mode
    */
    public function setAjax(){
        $this->bo_ajax = true;
    }
    
    /**
     * Returns the request command
     *
     * @return String The command
    */
    public function getCommand(){
        return $this->s_command;
    }
    
    /**
     * Returns the server host
     *
     * @return String The host
    */
    public function getHost(){
        return 'localhost';
    }
    
    /**
     * Returns the path to the website root
     * This value gets set in {LEVEL}
     *
     * @return String path
    */
    public function getBase(){
        return $this->s_base;
    }
    
    /**
     * Returns the login redirect url
     *
     * @return string The url
    */
    public function getLoginRedirect(){
        $s_page = $this->getBase() . 'index/view';
        
        return $s_page;
    }
    
    /**
     * Returns the logout redirect url
     *
     * @return string The url
    */
    public function getLogoutRedirect(){
        $s_page = $this->getBase() . 'index/view';
        
        return $s_page;
    }
    
    /**
     * Returns the registration redirect url
     *
     * @return string The url
    */
    public function getRegistrationRedirect(){
        $s_page = $this->getBase() . 'index/view';
        
        return $s_page;
    }
    
    /**
     * Returns if the normal login is activated
     *
     * @return boolean True if the normal login is activated
    */
    public function isNormalLogin(){
        return true;
    }
    
    public function isLDAPLogin(){
        return false;
    }
    
    public function getLoginTypes(){
        return array('normal');
    }
    
    public function getOpenAuth(){
        return array();
    }
    
    public function isOpenAuthEnabled($s_name){
        return false;
    }
    
    /**
     * Returns the log location (default admin/data/logs/)
     *
     * @return string The location
    */
    public function getLogLocation(){
        return str_replace(NIV, WEBSITE_ROOT, DATA_DIR) . 'logs' . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Returns the maximun log file size
     *
     * @return int The maximun size in bytes
    */
    public function getLogfileMaxSize(){
        return \Config::LOG_MAX_SIZE;
    }
    
    /**
     * Returns the admin name and email for logging
     *
     * @return array The name and email
    */
    public function getAdminAddress(){
        return array(
            'name' => 'phpunit',
            'email' => 'test@phpunit.org'
        );
    }
    
    /**
     * Returns if SSL is enabled
     *
     * @return int The SSL code
     * @see \core\services\Settings
    */
    public function isSslEnabled(){
        return false;
    }
    
    public function isMobile(){
        return true;
    }
}