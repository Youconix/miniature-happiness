function Groups() {
  this.url = '../modules/general/groups/';
}
Groups.prototype.init = function () {
  $('#groupAddButton, #admin_general_groups_add_group').click(function () {
    admin.show(groups.url + 'addScreen', groups.addScreen);
  });
  
  $('#groups tbody tr').each(function () {
    $(this).click(function () {
      var id = $(this).data('id');
      if (id !== -1) {
	admin.show(groups.url  + id, groups.view);
      }
    });
  });  
};
Groups.prototype.view = function () {
  $('#group_user_list tr').each(function () {
    $(this).click(function () {
      var id = $(this).data('id');

      admin.show(users.url + id, users.showUserEvents);
    });
  });

  $('#groups_edit').click(function () {
    var id = $(this).data('id');
    if (!groups.editAllowed(id)) {
      return;
    }

    admin.show(groups.url + 'editScreen/' + id, groups.editScreen);
  });
  $('#groups_delete').click(function () {
    groups.deleteItem();
  });

  $('#users_back').click(function () {
    general.showGroups();
  });
};
Groups.prototype.editAllowed = function (id) {
  if (id === 0 || id === 1) {
    return false;
  }
  return true;
};
Groups.prototype.deleteItem = function () {
  var id = $('#groups_delete').data('id');
  if (!groups.editAllowed(id)) {
    return;
  }

  var name = $('#groups_delete').data('name');

  confirmBox.init(150, groups.deleteConfirm);
  confirmBox.show(languageAdmin.groups_delete_title,
	  languageAdmin.users_delete.replace('[name]', name));
};
Groups.prototype.deleteConfirm = function () {
  var id = $('#groups_edit').data('id');

  $.post(groups.url + 'delete', {
    'id': id
  }, function () {
    //general.showGroups();
  });
};
Groups.prototype.showGroup = function () {
  $('#users_back, #groupCancel').click(function () {
    general.showGroups();
  });
  $('#groups_delete').click(function () {
    groups.deleteItem();
  });
};
Groups.prototype.editScreen = function(){
  $('#users_back, #groupCancel').click(function () {
    general.showGroups();
  });
  $('#groups_delete').click(function () {
    groups.deleteItem();
  });
  
  $('#groupEdit').click(function(){
    groups.edit();
  });
};
Groups.prototype.edit = function () {
  var data = groups.check();
  if (data === null) {
    return;
  }

  data['id'] = $('#id').val();
  $.post(groups.url + 'edit', data, function () {
    general.showGroups();
  });
}
Groups.prototype.addScreen = function () {
  $('#users_back, #groupCancel').click(function () {
    general.showGroups();
  });
  $('#groupSave').click(function () {
    groups.save();
  });
}
Groups.prototype.save = function () {
  var data = groups.check();
  if (data === null) {
    return;
  }

  $.post(groups.url + 'add', data, function () {
    general.showGroups();
  });
}
Groups.prototype.check = function () {
  var fields = new Array('name', 'description');
  if (!validation.html5ValidationArray(fields)) {
    return null;
  }

  var data = {
    'name': $('#name').val(),
    'description': $('#description').val(),
    'defaultGroup': 0
  };
  if ($('default_1').is(':checked')) {
    data['defaultGroup'] = 1;
  }

  return data;
}
var groups = new Groups();