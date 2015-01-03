<section id="registration">
	<h2>{registration}</h2>
	
	<h2 id="errorNotice" class="errorNotice">{errorNotice}</h2>

	<form action="{NIV}authorization/registration/save" method="post" onsubmit="return site.checkRegistration()">
		<table id="registrationForm">
		<tbody>
			<tr>
				<td><label>{nickText} *</label></td>
				<td><input type="text" name="nick" id="reg_nick" value="{nick}" required data-error-message="De gebruikersnaam is niet ingevuld"></td>
			</tr>
			<tr>
				<td><label>{emailText} *</label></td>
				<td><input type="email" name="email" id="reg_email" value="{email}" onblur="site.checkEmail(this.value)" required></td>
			</tr>
			</tbody>
			</table>
			
			{passwordForm}			

			<h2>{captchaText}</h2>
			
			<table>
			<tbody>
			<tr>
				<td><img src="{NIV}{style_dir}images/captcha.php" alt=""></td>
				<td><input type="text" name="captcha" value="" required></td>
			</tr> 
			<tr>
				<td colspan="2"><input type="checkbox" name="conditions" id="reg_conditions">
					<label><a href="{NIV}conditions.php" target="_new">{conditionsText}</a></label></td>
			</tr>
			<tr>
				<td colspan="2"><input type="hidden" name="command" value="result"/><br /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="{buttonRegister}"></td>
			</tr>
			<tr>
				<td colspan="2"><br/></td>
			</tr>
			<block {openID}>
			<tr>
				<td colspan="2"><a href="{NIV}authorization/registration_{key}.php">
				<img class="icon" src="{NIV}{style_dir}images/icons/{image}.png" alt="{key}" title="{key}">{text}</a></td>
			</tr>
			</block>
			</tbody>
		</table>
	</form>
</section>
