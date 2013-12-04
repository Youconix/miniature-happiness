<?php
if( !class_exists('Service_Authorization') )
	require(NIV.'include/services/Authorization.inc.php');

/**
 * Facebook account authorization service
 * Handles registration and login from the accounts for the Facebook logins 
 *
 * @author:		Rachelle Scheijen <rachelle.scheijen@unixerius.nl>
 * @copyright	The au pair BV	2013
 * @since     1.0
 * @changed   24/09/12
 * @see		include/services/Autorization.inc.php
 */
class Service_FacebookAuthorization extends Service_Authorization {		
	/**
	 * Starts the registration through Facebook
	 * 
	 * @param String $s_code	The openID response code
	 * @param String $s_type	The account type (Family|Au pair)
	 * @param String $s_username	The account username
	 * @return int	The fault code
	 * 		0		Username allready taken with openID server
	 * 		1		Registration OK
	 */
	public function registerOpenID($s_code,$s_type,$s_username,$s_email){
		$obj_openID	= $this->getOpenID('Facebook');	
		
		$model_User	= Memory::models('User');
		/* Check username */
		if( !$model_User->checkUsername($s_username,-1,$s_type) )
			return 0;
			
		$this->service_Session->set('username',$s_username);
		$this->service_Session->set('email',$s_email);
		$this->service_Session->set('type',$s_type);
		return 1;
	}
	
	/**
	 * Registers the user trough Facebook
	 * 
	 * @param	array	$a_data	The registration data
	 */
	public function registerOpenIDConfirm($a_data){
		$service_Database	= Memory::services('Database');
		
		try {
			$service_Database->transaction();
			
			$s_loginType	= 'Facebook';
			$obj_openID	= $this->getOpenID('Facebook');	
			
			$model_User	= Memory::models('User');
			
			$s_username	= $this->service_Session->get('username');
			$s_type	= $a_data['accountType'];
						
			$obj_User	= $model_User->createUser();
			$obj_User->setUsername($s_username);
			$obj_User->setName($a_data['forname']);
			$obj_User->setNameBetween($a_data['nameBetween']);
			$obj_User->setSurname($a_data['surname']);
			$obj_User->setEmail($this->service_Session->get('email'));
			$obj_User->enableAccount();
			$obj_User->setLoginType($s_loginType);
			$obj_User->setBot(false);
			$obj_User->setType($s_type);
			$obj_User->setNationality($a_data['nationality']);
			$obj_User->setTelephone($a_data['telephone']);
			$obj_User->save();
			
			/* Auto login */
			$this->service_Session->setLogin($obj_User->getID(),$obj_User->getUsername());
			
			$service_Database->commit();
			
			header('location: '.NIV.'home.php');
			exit();
		}
		catch(DBException $e){
			$service_Database->rollback();
			
			header('location: '.NIV.'registration.php');
			exit();
		}
	}
	
	/**
	 * Calls the openID login
	 * 
	 * Not implemented in Service_FacebookAutorization
	 */
	public function loginOpenID($s_type){
	}
	
	/**
	 * Logs the user in trough Facebook
	 * 
	 * @param String $s_code	The openID response code
	 * @param String $s_code	The username
	 * @return array	The id and nick if the user is logged in, otherwise null
	 */
	public function loginOpenIDConfirm($s_code,$s_username){		
		$obj_openID	= $this->getOpenID('Facebook');
		
		$this->service_QueryBuilder->select('users','id,nick,lastLogin,userType')->getWhere()->addAnd(array('nick','active','blocked','loginType'),
					array('s','s','s','s'),array($s_username,'1','0','Facebook'));
		$service_Database = $this->service_QueryBuilder->getResult();
		
		if( $service_Database->num_rows() == 0 ){
			$this->service_Logs->loginLog($s_username,'failed',-1,$s_type);
			return null;
		}
		
		$this->service_Logs->loginLog($s_username,'success',-1,'Facebook');
		$a_data	= $service_Database->fetch_assoc();
		return $a_data[0];
	}
}
?>
