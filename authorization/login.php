<?php
/**
 * Log in page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 *
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
 */
if (! defined('NIV')) {
    define('NIV', '../');
}
include (NIV . 'includes/BaseLogicClass.php');

class Login extends \includes\BaseLogicClass
{

    private $model_Login;

    private $model_User;

    /**
     * Inits the class Login
     */
    protected function init()
    {
        $this->init_get = array(
            'type' => 'string',
            'code' => 'string'
        );
        $this->init_post = array(
            'username' => 'string-DB',
            'password_old' => 'string-DB',
            'password' => 'string-DB',
            'password2' => 'string-DB',
            'autologin' => 'ignore'
        );
                
        parent::init();
        
        $this->model_Login = \core\Memory::models('Login');
        $this->model_User = \core\Memory::models('User');
        
        if (! $this->model_Config->isNormalLogin() && $this->model_Config->isLDAPLogin()) {
            \core\Memory::services('Headers')->redirect('authorization/login_ldap/index');
        }
    }

    /**
     * Generates the login form
     */
    protected function index($bo_callback = false)
    {
        if ($bo_callback) {
            $this->service_Template->loadView('index');
        } else {
            $this->model_Login->checkAutologin();
        }
        
        $model_Config = \core\Memory::models('Config');
        
        $this->service_Template->set('usernameText', $this->service_Language->get('system/admin/users/username'));
        $this->service_Template->set('passwordText', $this->service_Language->get('system/admin/users/password'));
        $this->service_Template->set('autologin', $this->service_Language->get('login/autologin'));
        $this->service_Template->set('loginButton', $this->service_Language->get('login/button'));
        $this->service_Template->set('registration', $this->service_Language->get('login/registration'));
        if ($model_Config->isNormalLogin()) {
            $this->service_Template->set('forgotPassword', $this->service_Language->get('login/forgotPassword'));
        }
        
        if ($bo_callback) {
            $this->service_Template->set('username', $this->post['username']);
        }
        
        if ($model_Config->isFacebookLogin()) {
            $this->service_Template->setBlock('login', array(
                'image' => 'facebook',
                'key' => 'facebook',
                'text' => $this->service_Language->get('loginFacebook')
            ));
        }
        if ($model_Config->isOpenIDLogin()) {
            $this->service_Template->setBlock('login', array(
                'image' => 'openID',
                'key' => 'openID',
                'text' => $this->service_Language->get('loginOpenID')
            ));
        }
    }

    /**
     * Checks the login data
     */
    protected function do_login()
    {
        if (trim($this->post['username']) == '' || trim($this->post['password']) == '') {
            $this->index(true);
            return;
        }
        
        (isset($this->post['autologin'])) ? $bo_autologin = true : $bo_autologin = false;
        $bo_login = $this->model_Login->do_login($this->post['username'], $this->post['password'], $bo_autologin);
        
        /* No redirect, so the login was incorrect */
        $this->index(true);
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
        $this->service_Template->set('expired_title', $this->service_Language->get('login/editPassword'));
        $this->service_Template->set('password', $this->service_Language->get('login/currentPassword'));
        $this->service_Template->set('newPassword', $this->service_Language->get('login/newPassword'));
        $this->service_Template->set('newPassword2', $this->service_Language->get('login/newPasswordAgain'));
        $this->service_Template->set('loginButton', $this->service_Language->get('login/editPassword'));
        
        if (! empty($s_notice)) {
            $this->service_Template->set('errorNotice', $s_notice);
        }
    }

    /**
     * Changes the expired password
     * Regular login only
     */
    private function update()
    {
        if (! $this->service_Session->exists('expired')) {
            \core\Memory::services('Headers')->redirect('index/view');
            exit();
        }
        
        $a_data = $this->service_Session->get('expired');
        
        if ($this->post['password_old'] == '' || $this->post['password'] == '' || $this->post['password2'] == '') {
            $this->expiredScreen($this->service_Language->get('registration/addHint/fieldsEmpty'));
            return;
        }
        if ($this->post['password'] != $this->post['password2']) {
            $this->expiredScreen($this->service_Language->get('registration/passwordIncorrect'));
            return;
        }
        
        if (! $this->model_User->changePassword($a_data['id'], $a_data['nick'], $this->post['password_old'], $this->post['password'])) {
            $this->expiredScreen($this->service_Language->get('login/currentPasswordIncorrect'));
            return;
        }
        
        $this->service_Session->delete('expired');
        $this->model_Login->setLogin($a_data);
    }
}