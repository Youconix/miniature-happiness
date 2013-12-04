<div id="registration">
	<h2>{registration}</h2>
	
	<h2 id="errorNotice" class="errorNotice"></h2>

	<form action="registration.php" method="post" onsubmit="return site.checkOpenRegistration()">
		<table cellspacing="0" cellpadding="0" id="registrationForm">
			<tr>
				<td colspan="2" class="errorNotice" id="errorNotice">{errorNotice}</td>
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
				<td colspan="2"><input type="hidden" name="command" value="openID"/>
					<input type="hidden" name="type" value="{type}"/><br /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="{buttonRegister}" class="button" />
				</td>
			</tr>
		</table>
	</form>
</div>