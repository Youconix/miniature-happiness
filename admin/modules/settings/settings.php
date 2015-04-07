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

include (NIV . 'core/AdminLogicClass.php');

class Settings extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\services\Settings
     */
    private $service_Settings;

    /**
     *
     * @var \core\services\FileHandler
     */
    private $service_FileHandler;

    /**
     *
     * @var \core\services\Builder
     */
    private $service_Builder;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if (! \core\Memory::models('Config')->isAjax()) {
            exit();
        }
        
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {
                case 'email':
                    $this->email();
                    break;
                
                case 'general':
                    $this->general();
                    break;
                
                case 'login':
                    $this->login();
                    break;
                
                case 'sessions':
                    $this->sessions();
                    break;
                
                case 'database':
                    $this->database();
                    break;
                
                case 'cache':
                    $this->cache();
                    break;
                    
                case 'language':
                    $this->language();
                    break;
            }
        } else 
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {
                    case 'email':
                        $this->emailSave();
                        break;
                    
                    case 'general':
                        $this->generalSave();
                        break;
                    
                    case 'login':
                        $this->login();
                        break;
                    
                    case 'sessions':
                        $this->sessionsSave();
                        break;
                    
                    case 'databaseCheck':
                        $this->databaseCheck();
                        break;
                    
                    case 'database':
                        $this->databaseSave();
                        break;
                    
                    case 'cache':
                        $this->cacheSave();
                        break;
                        
                    case 'language':
                        $this->languageSave();
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
            'email_name' => 'string',
            'email_email' => 'string',
            'smtp_active' => 'boolean',
            'smtp_host' => 'string',
            'smtp_username' => 'string',
            'smtp_password' => 'string',
            'smtp_port' => 'int',
            'email_admin_name' => 'string',
            'email_admin_email' => 'string',
            
            'name_site' => 'string',
            'site_url' => 'string',
            'site_base' => 'string',
            'timezone' => 'string',
            'template' => 'string',
            'logger' => 'string',
            'log_location' => 'string',
            'log_size' => 'int',
            
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
            'session_expire' => 'int',
            
            'prefix' => 'string',
            'type' => 'string',
            'username' => 'string',
            'password' => 'string',
            'database' => 'string',
            'host' => 'string',
            'port' => 'int',
            
            'default_language' => 'string',
            
            'language' => 'string'
        );
        
        parent::init();
        
        $this->service_Settings = \Loader::Inject('\core\services\Settings');
        $this->service_FileHandler = \Loader::Inject('\core\services\FileHandler');
        $this->service_Builder = \Loader::Inject('\core\services\QueryBuilder')->createBuilder();
    }

    /**
     * Displays the email settings
     */
    private function email()
    {
        $this->service_Template->set('emailTitle', t('system/admin/settings/email/title'));
        
        $this->service_Template->set('emailGeneralTitle', t('system/admin/settings/email/generalTitle'));
        $this->service_Template->set('nameText', t('system/admin/settings/email/senderName'));
        $this->service_Template->set('name', $this->getValue('mail/senderName'));
        $this->service_Template->set('emailText', t('system/admin/settings/email/senderEmail'));
        $this->service_Template->set('email', $this->getValue('mail/senderEmail'));
        
        $this->service_Template->set('SmtpTitle', t('system/admin/settings/email/smtp'));
        $this->service_Template->set('smtpActiveText', t('system/admin/settings/email/useSmtp'));
        $smtpActive = $this->getValue('mail/SMTP');
        if ($smtpActive == 1) {
            $this->service_Template->set('smtpActive', 'checked="checked"');
        } else {
            $this->service_Template->set('showSMTP', 'style="display:none"');
        }
        $this->service_Template->set('smtpHostText', t('system/admin/settings/host'));
        $this->service_Template->set('smtpHost', $this->getValue('mail/host'));
        $this->service_Template->set('smtpUsernameText', t('system/admin/settings/username'));
        $this->service_Template->set('smtpUsername', $this->getValue('mail/username'));
        $this->service_Template->set('smtpPasswordText', t('system/admin/settings/password'));
        $this->service_Template->set('smtpPassword', $this->getValue('mail/password'));
        $this->service_Template->set('smtpPortText', t('system/admin/settings/port'));
        $this->service_Template->set('smtpPort', $this->getValue('mail/port', 587));
        
        $this->service_Template->set('emailAdminTitle', t('system/admin/settings/email/adminSenderTitle'));
        $this->service_Template->set('nameAdminText', t('system/admin/settings/email/adminSenderName'));
        $this->service_Template->set('nameAdmin', $this->getValue('main/admin/name'));
        $this->service_Template->set('emailAdminText', t('system/admin/settings/email/adminSenderEmail'));
        $this->service_Template->set('emailAdmin', $this->getValue('main/admin/email'));
        
        $this->service_Template->set('nameError', t('system/admin/settings/email/senderEmpty'));
        $this->service_Template->set('emailError', t('system/admin/settings/email/senderEmailEmpty'));
        $this->service_Template->set('smtpHostError', t('system/admin/settings/email/smtpHostError'));
        $this->service_Template->set('smtpUsernameError', t('system/admin/settings/email/smtpUsernameError'));
        $this->service_Template->set('smptPasswordError', t('system/admin/settings/email/smptPasswordError'));
        $this->service_Template->set('smtpPortError', t('system/admin/settings/email/smtpPortError'));
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the email settings
     */
    private function emailSave()
    {
        if (! $this->service_Validation->validate(array(
            'email_name' => array(
                'required' => 1
            ),
            'email_email' => array(
                'required' => 1,
                'pattern' => 'email'
            ),
            'email_admin_name' => array(
                'required' => 1
            ),
            'email_admin_email' => array(
                'required' => 1,
                'pattern' => 'email'
            )
        ), $this->post)) {
            return;
        }
        
        if (isset($this->post['smtp_active']) && ! $this->service_Validation->validate(array(
            'smtp_host' => array(
                'required' => 1,
                'pattern' => 'url'
            ),
            'smtp_username' => array(
                'required' => 1
            ),
            'smtp_password' => array(
                'required' => 1
            ),
            'smtp_port' => array(
                'required' => 1,
                'type' => 'int',
                'min-value' => 1
            )
        ), $this->post)) {
            return;
        }
        
        $this->setValue('settings/mail/senderName', $this->post['email_name']);
        $this->setValue('settings/mail/senderEmail', $this->post['email_email']);
        
        $this->setValue('settings/mail/SMTP', ((isset($this->post['smtp_active'])) ? 1 : 0));
        $this->setValue('settings/mail/host', $this->post['smtpHost']);
        $this->setValue('settings/mail/username', $this->post['smtp_username']);
        $this->setValue('settings/mail/password', $this->post['smtp_password']);
        $this->setValue('settings/mail/port', $this->post['smtp_port']);
        
        $this->setValue('main/admin/name', $this->post['email_admin_name']);
        $this->setValue('main/admin/email', $this->post['email_admin_email']);
        
        $this->service_Settings->save();
    }

    /**
     * Displays the general settings
     */
    private function general()
    {
        $this->service_Template->set('generalTitle', t('system/admin/settings/general/title'));
        
        $this->service_Template->set('nameSiteText', t('system/admin/settings/general/nameSite'));
        $this->service_Template->set('nameSite', $this->getValue('main/nameSite'));
        $this->service_Template->set('nameSiteError', t('system/admin/settings/general/siteNameEmpty'));
        $this->service_Template->set('siteUrlText', t('system/admin/settings/general/siteUrl'));
        $this->service_Template->set('siteUrl', $this->getValue('main/url'));
        $this->service_Template->set('siteUrlError', t('system/admin/settings/general/urlEmpty'));
        $this->service_Template->set('siteBaseText', t('system/admin/settings/general/basedir'));
        $this->service_Template->set('siteBase', $this->getValue('main/base'));
        $this->service_Template->set('timezoneText', t('system/admin/settings/general/timezone'));
        $this->service_Template->set('timezone', $this->getValue('main/timeZone'));
        $this->service_Template->set('timezoneError', t('system/admin/settings/general/timezoneInvalid'));
        
        /* Templates */
        $this->service_Template->set('templatesHeader', t('system/admin/settings/general/templates'));
        $this->service_Template->set('templateText', 'Template set');
        $s_template = $this->getValue('templates/dir', 'default');
        
        $directory = $this->service_FileHandler->readDirectory(NIV . 'styles');
        $directory = $this->service_FileHandler->directoryFilterName($directory,array('!.','!..'));
        $templates = new \core\classes\OnlyDirectoryFilterIteractor($directory);
        
        foreach ($templates as $dir) {
            ($dir->getFilename() == $s_template) ? $selected = 'selected="selected"' : $selected = '';
            
            $this->service_Template->setBlock('template', array(
                'value' => $dir->getFilename(),
                'selected' => $selected,
                'text' => $dir->getFilename()
            ));
        }
        
        /* Logs */
        $this->service_Template->set('loggerText', t('system/admin/settings/general/logger') . ' (\Psr\Log\LoggerInterface)');
        $this->service_Template->set('logger', $this->getValue('main/logs', 'default'));
        $this->service_Template->set('loggerError', t('system/admin/settings/general/loggerInvalid'));
        if ($this->getValue('main/logs', 'default') != 'default') {
            $this->service_Template->set('location_log_default', 'style="display:none"');
        }
        
        $this->service_Template->set('logLocationText', t('system/admin/settings/general/logLocation'));
        $this->service_Template->set('logLocation', $this->getValue('main/log_location', str_replace(array(
            '../',
            './'
        ), array(
            '',
            ''
        ), DATA_DIR) . 'logs' . DIRECTORY_SEPARATOR));
        $this->service_Template->set('logLocationError', t('system/admin/settings/general/logLocationInvalid'));
        $this->service_Template->set('logSizeText', t('system/admin/settings/general/logSize'));
        $model_Config = $this->model_Config;
        $this->service_Template->set('logSize', $this->getValue('main/log_max_size', $model_Config::LOG_MAX_SIZE));
        $this->service_Template->set('logSizeError', t('system/admin/settings/general/logSizeInvalid'));
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the general settings
     */
    private function generalSave()
    {
        if (! $this->service_Validation->validate(array(
            'name_site' => array(
                'required' => 1
            ),
            'site_url' => array(
                'required' => 1,
                'pattern' => 'url'
            ),
            "timezone" => array(
                'required' => 1,
                'pattern' => '#^[a-zA-Z]+/[a-zA-Z]+$#'
            ),
            "template" => array(
                'required' => 1
            ),
            'logger' => array(
                'required' => 1
            ),
            'log_location' => array(
                'type' => 'string'
            ),
            'log_size' => array(
                'required' => 1,
                'type' => 'int',
                'min-value' => 1000
            )
        ), $this->post)) {
            return;
        }
        
        if ($this->post['logger'] == 'default' && empty($this->post['log_location'])) {
            return;
        }
        
        $this->setValue('main/nameSite', $this->post['name_site']);
        $this->setValue('main/url', $this->post['site_url']);
        $this->setValue('main/base', $this->post['site_base']);
        $this->setValue('main/timeZone', $this->post['timezone']);
        $this->setValue('templates/dir', $this->post['template']);
        $this->setValue('main/logs', $this->post['logger']);
        $this->setValue('main/log_location', str_replace(DATA_DIR, '', $this->post['log_location']));
        $this->setValue('main/log_max_size', $this->post['log_size']);
        
        $this->service_Settings->save();
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

    /**
     * Displays the database settings
     */
    private function database()
    {
        $this->service_Template->set('databaseTitle', t('system/admin/settings/database/title'));
        $this->service_Template->set('prefixText', t('system/admin/settings/database/prefix'));
        $this->service_Template->set('prefix', $this->getValue('SQL/prefix', 'MH_'));
        $this->service_Template->set('typeText', t('system/admin/settings/database/type'));
        $s_type = $this->getValue('SQL/type');
        
        $directory = $this->service_FileHandler->readDirectory(NIV . 'core' . DIRECTORY_SEPARATOR . 'database');
        $directory = $this->service_FileHandler->directoryFilterName($directory,array('*.inc.php','!_binded','!Database.inc.php','!builder_'));
        foreach ($directory as $file) {
            $s_name = str_replace('.inc.php', '', $file->getFilename());
            ($s_name == $s_type) ? $selected = 'selected="selected"' : $selected = '';
            $this->service_Template->setBlock('type', array(
                'value' => $s_name,
                'selected' => $selected,
                'text' => $s_name
            ));
        }
        
        $this->service_Template->set('usernameText', t('system/admin/settings/username'));
        $this->service_Template->set('username', $this->getValue('SQL/' . $s_type . '/username'));
        $this->service_Template->set('passwordText', t('system/admin/settings/password'));
        $this->service_Template->set('password', $this->getValue('SQL/' . $s_type . '/password'));
        $this->service_Template->set('databaseText', t('system/admin/settings/database/database'));
        $this->service_Template->set('database', $this->getValue('SQL/' . $s_type . '/database'));
        $this->service_Template->set('hostText', t('system/admin/settings/host'));
        $this->service_Template->set('host', $this->getValue('SQL/' . $s_type . '/host'));
        $this->service_Template->set('portText', t('system/admin/settings/port'));
        $this->service_Template->set('port', $this->getValue('SQL/' . $s_type . '/port', 3306));
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Checks the database settings
     */
    private function databaseCheck()
    {
        if (! $this->service_Validation->validate(array(
            'type' => array(
                'required' => 1
            ),
            'username' => array(
                'required' => 1
            ),
            'password' => array(
                'required' => 1
            ),
            'database' => array(
                'required' => 1
            ),
            'host' => array(
                'required' => 1
            ),
            'port' => array(
                'required' => 1,
                'type' => 'int'
            )
        ), $this->post)) {
            echo ('0');
            die();
        }
        $bo_oke = false;
        switch ($this->post['type']) {
            case 'Mysqli':
                require_once (NIV . 'core/database/Mysqli.inc.php');
                $bo_oke = \core\database\Database_Mysqli::checkLogin($this->post['username'], $this->post['password'], $this->post['database'], $this->post['host'], $this->post['port']);
                break;
            
            case 'PostgreSql':
                require_once (NIV . 'core/database/PostgreSql.inc.php');
                $bo_oke = \core\database\Database_PostgreSql::checkLogin($this->post['username'], $this->post['password'], $this->post['database'], $this->post['host'], $this->post['port']);
                break;
        }
        
        if ($bo_oke) {
            echo ('1');
        } else {
            echo ('0');
        }
        die();
    }

    /**
     * Saves the database settings
     */
    private function databaseSave()
    {
        if (! $this->service_Validation->validate(array(
            'type' => array(
                'required' => 1
            ),
            'username' => array(
                'required' => 1
            ),
            'password' => array(
                'required' => 1
            ),
            'database' => array(
                'required' => 1
            ),
            'host' => array(
                'required' => 1
            ),
            'port' => array(
                'required' => 1,
                'type' => 'int'
            )
        ), $this->post)) {
            return;
        }
        
        $s_type = $this->post['type'];
        $this->setValue('SQL/prefix', $this->post['prefix']);
        $this->setValue('SQL/type', $s_type);
        $this->setValue('SQL/' . $s_type . '/username', $this->post['username']);
        $this->setValue('SQL/' . $s_type . '/password', $this->post['password']);
        $this->setValue('SQL/' . $s_type . '/database', $this->post['database']);
        $this->setValue('SQL/' . $s_type . '/host', $this->post['host']);
        $this->setValue('SQL/' . $s_type . '/port', $this->post['port']);
        
        $this->service_Settings->save();
    }

    private function cache()
    {
        $this->service_Template->set('cacheTitle', t('system/admin/settings/cache/title'));
        $this->service_Template->set('cacheActiveText', 'Caching geactiveerd');
        if ($this->getValue('cache/status') == 1) {
            $this->service_Template->set('cacheActive', 'checked="checked"');
        } else {
            // $this->service_Template->set('cacheSettings','style="display:none"');
        }
        
        $this->service_Template->set('cacheExpireText', 'Cache verloop tijd in seconden');
        $this->service_Template->set('cacheExpire', $this->getValue('cache/timeout', 86400));
        
        $this->service_Builder->select('no_cache', '*');
        $service_database = $this->service_Builder->getResult();
        if ($service_database->num_rows() > 0) {
            $a_pages = $service_database->fetch_assoc();
            foreach ($a_pages as $a_page) {
                $this->service_Template->setBlock('noCache', array(
                    'id' => $a_page['id'],
                    'name' => $a_page['page']
                ));
            }
        }
        
        $this->service_Template->set('delete', t('system/buttons/delete'));
        $this->service_Template->set('saveButton', t('system/buttons/save'));
        $this->service_Template->set('page', 'Pagina');
        $this->service_Template->set('addButton', t('system/buttons/add'));
    }

    private function cacheSave()
    {}
    
    /**
     * Displays the languages
     */
    private function language(){
        $this->service_Template->set('languageTitle',t('system/admin/settings/languages/title'));
        $this->service_Template->set('defaultLanguageText','Standaard taal');
        
        $s_defaultLanguage = $this->getValue('defaultLanguage','nl_NL');
        
        $languages = $this->service_FileHandler->readDirectory(NIV . 'language');
        $languages = $this->service_FileHandler->directoryFilterName($languages,array('*_*|*.lang'));
        
        foreach($languages AS $language){
            $s_filename = $language->getFilename();            
            ($s_filename == $s_defaultLanguage) ? $selected = 'selected="selected"': $selected = '';
            
            $this->service_Template->setBlock('defaultLanguage',array('value'=>$s_filename,'text'=>$s_filename,'selected'=>$selected));
        }
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }
    
    /**
     * Saves the languages
     */
    private function languageSave(){
        if (! $this->service_Validation->validate(array(
            'default_language' => array(
                'required' => 1,
                'type'=>'string'
            )
            ),$this->post) ){
            return;
        }
        
        $this->setValue('defaultLanguage',$this->post['default_language']);
        $this->service_Settings->save();
    }

    /**
     * Returns the value
     *
     * @param string $s_key
     *            The key
     * @param string $default
     *            The default value if the key does not exist
     * @return string The value
     */
    private function getValue($s_key, $default = '')
    {
        if (! $this->service_Settings->exists($s_key)) {
            return $default;
        }
        
        $s_value = $this->service_Settings->get($s_key);
        if (empty($s_value) && ! empty($default)) {
            return $default;
        }
        
        return $s_value;
    }

    /**
     * Sets the value
     *
     * @param string $s_key
     *            The key
     * @param string $s_value
     *            The value
     */
    private function setValue($s_key, $s_value)
    {
        if (! $this->service_Settings->exists($s_key)) {
            $this->service_Settings->add($s_key, $s_value);
        } else {
            $this->service_Settings->set($s_key, $s_value);
        }
    }
}

$obj_Settings = new Settings();
unset($obj_Settings);