<?php

/**
 * Facebook OpenID class
 *
 * @author:		Rachelle Scheijen <rachelle.scheijen@unixerius.nl>
 * @copyright	The au pair BV	2013
 * @since     1.0
 * @changed   01/06/2013
 * @see include/openID/OpenAuth.inc.php
 */
class Facebook extends OpenAuth {
  protected $service_Security;
	protected $bo_sdkLoaded = false;
	protected $i_appID;
	protected $s_appSecret;
	protected $a_loggedOut	= array();
	protected $a_unautorized	= array();
	protected $a_loggedIn	= array();
	protected $s_permissions = '';

	/**
	 * PHP 5 constructor
   * 
   * @param \core\services\Xml $service_XML   The XML service
   * @param \core\services\Security $service_Security   The security handler
	 */
	public function __construct(\core\services\Xml $service_XML,  \core\services\Security $service_Security){
		parent::__construct();

		$service_Facebook	= $service_XML->cloneService();
    $this->service_Security = $service_Security;

		$service_Facebook->load(DATA_DIR.'settings/facebook.xml');

		$this->i_appID		= $service_Facebook->get('facebook/appID');
		$this->s_appSecret	= $service_Facebook->get('facebook/appSecret');
	}

	/**
	 * Performs the login
	 */
	public function login(){
	}

	/**
	 * Completes the login
	 *
	 * @param String $s_code	The response code
	 * @return String	The username, otherwise null
	 */
	public function loginConfirm($s_code){
		$s_token	= $this->obj_SDK->getAccessToken();
		$this->service_Session->set('token',$s_token);

		$i_user	= $this->obj_SDK->getUser();
		try {
			$a_profile	= $this->obj_SDK->api('/me','GET');
			return $this->service_Security->secureStringDB($a_profile['name']);
		}
		catch(\FacebookAPIException $e){
			return null;
		}
	}

	/**
	 * Performs the registration
	 */
	public function registration(){
		header('location: '.$this->s_registrationUrl);
		exit();
	}

	/**
	 * Completes the registration
	 *
	 * @param String $s_code	The response code
	 * @return	array	The login data, otherwise null
	 */
	public function registrationConfirm($s_code){
		$s_token	= $this->obj_SDK->getAccessToken();
		$this->service_Session->set('token',$s_token);

		$i_user	= $this->obj_SDK->getUser();
		try {
			$a_profile	= $this->obj_SDK->api('/me','GET');
				
			return array('username'=>$this->service_Security->secureStringDB($a_profile['name']),'email'=>$this->service_Security->secureStringDB($a_profile['mail']));
		}
		catch(\FacebookApiException $e){
			return null;
		}
	}

	/**
	 * Collects the user data
	 *
	 * @return object	The response data
	 */
	protected function getData(){
		$s_token	= $this->getToken();

		echo('https://graph.facebook.com/me?access_token='.$s_token);
		return file_get_contents('https://graph.facebook.com/me?access_token='.$s_token);
	}

	/**
	 * Performs the logout
	 */
	public function logout(){
	}

	/**
	 * Returns the user token
	 *
	 * @return string	The token
	 */
	protected function getToken(){
		return $this->service_Session->get('token');
	}
}
?>
