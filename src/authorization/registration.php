<?php
namespace authorization;

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
 * Registration page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Registration extends \includes\BaseLogicClass
{

    /**
     *
     * @var \core\models\Login
     */
    private $login;

    private $a_data;

    private $s_notice;
    
    
    
    /**
     * Constructor
     *
     * @param \core\Input $input    The input parser
     * @param \core\models\Config $config
     * @param \core\services\Language $language
     * @param \core\services\Template $template
     * @param \core\classes\Header $header
     * @param \core\classes\Menu $menu
     * @param \core\classes\Footer $footer
     * @param \core\models\Login $login
     * @param \core\services\Headers $headers
     */
    public function __construct(\core\Input $input,\core\models\Config $config,
        \core\services\Language $language,\core\services\Template $template,
        \core\classes\Header $header,\core\classes\Menu $menu,\core\classes\Footer $footer,\core\models\Login $login,\core\services\Headers $headers)
    {
        $this->login = $login;
        $this->headers = $headers;
        
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
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
        
        $this->s_current = 'normal';
        
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
        
        $this->template->setJavascriptLink('<script src="{NIV}js/registration.js"></script>');
        $this->template->setCssLink('<link rel="stylesheet" href="{NIV}{shared_style_dir}css/registration.css">');
        
        $this->a_types = $this->config->getLoginTypes();
        
        if (! in_array('normal',$this->a_types)) {
            $this->headers->redirect('/authorization/registration_'.$this->a_types[0].'/index');
        }
    }

    /**
     * Displays the registration form
     */
    protected function index()
    {   
        $this->setLoginTypes();
        
        $this->template->set('registration', t('registration/screenTitle'));
        
        if (! empty($this->s_notice)) {
            $this->template->set('errorNotice', $this->s_notice);
        }
        
        $this->template->set('nickText', t('system/admin/users/username'));
        $this->template->set('nick', $this->a_data['nick']);
        $this->template->set('emailText', t('system/admin/users/email'));
        $this->template->set('email', $this->a_data['email']);
        
        $this->template->set('passwordForm', \Loader::inject('\core\helpers\PasswordForm')->generate());
        
        $s_registration = t('registration/registrationVia');
        
        $this->template->set('captchaText', t('registration/captcha'));
        $this->template->set('buttonRegister', t('registration/submitButton'));
        $this->template->set('conditionsText', t('registration/conditions'));
    }

    /**
     * Registers the user normally
     */
    private function process()
    {
        if (! $this->check()) {
            $this->template->loadView('index.tpl');
            $this->form();
            return;
        }
        
        /* Register user */
        $this->a_data['username'] = $this->a_data['nick'];
        if (! $this->model_registration->register($this->a_data)) {
            $this->template->set('errorNotice', $this->service_Language->get('language/registration/failed'));
        } else {
            $this->template->set('notice', $this->service_Language->insert($this->service_Language->get('language/registration/emailSend'), 'email', $this->a_data['email']));
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
}