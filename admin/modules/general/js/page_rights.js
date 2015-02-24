function PageRights(){
	this.address	= '../../admin/modules/general/pages.php';
}
PageRights.prototype.init = function(){
	$('body').click(function(){ pageRights.hideMenu(); });
	
	$('#page_list .link').each(function(){
		$(this).click(function(){
			var item = $(this);
			pageRights.loadRights(item);
		});
		
		$(this).bind("contextmenu",function(e){
			var item = $(this);
			pageRights.hideMenu();
			pageRights.showMenu(item);
			e.preventDefault();
		});
	});
	
	$('#page_list .directory_pointer').each(function(){
		$(this).bind("contextmenu",function(e){
			var item = $(this);
			pageRights.hideMenu();
			pageRights.showMenu(item);
			e.preventDefault();
		});
	});
	
	$('#pages_add_page').click(function(){ pageRights.hideMenu(); pageRights.createNewPage(); });
	$('#pages_visit_page').click(function(){ pageRights.hideMenu();  pageRights.visitPage(); });
	$('#pages_edit_page').click(function(){
		pageRights.hideMenu();
		pageRights.loadRights( $('#page_menu') );
	});
	$('#pages_delete_page').click(function(){ pageRights.hideMenu(); pageRights.deletePage();  });
}
PageRights.prototype.hideMenu = function(){
	$('#dir_menu').css('display','none')
	$('#page_menu').css('display','none');
}
PageRights.prototype.showMenu = function(item){
	var position = item.position();
	
	var name;
	
	if( item.hasClass('link') ){
		name = '#page_menu';
	}
	else {
		name = '#dir_menu';
	}
	
	$(name).css('left',(position.left+20)+'px');
	$(name).css('top',(position.top+5)+'px');
	$(name).data('url',item.data('url'));
	$(name).css('display','block');	
}
PageRights.prototype.createNewPage	= function(){
	var dir = $('#dir_menu').data('url');
	console.log('creating new page in '+dir);
}
PageRights.prototype.visitPage	= function(){
	var page = $('#page_menu').data('url');
	
	location.href="../../../"+page;
}
PageRights.prototype.deletePage	= function(){
	var page = $('#page_menu').data('url');
	
	var height = parseInt($('#page_menu').css('height').replace('px',''));
	var offset = parseInt($('#page_menu').offset().top);
	
	height = (height+offset+60);
	
	confirmBox.init(height,pageRights.deletePageConfirm);
	confirmBox.show('Pagina verwijderen', 'Weet je zeker dat je '+page+' wilt verwijderen?');
}
PageRights.prototype.deletePageConfirm	= function(){
	var page;
	
	if ($('#page_menu').length > 0 ) {
		page = $('#page_menu').data('url'); 
	} else {
		page = $('#pages').data('url');
	}
	
/*	$.post(pageRights.address,{'command':'delete','url':page},function(){
		general.showPageRights();
	});*/
}
PageRights.prototype.loadRights = function(item){
	var link = item.data('url');
	admin.show(pageRights.address+'?command=view&url='+link,pageRights.loadRightsCallback);
}
PageRights.prototype.loadRightsCallback = function(){
	$('#pages_back').click(function(){ general.showPageRights(); });
	$('#pages_update').click(function(){ general.edit(); } );
	$('#pages_delete').click(function(){ 
		var page = $('#pages').data('url');
		
		var height = 250;
		
		confirmBox.init(height,pageRights.deletePageConfirm);
		confirmBox.show('Pagina verwijderen', 'Weet je zeker dat je '+page+' wilt verwijderen?');
	})
}
PageRights.prototype.edit	= function(){
	var rights = $('#pages_accesslevel').val();
	var group = $('#pages_group').val();
	var url = $('#pages').data('url');
	
	$.post(this.address,{'command':'edit','url':url,'rights':rights,'group':group},function(){
		general.showPageRights();
	});
}

var pageRights = new PageRights();