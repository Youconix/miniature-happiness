function arraySearch(arrayName, value) {
	for (i = 0; i < arrayName.length; i++) {
		if (arrayName[i] == value)
			return i;
		else if (arrayName[i].indexOf(value) != -1)
			return i;
	}

	return -1;
}

function Validation(){
	this.skip	= new Array('button','submit','reset','checkbox','radio');
}
Validation.prototype.init	= function(){
	/* Check for stylesheet */
	var styledir = $('body').data('styledir');
	if( !styledir ){	styledir = '/styles/shared/';	}
	
	if( !$("link[href='"+styledir+"css/HTML5_validation.css']").length ){
	    $('<link href="'+styledir+'css/HTML5_validation.css" rel="stylesheet">').appendTo("head");
	}
}
Validation.prototype.validateEmail	= function(email) {
	return email.match("^[a-zA-Z0-9!#\$%&\'\*\+\-\/=\?\^_`\{\|\}~\.]{2,64}[@]{1}[a-zA-Z0-9\-\.]{2,255}[\.]{1}[a-zA-Z0-9\-]{2,63}$");
}
Validation.prototype.validateURI	= function(uri){
  return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(uri);
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

	var postal = parseInt(value);
	if (postal < 1000 || postal > 9999)
		return false;

	return true;
}
Validation.prototype.html5ValidationArray	= function(fields){
	var oke = true;
	
  var i;
	for(i in fields){
		if( !this.html5Validation(fields[i]) ){
			oke = false;
		}
	}
	
	return oke;
}
Validation.prototype.bind	= function(names){
	var i;
	for(i in names){
		var type = $('#'+names[i]).prop('type');
		
		if( (!type) || (arraySearch(validation.skip,type) == -1) ){
			$('#'+names[i]).on("blur",function(){
				var item = $(this);
				
				validation.html5Validate(item);
			})
		}
	}
}
Validation.prototype.bindAll	= function(){	
	$('input, textarea').each(function(){
		var type = $(this).prop('type');
		
		if( (!type) || (arraySearch(validation.skip,type) == -1) ){
			$(this).on("blur",function(){
				var item = $(this);
				
				validation.html5Validate(item);
			})
		}
	});
}
Validation.prototype.html5ValidationAll	= function(){
	var oke = true;
	
	$('input, textarea').each(function(){
		var type = $(this).prop('type');
		
		if( (!type) || (arraySearch(validation.skip,type) == -1) ){
			var item = $(this);			
			if( !validation.html5Validate(item) ){
				oke = false;
			}
		}
	});
	
	return oke;
}
Validation.prototype.html5Validation	= function(id){
	var item = $("#" + id); 
	return this.html5Validate(item);
}
Validation.prototype.errorMessage = function(item,type){
	type = type || 'missing';
	item.next().remove();
	
  if( item.hasClass('valid') ){
    item.prop('title','');
    return;
  }
  console.log(type);
  if( item.data().hasOwnProperty('validation-'+type) || item.attr('data-validation-'+type) ){
	  item.after('<span class="validation-error-message">'+item.data('validation-'+type)+'</span>');
  }
  else if( item.data().hasOwnProperty('validation') || item.attr('data-validation') ){
	  item.after('<span class="validation-error-message">'+item.data('validation')+'</span>');
  }
}
Validation.prototype.html5Validate	= function(item){
	$(item).removeClass("invalid valid");
		
	var required = $(item).prop("required");
	var isRequired = false;
	if( (typeof required !== "undefined" && required !== false) ){
		isRequired = true;
	}
		
	var type = $(item).prop("type");
	var pattern = $(item).prop("pattern");
	var value = $(item).val();
  	
	if( isRequired && $.trim(value) == "" ){
		$(item).addClass("invalid");
		this.errorMessage(item);
		return false;
	}
	if( (typeof pattern != "undefined") && pattern != false ){
		if( pattern.substring(0,1) == '/' ){
			pattern = pattern.substring(1);
		}
		if( pattern.substring(pattern.length-1) == '/' ){
			pattern = pattern.substring(0,pattern.length-1);
		}
		
		pattern = new RegExp(pattern);
		if( value != "" && (value.match(pattern) == null)  ){
			$(item).addClass("invalid");
			this.errorMessage(item,'pattern');
			return false;
		}
	}
		
	if( (typeof type === "undefined" || type === false) ){
		$(item).addClass("valid");
		this.errorMessage(item);
		return true;
	}
		
	if( type == "number" || type == "range" ){
		if( isNaN(value) ){
			$(item).addClass("invalid");
			this.errorMessage(item,'range');
			return false;
		}
		
		var min = $(item).prop("min");
		var max = $(item).prop("max");
    
		value = parseFloat(value);
				
		if( ((typeof min !== "undefined") && min !== false && min !== '' && value < min) ){
			$(item).addClass("invalid");
			this.errorMessage(item,'min');
			return false;
		}
		if( ( (typeof max !== "undefined") && max !== '' && max !== false && value > max) ){
			$(item).addClass("invalid");
			this.errorMessage(item,'max');
			return false;
		}
	}
	if( type == "email" && $.trim(value) != "" && !this.validateEmail(value) ){
		$(item).addClass("invalid");
		this.errorMessage(item,'pattern');
		return false;
	}
	if( type == "url" && $.trim(value) != "" && !this.validateURI(value) ){
		$(item).addClass("invalid",'pattern');
    this.errorMessage(item);
		return false;
	}
	if( type == 'date' && value == '' ){
	    $(item).addClass("invalid");
	    this.errorMessage(item);
		return false;
  }
	
	$(item).addClass("valid");
	this.errorMessage(item);
	
	return true;
}

validation = new Validation();

$(document).ready(function(){
	validation.init();
})