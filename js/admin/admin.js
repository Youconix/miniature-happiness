function Admin(){
  this.adminData;
}
Admin.prototype.init	= function(){
	$('#admin_menu_link').click(function(){ admin.toggleMenu() });
}
Admin.prototype.toggleMenu = function(){
	if( $('#admin_panel').css('display') == 'none' ){
		admin.showMenu();
	}
	else {
		admin.hideMenu();
	}
}
Admin.prototype.hideMenu = function(){
	$('#admin_panel').hide();
}
Admin.prototype.showMenu = function(){
	$('#admin_panel').show();
}
Admin.prototype.show = function(url,callback){
  this.hideMenu();
  this.callback = callback || null;
  
  $.get(url,admin.showPage);
}
Admin.prototype.showPage = function(response){
  $('#adminContent').css('display','none');
  $('#adminContent').html(response);
  $('#adminContent').css('display','block');
  
  setTimeout(function(){
	  if( admin.callback != null ){
	      admin.callback();
	  }
  },500);
}

var admin = new Admin();

$(document).ready(function(){
  admin.init();
});