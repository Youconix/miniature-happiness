function Users(){
  this.url = '../modules/general/users.php';
}
Users.prototype.init = function(){
  $('#newUserButton, #newUserButton2').click(function(){  admin.show(users.url+"?command=addScreen",users.addUserScreen()); });  
  $('#searchUsername').on('keyup',function(){ users.filterUserList(); });    
  users.setUserListEvents();
}
Users.prototype.filterUserList	= function(){
	var value = $.trim($('#searchUsername').val());
	if( value.length < 3 ){	return; }
	
	$.get(users.url+'?command=searchResults&username='+value,function(response){
		users.filterUserListCallback(response);
	});
}
Users.prototype.filterUserListCallback = function(results){
	results = JSON.parse( $.trim(results) );
	console.log(results);
	
	$('#usertable tbody').empty();
	
	var i;
	for( i in results ){
		$('#usertable tbody').append('<tr data-id="'+results[i]['id']+'"> '+
			'	<td>'+results[i]['id']+'</td> '+
			'	<td>'+results[i]['username']+'</td> '+
			'	<td>'+results[i]['email']+'</td> '+
			'	<td>'+results[i]['loggedin']+'</td> '+
			'	<td>'+results[i]['registrated']+'</td> '+
			'	</tr>');
	}
}
Users.prototype.setUserListEvents = function(){
	$('#usertable tbody tr').each(function(){
		$(this).click(function(){
			var id = $(this).data('id');
			
			admin.show(users.url+'?command=view&userid='+id,users.showUserEvents);
		});
	});
}
Users.prototype.showUserEvents = function(){
	$('#users_back').click(function(){ general.showUsers(); });
	  $('#users_edit').click(function(){ 
		 var id = $(this).data('id');
		 admin.show(users.url+'?command=editScreen&userid='+id,users.editUserScreen);
	  });

	  $('#users_delete').click(function(){
		 var id = $(this).data('id');
		 var username = $(this).data('username');
		 var userid = $(this).data('userid');
		 
		 if( id == userid ){ return; }
		 
		 if( confirm('Weet u zeker dat u '+username+' wilt verwijderen?') ){
			 $.post(users.url,{'command':'delete','userid':id});
			 
			 general.showUsers();
		 }
	  });
	  
	  $('#user_login_as').click(function(){
		 var id = $(this).data('id');
		 var username = $(this).data('username');
		 var userid = $(this).data('userid');
		 
		 if( id == userid ){ return; }
		 
		 if( confirm('Weet u zeker dat u wilt inloggen als '+username+'?\nDit beeindigd uw admin sessie.') ){
			 $.post(users.url,{'command':'login','userid':id},function(response){
				 location.href = "/";
			 });
		 }
	  })
}
Users.prototype.editUserScreen = function(){
	$('#userUpdateButton').click(function(){ users.checkUpdate(); });
	$('#newGroup').on('change',function(){ users.addGroup(); });
	$('#newLevel').on('change',function(){ users.addGroup(); });
	
	this.setGrouplistEvents();
	this.showUserEvents();
}
Users.prototype.addGroup	= function(){
	var group = $('#newGroup').val();
	var groupName = $('#newGroup').text().split(' - ');
	var level = $('#newLevel').val();
	var levelText = $('#newLevel').text();
	var userid = $('#newGroup').data('id');
	
	if( group == '' || level == '' ){	return; }
	
	$('#newGroup option').find('[value="'+group+'"]').remove();
	
	$('#groupslist').append('<fieldset>'+$.trim(groupName[0])+' - '+levelText+' <img src="'+styleDir+'images/icons/delete.png" alt="'+deleteText+'" title="'+deleteText+'" class="delete" data-id="'+userid+'" data-group="'+group+'" data-level="'+level+'"></fieldset>');
	this.setGrouplistEvents();
	
	$.post(users.url,{'command':'addGroup', 'userid':userid,'group':group,'level':level});
}
Users.prototype.removeGroup	= function(item){
	var group = item.data('group');
	var level = item.data('level');
	var userid = item.data('id');
	var text = item.html().split(' - ');
	
	if( confirm('Weet u zeker dat u '+text[0]+' wilt verwijderen?') ){
		$('#newGroup').append('<option value="'+group+'">'+text[0]+'</option>');
		item.remove();
		
		$.post(users.url,{'command':'deleteGroup','groupID' : group,'userid':userid,'level':level});
	}
}
Users.prototype.setGrouplistEvents = function(){
	$('#groupslist fieldlist img').each(function(){
		$(this).off('click');
		
		$(this).click(function(){
			var item = $(this);
			users.removeGroup(item);
		});
	});
}
Users.prototype.checkUpdate = function(){
	var oke = true;
	$('#email').removeClass('invalid');
	$('#password1').removeClass('invalid');
	$('#password2').removeClass('invalid');
	
	if( !validateEmail($('#email').val()) ){
		$('#email').addClass('invalid');
		oke = false;
	}
	
	if( $('#password1').val() != '' && $('#password1').val() != $('#password2').val() ){
		$('#password1').addClass('invalid');
		$('#password2').addClass('invalid');
		oke = false;
	}
	
	var userid = $('#userid').val();
	var email = $('#email').val();
	var bot = 0;
	if( $('#bot_1').is(':checked') ){
		bot = 1;
	}
	var blocked = 0;
	if( $('#blocked_1').is(':checked') ){
		blocked = 1;
	}
	
	if( oke ){
		$.post(users.url,{'command':'edit','userid' : userid, 'email':email, 'bot' : bot,
			'blocked':blocked,'password':$('#password1').val(),'password2':$('#password2').val()},function(){
			admin.show(users.url+'?command=view&userid='+userid,users.showUserEvents);	
		});
	}
}
Users.prototype.addUserScreen = function(){
	if( $('#username').length == 0  ){
		setTimeout(function(){users.addUserScreen(); },500);
		return;
	}
	
	$('#username').on('blur',function(){ users.checkUsername(); });
	$('#email').on('blur',function(){ users.checkEmail(); });
	$('#userSaveButton').click(function(){ users.add(); });
	
	$('#username').trigger('blur');
	$('#email').trigger('blur');
}
Users.prototype.checkUsername = function(){
	$('#username').removeClass('invalid');
	
	var username = $.trim($('#username').val());
	if( username != '' ){
		$.get(users.url+'?command=checkUsername&username='+username,function(response){
			if( response != '1' ){
				$('#username').addClass('invalid');
				$('#email').title('De gebruikersnaam is al in gebruik.');
			}
			else {
				$('#usernameOK').val(1);
			}
		})
	}
}
Users.prototype.checkEmail = function(){
	$('#email').removeClass('invalid');
	
	var email = $.trim($('#email').val());
	if( email != '' ){
		$.get(users.url+'?command=checkEmail&email='+email,function(response){
			if( response != '1' ){
				$('#email').addClass('invalid');
				$('#email').title('Het E-mail adres is al in gebruik.');
			}
			else {
				$('#emailOK').val(1);
			}
		})
	}
}
Users.prototype.add = function(){
	var fields = new Array('username','email','password1','password2');
	if( !validation.html5ValidationArray(fields) ){
		return;
	}
	
	if( $('#usernameOK').val() == 0 || $('#emailOK').val() == 0 ){
		return;
	}
	
	var username = $('#username').val();
	var email = $('#email').val();
	var bot = 0;
	if( $('#bot_1').is(':checked') ){
		bot = 1;
	}
	var password1 = $('#password1').val();
	var password2 = $('#password2').val();
	
	if( password1 != password2 ){
		$('#password1').removeClass('valid').addClass('invalid');
		$('#password1').title('De wachtwoorden zijn niet gelijk');
		$('#password2').removeClass('valid').addClass('invalid');
		$('#password2').title('De wachtwoorden zijn niet gelijk');
		
		return;
	}
	
	$.post(users.url,{'command':'add', 'username':username,'email':email,'bot':bot,'password':password1,'password2':password2},function(userid){
		admin.show(users.url+'?command=view&userid='+userid,users.showUserEvents);
	});
}

var users = new Users();