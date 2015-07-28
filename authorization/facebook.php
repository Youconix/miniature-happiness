<?php
namespace authorization;

class Facebook extends  \authorization\Authorization  {
    /**
     * 
     * @var \core\models\LoginFacebook
     */
    
    private $login;
    
    /**
     * Constructor
     *
     * @param \Input $input    The input parser
     * @param \Config $config
     * @param \Language $language
     * @param \Output $template
     * @param \Header $header
     * @param \Menu $menu
     * @param \Footer $footer
     * @param \core\models\User $user
     * @param \Headers $headers
     * @param   \core\models\LoginFacebook    $login
     */
    public function __construct(
        \Input $input,
        \Config $config,
        \Language $language,
        \Output $template,
        \Header $header,
        \Menu $menu,
        \Footer $footer,
        \core\models\User $user,
        \Headers $headers,
        \core\models\LoginFacebook $login
        )
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer, $user, $headers);
        
        $this->login = $login;
    }
    
    protected function init(){
        $this->s_current = "facebook";
        
        parent::init();
    }
    
    /**
     * Shows the login screen
     */
    protected function login_screen() {
        //Starting login process begins here
    }
    
    /**
     * Performs the login
    */
    protected function do_login() {
        // Callback goes here
    }
    
    /**
     * Shows the registation screen
    */
    protected function registration_screen() {
        // if do_login() does not know user, go here.
    }
    
    /**
     * Performs the registration
    */
    protected function do_registration() {
        
    }
    
}