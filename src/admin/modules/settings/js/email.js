class SettingsEmail {
  constructor(language, validation) {
    this.language = language;
    this.validation = validation;    
    
    this.activeToggle;
    this.active;
    this.form;
  }

  init() {
    this.activeToggle = $('#smtp_active_slider');
    this.active = $('input[name="smtp_active"]');
    this.form = $('#email_form');
    
    this.activeToggle.click(() => {
      this.smtpToggle();
    });
    this.smtpToggle();
    
    $('#settings_email_save').click(() => {
      this.emailSave();
    });
  }
  smtpToggle() {
    if (this.active.val() === '1') {
      $('#smtp_settings').fadeIn(500);
    } else {
      $('#smtp_settings').fadeOut(500);
    }
  }
  emailSave() {
    if (this.active.val() !== '1') {
      $('#smtp_host').removeAttr('required');
      $('#smtp_username').removeAttr('required');
      $('#smtp_password').removeAttr('required');
      $('#smtp_port').removeAttr('required');
    } else {
      $('#smtp_host').attr('required', true);
      $('#smtp_username').attr('required', true);
      $('#smtp_password').attr('required', true);
      $('#smtp_port').attr('required', true);
    }

    let data = this.form.serializeArray();

    if (!this.validation.validateForm(this.form.prop('id'))) {
      return;
    }

    let address = this.prop.prop('action');
    $.post(address, data);

    $('#notice').html(this.language.admin_settings_saved);
  }
}

var settingsEmail;
$(document).ready(() => {
  settingsEmail = new SettingsEmail(languageAdmin, validation);
});