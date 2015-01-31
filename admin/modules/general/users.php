<?php

namespace admin;

/**
 * Admin user configuration class
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed    25/09/10
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
define('NIV', '../../../');
include(NIV . 'core/AdminLogicClass.php');

class Users extends \core\AdminLogicClass {
	private $model_Groups;

	/**
	 * Starts the class Users
	 */
	public function __construct() {
		$this->init();

		if( !\core\Memory::isAjax() )
			exit();

		if (isset($this->get['command'])) {
			if ($this->get['command'] == 'view') {
				$this->view();
			} else if ($this->get['command'] == 'checkUsername') {
				if( $this->checkUsername($this->get['username']) ){
					$this->service_Template->set('result','1');
				}
				else {
					$this->service_Template->set('result','0');
				}
			} else if ($this->get['command'] == 'checkEmail') {
				if( $this->checkEmail($this->get['email']) ){
					$this->service_Template->set('result','1');
				}
				else {
					$this->service_Template->set('result','0');
				}
			}
			else if( $this->get['command'] == 'index' ){
				$this->index();
			}
			else if( $this->get['command'] == 'search' && isset($this->get['username']) ){
				$this->search();
			}
			else if( $this->get['command'] == 'addScreen' ){
				$this->addScreen();
			}
			else if( $this->get['command'] == 'editScreen' ){
				$this->editScreen();
			}
		} else if (isset($this->post['command'])) {
			if ($this->post['command'] == 'add') {
				$this->add();
			} else if ($this->post['command'] == 'edit') {
				$this->edit();
			}
			else if ($this->post['command'] == 'delete') {
				$this->delete();
			}
			else if( $this->post['command'] == 'changePassword' ){
				$this->changePassword();
			} else if ($this->post['command'] == 'changeGroup') {
				$this->changeGroup();
			}
		}
	}

	/**
	 * Inits the class Users
	 */
	protected function init() {
		$this->init_get = array(
				'userid' => 'int',
				'command' => 'string',
				'email' => 'string-DB',
				'username'	=> 'string-DB',
				'page'	=> 'int'
		);

		$this->init_post = array(
				'command' => 'string',
				'userid' => 'int',
				'username' => 'string-DB',
				'firstName' => 'string-DB',
				'nameBetween' => 'string-DB',
				'surname' => 'string-DB',
				'email' => 'string-DB',
				'password'	=> 'string-DB',
				'bot' => 'int',
				'blocked' => 'int',
				'currentGroups'	=> 'int-array',
				'groupID'	=> 'int',
				'level'		=> 'int'
		);

		parent::init();

		$this->model_Groups = \core\Memory::models('Groups');
	}

	/**
	 * Generates the user overview
	 */
	private function index() {
		$i_page = 1;
		if( isset($this->get['page']) && $this->get['page'] > 0 ){
			$i_page = $this->get['page'];
		}		
		
		$i_start 	= $i_page*25-25;
		
		$a_users = $this->model_User->getUsers($i_start);
		$this->service_Template->set('userid',USERID);

		$this->userView($a_users);
		
		$helper_Nav	= \core\Memory::helpers('PageNavigation');
		$helper_Nav->setAmount($a_users['number'])->setPage($i_page)->setUrl('javascript:adminUsers.view({page})');
		$this->service_Template->set('nav',$helper_Nav->generateCode());
	}

	/**
	 * Generates the search overview
	 */
	private function search(){
		$this->service_Template->loadView('index.tpl');
		$a_users	= $this->model_User->searchUser($this->get['username']);
			
		$this->userView($a_users);
	}

	/**
	 * Generates the overview
	 * 
	 * @param array $a_users	The users to display
	 */
	private function userView($a_users){
		$this->service_Template->set('headerText', $this->service_Language->get('language/admin/users/users'));
		$this->service_Template->set('headerNick', $this->service_Language->get('language/admin/users/user'));

		foreach ($a_users['data'] AS $obj_user) {
			$a_data = array(
					'id' => $obj_user->getID(),
					'nick' => $obj_user->getUsername(),
					'email'	=> $obj_user->getEmail()
			);

			$this->service_Template->setBlock('users', $a_data);
		}

		$this->service_Template->set('view', $this->service_Language->get('language/buttons/view'));
		$this->service_Template->set('edit', $this->service_Language->get('language/buttons/edit'));
		$this->service_Template->set('delete', $this->service_Language->get('language/buttons/delete'));
		$this->service_Template->set('textAdd',$this->service_Language->get('language/buttons/add'));

		$this->service_Template->set('searchTitle',$this->service_Language->get('language/admin/users/headerSearch'));
		$this->service_Template->set('searchText',$this->service_Language->get('language/buttons/search'));
	}

	/**
	 * Displays the headers
	 */
	private function displayHeaders(){
		$this->service_Template->set('usernameHeader',$this->service_Language->get('language/admin/users/username'));
		$this->service_Template->set('emailHeader',$this->service_Language->get('language/admin/users/email'));
		$this->service_Template->set('botHeader',$this->service_Language->get('language/admin/users/bot'));
	}

	/**
	 * Generates the user detail view
	 */
	private function view() {
		try{
			$obj_User = $this->model_User->get($this->get['userid']);
		}
		catch(Exception $e){
			\core\Memory::services('Logs')->securityLog('Call to unknown user '.$this->get['userid']);
			$this->service_Session->destroyLogin();
			exit();
		}
		$i_registrated = $obj_User->getRegistrated();
		$s_registrated = '';
		if ($i_registrated > 0) {
			$s_registrated = date('d-m-Y H:i:s', $i_registrated);
		}

		$s_yes	= $this->service_Language->get('language/admin/users/yes');
		$s_no	= $this->service_Language->get('language/admin/users/no');

		$this->service_Template->set('userid',USERID);
		$this->service_Template->set('id',$obj_User->getID());
		$this->service_Template->set('username',$obj_User->getUsername());
		$this->service_Template->set('email',$obj_User->getEmail());
		$this->service_Template->set('bot',($obj_User->isBot() ? $s_yes : $s_no));
		$this->service_Template->set('registrated',$s_registrated);
		$this->service_Template->set('active',($obj_User->isEnabled() ? $s_yes : $s_no));
		$this->service_Template->set('blocked',($obj_User->isBlocked() ? $s_yes : $s_no));

		$this->displayHeaders();
		$this->service_Template->set('registratedHeader',$this->service_Language->get('language/admin/users/registrated'));
		$this->service_Template->set('activeHeader',$this->service_Language->get('language/admin/users/activated'));
		$this->service_Template->set('headerText',$this->service_Language->get('language/admin/users/headerView'));
		$this->service_Template->set('blockedHeader',$this->service_Language->get('language/admin/users/blocked'));

		$this->service_Template->set('buttonBack',$this->service_Language->get('language/buttons/back'));
		$this->service_Template->set('edit', $this->service_Language->get('language/buttons/edit'));
		$this->service_Template->set('delete', $this->service_Language->get('language/buttons/delete'));
	}

	/**
	 * Generates the edit screen
	 */
	private function editScreen() {
		try{
			$obj_User = $this->model_User->get($this->get['userid']);
		}
		catch(Exception $e){
			\core\Memory::services('Logs')->securityLog('Call to unknown user '.$this->get['userid']);
			$this->service_Session->destroyLogin();
			exit();
		}
		$i_registrated = $obj_User->getRegistrated();
		$s_registrated = '';
		if ($i_registrated > 0) {
			$s_registrated = date('d-m-Y H:i:s', $i_registrated);
		}
			
		$s_yes	= $this->service_Language->get('language/admin/users/yes');
		$s_no	= $this->service_Language->get('language/admin/users/no');

		$this->service_Template->set('userid',USERID);
		$this->service_Template->set('id',$obj_User->getID());
		$this->service_Template->set('username',$obj_User->getUsername());
		$this->service_Template->set('email',$obj_User->getEmail());
		$this->service_Template->set('registrated',$s_registrated);
			
		if( $obj_User->isBot() ){
			$this->service_Template->set('bot1','checked="checked"');
		}
		else {
			$this->service_Template->set('bot0','checked="checked"');
		}
		if( $obj_User->isBlocked() ){
			$this->service_Template->set('blocked1','checked="checked"');
		}
		else {
			$this->service_Template->set('blocked0','checked="checked"');
		}
		if( $obj_User->isEnabled()){
			$this->service_Template->set('active',$s_yes);
		}
		else {
			$this->service_Template->set('active',$s_no);
		}

		$this->displayHeaders();
		$this->service_Template->set('registratedHeader',$this->service_Language->get('language/admin/users/registrated'));
		$this->service_Template->set('activeHeader',$this->service_Language->get('language/admin/users/activated'));
		$this->service_Template->set('headerText',$this->service_Language->get('language/admin/users/headerEdit'));
		$this->service_Template->set('blockedHeader',$this->service_Language->get('language/admin/users/blocked'));
		
		$this->service_Template->set('buttonBack',$this->service_Language->get('language/buttons/back'));
		$this->service_Template->set('delete', $this->service_Language->get('language/buttons/delete'));
			
		$this->service_Template->set('no',$s_no);
		$this->service_Template->set('yes',$s_yes);
			
		$this->displayGroups($obj_User);
		
		$s_loginType	= $obj_User->getLoginType();
		if( $s_loginType == 'normal' ){
			$this->service_Template->loadTemplate('password_form','admin/users/passwordForm.tpl');
		}
		else {
			$this->service_Template->set('password_notice','<p>'.$this->service_Language->get('language/admin/users/registratedTrough').' '.ucfirst($s_loginType).'.</p>');
		}
	}

	/**
	 * Generates the add screen
	 */
	private function addScreen(){
		$this->displayHeaders();
			
		$this->service_Template->set('userTitle',$this->service_Language->get('language/admin/users/headerAdd'));
			
		$this->service_Template->set('buttonSave',$this->service_Language->get('language/buttons/save'));
		$this->service_Template->set('buttonBack',$this->service_Language->get('language/buttons/back'));
		
		$this->service_Template->set('passwordHeader',$this->service_Language->get('language/admin/users/password'));
		$this->service_Template->set('passwordRepeatHeader',$this->service_Language->get('language/admin/users/passwordAgain'));
			
		$this->service_Template->set('no',$this->service_Language->get('language/admin/users/no'));
		$this->service_Template->set('yes',$this->service_Language->get('language/admin/users/yes'));
	}

	/**
	 * Displays the groups from the user
	 * 
	 * @param Data_User $obj_User	The user object
	 */
	private function displayGroups($obj_User){
		$a_groupsUser	= $obj_User->getGroups();
		$a_groups	= $this->model_Groups->getGroups();
		$a_levels	= array(0=>$this->service_Language->get('language/rights/level_0'),1=>$this->service_Language->get('language/rights/level_1'),2=>$this->service_Language->get('language/rights/level_2'));
			
		$this->service_Template->set('groupHeader',$this->service_Language->get('language/admin/users/headerGroups'));
			
		foreach($a_groups AS $obj_group){
			$i_id	= $obj_group->getID();

			$s_checked	= '';
			$i_level	= 0; //default user
			if( array_key_exists($i_id,$a_groupsUser) ){
				$s_checked	= 'checked="checked"';
				$i_level	= $a_groupsUser[$i_id];
			}

			if( $i_id == 0 ){
				$s_selected	= '';
				if( $i_level == 2 )	$s_selected	= 'selected="selected"';
					
				$s_levels	= '<option value="2" '.$s_selected.'>'.$a_levels[2].'</option>';
			}
			else {
				$s_levels	= '';
				for($i=0; $i<=2; $i++){
					$s_selected	= '';
					if( $i == $i_level )	$s_selected	= 'selected="selected"';

					$s_levels	.= '<option value="'.$i.'" '.$s_selected.'>'.$a_levels[$i].'</option>';
				}
			}

			$a_data	= array(
					'groupID'	=> $i_id,
					'groupName'	=> $obj_group->getName(),
					'levels'	=> $s_levels,
					'checked'	=> $s_checked
			);

			if( $obj_group->isDefault() ){
				$this->service_Template->setBlock('groupDefault',$a_data);
			}
			else {
				$this->service_Template->setBlock('group',$a_data);
			}
		}
	}

	/**
	 * Edits the given user
	 */
	private function edit() {
		try{
			$obj_User = $this->model_User->get($this->post['userid']);
		}
		catch(Exception $e){
			\core\Memory::services('Logs')->securityLog('Call to unknown user '.$this->post['userid']);
			$this->service_Session->destroyLogin();
			exit();
		}
			
		if( !isset($this->post['firstName']) || $this->post['firstName'] == '' || !isset($this->post['nameBetween']) || 
				!isset($this->post['surname']) || $this->post['surname'] == '' || !isset($this->post['email']) || !isset($this->post['bot']) || ($this->post['bot'] != 0 && 
				$this->post['bot'] != 1) || !isset($this->post['blocked']) || ($this->post['blocked'] != 0 && $this->post['blocked'] != 1) )
			return;
			
		/* Edit user */
		$obj_User->setName($this->post['firstName']);
		$obj_User->setNameBetween($this->post['nameBetween']);
		$obj_User->setSurname($this->post['surname']);
		$obj_User->setEmail($this->post['email']);
		$obj_User->setBot($this->post['bot']);
		$obj_User->setBlocked($this->post['blocked']);
		$obj_User->persist();
	}

	/**
	 * Adds a new user to the database
	 */
	private function add() {
		if( !isset($this->post['username']) || $this->post['username'] == '' || !isset($this->post['firstName']) || $this->post['firstName'] == '' ||
				!isset($this->post['nameBetween']) || !isset($this->post['surname']) || $this->post['surname'] == '' || !isset($this->post['email']) ||
				!isset($this->post['bot']) || ($this->post['bot'] != 0 && $this->post['bot'] != 1) || !isset($this->post['password']) || $this->post['password'] == '')
			return;
	
		if( !$this->service_Security->checkEmail($this->post['email']) || !$this->model_User->checkUsername($this->post['username']) || !$this->checkEmail($this->post['email']) )
			return;
		
		/* Add user */
		$obj_User = $this->model_User->createUser();
		$obj_User->setUsername($this->post['username']);
		$obj_User->setName($this->post['firstName']);
		$obj_User->setNameBetween($this->post['nameBetween']);
		$obj_User->setSurname($this->post['surname']);
		$obj_User->setEmail($this->post['email']);
		$obj_User->setPassword($this->post['password'],true);
		$obj_User->setBot($this->post['bot']);
		$obj_User->enableAccount();
		$obj_User->persist();

			
		/* Send notification email */
		\core\Memory::services('Mailer')->adminAdd($this->post['username'],$this->post['password'],$this->post['email']);
	}

	/**
	 * Changes the group rights
	 */
	private function changeGroup() {
		if( !isset($this->post['groupID']) || $this->post['groupID'] < 0 || !isset($this->post['userid']) || $this->post['userid'] <= 0 || !isset($this->post['level']) || !in_array($this->post['level'],array(-1,0,1,2) ))
			exit();
		
		try {
			$obj_group	= $this->model_Groups->getGroup($this->post['groupID']);
			$obj_group->editUser($this->post['userid'],$this->post['level']);
		}
		catch(Exception $e){
			\core\Memory::services('Logs')->securityLog('Call to unknown group '.$this->post['groupID']);
			$this->service_Session->destroyLogin();
			exit();
		}
	}

	/**
	 * Deletes the given user
	 */
	private function delete() {
		if( $this->post['userid'] == USERID )
			exit();
			
		try{
			$obj_User = $this->model_User->get($this->post['userid']);
		}
		catch(Exception $e){
			\core\Memory::services('Logs')->securityLog('Call to unknown user '.$this->post['userid']);
			$this->service_Session->destroyLogin();
			exit();
		}
			
		/* Say bye bye */
		$obj_User->delete();
	}

	/**
	 * Changes the user password
	 */
	private function changePassword(){
		if( !isset($this->post['password']) || $this->post['password'] == '' || !isset($this->post['userid']) || $this->post['userid'] <= 0 )
			return;
			
		try{
			$obj_User = $this->model_User->get($this->post['userid']);
			$obj_User->setPassword($this->post['password'],true);
			$obj_User->setBlocked(false);
			$obj_User->enableAccount();
			$obj_User->persist();

			\core\Memory::services('Mailer')->adminPasswordReset($obj_User->getUsername(),$obj_User->getEmail(),$this->post['password']);
		}
		catch(Exception $e){
			\core\Memory::services('Logs')->securityLog('Call to unknown user '.$this->post['userid']);
			$this->service_Session->destroyLogin();
			exit();
		}
	}

	/**
	 * Checks if the username is available
	 *
	 * @return boolean	True if the username is available
	 */
	private function checkUsername($s_username){
		return  $this->model_User->checkUsername($s_username);
	}

	/**
	 * Checks if the email is available
	 *
	 * @return boolean	True if the email is available
	 */
	private function checkEmail($s_email){
		return $this->model_User->checkEmail($s_email);
	}
}

$obj_Users = new Users();
unset($obj_Users);
