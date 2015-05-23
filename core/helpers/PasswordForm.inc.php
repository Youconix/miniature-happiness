<?php
namespace core\helpers;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Contains the password form with a password strength test
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class PasswordForm extends Helper
{

    private $service_Language;

    /**
     * PHP 5 constructor 
     * 
     * @param \core\services\Language $service_Language     The language service
     * @param \core\services\Template $service_Template     The template service
     */
    public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template)
    {
        $this->service_Language = $service_Language;
        
        $s_link = '<script src="{NIV}js/widgets/password_check.js"></script>';
        $service_Template->setJavascriptLink($s_link);
        $s_link = '<script src="{NIV}js/validation.js"></script>';
        $service_Template->setJavascriptLink($s_link);
        $s_link = '<link rel="stylesheet" href="{NIV}{style_dir}css/HTML5_validation.css">';
        $service_Template->setCssLink($s_link);
    }

    /**
     * Generates the form
     * 
     * @return string   The form
     */
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