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
     * @var \Facebook\GraphNodes\GraphUser
     */
    protected $user_node;
    
    /**
     * @var \Facebook\GraphNodes\GraphEdge
     */
    protected $permissions_node;

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
        ob_start();
        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $this->logs->error('Graph returned an error: ' . $e->getMessage());
            session_destroy();
            die();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $this->logs->error('Facebook SDK returned an error: ' . $e->getMessage());
            session_destroy();
            die();
        } catch (\Exception $e) {
            $this->logs->error($e->getMessage());
        }
        
        $s_content = ob_get_contents();
        ob_clean();
        
        if (! isset($accessToken)) {
            session_destroy();
            die();
        }
        // Logged in!
        $_SESSION['facebook_access_token'] = (string) $accessToken; // Storing the token for later use.
        $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']); // <-- This is handy if you want to do multiple requests.
        $this->user_node = $this->fb->get('/me?fields=id,name,email,verified')->getGraphUser();
        $this->permissions_node = $this->fb->get('/me/permissions')->getGraphEdge();
        
        $user_node_id = $this->user_node->getId();
        $user_node_name = $this->user_node->getName();
        $user_node_email = $this->user_node->getField('email');
        $user_node_is_verified = boolval($this->user_node->getField('verified'));
        
        
        if ( ! $user_node_is_verified ) {
            return 2;
            // And die.
        }
            
            /* Check the login combination */
        $this->builder->select('users', '*');
        $this->builder->getWhere()->addAnd(array(
            'external_id',
            'loginType'
        ), array(
            's',
            's'
        ), array(
            $user_node_id,
            'facebook'
        ));
        $service_Database = $this->builder->getResult();
        
        if ($service_Database->num_rows() == 0) {
            // Check for preexisting (v1) facebook-type logins
            $this->builder->select('users', '*');
            $this->builder->getWhere()->addAnd(array(
                'email',
                'active',
                'loginType'
            ), array(
                's',
                's',
                's'
            ), array(
                $user_node_email,
                '1',
                'normal'
            ));
            $service_Database = $this->builder->getResult();
            if ($service_Database->num_rows() == 0) {
                return 3;
            }
            
            // Update user record
            $i_id = $service_Database->result(0, 'id');
            $builder = clone $this->builder;
            $builder->update('users', 'external_id', 's', $user_node_id)
                ->getWhere()
                ->addAnd('id', 'i', $i_id);
            $builder->getResult();
        }
        
        $a_data = $service_Database->fetch_assoc();
        $user = $this->user->createUser();
        $user->setData($a_data[0]);

        parent::perform_login($user);
        
        //if it gets here, user is blocked.
        return 1;
    }

    /**
     */
    public function do_registration()
    {
        $s_email = $this->user_node->getField('email');
        if( is_null($s_email) ){
            $s_email = '';
        }
        $i_external_id = $this->user_node->getId();
        $s_username = $this->user_node->getName();
        
        $user = $this->user->createUser();
        $user->setExternalID($i_external_id);
        $user->setUsername($s_username);
        $user->setEmail($s_email);
        $user->setLoginType('facebook');
        $user->setActivation('');
        $user->enableAccount();
        $user->setBot(false);
        $this->register($user);
    }

    /**
     * Peruses the given Permissions Node for the status of the given permission.
     * 
     * Returns null if said permission is not found.
     * @param string $permisson
     * @return string || null
     */
    private function permissionFor(string $permission)
    {
        $s_return = null;
        foreach ($this->permissions_node->getIterator() as $p) {
            if ($p->getField('permission') == $permission ) {
                $s_return = $p->getField('status');
                break;
            }
        }
        $permission_node->getIterator()->rewind();
        return $s_return;
    }

    /**
     *
     * @param \core\models\data\User $user
     *            The data of the User in question
     */
    protected function register(\core\models\data\User $user)
    {
        $user->save();
       
        $this->session->set('userid',$user->getID());
        $this->headers->redirect('/authorisation/registration2/index');
    }
}