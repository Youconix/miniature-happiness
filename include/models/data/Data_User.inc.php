<?php
/**
 * Model is the user data model class.
 * This class contains the user data
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   13/05/13
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
class Data_User extends GeneralUser {
	private $model_Groups;
	private $i_userid = null;
	private $s_username = '';
	private $s_email = '';
	private $i_bot = 0;
	private $i_registrated = 0;
	private $i_active = 0;
	private $i_blocked = 0;
	private $s_password;
	private $s_profile = '';
	private $s_activation = '';
	private $i_level;
	private $s_loginType;
	private $s_language;

	/**
	 * Generates a new user model
	 */
	public function __construct() {
		parent::__construct();

		$this->model_Groups = Memory::models('Groups');
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		$this->model_Groups = null;

		$this->i_userid = null;
		$this->s_email = null;
		$this->i_bot = null;
		$this->i_registrated = null;
		$this->i_active = null;
		$this->s_profile = null;
		$this->i_blocked = null;
		$this->s_password = null;
		$this->i_level = null;
		$this->s_loginType	= null;
		$this->s_language	= null;

		parent::__destruct();
	}

	/**
	 * Collects the users userid, nick and level
	 *
	 * @param   int $i_userid The userid
	 * @throws  DBException If the userid is invalid
	 */
	public function loadData($i_userid) {
		Memory::type('int', $i_userid);

		$this->service_QueryBuilder->select('users','*')->getWhere()->addAnd('id','i',$i_userid);
		$service_Database	= $this->service_QueryBuilder->getResult();
		
		if ($service_Database->num_rows() == 0) {
			throw new DBException("Unknown user with userid " . $i_userid);
		}

		$a_data = $service_Database->fetch_assoc();

		$this->setData($a_data[0]);
	}

	/**
	 * Sets the user data
	 *
	 * @param array $a_data	The user data
	 */
	public function setData($a_data) {
		Memory::type('array', $a_data);

		$this->i_userid = (int) $a_data['id'];
		$this->s_username = $a_data['nick'];
		$this->s_email	= $a_data['email'];
		$this->s_profile	= $a_data['profile'];
		$this->i_bot = (int) $a_data['bot'];
		$this->i_registrated = (int) $a_data['registrated'];
		$this->i_active = (int) $a_data['active'];
		$this->i_blocked = (int) $a_data['blocked'];
		$this->s_loginType	= $a_data['loginType'];
		$this->s_language	= $a_data['language'];

		$s_systemLanguage	= Memory::services('Language')->getLanguage();
		if( defined('USERID') && USERID == $this->i_userid && $this->s_language != $s_systemLanguage){
			if( $this->getLanguage() != $this->s_language ){
				$this->service_QueryBuilder->update('users','language','s',$s_systemLanguage)->getWhere()->addAnd('id','i',$this->i_userid);
				$this->service_QueryBuilder->getResult();
			}
		}
	}

	/**
	 * Returns the userid
	 *
	 * @return int The userid
	 */
	public function getID() {
		return $this->i_userid;
	}

	/**
	 * Returns the username
	 *
	 * @return String   The username
	 */
	public function getUsername() {
		return $this->s_username;
	}

	/**
	 * Sets the username
	 *
	 * @param String $s_username    The new username
	 * @throws Exception	If the username is empty
	 */
	public function setUsername($s_username) {
		Memory::type('string', $s_username);

		if (empty($s_username))
		throw new Exception("Username can not be empty");
		 
		$this->s_username = $s_username;
	}

	/**
	 * Returns the email address
	 *
	 * @return String   The email address
	 */
	public function getEmail() {
		return $this->s_email;
	}

	/**
	 * Sets the email address
	 *
	 * @param String $s_email	The email address
	 * @throws Exception	If the email address is empty
	 */
	public function setEmail($s_email) {
		Memory::type('string', $s_email);

		if (empty($s_email))
		throw new Exception("Email can not be empty");

		$this->s_email = $s_email;
	}

	/**
	 * Sets a new password
	 * Note : username has to be set first!
	 *
	 * @param String $s_password		The plain text password
	 * @param Boolean	$bo_expired		Set to true to set the password to expired
	 */
	public function setPassword($s_password,$bo_expired = false) {
		Memory::type('string', $s_password);

		$this->s_password = $this->hashPassword($s_password,$this->s_username);

		if( is_null($this->i_userid) )	return;

		if( $bo_expired ){
			$this->service_QueryBuilder->update('users',array('password','password_expired'),array('s','s'), array($this->s_password,'1'));
			$this->service_QueryBuilder->getWhere()->addAnd('id','i',$this->i_userid);
			$this->service_QueryBuilder->getResult();
		}
		else {
			$this->service_QueryBuilder->update('users','password','s', $this->s_password)->getWhere()->addAnd('id','i',$this->i_userid);
			$this->service_QueryBuilder->getResult();
		}
	}

	/**
	 * Checks if the user is a system account
	 *
	 * @return Boolean	True if the user is a system account
	 */
	public function isBot() {
		return ($this->i_bot == 1);
	}

	/**
	 * Sets the account as a normal or system account
	 *
	 * @param Boolean $bo_bot	Set to true for a system account
	 */
	public function setBot($bo_bot) {
		Memory::type('boolean', $bo_bot);

		$this->i_bot = 0;
		if ($bo_bot)
		$this->i_bot = 1;
	}

	/**
	 * Checks if the user is enabled
	 *
	 * @return Boolean	True if the user is enabled
	 */
	public function isEnabled() {
		return ($this->i_active == 1);
	}

	/**
	 * Returns the registration date
	 *
	 * @return int	The registration date as a timestamp
	 */
	public function getRegistrated() {
		return $this->i_registrated;
	}

	/**
	 * Checks if the account is blocked
	 *
	 * @return Boolean		True if the account is blocked
	 */
	public function isBlocked() {
		return ($this->i_blocked == 1);
	}

	/**
	 * (Un)Blocks the account
	 *
	 * @param Boolean $bo_blocked	Set to true to block the account, otherwise false
	 */
	public function setBlocked($bo_blocked) {
		Memory::type('boolean', $bo_blocked);

		$this->i_blocked = 0;
		if ($bo_blocked)
		$this->i_blocked = 1;
	}

	/**
	 * Sets the activation code
	 *
	 * @param String $s_activation	The activation code
	 */
	public function setActivation($s_activation){
		$this->s_activation	= $s_activation;
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
		$this->s_profile	= $s_profile;
	}

	/**
	 * Returns the groups where the user is in
	 *
	 * @return arrays	The groups
	 */
	public function getGroups(){
		$a_groups	= $this->model_Groups->getGroups();
		$a_groupsUser	= array();
		 
		foreach($a_groups AS $obj_group){
			$i_level	= $obj_group->getLevelByGroupID($this->i_userid);

			if( $i_level != Session::FORBIDDEN ){
				$a_groupsUser[$obj_group->getID()] = $i_level;
			}
		}
		 
		return $a_groupsUser;
	}

	/**
	 * Returns the access level for the current group
	 *
	 * @return int	The access level
	 */
	public function getLevel() {
		if (!is_null($this->i_level))
		return $this->i_level;

		if( is_null($this->i_userid) )
		return Session::FORBIDDEN;

		$this->i_level = $this->model_Groups->getLevel($this->i_userid);
		return $this->i_level;
	}

	/**
	 * Changes the password
	 *
	 * @param   String  $s_password     The new password
	 * @throws	Exception	If the account is not saved yet
	 */
	public function changePassword($s_password) {
		Memory::type('string', $s_password);
		 
		if( is_null($this->i_userid) )
			throw new Exeception("Can not change password from a not existing account");

		$this->service_QueryBuilder->update('users','password','s',$s_password)->getWhere()->addAnd('id','i',$this->i_userid);
		$this->service_QueryBuilder->getResult();
	}

	/**
	 * Disables the user account
	 */
	public function disableAccount() {
		$this->i_active = 0;
	}

	/**
	 * Enabled the user account
	 */
	public function enableAccount() {
		$this->i_active = 1;
	}

	/**
	 * Returns the color corosponding the users level
	 *
	 * @param   int $i_groupid  The groupid, leave empty for site group
	 * @return  String The color
	 */
	public function getColor($i_groupid = -1) {
		Memory::type('int', $i_groupid);

		$i_level = $this->checkGroup($i_groupid);

		switch ($this->getLevel($i_level)) {
			case Session::FORBIDDEN :
				return Session::FORBIDDEN_COLOR;

			case Session::USER :
				return Session::USER_COLOR;

			case Session::MODERATOR :
				return Session::MODERATOR_COLOR;

			case Session::ADMIN :
				return Session::ADMIN_COLOR;
		}
	}

	/**
	 * Checks is the visitor has moderator rights
	 *
	 * @param       int $i_groupid  The group ID, leave empty for site group
	 * @return      Boolean True if the visitor has moderator rights, otherwise false
	 */
	public function isModerator($i_groupid = -1) {
		Memory::type('int', $i_groupid);

		$i_groupid = $this->checkGroup($i_groupid);

		return ( $this->getLevel($i_groupid) >= Session::MODERATOR );
	}

	/**
	 * Checks is the visitor has administrator rights
	 *
	 * @param   int $i_groupid  The group ID, leave empty for site group
	 * @return  Boolean True if the visitor has administrator rights, otherwise false
	 */
	public function isAdmin($i_groupid = -1) {
		Memory::type('int', $i_groupid);

		$i_groupid = $this->checkGroup($i_groupid);
		return ( $this->getLevel($i_groupid) >= Session::ADMIN );
	}

	/**
	 * Checks the group ID
	 *
	 * @param int $i_groupid	The groupID, may be -1 for site group
	 * @return int	The group ID
	 */
	private function checkGroup($i_groupid) {
		if ($i_groupid == -1)
		$i_groupid = GROUP_SITE;

		return $i_groupid;
	}

	/**
	 * Sets the password as expired
	 * Forcing the user to change the password
	 */
	public function expirePassword(){
		$this->service_QueryBuilder->update('users','password_expires','s','i')->getWhere()->addAnd('id','i',$this->i_userid);
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
	public function save() {
		if( !is_null($this->i_userid) ){
			$this->persist();
			return;
		}
		 
		$this->i_registrated = time();

		$this->service_QueryBuilder->insert('users',array('nick','email','password','bot','registrated','active','activation','profile','loginType')
			,array('s','s','s','s','i','s','s','s','s'),array($this->s_username,$this->s_email,$this->s_password,$this->i_bot,$this->i_registrated,
			$this->i_active,$this->s_activation,$this->s_profile,$this->s_loginType));

		$this->i_userid = (int) $this->service_QueryBuilder->getResult()->getId();

		if ($this->i_userid == -1) {
			return;
		}

		$this->model_Groups->addUserDefaultGroups($this->i_userid);
	}

	/**
	 * Saves the changed user in the database
	 */
	public function persist() {
		if( is_null($this->i_userid) ){
			$this->save();
			return;
		}
		
		$this->service_QueryBuilder->update('users',array('nick','email','bot','active', 'blocked','profile'),array('s','s','s','s','s','s'),
			array($this->s_username,$this->s_email,$this->i_bot,$this->i_active,$this->i_blocked,$this->s_profile));
		$this->service_QueryBuilder->getWhere()->addAnd('id','i',$this->i_userid);
		$this->service_QueryBuilder->getResult();
	}

	/**
	 * Deletes the user permantly
	 */
	public function delete(){
		if( is_null($this->i_userid) )
		return;
		 
		/* Delete user from groups */
		$this->model_Groups->deleteGroupsUser($this->i_userid);
		 
		$this->service_QueryBuilder->delete('users')->getWhere()->addAnd('id','i',$this->i_userid);
		$this->service_QueryBuilder->getResult();
		$this->i_userid	= null;
	}
}
?>
