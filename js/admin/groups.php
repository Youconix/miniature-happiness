<?php
define('NIV', '../../');
require (NIV . 'js/generalJS.php');

class AdminGroups extends GeneralJS
{

    protected function display()
    {
        $s_file = 'AdminGroups.prototype = new AdminMain();
		AdminGroups.prototype.constructor = AdminGroups;
		
		function AdminGroups() {
			this.name = "groups";
		
			AdminGroups.prototype.view = function() {
				var _this = this;
				$.get(this.name+".php",{"AJAX":"true","command":"index"},_this.loadScreen);
			}
		
			AdminGroups.prototype.addScreen = function() {
				var _this = this;
				$.get(this.name+".php",{"AJAX":"true","command" : "addScreen"},_this.loadScreen);
			}
			
			AdminGroups.prototype.add	= function(){
				var params	= this.validate(); 
				if( params == null)
					return;
		
				var _this = this;
				params["AJAX"]	= "true";
				params["command"]	= "add";
				
				$.post(this.name+".php",params,_this.addEditResult);
			}
			
			AdminGroups.prototype.addEditResult	= function(response){
				/* Reload general view */
				adminGroups.view();
			}
		
			AdminGroups.prototype.edit = function(id) {
				var _this = this;
				var params	= {"AJAX" : "true", "command":"getGroup","id": id};
				$.get(this.name+".php",params,_this.loadScreen);
			}
		
			AdminGroups.prototype.editSave = function() {
				var params	= this.validate(); 
				if( params == null)
					return;
				
				var _this = this;
				params["AJAX"]	= "true";
				params["command"]	= "edit";
				
				$.post(this.name+".php",params,_this.addEditResult);
			}
			
			AdminGroups.prototype.deleteGroup	= function(id){
				var _this = this;				
				var params	= {"command" : "delete", "id": id };
				$.post(this.name+".php",params,_this.addEditResult);
			}
		
			AdminGroups.prototype.viewUsers = function(id) {
				var _this = this;
				var params 	= {"AJAX" : "true", "command" : "viewUsers", "id" : id };
				$.get(this.name+".php",params,_this.loadScreen);
			}
			
			AdminGroups.prototype.validate	= function(type){
				var params	= {};
				$("#notice").html("");
				
				/* Check fields */
				if ( $("#id").length != 0 ){
					if( isNaN($("#id").val()) ){
						this.closeScreens();
						return null;
					}
					
					params["id"]	= $("#id").val(); 
				}
		
				var name = $.trim( $("#name").val() );
				var description = $.trim( $("#description").val() );
		
				var error = "";
				if (name == "") {
					error += "' . $this->service_Language->get('language/admin/groups/js/nameEmpty') . '<br/>";
				}
		
				if (description == "") {
					error += "' . $this->service_Language->get('language/admin/groups/js/descriptionEmpty') . '<br/>";
				}
		
				if (error != "") {
					$("#notice").html(error);
					return null;
				}
		
				params["default"] = 0;
				if( $("#default_1").is(":checked") )
					params["default"] = 1;
				
				params["name"]	= name;
				params["description"]	= description;
		
				return params;
			}
		
			AdminGroups.prototype.closeScreens = function() {
				$("#groups").css("display","none");
			}
		}
		
		var adminGroups = new AdminGroups();';
        
        echo ($s_file);
    }
}

$obj_AdminGroups = new AdminGroups();
unset($obj_AdminGroups);
?>