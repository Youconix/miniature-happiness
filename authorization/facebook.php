<?php
namespace authorization;
use Facebook\Facebook;
define('NIV', '../../');
require 'vendor/autoload.php';

class Facebook extends  \authorization\Authorization  {
    /**
     * 
     * @var \core\models\LoginFacebook
     */
    
    private $login;
    
    /**
     * The Facebook API
     * @var \Facebook\Facebook
     */
    private $fb;
    
    /**
     * 
     * @var \Settings
     */
    private $settings;
    
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
        \Settings $settings
        )
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer, $user, $headers);
        
        $this->login = $login;
        $this->settings = $settings;
    }
    
    protected function init(){
        $this->s_current = "facebook";
        $this->fb = new Facebook([
            'app_id' => $this->settings->get('login/facebook/appId'),
            'app_secret' => $this->settings->get('login/facebook/appSecret')
            ]);
        
        parent::init();
        start_session();
    }
    
    /**
     * Initiates the connection with facebook, requesting a URL for a login window on their side and then redirects the client there.
     */
    protected function login_screen() {
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        
        // TODO Use known hostname instead of fixed address, here.
        $login_url = $helper -> getLoginUrl('http://84.27.181.42:8080/login/facebook/do_login', $permissions);
        header('Location: '.$login_url);
        exit();
    }
    
    /**
     * Succesful login from Facebook sends the client here.
    */
    protected function do_login() {
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            die;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            session_destroy();
            die;
        }
        
        if (isset($accessToken)) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $accessToken; // Storing the token for later use.
            /*$fb->setDefaultAccessToken($_SESSION['facebook_access_token']); <-- This is handy if you want to do multiple requests.*/
            $user_node = $fb -> get('/me?fields=id,name,email,verified', $_SESSION['facebook_access_token']) -> getGraphUser();
            // 'User Node' is Facebook nomenclature.
        
            print_r("<p>
            ID: " . $user_node -> getId() . "<br>
            Name: " . $user_node -> getName() . "<br>
            Email: " . $user_node -> getField('email') . "<br>
            Verified: " . $user_node -> getField('verified') . "</p>");
        }
        
        session_destroy();
        exit();
        
        /*
         * In  this order:
         * If user not verified, throw polite error and destroy session.
         * If user not known, do do_registration(), while maintaining session. (Not redirect header, function call.)
         * If user known, log in and destroy session upon success.
         */
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