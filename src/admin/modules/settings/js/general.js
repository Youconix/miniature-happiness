class SettingsGeneral {
  constructor(language, validation){
    this.SSL_DISABLED = 0;
    this.SSL_LOGIN = 1;
    this.SSL_ALL = 2;
    
    this.language = language;
    this.validation = validation;
  }
  init(){
    $('#settings_general_save').click(() => { this.generalSave(); });
    this.form = $('#general_form'); 
  }
  generalSave(){
    let data = this.form.serializeArray();
	
    if( $('#logger').val() === 'default' ){
      $('#log_location').attr('required',true);
    }
    else {
      $('#log_location').removeAttr('required');
    }
	
    if( !this.validation.validateForm(this.form.prop('id')) ){
      return;
    }
	
    let address = this.form.prop('action');
    $.post(address,data);
	
    $('#notice').html(this.language.admin_settings_saved);
  }
}

var settingsGeneral;
$(document).ready(() => {
  settingsGeneral = new SettingsGeneral(languageAdmin, validation);
});