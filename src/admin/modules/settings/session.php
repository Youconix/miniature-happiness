<?php
namespace admin\modules\settings;

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
class Session extends \admin\modules\settings\Settings
{

	private $a_openAuth = array('google','facebook','twitter');
	
    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            switch ($s_command) {
                case 'login':
                    $this->login();
                    break;
                
                case 'sessions':
                    $this->sessions();
                    break;
            }
        } else {
            switch ($s_command) {
                case 'login':
                    $this->loginSave();
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
            'ldap_login' => 'ignore',
            'ldap_server' => 'string',
            'ldap_port' => 'int',
            
            'session_name' => 'string',
            'session_path' => 'string',
            'session_expire' => 'int'
        );
        
        foreach($this->a_openAuth AS $s_name ){
        	$this->init_post[$s_name.'_login'] = 'ignore';
        	$this->init_post[$s_name.'_app_id'] = 'ignore';
        	$this->init_post[$s_name.'_app_secret'] = 'ignore';
        }
        
        parent::init();
    }

    /**
     * Displays the login settings
     */
    private function login()
    {
        $this->template->set('generalTitle', t('system/settings/login/title'));
        $this->template->set('loginRedirectText', t('system/settings/login/loginRedirect'));
        $this->template->set('loginRedirect', $this->getValue('login/login', 'index/view'));
        $this->template->set('logoutRedirectText', t('system/settings/login/logoutRedirect'));
        $this->template->set('logoutRedirect', $this->getValue('login/logout', 'index/view'));
        $this->template->set('registrationRedirectText', t('system/settings/login/registrationRedirect'));
        $this->template->set('registrationRedirect', $this->getValue('login/registration', 'index/view'));
        
        $a_types = $this->config->getLoginTypes();
        
        $this->setType('normal', $a_types);
        foreach($this->a_openAuth AS $s_name){
        	$this->setOpenAuth($s_name,$a_types);
        }
        $this->setLDAP($a_types);
        
        $this->template->set('redirectError', t('system/settings/login/redirectError'));
        $this->template->set('saveButton', t('system/buttons/save'));
        $this->template->set('loginChoiceText', t('system/settings/login/loginChoice'));
    }
    
    /**
     * Sets the openAuth setting and text
     * 
     * @param string $s_name	The name
     * @param array $a_types	The active logins
     */
    private function setOpenAuth($s_name,$a_types){
    	$this->setType($s_name, $a_types,true);
    	
    	$this->template->set($s_name.'AppID',$this->getValue('login/openAuth/'.$s_name.'/appId'));
    	$this->template->set($s_name.'AppSecret',$this->getValue('login/openAuth/'.$s_name.'/appSecret'));
    	
    	$this->template->set('appIDText', t('system/settings/login/appID'));
    	$this->template->set('appSecretText',t('system/settings/login/appSecret'));
    	$this->template->set('appError', t('system/settings/login/facebookAppError'));
    	$this->template->set('appSecretError', t('system/settings/login/facebookAppSecretError'));
    }
    
    /**
     * Sets the LDAP setting and text
     * 
     * @param array $a_types	The active logins
     */    
	private function setLDAP($a_types){
    	$this->setType('ldap', $a_types,true);
    	
    	$this->template->set('ldapServerText', t('system/settings/host'));
    	$this->template->set('ldapServer', $this->getValue('login/LDAP/server'));
    	$this->template->set('ldapPortText', t('system/settings/port'));
    	$this->template->set('ldapPort', $this->getValue('login/LDAP/port', 636));
    	$this->template->set('ldapServerError', t('system/settings/login/ldapServerError'));
    	$this->template->set('ldapPortError', t('system/settings/login/ldapPortError'));
    }
    
    /**
     * Sets the type
     * 
     * @param string $s_name	The name
     * @param array $a_types	The active logins
     * @param bool $bo_hide		Set to true to hide the block if inactive
     */
    private function setType($s_name,$a_types,$bo_hide = false){
    	$this->template->set($s_name.'LoginText', t('system/settings/login/'.$s_name.'Login'));
    	if ( in_array($s_name,$a_types)) {
    		$this->template->set($s_name.'Login', 'checked="checked"');
    	}
    	else if( $bo_hide ){
    		$this->template->set($s_name.'_login_data', 'style="display:none"');
    	}
    }

    /**
     * Saves the login settings
     */
    private function loginSave()
    {
    	$a_rules = array(
            'login_redirect' => 'required',
            'logout_redirect' => 'required',
            'registration_redirect' => 'required',
        	'normal_login' => 'required|set:0,1',
        	'ldap_login' => 'required|set:0,1'        	
        );
    	foreach($this->a_openAuth AS $s_name ){
    		$a_rules[$s_name.'_login'] = 'required|set:0,1';
    	}
    	
        if (! $this->post->validate($a_rules)) {
            return;
        }
        
        $bo_found = false;
        if( $this->post->get('normal_login') == 1 || $this->post->get('ldap_login') == 1 ){
        	$bo_found = true;
        }
        else {
        	foreach($this->a_openAuth AS $s_name){
        		if( $this->post->get($s_name.'_login') == 1 ){
        			$bo_found = true;
        			break;
        		}
        	}
        }
        
        if ( !$bo_found ) {
        	return;
        }
        
        foreach( $this->a_openAuth AS $s_name ){
        	if( $this->post->get($s_name.'_login') == 0 ){
        		continue;
        	}
        	
        	if( !$this->post->validate(array(
        		$s_name.'_app_id' => 'required',
        		$s_name.'_app_secret' => 'required'
        	))) {
        		return;
        	}
        }
        
        if ( $this->post->get('ldap_login') == 1 && ! $this->post->validate(array(
            'ldap_server' => 'required',
            'ldap_port' => 'required|type:port'
        ))) {
            return;
        }
        
        $this->setValue('login/login', $this->post->get('login_redirect'));
        $this->setValue('login/logout', $this->post->get('logout_redirect'));
        $this->setValue('login/registration', $this->post->get('registration_redirect'));
        $this->setValue('login/normalLogin', $this->post->get('normal_login'));
        
        foreach( $this->a_openAuth AS $s_name ){
        	$this->setValue('login/openAuth/'.$s_name.'/status', $this->post->get($s_name.'_login'));
        	$this->setValue('login/openAuth/'.$s_name.'/appId', $this->post->get($s_name.'_app_id'));
        	$this->setValue('login/openAuth/'.$s_name.'/appSecret', $this->post->get($s_name.'_app_secret'));
        }
        
        $this->setValue('login/LDAP/status', $this->post->get('ldap_login'));
        $this->setValue('login/LDAP/server', $this->post->get('ldap_server'));
        $this->setValue('login/LDAP/port', $this->post->get('ldap_port'));
        
        $this->settings->save();
    }

    /**
     * Displays the sessions
     */
    private function sessions()
    {
        $this->template->set('generalTitle', t('system/settings/sessions/title'));
        $this->template->set('sessionNameText', t('system/settings/sessions/name'));
        $this->template->set('sessionName', $this->getValue('session/sessionName', 'miniature-happiness'));
        $this->template->set('sessionPathText', t('system/settings/sessions/path'));
        $this->template->set('sessionPath', $this->getValue('sessions/sessionPath', 'data/sessions'));
        $this->template->set('sessionExpireText', t('system/settings/sessions/expire'));
        $this->template->set('sessionExpire', $this->getvalue('session/sessionExpire', 300));
        
        $this->template->set('sessionNameError',t('system/settings/sessions/nameError'));
        $this->template->set('sessionPathError',t('system/settings/sessions/pathError'));
        $this->template->set('sessionExpireError',t('system/settings/sessions/expireError'));
        
        $this->template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the sessions
     */
    private function sessionsSave()
    {
        if (! $this->post->validate(array(
            'session_name' => 'required',
            'session_path' => 'required',
            'session_expire' => 'required|type:int|min:60'
        ))) {
            return;
        }
        
        $this->setValue('session/sessionName', $this->post['session_name']);
        $this->getValue('sessions/sessionPath', $this->post['session_path']);
        $this->setvalue('session/sessionExpire', $this->post['session_expire']);
        
        $this->settings->save();
    }
}