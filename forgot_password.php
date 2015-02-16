<?php
/**
 * Password reset page
 * Does not work for openID accounts                                              
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 *
 *                                                                              
 * Scripthulp framework is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or            
 * (at your option) any later version.                                          
 *                                                                              
 * Scripthulp framework is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                
 * GNU General Public License for more details.                                 
 *                                                                              
 * You should have received a copy of the GNU Lesser General Public License     
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
define('NIV', './');
require (NIV . 'core/BaseLogicClass.php');

class ForgotPassword extends \core\BaseLogicClass
{

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if (isset($this->get['code'])) {
            $this->verifyCode();
        } else 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->sendEmail();
            } else {
                $this->form();
            }
        
        $this->header();
        
        $this->footer();
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
        
        $this->forceSSL();
        
        parent::init();
        
        $this->service_Template->set('forgotTitle', $this->service_Language->get('language/forgotPassword/header'));
    }

    /**
     * Verifies the reset code
     */
    private function verifyCode()
    {
        $this->service_Template->loadView('reset.tpl');
        
        if (! Memory::models('User')->resetPassword($this->get['code'])) {
            $this->service_Template->set('errorNotice', $this->service_Language->get('language/forgotPassword/verifyCodeFailed'));
        } else {
            $this->service_Template->set('notice', $this->service_Language->get('language/forgotPassword/verifyCodeSuccess'));
        }
    }

    /**
     * Sends the password reset email
     */
    private function sendEmail()
    {
        if ($this->post['email'] == '') {
            $this->service_Template->loadView('index.tpl');
            $this->form($this->service_Language->get('language/forgotPassword/fieldsEmpty'));
            return;
        }
        if (! $this->service_Security->checkEmail($this->post['email'])) {
            $this->service_Template->loadView('index.tpl');
            $this->form($this->service_Language->get('language/forgotPassword/emailError'));
            return;
        }
        
        $i_code = Memory::models('User')->resetPasswordMail($this->post['email']);
        if ($i_code == - 1) {
            /* Not a normal account */
            $this->service_Template->loadView('index.tpl');
            $this->form($this->service_Language->get('language/forgotPassword/openID'));
            return;
        } else 
            if ($i_code == 0) {
                $this->service_Template->loadView('index.tpl');
                $this->form($this->service_Language->get('language/forgotPassword/resetFailed'));
                return;
            }
        
        $this->service_Template->loadView('reset.tpl');
        
        $this->service_Template->set('notice', $this->service_Language->insert($this->service_Language->get('language/forgotPassword/resetSuccess'), 'email', $this->post['email']));
    }

    /**
     * Displays the reset form
     *
     * @param string $s_notice
     *            notice, optional
     */
    private function form($s_notice = '')
    {
        $this->service_Template->set('email', $this->service_Language->get('language/registration/email'));
        $this->service_Template->set('errorNotice', $s_notice);
        $this->service_Template->set('loginButton', $this->service_Language->get('language/buttons/reset'));
    }
}

$obj_ForgotPassword = new ForgotPassword();
unset($obj_ForgotPassword);
?>