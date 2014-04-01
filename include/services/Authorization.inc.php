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
  protected $s_openID_dir;
  protected $a_authorizationTypes = array();
  protected $a_openID_types = array();

  /**
   * Inits the service Autorization
   * 
   * @param \core\services\Cookie       $service_Cookie         The cookie handler
   * @param \core\services\QueryBuilder $service_QueryBuilder   The query builder
   * @param \core\services\Logs         $service_Logs           The log service
   */
  public function __construct(\core\services\Cookie $service_Cookie, \core\services\QueryBuilder $service_QueryBuilder, \core\services\Logs $service_Logs){
    $this->s_openID_dir = NIV . 'include/openID/';
    require_once($this->s_openID_dir . 'OpenAuth.inc.php');

    $this->service_Cookie = $service_Cookie;
    $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
    $this->service_Database = $this->service_QueryBuilder->getDatabase();
    $this->service_Logs = $service_Logs;

    $a_types = array( 'normal', 'Facebook', 'OpenID', 'LDAP' );
    foreach( $a_types AS $s_type ){
      try{
        $this->a_authorizationTypes[ $s_type ] = \core\Memory::services('Authorization' . ucfirst($s_type), true);
        $this->a_openID_types[] = $s_type;
      }
      catch( \MemoryException $ex ){
        
      }
    }
  }

  /**
   * Returns the available openID libs
   * 
   * @return array	The openID lib names
   */
  public function getOpenIDList(){
    $a_openID = array();
    foreach( $this->a_openID_types AS $a_type ){
      $a_openID[] = $a_type[ 0 ];
    }

    return $a_openID;
  }

  /**
   * Returns the authorization object
   * 
   * @param String  $s_type   The authorization type (normal|openID|Facebook|LDAP)
   * @return Authorization    The object
   * @throws MemoryException  If $s_type does not exist
   */
  private function getAuthorization($s_type){
    if( !array_key_exists($s_type, $this->a_authorizationTypes) ){
      throw new \MemoryException('Call to unknown authorization protocol ' . $s_type . '.');
    }
    return $this->a_authorizationTypes[ $s_type ];
  }

  /**
   * Registers the user
   * 
   * @param String  $s_type   The authorization type (normal|openID|Facebook|LDAP)
   * @param array		$a_data	The form data
   * @param	bool		$bo_skipActivation	Set to true to skip sending the activation email (auto activation)
   * @return bool	True if the user is registrated
   */
  public function register($s_type, $a_data, $bo_skipActivation = false){
    $obj_authorization = $this->getAuthorization($s_type);

    return $obj_authorization->register($a_data, $bo_skipActivation);
  }
  
  /**
   * Activates the user 
   * 
   * 
   * @param String $s_code    The activation code
   * @return boolean    True if the user is activated
   */
  public function activateUser($s_type,$s_code){
    $obj_authorization = $this->getAuthorization($s_type);
    
    return $obj_authorization->activateUser($s_code);
	}

  /**
   * Prepares the login 
   * Only implemented for openID
   * 
   * @param String  $s_type   The authorization type (normal|openID|Facebook|LDAP) 
   */
  public function loginStart($s_type){
    $obj_authorization = $this->getAuthorization($s_type);

    $obj_authorization->loginStart();
  }

  /**
   * Logs the user in
   *   
   * @param String  $s_type   The authorization type (normal|openID|Facebook|LDAP) 
   * @param	String	$s_username	The username
   * @param	String	$s_password	The plain text password
   * @param  Boolean	$bo_autologin	Set to true for auto login
   * @return array	The id, username and password_expired if the login is correct, otherwise null
   */
  public function login($s_type, $s_username, $s_password, $bo_autologin = false){
    $obj_authorization = $this->getAuthorization($s_type);

    return $obj_authorization->login($s_username, $s_password, $bo_autologin);
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
   * 
   * @param String  $s_type   The authorization type (normal|openID|Facebook|LDAP) 
   */
  public function logout($s_type){
    $obj_authorization = $this->getAuthorization($s_type);

    if( $this->service_Cookie->exists('autologin') ){
      $this->service_Cookie->delete('autologin', '/');
      $this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('userID', 'i', USERID);
      $this->service_QueryBuilder->getResult();
    }
    
    $this->service_Session->destroyLogin();

    $obj_authorization->logout(NIV . 'index.php');
  }

  /**
   * Loads the openID class
   * 
   * @param String $s_type	The name
   * @throws Exception	Unknown openID libary
   * @return OpenAuth		The class
   */
  protected function getOpenID($s_type){
    if( array_key_exists($s_type, $this->a_openID) ){
      return $this->a_openID[ $s_type ];
    }

    if( !array_key_exists($s_type, $this->a_openID_types) ){
      throw new \Exception("Unknown openID libary with name " . $s_type);
    }

    $a_data = $this->a_openID_types[ $s_type ];
    require($this->s_openID_dir . $a_data[ 1 ]);
    $obj_ID = new $s_type();
    $this->a_openID[ $s_type ] = $obj_ID;
    return $obj_ID;
  }

  /**
   * Resends the activation email
   *
   * @param String $s_username					The username
   * @param String $s_email							The email address
   * @return bool   True if the email has been send
   */
  public function resendActivationEmail($s_type, $s_username, $s_email){
    if( $s_type != 'normal' ){
      return;
    }

    return $obj_authorization = $this->resendActivationEmail($s_username, $s_email);
  }

}
?>
