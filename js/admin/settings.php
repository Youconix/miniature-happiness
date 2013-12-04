<?php 
define('NIV','../../');
require(NIV.'js/generalJS.php');

class AdminLogs extends GeneralJS{
	protected function display(){
		$s_file = 'AdminSettings.prototype = new AdminMain();
		AdminSettings.prototype.constructor = AdminSettings;
				
		function AdminSettings(){
			this.name	= "settings";
		    this.SQLChecked = true;
				
			AdminSettings.prototype.view	= function(){
				var _this	= this;
				$.get(this.name+".php",{"AJAX":"true","command":"index"},_this.loadScreen);	
			}
		    
		    AdminSettings.prototype.checkSettings = function(){
				$("#sqlError").html("");
				
		        /* Check fields */
		        var base = $("#base").val();
		        if( base != "" && !base.endsWith("/") ){
		            $("#base").val(bar+"/");
		        }
		        
		        var error   = "";
		        if( $.trim( $("#url").val() ) == "" ){
		            error   += "'.$this->service_Language->get('language/admin/settings/js/urlEmpty').'<br/>";
		        }
		                
		        if( $.trim( $("#timezone").val() ) == "" ){
		            error   += "'.$this->service_Language->get('language/admin/settings/js/timezoneEmpty').'<br/>";
		        }
		        else if( $("#timezone").val().indexOf("/") == -1 ){
		            error   += "'.$this->service_Language->get('language/admin/settings/js/timezoneInvalid').'<br/>";
		        }
		        
		        if( $.trim( $("#sessionExpire").val() ) != "" && isNaN($("#sessionExpire").val()) ){
		            error   += "'.$this->service_Language->get('language/admin/settings/js/sessionInvalid').'<br/>";
		        }
		        
		        if( error != "" ){
		            $("#sqlError").html(error);
		            return false;
		        }
		        
		        if( !this.checkSQL() )
		            return false;
		            		
		        if( !this.SQLChecked ) {
		            if( !this.SQLChecked )
		                this.validateSQL();
		        }}
				
					var params	= {};
					params["AJAX"]	= "true";
					params["command"] = "save";
		            params["base"] = $("#base").val();
					params["url"] = $("#url").val();
					params["timezone"] = $("#timezone").val();
		            params["sessionName"] = $("#sessionName").val();
					params["sessionPath"] = $("#sessionPath").val();
		            params["sessionExpire"] = $("#sessionExpire");
					params["language"] = $("#language").val();
					params["template"] = $("#template").val();
		            params["sqlUsername"] = $("#sqlUsername").val();
					params["sqlPassword"] = $("#sqlPassword").val();
					params["sqlHost"] = $("#sqlHost").val();
					params["sqlPort"] = $("#sqlPort").val();
					params["sqlDatabase"] = $("#sqlDatabase").val();
		            params["databaseType"] = $("#sqlType").val();
				        
		            var _this       = this;
					$.post(this.name+".php",params,_this.saveResult);
		        }
		        
		        return true;
		    }
		    
		    AdminSettings.prototype.saveResult  = function(response){
		        $("#notice").html("'.$this->service_Language->get('language/admin/settings/js/saved').'");
		    }
		    
		    AdminSettings.prototype.checkSQL    = function(){		        		
		        if( $.trim( $("#sqlUsername").val() ) == "" || $.trim( $("#sqlPassword").val() ) == "" || 
					$.trim( $("#sqlDatabase").val() ) == "" || $.trim( $("#sqlHost").val() ) == "" ){
		            $("#sqlError").html("'.$this->service_Language->get('language/admin/settings/js/databaseEmpty').'");
		            return false;
		        }
		        
		        if( $.trim( $("#sqlPort").val() ) != "" && isNaN($("#sqlPort").val()) ){
		            $("#sqlError").html("'.$this->service_Language->get('language/admin/settings/js/databasePortInvalid').'");
		            return false;
		        }
		        
		        return true;
		    }
		    		    
		    AdminSettings.prototype.validateSQL = function(){
		        if( !this.checkSQL() ){
		            this.SQLChecked    = false;
		        	return;    
		        }
		            		
		        var username    = $("#sqlUsername").val();
		        var password    = $("#sqlPassword").val();
		        var host        = $("#sqlHost").val();
		        var port        = $("#sqlPort").val();
		        var database    = $("#sqlDatabase").val();
		        var type        = $("#sqlType").val();
				
				var params	= {};
				params["AJAX"]	= "true";
				params["command"] = "checkSQL";
				params["sqlUsername"] = username;
		        params["sqlPassword"] = password;
				params["sqlHost"] = host;
				params["sqlPort"] = port;
				params["sqlDatabase"] = database;
				params["databaseType"] = type;				
				
		        var _this       = this;		
				$.post(this.name+".php",params,_this.validateSQLResult);
		    }
		    
		    AdminSettings.prototype.validateSQLResult = function(response){
		        response	= JSON.parse(response);
		        
		        if( response[0].code == 0 ){
		            this.SQLChecked    = false;
		            $("#sqlError").html("'.$this->service_Language->get('language/admin/settings/js/databaseInvalid').'");
		        }
		        else {
		            $("#sqlError").html("");
		            this.SQLChecked = true;
		        }
		    }
		}
		
		var adminSettings   = new AdminSettings();';
		echo($s_file);
	}
}

$obj_AdminLogs	 = new AdminLogs();
unset($obj_AdminLogs);
?>