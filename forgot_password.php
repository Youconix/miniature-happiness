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
require (NIV . 'includes/BaseLogicClass.php');

class Forgot_password extends \includes\BaseLogicClass
{

    /**
     *
     * @var \core\models\Login
     */
    protected $model_Login;

    /**
     *
     * @var \core\services\Headers
     */
    protected $service_Headers;

    /**
     *
     * @var \core\services\Validation
     */
    protected $service_Validation;

    /**
     * Base graphic class constructor
     *
     * @param \core\services\Security $service_Security            
     * @param \core\models\Config $model_Config            
     * @param \core\services\Language $service_Language            
     * @param \core\services\Template $service_Template            
     * @param \core\classes\Header $header            
     * @param \core\classes\Menu $menu            
     * @param \core\classes\Footer $footer            
     * @param \core\models\Login $model_Login            
     * @param \core\services\Headers $service_Headers            
     * @param \core\services\Validation $service_Validation            
     */
    public function __construct(\core\services\Security $service_Security, \core\models\Config $model_Config, \core\services\Language $service_Language, \core\services\Template $service_Template, \core\classes\Header $header, \core\classes\Menu $menu, \core\classes\Footer $footer, \core\models\Login $model_Login, \core\services\Headers $service_Headers, \core\services\Validation $service_Validation)
    {
        parent::__construct($service_Security, $model_Config, $service_Language, $service_Template, $header, $menu, $footer);
        
        $this->model_Login = $model_Login;
        $this->service_Headers = $service_Headers;
        $this->service_Validation = $service_Validation;
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
        
        $this->service_Template->set('forgotTitle', t('forgotPassword/header'));
    }

    /**
     * Verifies the reset code
     */
    protected function verifyCode()
    {
        if (! isset($this->get['code'])) {
            $this->service_Headers->redirect('forgot_password/index');
        }
        
        $this->service_Template->loadView('reset.tpl');
        
        if (! $this->model_Login->resetPassword($this->get['code'])) {
            $this->service_Template->set('errorNotice', t('forgotPassword/verifyCodeFailed'));
        } else {
            $this->service_Template->set('notice', t('forgotPassword/verifyCodeSuccess'));
        }
    }

    /**
     * Sends the password reset email
     */
    protected function sendEmail()
    {
        if ($this->post['email'] == '') {
            $this->service_Template->loadView('index.tpl');
            $this->form(t('forgotPassword/fieldsEmpty'));
            return;
        }
        if (! $this->service_Validation->checkEmail($this->post['email'])) {
            $this->service_Template->loadView('index.tpl');
            $this->form(t('forgotPassword/emailError'));
            return;
        }
        
        $i_code = $this->model_Login->resetPasswordMail($this->post['email']);
        if ($i_code == - 1) {
            /* Not a normal account */
            $this->service_Template->loadView('index.tpl');
            $this->form(t('forgotPassword/openID'));
            return;
        } else 
            if ($i_code == 0) {
                $this->service_Template->loadView('index.tpl');
                $this->form(t('forgotPassword/resetFailed'));
                return;
            }
        
        $this->service_Template->loadView('reset.tpl');
        
        $this->service_Template->set('notice', $this->service_Language->insert(t('forgotPassword/resetSuccess'), 'email', $this->post['email']));
    }

    /**
     * Displays the reset form
     *
     * @param string $s_notice
     *            notice, optional
     */
    protected function index($s_notice = '')
    {
        $this->service_Template->set('email', t('registration/email'));
        $this->service_Template->set('errorNotice', $s_notice);
        $this->service_Template->set('loginButton', t('system/buttons/reset'));
    }
}