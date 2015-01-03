<?php
namespace core\models;

/**
 * User data model.  Contains the user data
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   04/05/2014
 *
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
if( !class_exists('GeneralUser') ){
  require(NIV.'core/models/GeneralUser.inc.php');
}

class User extends GeneralUser {
  protected $service_Session;
	protected $model_Groups;
	protected $a_userModels;
  protected $model_UserData;

	/**
   * PHP5 constructor
   * 
   * @param \core\services\QueryBuilder $service_QueryBuilder The query builder
   * @parma \core\services\Security $service_Security The security service
   * @param \core\services\Hashing $service_Hashing   The hashing service
   * @param \core\services\Session $service_Session   The session service
   * @param \core\models\Groups $model_Groups   The groups model
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder, \core\services\Security $service_Security,\core\services\Hashing $service_Hashing, 
    \core\services\Session $service_Session,\core\models\Groups $model_Groups,\core\models\data\Data_User $model_UserData){
		parent::__construct($service_QueryBuilder,$service_Security,$service_Hashing);
			
		$this->a_userModels = array();
    $this->model_Groups = $model_Groups;
    $this->model_UserData = $model_UserData;
    $this->service_Session  = $service_Session;
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
		$this->service_QueryBuilder->select('users','*')->getWhere()->addAnd('id','i',array(0=>$a_userid),'IN');
		$service_Database = $this->service_QueryBuilder->getResult();

		if( $service_Database->num_rows() > 0 ){
			$a_data = $service_Database->fetch_assoc();

			foreach($a_data AS $a_user){
				$i_userid   = (int)$a_user['id'];

				if( array_key_exists($i_userid,$this->a_userModels) ){
					$a_users[$i_userid] = $this->a_userModels[$i_userid];
				}
				else {
					$obj_User   = $this->model_UserData->cloneModel();
					$obj_User->loadData($i_userid);
					$a_users[$i_userid]  = $obj_User;
				}
			}
		}
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
		$i_userid = (int)$this->checkUserid($i_userid);

		if( $i_userid == -1 ){
			return $this->model_UserData->cloneModel();
		}

		if( array_key_exists($i_userid,$this->a_userModels) ){
      return $this->a_userModels[$i_userid];
    }

		$obj_User   = $this->model_UserData->cloneModel();
		$obj_User->loadData($i_userid);
		$this->a_userModels[$i_userid]  = $obj_User;

		return $this->a_userModels[$i_userid];
	}

	/**
	 * Checks the userid
	 *
	 * @param int $i_userid		The userid, may be -1 for current user
	 * @return int	The userid
	 */
	private function checkUserid($i_userid){
    if( $i_userid == -1 && defined('USERID') ){   $i_userid   = USERID; }

		return (int) $i_userid;
	}

	/**
	 * Gets 25 of the users sorted on nick. Start from the given position, default 0
	 *
	 * @param       int $i_start    The startposition for the search, default 0
	 * @return      array   The users
	 */
	public function getUsers($i_start = 0){
		\core\Memory::type('int',$i_start);

		$this->service_QueryBuilder->select('users','*')->order('nick','ASC')->limit(25,$i_start);
		$service_Database = $this->service_QueryBuilder->getResult();

		$a_users    = $service_Database->fetch_assoc();
		$a_result   = array('number'=>0,'data'=>array());

		foreach($a_users AS $a_user){
			$obj_User   = $this->model_UserData->cloneModel();
			$obj_User->setData($a_user);
			$a_result['data'][] = $obj_User;
		}

		$this->service_QueryBuilder->select('users',$this->service_QueryBuilder->getCount('id','amount'));
		$a_result['number']	= $this->service_QueryBuilder->getResult()->result(0,'amount');

		return $a_result;
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

		$this->service_QueryBuilder->select('users','*')->order('nick','ASC')->limit(25)->getWhere()->addAnd('nick','s','%'.$s_username.'%','LIKE');

		$a_users    = $this->service_QueryBuilder->getResult()->fetch_assoc();
		$a_result   = array('number'=>0,'data'=>array());

		foreach($a_users AS $a_user){
			$obj_User   = $this->model_UserData->cloneModel();
			$obj_User->setData($a_user);
			$a_result['data'][] = $obj_User;
		}

		return $a_result;
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
		$s_passwordOld	= $this->hashPassword($s_passwordOld, $s_username);
		$s_password		= $this->hashPassword($s_password, $s_username);
			
		$this->service_QueryBuilder->select('users','id')->getWhere()->addAnd(array('id','password'),array('i','s'),array($i_userid,$s_passwordOld));
		$service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() == 0 ){ return false; }

		$this->service_QueryBuilder->update('users',array('password','password_expired'),array('s','s'),array($s_password,'0'));
		$this->service_QueryBuilder->getWhere()->addAnd('id','i',$i_userid);
		$this->service_QueryBuilder->getResult();
		return true;
	}

	/**
	 * Creates a new user object
	 *
	 * @return Data_User    The user object
	 */
	public function createUser(){
		return $this->model_UserData->cloneModel();
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
		\core\Memory::type('string',$s_username);
		\core\Memory::type('int',$i_userid);
		\core\Memory::type('string',$s_type);

		if( $i_userid != -1 ){
			$this->service_QueryBuilder->select('users','id')->getWhere()->addAnd(array('nick','loginType','id'),array('s','s','i'),array($s_username,$s_type,$i_userid),array('=','=','<>'));
		}
		else {
			$this->service_QueryBuilder->select('users','id')->getWhere()->addAnd(array('nick','loginType'),array('s','s'),array($s_username,$s_type));
		}

		$service_Database = $this->service_QueryBuilder->getResult();
		if( $service_Database->num_rows() != 0 ){
			return false;
		}

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
		\core\Memory::type('string',$s_email);
		\core\Memory::type('int',$i_userid);

		if( $i_userid != -1 ){
			$this->service_QueryBuilder->select('users','id')->getWhere()->addAnd(array('email','id'),array('s','i'),array($s_email,$i_userid),array('=','<>'));
		}
		else {
			$this->service_QueryBuilder->select('users','id')->getWhere()->addAnd('email','s',$s_email);
		}

		$service_Database = $this->service_QueryBuilder->getResult();
		if( $service_Database->num_rows() != 0 ){
			return false;
		}

		return true;
	}

	/**
	 * Returns the site admins (control panel)
	 *
	 * @return  Array   The admins
	 */
	public function getSiteAdmins(){
		$this->service_QueryBuilder->select('users u','u.id,u.nick')->innerJoin('group_users g','u.id','g.userid');
		$this->service_QueryBuilder->order('u.nick')->getWhere()->addAnd('g.groupID','i',0);
		$service_Database = $this->service_QueryBuilder->getResult();
		
		return $service_Database->fetch_assoc();
	}

	/**
	 * Gets the id from all the activated users
	 *
	 * @return array	The ID's
	 */
	public function getUserIDs(){
		$this->service_QueryBuilder->select('users','id')->getWhere()->addAnd(array('active','blocked'),array('s','s'),array('1','0'));
		$service_Database = $this->service_QueryBuilder->getResult();
		
		$a_users    = $service_Database->fetch_assoc();

		return $a_users;
	}
}
?>
