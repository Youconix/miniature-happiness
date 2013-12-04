<?php
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
class Service_Authorization extends Service {
	protected $service_Cookie;
	protected $service_Session;
	protected $service_Database;
	protected $service_QueryBuilder;
	protected $service_Logs;
	protected $s_openID_dir; 
	protected $a_openID	= array();
	protected $a_openID_types	= array();
	
	/**
	 * Inits the service Autorization
	 */
	public function __construct(){
		$this->s_openID_dir = NIV.'include/openID/';
		require_once($this->s_openID_dir.'OpenAuth.inc.php');
		$a_id	= Memory::services('File')->readDirectory($this->s_openID_dir);
		foreach($a_id AS $s_id){
			if( $s_id == '.' || $s_id == '..' || $s_id == 'OpenAuth.inc.php')	continue;
			
			$a_data	= explode('.',$s_id);			
			$this->a_openID_types[$a_data[0]]	= array($a_data[0],$s_id);
		}

		$this->service_Cookie	= Memory::services('Cookie');
		$this->service_Session	= Memory::services('Session');
		$this->service_Database	= Memory::services('Database');
		$this->service_QueryBuilder = Memory::services('QueryBuilder')->createBuilder();
		$this->service_Logs		= Memory::services('Logs');
	}
	
	/**
	 * Returns the available openID libs
	 * 
	 * @return array	The openID lib names
	 */
	public function getOpenIDList(){
		$a_openID	= array();
		foreach($this->a_openID_types AS $a_type){
			$a_openID[]	= $a_type[0];
		}
		
		return $a_openID;
	}
	
	/**
	 * Registers the user normaly
	 * 
	 * @param array		$a_data	The form data
	 * @param	bool		$bo_skipActivation	Set to true to skip sending the activation email (auto activation)
	 * @return bool	True if the user is registrated
	 */
	public function registerNormal($a_data,$bo_skipActivation = false){
		$s_username	= $a_data['username'];
		$s_forname	= $a_data['forname'];
		$s_nameBetween	= $a_data['nameBetween'];
		$s_surname	= $a_data['surname'];
		$s_password	= $a_data['password'];
		$s_email	= $a_data['email'];
		$s_nationality	= $a_data['nationality'];
		$s_telephone	= $a_data['telephone'];
		
		try {
			$this->service_Database->transaction();
			
			$s_registrationKey  = sha1(time().' '.$s_username.' '.$s_email);
			
			$obj_User	= Memory::models('User')->createUser();			
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
				$this->sendActivationEmail($s_username,$s_email,$s_registrationKey);
			}
			
			$this->service_Database->commit();
			
			if( !$bo_skipActivation ){
				Memory::models('User')->activateUser($s_registrationKey);
			}
			
			return true;
		}
		catch(Exception $e){
			$this->service_Database->rollback();
			
			Memory::services('ErrorHandler')->error($e);
			return false;
		}
	}
	
	/**
	 * Starts the registration through open ID
	 * 
	 * @param  array	$a_data	The form data
	 * @return Boolean	True if the user is registrated
	 * @throws Exception	Unknown openID libary
	 */
	public function registerOpenID($a_data){
		$obj_openID	= $this->getOpenID($a_data['type']);
		
		/* Temp save data */
		$this->service_Cookie->set('openID',$a_data['type'],'/');
		$this->service_Session->set('forname',$a_data['forname']);
		$this->service_Session->set('nameBetween',$a_data['nameBetween']);
		$this->service_Session->set('surname',$a_data['surname']);
		$this->service_Session->set('nationality',$a_data['nationality']);
		$this->service_Session->set('telephone',$a_data['telephone']);
		
		$this->service_Cookie->set('redirectOpenID','registration.php','/');
		
		$obj_openID->registration();
	}
	
	/**
	 * Registers the user trough open ID
	 * User gets auto logged in and redirect to index.php
	 * 
	 * @param String $s_code	The openID response code
	 * @param String $s_type	The account type (Family|Au pair)
	 * @param Boolean $bo_redirect	Set to true for auto redirect to home.php
	 * @return int	The fault code
	 * 		-1		Session timeout or communication error
	 * 		0		Username allready taken with openID server
	 * 		1		Email adres is taken
	 * 		2		Registration complete
	 */
	public function registerOpenIDConfirm($s_code,$s_type,$bo_redirect = true){
		if( !$this->service_Cookie->exists('openID') ){
			/* Timeout */
			return -1;
		}
		$s_loginType	= $this->service_Cookie->get('openID');
		$this->service_Cookie->delete('redirectOpenID','/');
		
		$obj_openID	= $this->getOpenID($s_loginType);	
		$a_data	= $obj_openID->registrationConfirm($s_code);
		
		if( is_null($a_data) )
			return -1;
		
		$model_User	= Memory::models('User');
		/* Check username */
		if( !$model_User->checkUsername($a_data['username'],-1,$s_loginType) )
			return 0;
		
		/* Check email */
		if( !$model_User->checkEmail($a_data['email']) )
			return 1;
		
		$s_forname	= $this->service_Session->get('forname');
		$s_nameBetween	= $this->service_Session->get('nameBetween');
		$s_surname	= $this->service_Session->get('surname');
		$s_nationality	= $this->service_Session->get('nationality');
		$s_telephone	= $this->service_Session->get('telephone');
		
		$obj_User	= $model_User->createUser();
		$obj_User->setUsername($a_data['username']);
		$obj_User->setName($s_forname);
		$obj_User->setNameBetween($s_nameBetween);
		$obj_User->setSurname($s_surname);
		$obj_User->setEmail($a_data['email']);
		$obj_User->enableAccount();
		$obj_User->setLoginType($s_loginType);
		$obj_User->setBot(false);
		$obj_User->setType($s_type);
		$obj_User->setNationality($s_nationality);
		$obj_User->setTelephone($s_telephone);
		$obj_User->save();
		
		/* Auto login */
		$this->service_Session->setLogin($obj_User->getID(),$a_data['username']);

		$this->service_Cookie->delete('openID','/');

		if( $bo_redirect ){
			header('location: '.NIV.'home.php');
			exit();
		}
		
		return 2;
	}
	
	/**
	 * Logs the user in normally
	 * 
	 * @param	String	$s_username	The username
	 * @param	String	$s_password	The plain text password
	 * @param  Boolean	$bo_autologin	Set to true for auto login
	 * @return array	The id, username and password_expired if the login is correct, otherwise null
	 */
	public function login($s_username, $s_password,$bo_autologin = false){
		$model_User	= Memory::models('User');
		$s_password	= $model_User->createUser()->hashPassword($s_password,$s_username);
		$i_tries		= $model_User->registerLoginTries();
		if( $i_tries > 6 ){
			/* Don't even check data */
			$this->service_Logs->loginLog($s_username,'failed',$i_tries);
			return null;
		}
		
		$this->service_QueryBuilder->select('users','id, nick,bot,active,blocked,password_expired,lastLogin');
		$this->service_QueryBuilder->getWhere()->addAnd(array('nick','password','active','loginType'),array('s','s','s','s'),array($s_username,$s_password,'1','normal'));
		$service_Database = $this->service_QueryBuilder->getResult();
		
		if( $service_Database->num_rows() == 0 ){
			$a_data	= null;
		}
		else {
			$a_data	= $service_Database->fetch_assoc();
		}
		
		if( $a_data[0]['bot'] == '1' || $a_data[0]['active'] == '0' || $a_data[0]['blocked'] == '1'){
			$a_data	= null;
		}		
		
		if( is_null($a_data) || $i_tries >= 5 ){
			if( $i_tries == 5 ){
				$model_User->disableAccount($s_username);
				$model_PM   = Memory::models('PM');
				$model_PM->systemMessage('Account block','The account '.$s_username.' is disabled on '.date('d-m-Y H:i:s').' after 3 failed login attempts.\n\n System');
			}
			else if( $i_tries == 10 ){
				$this->service_QueryBuilder->insert('ipban','ip','s',$_SERVER['REMOTE_ADDR'])->getResult();
		
				$model_PM   = Memory::models('PM');
				$model_PM->systemMessage('IP block','The IP '.$_SERVER['REMOTE_ADDR'].' is blocked on '.date('d-m-Y H:i:s').' after 6 failed login attempts. \n\n System');
			}
		
			$this->service_Logs->loginLog($s_username,'failed',$i_tries);
		
			return null;
		}
		
		$model_User->clearLoginTries();
		$this->service_Logs->loginLog($s_username,'success',$i_tries);
		
		unset($a_data[0]['bot']);
		unset($a_data[0]['active']);
		unset($a_data[0]['blocked']);
		
		if( $bo_autologin ){
			$this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('userID','i',$a_data[0]['id']);
			$this->service_QueryBuilder->getResult();
			
			$this->service_QueryBuilder->insert('autologin',array('userID','username','type','IP'),
					array('i','s','s','s'),array($a_data[0]['id'],$a_data[0]['nick'],$a_data[0]['userType'],$_SERVER['REMOTE_ADDR']));
			$service_Database = $this->service_QueryBuilder->getResult();
			
			$a_data[0]['autologin']	= $service_Database->getID();
		}
		
		return $a_data[0];
	}

	/**
	 * Performs the auto login
	 * 
	 * @param int $i_id		The auto login ID
	 * @return array	The id, username and password_expired if the login is correct, otherwise null
	 */
	public function performAutoLogin($i_id){
		$this->service_QueryBuilder->select('users u','u.id, u.nick,u.bot,u.active,u.blocked,u.password_expired,u.lastLogin,u.userType');
		$this->service_QueryBuilder->innerJoin('autologin al','u.id','al.userID')->getWhere()->addAnd(array('al.id','al.IP'),
			array('i','s'),array($i_id,$_SERVER['REMOTE_ADDR']));
		
		$service_Datababase = $this->service_QueryBuilder->getResult();
		if( $service_Database->num_rows() == 0 ){
			return null;
		}
		
		$a_data	= $service_Database->fetch_assoc();
		
		if( $a_data[0]['bot'] == '1' || $a_data[0]['active'] == '0' || $a_data[0]['blocked'] == '1'){
			$this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('id','i',$i_id);
			$this->service_QueryBuilder->getResult();
			return null;
		}		
		
		$this->service_Logs->loginLog($a_data[0]['nick'],'success',1);
		
		unset($a_data[0]['bot']);
		unset($a_data[0]['active']);
		unset($a_data[0]['blocked']);
		
		return $a_data[0];
	}
	
	/**
	 * Calls the openID login
	 * 
	 * @param	String	$s_type		The openID libary name
	 * @throws Exception	Unknown openID libary
	 */
	public function loginOpenID($s_type){
		$obj_openID	= $this->getOpenID($s_type);
		
		$this->service_Cookie->set('openID',$s_type,'/');
    $this->service_Cookie->set('redirectOpenID','login.php','/');
		
		$obj_openID->login();
	}
	
	/**
	 * Logs the user in trough openID
	 * 
	 * @param String $s_code	The openID response code
	 * @return array	The id and nick if the user is logged in, otherwise null
	 */
	public function loginOpenIDConfirm($s_code){
		if( !$this->service_Cookie->exists('openID') ){
			/* Timeout */
			return null;
		}
		$s_type	= $this->service_Cookie->get('openID');
		$this->service_Cookie->delete('redirectOpenID','/');
		
		$obj_openID	= $this->getOpenID($s_type);
		$s_username	= $obj_openID->loginConfirm($s_code);
		
		$this->service_QueryBuilder->select('users','id,nick,lastLogin,userType');
		$this->service_QueryBuilder->getWhere()->addAnd(array('nick','active','blocked','loginType'),array('s','s','s','s'),array($s_username,'1','0',$s_type));
		
		$service_Database = $this->service_QueryBuilder->getResult();
		if( $service_Database->num_rows() == 0 ){
			$this->service_Logs->loginLog($s_username,'failed',-1,$s_type);
			return null;
		}
		
		$this->service_Logs->loginLog($s_username,'success',-1,$s_type);
		$a_data	= $service_Database->fetch_assoc();
		return $a_data[0];
	}
	
	/**
	 * Logs the user out
	 */
	public function logout(){
		if( $this->service_Session->exists('openID') ){
			$obj_openID	= $this->getOpenID($this->service_Session->get('openID'));
			$obj_openID->logout();
			$this->service_Session->delete('openID');
		}
				
		$this->service_Session->destroyLogin();

		$service_Cookie	= Memory::services('Cookie');
		if( $service_Cookie->exists('autologin') ){
			$service_Cookie->delete('autologin','/');
			$this->service_QueryBuilder->delete('autologin')->getWhere()->addAnd('userID','i',USERID);			
			$this->service_QueryBuilder->getResult();
		}

		header('location: '.NIV.'index.php');
	}
	
	/**
	 * Loads the openID class
	 * 
	 * @param String $s_type	The name
	 * @throws Exception	Unknown openID libary
	 * @return OpenAuth		The class
	 */
	protected function getOpenID($s_type){
		if( array_key_exists($s_type,$this->a_openID) )
			return $this->a_openID[$s_type];
		
		if( !array_key_exists($s_type,$this->a_openID_types) )
			throw new Exception("Unknown openID libary with name ".$s_type);
		
		$a_data	= $this->a_openID_types[$s_type];
		require($this->s_openID_dir.$a_data[1]);
		$obj_ID	= new $s_type();
		$this->a_openID[$s_type] = $obj_ID;
		return $obj_ID;
	}
	
	/**
	 * Sends the activation email
	 *
	 * @param String $s_username					The username
	 * @param String $s_email							The email address
	 * @param String $s_registrationKey		The activation code
	 * @throws ErrorException If the sending of the email failes
	 */
	private function sendActivationEmail($s_username,$s_email,$s_registrationKey){
		$this->service_Mailer	= Memory::services('Mailer');
		if( !$this->service_Mailer->registrationMail($s_username,$s_email,$s_registrationKey) )
			throw new Exception("Sending registration mail to '.$s_email.' failed.");
	}
	
	/**
	 * Resends the activation email
	 *
	 * @param String $s_username					The username
	 * @param String $s_email							The email address
	 */
	public function resendActivationEmail($s_username,$s_email){
		$this->service_QueryBuilder->select('users','nick,email,activation')->getWhere()->addAnd(array('nick','email','activation'),
				array('s','s','s'),array($s_username,$s_email,''),array('=','=','<>'));			
		$service_Database = $this->service_QueryBuilder->getResult();
			
		if( $service_Database->num_rows() == 0 ){
			return false;
		}		
		
		try {
			$a_data = $service_Database->fetch_assoc();
			$this->sendActivationEmail($a_data[0]['nick'], $a_data[0]['email'],$a_data[0]['activation']);
				
			return true;
		}
		catch(Exception $e){
			return false;
		}
	}
}
?>
