<?php
namespace authorization;
use Facebook\GraphNodes\GraphNode;
require 'vendor/autoload.php';

/**
 * Controller class for the Facebook-type login mechanism.
 * 
 * This controller is the starting point for the entire process,
 * registring a new user if need be, and logging the user in.
 * 
 * If certain criteria are not met, it will abort. At this time,
 * this only includes a user being already known, and blocked
 * (ie. 'blacklisted'), or the user isn't verified with Facebook.
 * These situations result in errors being displayed.
 * 
 * @author Roxanna Lugtigheid <rlugtigheid@youconix.nl>
 * @copyright Youconix
 */
class Facebook extends  \authorization\Authorization  {
    /**
     * Instance of the Facebook login model
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
                $this->blacklisted();
                break;
            case 2 :
                // Email not verified
                $this->not_verified();
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
        $this->login->do_registration();
    }
    
    /**
     * This function is mandated by the parent class, but is not necessary in this context.
    */
    protected function registration_screen() {}
    
    /**
     * Display notice that the Facebook user trying to log in is blacklisted.
     */
    protected function blacklisted() {
        $this->template->loadView('blacklisted');
        
        $this->template->set('blacklisted_header', t('facebook/blacklisted_header'));
        $this->template->set('blacklisted_line_1', t('facebook/blacklisted_line_1'));
        $this->template->set('blacklisted_line_2', t('facebook/blacklisted_line_2'));
    }
    
    /**
     * Display notice that the Facebook user has not yet verified his/her email address with Facebook.
     */
    protected function not_verified() {
        $this->template->loadView('not_verified');
        
        $this->template->set('not_verified_header', t('facebook/not_verified_header'));
        $this->template->set('not_verified_line_1', t('facebook/not_verified_line_1'));
        $this->template->set('not_verified_line_2', t('facebook/not_verified_line_2'));
    }
}