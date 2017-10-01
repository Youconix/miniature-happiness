function Users() {
  this.url = "../modules/general/users/";
}
Users.prototype.init = function () {
  $("#newUserButton, #newUserButton2").click(function () {
    users.showAddUserScreen();
  });
  $("#searchUsername").on("keyup", function () {
    users.filterUserList();
  });
  users.setUserListEvents();
};
Users.prototype.showAddUserScreen = function () {
  admin.show(users.url + "addScreen", users.addUserScreen());
};
Users.prototype.filterUserList = function () {
  var value = $.trim($("#searchUsername").val());
  $.get(users.url + "search/username/" + value, function (
	  response) {
    users.filterUserListCallback(response);
  });
};
Users.prototype.filterUserListCallback = function (results) {
  results = JSON.parse($.trim(results));

  $("#usertable tbody tr").each(function () {
    $(this).off("click");
  });

  $("#usertable tbody").empty();

  var i;
  for (i in results) {
    $("#usertable tbody").append(
	    '<tr data-id="' + results[i]["id"] + '> ' + '	<td>'
	    + results[i]["id"] + '</td> ' + '	<td>'
	    + results[i]["username"] + '</td> ' + '	<td>'
	    + results[i]["email"] + '</td> ' + '	<td>'
	    + results[i]["loggedin"] + '</td> ' + '	<td>'
	    + results[i]["registrated"] + '</td> ' + '	</tr>');
  }

  this.setUserListEvents();
};
Users.prototype.setUserListEvents = function () {
  $("#usertable tbody tr").each(
	  function () {
	    $(this).click(
		    function () {
		      var id = $(this).data("id");

		      admin.show(
			      users.url + "view/" + id,
			      users.showUserEvents);
		    });
	  });
};
Users.prototype.showUserEvents = function () {
  $("#users_back").click(function () {
    general.showUsers();
  });
  $("#users_edit").click(
	  function () {
	    var id = $(this).data("id");
	    admin.show(users.url + "editScreen/" + id,
		    users.editUserScreen);
	  });

  $("#users_delete")
	  .click(
		  function () {
		    var id = $(this).data("id");
		    var username = $(this).data("username");
		    var userid = $(this).data("userid");

		    if (id === userid) {
		      return;
		    }

		    confirmBox.init(150, users.deleteConfirm);
		    confirmBox.show(languageAdmin.users_delete_title,
			    languageAdmin.users_delete.replace("[name]",
				    username));
		  });

  $("#user_login_as").click(function () {
    var id = $(this).data("id");
    var username = $(this).data("username");
    var userid = $(this).data("userid");

    if (id === userid) {
      return;
    }

    if (confirm(languageAdmin.login_as.replace("[username]", username))) {
      $.post(users.url + "login", {
	"userid": id
      }, function () {
	location.href = "/";
      });
    }
  })
};
Users.prototype.deleteConfirm = function () {
  var id = $("#users_delete").data("id");
  $.post(users.url + "delete", {
    "userid": id
  }, function () {
    general.showUsers();
  });
};
Users.prototype.editUserScreen = function () {
  $("#userUpdateButton").click(function () {
    users.checkUpdate();
  });
  $("#newGroup").on("change", function () {
    users.addGroup();
  });
  $("#newLevel").on("change", function () {
    users.addGroup();
  });

  users.setGrouplistEvents();
  users.showUserEvents();
};
Users.prototype.addGroup = function () {
  var group = $("#newGroup").val();
  var groupName = $("#newGroup").text().split(" - ");
  var level = $("#newLevel").val();
  var levelText = $("#newLevel").text();
  var userid = $("#newGroup").data("id");

  if (group === "" || level === "") {
    return;
  }

  $("#newGroup option").find('[value="' + group + '"]').remove();

  $("#groupslist").append(
	  '<fieldset>' + $.trim(groupName[0]) + ' - ' + levelText
	  + ' <img src="' + styleDir
	  + 'images/icons/delete.png" alt="' + deleteText
	  + '" title="' + deleteText + '" class="delete" data-id="'
	  + userid + '" data-group="' + group + '" data-level="'
	  + level + '"></fieldset>');
  this.setGrouplistEvents();

  $.post(users.url + "addGroup", {
    "userid": userid,
    "group": group,
    "level": level
  });
};
Users.prototype.removeGroup = function (item) {
  var group = item.data("group");
  var level = item.data("level");
  var userid = item.data("id");

  var text = item.parent().html().split(" - ");

  if (confirm(languageAdmin.users_delete_group.replace("[name]", text[0]))) {
    $("#newGroup").append('<option value="' + group + '">' + text[0] + '</option>');
    item.remove();

    $.post(users.url + "deleteGroup", {
      "groupID": group,
      "userid": userid,
      "level": level
    });
  }
};
Users.prototype.setGrouplistEvents = function () {
  $("#groupslist fieldset img").each(function () {
    $(this).off("click");

    $(this).click(function () {
      var item = $(this);
      users.removeGroup(item);
    });
  });
};
Users.prototype.checkUpdate = function () {
  var oke = true;
  $("#email").removeClass("invalid");
  $("#password1").removeClass("invalid");
  $("#password2").removeClass("invalid");

  if (!validation.validateEmail($("#email").val())) {
    $("#email").addClass("invalid");
    oke = false;
  }

  if ($("#password1").val() !== ""
	  && $("#password1").val() !== $("#password2").val()) {
    $("#password1").addClass("invalid");
    $("#password2").addClass("invalid");
    oke = false;
  }

  var userid = $("#users_delete").data("userid");
  var email = $("#email").val();
  var bot = 0;
  if ($("#bot_1").is(":checked")) {
    bot = 1;
  }
  var blocked = 0;
  if ($("#blocked_1").is(":checked")) {
    blocked = 1;
  }

  if (oke) {
    $.post(users.url+"edit/"+userid, {
      "email": email,
      "bot": bot,
      "blocked": blocked,
      "password": $("#password1").val(),
      "password2": $("#password2").val()
    }, function () {
      admin.show(users.url + "/view/" + userid,
	      users.showUserEvents);
    });
  }
};
Users.prototype.addUserScreen = function () {
  if ($("#username").length === 0) {
    setTimeout(function () {
      users.addUserScreen();
    }, 500);
    return;
  }

  $("#users_back").click(function () {
    general.showUsers();
  });
  $("#username").on("blur", function () {
    users.checkUsername();
  });
  $("#email").on("blur", function () {
    users.checkEmail();
  });
  $("#userSaveButton").click(function () {
    users.add();
  });

  $("#username").trigger("blur");
  $("#email").trigger("blur");
};
Users.prototype.checkUsername = function () {
  var item = $('#username');
  item.removeClass("invalid valid");

  var username = $.trim(item.val());
  if (username === "") {
    return;
  }
  
  $.get(users.url + "checkUsername/" + username,function (response) {
    if (response === "1") {
      item.addClass('valid');
      $("#usernameOK").val(1);
      validation.errorMessage(item);
      return;
    }

    item.attr('data-validation-taken',languageAdmin.users_username_taken);
    $("#usernameOK").val(0);
    validation.trigger(item,'taken');
  });
}
Users.prototype.checkEmail = function () {
  $("#email").removeClass("invalid");

  var email = $.trim($("#email").val());
  if (email !== "") {
    $.get(users.url + "checkEmail/" + email, function (
	    response) {
      if (response !== "1") {
	$("#email").addClass("invalid");
	$("#email").prop("title", languageAdmin.users_email_taken);
	$("#emailOK").val(0);
      } else {
	$("#emailOK").val(1);
      }
    })
  }
};
Users.prototype.add = function () {
  var fields = new Array("username", "email", "password1", "password2");
  if (!validation.html5ValidationArray(fields)) {
    return;
  }

  if ($("#usernameOK").val() === 0 || $("#emailOK").val() === 0) {
    return;
  }

  var username = $("#username").val();
  var email = $("#email").val();
  var bot = 0;
  if ($("#bot_1").is(":checked")) {
    bot = 1;
  }
  var password1 = $("#password1").val();
  var password2 = $("#password2").val();

  if (password1 !== password2) {
    $("#password1").removeClass("valid").addClass("invalid");
    $("#password1").title(languageAdmin.users_password_invalid);
    $("#password2").removeClass("valid").addClass("invalid");
    $("#password2").title(languageAdmin.users_password_invalid);

    return;
  }

  $.post(users.url + "add", {
    "username": username,
    "email": email,
    "bot": bot,
    "password": password1,
    "password2": password2
  }, function (userid) {
    admin.show(users.url + "view/" + userid,
	    users.showUserEvents);
  });
};

var users = new Users();