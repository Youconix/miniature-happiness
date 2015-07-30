<?php
namespace core\models;

/**
 *
 * @author Roxanna Lugtigheid
 * @copyright Youconix
 *           
 */
class LoginFacebook extends \core\models\LoginParent
{
    /**
     * The Facebook API
     * @var \Facebook\Facebook
     */
    protected $fb;
    
    /**
     * (non-PHPdoc)
     *
     * @see \core\models\LoginParent::do_login()
     */
    public function do_login()
    {
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
            $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);//  <-- This is handy if you want to do multiple requests.
            $user_node = $this->fb->get('/me?fields=id,name,email,verified') -> getGraphUser();
            $permissions_node = $this->fb->get('/me/permissions')->getGraphEdge();
        
            print_r("<p>
            ID: " . $user_node -> getId() . "<br>
            Name: " . $user_node -> getName() . "<br>
            Email: " . $user_node -> getField('email') . "<br>
            Verified: " . $user_node -> getField('verified') . "</p>");
        
            foreach ($permissions_node->getIterator() as $permission ) {
                if ($permission->getField('permission' ) == 'email') {
                    print_r($permission->getField('permission').' '.$permission->getField('status'));
                    break;
                }
            } $permission_node->getIterator()->rewind();
        
        
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

    public function init()
    {
        $fb_app_id = $this->config->getSettings()->get('login/openAuth/facebook/appId');
        $fb_app_secret = $this->config->getSettings()->get('login/openAuth/facebook/appSecret');
        $this->fb = new \Facebook\Facebook([
            'app_id' => $fb_app_id,
            'app_secret' => $fb_app_secret
        ]);
    }

    /**
     *
     * @param \core\models\data\User $user
     *            The data of the User in question
     */
    public function register(\core\models\data\User $user)
    {}
    
    public function startLogin() {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email'];
        
        $s_url = $this->config->getHost().
            $this->config->getBase().
            '/authorization/facebook/do_login';
        $s_url = str_replace('//','/',$s_url);
        $login_url = $helper -> getLoginUrl(
            $this->config->getProtocol().
            $s_url, $permissions);
        header('Location: '.$login_url);
        exit();;
    }
}