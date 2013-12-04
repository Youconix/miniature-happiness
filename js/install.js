function checkField(id){
	if( document.getElementById(id).value != '' ){
		document.getElementById('image_'+id).src = 'images/setup/oke.png';
	}
	else {
		document.getElementById('image_'+id).src = 'images/setup/notOke.png';
	}
}
function checkNrField(id){
	var value = document.getElementById(id).value; 
	if( !isNaN(value) && value > 0 ){
		document.getElementById('image_'+id).src = 'images/setup/oke.png';
	}
	else {
		document.getElementById('image_'+id).src = 'images/setup/notOke.png';
	}
}
function checkOptNrField(id){
	var value = document.getElementById(id).value; 
	if( (value == '') || (!isNaN(value) && value > 0) ){
		document.getElementById('image_'+id).src = 'images/setup/oke.png';
	}
	else {
		document.getElementById('image_'+id).src = 'images/setup/notOke.png';
	}
}
function checkDatabase(){
	var error = false;

	var username	= document.getElementById('sqlUsername').value;
	var password	= document.getElementById('sqlPassword').value;
	var host	= document.getElementById('sqlHost').value;
	var database	= document.getElementById('sqlDatabase').value;
	var type	= document.getElementById('sqlType').value;
	var port	= document.getElementById('sqlPort').value;

	var url		= 'install.php';
	var params	= '&command=checkDB&username='+username+'&password='+password+'&host='+host+'&database='+database+'&type='+type+'&port='+port;

	request = new AjaxRequest(checkDatabaseResult,null);
	request.sendGetRequest(url,'ajax=true'+params);
}


function checkDatabaseResult(response){
	var content = response.request.responseText;

	if( content.indexOf('NOT OK') != -1 ){
		document.getElementById('image_sqlUsername').src = 'images/setup/notOke.png';
		document.getElementById('image_sqlPassword').src = 'images/setup/notOke.png';
		document.getElementById('image_sqlHost').src = 'images/setup/notOke.png';
		document.getElementById('image_sqlDatabase').src = 'images/setup/notOke.png';
		document.getElementById('image_sqlType').src = 'images/setup/notOke.png';
		document.getElementById('image_sqlPort').src = 'images/setup/notOke.png';
	}
	else {
		document.getElementById('image_sqlUsername').src = 'images/setup/oke.png';
		document.getElementById('image_sqlPassword').src = 'images/setup/oke.png';
		document.getElementById('image_sqlHost').src = 'images/setup/oke.png';
		document.getElementById('image_sqlDatabase').src = 'images/setup/oke.png';
		document.getElementById('image_sqlType').src = 'images/setup/oke.png';
		document.getElementById('image_sqlPort').src = 'images/setup/oke.png';
	}
}
function checkForm(){
	if( document.getElementById('nick').value != '' ){
		document.getElementById('image_nick').src = 'images/setup/oke.png';
	}
	else {
		document.getElementById('image_nick').src = 'images/setup/notOke.png';
	}

	if( document.getElementById('email').value != '' && document.getElementById('email').value.match(/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+\.[a-zA-Z]{2,6}$/)){
		document.getElementById('image_email').src = 'images/setup/oke.png';
	}
	else {
		document.getElementById('image_email').src = 'images/setup/notOke.png';
	}


	if( document.getElementById("password").value == '' || document.getElementById("password").value != document.getElementById("password2").value ){
		document.getElementById("image_password").src ="images/setup/notOke.png";
		document.getElementById("image_password2").src ="images/setup/notOke.png";
	}
	else {
		document.getElementById("image_password").src ="images/setup/oke.png";
		document.getElementById("image_password2").src ="images/setup/oke.png";
	}
}
function checkRoot(){
	var ids	= new Array('nick','email','password','password2');
	var image;

	for(i=0; i<ids.length; i++){
		image	= document.getElementById('image_'+ids[i]).src;
		if( image.indexOf('images/setup/notOke.png') != -1 ){
			return false;
		}
	}

	return true;
}