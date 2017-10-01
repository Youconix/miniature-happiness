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
class Registration extends BaseController {

  /**
   *
   * @var \Language
   */
  protected $language;

  /**
   *
   * @var \core\services\Auth
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
   * Shows the registration screen
   *
   * @param string $s_name
   * @param string $s_error
   * @return \Output
   * @throws \Http404Exception
   */
  public function registration_screen($s_name,$s_error = ''){
     if( $s_name == 'index' ){
       $s_name = '';
     }
    
     try {
      $guard = $this->auth->getGuard($s_name);      
      $this->checkRegistration($guard);
      
      $template = $this->createView('registration/form');
      $guard->registrationForm($template,$this->post);
      $this->addStylesheet($template);
    
      return $template;
     } catch (\LogicException $e) {
      throw new \Http404Exception('Registration not activated.');
    }
  }

  /**
   * Performs the registration
   *
   * @param string $s_name
   * @return type
   * @throws \Http404Exception
   * @todo move error text to language file
   */
  public function do_registration($s_name){
    try {
      $guard = $this->auth->getGuard($s_name);
      $this->checkRegistration($guard);
      
      $i_status = $guard->do_registration($this->post);
      
      switch($i_status){
	case $guard::FORM_INVALID :
	  $s_error = 'Not all the fields are filled in correctly.';
	  break;
	case $guard::USERNAME_TAKEN :
	  $s_error = 'The username is allready taken.';
	  break;
	case $guard::EMAIL_TAKEN :
	  $s_error = 'This email is allready registered.';
	  break;
      }
      
      return $this->registration_screen($s_name,$s_error);
     } catch (\LogicException $e) {
      throw new \Http404Exception('Registration not activated.');
    }
  }

  /**
   * Checks the username or email
   *
   * @param string $s_name
   * @param string $s_field
   * @throws \Http404Exception
   */
  public function check($s_name,$s_field){
    try {
      $i_status = 0;
      
      $guard = $this->auth->getGuard($s_name);
      $this->checkRegistration($guard);
    
      if( $s_field == 'username' && $guard->usernameAvailable($this->post->getDefault('username')) ){
	$i_status = 1;
      }
      else if( $s_field == 'email' && $guard->emailAvailable($this->post->getDefault('email')) ){
	$i_status = 1;
      }
      
      echo($i_status);
    } catch (\LogicException $e) {
      throw new \Http404Exception('Registration not activated.');
    }
  }

  /**
   * Activates the account
   *
   * @param string $s_name
   * @param string $s_username
   * @param string $s_activationCode
   * @return \Output
   * @todo  implement function
   */
  public function activate_account($s_name,$s_username,$s_activationCode){
    
  }

  /**
   * Checks if registration is activated for the guard
   *
   * @param \Guard $guard
   * @throws \Http404Exception if registration is not activated
   */
  private function checkRegistration($guard){
    if( !$guard->hasRegistration() ){
	throw new \Http404Exception('Registration not activated.');
    }
  }

  /**
   * Adds the stylesheet
   *
   * @param \Output $template
   */
  private function addStylesheet($template){
    $template->append('head','<link rel="stylesheet" href="/'.$this->config->getSharedStylesDir().'css/registration.css">');
  }
}