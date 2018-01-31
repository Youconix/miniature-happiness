class OnOff{
  init(){
    $('.on_off').each((i,slider) => {
      $(slider).off('click');
      
      $(slider).click((item) => {
	item = $(item);

	this.click(item);
      });
    });
  }
  click(item){
    let active = item.hasClass('on_off_active');
    let id = item.prop('id').replace('_slider','');
    let value;

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
  }
  setValue(name,value){
    $('input[name="'+name+'"]').val(value);
  }
}

var onOff = new OnOff();
$(document).ready(function(){
  onOff.init();
});
