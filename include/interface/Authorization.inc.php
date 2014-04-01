<?php

namespace core\interfaces;

interface Authorization {
  /**
   * Registers the user
   * 
   * @param array		$a_data	The form data
   * @param	bool		$bo_skipActivation	Set to true to skip sending the activation email (auto activation)
   * @return bool	True if the user is registrated
   */
  public function register($a_data, $bo_skipActivation = false);
  
  /**
   * Activates the user 
   * 
   * @param String $s_code    The activation code
   * @return boolean    True if the user is activated
   */
  public function activateUser($s_code);
  
  /**
   * Prepares the login
   * 
   * Only implemented for openID
   */
  public function loginStart();
  
  /**
   * Logs the user in
   *   
   * @param	String	$s_username	The username
   * @param	String	$s_password	The plain text password
   * @param  Boolean	$bo_autologin	Set to true for auto login
   * @return array	The id, username and password_expired if the login is correct, otherwise null
   */
  public function login($s_username, $s_password, $bo_autologin = false);
  
  /**
   * Logs the user out
   * 
   * @param String  $s_url    The redirectUrl
   */
  public function logout($s_url);
}
?>