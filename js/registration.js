function Registration(){}

Registration.prototype.init = function() {
	var _this = this;
	
	$('#reg_nick').on('blur',function(){ _this.checkUsername(); });
}

Registration.prototype.checkUsername = function() {
	var username = $.trim($('#reg_nick').val());
	if( username == '' ){ return; }
	
	var _this = this;
	$.post('../authorization/normal/checkUsername',{'nick':username},_this.checkUsernameCallback);
}

Registration.prototype.checkUsernameCallback = function(response) {
	
}

Registration.prototype.checkEmail = function() {
	
}

Registration.prototype.checkEmailCallback = function() {
	
}

Registration.prototype.save = function() {
	
}

var registration = new Registration();

$(document).ready(function(){
	registration.init();
});