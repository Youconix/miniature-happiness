function Site() {
	Site.prototype.checkMessage = function() {
		var receiver = $.trim($("#receiver").val());
		var subject = $.trim($("#subject").val());
		var message = $.trim($("#message").val());
		error = "";

		if (receiver == "") {
			error += languageSite.messages_noReceiver + "<br/>";
		}
		if (subject == "") {
			error += languageSite.messages_noSubject + "<br/>";
		}
		if (message == "") {
			error += languageSite.messages_noMessage + "<br/>";
		}

		if (error != "") {
			$("#errorNotice").html(error);
			return false;
		}

		return true;
	}

	Site.prototype.confirmMessageDelete = function() {
		if (confirm(languageSite.messages_deleteConfirm)) {
			return true;
		}
		return false;
	}
}

var site = new Site();

/* Check session status */
$(document).ready(
		function() {
			$(document).ajaxError(
					function(event, jqxhr, settings, exception) {
						if (jqxhr.status == 401) {
							/* Session expired */
							var div = document.createElement("div");
							$(div).attr("id", "ajaxLoginBox").html(
									'<a href="javascript:removeAuthButton()">'
											+ languageSite.session_expired
											+ '</a>').appendTo($("body"));
						} else if (jqxhr.status == 403) {
							window.location.href = "/errors/403.php";
						}
					});
		});

function removeAuthButton() {
	if ($("#ajaxLoginBox").length > 0) {
		$("#ajaxLoginBox").remove();
		window
				.open(
						"/authorization/login.php?ajaxLogin=true",
						"session",
						config = "height=350,width=1100,toolbar=no,menubar=no,location=no,directories=no,status=no,left:40%,top:40%");
	}
}
