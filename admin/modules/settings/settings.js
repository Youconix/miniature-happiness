function Settings() {
	this.address = '../../admin/modules/settings/settings.php';
}
Settings.prototype.address = function(){
	return this.address;
}
Settings.prototype.init = function() {
	$('#admin_settings_email h2').click(function(){
		admin.show(settings.address+'?command=email',settings.emailInit);
	});
	$('#admin_settings_general h2').click(function(){
		admin.show(settings.address+'?command=general',settings.generalInit);
	});
	$('#admin_settings_login h2').click(function(){
		admin.show(settings.address+'?command=login',settings.loginInit);
	});
	$('#admin_settings_sessions h2').click(function(){
		admin.show(settings.address+'?command=sessions',settings.sessionsInit);
	});
	$('#admin_settings_database h2').click(function(){
		admin.show(settings.address+'?command=database',settings.databaseInit);
	});
	$('#admin_settings_cache h2').click(function(){
		admin.show(settings.address+'?command=cache',settings.cacheInit);
	});
}
Settings.prototype.emailInit = function(){
	$('#smtp_active').click(function(){
		if( $(this).is(':checked') ){
			$('#smtp_settings').show();
		}
		else {
			$('#smtp_settings').hide();
		}
	});
	$('#settings_email_save').click(function(){ settings.emailSave(); });
}
Settings.prototype.emailSave = function(){
	var data = {
			'command' : 'email','email_name' : $('#email_name').val(),'email_email' : $('#email_email').val(),'smtp_host' : $('#smtp_host').val(),
			'smtp_username' : $('#smtp_username').val(),'smtp_password' : $('#smtp_password').val(),'smtp_port' : $('#smtp_port').val(),
			'email_admin_name' : $('#email_admin_name').val(),'email_admin_email' : $('#email_admin_email').val()
	};
	
	if( !$('#smtp_active').is(':checked') ){
		$('#smtp_host').removeAttr('required');
		$('#smtp_username').removeAttr('required');
		$('#smtp_password').removeAttr('required');
		$('#smtp_port').removeAttr('required');		
	}
	else {
		$('#smtp_host').attr('required',true);
		$('#smtp_username').attr('required',true);
		$('#smtp_password').attr('required',true);
		$('#smtp_port').attr('required',true);
		data['smtp_active'] = 1;
	}
	
	var fields = new Array('email_name','email_email','smtp_host','smtp_username','smtp_password','smtp_port','email_admin_name','email_admin_email');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	$.post(settings.address,data);
	
	$('#notice').html(languageAdmin.admin_settings_saved);
}
Settings.prototype.generalInit = function(){
	$('#settings_general_save').click(function(){ settings.generalSave(); });
}
Settings.prototype.generalSave = function(){
	var data = {
		'command' : 'general','name_site' : $('#name_site').val(), 'site_url' : $('#site_url').val(),'site_base' : $('#site_base').val(),'template' : $('#template').val(),
		'timezone' : $('#timezone').val(), 'logger' : $('#logger').val(),'log_location' : $('#log_location').val(),'log_size' : $('#log_size').val()
	};
	
	if( $('#logger').val() == 'default' ){
		$('#log_location').attr('required',true);
	}
	else {
		$('#log_location').removeAttr('required');
	}
	
	var fields = new Array('name_site','site_url','site_base','timezone','logger','log_location','log_size');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	$.post(settings.address,data);
	
	$('#notice').html(languageAdmin.admin_settings_saved);
}
Settings.prototype.loginInit = function(){
	$('#facebook_login').click(function(){
		if( $('#facebook_login').is(':checked') ){
			$('#facebook_login_data').show();
		}
		else {
			$('#facebook_login_data').hide();
		}
	});
	$('#ldap_login').click(function(){
		if( $('#ldap_login').is(':checked') ){
			$('#ldap_login_data').show();
		}
		else {
			$('#ldap_login_data').hide();
		}
	});
	$('#settings_login_save').click(function(){ settings.loginSave(); });
}
Settings.prototype.loginSave = function(){
	if( $('#facebook_login').is(':checked') ){
		$('#facebook_app_id').attr('required',true);
	}
	else {
		$('#facebook_app_id').removeAttr('required');
	}
	if( $('#ldap_login').is(':checked') ){
		$('#ldap_server').attr('required',true);
		$('#ldap_port').attr('required',true);
	}
	else {
		$('#ldap_server').removeAttr('required');
		$('#ldap_port').removeAttr('required');
	}
	
	var fields = new Array('login_redirect','logout_redirect','registration_redirect','facebook_app_id','ldap_server','ldap_port');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	var normal_login = 0;
	var openid_login = 0;
	var facebook_login = 0;
	var ldap_login = 0;
	if( $('#normal_login').is(':checked') ){
		normal_login = 1;
	}
	if( $('#openid_login').is(':checked') ){
		openid_login = 1;
	}
	if( $('#facebook_login').is(':checked') ){
		facebook_login = 1;
	}
	if( $('#ldap_login').is(':checked') ){
		ldap_login = 1;
	}
	
	if( normal_login == 0 && openid_login == 0 && facebook_login == 0 && ldap_login == 0 ){
		return;
	}
	
	var data = {'command': 'login', 'login_redirect':$('#login_redirect').val(),'logout_redirect' : $('#logout_redirect').val(), 'registration_redirect' : $('#registration_redirect').val(),
		'normal_login' : normal_login,'openid_login' : openid_login,'facebook_login' : facebook_login,'facebook_app_id' : $('#facebook_app_id').val(),ldap_login : ldap_login,
		'ldap_server' : $('#ldap_server').val(),'ldap_port' : $('#ldap_port').val()
	};
	
	$.post(settings.address,data);
	$('#notice').html(languageAdmin.admin_settings_saved);
}
Settings.prototype.sessionsInit = function(){
	$('#settings_sessions_save').click(function(){ settings.sessionsSave(); });
}
Settings.prototype.sessionsSave = function(){
	var fields = new Array('session_name','session_path','session_expire');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	var data = {'command': 'sessions', 'session_name': $('#session_name').val(),'session_path' : $('#session_path').val(), '#session_expire' : $('#session_expire').val()};
		
	$.post(settings.address,data);
	$('#notice').html(languageAdmin.admin_settings_saved);
}
Settings.prototype.databaseInit = function(){
	$('#settings_database_save').click(function(){ settings.databaseCheck(); });
}
Settings.prototype.databaseCheck = function(){
	var data = {'command':'databaseCheck', 'type' : $('#type').val(),'username':$('#username').val(),'password' : $('#password').val(),'database' : $('#database').val(),
			'host':$('#host').val(),'port' : $('#port').val()};
	
	var fields = new Array('username','password','database','host','port');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	$('#notice').removeClass('notice errorNotice').html('Bezig met controleren van de database gegevens.');
	$.post(settings.address,data,function(response){
		if( response == 0 ){
			$('#notice').addClass('errorNotice').html('De database gegevens zijn incorrect.');
		}
		else {
			settings.databaseSave();
		}
	})
}
Settings.prototype.databaseSave = function(){
	var data = {'command':'database', 'prefix':$('#prefix').val(), 'type' : $('#type').val(),'username':$('#username').val(),'password' : $('#password').val(),
			'database' : $('#database').val(),'host':$('#host').val(),'port' : $('#port').val()};
	
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
	$.post(settings.address,data);
}
Settings.prototype.cacheInit = function(){
  
}
Settings.prototype.cacheSave	= function(){
  
  
}

var settings = new Settings();
$(document).ready(function() {
	settings.init();
});
