<?php

namespace core\services;

/**
 * Account authorization service
 * Handles registration and login from the accounts                           
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   01/07/2013
 * @see		include/openID/OpenAuth.inc.php
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
class Authorization extends Service{

  protected $service_Cookie;
  protected $service_QueryBuilder;
  protected $service_Logs;
  protected $service_Session;
  protected $service_Mailer;
  protected $service_Random;
  protected $service_Headers;

  /**
   * Inits the service Autorization
   * 
   * @param \core\services\Cookie       $service_Cookie         The cookie handler
   * @param \core\services\QueryBuilder $service_QueryBuilder   The query builder
   * @param \core\services\Logs         $service_Logs           The log service
   * @param \core\services\Session       $service_Session       The session service
   * @param \core\services\Mailer        $service_Mailer        The mailer service
   * @param \core\services\Random        $service_Random        The random service
   * @param \core\services\Headers       $service_Headers       The headers service
   */
  public function __construct(\core\services\Cookie $service_Cookie, \core\services\QueryBuilder $service_QueryBuilder, \core\services\Logs $service_Logs,
   \core\services\Session $service_Session,\core\services\Mailer $service_Mailer,\core\services\Random $service_Random,\core\services\Headers $service_Headers){
    $this->s_openID_dir = NIV . 'core/openID/';
    require_once($this->s_openID_dir . 'OpenAuth.inc.php');

    $this->service_Cookie = $service_Cookie;
    $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
    $this->service_Database = $this->service_QueryBuilder->getDatabase();
    $this->service_Logs = $service_Logs;
    $this->service_Session  = $service_Session;
    $this->service_Mailer = $service_Mailer;
    $this->service_Random = $service_Random;
    $this->service_Headers = $service_Headers;
  }

  /**
   * Registers the user
   * 
   * @param array		$a_data	The form data
   * @param	bool		$bo_skipActivation	Set to true to skip sending the activation email (auto activation)
   * @return bool	True if the user is registrated
   * @throws Exception  If registrating failes
   */
  public function register($a_data, $bo_skipActivation = false){
    $s_username = $a_data[ 'username' ];
    $s_forname = $a_data[ 'forname' ];
    $s_nameBetween = $a_data[ 'nameBetween' ];
    $s_surname = $a_data[ 'surname' ];
    $s_password = $a_data[ 'password' ];
    $s_email = $a_data[ 'email' ];

    try{
      $this->service_QueryBuilder->transaction();

      $s_registrationKey = sha1(time() . ' ' . $s_username . ' ' . $s_email);

      $obj_User = $this->model_User->createUser();
      $obj_User->setUsername($s_username);
      $obj_User->setName($s_forname);
      $obj_User->setNameBetween($s_nameBetween);
      $obj_User->setSurname($s_surname);
      $obj_User->setEmail($s_email);
      $obj_User->setPassword($s_password);
      $obj_User->setActivation($s_registrationKey);
      $obj_User->setBot(false);
      $obj_User->save();

      if( !$bo_skipActivation ){
        $this->sendActivationEmail($s_username, $s_email, $s_registrationKey);
      }

      $this->service_QueryBuilder->commit();

      if( !$bo_skipActivation ){
        $this->model_User->activateUser($s_registrationKey);
      }

      return true;
    }
    catch( \Exception $e ){
      $this->service_QueryBuilder->rollback();

      throw $e;
    }
  }
  
  /**
   * Activates the user 
   * 
   * 
   * @param String $s_code    The activation code
   * @return boolean    True if the user is activated
   * @throws Exception  If activating the user failes
   */
  public function activateUser($s_type,$s_code){
    $this->service_QueryBuilder->select('users','id')->getWhere()->addAnd('activation','s',$s_code);
		$service_Database = $this->service_QueryBuilder->getResult();
		if( $service_Database->num_rows() == 0 )
			return false;

		$i_userid	= $service_Database->result(0,'id');

		try {
			$this->service_QueryBuilder->transaction();

			$this->service_QueryBuilder->insert('profile','userid','i',$i_userid)->getResult();
				
			$this->service_QueryBuilder->update('users',array('activation','active'),array('s','s'),array('','1'));
			$this->service_QueryBuilder->getWhere()->addAnd('id','i',$i_userid);
			$this->service_QueryBuilder->getResult();

			define('USERID',$i_userid);
				
			$this->service_QueryBuilder->commit();

			return true;
		}
		catch( \Exception $e){
			$this->service_QueryBuilder->rollback();
			throw $e;
		}
	}

  /**
   * Logs the user in
   *   
   * @param	String	$s_username	The username
   * @param	String	$s_password	The plain text password
   * @param  Boolean	$bo_autologin	Set to true for auto login
   * @return array	The id, username and password_expired if the login is correct, otherwise null
   */
  public function login($s_username, $s_password, $bo_autologin = false){
    $s_password = $this->model_User->createUser()->hashPassword($s_password, $s_username);
    $i_tries = $this->model_User->registerLoginTries();
    if( $i_tries > 6 ){
      /* Don't even check data */
      $this->service_Logs->loginLog($s_username, 'failed', $i_tries);
      return null;
    }

    $this->service_QueryBuilder->select('users', 'id, nick,bot,active,blocked,password_expired,lastLogin');
    $this->service_QueryBuilder->getWhere()->addAnd(array( 'nick', 'password', 'active', 'loginType' ), array( 's', 's', 's', 's' ), array( $s_username, $s_password, '1', 'normal' ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() == 0 ){
      $a_data = null;
    }
    else {
      $a_data = $service_Database->fetch_assoc();
    }

    if( $a_data[ 0 ][ 'bot' ] == '1' || $a_data[ 0 ][ 'active' ] == '0' || $a_data[ 0 ][ 'blocked' ] == '1' ){
      $a_data = null;
    }

    if( is_null($a_data) || $i_tries >= 5 ){
      if( $i_tries == 5 ){
        $this->model_User->disableAccount($s_username);        
        $this->service_Logs->accountBlockLog($s_username,3);
      }
      else if( $i_tries == 10 ){
        $this->service_QueryBuilder->insert('ipban', 'ip', 's', $_SERVER[ 'REMOTE_ADDR' ])->getResult();
        $this->service_Logs->ipBlockLog(6);
      }

      $this->service_Logs->loginLog($s_username, 'failed', $i_tries);

      return null;
    }

    $this->model_User->clearLoginTries();
    $this->service_Logs->loginLog($s_username, 'success', $i_tries);

    unset($a_data[ 0 ][ 'bot' ]);
    unset($a_data[ 0 ][ 'active' ]);
    unset($a_data[ 0 ][ 'blocked' ]);

    if( $bo_autologin ){
      $this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('userID', 'i', $a_data[ 0 ][ 'id' ]);
      $this->service_QueryBuilder->getResult();

      $this->service_QueryBuilder->insert('autologin', array( 'userID', 'username', 'type', 'IP' ), array( 'i', 's', 's', 's' ), array( $a_data[ 0 ][ 'id' ], $a_data[ 0 ][ 'nick' ], $a_data[ 0 ][ 'userType' ], $_SERVER[ 'REMOTE_ADDR' ] ));
      $service_Database = $this->service_QueryBuilder->getResult();

      $s_fingerprint = $this->service_Session->getFingerprint();
      $this->service_Cookie->set('autologin', $s_fingerprint . ';' . $service_Database->getID(), '/');
    }
    
    if( $a_data[0]['expired'] == 1 ){
     $this->service_Session->set('expired', $a_data[0]);
     $this->service_Headers->redirect('/authorisation/login/expired');
    }

    $this->setLogin($a_data[0]);
  }

  public function checkAutologin(){
   if( !$this->service_Cookie->exists('autologin') ){
    return;
   }
    
   $s_fingerprint = $this->service_Session->getFingerprint();
   $a_data = explode(';', $this->service_Cookie->get('autologin'));
    
   if( $a_data[0] != $s_fingerprint ){
    $this->service_Cookie->delete('autologin', '/');
    return;
   }
    
   /* Check auto login */
   $a_login = $this->performAutoLogin($a_data[1]);
   if( is_null($a_login) ){
    $this->service_Cookie->delete('autologin', '/');
    return;
   }
    
   $this->service_Cookie->set('autologin', implode(';', $a_data), '/');
   $this->setLogin($a_login);
  }
  
  private function setLogin($a_login){
   $s_redirection = '/';
   
   if( $this->service_Session->exists('page') ){
    if( $this->service_Session->get('page') != 'logout.php' )
     $s_redirection = $this->service_Session->get('page');
     
    $this->service_Session->delete('page');
   }
   
   $this->service_Session->setLogin($a_data['id'], $a_data['nick'], $a_data['lastLogin']);
   
   header('location: ' . $s_page);
  }

  /**
   * Performs the auto login
   * 
   * @param int $i_id		The auto login ID
   * @return array	The id, username and password_expired if the login is correct, otherwise null
   */
  public function performAutoLogin($i_id){
    $this->service_QueryBuilder->select('users u', 'u.id, u.nick,u.bot,u.active,u.blocked,u.password_expired,u.lastLogin,u.userType');
    $this->service_QueryBuilder->innerJoin('autologin al', 'u.id', 'al.userID')->getWhere()->addAnd(array( 'al.id', 'al.IP' ), array( 'i', 's' ), array( $i_id, $_SERVER[ 'REMOTE_ADDR' ] ));

    $service_Database = $this->service_QueryBuilder->getResult();
    if( $service_Database->num_rows() == 0 ){
      return null;
    }

    $a_data = $service_Database->fetch_assoc();

    if( $a_data[ 0 ][ 'bot' ] == '1' || $a_data[ 0 ][ 'active' ] == '0' || $a_data[ 0 ][ 'blocked' ] == '1' ){
      $this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('id', 'i', $i_id);
      $this->service_QueryBuilder->getResult();
      return null;
    }

    $this->service_Logs->loginLog($a_data[ 0 ][ 'nick' ], 'success', 1);

    unset($a_data[ 0 ][ 'bot' ]);
    unset($a_data[ 0 ][ 'active' ]);
    unset($a_data[ 0 ][ 'blocked' ]);

    return $a_data[ 0 ];
  }

  /**
   * Logs the user out
   */
  public function logout(){
    if( $this->service_Cookie->exists('autologin') ){
      $this->service_Cookie->delete('autologin', '/');
      $this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('userID', 'i', USERID);
      $this->service_QueryBuilder->getResult();
    }
    
    $this->service_Session->destroyLogin();
  }

  /**
   * Resends the activation email
   *
   * @param String $s_username					The username
   * @param String $s_email							The email address
   * @return bool   True if the email has been send
   */
  public function resendActivationEmail($s_type, $s_username, $s_email){
    $this->service_QueryBuilder->select('users', 'nick,email,activation')->getWhere()->addAnd(array( 'nick', 'email', 'activation' ), array( 's', 's', 's' ), array( $s_username, $s_email, '' ), array( '=', '=', '<>' ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() == 0 ){
      return false;
    }

    try{
      $a_data = $service_Database->fetch_assoc();
      $this->sendActivationEmail($a_data[ 0 ][ 'nick' ], $a_data[ 0 ][ 'email' ], $a_data[ 0 ][ 'activation' ]);

      return true;
    }
    catch( \Exception $e ){
      throw $e;
    }
  }
  
  /**
	 * Registers the password reset request
	 *
	 * @param String $s_email			The email address
	 * @return int	The status code
	 * 		0	Email address unknown
	 * 		-1	OpenID account
	 * 		1 	Email send
	 */
	public function resetPasswordMail($s_email){
		$this->service_QueryBuilder->select('users','id,loginType,nick')->getWhere()->addAnd(array('active','blocked','email'),array('s','s','s'),array(1,0,$s_email));
		$service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() == 0 ){ return 0; }

		$s_username		= $service_Database->result(0,'nick');
		$i_userid		= $service_Database->result(0,'id');
		$s_loginType	= $service_Database->result(0,'loginType');

    if( $s_loginType != 'normal' ){ return -1; }


		$s_newPassword	= $this->service_Random->numberLetter(10,true);
		$s_hash			= sha1($s_username.$this->service_Random->numberLetter(20,true).$s_email);

		$s_passwordHash	= $this->hashPassword($s_newPassword,$s_username);
		$this->service_QueryBuilder->insert('password_codes',array('userid','code','password','expire'),array('i','s','s','i'),array($i_userid,$s_hash,$s_passwordHash,(time() + 86400)))->getResult();

		$this->service_Mailer->passwordResetMail($s_username,$s_email,$s_newPassword,$s_hash) ;


		return 1;
	}

	/**
	 * Resets the password
	 *
	 * @param String $s_hash			The reset hash
	 * @return boolean	True if the hash is correct, otherwise false
	 */
	public function resetPassword($s_hash){
		$this->service_QueryBuilder->select('password_codes','userid,password')->getWhere()->addAnd(array('code','expire'),
				array('s','i'),array($s_hash,time()),array('=','>'));

		$service_Database = $this->service_QueryBuilder->getResult();
    if( $service_Database->num_rows() == 0 ){ return false; }

		$i_userid	= $service_Database->result(0,'userid');
		$s_password	= $service_Database->result(0,'password');
		try {
			$this->service_QueryBuilder->transaction();

			$this->service_QueryBuilder->delete('password_codes')->getWhere()->addOr(array('code','expire'),
					array('s','i'),array($s_hash,time()),array('=','<'));
			$this->service_QueryBuilder->getResult();
			
			$this->service_QueryBuilder->delete('ipban')->getWhere()->addAnd('ip','s',$_SERVER['REMOTE_ADDR']);
			$this->service_QueryBuilder->getResult();
			$this->clearLoginTries();
			
			$this->service_QueryBuilder->update('users',array('password','active','password_expired'),array('s','s','s'),array($s_password,'1','1'));
			$this->service_QueryBuilder->getWhere()->addAnd('id','i',$i_userid);
			$this->service_QueryBuilder->getResult();

			$this->service_QueryBuilder->commit();
			return true;
		}
		catch(\DBException $e){
			$this->service_QueryBuilder->rollback();
			throw $e;
		}
	}

	/**
	 * Disables the account by the username
	 * Sends a notification email
	 *
	 * @param String $s_username	The username
	 */
	public function disableAccount($s_username){
		\core\Memory::type('string',$s_username);

		try {
			$this->service_QueryBuilder->select('users','email')->getWhere()->addAnd('nick','s',$s_username);
			
			$service_Database = $this->service_QueryBuilder->getResult();
			if( $service_Database->num_rows() == 0 )
				return;

			$s_email	= $service_Database->result(0,'email');

			$this->service_QueryBuilder->transaction();

			$this->service_QueryBuilder->update('users','active','s','0')->getWhere()->addAnd('nick','s',$s_username);
			$this->service_QueryBuilder->getResult();

			/* Send mail to user */
			$this->service_Mailer->accountDisableMail($s_username,$s_email);

			$this->service_QueryBuilder->commit();
		}
		catch(\Exception $e){
			$this->service_QueryBuilder->rollback();
			throw $e;
		}
	}
  
  /**
   * Sends the activation email
   *
   * @param String $s_username					The username
   * @param String $s_email							The email address
   * @param String $s_registrationKey		The activation code
   * @throws ErrorException If the sending of the email failes
   */
  private function sendActivationEmail($s_username, $s_email, $s_registrationKey){
    if( !$this->service_Mailer->registrationMail($s_username, $s_email, $s_registrationKey) ){
      throw new \Exception("Sending registration mail to '.$s_email.' failed.");
    }
  }
}
?>
