class Backup {
	constructor(){
		this.address = "../modules/general/backup/";
	}

  init() {
	  $('#admin_backup_createbackup').click(() => {		  
		  admin.show(this.address + 'createBackupscreen', () => {
			  this. hideErrors();
	    	 this.startBackup('createFullBackup')
	    });
	  });
	  $('#admin_backup_partialbackup').click(() =>  {
		  admin.show(this.address + 'createBackupscreen', () => {
			  this. hideErrors();
	    	this.startBackup('createPartialBackup');
	    });
	  });
	  $('#admin_backup_cleanBackups').click(() => {
		  confirmBox.init(350, () => { this.removeBackups(); });
		  confirmBox.show(languageAdmin.backups_title, languageAdmin.backups_remove);
	  });
  }
  
  hideErrors(){
	  $('#maintenance #error').hide();
	  $('#maintenance #message').show();
  }
  
  showErrors(){
	  $('#maintenance #error').show();
	  $('#maintenance #message').hide();
  }
  
  startBackup (type) {
	  setTimeout(() =>  {
	    $.post(this.address +type, {},  (response) => {
	    	response = JSON.parse(response);
	    	
	    	if (response.status == 0) {
	    		this.showErrors();
	    		return;
	    	}
	    	
	    	this.downloadBackup(response.file);
	    });
	  }, 750);
	}
  
  downloadBackup(filename){
	  let address = this.address+'download?file='+filename;
	  window.location = address;
  }
  
  removeBackups(){
	  $.post(this.address+"removeBackups",{});
  }
}
 
let backup = new Backup();