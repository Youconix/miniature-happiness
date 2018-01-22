class SettingsCache{
  constructor(language, validation){
    this.language = language;
    this.validation = validation;
  }
  init(){
    this.form = $('#cache_form');
    this.address = this.form.prop('action');
    this.cacheActive = $('input[name="cacheActive"]');
    
    $('#cacheActive_slider').click(() => {
      this.toggleSlider();
    });
    this.toggleSlider();
    $('#settings_cache_save').click(() => {
      this.save();
    });
  }
  toggleSlider(){
    if(this.cacheActive.val() === '1' ){
	$('#cacheSettings').fadeIn(500);
      }
      else {
	$('#cacheSettings').fadeOut(500);
      }
  }
  save(){
    if( !this.validation.validateForm(this.form.prop('id')) ){
      return;
    }
      
    let data = this.form.serializeArray();
    console.debug(this.address);
    $.post(this.address,data);
    $('#notice').addClass('notice').html(this.language.admin_settings_saved);
  }
}
var settingsCache;
$(document).ready(() => {
  settingsCache = new SettingsCache(languageAdmin, validation);
});