<?php
class Helper_PasswordForm extends Helper {
	private $service_Language;
	
	public function __construct(){
		$this->service_Language = Memory::services('Language');
		
		Memory::services('Template')->headerLink('<script src="{NIV}js/widgets/password_check.php?lang='.$this->service_Language->getLanguage().'"></script>');
	}
	
	public function generate(){
		$s_html	= '<section id="passwordForm">
		<table>
		<tbody>
			<tr>
				<td><label>'.$this->service_Language->get('language/admin/users/password').'</label></td>
				<td><input type="password" name="password" id="password1" required></td>
				<td id="passwordStrength"></td>
			</tr>
			<tr>
				<td><label>'.$this->service_Language->get('language/admin/users/passwordAgain').'</label></td>
				<td><input type="password" id="password2" required></td>
				<td id="passwordStrengthText"></td>
			</tr>
		</tbody>
		</table>
		</section>
		<style>
		<!--
		#passwordStrength div {	height:12px; width:12px; float:left; margin-right:2px; }
		.passwordRed	{ background-color:red; }	
		.passwordYellow { background-color:yellow; }
		.passwordGreen {	background-color:green; }
		//-->
		</style>
		<script>
		<!--
		passwordCheck = new PasswordCheck();
		passwordCheck.init();
		//-->
		</script>';
		
		return $s_html;		
	}
}
?>