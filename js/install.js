function Install(){
	this.step;
	this.databaseOK = false;
	this.ldapOK = false;
	this.smtpOK	= false;
}
Install.prototype.init	= function(){
	this.step = parseInt($("#step").val());
	this.dispatch();
}
Install.prototype.dispatch	= function(){
	var _this  = this;

	switch( this.step ){
		case 1 : {	this.checkSystem(); break; }
		case 3 : {	
			$.get("index.php?step=3").done(function(response){
				_this.settingsScreen(response);
			});
			break;
		}
		case 4 : {	this.populateDB(); break; }
		case 5 : {
			$.get('index.php?step=5').done(function(response){
				_this.standardUser(response);  
			});
			break;
		}
		case 6 : {	this.complete(); break; }
	}
}
Install.prototype.checkSystem	= function(){
	$("#content section").html("<h1>Checking installer....</h1>");
	
	var _this = this;
	setTimeout(function(){
		$.get("index.php?step=2").done(function(response){
			_this.checkSystemResult(response);
		});
	},1000);
}
Install.prototype.checkSystemResult	= function(response){
	response	= JSON.parse(response);
	response	= response[0];
	
	if( response['result'] == 1 ){
		this.step = 3;
		this.dispatch();
		return;
	}
	
	output = '';
	if( response['system'] != '' || response['logs'] != '' || response['settings'] != '' ){
		output += '<h1>System check</h1>';
	} 
	
	if( response['system'] != '' ){
		output += response['system'];
	}
	if( response['logs'] != '' ){
		output += '<p class="errorNotice">Directory '+ response['logs']+' is not writable.</p>';
	}
	if( response['settings'] != '' ){
		output += '<p class="errorNotice">Directory '+ response['settings']+' is not writable.</p>';
	}
	if( response['framework'] != '' ){
		output += '<h1>Missing framework files</h1>' +
		response['framework'];
	}
	
	output += '<p onclick="install.checkSystem()">Check again</p>';
	
	$("#content section").html(output);
}
Install.prototype.settingsScreen	= function(response){
	page = this.trimPage(response);
	
	$("#content").html(page);
	animation.animate();
	validation.bindAll();
	
	$("#lDAP").on('change',function(){
		if( $("#LDAP_block").css("display") != "block" ){
			$("#LDAP_block").css("display","block");
		}
		else {
			$("#LDAP_block").css("display","none");
		}
	});
	
	$("#smtp").on('change',function(){
		if( $("#smtp_box").css("display") != "block" ){
			$("#smtp_box").css("display","block");
		}
		else {
			$("#smtp_box").css("display","none");
		}
	});
	
	$("#ldap_server, #ldap_port").on('blur',function(){ install.checkLDAP(); });
	$("#smtp_host, #smtp_port, #smtp_username, #smtp_password").on("blur",function(){ install.checkSmtp(); });
	$('#sqlUsername, #sqlPassword, #sqlHost, #sqlDatabase, #sqlPort').on('blur',function(){ install.checkDatabase(); });
	$('#sqlType').on('change',function(){ install.checkDatabase(); });
	
	$("#settings_save").on("click",function(){ install.settingsSave(); });
}
Install.prototype.settingsSave	= function(){
	var fields = new Array('url','timezone','mail_email','sqlUsername','sqlPassword','sqlDatabase','sqlPassword','sqlHost','sqlPort','databasePrefix');
	var oke1 = validation.html5ValidationArray(fields);
	
	var oke = true;
	/* Login */
	if( !$("#normalLogin").is(":checked") && !$("#openID").is(":checked") && !$("#lDAP").is(":checked") ){
		oke = false;
	}
	else if( $("#lDAP").is(":checked") && !this.ldapOK ){
		this.checkLDAP();
		oke = false;
	}
	
	/* SMTP */
	if( $("#smtp").is(":checked") && !this.smtpOK ){
		oke = false;
		this.checkSmtp();
	}
	
	/* Database */
	if( !this.databaseOK ){
		oke = false;
		this.checkDatabase();
	}
	
	if( !oke1 || !oke ){	return; }
	
	var normalLogin = 0;
	var openID = 0 ;
	var lDAP = 0;
	var smtp = 0;
	
	if( $("#normalLogin").is(":checked") ) normalLogin = 1;
	if( $("#openID").is(":checked") ) openID = 1;
	if( $("#lDAP").is(":checked") ) lDAP = 1;
	if( $("#smtp").is(":checked") ) smtp = 1;
	
	var data = {"step":3,"base" : $("#base").val(),"url":$("#url").val(),"timezone": $("#timezone").val(),
		"normalLogin":normalLogin,"openID":openID,"lDAP":lDAP,"ldap_server":$("#ldap_server").val(),
		"ldap_port":$("#ldap_port").val(),"sessionName":$("#sessionName").val(),"sessionPath":$("#sessionPath").val(),
		"sessionExpire":$("#sessionExpire").val(),"language":$("#language").val(),"template":$("#template").val(),
		"mail_name":$("#mail_name").val(),"mail_email":$("#mail_email").val(),"smtp":smtp,
		"smtp_host":$("#smtp_host").val(),"smtp_port":$("#smtp_port").val(),"smtp_username":$("#smtp_username").val(),
		"smtp_password":$("#smtp_password").val(),"sqlUsername":$("#sqlUsername").val(),"sqlPassword":$("#sqlPassword").val(),
		"sqlDatabase":$("#sqlDatabase").val(),"sqlHost":$("#sqlHost").val(),"sqlPort":$("#sqlPort").val(),
		"databaseType":$("#databaseType").val(),"databasePrefix":$("#databasePrefix").val() };
	
	$.post("index.php",data,function(response){
		alert(response);
		if( response == "ok" ){		
			install.step = 4;
			install.dispatch();
			return;
		}
		else if( response == "eror"){
			$("#content section").html('<h1 class="errorNotice">Unable to create the settings-file. See the error log for details.</h1>');
		}
		else {
			data_dir = $('body').data('datadir');
			$("#content section").html('<h1 class="errorNotice">Security error!</h1>'+
			'<h2 class="errorNotice">The settingsfile '+data_dir+'settings/settings.xml is world readable!<h2>'+
			'<h2 class="errorNotice">Move the data dir outside the WWW-root or activate htaccess!</h2>');
		}
	});
}
Install.prototype.populateDB	= function(){
	this.progressBar();
	$("#content section").html("<h1>Filling the database...</h1>");
	
	setTimeout(function(){
		$.post("index.php",{"step":"4"},function(response){
			install.populateDbResult(response);
		});
	},500);
}
Install.prototype.populateDbResult	= function(response){
	if( response == '0' ){
		$("#content section").html("<h1>Filling the database failed. See the error log for details.</h1>");
		return;
	}
	
	this.step = 5;
	this.dispatch();
}
Install.prototype.standardUser	= function(response){
	page = this.trimPage(response);
	
	$("#content").html(page);
	animation.animate();
	validation.bindAll();
	
	$("#password").on('blur',function(){ install.checkPassword(); });
	$("#password2").on('blur',function(){ install.checkPassword(); });
	$('#userSubmit').click(function(){ install.standardUserSave();	});
}
Install.prototype.standardUserSave	= function(){
	if( !validation.html5ValidationAll() || !this.checkPassword() ){
		return;
	}
	
	nick = $.trim($("#nick").val());
	email = $("#email").val();
	password = $.trim($("#password").val());
	password2 = $.trim($("#password2").val());
	
	var _this = this;
	$.post('index.php',{"step":5,"nick":nick,"email":email,"password":password,"password2":password2},function(response){ _this.standardUserResult(response); });
}
Install.prototype.standardUserResult	= function(response){
	if( response == "error" ){
		$("#content section").html("<h1>Creating the admin user failed. See the error log for details.</h1>");
		return;
	}
	
	this.step = 6;
	this.dispatch();
}
Install.prototype.checkPassword	= function(){
	password1 = $("#password");
	password2	= $("#password2");
	
	if( $.trim(password1.val()) == '' || $.trim(password2.val()) == '' ){	return false; }
		
	password1.removeClass('valid invalid');
	password2.removeClass('valid invalid');
	
	password1.prop('title','');
	password2.prop('title','');
	
	if( $.trim(password1.val()) != $.trim(password2.val()) ){
		password1.addClass('invalid');
		password2.addClass('invalid');
		
		password1.prop('title','The passwords are not equal.');
		password2.prop('title','The passwords are not equal.');
		return false;
	}
	if( $.trim(password1.val()).length < 8 ){
		password1.addClass('invalid');
		password2.addClass('invalid');
		
		password1.prop('title','The password too short. At least 8 characters.');
		password2.prop('title','The password too short. At least 8 characters.');
		return false;
	}
	
	password1.addClass('valid');
	password2.addClass('valid');
	return true;
}
Install.prototype.complete = function(){
	this.progressBar();
	
	$("#content section").html("<h1>Installation complete.</h1>"+
		'<h2>Remove the install directory.</h2>');
}
Install.prototype.progressBar	= function(){
	progressBar = '';
	for(i=1; i<=6; i++){
		(i <= this.step ) ? className = 'current' : className = 'grey';
		
		progressBar += '<li class="'+className+'">'+i+'</li>';
	}
	$('#progressBar').html(progressBar);
}
Install.prototype.trimPage	= function(page){
	page = page.split('<section id="content">');
	page = page[1].split('<footer class="holder"></footer>');
	page = $.trim(page[0]);
	page = page.substring(0,(page.length-10));
	return page;
}
Install.prototype.checkLDAP	= function(){
	this.ldapOK = false;
	server = $.trim($("#ldap_server").val());
	port = parseInt($("#ldap_port").val());
		
	if( server == '' || isNaN(port) ){	return; }
	
	$.post('index.php',{"command":"checkLDAP","server" : server, "port" : port},function(response){ install.checkLdapResult(response); });
}
Install.prototype.checkLdapResult	= function(response){
	$("#ldap_server").removeClass('valid invalid');
	$("#ldap_port").removeClass('valid invalid');
	
	if( reponse == "1" ){
		$("#ldap_server").addClass('valid');
		$("#ldap_port").addClass('valid');
		this.ldapOK = true;
	}
	else {
		$("#ldap_server").addClass('invalid');
		$("#ldap_port").addClass('invalid');
	}
}
Install.prototype.checkSmtp	= function(){
	this.smtpOK	= false;
	server 		= $.trim($("#smtp_host").val());
	port		= parseInt($("#smtp_port").val());
	username	= $.trim($("#smtp_username").val());
	password	= $.trim($("#smtp_password").val());
	
	if( server == '' || isNaN(port) || username == '' || password == '' ){	return; }
	
	$.post("index.php",{"command":"checkSMTP","server":server,"port":port,"username" : username,"password":password},
		function(response){
			install.checkSmtpResult(response);
		});
}
Install.prototype.checkSmtpResult = function(response){
	$("#smtp_host").removeClass('valid invalid');
	$("#smtp_port").removeClass('valid invalid');
	$("#smtp_username").removeClass('valid invalid');
	$("#smtp_password").removeClass('valid invalid');
	
	if( reponse == "1" ){
		$("#smtp_host").addClass('valid');
		$("#smtp_port").addClass('valid');
		$("#smtp_username").addClass('valid');
		$("#smtp_password").addClass('valid');
		this.smtpOK = true;
	}
	else {
		$("#smtp_host").addClass('invalid');
		$("#smtp_port").addClass('invalid');
		$("#smtp_username").addClass('invalid');
		$("#smtp_password").addClass('invalid');
	}
}
Install.prototype.checkDatabase	= function(){
	this.databaseOK = false;

	username	= $.trim($('#sqlUsername').val());
	password	= $.trim($('#sqlPassword').val());
	host		= $.trim($('#sqlHost').val());
	database	= $.trim($('#sqlDatabase').val());
	type		= $('#databaseType').val();
	port		= $('#sqlPort').val();
	
	if( username == '' || password == '' || host == '' || database == '' || (port != '' && isNaN(port)) ){	return; }

	$.post("index.php",{"command":"checkDB","username":username,"password":password,"host":host,
		"database":database,"type":type,"port":port},function(response){
		install.checkDatabaseResult(response);
	});
}
Install.prototype.checkDatabaseResult	= function(response){
	$('#sqlUsername').removeClass('valid invalid');
	$('#sqlPassword').removeClass('valid invalid');
	$('#sqlHost').removeClass('valid invalid');
	$('#sqlDatabase').removeClass('valid invalid');
	$('#sqlPort').removeClass('valid invalid');
	
	if( response == 1 ){
		this.databaseOK = true;
		
		$('#sqlUsername').addClass('valid');
		$('#sqlPassword').addClass('valid');
		$('#sqlHost').addClass('valid');
		$('#sqlDatabase').addClass('valid');
		$('#sqlPort').addClass('valid');
	}
	else {
		$('#sqlUsername').addClass('invalid');
		$('#sqlPassword').addClass('invalid');
		$('#sqlHost').addClass('invalid');
		$('#sqlDatabase').addClass('invalid');
		$('#sqlPort').addClass('invalid');
	}
}

var install = new Install();

$(document).ready(function(){
	install.init();
});