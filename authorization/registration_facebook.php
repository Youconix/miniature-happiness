<?php
/**
 * Registration page
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
define('NIV', '../');
include (NIV . 'includes/BaseLogicClass.php');

class Registration extends \core\BaseLogicClass
{

    private $service_Authorization;

    private $a_data;

    private $s_notice;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if ($this->model_Config->isAjax()) {
            if ($this->get['command'] == 'checkUsername') {
                if (! $this->checkUsername($this->get['username'])) {
                    $this->service_Template->set('result', '0');
                } else {
                    $this->service_Template->set('result', '1');
                }
            } else 
                if ($this->get['command'] == 'checkEmail') {
                    if (! $this->checkEmail($this->get['email'])) {
                        $this->service_Template->set('result', '0');
                    } else {
                        $this->service_Template->set('result', '1');
                    }
                }
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($this->post['type'])) {
                $this->processOpenID();
            } else {
                $this->process();
            }
        } else 
            if (isset($this->get['command']) && $this->get['command'] == 'openID') {
                if (isset($this->get['type'])) {
                    $this->openIDForm();
                } else {
                    $this->openIDRegistration();
                }
            } else {
                $this->form();
            }
        
        $this->header();
        
        $this->menu();
        
        $this->footer();
    }

    /**
     * Inits the class Registration
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
        
        parent::init();
        
        $this->a_data = array(
            'nick' => '',
            'email' => '',
            'password' => '',
            'password2' => '',
            'bot' => '0',
            'activated' => '0'
        );
        $this->s_notice = '';
        
        $this->service_Authorization = \core\Memory::services('Authorization');
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->service_Authorization = null;
        $this->a_types = null;
        $this->a_data = null;
        $this->s_notice = null;
        
        parent::__destruct();
    }

    /**
     * Displays the openID registration form
     */
    private function openIDForm()
    {
        if ($this->get['type'] == 'Facebook') {
            \core\Memory::helpers('Facebook')->registrationScreen();
        }
        
        $this->service_Template->set('registration', $this->service_Language->get('language/registration/registrationVia') . ' ' . $this->get['type']);
        
        if (! empty($this->s_notice)) {
            $this->service_Template->set('errorNotice', $this->s_notice);
        }
        
        $this->registerText();
    }

    /**
     * Displays the registration form
     */
    private function form()
    {
        $this->service_Template->set('registration', $this->service_Language->get('language/registration/screenTitle'));
        
        if (! empty($this->s_notice)) {
            $this->service_Template->set('errorNotice', $this->s_notice);
        }
        
        $this->service_Template->set('nickText', $this->service_Language->get('language/admin/users/username'));
        $this->service_Template->set('nick', $this->a_data['nick']);
        $this->service_Template->set('emailText', $this->service_Language->get('language/admin/users/email'));
        $this->service_Template->set('email', $this->a_data['email']);
        
        $this->service_Template->set('passwordForm', \core\Memory::helpers('PasswordForm')->generate());
        
        $a_openID = $this->service_Authorization->getOpenIDList();
        $s_registration = $this->service_Language->get('language/registration/registrationVia');
        
        foreach ($a_openID as $s_openID) {
            $s_key = $s_openID;
            
            $this->service_Template->setBlock('openID', array(
                'key' => $s_key,
                'image' => strtolower($s_openID),
                'text' => $s_registration . ' ' . $s_openID
            ));
        }
        
        $this->registerText();
    }

    /**
     * Displays the register text
     */
    private function registerText()
    {
        $this->service_Template->set('captchaText', $this->service_Language->get('language/registration/captcha'));
        $this->service_Template->set('buttonRegister', $this->service_Language->get('language/registration/submitButton'));
        $this->service_Template->set('conditionsText', $this->service_Language->get('language/registration/conditions'));
    }

    /**
     * Starts the openID registration
     */
    private function processOpenID()
    {
        $bo_error = false;
        
        $helper_Captcha = \core\Memory::helpers('Captcha');
        if (! $helper_Captcha->checkCaptcha($this->post['captcha'])) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/codeInvalid') . '<br/>';
            $bo_error = true;
        }
        
        if ($bo_error) {
            $this->openIDForm();
            return;
        }
        
        try {
            if ($this->post['type'] != 'Facebook')
                $this->service_Authorization->registerOpenID($this->post);
            else {
                $this->service_Authorization = \core\Memory::services('FacebookAuthorization');
                $this->service_Authorization->registerOpenIDConfirm($this->post);
            }
        } catch (\Exception $e) {
            \core\Memory::services('Logs')->securityLog("Invalid type " . $this->post['type'] . ' on registration:processOpenID().');
        }
    }

    /**
     * Completes the openID registration
     */
    private function openIDRegistration()
    {
        $i_code = $this->service_Authorization->registerOpenIDConfirm($this->get['code'], $this->service_Session->get('type'));
        switch ($i_code) {
            case 0:
                $this->s_notice = $this->service_Language->get('language/registration/notices/usernameTakenOpenID');
                break;
            case 0:
                $this->s_notice = $this->service_Language->get('language/registration/notices/emailTakenOpenID');
                break;
        }
        
        $this->openIDForm();
    }

    /**
     * Registers the user normally
     */
    private function process()
    {
        if (! $this->check()) {
            $this->service_Template->loadView('index.tpl');
            $this->form();
            return;
        }
        
        /* Register user */
        $this->a_data['username'] = $this->a_data['nick'];
        if (! $this->service_Authorization->registerNormal($this->a_data)) {
            $this->service_Template->set('errorNotice', $this->service_Language->get('language/registration/failed'));
        } else {
            $this->service_Template->set('notice', $this->service_Language->insert($this->service_Language->get('language/registration/emailSend'), 'email', $this->a_data['email']));
        }
    }

    /**
     * Checks if all the fields are filled in
     *
     * @return boolean if all the fields are filled in
     */
    private function check()
    {
        $a_fields = array(
            'nick',
            'email',
            'password'
        );
        $bo_error = false;
        
        for ($i = 0; $i < count($a_fields); $i ++) {
            $this->a_data[$a_fields[$i]] = trim($this->post[$a_fields[$i]]);
            if (trim($this->post[$a_fields[$i]]) == '') {
                $this->s_notice .= $this->service_Language->get('language/registration/notices/notices' . $i) . '<br/>';
                $bo_error = true;
            }
        }
        if ($bo_error)
            return false;
        
        if (strlen($this->post['nick']) < 3) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/usernameToShort') . '<br/>';
        }
        if (strlen($this->post['password']) < 8) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/passwordToShort') . '<br/>';
        }
        
        if (! $this->service_Security->checkEmail($this->a_data['email'])) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/emailInvalid') . '<br/>';
            $bo_error = true;
        }
        
        if ($this->post['password'] != $this->post['password2']) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/passwordInvalid') . '<br/>';
            $bo_error = true;
        }
        
        if (! $this->checkUsername($this->post['nick'])) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/usernameTaken') . '<br/>';
            $bo_error = true;
        }
        
        if (! $this->checkEmail($this->post['email'])) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/emailTaken') . '<br/>';
            $bo_error = true;
        }
        
        $helper_Captcha = \core\Memory::helpers('Captcha');
        if (! $helper_Captcha->checkCaptcha($this->post['captcha'])) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/codeInvalid') . '<br/>';
            $bo_error = true;
        }
        
        if (! isset($this->post['conditions'])) {
            $this->s_notice .= $this->service_Language->get('language/registration/notices/conditions');
            $bo_error = true;
        }
        
        return ! $bo_error;
    }

    /**
     * Checks if the username is taken
     *
     * @param string $s_username
     *            The username
     * @return boolean if the username is free, otherwise false
     */
    private function checkUsername($s_username)
    {
        return \core\Memory::models('User')->checkUsername($s_username);
    }

    /**
     * Checks if the email is taken
     *
     * @param string $s_email
     *            email address
     * @return boolean if the email is free, otherwise false
     */
    private function checkEmail($s_email)
    {
        return \core\Memory::models('User')->checkEmail($s_email);
    }
}

$obj_Registration = new Registration();
unset($obj_Registration);