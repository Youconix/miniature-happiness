<?php
namespace admin;

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
 * Admin settings configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
if (! defined('NIV')) {
    define('NIV', '../../../');
}

include (NIV . 'admin/modules/settings/settings.php');

class Session extends \admin\Settings
{

    /**
     * Calls the functions
     */
    protected function menu()
    {
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {
                case 'login':
                    $this->login();
                    break;
                
                case 'sessions':
                    $this->sessions();
                    break;
            }
        } else 
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {
                    case 'login':
                        $this->login();
                        break;
                    
                    case 'sessions':
                        $this->sessionsSave();
                        break;
                }
            }
    }

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        $this->init_post = array(
            'login_redirect' => 'string',
            'logout_redirect' => 'string',
            'registration_redirect' => 'string',
            'normal_login' => 'ignore',
            'openid_login' => 'ignore',
            'facebook_login' => 'ignore',
            'facebook_app_id' => 'string',
            'ldap_login' => 'ignore',
            'ldap_server' => 'string',
            'ldap_port' => 'int',
            
            'session_name' => 'string',
            'session_path' => 'string',
            'session_expire' => 'int'
        );
        
        parent::init();
    }

    /**
     * Displays the login settings
     */
    private function login()
    {
        $this->service_Template->set('generalTitle', t('system/admin/settings/login/title'));
        $this->service_Template->set('loginRedirectText', t('system/admin/settings/login/loginRedirect'));
        $this->service_Template->set('loginRedirect', $this->getValue('main/settings/login', 'index/view'));
        $this->service_Template->set('logoutRedirectText', t('system/admin/settings/login/logoutRedirect'));
        $this->service_Template->set('logoutRedirect', $this->getValue('main/logout', 'index/view'));
        $this->service_Template->set('registrationRedirectText', t('system/admin/settings/login/registrationRedirect'));
        $this->service_Template->set('registrationRedirect', $this->getValue('main/registration', 'index/view'));
        
        $this->service_Template->set('normalLoginText', t('system/admin/settings/login/normalLogin'));
        if ($this->getValue('login/normalLogin', 1) == 1) {
            $this->service_Template->set('normalLogin', 'checked="checked"');
        }
        /* Open ID */
        $this->service_Template->set('openidLoginText', t('system/admin/settings/login/openidLogin'));
        if ($this->getValue('login/openID', 0) == 1) {
            $this->service_Template('openidLogin', 'checked="checked"');
        }
        /* Facebook */
        $this->service_Template->set('facebookLoginText', t('system/admin/settings/login/facebookLogin'));
        if ($this->getValue('login/facebook') == 1) {
            $this->service_Template->set('facebookLogin', 'checked="checked"');
        } else {
            $this->service_Template->set('facebook_login_data', 'style="display:none"');
        }
        $this->service_Template->set('facebookAppIDText', t('system/admin/settings/login/facebookAppID'));
        $this->service_Template->set('facebookAppID', $this->getValue('login/facebook_app_id'));
        /* LDAP */
        $this->service_Template->set('ldapLoginText', t('system/admin/settings/login/ldapLogin'));
        if ($this->getValue('login/LDAP') == 1) {
            $this->service_Template->set('ldapLogin', 'checked="checked"');
        } else {
            $this->service_Template->set('ldap_login_data', 'style="display:none"');
        }
        $this->service_Template->set('ldapServerText', t('system/admin/settings/host'));
        $this->service_Template->set('ldapServer', $this->getValue('login/ldap_server'));
        $this->service_Template->set('ldapPortText', t('system/admin/settings/port'));
        $this->service_Template->set('ldapPort', $this->getValue('login/ldap_port', 636));
        
        $this->service_Template->set('redirectError', t('system/admin/settings/login/redirectError'));
        $this->service_Template->set('saveButton', t('system/buttons/save'));
        $this->service_Template->set('loginChoiceText', t('system/admin/settings/login/loginChoice'));
        $this->service_Template->set('facebookAppError', t('system/admin/settings/login/facebookAppError'));
        $this->service_Template->set('ldapServerError', t('system/admin/settings/login/ldapServerError'));
        $this->service_Template->set('ldapPortError', t('system/admin/settings/login/ldapPortError'));
    }

    /**
     * Saves the login settings
     */
    private function loginSave()
    {
        if (empty($this->post['login_redirect']) || empty($this->post['logout_redirect']) || empty($this->post['registration_redirect'])) {
            return;
        }
        
        (isset($this->post['normal_login'])) ? $i_normalLogin = 1 : $i_normalLogin = 0;
        (isset($this->post['openid_login'])) ? $i_openidLogin = 1 : $i_openidLogin = 0;
        (isset($this->post['facebook_login'])) ? $i_facebookLogin = 1 : $i_facebookLogin = 0;
        (isset($this->post['ldap_login'])) ? $i_ldapLogin = 1 : $i_ldapLogin = 0;
        
        if ($i_facebookLogin == 1 && empty($this->post['facebook_app_id'])) {
            return;
        }
        
        if ($i_ldapLogin == 1 && (empty($this->post['ldap_server']) || ! isset($this->post['ldap_port']) || ! is_numeric($this->post['ldap_port']) || $this->post['ldap_port'] < 1)) {
            return;
        }
        
        if ($i_normalLogin == 0 && $i_openidLogin == 0 && $i_facebookLogin == 0 && $i_ldapLogin == 0) {
            return;
        }
        
        $this->setValue('main/login', $this->post['login_redirect']);
        $this->setValue('main/logout', $this->post['logout_redirect']);
        $this->setValue('main/logout', $this->post['registration_redirect']);
        $this->setValue('login/normalLogin', $i_normalLogin);
        $this->setValue('login/openID', $i_openidLogin);
        $this->setvalue('login/facebook', $i_facebookLogin);
        $this->setValue('login/facebook_app_id', $this->post['facebook_app_id']);
        $this->setValue('login/LDAP', $i_ldapLogin);
        $this->setValue('login/ldap_server', $this->post['ldap_server']);
        $this->setValue('login/ldap_port', $this->post['ldap_port']);
        
        $this->service_Settings->save();
    }

    /**
     * Displays the sessions
     */
    private function sessions()
    {
        $this->service_Template->set('generalTitle', t('system/admin/settings/sessions/title'));
        $this->service_Template->set('sessionNameText', t('system/admin/settings/sessions/name'));
        $this->service_Template->set('sessionName', $this->getValue('session/sessionName', 'miniature-happiness'));
        $this->service_Template->set('sessionPathText', t('system/admin/settings/sessions/path'));
        $this->service_Template->set('sessionPath', $this->getValue('sessions/sessionPath', 'admin/data/sessions'));
        $this->service_Template->set('sessionExpireText', t('system/admin/settings/sessions/expire'));
        $this->service_Template->set('sessionExpire', $this->getvalue('session/sessionExpire', 300));
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the sessions
     */
    private function sessionsSave()
    {
        if (! $this->service_Validation->validate(array(
            'session_name' => array(
                'required' => 1
            ),
            'session_path' => array(
                'required' => 1
            ),
            'session_expire' => array(
                'required' => 1,
                'type' => 'int',
                'min-value' => 60
            )
        ), $this->post)) {
            return;
        }
        
        $this->setValue('session/sessionName', $this->post['session_name']);
        $this->getValue('sessions/sessionPath', $this->post['session_path']);
        $this->setvalue('session/sessionExpire', $this->post['session_expire']);
        
        $this->service_Settings->save();
    }
}

$obj_Session = new Session();
unset($obj_Session);