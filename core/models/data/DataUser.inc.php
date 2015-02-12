<?php

namespace core\models\data;

/**
 * Model is the user data model class.
 * This class contains the user data
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   04/05/2014
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
class DataUser extends \core\models\GeneralUser{

  private $model_Groups;
  private $service_Language;
  protected $i_userid = null;
  protected $s_username = '';
  protected $s_email = '';
  protected $i_bot = 0;
  protected $i_registrated = 0;
  protected $i_loggedIn = 0;
  protected $i_active = 0;
  protected $i_blocked = 0;
  protected $s_password;
  protected $s_profile = '';
  protected $s_activation = '';
  protected $i_level;
  protected $s_loginType;
  protected $s_language = '';

  /**
   * PHP5 constructor
   * 
   * @param \core\services\QueryBuilder $service_QueryBuilder The query builder
   * @parma \core\services\Security $service_Security The security service   
   * @param \core\services\Hashing $service_Hashing   The hashing service
   * @param \core\models\Groups $model_Groups   The groups model
   * @param \core\services\Language $service_Language The language service
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder,\core\services\Security $service_Security,\core\services\Hashing $service_Hashing, 
    \core\models\Groups $model_Groups, \core\services\Language $service_Language){
    parent::__construct($service_QueryBuilder,$service_Security,$service_Hashing);
    $this->model_Groups = $model_Groups;
    $this->service_Language = $service_Language;
    
    $this->a_validation = array(
        's_username' => array('type'=>'string','required'=>1),
        's_email' => array('type'=>'string','required'=>1,'pattern'=>'email'),
        'i_bot' => array('type'=>'enum','set'=>array(0,1)),
        'i_registrated' => array('type'=>'enum','set'=>array(0,1)),
        'i_active' => array('type'=>'enum','set'=>array(0,1)),
        'i_blocked' => array('type'=>'enum','set'=>array(0,1)),
        's_password' => array('type'=>'string','required'=>1),
        's_profile' => array('type'=>'string'),
        's_activation' => array('type'=>'string'),
        's_loginType' => array('type'=>'string','required'=>1),
        's_language' => array('type'=> 'string')
    );
  }

  /**
   * Collects the users userid, nick and level
   *
   * @param   int $i_userid The userid
   * @throws  DBException If the userid is invalid
   */
  public function loadData($i_userid){
    \core\Memory::type('int', $i_userid);

    $this->service_QueryBuilder->select('users', '*')->getWhere()->addAnd('id', 'i', $i_userid);
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() == 0 ){
      throw new \DBException("Unknown user with userid " . $i_userid);
    }

    $a_data = $service_Database->fetch_assoc();

    $this->setData($a_data[ 0 ]);
  }

  /**
   * Sets the user data
   *
   * @param array $a_data	The user data
   */
  public function setData($a_data){
    \core\Memory::type('array', $a_data);

    $this->i_userid = ( int ) $a_data[ 'id' ];
    $this->s_username = $a_data[ 'nick' ];
    $this->s_email = $a_data[ 'email' ];
    $this->s_profile = $a_data[ 'profile' ];
    $this->i_bot = ( int ) $a_data[ 'bot' ];
    $this->i_registrated = ( int ) $a_data[ 'registrated' ];
    $this->i_loggedIn = (int) $a_data['lastLogin'];
    $this->i_active = ( int ) $a_data[ 'active' ];
    $this->i_blocked = ( int ) $a_data[ 'blocked' ];
    $this->s_loginType = $a_data[ 'loginType' ];
    $this->s_language = $a_data[ 'language' ];

    $s_systemLanguage = $this->service_Language->getLanguage();
    if( defined('USERID') && USERID == $this->i_userid && $this->s_language != $s_systemLanguage ){
      if( $this->getLanguage() != $this->s_language ){
        $this->service_QueryBuilder->update('users', 'language', 's', $s_systemLanguage)->getWhere()->addAnd('id', 'i', $this->i_userid);
        $this->service_QueryBuilder->getResult();
      }
    }
  }

  /**
   * Returns the userid
   *
   * @return int The userid
   */
  public function getID(){
    return $this->i_userid;
  }

  /**
   * Returns the username
   *
   * @return String   The username
   */
  public function getUsername(){
    return $this->s_username;
  }

  /**
   * Sets the username
   *
   * @param String $s_username    The new username
   */
  public function setUsername($s_username){
    \core\Memory::type('string', $s_username);
    $this->s_username = $s_username;
  }

  /**
   * Returns the email address
   *
   * @return String   The email address
   */
  public function getEmail(){
    return $this->s_email;
  }

  /**
   * Sets the email address
   *
   * @param String $s_email	The email address
   */
  public function setEmail($s_email){
    \core\Memory::type('string', $s_email);
    $this->s_email = $s_email;
  }

  /**
   * Sets a new password
   * Note : username has to be set first!
   *
   * @param String $s_password		The plain text password
   * @param Boolean	$bo_expired		Set to true to set the password to expired
   */
  public function setPassword($s_password, $bo_expired = false){
    \core\Memory::type('string', $s_password);
    
    $this->s_password = $this->hashPassword($s_password, $this->s_username);
    
    if( $bo_expired ){
      $this->service_QueryBuilder->update('users', array( 'password', 'password_expired' ), array( 's', 's' ), array( $this->s_password, '1' ));
      $this->service_QueryBuilder->getWhere()->addAnd('id', 'i', $this->i_userid);
      $this->service_QueryBuilder->getResult();
    }
    else {
      $this->service_QueryBuilder->update('users', 'password', 's', $this->s_password)->getWhere()->addAnd('id', 'i', $this->i_userid);
      $this->service_QueryBuilder->getResult();
    }
  }

  /**
   * Checks if the user is a system account
   *
   * @return Boolean	True if the user is a system account
   */
  public function isBot(){
    return ($this->i_bot == 1);
  }

  /**
   * Sets the account as a normal or system account
   *
   * @param Boolean $bo_bot	Set to true for a system account
   */
  public function setBot($bo_bot){
    \core\Memory::type('boolean', $bo_bot);

    if( $bo_bot ){ $this->i_bot = 1;  } else { $this->i_bot = 0; }
  }

  /**
   * Checks if the user is enabled
   *
   * @return Boolean	True if the user is enabled
   */
  public function isEnabled(){
    return ($this->i_active == 1);
  }

  /**
   * Returns the registration date
   *
   * @return int	The registration date as a timestamp
   */
  public function getRegistrated(){
    return $this->i_registrated;
  }
  
  /**
   * Returns the last login date
   * 
   * @return int The logged in date as a timestamp
   */
  public function lastLoggedIn(){
   return $this->i_loggedIn;
  }

  /**
   * Checks if the account is blocked
   *
   * @return Boolean		True if the account is blocked
   */
  public function isBlocked(){
    return ($this->i_blocked == 1);
  }

  /**
   * (Un)Blocks the account
   *
   * @param Boolean $bo_blocked	Set to true to block the account, otherwise false
   */
  public function setBlocked($bo_blocked){
    \core\Memory::type('boolean', $bo_blocked);

    if( $bo_blocked ){ $this->i_blocked = 1;  } else {  $this->i_blocked = 0; }
  }

  /**
   * Sets the activation code
   *
   * @param String $s_activation	The activation code
   */
  public function setActivation($s_activation){
    $this->s_activation = $s_activation;
  }

  /**
   * Returns the profile text
   *
   * @return String	The text
   */
  public function getProfile(){
    return $this->s_profile;
  }

  /**
   * Sets the profile text
   *
   * @param String $s_text	The text
   */
  public function setProfile($s_profile){
    $this->s_profile = $s_profile;
  }

  /**
   * Returns the groups where the user is in
   *
   * @return arrays	The groups
   */
  public function getGroups(){
    $a_groups = $this->model_Groups->getGroups();
    $a_groupsUser = array();

    foreach( $a_groups AS $obj_group ){
      $i_level = $obj_group->getLevelByGroupID($this->i_userid);

      if( $i_level != \core\services\Session::ANONYMOUS ){
        $a_groupsUser[ $obj_group->getID() ] = $i_level;
      }
    }

    return $a_groupsUser;
  }

  /**
   * Returns the access level for the current group
   *
   * @return int	The access level
   */
  public function getLevel(){
    if( !is_null($this->i_level) ){ return $this->i_level;  }
    if( is_null($this->i_userid) ){ return \core\services\Session::ANONYMOUS;  }

    $this->i_level = $this->model_Groups->getLevel($this->i_userid);
    return $this->i_level;
  }

  /**
   * Changes the password
   *
   * @param   String  $s_password     The new password
   * @throws	Exception	If the account is not saved yet
   */
  public function changePassword($s_password){
    \core\Memory::type('string', $s_password);

    if( is_null($this->i_userid) ){ throw new \Exeception("Can not change password from a not existing account"); }

    $this->service_QueryBuilder->update('users', 'password', 's', $s_password)->getWhere()->addAnd('id', 'i', $this->i_userid);
    $this->service_QueryBuilder->getResult();
  }

  /**
   * Disables the user account
   */
  public function disableAccount(){
    $this->i_active = 0;
  }

  /**
   * Enabled the user account
   */
  public function enableAccount(){
    $this->i_active = 1;
  }

  /**
   * Returns the color corosponding the users level
   *
   * @param   int $i_groupid  The groupid, leave empty for site group
   * @return  String The color
   */
  public function getColor($i_groupid = -1){
    \core\Memory::type('int', $i_groupid);

    $i_level = $this->checkGroup($i_groupid);

    switch( $this->getLevel($i_level) ){
      case \core\services\Session::ANONYMOUS :
        return \core\services\Session::ANONYMOUS_COLOR;

      case \core\services\Session::USER :
        return \core\services\Session::USER_COLOR;

      case \core\services\Session::MODERATOR :
        return \core\services\Session::MODERATOR_COLOR;

      case \core\services\Session::ADMIN :
        return \core\services\Session::ADMIN_COLOR;
    }
  }

  /**
   * Checks is the visitor has moderator rights
   *
   * @param       int $i_groupid  The group ID, leave empty for site group
   * @return      Boolean True if the visitor has moderator rights, otherwise false
   */
  public function isModerator($i_groupid = -1){
    \core\Memory::type('int', $i_groupid);

    $i_groupid = $this->checkGroup($i_groupid);

    return ( $this->getLevel($i_groupid) >= \core\services\Session::MODERATOR );
  }

  /**
   * Checks is the visitor has administrator rights
   *
   * @param   int $i_groupid  The group ID, leave empty for site group
   * @return  Boolean True if the visitor has administrator rights, otherwise false
   */
  public function isAdmin($i_groupid = -1){
    \core\Memory::type('int', $i_groupid);

    $i_groupid = $this->checkGroup($i_groupid);
    return ( $this->getLevel($i_groupid) >= \core\services\Session::ADMIN );
  }

  /**
   * Checks the group ID
   *
   * @param int $i_groupid	The groupID, may be -1 for site group
   * @return int	The group ID
   */
  private function checkGroup($i_groupid){
    if( $i_groupid == -1 ){ $i_groupid = GROUP_SITE;  }

    return $i_groupid;
  }

  /**
   * Sets the password as expired
   * Forcing the user to change the password
   */
  public function expirePassword(){
    $this->service_QueryBuilder->update('users', 'password_expires', 's', 'i')->getWhere()->addAnd('id', 'i', $this->i_userid);
    $this->service_QueryBuilder->getResult();
  }

  /**
   * Returns the set user language
   */
  public function getLanguage(){
    return $this->s_language;
  }

  /**
   * Returns the login type
   * 
   * @return String	The type
   */
  public function getLoginType(){
    return $this->s_loginType;
  }

  /**
   * Sets the login type
   *
   * @return String	$s_type	The type
   */
  public function setLoginType($s_type){
    $this->s_loginType = $s_type;
  }

  /**
   * Saves the new user in the database
   */
  public function save(){
    if( !is_null($this->i_userid) ){
      $this->persist();
      return;
    }
    
    $this->performValidation();

    $this->i_registrated = time();

    $this->service_QueryBuilder->insert('users', array( 'nick', 'email', 'password', 'bot', 'registrated','lastLogin', 'active', 'activation', 'profile', 'loginType' )
      , array( 's', 's', 's', 's', 'i','i', 's', 's', 's', 's' ), array( $this->s_username, $this->s_email, $this->s_password, $this->i_bot, $this->i_registrated,
        $this->i_loggedIn, $this->i_active, $this->s_activation, $this->s_profile, $this->s_loginType ));

    $this->i_userid = ( int ) $this->service_QueryBuilder->getResult()->getId();

    if( $this->i_userid == -1 ){
      return;
    }

    $this->model_Groups->addUserDefaultGroups($this->i_userid);
  }

  /**
   * Saves the changed user in the database
   */
  public function persist(){
    if( is_null($this->i_userid) ){
      $this->save();
      return;
    }
    
    $this->performValidation();

    $this->service_QueryBuilder->update('users', array( 'nick', 'email', 'bot', 'active', 'blocked', 'profile' ), array( 's', 's', 's', 's', 's', 's' ), array( $this->s_username, $this->s_email, $this->i_bot, $this->i_active, $this->i_blocked, $this->s_profile ));
    $this->service_QueryBuilder->getWhere()->addAnd('id', 'i', $this->i_userid);
    $this->service_QueryBuilder->getResult();
  }

  /**
   * Deletes the user permantly
   */
  public function delete(){
    if( is_null($this->i_userid) ){ return; }

    /* Delete user from groups */
    $this->model_Groups->deleteGroupsUser($this->i_userid);

    $this->service_QueryBuilder->delete('users')->getWhere()->addAnd('id', 'i', $this->i_userid);
    $this->service_QueryBuilder->getResult();
    $this->i_userid = null;
  }
}
?>
