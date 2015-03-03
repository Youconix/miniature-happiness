function ConfirmBox(){
	this.height;
	this.callback;
	this.interval;
	this.pos = -100;
}
ConfirmBox.prototype.init	= function(height,callback){
	
	this.height = height || 350;
	this.callback = callback || null;
}
ConfirmBox.prototype.show	= function(title,text){
	var html = '<section id="confirmboxLayer">' +
	'	<article id="confirmbox">'+
	'		<div class="header">'+title+'</div>	'+
	'	<div class="body"><fielset>'+text +'</fieldset>'+
	'	<fieldset><input type="button" class="confirm" value="Yes" id="confirm_oke"> <input type="button" class="cancel" value="No" id="confirm_cancel"></fieldset>'+
	'</div>' + 
	'	</article>' +
	'</section>';
	
	$('body').append(html);
	
	var widthSite = parseInt($('body').css('width'));
	width = ((widthSite - 400)/2);
	
	$('#confirmboxLayer').css('width',widthSite+'px');
	$('#confirmbox').css('left',width+'px');
	$('#confirmbox').css('margin-left','-100%');
	$('#confirmbox').css('top',confirmBox.height+'px');
	$('#confirmboxLayer').css('display','block');
	$('#confirm_oke').click(function(){
		confirmBox.hide();
		confirmBox.confirm();
	});
	
	$('#confirm_cancel').click(function(){
		confirmBox.hide();
	});

	confirmBox.interval = setInterval(function(){
		confirmBox.pos += 1;
		$('#confirmbox').css('margin-left',confirmBox.pos+'%');
		if( confirmBox.pos == 0 ){
			clearInterval(confirmBox.interval);
		}
	},10);
}
ConfirmBox.prototype.hide	= function(){
	$('#confirm_cancel').off('click');
	$('#confirm_oke').off('click');


	confirmBox.interval = setInterval(function(){
		confirmBox.pos -= 1;
		$('#confirmbox').css('margin-left',confirmBox.pos+'%');
		if( confirmBox.pos == -100 ){
			clearInterval(confirmBox.interval);
			$('#confirmboxLayer').remove();
		}
	},10);
	
}
ConfirmBox.prototype.confirm	= function(){
	if( confirmBox.callback != null ){
		confirmBox.callback();
	}
}

var confirmBox = new ConfirmBox();
