<?php
if( !class_exists('\core\models\User') ){
  require(NIV.'include/models/User.inc.php');
}

class DummyModelUser extends \core\models\User {
  public function __construct(\core\models\data\Data_User $model_UserData){		
    $this->a_userModels = array();
    $this->model_UserData   = $model_UserData;
	}

	/**
	 * Gets the requested users
	 *
	 * @param   array $a_userid   Array from user IDs
	 * @return  Data_User-array   The data objects
	 */
	public function getUsersById($a_userid){
		\core\Memory::type('array',$a_userid);

		$a_users   = array();
		return $a_users;
	}

	/**
	 * Gets the requested user
	 *
	 * @param   int $i_userid   The userid, leave empty for logged in user
	 * @return  Data_User   The data object of a empty data object if the user is not logged in
	 * @throws  DBException If the userid is invalid
	 */
	public function get($i_userid = -1){
    $model = $this->createUser();
    $model->i_userid = $i_userid;
		
    return $model;
	}

	/**
	 * Gets 25 of the users sorted on nick. Start from the given position, default 0
	 *
	 * @param       int $i_start    The startposition for the search, default 0
	 * @return      array   The users
	 */
	public function getUsers($i_start = 0){
		\core\Memory::type('int',$i_start);

		return array();
	}

	/**
	 * Searches the user(s)
	 * Limitated on 25 results
	 *
	 * @param String $s_username	The username to search on
	 * @return      array   The users
	 */
	public function searchUser($s_username){
		\core\Memory::type('string',$s_username);

		return array();
	}

	/**
	 * Registers the login try
	 *
	 * @return int	The number of tries done including this one
	 */
	public function registerLoginTries(){
	}

	/**
	 * Clears the login tries
	 */
	public function clearLoginTries(){
	}

	/**
	 * Changes the saved password
	 *
	 * @param int		$i_userid		The user ID
	 * @param String	$s_username		The username
	 * @param String	$s_passwordOld	The current plain text password
	 * @param String	$s_password		The new plain text password
   * @return bool True if the password is changed
	 */
	public function changePassword($i_userid,$s_username,$s_passwordOld,$s_password){		
		return true;
	}

	/**
	 * Checks if the username is available
	 *
	 * @param   String  $s_username The username to check
	 * @param   int $i_userid    The userid who to exclude, -1 for ignore
	 * @param	String	$s_type	 The login type, default normal
	 * @return  boolean	True if the username is available
	 */
	public function checkUsername($s_username,$i_userid = -1,$s_type = 'normal'){
		return true;
	}

	/**
	 * Checks or the given email address is availabel
	 *
	 * @param       String  $s_email The email address to check
	 * @param       int $i_userid    The userid who to exclude, -1 for ignore
	 * @return      Boolean  True if the email address is available
	 */
	public function checkEmail($s_email,$i_userid = -1){
		return true;
	}

	/**
	 * Returns the site admins (control panel)
	 *
	 * @return  Array   The admins
	 */
	public function getSiteAdmins(){
		return array();
	}

	/**
	 * Disables the account by the username
	 * Sends a notification email
	 *
	 * @param String $s_username	The username
	 */
	public function disableAccount($s_username){
		\core\Memory::type('string',$s_username);
	}

	/**
	 * Gets the id from all the activated users
	 *
	 * @return array	The ID's
	 */
	public function getUserIDs(){
		return array();
	}
}
?>