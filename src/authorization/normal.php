<?php
namespace authorization;

class Normal extends \authorization\Authorization {
    /**
     *
     * @var \core\models\Login
     */
    private $login;
    
    /**
     * 
     * @var \core\helpers\PasswordForm
     */
    private $form;
    
    /**
     * 
     * @var \core\helpers\Captcha
     */
    private $captcha;
    
    /**
     * Constructor
     *
     * @param \Request $request
     * @param \Language $language
     * @param \Output $template
     * @param \youconix\core\models\User $user
     * @param \core\models\Login $login
     * @param \core\helpers\PasswordForm $form
     * @param \core\helpers\Captcha $captcha
     */
    public function __construct(\Request $request, \Language $language,\Output $template,\youconix\core\models\User $user,
        \youconix\core\models\Login $login, \youconix\core\helpers\PasswordForm $form,\youconix\core\helpers\Captcha $captcha)
    {
        parent::__construct($request, $language, $template, $user);
        
        $this->login = $login;
        $this->form = $form;
        $this->captcha = $captcha;
    }
    
    /**
     * Inits the class Normal
     */
    protected function init()
    {
        $this->init_get = array(
            'username' => 'string-DB',
            'email' => 'string-DB',
            'code' => 'string',
            'type' => 'string'
        );
        
        $this->init_post = array(
            'nick' => 'string-DB',
            'email' => 'string-DB',
            'password' => 'string-DB',
            'password2' => 'string-DB',
            'captcha' => 'string',
            'type' => 'string',
            'conditions' => 'ignore',
            'autologin' => 'ignore',
            'username' => 'string-DB',
        	'password_old' => 'string-DB',
        	'password' => 'string-DB',
        	'password2' => 'string-DB'
        );
        
        $this->s_current = 'normal';
        
        parent::init();
        
        $this->template->setJavascriptLink('<script src="{NIV}js/authorization/normal.js"></script>');
        $this->template->setCssLink('<link rel="stylesheet" href="{NIV}{shared_style_dir}css/registration.css">');
    }
    
    /**
     * Shows the login screen
     */
    protected function login_screen($bo_callback = false){
        if ($bo_callback) {
            $this->template->loadView('login_screen');
        } else {
            $this->login->checkAutologin();
        }
        
        $this->template->set('usernameText', t('system/admin/users/username'));
        $this->template->set('username', $this->post->getDefault('username'));
        $this->template->set('passwordText', t('system/admin/users/password'));
        $this->template->set('autologin', t('login/autologin'));
        $this->template->set('loginButton', t('login/button'));
        $this->template->set('registration', t('login/registration'));
        
        $this->template->set('loginTitle','Login');
        $this->template->set('forgotPassword','Forgot password');
        
        $this->setLoginTypes();
    }
    
    /**
     * Performs the login
     */
    protected function do_login(){
        if (! $this->post->validate(array(
            'username' => 'required',
            'password' => 'required'
        ))) {
        	$this->login_screen(true);
            return;
        }
        
        (isset($this->post['autologin'])) ? $bo_autologin = true : $bo_autologin = false;
        echo('doing login');
        $bo_login = $this->login->do_login($this->post['username'], $this->post['password'], $bo_autologin);
        
        /* No redirect, so the login was incorrect */
        $this->login_screen(true);
    }
    
    /**
     * Displays the password expires screen
     * Regular login only
     *
     * @param string $s_notice
     *            form notice, optional
     */
    protected function expired($s_notice = '')
    {
    	$this->template->set('expired_title', t('login/editPassword'));
    	$this->template->set('password', t('login/currentPassword'));
    	$this->template->set('newPassword', t('login/newPassword'));
    	$this->template->set('newPassword2', t('login/newPasswordAgain'));
    	$this->template->set('loginButton', t('login/editPassword'));
    
    	if (! empty($s_notice)) {
    		$this->template->set('errorNotice', $s_notice);
    	}
    }
    
    /**
     * Changes the expired password
     */
    protected function update()
    {    	
    	if ( !$this->post->validate(array(
    			'password_old' => 'required',
    			'password' => 'required',
    			'password2' => 'required'
    	)) ){
    		$this->expired(t('registration/addHint/fieldsEmpty'));
    		return;
    	}
    	
    	if ($this->post['password'] != $this->post['password2']) {
    		$this->expired(t('registration/passwordIncorrect'));
    		return;
    	} 
    
    	if (! $this->login->changePassword($this->post['password_old'], $this->post['password'])) {
    		$this->expired(t('login/currentPasswordIncorrect'));
    		return;
    	}
    }
    
    /**
     * Shows the registation screen
     */
    protected function registration_screen(){
        $this->setLoginTypes();
        
        $this->template->set('registration', t('registration/screenTitle'));
        
        if (! empty($this->s_notice)) {
            $this->template->set('errorNotice', $this->s_notice);
        }
        
        $this->template->set('usernameText', t('system/admin/users/username'));
        $this->template->set('username', $this->post->getDefault('username'));
        $this->template->set('emailText', t('system/admin/users/email'));
        $this->template->set('email', $this->post->getDefault('email'));
        
        $this->template->set('passwordForm', $this->form->generate());
                
        $this->template->set('captchaText', t('registration/captcha'));
        $this->template->set('buttonRegister', t('registration/submitButton'));
        $this->template->set('conditionsText', t('registration/conditions'));
        
        $this->template->set('usernameError',t('registration/notices/notices0'));
        $this->template->set('emailError',t('registration/notices/notices1'));
    }
    
    /**
     * Performs the registration
     */
    protected function do_registration(){
        if( !$this->post->validate(array(
            'username' => 'required|minlength:3',
            'email' => 'required|pattern:email',
            'password' => 'required|minlength:8',
            'password2' => 'required|minlength:8',
            'captcha' => 'required',
            'conditions' => 'required'
        )) ) {
            $this->template->loadView('registration_view');
            $this->registration_screen();
            return;
        }
        
        $a_errors[] = array();
        if( $this->post->get('password') != $this->post->get('password2') ){
            $a_errors[] = $this->service_Language->get('language/registration/notices/passwordInvalid');
        }
        if( !$this->checkUsername($this->post->get('username')) ){
            $a_errors[] = $this->service_Language->get('language/registration/notices/usernameTaken');
        }
        if( !$this->checkEmail($this->post->get('email')) ){
            $a_errors[] = $this->service_Language->get('language/registration/notices/emailTaken');
        }
        if (! $this->captcha->checkCaptcha($this->post->get('captcha') )) {
            $a_errors[] = $this->service_Language->get('language/registration/notices/codeInvalid');
        }
        
        if( count($a_errors) > 0 ){
            $this->s_notice = implode('<br/>',$a_errors);
            $this->template->loadView('registration_view');
            $this->registration_screen();
            return;
        }
        
        /* Register user */
        if (! $this->login->register($this->post) ){
            $this->template->set('errorNotice', $this->service_Language->get('language/registration/failed'));
        } else {
            $this->template->set('notice', $this->service_Language->insert($this->service_Language->get('language/registration/emailSend'), 'email', $this->post->get('email') ));
        }
    }
}