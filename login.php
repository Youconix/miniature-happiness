<?php
/**
 * Log in page                              
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed    07/07/2014                                                     
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
define('NIV','./');
include(NIV.'include/BaseLogicClass.php');

class Login extends BaseLogicClass  {
	private $service_Authorization;
	private $model_User;
  private $block_Login;

	/**
	 * PHP5 constructor
	 */
	public function __construct(){
		$this->init();

		if( $_SERVER['REQUEST_METHOD'] != 'POST' ){
			$this->checkAutologin();
				
			$this->form();
		}
		else if( isset($this->post['command']) && $this->post['command'] == 'expired' ){
			$this->expired();
		}
		else {
			$this->login();
		}

		$this->header();
		
		$this->menu();

		$this->footer();
	}

	/**
	 * Inits the class Login
	 */
	protected function init(){
		$this->init_get	= array(
			'type'	=> 'string',
			'code'	=> 'string'
		);
		$this->init_post = array(
		'username'	=> 'string-DB',
		'password_old'	=> 'string-DB',
		'password'	=> 'string-DB',
		'password2'	=> 'string-DB',
		'autologin'	=> 'ignore'
		);
		
		$this->forceSSL();
		
		parent::init();

		$this->service_Authorization  = \core\Memory::services("Authorization");
		$this->model_User   = \core\Memory::models('User');
    $this->block_Login  = \core\Memory::blocks('Login');
	}
	
	/**
	 * Checks if auto login is enabled
	 */
	private function checkAutologin(){
		$service_Cookie	= \core\Memory::services('Cookie');
		if( !$service_Cookie->exists('autologin') ){
			return;
		}

		$s_fingerprint	= \core\Memory::services('Session')->getFingerprint();
		$a_data	= explode(';',$service_Cookie->get('autologin'));
			
		if( $a_data[0] != $s_fingerprint ){
			$service_Cookie->delete('autologin','/');
			return;
		}

		/* Check auto login */
		$a_login = $this->service_Authorization->performAutoLogin($a_data[1]);
		if( is_null($a_login) ){
			$service_Cookie->delete('autologin','/');
			return;
		}
		
		$service_Cookie->set('autologin',implode(';',$a_data),'/');
		$this->setLogin($a_login);
	}

	/**
	 * Generates the login form
	 */
	private function form(){
    $this->block_Login->form();
	}

	/**
	 * Checks the login data
	 */
	private function login(){		
		if( trim($this->post['username']) == '' || trim($this->post['password']) == '' ){
			$this->form();
			return;
		}

		(isset($this->post['autologin']) )? 	$bo_autologin	= true : $bo_autologin	= false;
		$a_login	= $this->service_Authorization->login($this->post['username'],$this->post['password'],$bo_autologin);
		
		if( is_null($a_login) ){
			$this->form();
			return;
		}

		$service_Cookie	= \core\Memory::services('Cookie');
		if( isset($this->post['autologin']) ){
			$s_fingerprint	= \core\Memory::services('Session')->getFingerprint();
			$service_Cookie->set('autologin',$s_fingerprint.';'.$a_login['autologin'],'/');
		}
		
		/* Check for expire */
		if( $a_login['password_expired'] == '1' ){
			$this->service_Session->set('expired',$a_login);
			$this->expiredScreen();
			return;
		}

		$this->setLogin($a_login);
	}
	
	/**
	 * Displays the password expires screen
	 * Regular login only
	 * 
	 * @param string $s_notice		The form notice, optional
	 */
	private function expiredScreen($s_notice = ''){
	}
	
	/**
	 * Changes the expired password
	 * Regular login only
	 */
	private function expired(){
		if( !$this->service_Session->exists('expired') ){
			header('location: login.php');
			exit();
		}
		
		$a_data	= $this->service_Session->get('expired');
		
		if( $this->post['password_old'] == '' || $this->post['password'] == '' || $this->post['password2'] == ''){
			$this->expiredScreen($this->service_Language->get('language/registration/addHint/fieldsEmpty'));
			return;
		}
		if( $this->post['password'] != $this->post['password2']){
			$this->expiredScreen($this->service_Language->get('language/registration/passwordIncorrect'));
			return;
		}

		if( !$this->model_User->changePassword($a_data['id'],$a_data['nick'],$this->post['password_old'],$this->post['password']) ){
			$this->expiredScreen($this->service_Language->get('language/login/currentPasswordIncorrect'));
			return;
		}
		
		$this->service_Session->delete('expired');
		$this->setLogin($a_data);
	}
	
	/**
	 * Sets the login session
	 * 
	 * @param array $a_data		The login data
	 */
	private function setLogin($a_data){
		$s_page = $this->getRedirection($a_data);
		
		$this->service_Session->setLogin($a_data['id'],$a_data['nick'],$a_data['lastLogin']);
		
		header('location: '.$s_page);
		exit();
	}
	
	/**
	 * Returns the redirection page
	 * 
	 * @param array $a_data		The login data
	 * @return string	The redirection page
	 */
	private function getRedirection($a_data){
		if( $this->service_Session->exists('page') ){
			if( $this->service_Session->get('page') != 'logout.php' )
				return NIV.$this->service_Session->get('page');
				
			$this->service_Session->delete('page');
		}
				
		return NIV.'index.php';
	}
}

$obj_Login = new Login();
unset($obj_Login);
