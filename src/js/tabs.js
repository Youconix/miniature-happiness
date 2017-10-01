function Tabs(){
	this.current = -1;
	this.config;
}
Tabs.prototype.init	= function(config){
	if( !config || !config.hasOwnProperty('id') ){
	  return;
	}
	if( !config.hasOwnProperty('start') ){
	  config['start'] = 1;
	}
	this.config = config;

	var _this = this;
	$('#'+config['id']+' .tab_header div').each(function(){
	  $(this).click(function(){
		var item = $(this);
		var id = $(this).data('id');
		
		_this.clear(item);
		_this.click(id);
	  });
	});
	
	var i = 1;
	var _this = this;
	$('#'+config['id']+' .tab_header div').each(function(){
	  if( i == config['start'] ){
	   var id = $(this).data('id');
	   
	   _this.clear($(this));
	   _this.click(id);
	   return false;
	  }
	  i++;
	});
}
Tabs.prototype.click	= function(id){
  	$('#tab_'+this.current).css('display','none');
	$('#tab_'+id).css('display','block');
	
	this.current = id;
}
Tabs.prototype.clear	= function(item){
	$('#'+this.config['id']+' .tab_header div.tab_header_active').removeClass('tab_header_active');
	item.addClass('tab_header_active');
}