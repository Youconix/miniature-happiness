<?php
/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *     
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *     
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 * 
 * Password reset page
 * Does not work for openID accounts                                              
 *                                                                              
 * This file is part of Miniature-happiness                                    
 *                                                                              
 * @copyright Youconix                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0
 */
if (! class_exists('\includes\BaseLogicClass')) {
    require (NIV . 'includes/BaseLogicClass.php');
}

class Forgot_password extends \includes\BaseLogicClass
{

    /**
     *
     * @var \core\models\Login
     */
    protected $login;

    /**
     *
     * @var \core\services\Headers
     */
    protected $headers;

    /**
     *
     * @var \Validation
     */
    protected $validation;

    /**
     * Base graphic class constructor
     *
     * @param \Input $input            
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Header $header            
     * @param \Menu $menu            
     * @param \Footer $footer            
     * @param \core\models\Login $login            
     * @param \core\services\Headers $headers            
     * @param \Validation $validation            
     */
    public function __construct(\Input $input, \Config $config, \Language $language, \Output $template, \Header $header, \Menu $menu, \Footer $footer, \core\models\Login $login, \core\services\Headers $headers, \Validation $validation)
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
        
        $this->login = $login;
        $this->headers = $headers;
        $this->validation = $validation;
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
            'email' => 'string-DB'
        );
        
        parent::init();
        
        $this->template->set('forgotTitle', t('forgotPassword/header'));
    }

    /**
     * Verifies the reset code
     */
    protected function verifyCode()
    {
        if (! isset($this->get['code'])) {
            $this->headers->redirect('forgot_password/index');
        }
        
        $this->template->loadView('reset.tpl');
        
        if (! $this->login->resetPassword($this->get['code'])) {
            $this->template->set('errorNotice', t('forgotPassword/verifyCodeFailed'));
        } else {
            $this->template->set('notice', t('forgotPassword/verifyCodeSuccess'));
        }
    }

    /**
     * Sends the password reset email
     */
    protected function sendEmail()
    {
        if ($this->post['email'] == '') {
            $this->template->loadView('index.tpl');
            $this->form(t('forgotPassword/fieldsEmpty'));
            return;
        }
        if (! $this->validation->checkEmail($this->post['email'])) {
            $this->template->loadView('index.tpl');
            $this->form(t('forgotPassword/emailError'));
            return;
        }
        
        $i_code = $this->login->resetPasswordMail($this->post['email']);
        if ($i_code == - 1) {
            /* Not a normal account */
            $this->template->loadView('index.tpl');
            $this->form(t('forgotPassword/openID'));
            return;
        } else 
            if ($i_code == 0) {
                $this->template->loadView('index.tpl');
                $this->form(t('forgotPassword/resetFailed'));
                return;
            }
        
        $this->template->loadView('reset.tpl');
        
        $this->template->set('notice', $this->language->insert(t('forgotPassword/resetSuccess'), 'email', $this->post['email']));
    }

    /**
     * Displays the reset form
     *
     * @param string $s_notice
     *            notice, optional
     */
    protected function index($s_notice = '')
    {
        $this->template->set('email', t('registration/email'));
        $this->template->set('errorNotice', $s_notice);
        $this->template->set('loginButton', t('system/buttons/reset'));
    }
}