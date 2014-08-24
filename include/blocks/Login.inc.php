<?php

namespace core\blocks;

class Login extends Block{

  private $service_Authorization;
  private $service_Session;
  private $service_Settings;
  private $s_url;

  public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template, 
    \core\services\Authorization $service_Authorisation,\core\services\Session $service_Session,\core\services\Settings $service_Settings){
    parent::__construct($service_Language, $service_Template);
    $this->service_Authorization = $service_Authorisation;
    $this->service_Session  = $service_Session;
    $this->service_Settings  = $service_Settings;
    
    $this->s_url = \core\Memory::parseUrl('login');
  }

  public function form(){
    $this->service_Template->set('username',$this->service_Language->get('system/login/username'));
		$this->service_Template->set('password',$this->service_Language->get('system/login/password'));
		$this->service_Template->set('loginButton',$this->service_Language->get('system/login/button'));
		$this->service_Template->set('registration',$this->service_Language->get('system/login/registration'));
		$this->service_Template->set('forgotPassword',$this->service_Language->get('system/login/forgotPassword'));
		$this->service_Template->set('autologin',$this->service_Language->get('system/login/autologin'));
	
    $this->extraLogins();		
  }
  
  private function extraLogins(){
    return;
    $a_openID	= $this->service_Authorization->getOpenIDList();		
		$s_login	= $this->service_Language->get('language/login/loginWith');
		foreach($a_openID AS $s_openID){
			$this->service_Template->setBlock('openID',array('key'=>$s_openID,'text'=>$s_login.' '.$s_openID));
		}
  }

  public function expired($s_notice = ''){
    if( !$this->service_Session->exists('expired') ){      
			header('location: '.$this->s_url);
			exit();
		}
		
		$this->service_Template->loadView('expired.tpl');
		
		$this->service_Template->set('errorNotice',$s_notice);
		$this->service_Template->set('expired_title',$this->service_Language->get('system/login/editPassword'));
		$this->service_Template->set('password',$this->service_Language->get('system/login/currentPassword'));
		$this->service_Template->set('newPassword',$this->service_Language->get('system/login/newPassword'));
		$this->service_Template->set('newPassword2',$this->service_Language->get('system/login/newPasswordAgain'));
		$this->service_Template->set('loginButton',$this->service_Language->get('system/buttons/edit'));
  }
}
?>