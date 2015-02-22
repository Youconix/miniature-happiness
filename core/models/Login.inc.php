<?php

namespace core\models;

/**
 * Account authorization models Handles login from the accounts This file is part of Scripthulp framework
 * Scripthulp framework is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser
 * General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the
 * GNU Lesser General Public License along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 2.0 @changed 03/01/2015
 */
class Login extends Model {
 protected $service_Cookie;
 protected $service_QueryBuilder;
 protected $service_Logs;
 protected $service_Hashing;
 protected $service_Session;
 protected $service_Mailer;
 protected $service_Random;
 protected $service_Headers;
 protected $model_Config;
 
 /**
  * Inits the service Autorization
  *
  * @param \core\services\Cookie $service_Cookie The cookie handler
  * @param \core\services\QueryBuilder $service_QueryBuilder The query builder
  * @param \core\services\Logs $service_Logs The log service
  * @param \core\services\Hashing $service_Hashing The hashing service
  * @param \core\services\Session $service_Session The session service
  * @param \core\services\Mailer $service_Mailer The mailer service
  * @param \core\services\Random $service_Random The random service
  * @param \core\services\Headers $service_Headers The headers service
  * @param \core\models\Config $model_Config The site config
  */
 public function __construct( \core\services\Cookie $service_Cookie, \core\services\QueryBuilder $service_QueryBuilder, \core\services\Logs $service_Logs, \core\services\Hashing $service_Hashing, \core\services\Session $service_Session, \core\services\Mailer $service_Mailer, \core\services\Random $service_Random, \core\services\Headers $service_Headers, \core\models\Config $model_Config ){
  $this->service_Cookie = $service_Cookie;
  $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
  $this->service_Database = $this->service_QueryBuilder->getDatabase();
  $this->service_Logs = $service_Logs;
  $this->service_Hashing = $service_Hashing;
  $this->service_Session = $service_Session;
  $this->service_Mailer = $service_Mailer;
  $this->service_Random = $service_Random;
  $this->service_Headers = $service_Headers;
  $this->model_Config = $model_Config;
 }
 
 /**
  * Logs the user in
  *
  * @param String $s_username
  * @param String $s_password text password
  * @param Boolean $bo_autologin true for auto login
  */
 public function do_login( $s_username, $s_password, $bo_autologin = false ){
  $s_password = $this->service_Hashing->hashUserPassword($s_password, $s_username);
  $i_tries = $this->registerLoginTries();
  
  /* Check the login combination */  
  $this->service_QueryBuilder->select('users', 'id, nick,bot,active,blocked,password_expired,lastLogin');
  $this->service_QueryBuilder->getWhere()->addAnd(array( 
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
  $service_Database = $this->service_QueryBuilder->getResult();
  
  if( $service_Database->num_rows() == 0 ){
   return;
  }
  
  $a_data = $service_Database->fetch_assoc();

  if( $a_data[0]['bot'] == '1' || $a_data[0]['active'] == '0' || $a_data[0]['blocked'] == '1' ){
   return;
  }

  /* Check the number of tries */
  if( $i_tries >= 5 ){
   if( $i_tries == 5 ){
    $this->service_QueryBuilder->select('users', 'email')->getWhere()->addAnd(array( 
      'username',
      'active' 
    ), array( 
      's',
      's' 
    ), array( 
      $s_username,
      '1' 
    ));
    $service_Database = $this->service_QueryBuilder->getResult();
    
    if( $service_Database->num_rows() > 0 ){
     $s_email = $service_Database->result(0, 'email');
     
     $this->service_QueryBuilder->update('users', 'active', '0')->getWhere()->addAnd('username', 's', $s_username);
     $this->service_QueryBuilder->getResult();
     
     $this->service_Mailer->accountDisableMail($s_username, $s_email);
    }
    
    $this->service_Logs->accountBlockLog($s_username, 3);
   }
   else if( $i_tries == 10 ){
    $this->service_QueryBuilder->insert('ipban', 'ip', 's', $_SERVER['REMOTE_ADDR'])->getResult();
    $this->service_Logs->ipBlockLog(6);
   }
   else{
    $this->service_Logs->loginLog($s_username, 'failed', $i_tries);
   }
   
   return;
  }
  
  $this->clearLoginTries();
  $this->service_Logs->loginLog($s_username, 'success', $i_tries);
  
  if( $bo_autologin ){
   /* Set auto login for the next time */
   $this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('userID', 'i', $a_data[0]['id']);
   $this->service_QueryBuilder->getResult();
   
   $this->service_QueryBuilder->insert('autologin', array( 
     'userID',
     'username',
     'type',
     'IP' 
   ), array( 
     'i',
     's',
     's',
     's' 
   ), array( 
     $a_data[0]['id'],
     $a_data[0]['nick'],
     $a_data[0]['userType'],
     $_SERVER['REMOTE_ADDR'] 
   ));
   $service_Database = $this->service_QueryBuilder->getResult();
   
   $s_fingerprint = $this->service_Session->getFingerprint();
   $this->service_Cookie->set('autologin', $s_fingerprint . ';' . $service_Database->getID(), '/');
  }
  
  if( $a_data[0]['password_expired'] == 1 ){
   /* Password is expired */
   $this->service_Session->set('expired', $a_data[0]);
   $this->service_Headers->redirect('/authorisation/login/expired');
  }
  
  $this->setLogin($a_data[0]);
 }
 
 /**
  * Checks if auto login is present and valid.
  * If so, the user is logged in
  */
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
 
 /**
  * Sets the login session and redirects to the given page or the set default
  *
  * @param array $a_login The login data
  */
 public function setLogin( $a_login ){
  $s_redirection = $this->model_Config->getLoginRedirect();
  
  if( $this->service_Session->exists('page') ){
   if( $this->service_Session->get('page') != 'logout.php' )
    $s_redirection = $this->service_Session->get('page');
   
   $this->service_Session->delete('page');
  }
  
  while( strpos($s_redirection,'//') !== false ){
      $s_redirection = str_replace('//','/',$s_redirection);
  }
  
  $this->service_QueryBuilder->update('users', 'lastLogin', 'i', time())->getWhere()->addAnd('id','i',$a_login['id']);
  $this->service_QueryBuilder->getResult();
  
  $this->service_Session->setLogin($a_login['id'], $a_login['nick'], $a_login['lastLogin']);

  $this->service_Headers->redirect($s_redirection);
 }
 
 /**
  * Performs the auto login
  *
  * @param int $i_id auto login ID
  * @return array id, username and password_expired if the login is correct, otherwise null
  */
 private function performAutoLogin( $i_id ){
  $this->service_QueryBuilder->select('users u', 'u.id, u.nick,u.bot,u.active,u.blocked,u.password_expired,u.lastLogin,u.userType');
  $this->service_QueryBuilder->innerJoin('autologin al', 'u.id', 'al.userID')->getWhere()->addAnd(array( 
    'al.id',
    'al.IP' 
  ), array( 
    'i',
    's' 
  ), array( 
    $i_id,
    $_SERVER['REMOTE_ADDR'] 
  ));
  
  $service_Database = $this->service_QueryBuilder->getResult();
  if( $service_Database->num_rows() == 0 ){
   return null;
  }
  
  $a_data = $service_Database->fetch_assoc();
  
  if( $a_data[0]['bot'] == '1' || $a_data[0]['active'] == '0' || $a_data[0]['blocked'] == '1' ){
   $this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('id', 'i', $i_id);
   $this->service_QueryBuilder->getResult();
   return null;
  }
  
  $this->service_Logs->loginLog($a_data[0]['nick'], 'success', 1);
  
  return $a_data[0];
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
  
  $this->service_Headers->redirect($this->model_Config->getLogoutRedirect());
 }
 
 /**
  * Registers the password reset request
  *
  * @param String $s_email email address
  * @return int status code 0	Email address unknown -1	OpenID account 1 Email send
  */
 public function resetPasswordMail( $s_email ){
  $this->service_QueryBuilder->select('users', 'id,loginType,nick')->getWhere()->addAnd(array( 
    'active',
    'blocked',
    'email' 
  ), array( 
    's',
    's',
    's' 
  ), array( 
    1,
    0,
    $s_email 
  ));
  $service_Database = $this->service_QueryBuilder->getResult();
  
  if( $service_Database->num_rows() == 0 ){
   return 0;
  }
  
  $s_username = $service_Database->result(0, 'nick');
  $i_userid = $service_Database->result(0, 'id');
  $s_loginType = $service_Database->result(0, 'loginType');
  
  if( $s_loginType != 'normal' ){
   return -1;
  }
  
  $s_newPassword = $this->service_Random->numberLetter(10, true);
  $s_hash = sha1($s_username . $this->service_Random->numberLetter(20, true) . $s_email);
  
  $s_passwordHash = $this->hashPassword($s_newPassword, $s_username);
  $this->service_QueryBuilder->insert('password_codes', array( 
    'userid',
    'code',
    'password',
    'expire' 
  ), array( 
    'i',
    's',
    's',
    'i' 
  ), array( 
    $i_userid,
    $s_hash,
    $s_passwordHash,
    (time() + 86400) 
  ))->getResult();
  
  $this->service_Mailer->passwordResetMail($s_username, $s_email, $s_newPassword, $s_hash);
  
  return 1;
 }
 
 /**
  * Resets the password
  *
  * @param String $s_hash reset hash
  * @return boolean if the hash is correct, otherwise false
  */
 public function resetPassword( $s_hash ){
  $this->service_QueryBuilder->select('password_codes', 'userid,password')->getWhere()->addAnd(array( 
    'code',
    'expire' 
  ), array( 
    's',
    'i' 
  ), array( 
    $s_hash,
    time() 
  ), array( 
    '=',
    '>' 
  ));
  
  $service_Database = $this->service_QueryBuilder->getResult();
  if( $service_Database->num_rows() == 0 ){
   return false;
  }
  
  $i_userid = $service_Database->result(0, 'userid');
  $s_password = $service_Database->result(0, 'password');
  try{
   $this->service_QueryBuilder->transaction();
   
   $this->service_QueryBuilder->delete('password_codes')->getWhere()->addOr(array( 
     'code',
     'expire' 
   ), array( 
     's',
     'i' 
   ), array( 
     $s_hash,
     time() 
   ), array( 
     '=',
     '<' 
   ));
   $this->service_QueryBuilder->getResult();
   
   $this->service_QueryBuilder->delete('ipban')->getWhere()->addAnd('ip', 's', $_SERVER['REMOTE_ADDR']);
   $this->service_QueryBuilder->getResult();
   $this->clearLoginTries();
   
   $this->service_QueryBuilder->update('users', array( 
     'password',
     'active',
     'password_expired' 
   ), array( 
     's',
     's',
     's' 
   ), array( 
     $s_password,
     '1',
     '1' 
   ));
   $this->service_QueryBuilder->getWhere()->addAnd('id', 'i', $i_userid);
   $this->service_QueryBuilder->getResult();
   
   $this->service_QueryBuilder->commit();
   return true;
  }
  catch( \DBException $e ){
   $this->service_QueryBuilder->rollback();
   throw $e;
  }
 }
 
 /**
  * Disables the account by the username Sends a notification email
  *
  * @param String $s_username username
  */
 public function disableAccount( $s_username ){
  \core\Memory::type('string', $s_username);
  
  try{
   $this->service_QueryBuilder->select('users', 'email')->getWhere()->addAnd('nick', 's', $s_username);
   
   $service_Database = $this->service_QueryBuilder->getResult();
   if( $service_Database->num_rows() == 0 )
    return;
   
   $s_email = $service_Database->result(0, 'email');
   
   $this->service_QueryBuilder->transaction();
   
   $this->service_QueryBuilder->update('users', 'active', 's', '0')->getWhere()->addAnd('nick', 's', $s_username);
   $this->service_QueryBuilder->getResult();
   
   /* Send mail to user */
   $this->service_Mailer->accountDisableMail($s_username, $s_email);
   
   $this->service_QueryBuilder->commit();
  }
  catch( \Exception $e ){
   $this->service_QueryBuilder->rollback();
   throw $e;
  }
 }
 
 /**
  * Registers the login try
  *
  * @return int number of tries done including this one
  */
 private function registerLoginTries(){
  $s_fingerprint = $this->service_Session->getFingerprint();
  
  $this->service_QueryBuilder->select('login_tries', 'tries')->getWhere()->addAnd('hash', 's', $s_fingerprint);
  $service_Database = $this->service_QueryBuilder->getResult();
  
  if( $service_Database->num_rows() == 0 ){
   $i_tries = 1;
   $this->service_QueryBuilder->select('login_tries', 'tries')->getWhere()->addAnd(array( 
     'ip',
     'timestamp' 
   ), array( 
     's',
     'i',
     'i' 
   ), array( 
     $_SERVER['REMOTE_ADDR'],
     time(),
     (time() - 3) 
   ), array( 
     '=',
     'BETWEEN' 
   ));
   $service_Database = $this->service_QueryBuilder->getResult();
   if( $service_Database->num_rows() > 10 ){
    $i_tries = 6; // reject login to be sure
   }
   
   $this->service_QueryBuilder->insert('login_tries', array( 
     'hash',
     'ip',
     'tries',
     'timestamp' 
   ), array( 
     's',
     's',
     'i',
     'i' 
   ), array( 
     $s_fingerprint,
     $_SERVER['REMOTE_ADDR'],
     1,
     time() 
   ))->getResult();
   
   return $i_tries;
  }
  
  $i_tries = ($service_Database->result(0, 'tries') + 1);
  $this->service_QueryBuilder->update('login_tries', 'tries', 'l', 'tries + 1')->getWhere()->addAnd('hash', 's', $s_fingerprint);
  $this->service_QueryBuilder->getResult();
  return $i_tries;
 }
 
 /**
  * Clears the login tries
  */
 private function clearLoginTries(){
  $s_fingerprint = $this->service_Session->getFingerprint();
  
  $this->service_QueryBuilder->delete('login_tries')->getWhere()->addAnd('hash', 's', $s_fingerprint);
  $this->service_QueryBuilder->getResult();
 }
 
 /**
  * Logs the user in as the given user
  * Control panel only function
  * 
  * @param int $i_userid The user ID
  */
 public function loginAs($i_userid){
  $this->service_QueryBuilder->select('users', 'id, nick,lastLogin')->getWhere()->addAnd('id','i',$i_userid);
  $service_Database = $this->service_QueryBuilder->getResult();
  
  if( $service_Database->num_rows() == 0 ){
   return;
  }
  
  $a_data = $service_Database->fetch_assoc();
  
  $this->service_Session->setLoginTakeover($a_data[0]['id'], $a_data[0]['nick'], $a_data[0]['lastLogin']);
 }
}
?>
