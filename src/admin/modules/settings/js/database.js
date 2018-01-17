class SettingsDatabase {
  constructor(language, validation) {
    this.language = language;
    this.validation = validation;

    this.form;
    this.address;
    this.check;
  }
  init() {
    this.form = $('#database_form');
    this.address = this.form.prop('action');
    this.check = this.form.data('check');

    $('#settings_database_save').click(() => {
      this.databaseCheck();
    });
  }
  databaseCheck() {
    if (!this.validation.validateForm(this.form.prop('id'))) {
      return;
    }

    $('#notice').removeClass('notice errorNotice').html(this.language.settings_check_database);

    let data = this.form.serializeArray();

    $.post(this.check, data, (response) => {
      if (parseInt(response) === 0) {
	$('#notice').addClass('errorNotice').html(this.language.settings_check_database_invalid);
      } else {
	this.databaseSave();
      }
    });
  }
  databaseSave() {
    let data = this.form.serializeArray();

    $('#notice').addClass('notice').html(this.language.admin_settings_saved);
    $.post(this.address, data);
  }
}
var settingsDatabase;
$(document).ready(() => {
  settingsDatabase = new SettingsDatabase(languageAdmin, validation);
});