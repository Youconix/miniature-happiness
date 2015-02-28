function Groups(){
  this.url = '../modules/general/groups.php';
}
Groups.prototype.init = function(){
	$('#groups tbody tr').each(function(){
		$(this).click(function(){
			var id = $(this).data('id');
			if( id != -1 ){
				admin.show(groups.url+'?command=view&id='+id,groups.view);
			}
		});
	});
	
	$('#groupAddButton').click(function(){
		admin.show(groups.url+'?command=addScreen',groups.addScreen);
	})
	$('#admin_general_groups_add_group').click(function(){
		admin.show(groups.url+'?command=addScreen',groups.addScreen);
	});
}
Groups.prototype.view = function(){
	$('#group_user_list tr').each(function(){
		$(this).click(function(){
			var id = $(this).data('id');
			
			admin.show(users.url+'?command=view&userid='+id,users.showUserEvents);
		});
	});
	
	$('#groups_edit').click(function(){
		var id = $(this).data('id');
		if( !groups.editAllowed(id) ){	return; }
		
		admin.show(groups.url+'?command=getGroup&id='+id,groups.showGroup);
	});
	
	$('#groups_delete').click(function(){
		groups.deleteItem();
	});
	
	$('#users_back').click(function(){ general.showGroups(); });
}
Groups.prototype.editAllowed = function(id){
	if( id == 0 || id == 1 ){	return false; }
	return true;
}
Groups.prototype.deleteItem = function(){
	var id = $('#groups_delete').data('id');
	if( !groups.editAllowed(id) ){	return; }
	
	var name = $('#groups_delete').data('name');
	
	confirmBox.init(150,groups.deleteConfirm);
	confirmBox.show(languageAdmin.groups_delete_title,languageAdmin.users_delete.replace('[name]',name));
}
Groups.prototype.deleteConfirm = function(){
	var id = $('#groups_edit').data('id');
	
	$.post(groups.url,{'command':'delete','id':id},function(){
		general.showGroups();
	});
}
Groups.prototype.showGroup	= function(){
	$('#users_back, #groupCancel').click(function(){ general.showGroups(); });
	$('#groups_delete').click(function(){
		groups.deleteItem();
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