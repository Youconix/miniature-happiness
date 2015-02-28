function General(){
	this.address	= '../../admin/modules/general/';
}
General.prototype.init	= function(){  
  $('#admin_general_users h2').click(function(){ general.showUsers() } );
  $('#admin_general_users_add_user').click(function(){ users.showAddUserScreen(); });
  
  $('#admin_general_groups h2').click(function(){ general.showGroups() } );
  $('#admin_general_page_rights h2').click(function(){ general.showPageRights() } );
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
  admin.show(this.address+'groups.php?command=index',groups.init);
}
General.prototype.showPageRights = function(){
  admin.show(this.address+'pages.php?command=index',pageRights.init);
}
General.prototype.showUpdates = function(){
  admin.show(this.address+'updates.php');
}
General.prototype.showBackup = function(){
  admin.show('maintenance.php?command=backup');
}
General.prototype.showMaintenance = function(){
  admin.show('maintenance.php');
}
General.prototype.showCache	= function(){
  admin.show('cache.php');
}
General.prototype.showModules	= function(){
	  admin.show(this.address+'modules.php?command=index',modules.init);
	}

var general = new General();
$(document).ready(function(){
  general.init();
});