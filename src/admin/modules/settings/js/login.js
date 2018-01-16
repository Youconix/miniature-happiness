class SettingsLogin {
  constructor(language, validation) {
    this.language = language;
    this.validation = validation;
    this.guards = {};
    this.form = null;
    this.defaultGuard;
    this.defaultGuardItem;
  }

  init(guards, defaultGuard) {
    this.guards = guards;
    this.form = $('#login_settings');
    this.defaultGuardItem = $('select[name="default_guard"]');
    this.defaultGuard = defaultGuard;

    for (let name in guards) {
      if (guards[name].config) {
	$('#' + name + '_enabled_slider').click((event) => {
	  this.toggleConfig($(event.currentTarget));
	  this.updateDefaultGuard();
	});
	this.toggleConfig($('#' + name + '_enabled_slider'));
      }
    }
    
    $('#settings_login_save').click(() => {
      this.save();
    });
    
    this.defaultGuardItem.on('change', () => {
      this.defaultGuard = this.defaultGuardItem.val();
    });
    this.updateDefaultGuard();
  }
  toggleConfig(field) {
    let name = field.prop('id').replace('_enabled_slider', '');
    if ($('input[name="'+name+'_enabled"]').val() === '1') {
      $('#' + name + "_config").fadeIn(500);
    } else {
      $('#' + name + "_config").fadeOut(500);
    }
  }
  updateDefaultGuard(){
    this.defaultGuardItem.empty();
    
    let selected;
    for (let name in guards) {
      selected = ((name === this.defaultGuard) ? 'selected="selected"' : '');
      
      if ($('input[name="' + name + '_enabled"]').val() === '1') {
	this.defaultGuardItem.append('<option value="'+name+'" '+selected+'>'+guards[name].name+'</option>');
      }
    }
  }
  save() {
    for (let name in guards) {
      if (!guards[name].config) {
	continue;
      }
      $('input[name="' + name + '_enabled"]').find('input,select').each((i, item) => {
	$(item).removeProp('required');
	if ($('input[name="' + name + '_enabled"]').val() === '1') {
	  $('input[name="' + name + '_enabled"]').find('input,select').each((i, item) => {
	    $(item).addProp('required', true);
	  });
	}
      });
    }

    if (!this.validation.validateForm(this.form.prop('id'))) {
      return;
    }
    
    let enabledLogins = [];
    for (let name in guards) {
      if ($('input[name="' + name + '_enabled"]').val() === '1') {
	enabledLogins.push(name);
      }
    }

    if (enabledLogins.length === 0) {
      return;
    }

    let data = this.form.serializeArray();
    let address = this.form.prop('action');
    $.post(address, data);
    $('#notice').html(this.language.admin_settings_saved);
  }
}

var settingsLogin;
$(document).ready(() => {
  settingsLogin = new SettingsLogin(languageAdmin, validation);
});