<?php

use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\auth\Auth as Auth;
use \includes\BaseLogicClass AS Layout;

/**
 * General landing page.
 *
 * @author : Rachelle Scheijen
 * @copyright Youconix
 * @version 1.0
 * @since 1.0
 */
class Login extends BaseController {

  /**
   *
   * @var \Language
   */
  protected $language;

  /**
   *
   * @var \youconix\core\services\Auth
   */
  protected $auth;
  
  /**
   *
   * @var \Config
   */
  protected $config;

  /**
   * Constructor
   *
   * @param \Request $request
   * @param \Language $language            
   * @param \Output $template            
   * @param \core\services\Auth $auth
   */
  public function __construct(\Request $request, \Language $language, \Output $template, Layout $layout, Auth $auth,\Config $config) {
    $this->bo_acceptAllInput = true;
    
    parent::__construct($request, $layout, $template);

    $this->language = $language;
    $this->auth = $auth;
    $this->config = $config;
  }

  /**
   * Inits the class
   */
  protected function init(){
    $this->init_post = [
	'username' => 'string',
	'password' => 'string',
	'password_old' => 'string',
	'password2' => 'string'
    ];
    
    parent::init();
  }

  /**
   * Shows the login screen
   *
   * @param string $s_name
   * @return \Output
   * @throws \Http404Exception
   */
  public function login_screen($s_name){
    if( $s_name == 'index' ){
      $s_name = '';
    }
    
    try {
      $guard = $this->auth->getGuard($s_name);

      $template = $this->createView('login/form');
      $this->addHead($template);
      $guard->loginForm($template,$this->post);
      $this->addExtraLogin($template,$guard);
      
      return $template;
    } catch (\LogicException $e) {
      throw new \Http404Exception('Login not activated.');
    }
  }

  /**
   * Adds an extra guard
   *
   * @param \Ouput $output
   * @param \Guard $current_guard
   */
  private function addExtraLogin($output,$current_guard){
    $a_allGuards = $this->auth->getGuards();
    $a_guards = [];
    foreach($a_allGuards AS $guard){
      if( $guard->getName() != $current_guard->getName() ){
	$a_guards[] = $guard;
      }
    }
    
    $output->set('guards',$a_guards);
  }

  /**
   * Logs the user in
   *
   * @param string $type
   * @return \Output
   * @throws \Http404Exception
   */
  public function do_login($type){
    try {
      $guard = $this->auth->getGuard($type);

      $status = $guard->do_login($this->post);
      echo($status);
      switch($status){
	case $guard::FORM_INVALID :
	case $guard::INVALID_LOGIN :
	  return $this->login_screen($type);
	  
	case $guard::LOGIN_EXPIRED :
	  return $this->expired($guard);
      }
      
    } catch (\LogicException $e) {
      throw new \Http404Exception('Invalid guard.');
    }
  }

  /**
   * Updates the password
   *
   * @param type $type
   * @return \Output
   * @throws \Http404Exception
   */
  public function update_password($type){
    try {
      $guard = $this->auth->getGuard($type);

      $guard->do_login($this->post);
      
      return $this->expired($guard);
      
    } catch (\LogicException $e) {
      throw new \Http404Exception('Invalid guard.');
    }
  }

  /**
   * Logs the user out
   */
  public function logout(){
    $this->auth->logout();
  }

  /**
   * Shows the password expired screen
   *
   * @param \Guard $guard
   * @return \Output
   */
  private function expired($guard){
    $template = $this->createView('login/expired');
    $this->addHead($template);
    $guard->expiredForm($template);
    return $template;
  }
  
  private function addHead($template){
    $template->append('head','<link rel="stylesheet" href="/'.$this->config->getSharedStylesDir().'css/registration.css">');    
  }
}
