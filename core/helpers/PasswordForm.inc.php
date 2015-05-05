<?php
namespace core\helpers;

class PasswordForm extends Helper
{

    private $service_Language;

    public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template)
    {
        $this->service_Language = $service_Language;
        
        $s_link = '<script src="{NIV}js/widgets/password_check.php?lang=' . $this->service_Language->getLanguage() . '"></script>';
        $service_Template->setJavascriptLink($s_link);
    }

    public function generate()
    {
        $s_html = '<section id="passwordForm">
		<table>
		<tbody>
			<tr>
				<td><label>' . $this->service_Language->get('system/admin/users/password') . '</label></td>
				<td><input type="password" name="password" id="password1" required></td>
				<td id="passwordStrength"></td>
			</tr>
			<tr>
				<td><label>' . $this->service_Language->get('system/admin/users/passwordAgain') . '</label></td>
				<td><input type="password" name="password2" id="password2" required></td>
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