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
	protected $bo_sdkLoaded = false;
	protected $i_appID;
	protected $s_appSecret;
	protected $a_loggedOut	= array();
	protected $a_unautorized	= array();
	protected $a_loggedIn	= array();
	protected $s_permissions = '';

	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		parent::__construct();

		$service_Facebook	= Memory::services('Xml')->cloneService();

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
		$service_Security	= Memory::services('Security');

		$s_token	= $this->obj_SDK->getAccessToken();
		$this->service_Session->set('token',$s_token);

		$i_user	= $this->obj_SDK->getUser();
		try {
			$a_profile	= $this->obj_SDK->api('/me','GET');
			return $service_Security->secureStringDB($a_profile['name']);
		}
		catch(FacebookAPIException $e){
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
		$service_Security	= Memory::services('Security');

		$s_token	= $this->obj_SDK->getAccessToken();
		$this->service_Session->set('token',$s_token);

		$i_user	= $this->obj_SDK->getUser();
		try {
			$a_profile	= $this->obj_SDK->api('/me','GET');
				
			return array('username'=>$service_Security->secureStringDB($a_profile['name']),'email'=>$service_Security->secureStringDB($a_profile['mail']));
		}
		catch(FacebookApiException $e){
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
		$s_token	= $this->getToken();
		$this->service_CurlManager->performGetCall($this->s_logoutUrl.$s_token,array());
	}

	/**
	 * Returns the user token
	 *
	 * @return string	The token
	 */
	protected function getToken(){
		return $this->service_Session->get('token');
	}


	/**
	 * Checks if the SDK is allready loaded
	 */
	protected function checkSDK(){
		if( $this->bo_sdkLoaded )
		return;

		$s_locale	= Memory::services('Language')->get('language/locale');
		
		if( !empty($this->s_permissions) )		$this->s_permissions = ', {scope : \''.$this->s_permissions.'\'}';
		
		$s_sdk	= '/* Load namespace */
		var head	= document.getElementsByTagName("html")[0];
		head.setAttribute("xmlns:fb","http://ogp.me/ns/fb#");				
				
		window.fbAsyncInit = function() {
			/* Load main tag */
			var container	= document.getElementsByTagName("body")[0];
			
			var fb_root	= document.createElement("div");
			fb_root.id	= "fb-root";
		  	container.insertBefore(fb_root,container.firstChild);
				
		    // init the FB JS SDK
    		FB.init({
		      appId      : "'.$this->i_appID.'", // App ID from the App Dashboard
		      channelUrl : "https://'.$_SERVER['HTTP_HOST'].'/'.Memory::getBase().'/openID/channelFacebook.php", // Channel File for x-domain communication
		      status     : true, // check the login status upon init?
		      cookie     : true, // set sessions cookies to allow your server to access the session?		       
      		  oauth: true,
		      xfbml      : true  // parse XFBML tags on this page?
		  });
		  
			FB.getLoginStatus(function(response) {
	  			if (response.status === "connected" ){
	  				// logged in
				    '.implode(";\n",$this->a_loggedIn).'
	  			} 
	  			else if (response.status === "not_authorized"){
				    // 	not_authorized
				    '.implode(";\n",$this->a_unautorized).'
	  			}
	  			else {
				    // not_logged_in
				    '.implode(";\n",$this->a_loggedOut).'
	  			}	
 			});
  		};
  		
  		function login() {
		    FB.login(function(response) {
		        if( response.authResponse ){
		            '.implode(";\n",$this->a_loggedIn).'
		        } else {
		            window.location = "index.php";
		        }
		    });
		}

		  // Load the SDK\'s source Asynchronously
		  // Note that the debug version is being actively developed and might 
		  // contain some type checks that are overly strict. 
		  // Please report such bugs using the bugs tool.
		  (function(d, debug){
		     var js, id = "facebook-jssdk", ref = d.getElementsByTagName("script")[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement("script"); js.id = id; js.async = true;
		     js.src = "//connect.facebook.net/'.$s_locale.'/all" + (debug ? "/debug" : "") + ".js";
		     ref.parentNode.insertBefore(js, ref);
		   }(document, /*debug*/ false));
   		';

		$obj_sdk	= Memory::helpers('HTML')->javascript($s_sdk);
		Memory::services('Template')->headerLink($obj_sdk);
		 
		$this->bo_sdkLoaded	= true;
	}
}
?>
