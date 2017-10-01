function General() {
  this.address = '../../admin/modules/general/';
}
General.prototype.init = function () {
  $('#admin_general_users h2').click(function () {
    general.showUsers()
  });
  $('#admin_general_users_add_user').click(function () {
    users.showAddUserScreen();
  });

  $('#admin_general_groups h2').click(function () {
    general.showGroups();
  });
  $('#admin_general_page_rights h2').click(function () {
    general.showPageRights();
  });
  $('#admin_general_updates h2').click(function () {
    general.showUpdates();
  });
  $('#admin_general_modules h2').click(function () {
    general.showModules();
  });

  cache.init();
  groups.init();
  maintenance.init();
  updater.init();
  backup.init();
  users.init();
  pageRights.init();
}
General.prototype.showUsers = function () {
  admin.show(this.address + 'users/index', users.init);
}
General.prototype.showGroups = function () {
  admin.show(this.address + 'groups/index', groups.init);
}
General.prototype.showPageRights = function () {
  admin.show(this.address + 'pages/index', pageRights.init);
}
General.prototype.showModules = function () {
  admin.show(this.address + 'modules/index', modules.init);
}

var general = new General();
$(document).ready(function () {
  general.init();
});