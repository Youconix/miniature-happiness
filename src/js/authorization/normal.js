function AuthorizationNormal(){
  this.validUsername = 1;
  this.validEmail = 1;
}
AuthorizationNormal.prototype.init = function(){
  if($('#reg_conditions').length > 0 ){
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
  var button = $('#registration_form').find('[type="submit"]');
  button.prop('type','button');
  button.click(function(){ authorizationNormal.checkRegistration(); });
  
  $('[name="username"]').on('blur',function(){ authorizationNormal.checkUsername(); });
  $('[name="email"]').on('blur',function(){ authorizationNormal.checkEmail(); });
  
  validation.bindNames(['username','email','password','password2','captcha']);
}
AuthorizationNormal.prototype.initExpired = function(){
	
}
AuthorizationNormal.prototype.checkUsername = function() {
  var username = $.trim($('[name="username"]').val());
  if( username === '' ){ return; }
	
  $.post('/registration/check/normal/username',{"username":username},authorizationNormal.checkUsernameCallback);
}
AuthorizationNormal.prototype.checkUsernameCallback = function(response) {
  var item = $('[name="username"]');
  if( response == 0 ){
    validation.trigger(item,'taken');
    authorizationNormal.validUsername = 0;
  }
  else {
    item.removeClass('invalid').addClass('valid');
    item.next().remove();
    authorizationNormal.validUsername = 1;
  }
}
AuthorizationNormal.prototype.checkEmail = function() {
  var email = $.trim($('[name="email"]').val());
  if( !validation.validateEmail(email) ){ return; }
	
  $.post('/registration/check/normal/email',{"email":email},authorizationNormal.checkEmailCallback);
}

AuthorizationNormal.prototype.checkEmailCallback = function(response) {
  var item = $('[name="email"]');
  if( response == 0 ){
    validation.trigger(item,'taken');
    authorizationNormal.validEmail = 0;
  }
  else {
    item.removeClass('invalid').addClass('valid');
    item.next().remove();
    authorizationNormal.validEmail = 1;
  }
}
AuthorizationNormal.prototype.checkRegistration = function(){
  var ok = true;
  if( !validation.html5ValidationArray(['username','email','password','password2','captcha']) ){
    ok = false;
  }
  if( this.validUsername === 0 ){
    validation.trigger($('[name="username"]'),'taken');
    ok = false;
  }
  if( this.validEmail === 0 ){
    validation.trigger($('[name="email"]'),'taken');
    ok = false;
  }
  passwordCheck.validate();
  if( !passwordCheck.passwords_ok ){
    ok = false;
  }
  
  if( ok ){
    $('#registration_form').submit();
  }
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
AuthorizationNormal.prototype.reloadCaptcha = function(){
  var date = Math.ceil(Date.now()/1000);
  var image = $('#registration_captcha').prop('src').split('time=');
  
  image = image[0]+"time="+date;
  $('#registration_captcha').prop('src',image);
}

var authorizationNormal = new AuthorizationNormal();
$(document).ready(function(){
	authorizationNormal.init();
	$('#reload_captcha').click(function(){ authorizationNormal.reloadCaptcha(); } );
})