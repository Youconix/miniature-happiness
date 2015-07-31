function SettingsDatabase(){
	this.address = '../../admin/modules/settings/database/';
}
SettingsDatabase.prototype.init = function(){
	$('#settings_database_save').click(function(){ settingsDatabase.databaseCheck(); });
}
SettingsDatabase.prototype.databaseCheck = function(){
	var data = {'type' : $('#type').val(),'username':$('#username').val(),'password' : $('#password').val(),'database' : $('#database').val(),
			'host':$('#host').val(),'port' : $('#port').val()};
	
	var fields = new Array('username','password','database','host','port');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	$('#notice').removeClass('notice errorNotice').html(languageAdmin.settings_check_database);
	$.post(settingsDatabase.address+'databaseCheck',data,function(response){
		if( response == 0 ){
			$('#notice').addClass('errorNotice').html(languageAdmin.settings_check_database_invalid);
		}
		else {
			settingsDatabase.databaseSave();
		}
	})
}
SettingsDatabase.prototype.databaseSave = function(){
	var data = {'prefix':$('#prefix').val(), 'type' : $('#type').val(),'username':$('#username').val(),'password' : $('#password').val(),
			'database' : $('#database').val(),'host':$('#host').val(),'port' : $('#port').val()};
	
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
	$.post(settingsDatabase.address+'database',data);
}
var settingsDatabase = new SettingsDatabase();