function General(){
	this.address	= '../../admin/modules/general/';
}
General.prototype.init	= function(){  
  $('#admin_general_users h2, #admin_general_users2').click(function(){ general.showUsers() } );
  $('#admin_general_add_user').click(function(){ admin.show(users.url+"?command=addScreen",users.addUserScreen()); });
  
  $('#admin_general_groups h2, #admin_general_groups2').click(function(){ general.showGroups() } );
  $('#admin_general_group_add').click(function(){  admin.show(groups.url+'?command=addScreen',groups.addScreen);  });
  
  $('#admin_general_page_rights h2').click(function(){ general.showPageRights() } );
  $('#admin_general_logs h2').click(function(){ general.showLogs() } );
  $('#admin_general_updates h2').click(function(){ general.showUpdates() } );
  $('#admin_general_backup h2').click(function(){ general.showBackup() } );
  $('#admin_general_maintenance h2').click(function(){ general.showMaintenance() } );
  $('#admin_general_cache h2').click(function(){ general.showCache() } );
  $('#admin_general_modules h2').click(function(){ general.showModules() } );
}
General.prototype.showUsers = function(){
  admin.show(this.address+'users.php?command=index',users.init);
}
General.prototype.showGroups = function(){
  admin.show(this.address+'groups.php',groups.init);
}
General.prototype.showPageRights = function(){
  admin.show(this.address+'pages.php',pageRights.init);
}
General.prototype.showLogs = function(){
  admin.show(this.address+'logs.php');
}
General.prototype.showUpdates = function(){
  admin.show(this.address+'updates.php');
}
General.prototype.showBackup = function(){
  admin.show('maintenance.php?view=backup');
}
General.prototype.showMaintenance = function(){
  admin.show('maintenance.php');
}
General.prototype.showCache	= function(){
  admin.show('cache.php');
}
General.prototype.showModules	= function(){
	  admin.show('modules.php');
	}

var general = new General();
$(document).ready(function(){
  general.init();
});