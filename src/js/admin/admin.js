function Admin() {
  this.adminData;
  this.url;
}
Admin.prototype.init = function () {
  $('#admin_menu_link').click(function () {
    admin.toggleMenu()
  });
}
Admin.prototype.toggleMenu = function () {
  if ($('#menu_wrapper').css('display') == 'none') {
    admin.showMenu();
  } else {
    admin.hideMenu();
  }
}
Admin.prototype.hideMenu = function (callback) {
  admin.slideLeft('#menu_wrapper', callback);
}
Admin.prototype.showMenu = function () {
  admin.slideRight('#menu_wrapper');
}
Admin.prototype.show = function (url, callback) {
  if ($('#menu_wrapper').css('display', 'block')) {
    this.hideMenu();
  }

  adminContent.load(url, callback);
}
Admin.prototype.slideLeft = function (id, callback) {
  callback = callback || null;
  $(id).animate({
    left: '-100%'
  }, 1000, function () {
    $(id).css('display', 'none');

    if (callback !== null) {
      callback();
    }
  });
}
Admin.prototype.slideRight = function (id, callback) {
  callback = callback || null;

  $(id).css('display', 'block');

  $(id).animate({
    left: '0%'
  }, 1000, function () {
    if (callback != null) {
      callback();
    }
  });
}

var admin = new Admin();

function AdminContent() {
  this.callback;
  this.content;
  this.interval;
  this.visible = false;
  this.pos = 0;
}
AdminContent.prototype.load = function (url, callback) {
  this.callback = callback || null;

  $.get(url, adminContent.loadCallback);
}
AdminContent.prototype.loadCallback = function (response) {
  adminContent.content = response;
  if (adminContent.visible) {
    adminContent.swap();
  } else {
    $('#adminContent').css('margin-left', '-100%');
    $('#adminContent').css('display', 'block');
    adminContent.pos = -100;
    $('#adminContent').html(adminContent.content);
    adminContent.show();
  }
}
AdminContent.prototype.show = function () {
  adminContent.interval = setInterval(function () {
    adminContent.pos += 1;
    $('#adminContent').css('margin-left', adminContent.pos + '%');
    if (adminContent.pos == 0) {
      clearInterval(adminContent.interval);
      adminContent.visible = true;

      if (adminContent.callback != null) {
	adminContent.callback();
      }
    }
  }, 10);
}
AdminContent.prototype.swap = function () {
  adminContent.interval = setInterval(function () {
    adminContent.pos -= 1;
    $('#adminContent').css('margin-left', adminContent.pos + '%');
    if (adminContent.pos == -100) {
      adminContent.visible = false;
      clearInterval(adminContent.interval);
      $('#adminContent').html(adminContent.content);
      $('#adminContent').css('display', 'block');
      adminContent.show();
    }
  }, 10);
}

var adminContent = new AdminContent();

$(document).ready(function () {
  admin.init();
});
