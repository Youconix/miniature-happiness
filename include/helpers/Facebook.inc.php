<?php 
/** 
 * Helper for Facebook GUI items                                                
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed    01/06/08                                                         
 * @see 	  include/openID/Facebook.inc.php									
 * @see		  include/openID/OpenAuth.inc.php									
 *                                                                              
 * Scripthulp framework is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or            
 * (at your option) any later version.                                          
 *                                                                              
 * Scripthulp framework is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                
 * GNU General Public License for more details.                                 
 *                                                                              
 * You should have received a copy of the GNU Lesser General Public License     
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */

if( !class_exists('OpenAuth') )
	require_once(NIV.'include/openID/OpenAuth.inc.php');
	
if( !class_exists('Facebook') )
	require_once(NIV.'include/openID/Facebook.inc.php');

class Helper_Facebook extends Facebook {
	private $s_postUrl;
	private $s_iconUrl;
	
	/**
	 * Displays the login screen
	 */
	public function loginScreen(){
		$this->a_loggedOut[]	= '';
		$this->a_unautorized[]	= '';
		$this->a_loggedIn[]	 = 'registration.setFacebookLogin(response.authResponse.accessToken,response.authResponse.userID)';
		
		$this->checkSDK();
		
		$service_Template	= Memory::services('Template');
		$service_Language	= Memory::services('Language');
		
		$service_Template->set('loginButton','<a href="javascript:login()" class="button">'.$service_Language->get('language/menu/login').'</a>');
	}
	
	/**
	 * Displays the registration login screen
	 */
	public function registrationScreen(){
		$this->a_loggedOut[]	= '';
		$this->a_unautorized[]	= '';
		$this->a_loggedIn[]	 = 'registration.setFacebookRegistration(response.authResponse.accessToken,response.authResponse.userID)';
		
		$this->s_permissions	= 'email';
		
		$this->checkSDK();
		
		$service_Template	= Memory::services('Template');
		$service_Language	= Memory::services('Language');
		
		$service_Template->set('loginButton','<a href="javascript:login()" class="button">'.$service_Language->get('language/registration/facebookLogin').'</a>');
	}
	
	/**
	 * Displays a like button
	 * 
	 * @return string	The button
	 */
	public function likeButton(){
		$this->checkSDK();
		
		return '<fb:like send="true" width="450" show_faces="true" />';
	}
	
	/**
	 * Displays the activity feead 
	 * 
	 * @param array $a_actions		The actions to display
	 * @return string	The feed
	 */
	public function activityFeed($a_actions = array()){
		$this->checkSDK();
		
		if( count($a_actions) > 0 ){
			$s_actions	= ' action="'.implode(',',$a_actions).'"';
		}
		else {
			$s_actions	= '';
		}
		
		return '<fb:activity 
			site="'.$this->s_protocol.$_SERVER['HTTP_HOST'].'"
			app_id="'.$this->i_appID.'"
			'.$s_actions.'>
			</fb:activity>';
	}
	
	/**
	 * Performs a post to the Facebook wall
	 * 
	 * @todo	Implement methode Helper_Facebook:performPost()
	 */
	public function performPost(){
		$this->checkSDK();
		
		$s_post	= '';
      
      return $s_post;
	}
}
?>
