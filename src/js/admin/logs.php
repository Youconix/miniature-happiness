<?php
define('NIV', '../../');
require (NIV . 'js/generalJS.php');

class AdminLogs extends GeneralJS
{

    protected function display()
    {
        $s_file = 'AdminLogs.prototype = new AdminMain();
		AdminLogs.prototype.constructor = AdminLogs;
				
		function AdminLogs(){
			this.name	= "logs";
				
			AdminLogs.prototype.view	= function(){
				var _this   = this;		
				$.get(this.name+".php",{"AJAX":"true","command":"index"},_this.loadScreen);
			}
				
		    AdminLogs.prototype.viewLog = function(name){
		        var _this   = this;		        
		        $.get(this.name+".php",{"AJAX":"true","command":"view","name":name},_this.loadScreen);
		    }
		    
		    AdminLogs.prototype.deleteLog = function(name){
		        var _this   = this;
		        
		        if( !confirm("' . $this->service_Language->get('language/admin/logs/jsDelete') . '") ){
		            return;
		        }
		        		
		        var params	= {"AJAX" : "true", "command":"delete", "name" : name};
		        $.post(this.name+".php",params,_this.displayNull);
		    }
		    
		    AdminLogs.prototype.displayNull = function(response){
				adminLogs.view();		        
		    }
		    
		    AdminLogs.prototype.downloadLog = function(name){
				window.location = this.name+".php?AJAX=true&command=download&name="+name;
		    }
		}
		
		var adminLogs   = new AdminLogs();';
        
        echo ($s_file);
    }
}

$obj_AdminLogs = new AdminLogs();
unset($obj_AdminLogs);
?>