<?php
/**
 * Log in page                              
 *                                                                              
 * This file is part of Miniature-happiness                                    
 *                                                                              
 * @copyright Youconix                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                               
 *                                                                              
 * Miniature-happiness is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or            
 * (at your option) any later version.                                          
 *                                                                              
 * Miniature-happiness is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                
 * GNU General Public License for more details.                                 
 *                                                                              
 * You should have received a copy of the GNU Lesser General Public License     
 * along with Miniature-happiness.  If not, see <http://www.gnu.org/licenses/>.
 */
if( !defined('NIV') ){
    define('NIV', './');
}
define('FORCE_SSL',true);
include (NIV . 'core/BaseLogicClass.php');

class Login extends \core\BaseLogicClass
{

    private $service_Authorization;

    private $model_User;

    /**
     * PHP5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            if (isset($this->get['command']) && $this->get['command'] == 'openID') {
                if (isset($this->get['type'])) {
                    $this->openIDScreen();
                } else {
                    $this->openIDLogin();
                }
            } else {
                $this->checkAutologin();
                
                $this->form();
            }
        } else 
            if (isset($this->post['command']) && $this->post['command'] == 'expired') {
                $this->expired();
            } else {
                $this->login();
            }
        
        $this->header();
        
        $this->menu();
        
        $this->footer();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->service_Authorization = null;
        $this->model_User = null;
        
        parent::__destruct();
    }

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
        
        $this->service_Authorization = Memory::services("Authorization");
        $this->model_User = Memory::models('User');
    }

    /**
     * Checks if auto login is enabled
     */
    private function checkAutologin()
    {
        $service_Cookie = Memory::services('Cookie');
        if (! $service_Cookie->exists('autologin')) {
            return;
        }
        
        $s_fingerprint = Memory::services('Session')->getFingerprint();
        $a_data = explode(';', $service_Cookie->get('autologin'));
        
        if ($a_data[0] != $s_fingerprint) {
            $service_Cookie->delete('autologin', '/');
            return;
        }
        
        /* Check auto login */
        $a_login = $this->service_Authorization->performAutoLogin($a_data[1]);
        if (is_null($a_login)) {
            $service_Cookie->delete('autologin', '/');
            return;
        }
        
        $service_Cookie->set('autologin', implode(';', $a_data), '/');
        $this->setLogin($a_login);
    }

    /**
     * Generates the login form
     */
    private function form()
    {
        $this->service_Template->set('username', $this->service_Language->get('language/login/username'));
        $this->service_Template->set('password', $this->service_Language->get('language/login/password'));
        $this->service_Template->set('loginButton', $this->service_Language->get('language/login/button'));
        $this->service_Template->set('registration', $this->service_Language->get('language/login/registration'));
        $this->service_Template->set('forgotPassword', $this->service_Language->get('language/login/forgotPassword'));
        $this->service_Template->set('autologin', $this->service_Language->get('language/login/autologin'));
        
        $a_openID = $this->service_Authorization->getOpenIDList();
        $s_login = $this->service_Language->get('language/login/loginWith');
        foreach ($a_openID as $s_openID) {
            $this->service_Template->setBlock('openID', array(
                'key' => $s_openID,
                'text' => $s_login . ' ' . $s_openID
            ));
        }
    }

    /**
     * Checks the login data
     */
    private function login()
    {
        if (trim($this->post['username']) == '' || trim($this->post['password']) == '') {
            $this->form();
            return;
        }
        
        (isset($this->post['autologin'])) ? $bo_autologin = true : $bo_autologin = false;
        $a_login = $this->service_Authorization->login($this->post['username'], $this->post['password'], $bo_autologin);
        
        if (is_null($a_login)) {
            $this->form();
            return;
        }
        
        $service_Cookie = Memory::services('Cookie');
        if (isset($this->post['autologin'])) {
            $s_fingerprint = Memory::services('Session')->getFingerprint();
            $service_Cookie->set('autologin', $s_fingerprint . ';' . $a_login['autologin'], '/');
        }
        
        /* Check for expire */
        if ($a_login['password_expired'] == '1') {
            $this->service_Session->set('expired', $a_login);
            $this->expiredScreen();
            return;
        }
        
        $this->setLogin($a_login);
    }

    /**
     * Displays the password expires screen
     * Regular login only
     *
     * @param string $s_notice
     *            form notice, optional
     */
    private function expiredScreen($s_notice = '')
    {
        if (! $this->service_Session->exists('expired')) {
            header('location: login.php');
            exit();
        }
        
        $this->service_Template->loadView('expired.tpl');
        
        $this->service_Template->set('errorNotice', $s_notice);
        $this->service_Template->set('expired_title', $this->service_Language->get('language/login/editPassword'));
        $this->service_Template->set('password', $this->service_Language->get('language/login/currentPassword'));
        $this->service_Template->set('newPassword', $this->service_Language->get('language/login/newPassword'));
        $this->service_Template->set('newPassword2', $this->service_Language->get('language/login/newPasswordAgain'));
        $this->service_Template->set('loginButton', $this->service_Language->get('language/buttons/edit'));
    }

    /**
     * Changes the expired password
     * Regular login only
     */
    private function expired()
    {
        if (! $this->service_Session->exists('expired')) {
            header('location: login.php');
            exit();
        }
        
        $a_data = $this->service_Session->get('expired');
        
        if ($this->post['password_old'] == '' || $this->post['password'] == '' || $this->post['password2'] == '') {
            $this->expiredScreen($this->service_Language->get('language/registration/addHint/fieldsEmpty'));
            return;
        }
        if ($this->post['password'] != $this->post['password2']) {
            $this->expiredScreen($this->service_Language->get('language/registration/passwordIncorrect'));
            return;
        }
        
        if (! $this->model_User->changePassword($a_data['id'], $a_data['nick'], $this->post['password_old'], $this->post['password'])) {
            $this->expiredScreen($this->service_Language->get('language/login/currentPasswordIncorrect'));
            return;
        }
        
        $this->service_Session->delete('expired');
        $this->setLogin($a_data);
    }

    /**
     * Sets the login session
     *
     * @param array $a_data
     *            login data
     */
    private function setLogin($a_data)
    {
        $s_page = $this->getRedirection($a_data);
        
        $this->service_Session->setLogin($a_data['id'], $a_data['nick'], $a_data['lastLogin']);
        
        header('location: ' . $s_page);
        exit();
    }

    /**
     * Returns the redirection page
     *
     * @param array $a_data
     *            login data
     * @return string redirection page
     */
    private function getRedirection($a_data)
    {
        if ($this->service_Session->exists('page')) {
            if ($this->service_Session->get('page') != 'logout.php')
                return NIV . $this->service_Session->get('page');
            
            $this->service_Session->delete('page');
        }
        
        return NIV . 'index.php';
    }

    /**
     * Displays the openID screen
     */
    private function openIDScreen()
    {
        if ($this->get['type'] == 'Facebook') {
            Memory::helpers('Facebook')->loginScreen();
            return;
        }
        
        try {
            $this->service_Authorization->loginOpenID($this->get['type']);
        } catch (Exception $e) {
            Memory::services('Logs')->securityLog("Invalid type " . $this->get['type'] . ' on login.php:openID.');
            header('location: ' . NIV . 'index.php');
            exit();
        }
    }

    /**
     * Logs the user in with openID
     */
    private function openIDLogin()
    {
        $a_data = $this->service_Authorization->loginOpenIDConfirm($this->get['code']);
        if (is_null($a_data)) {
            header('location: login.php');
            exit();
        }
        
        $this->setLogin($a_data);
    }
}

$obj_Login = new Login();
unset($obj_Login);