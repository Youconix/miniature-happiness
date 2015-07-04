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
     * @param \core\services\Headers $headers
     * @param \core\models\Login $login
     * @param \core\helpers\PasswordForm $form
     * @param \core\helpers\Captcha $captcha
     */
    public function __construct(\Input $input,\Config $config,
        \Language $language,\Output $template,\Header $header, \Menu $menu, \Footer $footer,\core\models\User $user,\core\services\Headers $headers,\core\models\Login $login,
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
            'conditions' => 'ignore'
        );
        
        $this->s_current = 'normal';
        
        parent::init();
        
        $this->template->setJavascriptLink('<script src="{NIV}js/registration.js"></script>');
        $this->template->setCssLink('<link rel="stylesheet" href="{NIV}{shared_style_dir}css/registration.css">');
    }
    
    /**
     * Shows the login screen
     */
    protected function login_screen(){
        
    }
    
    /**
     * Performs the login
     */
    protected function do_login(){
        
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