function SettingsLanguage(){
	this.address = '../../admin/modules/settings/languages/';
}
SettingsLanguage.prototype.languagesInit  = function(){
	$('#admin_settings_newlanguages').click(function(){
		admin.show(settings.address_language+'install_language',settings.addLanguages);
	});
	$('#admin_settings_editLanguages').click(function(){
		admin.show(settings.address_language+'edit_language',settings.editLanguages);
	});
	
	$('#settings_database_save').click(function(){
		settingsLanguage.languagesSave();
	});
	$('#install_new_languages').click(function(){
		admin.show(settingsLanguage.address+'install_language',settingsLanguage.addLanguages);
	});
	$('#admin_settings_editLanguages').click(function(){
		admin.show(settingsLanguage.address+'edit_language',settingsLanguage.editLanguages);
	});
}
SettingsLanguage.prototype.languagesSave	=  function(){
	$('#notice').addClass('notice').html(languageAdmin.admin_settings_saved);
	$.post(settingsLanguage.address+'language',{'default_language':$('#defaultLanguage').val()});
}
SettingsLanguage.prototype.addLanguages	= function(){}
SettingsLanguage.prototype.editLanguages = function(){
	$('#language_tree li').each(function(){
		$(this).click(function(e){
			var item = $(this);
			settingsLanguage.treeClick(item);
			e.stopPropagation();
			return false;
		});
	});
	
	$('#current_languagefile').on('change',function(){
		var file = $(this).val();
		admin.show(settingsLanguage.address+'edit_language?file='+file,settingsLanguage.editLanguages);
	});
}
SettingsLanguage.prototype.treeClick = function(item){
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
		settingsLanguage.openLeaf(item);
	}
}
SettingsLanguage.prototype.openLeaf = function(item){
	var file = $('#current_languagefile').val();
	var path = item.data('path');
	
	admin.show(settingsLanguage.address+'edit_language_form?file='+file+'&path='+path,settingsLanguage.openLeafInit);
}
SettingsLanguage.prototype.openLeafInit = function(){
	var i=1;
	while( $('#editor'+i).length > 0 ){
		CKEDITOR.replace( 'editor'+i,{'width':'99%','enterMode' : CKEDITOR.ENTER_BR} );
		i++;
	}
	
	$('#languageEditor_back').click(function(){
		var file = $('#file').val();
		admin.show(settingsLanguage.address+'edit_language?file='+file,settingsLanguage.editLanguages);
	});
	$('#languageEditorSave').click(function(){
		settingsLanguage.languageEditorSave();
	});
}
SettingsLanguage.prototype.languageEditorSave	= function(){
	var languages = {};
	var i = 1;
	var language;
	var content;
	while( $('#editor'+i).length != 0 ){
		language = $('#editor'+i).data('id');
		content = CKEDITOR.instances['editor'+i].getData();		
		languages[language] = content;
		
		i++;
	}
	
	$.post(settingsLanguage.address+'edit_language_save',{
		'data' :languages,
		'path' : $('#path').val(),
		'file' : $('#file').val()
	});
	$('#notice').html(languageAdmin.languageEditorSaved);
}
var settingsLanguage = new SettingsLanguage();