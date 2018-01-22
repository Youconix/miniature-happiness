class SettingsSSL {
  constructor(language, validation){
    this.SSL_DISABLED = 0;
    this.SSL_LOGIN = 1;
    this.SSL_ALL = 2;
    
    this.language = language;
    this.validation = validation;
  }
  init(){
    $('#settings_ssl_save').click(() => {
      this.sslSave();
    });
  }
  sslSave(){
    let currentSSL = $('#current_ssl').val();
	
    let ssl;
    $('input[name="ssl"]').each((i, item) => {
      item = $(item);
      if( item.is(':checked') ){
	ssl = item.val();
      }
    });
	
    let address = $('#ssl_form').prop('action');
    let data = $('#ssl_form').serializeArray();
    $.post(address,data);
    $('#current_ssl').val(ssl);
	
    $('#notice').addClass('notice').html(this.language.admin_settings_saved);
  }
}

var settingsSSL;
$(document).ready(() => {
  settingsSSL = new SettingsSSL(languageAdmin, validation);
});