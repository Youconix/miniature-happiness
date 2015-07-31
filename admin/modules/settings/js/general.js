function SettingsGeneral(){
	this.address = '../../admin/modules/settings/general/';	
	this.SSL_DISABLED = 0;
	this.SSL_LOGIN = 1;
	this.SSL_ALL = 2;
}
SettingsGeneral.prototype.init = function(){
	$('#settings_general_save').click(function(){ settingsGeneral.generalSave(); });
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
		$('#current_ssl').val(ssl);
	}
	
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
}
var settingsGeneral = new SettingsGeneral();