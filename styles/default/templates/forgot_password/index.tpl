<div class="login">
	<form action="forgot_password.php" method="post">
		<h1>{forgotTitle}</h1>
		
		<table style="margin: auto; border: 0;">
			<tr>
				<td colspan="2" class="errorNotice">{errorNotice}</td>
			</tr>
			<tr>
				<td class="bold">{email} :</td>
				<td><input type="text" name="email" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="hidden" name="command" value="reset"/>
				<input type="submit" value="{loginButton}" class="button" /></td>
			</tr>
		</table>
	</form>
</div>