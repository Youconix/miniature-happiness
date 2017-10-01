<?php
define('NIV', '../../');
require (NIV . 'js/generalJS.php');

class AdminMaintenance extends GeneralJS
{

    protected function display()
    {
        $s_file = 'AdminMaintenance.prototype = new AdminMain();
		AdminMaintenance.prototype.constructor = AdminMaintenance;
				
		function AdminMaintenance(){    
			this.name	= "maintenance";
			this.pending	= "";
				
			AdminMaintenance.prototype.setPending	= function(name){
				this.pending	= name;
			    $("#"+name).attr("class","maintenancePending");
			    $("#"+name).html("' . $this->service_Language->get('language/admin/maintenance/pending') . '");
			}
			
			AdminMaintenance.prototype.setReady	= function(){				
		    	$("#"+this.pending).attr("class","maintenanceReady");
		    	$("#"+this.pending).html("' . $this->service_Language->get('language/admin/maintenance/ready') . '");
				
				this.pending	= "";
			}
		    
			AdminMaintenance.prototype.setError	= function(){
		    	$("#"+this.pending).attr("class","maintenanceError");
		    	$("#"+this.pending).html("' . $this->service_Language->get('language/admin/maintenance/error') . '");
		    	
		    	this.pending	= "";
			}
				
			AdminMaintenance.prototype.view	= function(){
				var _this   = this;		        
				$.get(this.name+".php",{"AJAX":"true","command":"index"},_this.loadScreen);
			}
				
		    AdminMaintenance.prototype.compressCSS    = function(){
		        this.setPending("css_compress");

				var _this       = this;
				$.get(this.name+".php",{"AJAX":"true","command":"result","action":"css"},_this.actionResult);
		    }
				
			AdminMaintenance.prototype.compressJS    = function(){
		        this.setPending("js_compress");
		
				var _this       = this;
				$.get(this.name+".php",{"AJAX":"true","command":"result","action":"js"},_this.actionResult);
		    }
				
			AdminMaintenance.prototype.checkDatabase    = function(){
		        this.setPending("check_database");
		
				var _this       = this;
				$.get(this.name+".php",{"AJAX":"true","command":"result","action":"checkDatabase"},_this.actionResult);
		    }
				
			 AdminMaintenance.prototype.optimizeDatabase    = function(){
		        this.setPending("optimize_database");
		
				var _this       = this;
				$.get(this.name+".php",{"AJAX":"true","command":"result","action":"optimizeDatabase"},_this.actionResult);
		    }
				
			AdminMaintenance.prototype.cleanStatsYear    = function(){
		        this.setPending("clean_stats_year");
		
				var _this       = this;
				var params		= {"AJAX": "true","command":"result","action":"cleanStatsYear"};
				$.post(this.name+".php",params,_this.actionResult);
		    }
				
			AdminMaintenance.prototype.cleanStatsMonth    = function(){
		        this.setPending("clean_stats_month");
		
				var _this       = this;
				var params		= {"AJAX": "true","command":"result","action":"cleanStatsMonth"};
				$.post(this.name+".php",params,_this.actionResult);
		    }
				
			AdminMaintenance.prototype.cleanLogs    = function(){
		        this.setPending("clean_logs");
		
				var _this       = this;
				var params		= {"AJAX":"true","command":"result","action":"cleanLogs"};
		        $.post(this.name+".php",params,_this.actionResult);
		    }
		    
		    AdminMaintenance.prototype.actionResult    = function(response){
				setTimeout(function(){
     				adminMaintenance.actionHandler(response);
 				},500);
		    }
				
			AdminMaintenance.prototype.actionHandler	= function(response){
				if( response == 1 ){
					adminMaintenance.setReady();
				}
				else {
					adminMaintenance.setError();
				}
			}
		}
		
		var adminMaintenance    = new AdminMaintenance();';
        echo ($s_file);
    }
}

$obj_AdminMaintenance = new AdminMaintenance();
unset($obj_AdminMaintenance);
?>
