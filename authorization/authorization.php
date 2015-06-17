<?php
namespace authorization;

abstract class Authorization extends \includes\BaseLogicClass {
    /**
     *
     * @var \core\services\Headers
     */
    protected $headers;
    
    /**
     * 
     * @var \core\models\User
     */
    protected $user;
    
    protected $a_types;
    
    protected $s_current;
    
    protected $s_notice;
    
    /**
     * Constructor
     *
     * @param \core\Input $input    The input parser
     * @param \core\models\Config $config
     * @param \core\services\Language $language
     * @param \core\services\Template $template
     * @param \core\classes\Header $header
     * @param \core\classes\Menu $menu
     * @param \core\classes\Footer $footer
     * @param \core\models\User $user
     * @param \core\services\Headers $headers
     */
    public function __construct(\core\Input $input,\core\models\Config $config,
        \core\services\Language $language,\core\services\Template $template,\core\classes\Header $header,\core\classes\Menu $menu,
        \core\classes\Footer $footer,\core\models\User $user,\core\services\Headers $headers)
    {
        $this->user = $user;
        $this->headers = $headers;
    
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
    }
    
    /**
     * Inits the class Authorization
     */
    protected function init()
    {    
        parent::init();
        
        $this->s_notice = '';
    
        $this->a_types = $this->config->getLoginTypes();
    
        if (! in_array($this->s_current,$this->a_types)) {
            $this->headers->redirect('/authorization/registration_'.$this->a_types[0].'/index');
        }
    }
    
    protected function setLoginTypes(){
        foreach ($this->a_types as $s_login) {
            if( $s_login == $this->s_current ){ continue; }
        
            $this->template->setBlock('openID', array(
                'key' => $s_login,
                'image' => strtolower($s_login),
                'text' => ($this->language->exists('registration/registration_' . $s_login))? t('registration/registration_' . $s_login) : ucfirst($s_login)
            ));
        }
    }
    
    /**
     * Shows the login screen
     */
    abstract protected function login_screen();
    
    /**
     * Performs the login
     */
    abstract protected function do_login();
    
    /**
     * Shows the registation screen
     */
    abstract protected function registration_screen();

    /**
     * Performs the registration
     */
    abstract protected function do_registration();
    
    /**
     * Checks if the username is taken
     *
     * @param string $s_username
     *            The username
     * @return boolean if the username is free, otherwise false
     */
    protected function checkUsername($s_username)
    {
        return $this->user->checkUsername($s_username,$this->s_current);
    }
    
    /**
     * Checks if the email is taken
     *
     * @param string $s_email
     *            email address
     * @return boolean if the email is free, otherwise false
     */
    protected function checkEmail($s_email)
    {
        return $this->user->checkEmail($s_email,$this->s_current);
    }
}