function Validation(){
	this.skip	= new Array('button','submit','reset','checkbox','radio');
}
Validation.prototype.init	= function(){
	/* Check for stylesheet */
	styledir = $('body').data('styledir');
	if( !styledir ){	styledir = '/styles/default/';	}
	
	if( !$("link[href='"+styledir+"css/HTML5_validation.css']").length ){
	    $('<link href="'+styledir+'css/HTML5_validation.css" rel="stylesheet">').appendTo("head");
	}
}
Validation.prototype.validateEmail	= function(email) {
	return email.match("^[a-zA-Z0-9!#$%&'*+\-\/=?^_`\{\|\}~\.]{2,64}[@]{1}[a-zA-Z0-9\-\.]{2,255}[\.]{1}[a-zA-Z0-9\-]{2,63}$");
}
Validation.prototype.validateURI	= function(uri){

}
Validation.prototype.checkPostalNL	= function(value){
	if (value == "")
		return false;
	if (!value.match("^\\d{4}\\s?[a-zA-Z]{2}$"))
		return false;

	return true;
}
Validation.prototype.checkPostalBE	= function(value){
	if (value == "")
		return false;
	if (!value.match("^\\d{4}$"))
		return false;

	postal = parseInt(value);
	if (postal < 1000 || postal > 9999)
		return false;

	return true;
}
Validation.prototype.html5ValidationArray	= function(fields){
	oke = true;
	
	for(i in fields){
		if( !this.html5Validation(fields[i]) ){
			oke = false;
		}
	}
	
	return oke;
}
Validation.prototype.bindAll	= function(){	
	$('input, textarea').each(function(){
		type = $(this).prop('type');
		
		if( (!type) || (arraySearch(validation.skip,type) == -1) ){
			$(this).on("blur",function(){
				item = $(this);
				
				validation.html5Validate(item);
			})
		}
	});
}
Validation.prototype.html5ValidationAll	= function(){
	oke = true;
	
	$('input, textarea').each(function(){
		type = $(this).prop('type');
		
		if( (!type) || (arraySearch(validation.skip,type) == -1) ){
			item = $(this);
			
			if( !validation.html5Validate(item) ){
				oke = false;
			}
		}
	});
	
	return oke;
}
Validation.prototype.html5Validation	= function(id){
	item = $("#" + id); 
	return this.html5Validate(item);
}
Validation.prototype.html5Validate	= function(item){
	$(item).removeClass("invalid valid");
		
	required = $(item).prop("required");
	isRequired = false;
	if( (typeof required !== "undefined" && required !== false) ){
		isRequired = true;
	}
		
	type = $(item).prop("type");
	pattern = $(item).prop("pattern");
	value = $(item).val();
	
	if( isRequired && $.trim(value) == "" ){
		$(item).addClass("invalid");
		return false;
	}
	if( (typeof pattern != "undefined") && pattern != false ){
		if( value != "" && (value.match(pattern) == null)  ){
			$(item).addClass("invalid");
			return false;
		}
	}
		
	if( (typeof type === "undefined" || type === false) ){
		$(item).addClass("valid");
		return true;
	}
		
	if( type == "number" || type == "range" ){
		if( isNaN(value) ){
			$(item).addClass("invalid");
			return false;
		}
		
		min = $(item).prop("min");
		max = $(item).prop("max");
				
		if( ((typeof min !== "undefined") && min !== false && value < min) || ((typeof max !== "undefined") && max !== false && value > max) ){
			$(item).addClass("invalid");
			return false;
		}
	}
	if( type == "email" && $.trim(value) != "" && !this.validateEmail(value) ){
		$(item).addClass("invalid");
		return false;
	}
	if( type == "url" && $.trim(value) != "" && !this.validateURI(value) ){
		$(item).addClass("invalid");
		return false;
	}
	
	$(item).addClass("valid");
	
	return true;
}

validation = new Validation();

$(document).ready(function(){
	validation.init();
})