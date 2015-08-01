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
     * 
     * @var \Facebook\Facebook
     */
    protected $fb;

    /**
     * Sets up necessary services through the autoloader.
     *
     * @param \Cookie $cookie            
     * @param \Builder $builder            
     * @param \Logger $logs            
     * @param \Session $session            
     * @param \Headers $headers            
     * @param \Config $config            
     * @param \core\models\User $user;            
     */
    public function __construct(\Cookie $cookie, \Builder $builder, \Logger $logs, \Session $session, \Headers $headers, \Config $config, \core\models\User $user)
    {
        parent::__construct($cookie, $builder, $logs, $session, $headers, $config, $user);
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
     * Initiate Facebook login process, ending in sending the client a URL Redirect to a login page on the Facebook domain.
     */
    public function startLogin()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = [
            'email'
        ];
        
        $s_url = $this->config->getHost() . $this->config->getBase() . '/authorization/facebook/do_login';
        $s_url = str_replace('//', '/', $s_url);
        
        $login_url = $helper->getLoginUrl($this->config->getProtocol() . $s_url, $permissions);
        header('Location: ' . $login_url);
        exit();
    }

    /**
     * This function attempts login locally, after returning from Facebook's domain.
     *
     * @see \core\models\LoginParent::do_login()
     * @return integer (
     *         1: Facebook id blacklisted,
     *         2: Facebook id is not verified,
     *         3: Facebook id is unknown (ie. new user). )
     */
    public function do_login()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            die();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            session_destroy();
            die();
        } catch (\Exception $e) {
            echo ($e->getMessage());
        }
        
        if (! isset($accessToken)) {
            session_destroy();
            die();
        }
        // Logged in!
        $_SESSION['facebook_access_token'] = (string) $accessToken; // Storing the token for later use.
        $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']); // <-- This is handy if you want to do multiple requests.
        $user_node = $this->fb->get('/me?fields=id,name,email,verified')->getGraphUser();
        $permissions_node = $this->fb->get('/me/permissions')->getGraphEdge();
        
        $user_node_id = $user_node->getId();
        $user_node_name = $user_node->getName();
        $user_node_email = $user_node->getField('email');
        $user_node_is_verified = boolval($user_node->getField('verified'));
        
        /*
        print_r("<p>
            ID: " . $user_node->getId() . "<br>
            Name: " . $user_node->getName() . "<br>
            Email: " . $user_node->getField('email') . "<br>
            Verified: " . $user_node->getField('verified') . "</p>");
        */
        
        foreach ($permissions_node->getIterator() as $permission) {
            if ($permission->getField('permission') == 'email') {
                print_r($permission->getField('permission') . ' ' . $permission->getField('status'));
                break;
            }
        }
        $permission_node->getIterator()->rewind();
            
            /* Check the login combination */
        $this->builder->select('users', '*');
        $this->builder->getWhere()->addAnd(array(
            'nick',
            'password',
            'active',
            'loginType'
        ), array(
            's',
            's',
            's',
            's'
        ), array(
            $s_username,
            $s_passwordHash,
            '1',
            'normal'
        ));
        $service_Database = $this->builder->getResult();
        
        if ($service_Database->num_rows() == 0) {
            /* Check old way */
            $s_password = $this->hashPassword($s_password, $s_username);
            $this->builder->select('users', '*');
            $this->builder->getWhere()->addAnd(array(
                'nick',
                'password',
                'active',
                'loginType'
            ), array(
                's',
                's',
                's',
                's'
            ), array(
                $s_username,
                $s_password,
                '1',
                'normal'
            ));
            $service_Database = $this->builder->getResult();
            if ($service_Database->num_rows() == 0) {
                return;
            }
            
            /* Update user record */
            $i_id = $service_Database->result(0, 'id');
            $builder = clone $this->builder;
            $builder->update('users', 'password', 's', $s_passwordHash)
                ->getWhere()
                ->addAnd('id', 'i', $i_id);
            $builder->getResult();
        }
        
        $a_data = $service_Database->fetch_assoc();
        $user = $this->user->createUser();
        $user->setData($a_data[0]);
        if ($bo_autologin) {
            $this->setAutoLogin($user);
        }
        return parent::perform_login($user);
    }

    /**
     */
    public function do_registration()
    {}

    /**
     *
     * @param \core\models\data\User $user
     *            The data of the User in question
     */
    protected function register(\core\models\data\User $user)
    {}
}