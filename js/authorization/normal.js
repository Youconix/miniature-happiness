function AuthorizationNormal(){}
AuthorizationNormal.prototype.init = function(){
	if($('#reg_nick').length > 0 ){
		this.initRegistration();
	}
	else if( $('#password_old').length > 0 ){
		this.initExpired();
	}
	else {
		this.initLogin();
	}
}
AuthorizationNormal.prototype.initRegistration = function(){
	$('#reg_nick').on('blur',function(){ _this.checkUsername(); });
}
AuthorizationNormal.prototype.checkUsername = function() {
	var username = $.trim($('#reg_nick').val());
	if( username == '' ){ return; }
	
	var _this = this;
	$.post('../authorization/normal/checkUsername',{'nick':username},_this.checkUsernameCallback);
}
AuthorizationNormal.prototype.checkUsernameCallback = function(response) {
	
}

AuthorizationNormal.prototype.checkEmail = function() {
	
}

AuthorizationNormal.prototype.checkEmailCallback = function() {
	
}

AuthorizationNormal.prototype.initLogin = function(){
	validation.bind(['username','password']);
	$('.login input[type="submit"]').click(function(){
		if( !validation.html5ValidationArray(['username','password']) ){
			return false;
		}
		return true;
	});
}
AuthorizationNormal.prototype.initExpired = function(){
	
}

var authorizationNormal = new AuthorizationNormal();
$(document).ready(function(){
	authorizationNormal.init();
})