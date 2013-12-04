<div class="login">
	<form action="login.php" method="post">
		<h1>{expired_title}</h1>
	
		<table cellpadding="0" cellspacing="0" style="margin: auto; border: 0;">
			<tr>
				<td colspan="2" class="errorNotice">{errorNotice}</td>
			</tr>
			<tr>
				<td class="bold">{password} :</td>
				<td><input type="password" name="password_old" />
				</td>
			</tr>
			<tr>
				<td class="bold">{newPassword} :</td>
				<td><input type="password" name="password" />
				</td>
			</tr>
			<tr>
				<td class="bold">{newPassword2} :</td>
				<td><input type="password" name="password2" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="hidden" name="command" value="expired"/>
					<input type="hidden" name="userid" value="{userid}"/>
					<input type="hidden" name="username" value="{username}"/>
					<input type="submit" value="{loginButton}" class="button" /></td>
			</tr>
		</table>
	</form>
</div>
