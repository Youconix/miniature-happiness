function SettingsSession(){
	this.address = '../../admin/modules/settings/session/';
}
SettingsSession.prototype.loginInit = function(){
	settingsSession.toggleOpenAuth('facebook');
	//settingsSession.toggleOpenAuth('google');
	//settingsSession.toggleOpenAuth('twitter');
	
	/*
	$('#ldap_login').click(function(){
		if( $('#ldap_login').is(':checked') ){
			$('#ldap_login_data').show();
		}
		else {
			$('#ldap_login_data').hide();
		}
	});
	*/ 
	
	$('#settings_login_save').click(function(){ settingsSession.loginSave(); });
}
SettingsSession.prototype.toggleOpenAuth = function(name){
	$('#'+name+'_login').click(function(){
		if( $('#'+name+'_login').is(':checked') ){
			$('#'+name+'_login_data').show();
		}
		else {
			$('#'+name+'_login_data').hide();
		}
	});
}
SettingsSession.prototype.checkOpenAuth = function(name,fields){
	if( $('#'+name+'_login').is(':checked') ){
		$('#'+name+'_app_id').attr('required',true);
	}
	else {
		$('#'+name+'_app_id').removeAttr('required');
	}
	
	fields.push(name+'_app_id');
	fields.push(name+'_app_secret');
	
	return fields;
}
SettingsSession.prototype.loginSave = function(){
	var fields = new Array('login_redirect','logout_redirect','registration_redirect');
	fields = this.checkOpenAuth('facebook',fields);
	//fields = this.checkOpenAuth('google',fields);
	//fields = this.checkOpenAuth('twitter',fields);
	
	/* LDAP * /
	if( $('#ldap_login').is(':checked') ){
		$('#ldap_server').attr('required',true);
		$('#ldap_port').attr('required',true);
	}
	else {
		$('#ldap_server').removeAttr('required');
		$('#ldap_port').removeAttr('required');
	}
	fields.push('ldap_server');
	fields.push('ldap_port');
	*/
	
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	var normal_login = 0;
	var google_login = 0;
	var facebook_login = 0;
	var twitter_login = 0;
	var ldap_login = 0;
	if( $('#normal_login').is(':checked') ){
		normal_login = 1;
	}
	/*if( $('#google_login').is(':checked') ){
		google_login = 1;
	}*/
	if( $('#facebook_login').is(':checked') ){
		facebook_login = 1;
	}/*
	if( $('#twitter_login').is(':checked') ){
		twitter_login = 1;
	}
	if( $('#ldap_login').is(':checked') ){
		ldap_login = 1;
	}*/
	
	if( normal_login == 0 && google_login == 0 && facebook_login == 0  && twitter_login == 0 && ldap_login == 0 ){
		return;
	}
	
	/*
	var data = {'login_redirect':$('#login_redirect').val(),'logout_redirect' : $('#logout_redirect').val(), 
		'registration_redirect' : $('#registration_redirect').val(), 'normal_login' : normal_login,
		'google_login' : google_login,'google_app_id':$('#google_app_id').val(),'google_app_secret' : $('#google_app_secret').val(), 
		'facebook_login' : facebook_login,'facebook_app_id' : $('#facebook_app_id').val(),'facebook_app_secret' : $('#facebook_app_secret').val(),
		'twitter_login' : twitter_login,'twitter_app_id' : $('#twitter_app_id').val(),'twitter_app_secret' : $('#twitter_app_secret').val(),
		ldap_login : ldap_login, 'ldap_server' : $('#ldap_server').val(),'ldap_port' : $('#ldap_port').val()
	}; */
	
	var data = {'login_redirect':$('#login_redirect').val(),'logout_redirect' : $('#logout_redirect').val(), 
			'registration_redirect' : $('#registration_redirect').val(), 'normal_login' : normal_login,
			'google_login' : google_login,'google_app_id':'','google_app_secret' : '', 
			'facebook_login' : facebook_login,'facebook_app_id' : $('#facebook_app_id').val(),'facebook_app_secret' : $('#facebook_app_secret').val(),
			'twitter_login' : twitter_login,'twitter_app_id' : '','twitter_app_secret' : '',
			ldap_login : ldap_login, 'ldap_server' : '','ldap_port' : 636
		};
	
	$.post(settingsSession.address+'login',data);
	$('#notice').html(languageAdmin.admin_settings_saved);
}
SettingsSession.prototype.init = function(){
	$('#settings_sessions_save').click(function(){ settingsSession.sessionsSave(); });
}
SettingsSession.prototype.sessionsSave = function(){
	var fields = new Array('session_name','session_path','session_expire');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	var data = {'session_name': $('#session_name').val(),'session_path' : $('#session_path').val(), '#session_expire' : $('#session_expire').val()};
		
	$.post(settingsSession.address+'sessions',data);
	$('#notice').html(languageAdmin.admin_settings_saved);
}
var settingsSession = new SettingsSession();