<div id="registration">
	<h2>{registration}</h2>
	
	<h2 id="errorNotice" class="errorNotice">{errorNotice}</h2>

	<form action="registration.php" method="post" onsubmit="return site.checkRegistration()">
		<table cellspacing="0" cellpadding="0" id="registrationForm">
			<tr>
				<td class="bold">{nickText} *</td>
				<td><input type="text" name="nick" id="reg_nick" value="{nick}" class="input" onblur="site.checkUsername(this.value)"/>
				</td>
			</tr>
			<tr>
				<td class="bold">{emailText} *</td>
				<td><input type="text" name="email" id="reg_email" value="{email}" class="input" onblur="site.checkEmail(this.value)"/>
				</td>
			</tr>
			<tr>
				<td class="bold">{passwordText} *</td>
				<td><input type="password" name="password" id="reg_password" class="input" />
				</td>
			</tr>
			<tr>
				<td class="bold">{password2Text} *</td>
				<td><input type="password" name="password2" id="reg_password2" class="input" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><br/></td>
			</tr>
			<tr>
				<td colspan="2" class="bold">{captchaText}</td>
			</tr>
			<tr>
				<td><img src="{style_dir}images/captcha.php" alt=""/></td>
				<td><input type="text" name="captcha" value=""/></td>
			</tr> 
			<tr>
				<td colspan="2"><input type="checkbox" name="conditions" id="reg_conditions"/>
					<label><a href="{NIV}conditions.php" target="_new">{conditionsText}</a></label></td>
			</tr>
			<tr>
				<td colspan="2"><input type="hidden" name="command" value="result"/><br /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="{buttonRegister}" class="button" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><br/></td>
			</tr>
			<block {openID}>
			<tr>
				<td colspan="2"><a href="registration.php?command=openID&type={key}"><img src="{style_dir}images/icons/{image}.png" alt="{key}"/> {text}</a></td>
			</tr>
			</block>
		</table>
	</form>
</div>