function OnOff(){}
OnOff.prototype.init = function(){
  _this = this;
  
  $('.on_off').each(function(){
    $(this).off('click');
  });
  
  $('.on_off').each(function(){
    $(this).click(function(){
      var item = $(this);
      
      _this.click(item);
    });
  });
};
OnOff.prototype.click = function(item){
  var active = item.hasClass('on_off_active');
  var id = item.prop('id').replace('_slider','');
  var value;
  
  if( !active ){
    value = item.data('on');
  }
  else {
    value = item.data('off');
  }
  
  this.setValue(id,value);
  
  if( active ){
    item.removeClass('on_off_active');
  }
  else {
    item.addClass('on_off_active');
  }
};
OnOff.prototype.setValue = function(name,value){
  $('input[name="'+name+'"]').val(value);
};

var onOff = new OnOff();
$(document).ready(function(){
  onOff.init();
});
