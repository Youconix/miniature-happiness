function Groups(){
  this.url = '../modules/general/groups.php';
}
Groups.prototype.init = function(){
	$('#groups tbody tr').each(function(){
		$(this).click(function(){
			var id = $(this).data('id');
			if( id != -1 ){
				admin.show(groups.url+'?command=getGroup&id='+id,groups.showGroup);
			}
		});
	});
	
	$('#groupAddButton').click(function(){
		admin.show(groups.url+'?command=addScreen',groups.addScreen);
	})
}
Groups.prototype.showGroup	= function(){
	$('#users_back, #groupCancel').click(function(){ general.showGroups(); });
	$('#users_delete').click(function(){
		var name = $(this).data('name');
		var id = $(this).data('name');
		
		if( confirm(users_delete.replace('[username]',name)) ){
			$.post(groups.url,{'command':'delete','id':id},function(){
				general.showGroups();
			});
		}
	});
	
	$('#groupEditSave').click(function(){ groups.edit(); });
}
Groups.prototype.edit	= function(){
	var data = groups.check();
	if( data == null ){	return };
	
	data['id'] = $('#id').val();
	data['command'] = 'edit';
	$.post(groups.url,data,function(){
		general.showGroups();
	});
}
Groups.prototype.addScreen = function(){
	$('#users_back, #groupCancel').click(function(){ general.showGroups(); });
	$('#groupSave').click(function(){ groups.save(); });
}
Groups.prototype.save = function(){
	var data = groups.check();
	if( data == null ){	return };
	
	data['command'] = 'save';
	$.post(groups.url,data,function(){
		general.showGroups();
	});
}
Groups.prototype.check = function(){
	var fields = new Array('name','description');
	if( !validation.html5ValidationArray(fields) ){
		return null;
	}
	
	var data = {
		'name' : $('#name').val(), 
		'description' : $('#description').val(),
		'defaultGroup' : 0
	};
	if( $('default_1').is(':checked') ){
		data['defaultGroup'] = 1;
	}
	
	return data;
}
var groups = new Groups();