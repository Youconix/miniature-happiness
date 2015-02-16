<?php
namespace\core\helpers;

/**
 * Helper for Facebook GUI items
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *
 * @see include/openID/Facebook.inc.php
 * @see include/openID/OpenAuth.inc.php Scripthulp framework is free software: you can redistribute it and/or modify
 *      it under the terms of the GNU Lesser General Public License as published by
 *      the Free Software Foundation, either version 3 of the License, or
 *      (at your option) any later version.
 *     
 *      Scripthulp framework is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *      GNU General Public License for more details.
 *     
 *      You should have received a copy of the GNU Lesser General Public License
 *      along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
if (! class_exists('\core\openid\OpenAuth')) {
    require_once (NIV . 'include/openID/OpenAuth.inc.php');
}

if (! class_exists('\core\openid\Facebook')) {
    require_once (NIV . 'include/openID/Facebook.inc.php');
}

class Facebook extends \core\openid\Facebook
{

    private $service_Language;

    private $service_Template;

    /**
     * PHP 5 constructor
     *
     * @param \core\services\Xml $service_XML
     *            The XML service
     * @param \core\services\Security $service_Security
     *            The security handler
     * @param \core\services\Language $service_Language
     *            The language service
     * @param \core\services\Template $service_Template
     *            The template service
     */
    public function __construct(\core\services\Xml $service_XML, \core\services\Security $service_Security, \core\services\Language $service_Language, \core\services\Template $service_Template)
    {
        parent::__construct($service_XML, $service_Security);
        
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;
    }

    /**
     * Displays the login screen
     */
    public function loginScreen()
    {
        $this->a_loggedOut[] = '';
        $this->a_unautorized[] = '';
        $this->a_loggedIn[] = 'registration.setFacebookLogin(response.authResponse.accessToken,response.authResponse.userID)';
        
        $this->checkSDK();
        
        $this->service_Template->set('loginButton', '<a href="javascript:login()" class="button">' . $this->service_Language->get('language/menu/login') . '</a>');
    }

    /**
     * Displays the registration login screen
     */
    public function registrationScreen()
    {
        $this->a_loggedOut[] = '';
        $this->a_unautorized[] = '';
        $this->a_loggedIn[] = 'registration.setFacebookRegistration(response.authResponse.accessToken,response.authResponse.userID)';
        
        $this->s_permissions = 'email';
        
        $this->checkSDK();
        
        $this->service_Template->set('loginButton', '<a href="javascript:login()" class="button">' . $this->service_Language->get('language/registration/facebookLogin') . '</a>');
    }

    /**
     * Displays a like button
     *
     * @return string button
     */
    public function likeButton()
    {
        $this->checkSDK();
        
        return '<fb:like send="true" width="450" show_faces="true" />';
    }

    /**
     * Displays the activity feead
     *
     * @param array $a_actions
     *            actions to display
     * @return string feed
     */
    public function activityFeed($a_actions = array())
    {
        $this->checkSDK();
        
        if (count($a_actions) > 0) {
            $s_actions = ' action="' . implode(',', $a_actions) . '"';
        } else {
            $s_actions = '';
        }
        
        return '<fb:activity 
			site="' . $this->s_protocol . $_SERVER['HTTP_HOST'] . '"
			app_id="' . $this->i_appID . '"
			' . $s_actions . '>
			</fb:activity>';
    }

    /**
     * Performs a post to the Facebook wall
     *
     * @todo Implement methode Helper_Facebook:performPost()
     */
    public function performPost()
    {
        $this->checkSDK();
        
        $s_post = '';
        
        return $s_post;
    }

    /**
     * Checks if the SDK is allready loaded
     */
    protected function checkSDK()
    {
        if ($this->bo_sdkLoaded)
            return;
        
        $s_locale = $this->service_Language->get('language/locale');
        
        if (! empty($this->s_permissions))
            $this->s_permissions = ', {scope : \'' . $this->s_permissions . '\'}';
        
        $s_sdk = '/* Load namespace */
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
		      appId      : "' . $this->i_appID . '", // App ID from the App Dashboard
		      channelUrl : "https://' . $_SERVER['HTTP_HOST'] . '/' . \core\Memory::getBase() . '/openID/channelFacebook.php", // Channel File for x-domain communication
		      status     : true, // check the login status upon init?
		      cookie     : true, // set sessions cookies to allow your server to access the session?		       
      		  oauth: true,
		      xfbml      : true  // parse XFBML tags on this page?
		  });
		  
			FB.getLoginStatus(function(response) {
	  			if (response.status === "connected" ){
	  				// logged in
				    ' . implode(";\n", $this->a_loggedIn) . '
	  			} 
	  			else if (response.status === "not_authorized"){
				    // 	not_authorized
				    ' . implode(";\n", $this->a_unautorized) . '
	  			}
	  			else {
				    // not_logged_in
				    ' . implode(";\n", $this->a_loggedOut) . '
	  			}	
 			});
  		};
  		
  		function login() {
		    FB.login(function(response) {
		        if( response.authResponse ){
		            ' . implode(";\n", $this->a_loggedIn) . '
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
		     js.src = "//connect.facebook.net/' . $s_locale . '/all" + (debug ? "/debug" : "") + ".js";
		     ref.parentNode.insertBefore(js, ref);
		   }(document, /*debug*/ false));
   		';
        
        $obj_sdk = '<javascript src="' . $s_sdk . '" type="text/javascript"></script>';
        $this->service_Template->headerLink($obj_sdk);
        
        $this->bo_sdkLoaded = true;
    }
}
?>
