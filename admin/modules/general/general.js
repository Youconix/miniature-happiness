function General(){
	this.address	= '../..//admin/modules/general/';
}
General.prototype.init	= function(){
	
}
General.prototype.show	= function(page){
	var _this = this;
	
	switch(page){
		case 'users' :
			$.get(this.address+'users.php',_this.bigScreen);
			break;
			
		case 'groups':
			$.get(this.address+'groups.php',_this.bigScreen);
			break;
			
		case 'page_right':
			$.get(this.address+'pages.php',_this.bigScreen);
			break;
			
		case 'logs' :
			$.get(this.address+'logs.php',_this.smallScreen);
			break;
			
		case 'updates' :
			$.get(this.address+'updates.php',_this.smallScreen);
			break;
			
		case 'backup' :
			$.get(this.address+'maintenance.php',_this.smallScreen);
		break;
	}
}
General.prototype.bigScreen	= function(response){
	alert(response);
}
General.prototype.smallScreen	= function(response){
	
}
var general = new General();
general.init();