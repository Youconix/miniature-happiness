<?php
namespace authorization;
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
        $this->login = $login;
        
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer, $user, $headers);
    }
    
    protected function init(){
        $this->init_get = array(
            'code'  => 'ignore-keep',
            'state' => 'ignore-keep'
        );
        $this->s_current = "facebook";
        
        try{
        $fb_app_id = $this->config->getSettings()->get('login/openAuth/facebook/appId');
        $fb_app_secret = $this->config->getSettings()->get('login/openAuth/facebook/appSecret');
        $this->fb = new \Facebook\Facebook([
            'app_id' => $fb_app_id,
            'app_secret' => $fb_app_secret
            ]);
        } catch (\Exception $e) {
            print_r($e);
        }
        
        parent::init();
    }
    
    /**
     * Initiates the connection with facebook, requesting a URL for a login window on their side and then redirects the client there.
     */
    protected function login_screen() {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email'];
        
        $s_url = $this->config->getHost().
            $this->config->getBase().
            '/authorization/facebook/do_login';
        $s_url = str_replace('//','/',$s_url);
        // TODO Use known hostname instead of fixed address, here.
        $login_url = $helper -> getLoginUrl(
            $this->config->getProtocol().
            $s_url, $permissions);
        header('Location: '.$login_url);
        exit();
    }
    
    /**
     * Succesful login from Facebook sends the client here.
    */
    protected function do_login() {
        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            die;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            session_destroy();
            die;
        } catch(\Exception $e) {
            echo($e->getMessage());
        }
        
        if (isset($accessToken)) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $accessToken; // Storing the token for later use.
            /*$fb->setDefaultAccessToken($_SESSION['facebook_access_token']); <-- This is handy if you want to do multiple requests.*/
            $user_node = $this->fb -> get('/me?fields=id,name,email,verified', $_SESSION['facebook_access_token']) -> getGraphUser();
            // 'User Node' is Facebook nomenclature.
        
            print_r("<p>
            ID: " . $user_node -> getId() . "<br>
            Name: " . $user_node -> getName() . "<br>
            Email: " . $user_node -> getField('email') . "<br>
            Verified: " . $user_node -> getField('verified') . "</p>");
        } else {
            echo("No token!");
            die;
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