<?php
if( !class_exists('\core\models\data\Data_User') ){
  if( !class_exists('\core\models\GeneralUser') ){
    require(NIV.'include/models/GeneralUser');
  }
  
  require(NIV.'include/models/data/Data_User.inc.php');
}

class DummyModelUserData extends \core\models\data\Data_User {
  public $i_userid;
  
  public function __construct(){  }
  
  public function loadData($i_userid){
    $this->i_userid = $i_userid;
  }

  /**
   * Sets the user data
   *
   * @param array $a_data	The user data
   */
  public function setData($a_data){
    \core\Memory::type('array', $a_data);
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
  }

  /**
   * Returns the groups where the user is in
   *
   * @return arrays	The groups
   */
  public function getGroups(){
    return array();
  }

  /**
   * Returns the access level for the current group
   *
   * @return int	The access level
   */
  public function getLevel(){
    return \core\services\Session::USER;
  }

  /**
   * Changes the password
   *
   * @param   String  $s_password     The new password
   * @throws	Exception	If the account is not saved yet
   */
  public function changePassword($s_password){
    \core\Memory::type('string', $s_password);
  }
  
  /**
   * Returns the color corosponding the users level
   *
   * @param   int $i_groupid  The groupid, leave empty for site group
   * @return  String The color
   */
  public function getColor($i_groupid = -1){
    \core\Memory::type('int', $i_groupid);

    return \core\services\Session::USER_COLOR;
  }

  /**
   * Checks is the visitor has moderator rights
   *
   * @param       int $i_groupid  The group ID, leave empty for site group
   * @return      Boolean True if the visitor has moderator rights, otherwise false
   */
  public function isModerator($i_groupid = -1){
    \core\Memory::type('int', $i_groupid);

    return false;
  }

  /**
   * Checks is the visitor has administrator rights
   *
   * @param   int $i_groupid  The group ID, leave empty for site group
   * @return  Boolean True if the visitor has administrator rights, otherwise false
   */
  public function isAdmin($i_groupid = -1){
    \core\Memory::type('int', $i_groupid);

    return false;
  }

  /**
   * Sets the password as expired
   * Forcing the user to change the password
   */
  public function expirePassword(){
  }

  /**
   * Saves the new user in the database
   */
  public function save(){
  }

  /**
   * Saves the changed user in the database
   */
  public function persist(){
  }

  /**
   * Deletes the user permantly
   */
  public function delete(){
  }
}
?>

