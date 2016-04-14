<?php
use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\models\Login as Login;

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
class Forgot_password extends BaseController
{

    /**
     *
     * @var \Language
     */
    protected $language;

    /**
     *
     * @var \Output
     */
    protected $template;

    /**
     *
     * @var \core\models\Login
     */
    protected $login;

    private $s_notices = '';

    /**
     * Base graphic class constructor
     *
     * @param \Request $request            
     * @param \Language $language            
     * @param \Output $template            
     * @param \core\models\Login $login            
     */
    public function __construct(\Request $request, \Language $language, \Output $template, Login $login)
    {
        $this->login = $login;
        $this->language = $language;
        $this->template = $template;
        
        parent::__construct($request);
    }

    /**
     * Inits the class ForgotPassword
     *
     * @see BaseClass::init()
     */
    protected function init()
    {
        $this->init_get = array(
            'code' => 'string-DB'
        );
        $this->init_post = array(
            'email' => 'string-DB',
            'username' => 'string-DB'
        );
        
        parent::init();
        
        $this->template->set('forgotTitle', t('forgotPassword/header'));
    }

    /**
     * Verifies the reset code
     */
    protected function verifyCode()
    {
        if (! $this->get->validate(array(
            'code' => 'required|type:text'
        ))) {
            $this->headers->redirect('forgot_password/index');
        }
        
        $this->template->loadView('reset.tpl');
        
        if (! $this->login->resetPassword($this->get->get('code'))) {
            $this->template->set('errorNotice', t('forgotPassword/verifyCodeFailed'));
        } else {
            $this->template->set('notice', t('forgotPassword/verifyCodeSuccess'));
        }
    }

    /**
     * Sends the password reset email
     */
    protected function reset()
    {
        if (! $this->post->validate(array(
            'email' => array(
                'required|type:email',
                t('forgotPassword/emailError')
            ),
            'username' => array(
                'required|type:text',
                t('forgotPassword/fieldsEmpty')
            )
        ))) {
            $this->s_notices = implode('<br>', $this->post->getValidateErrors());
            $this->template->loadView('index.tpl');
            $this->index();
            return;
        }
        
        $i_code = $this->login->resetPasswordMail($this->post->get('email'), $this->post->get('username'));
        if ($i_code == - 1) {
            /* Not a normal account */
            $this->s_notices = t('forgotPassword/openID');
            $this->template->loadView('index.tpl');
            $this->index();
            return;
        } else 
            if ($i_code == 0) {
                $this->s_notices = t('forgotPassword/resetFailed');
                $this->template->loadView('index.tpl');
                $this->index();
                return;
            }
        
        $this->template->loadView('reset.tpl');
        
        $this->template->set('notice', $this->language->insert(t('forgotPassword/resetSuccess'), 'email', $this->post['email']));
    }

    /**
     * Displays the reset form
     */
    protected function index()
    {
        $this->template->set('email', t('registration/email'));
        $this->template->set('username', t('registration/name'));
        $this->template->set('errorNotice', $this->s_notices);
        $this->template->set('loginButton', t('system/buttons/reset'));
    }
}