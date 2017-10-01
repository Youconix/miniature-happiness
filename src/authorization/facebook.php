<?php
namespace authorization;

class Facebook extends  \authorization\Authorization  {
    /**
     * 
     * @var \youconix\core\models\LoginFacebook
     */
    
    private $login;
    
    /**
     * Constructor
     *
     * @param \Request $request
     * @param \Language $language
     * @param \Output $template
     * @param \youconix\core\models\User $user
     * @param   \youconix\core\models\LoginFacebook    $login
     */
    public function __construct(
        \Request $request,
        \Language $language,
        \Output $template,
        \youconix\core\models\User $user,
        \youconix\core\models\LoginFacebook $login
        )
    {
        parent::__construct($request, $language, $template, $user);
        
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