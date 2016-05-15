function Maintenance(){
	this.address = "../modules/general/maintenance/";
	this.action = '';
}
Maintenance.prototype.init = function(){
	$('#admin_general_optimize_database').click(function(){
		maintenance.action = 'optimize_database';
		admin.show(maintenance.address + 'index', maintenance.mainScreen);
	});
	$('#admin_general_checkDatabase').click(function(){
		maintenance.action = 'check_database';
		admin.show(maintenance.address + 'index', maintenance.mainScreen);
	});
	$('#admin_general_stats').click(function(){
		maintenance.action = 'stats';
		admin.show(maintenance.address + 'index', maintenance.mainScreen);
	});
}
Maintenance.prototype.mainScreen = function(){
	$('#maintenance_check_database_label').click(function(){
		maintenance.showPending('maintenance_check_database');
		
		setTimeout(function(){
			$.post(maintenance.address+'result',{'action':'checkDatabase'},function(response){
				maintenance.checkResponse(response,'maintenance_check_database');
			});
		},750);
	});
	
	$('#maintenance_optimize_database_label').click(function(){
		maintenance.showPending('maintenance_optimize_database');
		
		setTimeout(function(){
			$.post(maintenance.address+'result',{'action':'optimizeDatabase'},function(response){
				maintenance.checkResponse(response,'maintenance_optimize_database');
			});
		},750);
	});
	
	$('#maintenance_clean_stats_label').click(function(){
		maintenance.showPending('maintenance_clean_stats');
		
		setTimeout(function(){
			$.post(maintenance.address+'result',{'action':'cleanStats'},function(response){
				maintenance.checkResponse(response,'maintenance_clean_stats');
			});
		},750);
	});
	
	if( maintenance.action == 'check_database' ){
		$('#maintenance_check_database_label').trigger('click');
	}
	else if( maintenance.action == 'optimize_database' ){
		$('#maintenance_optimize_database_label').trigger('click');
	}
	else if( maintenance.action == 'stats' ){
		$('#maintenance_clean_stats_label').trigger('click');
	}
}
Maintenance.prototype.checkResponse = function(response,field){
	if( response != 1 ){
		maintenance.showError(field);
	}
	else {
		maintenance.showReady(field);
	}
}
Maintenance.prototype.showReady	= function(field){
	$('#'+field).removeClass('maintenancePending').removeClass('maintenanceError').addClass('maintenanceReady');
	$('#'+field).html(languageAdmin.maintenance_ready);
}
Maintenance.prototype.showPending	= function(field){
	$('#'+field).removeClass('maintenanceError').removeClass('maintenanceReady').addClass('maintenancePending');
	$('#'+field).html(languageAdmin.maintenance_pending);
}
Maintenance.prototype.showError	= function(field){
	$('#'+field).removeClass('maintenancePending').removeClass('maintenanceReady').addClass('maintenanceError');
	$('#'+field).html(languageAdmin.maintenance_error);
}

var maintenance = new Maintenance();