<?php 
define('NIV','../../');
require(NIV.'js/generalJS.php');

class AdminUsers extends GeneralJS {
	protected function display(){
		$s_file = 'AdminUsers.prototype = new AdminMain();
		AdminUsers.prototype.constructor = AdminUsers;
		function AdminUsers(){
			this.name = "users";
			this.usernameValid	= false;
			this.emailValid	= false;
				
			AdminUsers.prototype.view	= function(page){
				page = page || 1
			
				var _this = this;
				$.get(this.name+".php",{"AJAX":"true","command":"index","page":page},_this.loadScreen);
			}
				
			AdminUsers.prototype.search	= function(){
				var _this	= this;
				var username	= $("#searchUsername").val();
				$.get(this.name+".php",{"AJAX":"true","command":"search","username":username},_this.loadScreen);
			}
				
		    AdminUsers.prototype.newUser = function(){
				this.usernameValid	= false;
				this.emailValid		= false;
				
		        var _this = this;
				$.get(this.name+".php",{"AJAX":"true","command":"addScreen"},_this.loadScreen);
		    }
				
			AdminUsers.prototype.add	= function(){
				var params	= this.validate();
				if( params == null )
					return;
				
				params["command"]	= "add";
				var _this	= this;
				$.post(this.name+".php",params,_this.addEditResult);
			}
				
			AdminUsers.prototype.addEditResult	= function(response){
				adminUsers.view();	
			}
				
			AdminUsers.prototype.validate	= function(){
				var params	= {};
				$("#notice").html("");
				var error	= "";
				
				/* Check fields */
				if ( $("#id").length != 0 ){
					/* Edit */
					if( isNaN($("#id").val()) ){
						this.view();
						return null;
					}
					
					params["userid"]	= $("#id").val();

					var blocked		= 0;
					if( $("#blocked_1").is(":checked") ){
						blocked	= 1;
					}
					params["blocked"]	= blocked;
				}
				else {
					/* Add */
					var username	= $.trim( $("#username").val() );
				
					if( username == ""){
						error	+= "'.$this->service_Language->get('language/admin/users/js/usernameEmpty').'<br/>";
					}
				
					params["username"]	= username;
								
					var password	= $.trim( $("#password").val() );
					var password2	= $.trim( $("#password2").val() );
					if( password == "" ){
						error	+= "'.$this->service_Language->get('language/admin/users/js/passwordEmpty').'<br/>";
					}
					else if( password != password2 ){
						error	+= "'.$this->service_Language->get('language/admin/users/js/passwordInvalid').'<br/>";
					}
								
					params["password"]		= password;	
				}
								
				var firstName	= $("#firstname").val();
				var nameBetween	= $("#nameBetween").val();
				var surname		= $("#surname").val();
				var email		= $("#email").val();
				var bot			= 0;
				
				if( firstName == "" ){
					error	+= "'.$this->service_Language->get('language/admin/users/js/firstNameEmpty').'<br/>";
				}
				
				if( surname	== "" ){
					error	+= "'.$this->service_Language->get('language/admin/users/js/surnameEmpty').'<br/>";
				}
				
				if( !validateEmail(email) ){
					error	+= "'.$this->service_Language->get('language/admin/users/js/emailInvalid').'<br/>";
				}
				
				if( $("#bot_1").is(":checked") ){
					bot	= 1;
				}
				
				if( error != "" ){
					$("#notice").html(error);
					return null;
				}

				if( !this.usernameValid || !this.emailValid ){
					return null;
				}
				
				params["AJAX"]	= "true";
				params["firstName"]	= firstName;
				params["nameBetween"]	= nameBetween;				
				params["surname"] = surname;
				params["email"]	= email;
				params["bot"] = bot;
	
				return params;
			}
		    
		    AdminUsers.prototype.viewUser = function(id){
		        var _this   = this;		        
				$.get(this.name+".php",{"AJAX":"true","command":"view","userid":id},_this.loadScreen);
		    }
		    
		    AdminUsers.prototype.editUser = function(id){
				this.usernameValid	= true;
				this.emailValid		= true;
				
		        var _this   = this;
				$.get(this.name+".php",{"AJAX":"true","command":"editScreen","userid":id},_this.loadScreen);
		    }

		    AdminUsers.prototype.editUserSave = function(userid){
				var params	= this.validate();
				if( params == null )
					return;
				
				if( params["blocked"] == 1 ){
					if( params["userid"] == userid ){
						/* You can\'t block yourself */
						alert("'.$this->service_Language->get('language/admin/users/js/blockRejected').'");
						return;
					}
				
					if( !confirm("'.$this->service_Language->get('language/admin/users/js/blockConfirm').'") ){
						return;
					}
				}
				
		        var _this   = this;
		        if( id == "" ){
		            /* new user */
					params["command"]	= "add";
		        }
		        else {
		            /* Update user */
					params["command"]	= "edit";
		        }

				$.post(this.name+".php",params,_this.editSaveResult);
		    }
				
			AdminUsers.prototype.checkUsername	= function(username){
				this.usernameValid	= false;
				
				var _this   = this;
				$.get(this.name+".php",{"AJAX":"true","command":"checkUsername","username":username},_this.checkUsernameResult);
			}
				
			AdminUsers.prototype.checkUsernameResult = function(response){
		        if( response == 1 ){
					adminUsers.usernameValid	= true;
					$("#formNotice").html("");
				}
				else {
					adminUsers.usernameValid	= false;
					$("#formNotice").html("'.$this->service_Language->get('language/admin/users/js/nickNotice').'");
				}
			}
				
			AdminUsers.prototype.checkEmail	= function(email){
				this.emailValid	= false;
				
				var _this   = this;
				$.get(this.name+".php",{"AJAX":"true","command":"checkEmail","email":email},_this.checkEmailResult);
			}
				
			AdminUsers.prototype.checkEmailResult = function(response){		        
		        if( response == 1 ){
					adminUsers.emailValid	= true;
					$("#formNotice").html("");
				}
				else {
					adminUsers.emailValid	= false;
					$("#formNotice").html("'.$this->service_Language->get('language/admin/users/js/emailNotice').'");
				}
			}
		    
		    AdminUsers.prototype.editSaveResult = function(response){
		        adminUsers.view();
		    }
		    
		    AdminUsers.prototype.deleteUser = function(id,userid){
		        if( id == userid ){
					/* You can\'t remove yourself */
					alert("'.$this->service_Language->get('language/admin/users/js/deleteRejected').'");
					return;
				}
		        
		        if( !confirm("'.$this->service_Language->get('language/admin/users/js/deleteConfirm').'") ){
		            return;
		        }
				
		        var _this	= this;
				var params	= {"AJAX":"true", "command":"delete","userid":id};
				$.post(this.name+".php",params,_this.editSaveResult);
		    }
				
			AdminUsers.prototype.changePassword	= function(){
				password1	= $.trim( $("#password1").val() );
				password2	= $.trim( $("#password2").val() );
				
				$("#passwordErrorNotice").html("");
				$("#passwordNotice").html("");
				
				if( password1 == "" || password1 != password2 ){
					$("#passwordErrorNotice").html("'.$this->service_Language->get('language/admin/users/js/passwordInvalid').'");
				}
				
				var params	={"AJAX":"true","command":"changePassword","password":password1,"userid":$("#id").val()};
				
				var _this   = this;		   
				$.post(this.name+".php",params,_this.changePasswordresult);
			}
				
			AdminUsers.prototype.changePasswordresult	= function(response){
				$("#passwordNotice").html("'.$this->service_Language->get('language/admin/users/js/passwordChanged').'");
			}
				
			AdminUsers.prototype.selectGroup	= function(groupID,userid){
				var active = 0;
				
				if( $("#group_"+groupID).is(":checked") ){
					active	= 1;
				}

				if( groupID == 0 ){
					if( active == 0 && userid == $("#id").val() ){
						alert("'.$this->service_Language->get('language/admin/users/js/groupAdminRejected').'");
						$("#group_"+groupID).prop("checked", true);
						return;
					}
					else if( active == 1 ){
						if( !confirm("'.$this->service_Language->get('language/admin/users/js/groupAdminConfirm').'") ){
							$("#group_"+groupID).prop("checked", false);
							return;
						}
					}
				}

				if( active == 0 ){
					this.changeGroupProcess(groupID,-1);
				}
				else{
					value	= $("#groep_level_"+groupID).val();
					this.changeGroupProcess(groupID,value);
				}
			}
				
			AdminUsers.prototype.changeGroup	= function(groupID,value){
				if( !$("#group_"+groupID).is(":checked") )
					return;
				
				this.changeGroupProcess(groupID,value);
			}
								
			AdminUsers.prototype.changeGroupProcess	= function(groupID,value){				
				var params	= {"AJAX":"true","command":"changeGroup","groupID":groupID,"level":value,"userid":$("#id").val()};				
				var _this   = this; 
				$.post(this.name+".php",params,_this.changeGroupResult);
			}
				
			AdminUsers.prototype.changeGroupResult	= function(response){
				
			}
		}
		
		var adminUsers  = new AdminUsers();';
		echo($s_file);
	}
}

$obj_AdminUsers	= new AdminUsers();
unset($obj_AdminUsers);
?>
