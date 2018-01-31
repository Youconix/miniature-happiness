class ConfirmBox{
  constructor(){
    this.height;
    this.callback;
    this.interval;
    this.pos = -100;
  }
  init(height = 350,callback = null){	
    this.height = height;
    this.callback = callback;
  }
  show(title,text){
    let html = '<section id="confirmboxLayer">' +
    '	<article id="confirmbox">'+
    '		<div class="header">'+title+'</div>	'+
    '	<div class="body"><fielset>'+text +'</fieldset>'+
    '	<fieldset><input type="button" class="confirm" value="Yes" id="confirm_oke"> <input type="button" class="cancel" value="No" id="confirm_cancel"></fieldset>'+
    '</div>' + 
    '	</article>' +
    '</section>';
	
    $('body').append(html);
	
    let widthSite = parseInt($('body').css('width'));
    let width = ((widthSite - 400)/2);
	
    $('#confirmboxLayer').css('width',widthSite+'px');
    $('#confirmbox').css('left',width+'px');
    $('#confirmbox').css('margin-left','-100%');
    $('#confirmbox').css('top',confirmBox.height+'px');
    $('#confirmboxLayer').css('display','block');
    $('#confirm_oke').click(() => {
      this.hide();
      this.confirm();
    });
	
    $('#confirm_cancel').click(() => {
      this.hide();
    });

    this.interval = setInterval(() => {
      this.pos += 1;
      $('#confirmbox').css('margin-left',this.pos+'%');
      if( this.pos == 0 ){
	clearInterval(this.interval);
      }
    },10);
  }
  hide(){
    $('#confirm_cancel').off('click');
    $('#confirm_oke').off('click');

    this.interval = setInterval(() => {
      this.pos -= 1;
      $('#confirmbox').css('margin-left',this.pos+'%');
      if( this.pos == -100 ){
	clearInterval(this.interval);
	$('#confirmboxLayer').remove();
      }
    },10);	
  }
  confirm(){
    if( this.callback != null ){
      this.callback();
    }
  }
}

var confirmBox = new ConfirmBox();
