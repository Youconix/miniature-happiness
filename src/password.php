<?php

use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\auth\Auth as Auth;
use \includes\BaseLogicClass AS Layout;

/**
 * Password reset page
 * Does not work for openID accounts
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Password extends BaseController {

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
  public function __construct(\Request $request, \Language $language, \Output $template, Layout $layout, Auth $auth, \Config $config) {
    $this->bo_acceptAllInput = true;
    
    parent::__construct($request, $layout, $template);

    $this->language = $language;
    $this->auth = $auth;
    $this->config = $config;    
  }

  public function index() {
    try {
      $guard = $this->auth->getGuard();

      return $this->password_screen($guard->getName());
    } catch (\LogicException $e) {
      throw new \Http404Exception('Password reset not activated.');
    }
  }

  public function password_screen($s_name) {
    try {
      $guard = $this->auth->getGuard($s_name);
    
      if( !$guard->hasReset() ){
	throw new \Http404Exception('Password reset not activated.');
      }
      
      $template = $this->createView('password/form');
      $guard->resetForm($template,$this->post);
      $this->addStylesheet($template);
    
      return $template;
     } catch (\LogicException $e) {
      throw new \Http404Exception('Password reset not activated.');
    }
  }

  /**
   * Verifies the reset code
   */
  public function verifyCode($s_name,$s_hash) {
    try {
      $guard = $this->auth->getGuard($s_name);
    
      if( !$guard->hasReset() ){
	throw new \Http404Exception('Password reset not activated.');
      }
      
      $guard->do_reset($s_hash);
      
      return $this->password_screen($s_name);
     } catch (\LogicException $e) {
      throw new \Http404Exception('Password reset not activated.');
    }
  }

  /**
   * Sends the password reset email
   */
  public function reset($s_name) {
    try {
      $guard = $this->auth->getGuard($s_name);
    
      if( !$guard->hasReset() ){
	throw new \Http404Exception('Password reset not activated.');
      }
      
      $i_status = $guard->sendResetEmail($this->post);
      switch($i_status){
	case \Guard::FORM_INVALID :
	  return $this->password_screen($s_name);
      }    
     } catch (\LogicException $e) {
      throw new \Http404Exception('Password reset not activated.');
    }
    
    $template = $this->createView('password/mail_send',[
	'send_title' => t('forgotPassword/header'),
	'send_text' => t('forgotPassword/resetSuccess')
    ]);
    $this->addStylesheet($template);
    
    return $template;
  }

  private function addStylesheet($template){
    $template->append('head','<link rel="stylesheet" href="/'.$this->config->getSharedStylesDir().'css/registration.css">');
  }
}
