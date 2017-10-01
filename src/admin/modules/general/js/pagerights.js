function PageRights() {
  this.address = '../../admin/modules/general/pages/';
  this.viewID = -1;
}
PageRights.prototype.init = function () {
  $('body').click(function () {
    pageRights.hideMenu();
  });

  $('#page_list .link').each(function () {
    $(this).click(function () {
      var item = $(this);
      pageRights.loadRights(item);
    });

    $(this).bind("contextmenu", function (e) {
      var item = $(this);
      pageRights.hideMenu();
      pageRights.showMenu(item);
      e.preventDefault();
    });
  });

  $('#page_list .directory_pointer').each(function () {
    $(this).bind("contextmenu", function (e) {
      var item = $(this);
      pageRights.hideMenu();
      pageRights.showMenu(item);
      e.preventDefault();
    });
  });

  $('#pages_add_page').click(function () {
    pageRights.hideMenu();
    pageRights.createNewPage();
  });
  $('#pages_visit_page').click(function () {
    pageRights.hideMenu();
    pageRights.visitPage();
  });
  $('#pages_edit_page').click(function () {
    pageRights.hideMenu();
    pageRights.loadRights($('#page_menu'));
  });
  $('#pages_delete_page').click(function () {
    pageRights.hideMenu();
    pageRights.deletePage();
  });
}
PageRights.prototype.hideMenu = function () {
  $('#dir_menu').css('display', 'none')
  $('#page_menu').css('display', 'none');
}
PageRights.prototype.showMenu = function (item) {
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
PageRights.prototype.createNewPage = function () {
  var dir = $('#dir_menu').data('url');
  console.log('creating new page in ' + dir);
}
PageRights.prototype.visitPage = function () {
  var page = $('#page_menu').data('url');

  location.href = "/" + page;
}
PageRights.prototype.deletePage = function () {
  var page = $('#page_menu').data('url');

  var height = parseInt($('#page_menu').css('height').replace('px', ''));
  var offset = parseInt($('#page_menu').offset().top);

  height = (height + offset + 60);

  confirmBox.init(height, pageRights.deletePageConfirm);
  confirmBox.show(languageAdmin.pagerights_delete_confirm_title, languageAdmin.pagerights_delete_confirm.replace('[page]', page));
}
PageRights.prototype.deletePageConfirm = function () {
  var page;

  if ($('#page_menu').length > 0) {
    page = $('#page_menu').data('url');
  } else {
    page = $('#pages').data('url');
  }

  $.post(pageRights.address + "delete", {'url': page}, function () {
    general.showPageRights();
  });
}
PageRights.prototype.loadRights = function (item) {
  var link = item.data('url');
  admin.show(pageRights.address + 'view/' + link,
	  pageRights.loadRightsCallback);
}
PageRights.prototype.loadRightsCallback = function () {
  $('#pages_back').click(function () {
    general.showPageRights();
  });
  $('#pages_update').click(function () {
    pageRights.edit();
  });
  $('#pages_delete').click(function () {
    var page = $('#pages').data('url');

    var height = 250;
    confirmBox.init(height, pageRights.deletePageConfirm);
    confirmBox.show(languageAdmin.pagerights_delete_confirm_title, languageAdmin.pagerights_delete_confirm.replace('[page]', page));
  });

  $('#pages_template_add').click(function () {
    pageRights.editView();
  });

  $('#pages_reset').click(function () {
    var url = $('#pages').data('url');

    $('#pages_group').removeProp('selected').find('option:first').prop('selected', 'selected');
    $('#pages_accesslevel').removeProp('selected').find('option:first').prop('selected', 'selected');
    $.post(pageRights.address + "reset", {"url": url});
  });
  pageRights.viewRights();
}
PageRights.prototype.edit = function () {
  $('#pages_group').removeClass('invalid');
  $('#pages_accesslevel').removeClass('invalid');

  var rights = $('#pages_accesslevel').val();
  var group = $('#pages_group').val();
  var url = $('#pages').data('url');

  var errors = false;
  if (group == '') {
    $('#pages_group').addClass('invalid');
    errors = true;
  }
  if (rights == '') {
    $('#pages_accesslevel').addClass('invalid');
    errors = true;
  }

  if (errors) {
    return;
  }

  $.post(this.address + 'edit', {
    'url': url,
    'rights': rights,
    'group': group
  }, function () {
    general.showPageRights();
  });
}
PageRights.prototype.editView = function () {
  var rights = $('#template_level').val();
  var url = $('#pages').data('url');
  var view = $('#view_name').val();
  var group = $('#viewGroups').val();

  errors = false;
  var fields = new Array('view_name', 'template_level', 'viewGroups');
  var i;
  for (i in fields) {
    $('#' + fields[i]).removeClass('invalid');
    if ($.trim($('#' + fields[i]).val()) == '') {
      $('#' + fields[i]).addClass('invalid');
      errors = true;
    }
  }
  if (errors) {
    return;
  }

  $('#pages_group').removeProp('selected').find('option:first').prop('selected', 'selected');
  $('#pages_accesslevel').removeProp('selected').find('option:first').prop('selected', 'selected');

  $.post(this.address + 'addView', {
    'url': url,
    'rights': rights,
    'group': group,
    'view': view
  }, function (response) {
    response = JSON.parse(response);
    if (response['id'] == -1) {
      return;
    }

    var style_dir = $('#right_list').data('styledir');

    $('#right_list tbody').append('<tr data-template="' + response['command'] + '" data-id="' + response['id'] + '" id="view_' + response['id'] + '"> ' +
	    '	<td style="width:50px"><img src="' + style_dir + '/images/icons/delete.png" alt="' + response['deleteText'] + '" title="' + response['deleteText'] + '"></td> ' +
	    '	<td>' + response['command'] + '</td>' +
	    '	<td>' + response['group'] + '</td>' +
	    '	<td>' + response['level'] + '</td>' +
	    '    </tr>');

    pageRights.viewRights();
  });
}
PageRights.prototype.viewRights = function () {
  $('#right_list tbody img').each(function () {
    $(this).off('click');
  });
  $('#right_list tbody img').each(function () {
    $(this).click(function () {
      var item = $(this);
      pageRights.deleteView(item);
    });
  });
}
PageRights.prototype.deleteView = function (item) {
  var id = item.parent().parent().data('id');
  var template = item.parent().parent().data('template');
  pageRights.viewID = id;
  var height = 250;
  confirmBox.init(height, pageRights.deleteViewConfirm);
  confirmBox.show(languageAdmin.pagerights_delete_view_title, languageAdmin.pagerights_delete_confirm.replace('[page]', template));
}
PageRights.prototype.deleteViewConfirm = function () {
  id = pageRights.viewID;
  $('#view_' + id).remove();
  $.post(pageRights.address + 'deleteView', {'id': id});
}

var pageRights = new PageRights();