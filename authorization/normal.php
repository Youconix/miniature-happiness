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
     * @param \Input $input    The input parser
     * @param \onfig $config
     * @param \Language $language
     * @param \Output $template
     * @param \Header $header
     * @param \Menu $menu
     * @param \Footer $footer
     * @param \core\models\User $user
     * @param \Headers $headers
     * @param \core\models\Login $login
     * @param \core\helpers\PasswordForm $form
     * @param \core\helpers\Captcha $captcha
     */
    public function __construct(\Input $input,\Config $config,
        \Language $language,\Output $template,\Header $header, \Menu $menu, \Footer $footer,\core\models\User $user,\Headers $headers,\core\models\Login $login,
        \core\helpers\PasswordForm $form,\core\helpers\Captcha $captcha)
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer, $user, $headers);
        
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
            'username' => 'string-DB'
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
            $this->template->loadView('index');
        } else {
            $this->login->checkAutologin();
        }
        
        $this->template->set('usernameText', t('system/admin/users/username'));
        $this->template->set('passwordText', t('system/admin/users/password'));
        $this->template->set('autologin', t('login/autologin'));
        $this->template->set('loginButton', t('login/button'));
        $this->template->set('registration', t('login/registration'));
        
        $this->template->set('loginTitle','Login');
        $this->template->set('forgotPassword','Forgot password');
        
        if ($bo_callback) {
            $this->template->set('username', $this->post['username']);
        }
        
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
    	if (! $this->session->exists('expired')) {
    		$this->headers->redirect('index/view');
    	}
    
    	$a_data = $this->session->get('expired');
    
    	if ($this->post['password_old'] == '' || $this->post['password'] == '' || $this->post['password2'] == '') {
    		$this->expiredScreen(t('registration/addHint/fieldsEmpty'));
    		return;
    	}
    	if ($this->post['password'] != $this->post['password2']) {
    		$this->expiredScreen(t('registration/passwordIncorrect'));
    		return;
    	}
    
    	$user = $this->user->createUser();
    	$user->setData($a_data);
    
    	if (! $user->changePassword($this->post['password_old'], $this->post['password'])) {
    		$this->expiredScreen(t('login/currentPasswordIncorrect'));
    		return;
    	}
    
    	$this->session->delete('expired');
    	$this->login->setLogin($user);
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