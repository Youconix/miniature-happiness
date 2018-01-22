class SettingsLanguage{
  constructor(language){
    this.language = language;
  }
  init(){
    $('#settings_language_save').click(() => {
      this.save();
    });
  }
  save(){
    let form = $('#language_form');
    let address = form.prop("action");
    let data = form.serializeArray();
    
    $.post(address, data);
    $('#notice').html(this.language.admin_settings_saved);
  }
}
var settingsLanguage;
$(document).ready(() => {
  settingsLanguage = new SettingsLanguage(languageAdmin);
});