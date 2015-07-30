<?php
namespace authorization;
use Facebook\GraphNodes\GraphNode;
require 'vendor/autoload.php';

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
        \core\models\LoginFacebook $login,
        \core\services\Session $session
        )
    {
        $this->login = $login;
        
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer, $user, $headers);
    }
    
    protected function init(){
        $this->init_get = array(
            'code'  => 'ignore-keep',
            'state' => 'ignore-keep'
        );
        $this->s_current = "facebook";
        
        $this->login->init();
        parent::init();
    }
    
    /**
     * Initiates the connection with facebook, requesting a URL for a login window on their side and then redirects the client there.
     */
    protected function login_screen() {
        $this->login->startLogin();
    }
    
    /**
     * Succesful login from Facebook sends the client here.
    */
    protected function do_login() {
        if ( empty($_GET['code']) || empty ($_GET['state'])) {
            header('Location: /');
            die;
        }
        
        $i_status = $this->login->do_login();
        switch($i_status) {
            case 1 :
                // Blacklisted
                break;
            case 2 :
                // Email not verified
                break;
            case 3 :
                // Unknown, new user
                $this->do_registration();
                break;
        }
    }
    
    /**
     * Performs the registration
    */
    protected function do_registration() {
        // Upon failure, always destroy session and return FALSE.
        // Upon completion, return TRUE and continue do_login() from where it left off. <-- Is this possible? <-- YES
    }
    
    /**
     * This function is mandated by the parent class, but is not necessary in this context.
    */
    protected function registration_screen() {}
    
}