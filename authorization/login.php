<?php
namespace Authorization;

/**
 * Log in page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 *       
 *        Miniature-happiness is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Miniature-happiness is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 */
if (! defined('NIV')) {
    define('NIV', '../');
    include (NIV . 'includes/BaseLogicClass.php');
}
define('FORCE_SSL', true);

class Login extends \includes\BaseLogicClass
{

    /**
     *
     * @var \core\models\Login
     */
    private $login;

    /**
     *
     * @var \core\models\User
     */
    private $user;

    /**
     *
     * @var \core\services\Headers
     */
    private $headers;

    /**
     *
     * @var \core\services\Session
     */
    private $session;

    /**
     * Base graphic class constructor
     *
     * @param \core\Input $input
     *            The input parser
     * @param \core\models\Config $config            
     * @param \core\services\Language $language            
     * @param \core\services\Template $template            
     * @param \core\classes\Header $header            
     * @param \core\classes\Menu $menu            
     * @param \core\classes\Footer $footer            
     * @param \core\models\Login $login            
     * @param \core\models\User $user            
     * @param \core\services\Headers $headers            
     * @param \core\services\Session $session            
     */
    public function __construct(\core\Input $input, \core\models\Config $config, \core\services\Language $language, \core\services\Template $template, \core\classes\Header $header, \core\classes\Menu $menu, \core\classes\Footer $footer, \core\models\Login $login, \core\models\User $user, \core\services\Headers $headers, \core\services\Session $session)
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
        
        $this->login = $login;
        $this->user = $user;
        $this->headers = $headers;
        $this->session = $session;
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
        
        if (! $this->config->isNormalLogin() && $this->config->isLDAPLogin()) {
            $this->headers->redirect('authorization/login_ldap/index');
        }
    }

    /**
     * Generates the login form
     */
    protected function index($bo_callback = false)
    {
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
        if ($this->config->isNormalLogin()) {
            $this->template->set('forgotPassword', t('login/forgotPassword'));
        }
        
        if ($bo_callback) {
            $this->template->set('username', $this->post['username']);
        }
        
        if ($this->config->isFacebookLogin()) {
            $this->template->setBlock('specialLogin', array(
                'image' => 'facebook',
                'key' => 'facebook',
                'text' => t('loginFacebook')
            ));
        }
        if ($this->config->isOpenIDLogin()) {
            $this->template->setBlock('specialLogin', array(
                'image' => 'openID',
                'key' => 'openID',
                'text' => t('loginOpenID')
            ));
        }
    }

    /**
     * Checks the login data
     */
    protected function do_login()
    {
        if (! $this->post->validate(array(
            'username' => array(
                'required' => 1
            ),
            'password' => array(
                'required' => 1
            )
        ))) {
            $this->index(true);
            return;
        }
        
        (isset($this->post['autologin'])) ? $bo_autologin = true : $bo_autologin = false;
        $bo_login = $this->login->do_login($this->post['username'], $this->post['password'], $bo_autologin);
        
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
     * Regular login only
     */
    private function update()
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
}
