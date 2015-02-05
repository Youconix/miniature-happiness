<?php
define('NIV','../../');
require(NIV.'js/generalJS.php');

class AdminGroups extends GeneralJS {	
	protected function display(){
	 echo('var languageAdmin = {
	   "users_delete" : "Weet u zeker dat u [username] wilt verwijderen?",
	   "users_login_as" : "Weet u zeker dat u wilt inloggen als [username]?\nDit beeindigd uw admin sessie.",
	   "users_delete_group" : "Weet u zeker dat u [name] wilt verwijderen?",
	   "users_username_taken" : "De gebruikersnaam is al in gebruik.",
	   "users_email_taken" : "Het E-mail adres is al in gebruik.",
	   "users_password_invalid" : "De wachtwoorden zijn niet gelijk",
	   };');
	}
}

$obj_AdminGroups = new AdminGroups();
?>