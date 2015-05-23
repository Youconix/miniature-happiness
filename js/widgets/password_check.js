function PasswordCheck() {
	this.password1;
	this.password2;

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
}
PasswordCheck.prototype.validate = function() {
	this.password1.removeClass("valid invalid");
	this.password2.removeClass("valid invalid");

	this.password1.prop("title", "");
	this.password2.prop("title", "");

	$("#passwordStrength").html("");
	$("#passwordStrengthText").html("");

	password1 = $.trim(this.password1.val());
	password2 = $.trim($("#password2").val());

	if (password1 == "" || password2 == "") {
		return;
	}

	error = false;
	if (password1 != password2) {
		this.password1.prop("title", languageWidgets.passwordform_invalid);
		this.password2.prop("title", languageWidgets.passwordform_invalid);

		error = true;
	} else if (password1.length < 8) {
		this.password1.prop("title", languageWidgets.passwordform_toShort);
		this.password2.prop("title", languageWidgets.passwordform_toShort);

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
		text = languageWidgets.passwordform_veryStrongPassword;
		strength = 4;
	} else if (this.checkLetters(password)
			&& (this.checkNumbers(password) || this.checkSpecial(password))) {
		text = languageWidgets.passwordform_strongPassword;
		strength = 3;
	} else if (password.length > 12) {
		text = languageWidgets.passwordform_fairPassword;
		strength = 2;
	} else {
		text = languageWidgets.passwordform_weakPassword;
		strength = 1;
	}

	output = "";
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

	$("#passwordStrength").html(output);
	$("#passwordStrengthText").html(text);
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
