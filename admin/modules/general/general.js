function General() {
	this.address = '../../admin/modules/general/';
}
General.prototype.init = function() {
	$('#admin_general_users h2').click(function() {
		general.showUsers()
	});
	$('#admin_general_users_add_user').click(function() {
		users.showAddUserScreen();
	});

	$('#admin_general_groups h2').click(function() {
		general.showGroups()
	});
	$('#admin_general_page_rights h2').click(function() {
		general.showPageRights()
	});
	$('#admin_general_updates h2').click(function() {
		general.showUpdates()
	});
	$('#admin_general_backup h2').click(function() {
		general.showBackup()
	});
	$('#admin_general_maintenance h2').click(function() {
		general.showMaintenance()
	});
	$('#admin_general_cache h2').click(function() {
		general.showCache()
	});
	$('#admin_general_modules h2').click(function() {
		general.showModules()
	});
}
General.prototype.showUsers = function() {
	admin.show(this.address + 'users.php?command=index', users.init);
}
General.prototype.showGroups = function() {
	admin.show(this.address + 'groups.php?command=index', groups.init);
}
General.prototype.showPageRights = function() {
	admin.show(this.address + 'pages.php?command=index', pageRights.init);
}
General.prototype.showUpdates = function() {
	admin.show(this.address + 'updates.php');
}
General.prototype.showBackup = function() {
	admin.show('maintenance.php?command=backup');
}
General.prototype.showMaintenance = function() {
	admin.show('maintenance.php');
}
General.prototype.showCache = function() {
	admin.show('cache.php');
}
General.prototype.showModules = function() {
	admin.show(this.address + 'modules.php?command=index', modules.init);
}

var general = new General();
$(document).ready(function() {
	general.init();
});

/* Groups */
function Groups() {
	this.url = '../modules/general/groups.php';
}
Groups.prototype.init = function() {
	$('#groups tbody tr').each(function() {
		$(this).click(function() {
			var id = $(this).data('id');
			if (id != -1) {
				admin.show(groups.url + '?command=view&id=' + id, groups.view);
			}
		});
	});

	$('#groupAddButton').click(function() {
		admin.show(groups.url + '?command=addScreen', groups.addScreen);
	})
	$('#admin_general_groups_add_group').click(function() {
		admin.show(groups.url + '?command=addScreen', groups.addScreen);
	});
}
Groups.prototype.view = function() {
	$('#group_user_list tr').each(
			function() {
				$(this).click(
						function() {
							var id = $(this).data('id');

							admin.show(
									users.url + '?command=view&userid=' + id,
									users.showUserEvents);
						});
			});

	$('#groups_edit').click(
			function() {
				var id = $(this).data('id');
				if (!groups.editAllowed(id)) {
					return;
				}

				admin.show(groups.url + '?command=getGroup&id=' + id,
						groups.showGroup);
			});

	$('#groups_delete').click(function() {
		groups.deleteItem();
	});

	$('#users_back').click(function() {
		general.showGroups();
	});
}
Groups.prototype.editAllowed = function(id) {
	if (id == 0 || id == 1) {
		return false;
	}
	return true;
}
Groups.prototype.deleteItem = function() {
	var id = $('#groups_delete').data('id');
	if (!groups.editAllowed(id)) {
		return;
	}

	var name = $('#groups_delete').data('name');

	confirmBox.init(150, groups.deleteConfirm);
	confirmBox.show(languageAdmin.groups_delete_title,
			languageAdmin.users_delete.replace('[name]', name));
}
Groups.prototype.deleteConfirm = function() {
	var id = $('#groups_edit').data('id');

	$.post(groups.url, {
		'command' : 'delete',
		'id' : id
	}, function() {
		general.showGroups();
	});
}
Groups.prototype.showGroup = function() {
	$('#users_back, #groupCancel').click(function() {
		general.showGroups();
	});
	$('#groups_delete').click(function() {
		groups.deleteItem();
	});

	$('#groupEditSave').click(function() {
		groups.edit();
	});
}
Groups.prototype.edit = function() {
	var data = groups.check();
	if (data == null) {
		return

	}
	;

	data['id'] = $('#id').val();
	data['command'] = 'edit';
	$.post(groups.url, data, function() {
		general.showGroups();
	});
}
Groups.prototype.addScreen = function() {
	$('#users_back, #groupCancel').click(function() {
		general.showGroups();
	});
	$('#groupSave').click(function() {
		groups.save();
	});
}
Groups.prototype.save = function() {
	var data = groups.check();
	if (data == null) {
		return

	}
	;

	data['command'] = 'save';
	$.post(groups.url, data, function() {
		general.showGroups();
	});
}
Groups.prototype.check = function() {
	var fields = new Array('name', 'description');
	if (!validation.html5ValidationArray(fields)) {
		return null;
	}

	var data = {
		'name' : $('#name').val(),
		'description' : $('#description').val(),
		'defaultGroup' : 0
	};
	if ($('default_1').is(':checked')) {
		data['defaultGroup'] = 1;
	}

	return data;
}
var groups = new Groups();

/* modules */
function Modules() {
	this.url = '../modules/general/modules.php';
	this.item;
}
Modules.prototype.init = function() {
	$('#installed_modules tbody tr').each(function() {
		$(this).click(function() {
			modules.item = $(this);
			modules.deleteModule();
		});
	});

	$('#upgradable_modules tbody tr').each(function() {
		$(this).click(function() {
			modules.item = $(this);
			modules.upgrade();
		});
	});

	$('#new_modules tbody tr').each(function() {
		$(this).click(function() {
			modules.item = $(this);
			modules.install();
		});
	});
}
Modules.prototype.deleteModule = function() {
	var name = modules.item.data('name');

	if (name == 'general' || name == 'settings' || name == 'statistics') {
		/*
		 * Framework modules Do not remove
		 */
		return;
	}

	confirmBox.init(250, modules.deleteModuleCallback);
	confirmBox.show(languageAdmin.modules_delete_title,
			languageAdmin.modules_delete.replace('[name]', name));
}
Modules.prototype.deleteModuleCallback = function() {
	var id = modules.item.data('id');

	$.post(this.address, {
		'command' : 'delete',
		'id' : id
	}, function() {
		general.showModules();
	});
}
Modules.prototype.upgrade = function() {
	var name = modules.item.data('name');

	confirmBox.init(250, modules.upgradeCallback);
	confirmBox.show(languageAdmin.modules_upgrade_title,
			languageAdmin.modules_upgrade.replace('[name]', name));
}
Modules.prototype.upgradeCallback = function() {
	var id = modules.item.data('id');

	$.post(this.address, {
		'command' : 'upgrade',
		'id' : id
	}, function() {
		general.showModules();
	});
}
Modules.prototype.install = function() {
	var name = modules.item.data('name');

	confirmBox.init(250, modules.installCallback);
	confirmBox.show(languageAdmin.modules_install_title,
			languageAdmin.modules_install.replace('[name]', name));
}
Modules.prototype.installCallback = function() {
	var name = modules.item.data('name');

	$.post(this.address, {
		'command' : 'install',
		'name' : name
	}, function() {
		general.showModules();
	});
}

var modules = new Modules();

/* Page rights */
function PageRights() {
	this.address = '../../admin/modules/general/pages.php';
}
PageRights.prototype.init = function() {
	$('body').click(function() {
		pageRights.hideMenu();
	});

	$('#page_list .link').each(function() {
		$(this).click(function() {
			var item = $(this);
			pageRights.loadRights(item);
		});

		$(this).bind("contextmenu", function(e) {
			var item = $(this);
			pageRights.hideMenu();
			pageRights.showMenu(item);
			e.preventDefault();
		});
	});

	$('#page_list .directory_pointer').each(function() {
		$(this).bind("contextmenu", function(e) {
			var item = $(this);
			pageRights.hideMenu();
			pageRights.showMenu(item);
			e.preventDefault();
		});
	});

	$('#pages_add_page').click(function() {
		pageRights.hideMenu();
		pageRights.createNewPage();
	});
	$('#pages_visit_page').click(function() {
		pageRights.hideMenu();
		pageRights.visitPage();
	});
	$('#pages_edit_page').click(function() {
		pageRights.hideMenu();
		pageRights.loadRights($('#page_menu'));
	});
	$('#pages_delete_page').click(function() {
		pageRights.hideMenu();
		pageRights.deletePage();
	});
}
PageRights.prototype.hideMenu = function() {
	$('#dir_menu').css('display', 'none')
	$('#page_menu').css('display', 'none');
}
PageRights.prototype.showMenu = function(item) {
	var position = item.position();

	var name;

	if (item.hasClass('link')) {
		name = '#page_menu';
	} else {
		name = '#dir_menu';
	}

	$(name).css('left', (position.left + 20) + 'px');
	$(name).css('top', (position.top + 5) + 'px');
	$(name).data('url', item.data('url'));
	$(name).css('display', 'block');
}
PageRights.prototype.createNewPage = function() {
	var dir = $('#dir_menu').data('url');
	console.log('creating new page in ' + dir);
}
PageRights.prototype.visitPage = function() {
	var page = $('#page_menu').data('url');

	location.href = "../../../" + page;
}
PageRights.prototype.deletePage = function() {
	var page = $('#page_menu').data('url');

	var height = parseInt($('#page_menu').css('height').replace('px', ''));
	var offset = parseInt($('#page_menu').offset().top);

	height = (height + offset + 60);

	confirmBox.init(height, pageRights.deletePageConfirm);
	confirmBox.show('Pagina verwijderen', 'Weet je zeker dat je ' + page
			+ ' wilt verwijderen?');
}
PageRights.prototype.deletePageConfirm = function() {
	var page;

	if ($('#page_menu').length > 0) {
		page = $('#page_menu').data('url');
	} else {
		page = $('#pages').data('url');
	}

	/*
	 * $.post(pageRights.address,{'command':'delete','url':page},function(){
	 * general.showPageRights(); });
	 */
}
PageRights.prototype.loadRights = function(item) {
	var link = item.data('url');
	admin.show(pageRights.address + '?command=view&url=' + link,
			pageRights.loadRightsCallback);
}
PageRights.prototype.loadRightsCallback = function() {
	$('#pages_back').click(function() {
		general.showPageRights();
	});
	$('#pages_update').click(function() {
		general.edit();
	});
	$('#pages_delete').click(
			function() {
				var page = $('#pages').data('url');

				var height = 250;

				confirmBox.init(height, pageRights.deletePageConfirm);
				confirmBox.show('Pagina verwijderen', 'Weet je zeker dat je '
						+ page + ' wilt verwijderen?');
			})
}
PageRights.prototype.edit = function() {
	var rights = $('#pages_accesslevel').val();
	var group = $('#pages_group').val();
	var url = $('#pages').data('url');

	$.post(this.address, {
		'command' : 'edit',
		'url' : url,
		'rights' : rights,
		'group' : group
	}, function() {
		general.showPageRights();
	});
}

var pageRights = new PageRights();

/* Users */
function Users() {
	this.url = '../modules/general/users.php';
}
Users.prototype.init = function() {
	$('#newUserButton, #newUserButton2').click(function() {
		users.showAddUserScreen();
	});
	$('#searchUsername').on('keyup', function() {
		users.filterUserList();
	});
	users.setUserListEvents();
}
Users.prototype.showAddUserScreen = function() {
	admin.show(users.url + "?command=addScreen", users.addUserScreen());
}
Users.prototype.filterUserList = function() {
	var value = $.trim($('#searchUsername').val());
	if (value.length < 3) {
		return;
	}

	$.get(users.url + '?command=searchResults&username=' + value, function(
			response) {
		users.filterUserListCallback(response);
	});
}
Users.prototype.filterUserListCallback = function(results) {
	results = JSON.parse($.trim(results));

	$('#usertable tbody').empty();

	var i;
	for (i in results) {
		$('#usertable tbody').append(
				'<tr data-id="' + results[i]['id'] + '"> ' + '	<td>'
						+ results[i]['id'] + '</td> ' + '	<td>'
						+ results[i]['username'] + '</td> ' + '	<td>'
						+ results[i]['email'] + '</td> ' + '	<td>'
						+ results[i]['loggedin'] + '</td> ' + '	<td>'
						+ results[i]['registrated'] + '</td> ' + '	</tr>');
	}
}
Users.prototype.setUserListEvents = function() {
	$('#usertable tbody tr').each(
			function() {
				$(this).click(
						function() {
							var id = $(this).data('id');

							admin.show(
									users.url + '?command=view&userid=' + id,
									users.showUserEvents);
						});
			});
}
Users.prototype.showUserEvents = function() {
	$('#users_back').click(function() {
		general.showUsers();
	});
	$('#users_edit').click(
			function() {
				var id = $(this).data('id');
				admin.show(users.url + '?command=editScreen&userid=' + id,
						users.editUserScreen);
			});

	$('#users_delete')
			.click(
					function() {
						var id = $(this).data('id');
						var username = $(this).data('username');
						var userid = $(this).data('userid');

						if (id == userid) {
							return;
						}

						confirmBox.init(150, users.deleteConfirm);
						confirmBox.show(languageAdmin.users_delete_title,
								languageAdmin.users_delete.replace('[name]',
										username));
					});

	$('#user_login_as').click(function() {
		var id = $(this).data('id');
		var username = $(this).data('username');
		var userid = $(this).data('userid');

		if (id == userid) {
			return;
		}

		if (confirm(languageAdmin.login_as.replace('[username]', username))) {
			$.post(users.url, {
				'command' : 'login',
				'userid' : id
			}, function(response) {
				location.href = "/";
			});
		}
	})
}
Users.prototype.deleteConfirm = function() {
	var id = $('#users_delete').data('id');
	$.post(users.url, {
		'command' : 'delete',
		'userid' : id
	}, function() {
		general.showUsers();
	});
}
Users.prototype.editUserScreen = function() {
	$('#userUpdateButton').click(function() {
		users.checkUpdate();
	});
	$('#newGroup').on('change', function() {
		users.addGroup();
	});
	$('#newLevel').on('change', function() {
		users.addGroup();
	});

	users.setGrouplistEvents();
	users.showUserEvents();
}
Users.prototype.addGroup = function() {
	var group = $('#newGroup').val();
	var groupName = $('#newGroup').text().split(' - ');
	var level = $('#newLevel').val();
	var levelText = $('#newLevel').text();
	var userid = $('#newGroup').data('id');

	if (group == '' || level == '') {
		return;
	}

	$('#newGroup option').find('[value="' + group + '"]').remove();

	$('#groupslist').append(
			'<fieldset>' + $.trim(groupName[0]) + ' - ' + levelText
					+ ' <img src="' + styleDir
					+ 'images/icons/delete.png" alt="' + deleteText
					+ '" title="' + deleteText + '" class="delete" data-id="'
					+ userid + '" data-group="' + group + '" data-level="'
					+ level + '"></fieldset>');
	this.setGrouplistEvents();

	$.post(users.url, {
		'command' : 'addGroup',
		'userid' : userid,
		'group' : group,
		'level' : level
	});
}
Users.prototype.removeGroup = function(item) {
	var group = item.data('group');
	var level = item.data('level');
	var userid = item.data('id');

	var text = item.parent().html().split(' - ');

	if (confirm(languageAdmin.users_delete_group.replace('[name]', text[0]))) {
		$('#newGroup').append(
				'<option value="' + group + '">' + text[0] + '</option>');
		item.remove();

		$.post(users.url, {
			'command' : 'deleteGroup',
			'groupID' : group,
			'userid' : userid,
			'level' : level
		});
	}
}
Users.prototype.setGrouplistEvents = function() {
	$('#groupslist fieldset img').each(function() {
		$(this).off('click');

		$(this).click(function() {
			var item = $(this);
			users.removeGroup(item);
		});
	});
}
Users.prototype.checkUpdate = function() {
	var oke = true;
	$('#email').removeClass('invalid');
	$('#password1').removeClass('invalid');
	$('#password2').removeClass('invalid');

	if (!validateEmail($('#email').val())) {
		$('#email').addClass('invalid');
		oke = false;
	}

	if ($('#password1').val() != ''
			&& $('#password1').val() != $('#password2').val()) {
		$('#password1').addClass('invalid');
		$('#password2').addClass('invalid');
		oke = false;
	}

	var userid = $('#userid').val();
	var email = $('#email').val();
	var bot = 0;
	if ($('#bot_1').is(':checked')) {
		bot = 1;
	}
	var blocked = 0;
	if ($('#blocked_1').is(':checked')) {
		blocked = 1;
	}

	if (oke) {
		$.post(users.url, {
			'command' : 'edit',
			'userid' : userid,
			'email' : email,
			'bot' : bot,
			'blocked' : blocked,
			'password' : $('#password1').val(),
			'password2' : $('#password2').val()
		}, function() {
			admin.show(users.url + '?command=view&userid=' + userid,
					users.showUserEvents);
		});
	}
}
Users.prototype.addUserScreen = function() {
	if ($('#username').length == 0) {
		setTimeout(function() {
			users.addUserScreen();
		}, 500);
		return;
	}

	$('#users_back').click(function() {
		general.showUsers();
	});
	$('#username').on('blur', function() {
		users.checkUsername();
	});
	$('#email').on('blur', function() {
		users.checkEmail();
	});
	$('#userSaveButton').click(function() {
		users.add();
	});

	$('#username').trigger('blur');
	$('#email').trigger('blur');
}
Users.prototype.checkUsername = function() {
	$('#username').removeClass('invalid');

	var username = $.trim($('#username').val());
	if (username != '') {
		$.get(users.url + '?command=checkUsername&username=' + username,
				function(response) {
					if (response != '1') {
						$('#username').addClass('invalid');
						$('#email').title(languageAdmin.users_username_taken);
					} else {
						$('#usernameOK').val(1);
					}
				})
	}
}
Users.prototype.checkEmail = function() {
	$('#email').removeClass('invalid');

	var email = $.trim($('#email').val());
	if (email != '') {
		$.get(users.url + '?command=checkEmail&email=' + email, function(
				response) {
			if (response != '1') {
				$('#email').addClass('invalid');
				$('#email').title(languageAdmin.users_email_taken);
			} else {
				$('#emailOK').val(1);
			}
		})
	}
}
Users.prototype.add = function() {
	var fields = new Array('username', 'email', 'password1', 'password2');
	if (!validation.html5ValidationArray(fields)) {
		return;
	}

	if ($('#usernameOK').val() == 0 || $('#emailOK').val() == 0) {
		return;
	}

	var username = $('#username').val();
	var email = $('#email').val();
	var bot = 0;
	if ($('#bot_1').is(':checked')) {
		bot = 1;
	}
	var password1 = $('#password1').val();
	var password2 = $('#password2').val();

	if (password1 != password2) {
		$('#password1').removeClass('valid').addClass('invalid');
		$('#password1').title(languageAdmin.users_password_invalid);
		$('#password2').removeClass('valid').addClass('invalid');
		$('#password2').title(languageAdmin.users_password_invalid);

		return;
	}

	$.post(users.url, {
		'command' : 'add',
		'username' : username,
		'email' : email,
		'bot' : bot,
		'password' : password1,
		'password2' : password2
	}, function(userid) {
		admin.show(users.url + '?command=view&userid=' + userid,
				users.showUserEvents);
	});
}

var users = new Users();