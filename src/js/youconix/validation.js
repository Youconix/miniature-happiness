function arraySearch(arrayName, value) {
  for (i = 0; i < arrayName.length; i++) {
    if (arrayName[i] === value)
      return i;
    else if (arrayName[i].indexOf(value) !== -1)
      return i;
  }

  return -1;
}

class Validation {
  constructor(){
    this.skip = new Array('button', 'submit', 'reset', 'checkbox', 'radio', 'hidden');
  }
  init(){
    /* Check for stylesheet */
    if (!$("link[href='/resources/css/youconix.css']").length) {
      $('<link href="/resources/css/youconix.css" rel="stylesheet">').appendTo("head");
    }
  }
  validateEmail(email) {
    return email.match("^[a-zA-Z0-9!#\$%&\'\*\+\-\/=\?\^_`\{\|\}~\.]{2,64}[@]{1}[a-zA-Z0-9\-\.]{2,255}[\.]{1}[a-zA-Z0-9\-]{2,63}$");
  }
  validateURI(uri) {
      return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(uri);
    }
    checkPostalNL(value) {
    if (value === ""){
      return false;
    }
    if (!value.match("^\\d{4}\\s?[a-zA-Z]{2}$")){
      return false;
    }

    return true;
  }
  checkPostalBE(value) {
    if (value === ""){
      return false;
    }
    if (!value.match("^\\d{4}$")){
      return false;
    }

    let postal = parseInt(value);
    if (postal < 1000 || postal > 9999){
      return false;
    }

    return true;
  }
  html5ValidationArray(fields) {
    let oke = true;

    for (let i in fields) {
      if (!this.html5Validation(fields[i])) {
	oke = false;
      }
    }

    return oke;
  }
  bind(names) {
    for (let i in names) {
      let type = $('#' + names[i]).prop('type');

      if ((!type) || (arraySearch(this.skip, type) === -1)) {
	$('#' + names[i]).on("blur", (item) => {
	  item = $(item);
	  this.html5Validate(item);
	});
      }
    }
  }
  bindNames(names) {
    for (let i in names) {
      let type = $('[name="' + names[i] + '"]').prop('type');

      if ((!type) || (arraySearch(this.skip, type) === -1)) {
	$('[name="' + names[i] + '"]').on("blur", (item) => {
	  item = $(item);
	  this.html5Validate(item);
	});
      }
    }
  }
  bindAll() {
    $('input, textarea').each((i, type) => {
      type = $(type).prop('type');

      if ((!type) || (arraySearch(this.skip, type) === -1)) {
	$(this).on("blur", (item) => {
	  item = $(item);
	  this.html5Validate(item);
	});
      }
    });
  }
  validateForm(id){
    let oke = true;
    let form = $('#'+id);
    
    form.find("input,select").each((i,item) => {
      item = $(item);
      let type = item.prop('type');

      if ((!type) || (arraySearch(this.skip, type) === -1)) {
	if (!this.html5Validate(item) ){
	  oke = false;
	}
      }
    });
    
    return oke;
  }
  html5ValidationAll() {
    let oke = true;

    $('input, textarea').each((i, item) => {
      item = $(item);
      let type = item.prop('type');

      if ((!type) || (arraySearch(this.skip, type) === -1)) {
	if (!this.html5Validate(item)) {
	  oke = false;
	}
      }
    });

    return oke;
  }
  html5Validation(id) {
    let item;
    if( $('#'+id).length > 0 ){
      item = $("#" + id);
    }
    else if( $('[name="'+id+'"]').length > 0  ){
      item = $('[name="'+id+'"]');
    }
    return this.html5Validate(item);
  }
  errorMessage(item, type = 'missing') {
    let message;
    
    if (item.data().hasOwnProperty('validation-' + type) || item.attr('data-validation-' + type)) {
      message = item.data('validation-' + type);
    } else if (item.data().hasOwnProperty('validation') || item.attr('data-validation')) {
      message = item.data('validation');
    }
    else {
      return;
    }
    
    let id = this.getErrorMessageId(item);
    message = '<span class="validation-error-message" id="'+id+'">'+message+'</span>';
    $(message).insertAfter(item);
    
    let position = item.position();
    message = $('#'+id);
    let height = parseInt(message.css('height').replace('px',''));
    
    let left = (parseFloat(position.left));
    let top = (parseFloat(position.top) - height - 40);
    message.css('left',left+'px');
    message.css('top',top+'px');
    
    item.on('mouseover', (event) => {
      this.showErrorMessage($(event.currentTarget));
    });
    item.on('mouseout', (event) => {
      this.hideErrorMessage($(event.currentTarget));
    });
  }
  trigger(item,error_type){
    item = $(item);
    
    item.removeClass('valid').addClass('invalid');
    this.errorMessage(item,error_type);
  }
  clearItem(item){
    item.removeClass("invalid valid");
    item.off('mouseover');
    item.off('mouseout');
    
    let id = this.getErrorMessageId(item);
    $('#'+id).remove();    
  }
  html5Validate(item) {
    item = $(item);
    this.clearItem(item);    

    let required = $(item).prop("required");
    let isRequired = false;
    if ((typeof required !== "undefined" && required !== false)) {
      isRequired = true;
    }

    let type = item.prop("type");
    let pattern = item.prop("pattern");
    let value = item.val();

    if (isRequired && $.trim(value) === "") {
      item.addClass("invalid");
      this.errorMessage(item);
      return false;
    }
    if ((typeof pattern !== "undefined") && pattern !== false) {
      if (pattern.substring(0, 1) === '/') {
	pattern = pattern.substring(1);
      }
      if (pattern.substring(pattern.length - 1) === '/') {
	pattern = pattern.substring(0, pattern.length - 1);
      }

      pattern = new RegExp(pattern);
      if (value !== "" && (value.match(pattern) === null)) {
	item.addClass("invalid");
	this.errorMessage(item, 'pattern');
	return false;
      }
    }

    if ((typeof type === "undefined" || type === false)) {
      item.addClass("valid");
      this.errorMessage(item);
      return true;
    }

    if (type === "number" || type === "range") {
      if (isNaN(value)) {
	item.addClass("invalid");
	this.errorMessage(item, 'range');
	return false;
      }

      let min = item.prop("min");
      let max = item.prop("max");

      value = parseFloat(value);

      if (((typeof min !== "undefined") && min !== false && min !== '' && value < min)) {
	item.addClass("invalid");
	this.errorMessage(item, 'min');
	return false;
      }
      if (((typeof max !== "undefined") && max !== '' && max !== false && value > max)) {
	item.addClass("invalid");
	this.errorMessage(item, 'max');
	return false;
      }
    }
    if (type === "email" && $.trim(value) !== "" && !this.validateEmail(value)) {
      item.addClass("invalid");
      this.errorMessage(item, 'pattern');
      return false;
    }
    if (type === "url" && $.trim(value) !== "" && !this.validateURI(value)) {
      item.addClass("invalid", 'pattern');
      this.errorMessage(item);
      return false;
    }
    if (type === 'date' && value === '') {
      item.addClass("invalid");
      this.errorMessage(item);
      return false;
    }

    item.addClass("valid");

    return true;
  }
  showErrorMessage(item){
    let id = this.getErrorMessageId(item);
    $('#'+id).fadeIn(500);
  }
  hideErrorMessage(item){
    let id = this.getErrorMessageId(item);
    $('#'+id).fadeOut(500);
  }
  getErrorMessageId(item){
    let name = item.prop('name');
    return 'error_message_'+name;
  }
}

var validation = new Validation();

$(document).ready(function () {
  validation.init();
});