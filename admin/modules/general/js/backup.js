function Backup(){
	this.address = "../modules/general/backup/";
}
Backup.prototype.init = function(){
	$('#admin_backup_createbackup').click(function(){
		admin.show(backup.address + 'createBackupscreen', backup.backup);
	});
	$('#admin_backup_restorebackup').click(function(){
		admin.show(backup.address + 'restoreBackupScreen', backup.restoreBackup);
	});
}
Backup.prototype.backup = function(){	
	setTimeout(function(){
		$.post(backup.address+'createBackup',{}, function(response){
			
		});
	},750);
}
Backup.prototype.restoreBackup = function(){
	
}

var backup = new Backup();