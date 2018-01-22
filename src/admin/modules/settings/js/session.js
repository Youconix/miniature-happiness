class SettingsSession{
  constructor(language, validation){
    this.language = language;
    this.validation = validation;
  }

  init(){
    $('#settings_sessions_save').click(() => { this.save(); });
  }
  save(){
    let form = $('#session_form');
    
    if( !this.validation.validateForm(form.prop('id')) ){
      return;
    }
	
    let data = form.serializeArray();

    let address = form.prop('action');
    $.post(address,data);
    $('#notice').html(this.language.admin_settings_saved);
  }
}

var settingsSession;
$(document).ready(() => {
  settingsSession = new SettingsSession(languageAdmin, validation);
});