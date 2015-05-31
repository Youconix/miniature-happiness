function Settings() {	
	this.address_database = '../../admin/modules/settings/database/';
	this.address_cache = '../../admin/modules/settings/cache/';
	this.address_language = '../../admin/modules/settings/languages/';
}
Settings.prototype.address = function(){
	return this.address;
}
Settings.prototype.init = function() {
	$('#admin_settings_email h2').click(function(){
		admin.show(settingsEmail.address+'showemail',settingsEmail.init);
	});
	$('#admin_settings_general h2').click(function(){
		admin.show(settingsGeneral.address+'general',settingsGeneral.init);
	});
	$('#admin_settings_ssl h2').click(function(){
		admin.show(settingsGeneral.address+'ssl',settingsGeneral.sslInit);
	});
	$('#admin_settings_login h2').click(function(){
		admin.show(settingsSession.address+'login',settingsSession.loginInit);
	});
	$('#admin_settings_sessions h2').click(function(){
		admin.show(settingsSession.address+'sessions',settingsSession.init);
	});
	$('#admin_settings_database h2').click(function(){
		admin.show(settings.address_database+'database',settings.databaseInit);
	});
	$('#admin_settings_cache h2').click(function(){
		admin.show(settings.address_cache+'cache',settings.cacheInit);
	});
	$('#admin_settings_languages h2').click(function(){
		admin.show(settings.address_language+'language',settings.languagesInit);
	});
	$('#admin_settings_newlanguages').click(function(){
		admin.show(settings.address_language+'install_language',settings.addLanguages);
	});
	$('#admin_settings_editLanguages').click(function(){
		admin.show(settings.address_language+'edit_language',settings.editLanguages);
	});
}
var settings = new Settings();
$(document).ready(function() {
	settings.init();
});

function SettingsEmail(){
	this.address = '../../admin/modules/settings/email/';
}
SettingsEmail.prototype.init = function(){
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
SettingsEmail.prototype.emailSave = function(){
	var data = {
			'email_name' : $('#email_name').val(),'email_email' : $('#email_email').val(),'smtp_host' : $('#smtp_host').val(),
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
	
	$.post(settingsEmail.address+'showemail',data);
	
	$('#notice').html(languageAdmin.admin_settings_saved);
}
var settingsEmail = new SettingsEmail();


function SettingsGeneral(){
	this.address = '../../admin/modules/settings/general/';	
	this.SSL_DISABLED = 0;
	this.SSL_LOGIN = 1;
	this.SSL_ALL = 2;
}
SettingsGeneral.prototype.init = function(){
	$('#settings_general_save').click(function(){ settings.generalSave(); });
}
SettingsGeneral.prototype.generalSave = function(){
	var data = {
		'name_site' : $('#name_site').val(), 'site_url' : $('#site_url').val(),'site_base' : $('#site_base').val(),'template' : $('#template').val(),
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
	
	$.post(settingsGeneral.address+'general',data);
	
	$('#notice').html(languageAdmin.admin_settings_saved);
}
SettingsGeneral.prototype.sslInit	= function(){
	$('#settings_ssl_save').click(function(){
		settingsGeneral.sslSave();
	});
}
SettingsGeneral.prototype.sslSave= function(){
	var currentSSL = $('#current_ssl').val();
	
	var ssl;
	$('input[name="ssl"]').each(function(){
		if( $(this).is(':checked') ){
			ssl = $(this).val();
		}
	});
	
	if( currentSSL != ssl ){
		$.post(settingsGeneral.address+'ssl',{'ssl':ssl});
	}
	
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
}
var settingsGeneral = new SettingsGeneral();


function SettingsSession(){
	this.address = '../../admin/modules/settings/session/';
}
SettingsSession.prototype.loginInit = function(){
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
	$('#settings_login_save').click(function(){ settingsSession.loginSave(); });
}
SettingsSession.prototype.loginSave = function(){
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
	
	var data = {'login_redirect':$('#login_redirect').val(),'logout_redirect' : $('#logout_redirect').val(), 'registration_redirect' : $('#registration_redirect').val(),
		'normal_login' : normal_login,'openid_login' : openid_login,'facebook_login' : facebook_login,'facebook_app_id' : $('#facebook_app_id').val(),ldap_login : ldap_login,
		'ldap_server' : $('#ldap_server').val(),'ldap_port' : $('#ldap_port').val()
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



Settings.prototype.databaseInit = function(){
	$('#settings_database_save').click(function(){ settings.databaseCheck(); });
}
Settings.prototype.databaseCheck = function(){
	var data = {'type' : $('#type').val(),'username':$('#username').val(),'password' : $('#password').val(),'database' : $('#database').val(),
			'host':$('#host').val(),'port' : $('#port').val()};
	
	var fields = new Array('username','password','database','host','port');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	$('#notice').removeClass('notice errorNotice').html('Bezig met controleren van de database gegevens.');
	$.post(settings.address_database+'databaseCheck',data,function(response){
		if( response == 0 ){
			$('#notice').addClass('errorNotice').html('De database gegevens zijn incorrect.');
		}
		else {
			settings.databaseSave();
		}
	})
}
Settings.prototype.databaseSave = function(){
	var data = {'prefix':$('#prefix').val(), 'type' : $('#type').val(),'username':$('#username').val(),'password' : $('#password').val(),
			'database' : $('#database').val(),'host':$('#host').val(),'port' : $('#port').val()};
	
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
	$.post(settings.address_database+'database',data);
}
Settings.prototype.cacheInit = function(){
	$('#cacheActive').on('change',function(){
		if( $(this).is(':checked') ){
			$('#cacheSettings').show();
		}
		else {
			$('#cacheSettings').hide();
		}
	});
	$('#settings_cache_save').click(function(){
		settings.cacheSave();
	});
	$('#no_cache_submit').click(function(){
		settings.addNoCache();  
	});
	$('#nonCacheList tr').click(function(){
		var item = $(this);
		settings.deleteNoCache(item);
	});
}
Settings.prototype.cacheSave	= function(){
	var data = {
		 'cache' : 0, 'expire' : $('#expire').val()
	};
	if( $('#cacheActive').is(':checked') ){
		data['cache'] = 1;
	}
  
	$.post(settings.address_cache+'cache',data);
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
}
Settings.prototype.addNoCache	= function(){
	var cacheItem = $.trim($('#noCachePage').val());
	
	$('#noCachePage').val('');
	$.post(settings.address_cache+'addNoCache',{'page':cacheItem},function(response){
		response = JSON.parse(response);
		if( response.id != -1 ){
			$('#nonCacheList').append('<tr data-id="'+response.id+'"> '+
			'  <td><img src="'+response.style_dir+'images/icons/delete.png" alt="'+response.deleteText+'" title="'+response.deleteText+'"></td>'+
			'  <td>'+response.name+'</td> '+
			'</tr>');
		}
	});
}
Settings.prototype.deleteNoCache	= function(item){
	var id = item.data('id');
	confirmBox.init(350,function(){
		item.remove();
		$.post(settings.address_cache+'deleteNoCache',{'id':id});
	});
	confirmbox.show('Cache pagina verwijderen','Weet u zeker dat u deze pagina weer wilt cachen?');
}
Settings.prototype.languagesInit  = function(){
	$('#settings_database_save').click(function(){
		settings.languagesSave();
	});
	$('#install_new_languages').click(function(){
		admin.show(settings.address_language+'install_language',settings.addLanguages);
	});
	$('#admin_settings_editLanguages').click(function(){
		admin.show(settings.address_language+'edit_language',settings.editLanguages);
	});
}
Settings.prototype.languagesSave	=  function(){
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
	$.post(settings.address_language+'language',{'default_language':$('#defaultLanguage').val()});
}
Settings.prototype.addLanguages	= function(){
	
}
Settings.prototype.editLanguages = function(){
	$('#language_tree li').each(function(){
		$(this).click(function(e){
			var item = $(this);
			settings.treeClick(item);
			e.stopPropagation();
			return false;
		});
	});
}
Settings.prototype.treeClick = function(item){
	if( item.data('type') == 'tree' ){
		var child = item.children().filter('ul');
		if( child.hasClass('closed') ){
			item.children(':first').html('-');
			child.removeClass('closed').addClass('open');
		}
		else {
			item.children(':first').html('+');
			child.removeClass('open').addClass('closed');
		}
	}
	else {
		settings.openLeaf(item);
	}
}
Settings.prototype.openLeaf = function(item){
	var file = $('#current_languagefile').val();
	var path = item.data('path');
	
	admin.show(settings.address_language+'edit_language_form?file='+file+'&path='+path,settings.openLeafInit);
}
Settings.prototype.openLeafInit = function(){
	var i=1;
	while( $('#editor'+i).length > 0 ){
		console.log('starting editor '+i);
		CKEDITOR.replace( 'editor'+i );
		i++;
	}
}