function Modules() {
  this.url = '../modules/general/modules.php';
  this.item = null;
}
Modules.prototype.init = function () {
  $('#installed_modules tbody tr').each(function () {
    $(this).click(function () {
      modules.item = $(this);
      modules.deleteModule();
    });
  });

  $('#upgradable_modules tbody tr').each(function () {
    $(this).click(function () {
      modules.item = $(this);
      modules.upgrade();
    });
  });

  $('#new_modules tbody tr').each(function () {
    $(this).click(function () {
      modules.item = $(this);
      modules.install();
    });
  });
}
Modules.prototype.deleteModule = function () {
  var name = modules.item.data('name');

  if (name === 'general' || name === 'settings' || name === 'statistics') {
    /*
     * Framework modules Do not remove
     */
    return;
  }

  confirmBox.init(250, modules.deleteModuleCallback);
  confirmBox.show(languageAdmin.modules_delete_title,
	  languageAdmin.modules_delete.replace('[name]', name));
}
Modules.prototype.deleteModuleCallback = function () {
  var id = modules.item.data('id');

  $.post(this.address, {
    'command': 'delete',
    'id': id
  }, function () {
    general.showModules();
  });
}
Modules.prototype.upgrade = function () {
  var name = modules.item.data('name');

  confirmBox.init(250, modules.upgradeCallback);
  confirmBox.show(languageAdmin.modules_upgrade_title,
	  languageAdmin.modules_upgrade.replace('[name]', name));
}
Modules.prototype.upgradeCallback = function () {
  var id = modules.item.data('id');

  $.post(this.address, {
    'command': 'upgrade',
    'id': id
  }, function () {
    general.showModules();
  });
}
Modules.prototype.install = function () {
  var name = modules.item.data('name');

  confirmBox.init(250, modules.installCallback);
  confirmBox.show(languageAdmin.modules_install_title,
	  languageAdmin.modules_install.replace('[name]', name));
}
Modules.prototype.installCallback = function () {
  var name = modules.item.data('name');

  $.post(this.address, {
    'command': 'install',
    'name': name
  }, function () {
    general.showModules();
  });
}

var modules = new Modules();