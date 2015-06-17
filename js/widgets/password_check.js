function PasswordCheck() {
	this.password1;
	this.password2;
	this.language = {};
}
PasswordCheck.prototype.setLanguage = function(language){
	this.language = language;
}
PasswordCheck.prototype.init = function() {
	var self = this;

	this.password1 = $("#password1");
	this.password2 = $("#password2");

	this.password1.on("blur", function() {
		self.validate();
	});
	this.password2.on("blur", function() {
		self.validate();
	});
	
	var offset = $('#password1').offset();
	var width = offset.left + parseInt($('#password1').css('width')) + 15;
	var top = offset.top;
	var index = parseInt($('#password1').css('z-index')) + 5;
	
	$('#passwordStrength').css('left',width);
	$('#passwordStrength').css('top',top);
	$('#passwordStrength').css('z-index',index);
}
PasswordCheck.prototype.validate = function() {
	password1 = $.trim(this.password1.val());
	password2 = $.trim($("#password2").val());
	
	$('#passwordStrength').hide();

	if (password1 == "" || password2 == "") {
		return;
	}
	
	this.password1.removeClass("valid invalid");
	this.password2.removeClass("valid invalid");
	
	this.password1.next().remove();
	this.password2.next().remove();

	$("#passwordIndicator").html("");
	$("#passwordStrengthText").html("");

	error = false;
	if (password1 != password2) {
		this.password1.after('<span class="validation-error-message">'+this.language.passwordform_invalid+'</span>');
		
		error = true;
	} else if (password1.length < 8) {
		this.password1.after('<span class="validation-error-message">'+this.language.passwordform_toShort+'</span>');
		
		error = true;
	}

	if (error) {
		this.password1.addClass("invalid");
		this.password2.addClass("invalid");
		return;
	}

	this.checkComplexity(password1);
}
PasswordCheck.prototype.checkComplexity = function(password) {
	this.password1.addClass("valid");
	this.password2.addClass("valid");

	this.password1.prop("title", "");
	this.password2.prop("title", "");

	if (this.checkLetters(password) && this.checkNumbers(password)
			&& this.checkSpecial(password)) {
		text = this.language.passwordform_veryStrongPassword;
		strength = 4;
	} else if (this.checkLetters(password)
			&& (this.checkNumbers(password) || this.checkSpecial(password))) {
		text = this.language.passwordform_strongPassword;
		strength = 3;
	} else if (password.length > 12) {
		text = this.language.passwordform_fairPassword;
		strength = 2;
	} else {
		text = this.language.passwordform_weakPassword;
		strength = 1;
	}

	output = '';
	if (strength >= 1) {
		output += '<div class="passwordRed"></div><div class="passwordRed"></div>';
	}
	if (strength >= 2) {
		output += '<div class="passwordYellow"></div><div class="passwordYellow"></div>';
	}
	if (strength >= 3) {
		output += '<div class="passwordGreen"></div><div class="passwordGreen"></div>';
	}
	if (strength == 4) {
		output += '<div class="passwordGreen"></div><div class="passwordGreen"></div>';
	}

	$("#passwordIndicator").html(output);
	$("#passwordStrengthText").html(text);
	
	$('#passwordStrength').show();
}
PasswordCheck.prototype.checkLetters = function(password) {
	return password.match("[a-zA-Z]+");
}
PasswordCheck.prototype.checkNumbers = function(password) {
	return password.match("[0-9]+");
}
PasswordCheck.prototype.checkSpecial = function(password) {
	return password.match("[!@#\$%\^&\*\(\)\-_\+=\{\},\.\<\>/\?\|]+");
}
